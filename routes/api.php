<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiCallController;
use App\Http\Controllers\AiEmailController;
use App\Http\Controllers\SalesPipelineController;
use Illuminate\Http\Request;          // ✅ This one is correct
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Company;
use App\Http\Controllers\LeadScoreController;

Route::middleware('auth:sanctum')->prefix('lead-scores')->group(function () {
    Route::get('/', [LeadScoreController::class, 'index']);
    Route::post('/', [LeadScoreController::class, 'store']);
    Route::get('/{lead_id}/{contact_id}', [LeadScoreController::class, 'show']);
    Route::put('/{lead_id}/{contact_id}', [LeadScoreController::class, 'update']);
    Route::delete('/{lead_id}/{contact_id}', [LeadScoreController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/sales_pipeline', [SalesPipelineController::class, 'index']);
    Route::post('/sales_pipeline', [SalesPipelineController::class, 'store']);
    Route::get('/sales_pipeline/{id}', [SalesPipelineController::class, 'show']);
    Route::put('/sales_pipeline/{id}', [SalesPipelineController::class, 'update']);
    Route::delete('/sales_pipeline/{id}', [SalesPipelineController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/ai_calls', [AiCallController::class, 'index']);
    Route::post('/ai_calls', [AiCallController::class, 'store']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('ai_emails', AiEmailController::class);
});


Route::post('/token', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $user = User::where('email', $request->email)->first();

    return response()->json([
        'token' => $user->createToken('api-token')->plainTextToken
    ]);
});



Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:sanctum')->post('/insertContacts', function (Request $request) {
 
    $data=$request->data;
    $company_id=$request->company_id;

    Company::where('id',$company_id)->update([
        'scrapper'=> 0,
        'scrapper_date_time'=>date('Y-m-d H:i:s')
    ]);

    return response()->json(['Message'=>'Contacts Inserted Successfully.']);



});




