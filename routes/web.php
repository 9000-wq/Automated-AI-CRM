<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CallController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AiCallController;
use App\Http\Controllers\AiEmailController;
use App\Http\Controllers\EmailsController;
use App\Http\Controllers\EmailSentController;



Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/csrf-token', function () {
    return response()->json([
        'csrf_token' => csrf_token()
    ]);
});

Route::middleware('auth')->group(function () {

    // IMAP Email Settings
    // Route::get('/email-settings', [EmailController::class, 'index'])->name('email.settings');

    // // Gmail
    // Route::get('/google/auth', [EmailController::class, 'redirectToGoogle'])->name('google.auth');
    // Route::get('/auth/google/callback', [EmailController::class, 'handleGoogleCallback'])->name('google.callback');

    // // Outlook
    // Route::get('/outlook/auth', [EmailController::class, 'redirectToOutlook'])->name('outlook.auth');
    // Route::get('/outlook/callback', [EmailController::class, 'handleOutlookCallback'])->name('outlook.callback');

    // // IMAP/SMTP
    // Route::post('/imap/save', [EmailController::class, 'saveImap'])->name('imap.save');

    // Gmail
    Route::get('/auth/google/redirect', [EmailController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/auth/google/callback', [EmailController::class, 'handleGoogleCallback'])->name('google.callback');

    // Outlook
    Route::get('/auth/outlook/redirect', [EmailController::class, 'redirectToOutlook'])->name('outlook.redirect');
    Route::get('/auth/outlook/callback', [EmailController::class, 'handleOutlookCallback'])->name('services.outlook.callback');

    // IMAP
    Route::post('/imap/save', [EmailController::class, 'saveImap'])->name('imap.save');
    Route::post('/email/reset', [EmailController::class, 'emailReset'])->name('email.reset');

    // Email settings page
    Route::get('/email-settings', [EmailController::class, 'index'])->name('email.settings');

    //Profile Controller
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // compose email fetch from db or show sent page
    Route::get('/emails/sent', [EmailSentController::class, 'sent'])->name('emails.sent');
    Route::get('/emails/sent/list', [EmailSentController::class, 'sentList'])->name('emails.sent.list');

    //CRUD for AI Calls table


    //User Controller
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/createusers', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/editusers/{user?}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users', [UserController::class, 'destroy'])->name('users.destroy');

    //Account Controller
    Route::resource('accounts', AccountController::class);
    Route::get('/accounts/{id}/contacts', [AccountController::class, 'getContacts'])->name('accounts.getContacts');
    Route::get('/accounts/{id}/leads', [AccountController::class, 'getLeads'])->name('accounts.getLeads');
    Route::get('/account', [AccountController::class, 'showaccount'])->name('account');
    Route::post('/account/store', [AccountController::class, 'store'])->name('account.store');
    Route::get('/home/account', [AccountController::class, 'account'])->name('home.account');
    Route::get('/account/{id}/edit', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/accounts/{id}', [AccountController::class, 'update'])->name('account.update');

    //Lead Controller
    Route::get('Opportunities', [LeadController::class, 'Opportunities'])->name('Opportunities');
    Route::get('/leads/fetch', [LeadController::class, 'fetchLeads'])->name('leads.fetch');

    //Contact Controller
    Route::resource('contacts', ContactController::class);
    Route::get('/contacts/by-lead/{lead_id}', [ContactController::class, 'getContactsByLead'])->name('getContactsByLead');
    

    //Ai Call Controller
    Route::get('/AiCalls', [AiCallController::class,'aicalls'])->name('AiCalls');

    //Ai Email Controller
    Route::get('/AiEmails', [AiEmailController::class,'aiemails'])->name('AiEmails');



    Route::middleware(['role:super admin|admin'])->group(function () {
        // Lead Routes
       
        Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
        Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create');
        Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
        Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
        Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
        Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
        Route::post('/leads/scrape', [LeadController::class, 'scrape'])->name('leads.scrape');


        Route::delete('/leads', [LeadController::class, 'destroy'])->name('leads.destroy');
        Route::get('/editcontact/{contact?}/{leadid?}', [LeadController::class, 'editcontact'])->name('leadcontact');
        Route::put('/updatelead', [LeadController::class, 'updatelead'])->name('updateleadcontact');
        Route::delete('/deleteleadcontact/{contact?}/{lead?}', [LeadController::class, 'deleteleadcontact'])->name('deleteleadcontact');
        Route::post('savenotes', [LeadController::class, 'savenotes'])->name('leads.savenotes');
        Route::get('/leads/{lead}/notes', [LeadController::class, 'fetchNotes'])->name('leads.notes');
        Route::delete('/deletenotes', [LeadController::class, 'deletenotes'])->name('deletenotes');

        Route::get('/fetchaccounts', [LeadController::class,'fetchaccounts'])->name('fetchaccounts');

        Route::get('/companyinfo', [HomeController::class,'companyinfo'])->name('companyinfo');
        Route::post('/updatecompanyinfo/{companyid?}', [HomeController::class,'updatecompanyinfo'])->name('updatecompanyinfo');

        Route::get('/ScrapContacts', [ContactController::class,'ScrapContacts'])->name('ScrapContacts');

        Route::post('/StartScrapper',[ContactController::class,'StartScrapper'])->name('StartScrapper');


        
    });
    Route::get('/email-logs', [EmailController::class, 'showEmailLogs'])->name('email-logs');
    Route::get('/email-logs/data', [EmailController::class, 'getEmailLogsData'])->name('email-logs-data');
    Route::get('/ajax/leads', [EmailController::class, 'getLeads'])->name('ajax.leads');
    Route::post('sendleademail', [LeadController::class, 'sendleademail'])->name('sendleademail');
    Route::post('leaduploadimage', [LeadController::class, 'leaduploadimage'])->name('leaduploadimage');
    Route::post('leaduploadfile', [LeadController::class, 'leaduploadfile'])->name('leaduploadfile');

    Route::get('/call-screen/{leadid?}/{contact?}', [CallController::class, 'callScreen'])->name('call-screen');
    Route::get('generate-twilio-token', [CallController::class, 'generateTwilioToken'])->name('generate-twilio-token');

    Route::get('/create-call/{lead?}', [LeadController::class, 'createCall'])->name('create.call');
    Route::post('/create-call/{lead?}', [CallController::class, 'store'])->name('store.call');
    Route::get('/calls/history/{lead}/{call?}', [CallController::class, 'history'])->name('call.history');
    Route::post('/leads/{lead}/call-history', [CallController::class, 'updateDetails'])->name('call.history.update');
    Route::get('/ajax/users/call', [UserController::class, 'searchUsers'])->name('ajax.users.call');
    Route::middleware(['role:super admin'])->group(function () {

        Route::get('/manageprices', [HomeController::class, 'index'])->name('manageprices');
        Route::get('/newplan/{companyid?}', [HomeController::class, 'newplan'])->name('newplan');
        Route::post('savenewplan', [HomeController::class, 'savenewplan'])->name('savenewplan');
        Route::post('deletenewplan', [HomeController::class, 'deletenewplan'])->name('deletenewplan');
        Route::get('seeallplans/{companyid?}', [HomeController::class, 'seeallplans'])->name('seeallplans');
        Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');           // List all plans
        Route::get('/createplans', [PlanController::class, 'create'])->name('plans.create');   // Show form to create
        Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');           // Store new plan
        Route::get('/showplans', [PlanController::class, 'show'])->name('plans.show');       // View single plan
        Route::get('/editplans/{plan?}', [PlanController::class, 'edit'])->name('plans.edit');  // Show form to edit
        Route::put('/updateplans/{plan}', [PlanController::class, 'update'])->name('plans.update');   // Update plan
        Route::delete('/deleteplans', [PlanController::class, 'destroy'])->name('plans.destroy'); // Delete plan

    });

        //Email Controller
// Manually email compose
Route::get('/emails', [EmailsController::class, 'index'])->name('emails');

// Manually email compose
Route::post('/emails/send', [App\Http\Controllers\EmailsController::class, 'sendEmail'])->name('emails.send');


});

require __DIR__ . '/auth.php';


Route::fallback(function () {
    return response()->view('404', [], 404);
});

Route::get('/twilio/voice', [CallController::class, 'handleVoiceCall']);


