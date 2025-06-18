<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\ContactRole;
use App\Models\Contact;

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
                        return '<span class="btn btn-sm btn-secondary">InActive</span>';
                    }
                })
                ->addColumn('action', function ($row) {
    return '<div class="d-flex gap-1">
                <a href="' . route('account.edit', $row->id) . '" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i>
                </a>
                <button id="' . $row->id . '" class="deletebtn btn btn-danger btn-sm">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>';
})

                ->rawColumns(['status', 'action']) // Allow HTML rendering
                ->make(true);
        }

        return view('account');
    }

   public function edit($id)
{
    $account = Account::with('contacts.ContactRole')->findOrFail($id);
    return view('editaccount', compact('account'));
}



    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|digits_between:7,15',
            'website' => 'nullable|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            
        ]);

        if ($request->has('contacts') && is_array($request->contacts) && count($request->contacts) > 0) {
            $rules['contacts'] = 'array|min:1';
    
            foreach ($request->contacts as $index => $contact) {
                $rules["contacts.$index.name"] = 'required|string|max:255';
                $rules["contacts.$index.email"] = 'required|email|max:255|unique:contacts,email';
                $rules["contacts.$index.phone"] = 'required|string|max:20';
                $rules["contacts.$index.birthday"] = 'nullable|date';
                $rules["contacts.$index.address"] = 'nullable|string|max:255';
                $rules["contacts.$index.description"] = 'nullable|string';
                $rules["contacts.$index.contact_role"] = 'required';
            }

            $validated = $request->validate($rules);
        }
    
       

        $account=Account::create($validatedData);


        if (!empty($validated['contacts'])) {
            foreach ($validated['contacts'] as $contactData) {

                $rolevalue = ContactRole::firstOrCreate(
                    ['label' => $contactData['contact_role']]
                );

                Contact::create([
                    'name'            => $contactData['name'],
                    'email'           => $contactData['email'],
                    'phone'           => $contactData['phone'],
                    'birthday'        => $contactData['birthday'] ?? null,
                    'address'         => $contactData['address'] ?? null,
                    'description'     => $contactData['description'] ?? null,
                    'contact_role_id' => $rolevalue->id,
                    'account_id'=>$account->id
                ]);
            }
        }
   

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
        'name' => 'required',
        'email' => 'required|email',
        'status' => 'required',
        // other account validations...
    ]);

    if ($request->has('contacts') && is_array($request->contacts) && count($request->contacts) > 0) {
            $rules['contacts'] = 'array|min:1';
    
            foreach ($request->contacts as $index => $contact) {
  
               
                    $rules["contacts.$index.name"] = 'required|string|max:255';
                    $rules["contacts.$index.phone"] = 'required|string|max:20';
                    $rules["contacts.$index.birthday"] = 'nullable|date';
                    $rules["contacts.$index.address"] = 'nullable|string|max:255';
                    $rules["contacts.$index.description"] = 'nullable|string';
                    $rules["contacts.$index.contact_role"] = 'required';
                
                    if (!isset($contact['contact_id'])) {
                        $rules["contacts.$index.email"] = 'required|email|max:255|unique:contacts,email';
                    } else {
                        $rules["contacts.$index.email"] = 'required|email|max:255';
                    }

            }

            $validated = $request->validate($rules);
        }

    $account = Account::findOrFail($id);
    $account->update($validatedData);

    // Add updated contacts
    if ($request->contacts) {
        foreach ($request->contacts as $contact) {
            $role = ContactRole::firstOrCreate(['label' => $contact['contact_role'] ?? '']);

        if (isset($contact['contact_id'])) {

                Contact::where('id',$contact['contact_id'] )->update([
                'name' => $contact['name'],
                'email' => $contact['email'],
                'phone' => $contact['phone'],
                'birthday' => $contact['birthday'] ?? null,
                'address' => $contact['address'] ?? null,
                'description' => $contact['description'] ?? null,
                'contact_role_id' => $role->id,
                'account_id'=>$account->id
            ]);
        }
        else{
                Contact::create([
                'name' => $contact['name'],
                'email' => $contact['email'],
                'phone' => $contact['phone'],
                'birthday' => $contact['birthday'] ?? null,
                'address' => $contact['address'] ?? null,
                'description' => $contact['description'] ?? null,
                'contact_role_id' => $role->id,
                'account_id'=>$account->id
            ]);
        }
           
        }
    }

    return redirect()->route('home.account')->with('success', 'Account updated with contacts');
}


   public function destroy($id)
{
    $account = Account::findOrFail($id);

    // Delete all associated contacts first
    $account->contacts()->delete();

    // Then delete the account
    $account->delete();

    return response()->json(['message' => 'Account and associated contacts deleted successfully']);
}

}
