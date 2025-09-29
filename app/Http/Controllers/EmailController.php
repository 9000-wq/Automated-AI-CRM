<?php

namespace App\Http\Controllers;

use Google_Client;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\Models\EmailLog;
use Illuminate\Encryption\Encrypter;
use App\Models\Lead; // Added Lead model import
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\EmailAccount;
use Google_Service_Oauth2;

class EmailController extends Controller
{
    public function showEmailLogs()
    {
       
        return view('leads.emailindex'); 
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'emailTo' => 'required|email',
            'emailSubject' => 'required',
            'emailBody' => 'required'
        ]);

        $log = EmailLog::create([
            'lead_id' => $request->emailParent ?: null,
            'email' => $request->emailTo,
            'cc' => $request->emailCC,
            'subject' => $request->emailSubject,
            'body' => $request->emailBody,
            'status' => 'sent'
        ]);

        // Here you would add your actual email sending logic
        // Mail::to($request->emailTo)->send(new CustomEmail($log));

        return response()->json([
            'success' => true,
            'message' => 'Email sent successfully'
        ]);
    }

    public function getEmailLogsData(Request $request)
    {
        $logs = EmailLog::with('lead') 
            ->when($request->lead_id, function($query) use ($request) {
                $query->where('lead_id', $request->lead_id);
            })
            ->orderBy('created_at', 'desc');

        return datatables()->of($logs)
            ->addColumn('parent', function($log) {
                return $log->lead ? $log->lead->name : 'N/A';
            })
            ->editColumn('body', function($log) {
                return Str::limit(strip_tags($log->body), 100);
            })
            ->addColumn('action', function($log) {
                return '<button class="btn btn-sm btn-primary view-log" data-id="'.$log->id.'">View</button>';
            })
            ->rawColumns(['body', 'action'])
            ->make(true);
    }

    public function getEmailLog($id)
    {
        $log = EmailLog::findOrFail($id);
        return response()->json([
            'subject' => $log->subject,
            'body' => $log->body,
            'to' => $log->email,
            'cc' => $log->cc,
            'parent' => $log->lead ? $log->lead->name : null
        ]);
    }

    public function getLeads(Request $request)
    {

        $search = $request->input('term');

        $leads = Lead::query()
            ->where('company_id', auth()->user()->company_id)
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(20, ['id', 'name']);

        return response()->json([
            'data' => $leads->items(),
            'total' => $leads->total()
        ]);
    }


    // Google, Microsoft OAuth and IMAP/SMTP methods would go here
    public function index()
    {
        $accounts = EmailAccount::where('user_id', Auth::id())->get();
        return view('email-settings', compact('accounts'));
    }

    // ✅ Gmail OAuth
    public function redirectToGoogle()
    {
        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        
        // dd(config('services.google.redirect'));

        $client->addScope([
            "https://www.googleapis.com/auth/gmail.readonly",
            "https://www.googleapis.com/auth/gmail.send",
            "https://www.googleapis.com/auth/gmail.modify",
            "email",
            "profile"
        ]);
        $client->setAccessType("offline");
        $client->setPrompt("consent");

        return redirect()->away($client->createAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));

        // dd(config('services.google.redirect'));

        $token = $client->fetchAccessTokenWithAuthCode($request->code);

        if (isset($token['error'])) {
            return redirect()->route('companyinfo')
                ->with('error', 'Google authentication failed: ' . $token['error_description']);
        }
        // Get email from Google API
        $client->setAccessToken($token['access_token']);
        $oauth2 = new Google_Service_Oauth2($client);
        $googleUser = $oauth2->userinfo->get();

        
        // ✅ Check if this email already belongs to another user
        $existing = EmailAccount::where('email', $googleUser->email)
            ->where('user_id', '!=', Auth::id())
            ->first();

        if ($existing) {
            return redirect()->route('companyinfo')
                ->with('error', 'This email account is already connected by another user.');
        }

        // 🔥 Delete old accounts for this user
        EmailAccount::where('user_id', Auth::id())->delete();

        EmailAccount::updateOrCreate(
            ['user_id' => Auth::id(), 'provider' => 'gmail'],
            [
                'email' => $googleUser->email,
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'] ?? null,
                'expires_in' => $token['expires_in'],
            ]
        );

        return redirect()->route('companyinfo')->with('success', 'Gmail connected!');
    }

    // ✅ Outlook OAuth (Microsoft Graph API)
    public function redirectToOutlook()
    {
        $url = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?' . http_build_query([
            'client_id' => config('services.outlook.client_id'),
            'response_type' => 'code',
            'redirect_uri' => route('services.outlook.callback'),
            'response_mode' => 'query',
            'scope' => 'offline_access Mail.Read Mail.Send User.Read',
        ]);

        return redirect()->away($url);
    }

    public function handleOutlookCallback(Request $request)
    {
        $tokenUrl = "https://login.microsoftonline.com/common/oauth2/v2.0/token";

        $response = Http::asForm()->post($tokenUrl, [
            'client_id' => config('services.outlook.client_id'),
            'client_secret' => config('services.outlook.client_secret'),
            'code' => $request->code,
            'redirect_uri' => route('services.outlook.callback'),
            'grant_type' => 'authorization_code',
        ])->json();

        // Fetch email using Graph API
        $graphResponse = Http::withToken($response['access_token'])
            ->get('https://graph.microsoft.com/v1.0/me')
            ->json();

        // 🔥 Delete old accounts for this user
        EmailAccount::where('user_id', Auth::id())->delete();

        EmailAccount::updateOrCreate(
            ['user_id' => Auth::id(), 'provider' => 'outlook'],
            [
                'email' => $graphResponse['mail'] ?? $graphResponse['userPrincipalName'],
                'access_token' => $response['access_token'],
                'refresh_token' => $response['refresh_token'] ?? null,
                'expires_in' => $response['expires_in'],
            ]
        );

        return redirect()->route('companyinfo')->with('success', 'Outlook connected!');
    }

    // ✅ IMAP Save
    public function saveImap(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'imap_host' => 'required|string',
            'imap_port' => 'required|numeric',
            'imap_encryption' => 'nullable|string',
            'imap_username' => 'required|string',
            'imap_password' => 'required|string',
        ]);

        try {
            // Build mailbox connection string
            $mailbox = "{" . $request->imap_host . ":" . $request->imap_port 
                    . "/imap" 
                    . ($request->imap_encryption ? "/" . $request->imap_encryption : "") 
                    . "/novalidate-cert}INBOX";

            // Try to connect
            $connection = @imap_open($mailbox, $request->imap_username, $request->imap_password);

            if (!$connection) {
               return response()->json([
                    'success' => false,
                    'message' => 'IMAP connection failed: ' . imap_last_error(),
                ], 400);
            }

            imap_close($connection);
            // 🔥 Delete old accounts for this user
            EmailAccount::where('user_id', Auth::id())->delete();

            // Use custom encrypter with EMAIL_SECRET_KEY
            $email_secret_key = env('EMAIL_SECRET_KEY'); // base64 encoded
            $encrypter = new Encrypter(base64_decode($email_secret_key), 'AES-256-CBC');
            $encrypted_password = $encrypter->encrypt($request->imap_password, false);

            // Save only if connection works
            EmailAccount::updateOrCreate(
                ['user_id' => Auth::id(), 'provider' => 'imap'],
                [
                    'email' => $request->email,
                    'imap_host' => $request->imap_host,
                    'imap_port' => $request->imap_port,
                    'imap_encryption' => $request->imap_encryption,
                    'imap_username' => $request->imap_username,
                    'imap_password' => $encrypted_password,
                ]
            );

            // return response()->json([
            //     'success' => true,
            //     'message' => 'IMAP settings verified & saved!',
            // ]);
            return redirect()->route('companyinfo')->with('success', 'IMAP settings verified & saved!');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'IMAP check failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function emailReset(Request $request)
    {
        try {
            // 🔥 Delete old accounts for this user
            EmailAccount::where('user_id', Auth::id())->delete();

            return redirect()->route('companyinfo')->with('success', 'Email settings reset successfully!');
        } catch (\Exception $e) {
            return redirect()->route('companyinfo')->with('error', 'Failed to reset Email settings: ' . $e->getMessage());
        }
    }
    /**
     * Update access token & expiry when Python refreshes it.
     */
    public function updateToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'access_token' => 'required|string',
            'expires_in' => 'required|string', // ISO8601 string
        ]);

        $account = EmailAccount::where('email', $request->email)->first();

        if (!$account) {
            return response()->json(['error' => 'Email account not found'], 404);
        }

        $account->access_token = $request->access_token;
        $account->expires_in = strtotime($request->expires_in); // store as timestamp
        $account->save();

        return response()->json([
            'message' => 'Token updated successfully',
            'email' => $account->email,
        ]);
    }
}