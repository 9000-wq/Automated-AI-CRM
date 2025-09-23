<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailAccount;
use Illuminate\Support\Facades\Auth;

class EmailsController extends Controller
{
    public function index(){
        $accounts = EmailAccount::where('user_id', Auth::id())->get();
        return view('emails', compact('accounts'));
    }
}
