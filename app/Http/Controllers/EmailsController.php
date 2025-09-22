<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailAccount;
use App\Models\SentEmail;
use Illuminate\Support\Facades\Auth;
use Google\Client as GoogleClient;
use Google\Service\Gmail;

class EmailsController extends Controller
{
    /**
     * Show inbox emails with pagination
     */
    public function index(Request $request)
    {
        $accounts = EmailAccount::where('user_id', Auth::id())->get();
        
        // If AJAX request from DataTables, return JSON data
        if ($request->ajax()) {
            $emails = [];
            $nextPageToken = $request->input('pageToken');
            $totalMessages = 0;
            
            if ($accounts->isNotEmpty() && $accounts[0]->provider === 'gmail') {
                $account = $accounts[0];

                $client = new GoogleClient();
                $client->setClientId(config('services.google.client_id'));
                $client->setClientSecret(config('services.google.client_secret'));
                $client->setRedirectUri(config('services.google.redirect'));
                $client->setAccessToken($account->access_token);

                // Refresh token if expired
                if ($client->isAccessTokenExpired() && $account->refresh_token) {
                    $client->fetchAccessTokenWithRefreshToken($account->refresh_token);
                    $account->access_token = $client->getAccessToken();
                    $account->save();
                }

                $service = new Gmail($client);

                $params = [
                    'maxResults' => 10, // fetch 10 emails per page for pagination
                    'labelIds' => ['INBOX'],
                    'q' => 'in:inbox' // Ensure we're only getting inbox emails
                ];

                if ($nextPageToken) {
                    $params['pageToken'] = $nextPageToken;
                }

                $messagesResponse = $service->users_messages->listUsersMessages('me', $params);
                $messages = $messagesResponse->getMessages();
                $nextPageToken = $messagesResponse->getNextPageToken();
                
                // Get the total count of messages in the inbox
                try {
                    $profile = $service->users->getProfile('me');
                    $totalMessages = $profile->getMessagesTotal();
                } catch (\Exception $e) {
                    // Fallback to estimated count if exact count fails
                    $totalMessages = $messagesResponse->getResultSizeEstimate();
                }

                if ($messages) {
                    foreach ($messages as $message) {
                        $msg = $service->users_messages->get('me', $message->getId(), [
                            'format' => 'full',
                        ]);

                        $headers = collect($msg->getPayload()->getHeaders());
                        
                        // Extract body content
                        $body = '';
                        $parts = $msg->getPayload()->getParts();
                        if ($parts) {
                            foreach ($parts as $part) {
                                if ($part->getMimeType() == 'text/plain') {
                                    $body = base64_decode(str_replace(['-', '_'], ['+', '/'], $part->getBody()->getData()));
                                    break;
                                } else if ($part->getMimeType() == 'text/html') {
                                    $body = strip_tags(base64_decode(str_replace(['-', '_'], ['+', '/'], $part->getBody()->getData())));
                                    break;
                                }
                            }
                        } else {
                            // For simple emails without parts
                            $body = base64_decode(str_replace(['-', '_'], ['+', '/'], $msg->getPayload()->getBody()->getData()));
                        }
                        
                        // Truncate body for display
                        $preview = strlen($body) > 100 ? substr($body, 0, 100) . '...' : $body;

                        $emails[] = [
                            'id' => $msg->getId(),
                            'from' => optional($headers->firstWhere('name', 'From'))->getValue(),
                            'subject' => optional($headers->firstWhere('name', 'Subject'))->getValue() ?: '(No Subject)',
                            'date' => optional($headers->firstWhere('name', 'Date'))->getValue(),
                            'body_preview' => $preview,
                            'body_full' => $body,
                            'timestamp' => strtotime(optional($headers->firstWhere('name', 'Date'))->getValue()), // Add timestamp for sorting
                        ];
                    }
                }
                
                // Sort emails by date (newest first)
                usort($emails, function($a, $b) {
                    return $b['timestamp'] <=> $a['timestamp'];
                });
            }

            return response()->json([
                'data' => $emails,
                'nextPageToken' => $nextPageToken,
                'draw' => $request->input('draw', 1),
                'recordsTotal' => $totalMessages,
                'recordsFiltered' => $totalMessages,
            ]);
        }

        // First normal page load
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

        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->setAccessToken($account->access_token);

        // Refresh token if expired
        if ($client->isAccessTokenExpired() && $account->refresh_token) {
            $client->fetchAccessTokenWithRefreshToken($account->refresh_token);
            $account->access_token = $client->getAccessToken();
            $account->save();
        }

        $service = new Gmail($client);

        // Build raw email message
        $rawMessageString = "To: {$request->to}\r\n";
        if ($request->cc) {
            $rawMessageString .= "Cc: {$request->cc}\r\n";
        }
        $rawMessageString .= "Subject: {$request->subject}\r\n";
        $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n\r\n";
        $rawMessageString .= $request->body;

        $rawMessage = base64_encode($rawMessageString);
        $rawMessage = str_replace(['+', '/', '='], ['-', '_', ''], $rawMessage);

        $message = new \Google\Service\Gmail\Message();
        $message->setRaw($rawMessage);

        try {
            $service->users_messages->send('me', $message);

            SentEmail::create([
                'user_id' => Auth::id(),
                'to'      => $request->to,
                'cc'      => $request->cc,
                'subject' => $request->subject,
                'body'    => $request->body,
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