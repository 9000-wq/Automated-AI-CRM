<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailAccount;
use App\Models\SentEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Google\Client as GoogleClient;
use Google\Service\Gmail;
use Webklex\PHPIMAP\ClientManager;

class EmailsController extends Controller
{
    /**
     * Show inbox emails with pagination
     */
    public function index(Request $request)
    {
        $accounts = EmailAccount::where('user_id', Auth::id())->get();

        $connected = $accounts->isNotEmpty() ? ($request->input('provider', $accounts->first()->provider)) : null;
        $account = $accounts->firstWhere('provider', $connected);

        if ($request->ajax()) {
            $perPage = 10;
            $page = max(1, (int) $request->input('page', 1));
            $nextPageToken = '';
            $totalMessages = 0;
            $emails = [];

            if ($account) {
                try {
                    if ($account->provider === 'gmail') {
                        // ---------- Gmail ----------
                        $client = new GoogleClient();
                        $client->setClientId(config('services.google.client_id'));
                        $client->setClientSecret(config('services.google.client_secret'));
                        $client->setRedirectUri(config('services.google.redirect'));
                        $client->setAccessToken($account->access_token);

                        if ($client->isAccessTokenExpired() && $account->refresh_token) {
                            $client->fetchAccessTokenWithRefreshToken($account->refresh_token);
                            $account->access_token = $client->getAccessToken();
                            $account->save();
                        }

                        $service = new Gmail($client);
                        $params = ['maxResults' => $perPage, 'labelIds' => ['INBOX']];
                        if ($request->input('pageToken')) {
                            $params['pageToken'] = $request->input('pageToken');
                        }

                        $msgResponse = $service->users_messages->listUsersMessages('me', $params);
                        $messages = $msgResponse->getMessages();
                        $nextPageToken = $msgResponse->getNextPageToken();
                        $profile = $service->users->getProfile('me');
                        $totalMessages = $profile->getMessagesTotal();

                        foreach ($messages as $message) {
                            $msg = $service->users_messages->get('me', $message->getId(), ['format' => 'full']);
                            $headers = collect($msg->getPayload()->getHeaders());

                            $body = '';
                            $parts = $msg->getPayload()->getParts();
                            if ($parts) {
                                foreach ($parts as $part) {
                                    if (in_array($part->getMimeType(), ['text/plain', 'text/html'])) {
                                        $data = $part->getBody()->getData() ?: '';
                                        $body = base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
                                        if ($part->getMimeType() === 'text/html') {
                                            $body = strip_tags($body);
                                        }
                                        break;
                                    }
                                }
                            } else {
                                $data = $msg->getPayload()->getBody()->getData() ?: '';
                                $body = base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
                            }

                            $emails[] = [
                                'id' => $msg->getId(),
                                'from' => optional($headers->firstWhere('name', 'From'))->getValue(),
                                'subject' => optional($headers->firstWhere('name', 'Subject'))->getValue() ?: '(No Subject)',
                                'date' => optional($headers->firstWhere('name', 'Date'))->getValue(),
                                'body_preview' => mb_strimwidth($body, 0, 100, '…'),
                                'body_full' => $body,
                                'timestamp' => strtotime(optional($headers->firstWhere('name', 'Date'))->getValue()),
                            ];
                        }

                    } elseif ($account->provider === 'imap') {
                        // ---------- IMAP ----------
                        $cm = new ClientManager();
                        $client = $cm->make([
                            'host' => $account->imap_host,
                            'port' => $account->imap_port,
                            'encryption' => $account->imap_encryption,
                            'validate_cert' => true,
                            'username' => $account->imap_username,
                            'password' => decrypt($account->imap_password),
                            'protocol' => 'imap',
                        ]);

                        $client->connect();
                        $folder = $client->getFolder('INBOX');

                        // Fetch emails with offset for pagination
                        $messages = $folder->query()
                            ->all()
                            ->setFetchOrder('desc')
                            ->limit($perPage, ($page - 1) * $perPage)
                            ->get();

                        $totalMessages = $folder->messages()->all()->count();

                        foreach ($messages as $msg) {
                            // Ensure headers are fully fetched
                            $msg->getHeaders();

                            $from = $msg->getFrom()[0]->mail ?? '(Unknown)';
                            $subject = (string) $msg->getSubject() ?: '(No Subject)';

                            // Handle date safely
                            $rawDate = $msg->getDate();
                            if ($rawDate instanceof \DateTime) {
                                // If getDate() returns a DateTime object, format it in 12-hour format
                                $date = $rawDate->format('Y-m-d h:i:s A');
                            } else {
                                // If getDate() returns a string, try to parse it and format in 12-hour format
                                $date = $rawDate ? date('Y-m-d h:i:s A', strtotime($rawDate)) : now()->format('Y-m-d h:i:s A');
                            }

                            // Ensure the date is valid for timestamp
                            $timestamp = $rawDate ? strtotime($rawDate) : time();

                            // Decode body safely
                            $body = $msg->getTextBody() ?: $msg->getHtmlBody() ?: '';
                            $body = strip_tags($body);

                            $emails[] = [
                                'id' => $msg->getUid(),
                                'from' => $from,
                                'subject' => $subject,
                                'date' => $date,
                                'body_preview' => mb_strimwidth($body, 0, 100, '…'),
                                'body_full' => $body,
                                'timestamp' => $timestamp,
                            ];
                        }

                        // Sort latest first
                        usort($emails, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);
                    }

                } catch (\Exception $e) {
                    \Log::error('Email fetch error: ' . $e->getMessage());
                    $totalMessages = 0;
                    $emails = [];
                }
            }

            return response()->json([
                'draw' => $request->input('draw', 1),
                'recordsTotal' => $totalMessages,
                'recordsFiltered' => $totalMessages,
                'data' => $emails,
                'nextPageToken' => $nextPageToken,
                'status' => 'success',
                'connected' => $connected,
            ]);
        }

        return view('emails', compact('accounts'));
    }


