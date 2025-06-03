<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function getContacts($id)
    {
        $account = Account::with('contacts')->find($id);

        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        return response()->json($account->contacts);
    }

    public function getLeads($id)
    {
        $account = Account::with('leads')->find($id);

        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        return response()->json($account->leads);
    }

    public function index()
    {
        return response()->json(Account::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $account = Account::create($request->all());

        return response()->json($account, 201);
    }

    public function show($id)
    {
        $account = Account::findOrFail($id);
        return response()->json($account);
    }

    public function update(Request $request, $id)
    {
        $account = Account::findOrFail($id);
        $account->update($request->all());

        return response()->json($account);
    }

    public function destroy($id)
    {
        $account = Account::findOrFail($id);
        $account->delete();

        return response()->json(['message' => 'Account deleted']);
    }
}
