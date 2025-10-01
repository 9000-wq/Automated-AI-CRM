<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Facebook\Facebook;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class FacebookController extends Controller
{
    // Step 1: Redirect user to Meta Login
    public function redirectToFacebook()
    {
        $fb = new Facebook([
            'app_id' => env('FACEBOOK_CLIENT_ID'),
            'app_secret' => env('FACEBOOK_CLIENT_SECRET'),
            'default_graph_version' => 'v23.0',
        ]);

        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['pages_show_list','pages_manage_posts','pages_read_engagement']; // required permissions
        $loginUrl = $helper->getLoginUrl(env('FACEBOOK_REDIRECT_URI'), $permissions);

        return redirect($loginUrl);
    }

    // Step 2: Handle Callback from Meta
    public function handleFacebookCallback(Request $request)
    {
        $fb = new Facebook([
            'app_id' => env('FACEBOOK_CLIENT_ID'),
            'app_secret' => env('FACEBOOK_CLIENT_SECRET'),
            'default_graph_version' => 'v23.0',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken(env('FACEBOOK_REDIRECT_URI'));
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            return response()->json(['error' => 'Graph returned an error: ' . $e->getMessage()], 400);
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            return response()->json(['error' => 'Facebook SDK returned an error: ' . $e->getMessage()], 400);
        }

        if (! isset($accessToken)) {
            return response()->json(['error' => 'No OAuth data returned from Facebook'], 400);
        }

        // Get Pages this user manages
        $response = Http::get("https://graph.facebook.com/v23.0/me/accounts", [
            'access_token' => $accessToken->getValue()
        ]);

        $pages = $response->json()['data'] ?? [];

        // Save first page (for testing)
        if (!empty($pages)) {
            $page = $pages[0];

            DB::table('facebook_pages')->updateOrInsert(
                ['page_id' => $page['id']],
                [
                    'page_name' => $page['name'],
                    'page_token' => $page['access_token'],
                    'user_access_token' => $accessToken->getValue(),
                ]
            );
        }

        return view('facebook.success', ['pages' => $pages]);
    }
}