    /**
     * Send email and save in sent_emails table
     */
    public function sendEmail(Request $request)
    {
        $request->validate([
            'to' => 'required|email',
            'cc' => 'nullable|email',
            'subject' => 'required|string',
            'body' => 'nullable|string',
        ]);

        $account = EmailAccount::where('user_id', Auth::id())->firstOrFail();

        try {
            if ($account->provider === 'gmail' && $account->access_token) {
                $client = new GoogleClient();
                $client->setClientId(config('services.google.client_id'));
                $client->setClientSecret(config('services.google.client_secret'));
                $client->setRedirectUri(config('services.google.redirect'));
                $client->setAccessToken($account->access_token);

                if ($client->isAccessTokenExpired() && $account->refresh_token) {
                    $newToken = $client->fetchAccessTokenWithRefreshToken($account->refresh_token);
                    $account->access_token = $newToken;
                    $account->save();
                }

                $service = new Gmail($client);
                $rawMessageString = "To: {$request->to}\r\n";
                if ($request->cc)
                    $rawMessageString .= "Cc: {$request->cc}\r\n";
                $rawMessageString .= "Subject: {$request->subject}\r\n";
                $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n\r\n";
                $rawMessageString .= $request->body;

                $rawMessage = base64_encode($rawMessageString);
                $rawMessage = str_replace(['+', '/', '='], ['-', '_', ''], $rawMessage);

                $message = new \Google\Service\Gmail\Message();
                $message->setRaw($rawMessage);

                $service->users_messages->send('me', $message);

            } else {
                $imapPassword = decrypt($account->imap_password);

                config([
                    'mail.mailers.smtp.transport' => 'smtp',
                    'mail.mailers.smtp.host' => $account->imap_host,
                    'mail.mailers.smtp.port' => $account->imap_port,
                    'mail.mailers.smtp.encryption' => $account->imap_encryption,
                    'mail.mailers.smtp.username' => $account->imap_username,
                    'mail.mailers.smtp.password' => $imapPassword,
                    'mail.from.address' => $account->imap_username,
                    'mail.from.name' => 'MyApp Mailer',
                ]);

                Mail::raw($request->body ?? '', function ($message) use ($request) {
                    $message->to($request->to)
                        ->subject($request->subject);

                    if ($request->cc)
                        $message->cc($request->cc);
                });
            }

            SentEmail::create([
                'user_id' => Auth::id(),
                'to' => $request->to,
                'cc' => $request->cc,
                'subject' => $request->subject,
                'body' => $request->body,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Email sent successfully']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Show sent emails from DB
     */
    public function sentEmails()
    {
        $sent = SentEmail::where('user_id', Auth::id())->latest()->paginate(50);
        return view('sent-emails', compact('sent'));
    }
}
