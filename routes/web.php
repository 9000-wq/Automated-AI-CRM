<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\UserController;




Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/createusers', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/editusers/{user?}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users', [UserController::class, 'destroy'])->name('users.destroy');

    
    Route::middleware(['role:super admin'])->group(function () {

    Route::get('/manageprices', [HomeController::class, 'index'])->name('manageprices');
    Route::get('/newplan/{companyid?}', [HomeController::class, 'newplan'])->name('newplan');
    Route::post('savenewplan', [HomeController::class,'savenewplan'])->name('savenewplan');
    Route::post('deletenewplan', [HomeController::class,'deletenewplan'])->name('deletenewplan');
    Route::get('seeallplans/{companyid?}', [HomeController::class,'seeallplans'])->name('seeallplans');
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');           // List all plans
    Route::get('/createplans', [PlanController::class, 'create'])->name('plans.create');   // Show form to create
    Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');           // Store new plan
    Route::get('/showplans', [PlanController::class, 'show'])->name('plans.show');       // View single plan
    Route::get('/editplans/{plan?}', [PlanController::class, 'edit'])->name('plans.edit');  // Show form to edit
    Route::put('/updateplans/{plan}', [PlanController::class, 'update'])->name('plans.update');   // Update plan
    Route::delete('/deleteplans', [PlanController::class, 'destroy'])->name('plans.destroy'); // Delete plan

    });

});

require __DIR__.'/auth.php';


Route::fallback(function () {
    return response()->view('404', [], 404);
});

