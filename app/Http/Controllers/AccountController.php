<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

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

    public function showaccount()
    {
        return view('createaccount');
    }

    public function account(Request $request)
    {
        if ($request->ajax()) {
            $data = Account::latest()->get();

            return DataTables::of($data)
                ->addColumn('status', function ($row) {
                    if ($row->status === 'active') {
                        return '<span class="btn btn-sm btn-success">Active</span>';
                    } else {
                        return '<span class="btn btn-sm btn-danger">Inactive</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('account.edit', $row->id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
                    $btn .= '<button id="' . $row->id . '" class="deletebtn btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></button>';
                    return $btn;
                })
                ->editColumn('phone', function ($row) {
                    return $row->number;
                })
                ->rawColumns(['status', 'action']) // Allow HTML rendering
                ->make(true);
        }

        return view('account');
    }

    public function edit($id)
    {
        $account = Account::findOrFail($id);
        return view('editaccount', compact('account'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'number' => 'required|digits_between:7,15',
            'website' => 'nullable|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        Account::create($validatedData);

        return redirect()->route('home.account')->with('success', 'Account created successfully!');
    }

    public function show($id)
    {
        $account = Account::findOrFail($id);
        return response()->json($account);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'number' => 'required|digits_between:7,15',
            'website' => 'nullable|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $account = Account::findOrFail($id);
        $account->update($validatedData);

        return redirect()->route('home.account')->with('success', 'Account edited successfully!');
    }

    public function destroy($id)
    {
        $account = Account::findOrFail($id);
        $account->delete();

        return response()->json(['message' => 'Account deleted']);
    }
}
