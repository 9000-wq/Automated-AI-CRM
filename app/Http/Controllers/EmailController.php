<?php

namespace App\Http\Controllers;

use Google_Client;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\Models\EmailLog;
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
            return redirect()->route('email.settings')
                ->with('error', 'Google authentication failed: ' . $token['error_description']);
        }
        // Get email from Google API
        $client->setAccessToken($token['access_token']);
        $oauth2 = new Google_Service_Oauth2($client);
        $googleUser = $oauth2->userinfo->get();

        EmailAccount::updateOrCreate(
            ['user_id' => Auth::id(), 'provider' => 'gmail'],
            [
                'email' => $googleUser->email,
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'] ?? null,
                'expires_in' => $token['expires_in'],
            ]
        );

        return redirect()->route('email.settings')->with('success', 'Gmail connected!');
    }

    // ✅ Outlook OAuth (Microsoft Graph API)
    public function redirectToOutlook()
    {
        $url = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?' . http_build_query([
            'client_id' => config('services.outlook.client_id'),
            'response_type' => 'code',
            'redirect_uri' => route('outlook.callback'),
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
            'redirect_uri' => route('outlook.callback'),
            'grant_type' => 'authorization_code',
        ])->json();

        // Fetch email using Graph API
        $graphResponse = Http::withToken($response['access_token'])
            ->get('https://graph.microsoft.com/v1.0/me')
            ->json();

        EmailAccount::updateOrCreate(
            ['user_id' => Auth::id(), 'provider' => 'outlook'],
            [
                'email' => $graphResponse['mail'] ?? $graphResponse['userPrincipalName'],
                'access_token' => $response['access_token'],
                'refresh_token' => $response['refresh_token'] ?? null,
                'expires_in' => $response['expires_in'],
            ]
        );

        return redirect()->route('email.settings')->with('success', 'Outlook connected!');
    }

    // ✅ IMAP Save
    public function saveImap(Request $request)
    {
        EmailAccount::updateOrCreate(
            ['user_id' => Auth::id(), 'provider' => 'imap'],
            [
                'email' => $request->email,
                'imap_host' => $request->imap_host,
                'imap_port' => $request->imap_port,
                'imap_encryption' => $request->imap_encryption,
                'imap_username' => $request->imap_username,
                'imap_password' => encrypt($request->imap_password), // secure storage
            ]
        );

        return redirect()->route('email.settings')->with('success', 'IMAP settings saved!');
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