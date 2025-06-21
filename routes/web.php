<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AiCallController;





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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //CRUD for AI Calls table



    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/createusers', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/editusers/{user?}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users', [UserController::class, 'destroy'])->name('users.destroy');

    Route::resource('accounts', AccountController::class);
    Route::get('/accounts/{id}/contacts', [AccountController::class, 'getContacts']);
    Route::get('/accounts/{id}/leads', [AccountController::class, 'getLeads']);
    Route::get('/account', [AccountController::class, 'showaccount'])->name('account');
    Route::post('/account/store', [AccountController::class, 'store'])->name('account.store');
    Route::get('/home/account', [AccountController::class, 'account'])->name('home.account');

    Route::get('/account/{id}/edit', [AccountController::class, 'edit'])->name('account.edit');

    Route::put('/accounts/{id}', [AccountController::class, 'update'])->name('account.update');

    Route::get('Opportunities', [LeadController::class, 'Opportunities'])->name('Opportunities');
    Route::get('/leads/fetch', [LeadController::class, 'fetchLeads'])->name('leads.fetch');
    Route::resource('contacts', ContactController::class);
    Route::get('/contacts/by-lead/{lead_id}', [ContactController::class, 'getContactsByLead']);
    


    Route::middleware(['role:super admin|admin'])->group(function () {
        // Lead Routes
        Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
        Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create');
        Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
        Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
        Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
        Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
        Route::delete('/leads', [LeadController::class, 'destroy'])->name('leads.destroy');
        Route::get('/editcontact/{contact?}', [LeadController::class, 'editcontact'])->name('leadcontact');
        Route::put('/updatelead', [LeadController::class, 'updatelead'])->name('updateleadcontact');
        Route::delete('/deleteleadcontact/{contact?}', [LeadController::class, 'deleteleadcontact'])->name('deleteleadcontact');
        Route::post('savenotes', [LeadController::class, 'savenotes'])->name('leads.savenotes');
        Route::get('/leads/{lead}/notes', [LeadController::class, 'fetchNotes'])->name('leads.notes');
        Route::delete('/deletenotes', [LeadController::class, 'deletenotes'])->name('deletenotes');


    });


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

});

require __DIR__ . '/auth.php';


Route::fallback(function () {
    return response()->view('404', [], 404);
});

