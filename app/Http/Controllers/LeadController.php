<?php

namespace App\Http\Controllers;
use App\Models\Call;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\ContactRole;
use App\Models\User;
use App\Models\Note;
use App\Models\Account;
use App\Models\LeadContact;
use App\Models\EmailLog;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeadEmail;
use DataTables;
use Illuminate\Validation\Rule;
use DB;
use Illuminate\Support\Facades\Log;



class LeadController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            if (auth()->user()->user_role == 'super admin') {
                $data = Lead::select('leads.*', 'users.name as assigned_user_name')->leftjoin('users', 'leads.assigned_to', '=', 'users.id');
            } else {
                $data = Lead::select('leads.*', 'users.name as assigned_user_name')->leftjoin('users', 'leads.assigned_to', '=', 'users.id')->where('leads.company_id', auth()->user()->company_id);
            }




            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "<a href='" . route('leads.show', $row->id) . "' class='btn btn-info'><i class='fas fa-eye'></i></a>
                            <a href='" . route('leads.edit', $row->id) . "' class='btn btn-warning'><i class='fas fa-edit'></i></a>
                            <button class='btn btn-danger deletebtn' id='$row->id'><i class='fas fa-trash'></i></button>
                            <button href='" . route('leads.scrape', $row->id) . "' class='btn btn-secondary scrapeBtn'><i class='fas fa-robot'></i></button>
                            <a href='" . route('leadcontact') . "/lead$row->id' class='btn btn-primary addContact' title='Add Contact' id='$row->id'><i class='fas fa-address-book	'></i></a>";
                    return $btn;
                })->editColumn('status', function ($row) {
                    $status = $row->status;
                    $color = match ($status) {
                        'New' => 'primary',
                        'Contacted' => 'info',
                        'Follow-up' => 'warning',
                        'Converted' => 'success',
                        'Lost' => 'danger',
                        default => 'secondary',
                    };

                    return '<button class="btn btn-sm btn-' . $color . '">' . e($status) . '</button>';
                })
                ->filterColumn('assigned_user_name', function ($query, $keyword) {
                    $query->where('users.name', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('leads.index');
    }

    public function create()
    {
        $users = User::select('name', 'id')->where('company_id', auth()->user()->company_id)->get();
        return view('leads.create')->with('users', $users);
    }

    public function store(Request $request)
    {



        $validatedData = $request->validate([
            'case_ref' => 'required',
            'name' => 'required',
            'source' => 'required',
            'status' => 'required',

        ]);

        $lead = Lead::create(array_merge(
            $request->only(['case_ref', 'name', 'source', 'status', 'assigned_to']),
            ['company_id' => auth()->user()->company_id]
        ));


        if ($request->account != '' and $request->account != NULL) {
            $contactids = Contact::where('account_id', $request->account)->pluck('id');

            foreach ($contactids as $contactid) {
                LeadContact::create([
                    'lead_id' => $lead->id,
                    'contact_id' => $contactid,
                    'account_id' => $request->account
                ]);
            }
        }


        //if any user eneter contacts in contact form while creating lead
        if ($request->has('contacts')) {

            foreach ($request->contacts as $contact) {


                if ($contact['full_name'] != NULL and $contact['full_name'] != '') {

                    $role = ContactRole::firstOrCreate(
                        ['label' => $contact['role']]
                    );

                    $contact = Contact::create([
                        'name' => $contact['full_name'],
                        'email' => $contact['email'],
                        'birthday' => $contact['birthday'],
                        'phone' => $contact['phone'],
                        'description' => $contact['description'],
                        'address' => $contact['address'],
                        'contact_role_id' => $role->id,
                        'company_id' => auth()->user()->company_id

                    ]);

                    LeadContact::create([
                        'lead_id' => $lead->id,
                        'contact_id' => $contact->id
                    ]);


                }

            }
        }

        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function createCall($leadId = null)
    {
        // Optional: fetch the lead if needed
        $lead = null;
        if ($leadId) {
            $lead = Lead::find($leadId);
        }

        return view('leads.create_call', compact('lead'));
    }


public function scrape($leadId = null): \Illuminate\Http\JsonResponse
{
    // Optional: fetch the lead if needed
    $lead = null;
    if ($leadId) {
        $lead = Lead::find($leadId);
    }

    $results = Lead::select(
            'leads.name as customer_name',
            'c.company_name',
            'c.company_description',
            'c.bussiness_knowledge',
            'c.price_guidelines',
            'c.business_type',
            'c.company_email',
            'contacts.email',
            'contacts.phone'
        )
        ->join('companies as c', 'c.id', '=', 'leads.company_id')
        ->leftJoin('lead_contacts as lc', 'lc.lead_id', '=', 'leads.id')
        ->leftJoin('contacts', 'contacts.id', '=', 'lc.contact_id')
        ->where('leads.source', 'Email')
        ->where('leads.status', 'New')
        ->where('leads.id',$leadId)
        ->get();

    $userId = Auth::id();

    $firstResult = $results->first();

    // prepare payload
    $companyDescription = '';
    if ($firstResult) {
        $companyDescription = $firstResult->company_description 
            . ' | Knowledge: ' . $firstResult->bussiness_knowledge 
            . ' | Pricing: ' . $firstResult->price_guidelines;
    }

    $payload = [
        'user_id' => $userId,
        'to' => $firstResult->email ?? '', 
        'our_company_name' => $firstResult->company_name ?? '', 
        'customer_name' => $firstResult->customer_name ?? '', 
        'company_description' => $companyDescription,
        'number' => (int) $firstResult->phone_number ?? 0
    ];

    $response = Http::post(env('API_URL_ENDPOINT').'/send-email', $payload);

    return response()->json([
        'success' => true,
        'lead' => $lead,
        'results' => $results,
        'api_response' => $response->json()
    ], 200);
}

  public function show(Lead $lead)
{
    $activities = Call::where('lead_id', $lead->id)
                    ->whereIn('status', ['planned'])
                    ->orderBy('date_start', 'desc')
                    ->get();

    $history = Call::where('lead_id', $lead->id)
                 ->where('status', 'held')
                 ->orderBy('date_start', 'desc')
                 ->get();

    return view('leads.show', compact('lead', 'activities', 'history'));
}

// public function getUsers(Request $request)
// {
//     $search = $request->input('search');
    
//     $users = User::query()
//         ->where('company_id', auth()->user()->company_id)
//         ->when($search, function($query, $search) {
//             return $query->where('name', 'like', "%{$search}%");
//         })
//         ->select(['id', 'name as text']) // Note: using 'text' as key
//         ->paginate(10);

//     return response()->json([
//         'data' => $users->items(),
//         'total' => $users->total()
//     ]);
// }

    public function edit(Lead $lead)
    {
        $users = User::select('name', 'id')->where('company_id', auth()->user()->company_id)->get();
        return view('leads.edit', compact('lead'))->with('users', $users);
    }

    public function update(Request $request, Lead $lead)
    {
        $lead->update($request->only([
            'case_ref',
            'name',
            'source',
            'status',
            'assigned_to'
        ]));

        // Optional: Update contacts logic

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(Request $request)
    {
        $lead = Lead::find($request->leadid);

        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found.');
        }

        $lead->delete();

        return redirect()->back()->with('success', value: 'Lead deleted successfully.');
    }



    public function editcontact(Request $request)
    {
        $contact = [];
        $leadid = $request->contact;
        $lead_id=$request->leadid;
        if (
            $request->contact != null &&
            $request->contact != '' &&
            !str_contains($request->contact, 'lead')
        ) {
            $contact = Contact::where('id', $request->contact)->get();

        }

        return view('leads.editcontact')->with('contact', $contact)->with('leadid', $leadid)->with('lead_id',$lead_id);
    }

    public function updatelead(Request $request)
    {
        $contactid = $request->contactid;
        $name = $request->full_name;
        $phone = $request->phone;
        $email = $request->email;
        $role = $request->role;
        $address = $request->address;
        $description = $request->description;
        $birthday = $request->birthday;
        $leadid = $request->leadid;


        $rules = [
            'full_name' => 'required',
            'phone' => 'required|numeric',
            'email' => ['required'],
            'role' => 'required',
            'address' => 'required',
            'description' => 'required',
            'birthday' => 'required',
        ];

        if (str_contains($contactid, 'lead')) {
            $rules['email'][] = Rule::unique('contacts', 'email');
        }

        $validatedData = $request->validate($rules);



        if (!str_contains($contactid, 'lead')) {

            $rolevalue = ContactRole::firstOrCreate(
                ['label' => $role]
            );

            $contact = Contact::find($contactid);
            $contact->name = $name;
            $contact->phone = $phone;
            $contact->email = $email;
            $contact->contact_role_id = $rolevalue->id;
            $contact->address = $address;
            $contact->description = $description;
            $contact->birthday = $birthday;
            $contact->company_id = auth()->user()->company_id;
            $contact->update();

            return redirect()->route('leads.show', ['lead' => $leadid]);
        } else {

            $contactid = str_replace('lead', '', $contactid);

            $rolevalue = ContactRole::firstOrCreate(['label' => $role]);

            $account_id = $request->input('account_id');

            $contact = Contact::create([
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'description' => $description,
                'birthday' => $birthday,
                'contact_role_id' => $rolevalue->id,
                'account_id' => $account_id,
                'company_id' => auth()->user()->company_id

            ]);

            LeadContact::create([
                'lead_id' => $contactid,
                'contact_id' => $contact->id,
            ]);

            return redirect()->route('leads.index');

        }


    }

    public function deleteleadcontact(Request $request)
    {

        LeadContact::where('contact_id', $request->contact)->where('lead_id', $request->lead)->delete();
        return redirect()->back()->with('success', 'Contact deleted successfully');

    }

    public function savenotes(Request $request)
    {

        $notes = $request->notes;

        $validatedData = $request->validate([
            'notes' => 'required',
        ]);

        Note::create([
            'lead_id' => $request->leadid,
            'user_id' => auth()->user()->id,
            'notes' => $notes
        ]);

        return response()->json(['Success' => 'Notes Added Successfully']);

    }


    public function fetchNotes(Request $request, $leadId)
    {
        $pageno = $request->page;
        $notes = Note::join('users', 'notes.user_id', 'users.id')->select('notes.*', 'users.name')->where('lead_id', $leadId)
            ->orderBy('notes.created_at', 'desc')
            ->paginate(10); // Load 10 notes at a time

        // if ($request->ajax()) {
        //     return view('leads.note_items', compact('notes'))->render();
        // }

        return view('leads.shownotes', compact('notes', 'leadId', 'pageno'));
    }

    public function deletenotes(Request $request)
    {
        $note = Note::find($request->noteid);

        if (!$note) {
            return response()->json(['error' => 'Note not found.'], 404);
        }

        $note->delete();

        return response()->json(['success' => 'Note Deleted Successfully.']);
    }



    public function Opportunities()
    {
        return view('opportunities');
    }

    // LeadController.php
    public function fetchLeads(Request $request)
    {
        $status = $request->input('status');
        $page = $request->input('page', 1);
        $search = $request->input('search');

        $leads = Lead::where('status', $status)
            ->where('company_id', auth()->user()->company_id)
            ->when(!empty($search), function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('case_ref', 'like', "%{$search}%")
                        ->orWhere('source', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->paginate(10, ['*'], 'page', $page);


        return view('layouts.leads', compact('leads', 'status'))->render();
    }


    public function fetchaccounts(Request $request)
    {
        $search = $request->q;

        $accounts = Account::where('name', 'like', "%$search%")
            ->orwhere('email', 'like', "%$search%")
            ->select('id', 'name')
            ->limit(20)
            ->get();

        $results = $accounts->map(function ($account) {
            return ['id' => $account->id, 'text' => $account->name];
        });

        return response()->json($results);
    }


    
    function extractFilePathsFromHtml($html)
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // Prevents warning on malformed HTML
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $files = [];

        // Extract images
        foreach ($dom->getElementsByTagName('img') as $img) {
            $src = $img->getAttribute('src');
            if ($this->isLocalFile($src)) {
                $files[] = $this->convertToStoragePath($src);
            }
        }

        // Extract file links (e.g., PDFs or docs)
        foreach ($dom->getElementsByTagName('a') as $a) {
            $href = $a->getAttribute('href');
            if ($this->isLocalFile($href)) {
                $files[] = $this->convertToStoragePath($href);
            }
        }

        return $files;
    }

    function isLocalFile($url)
    {
        return str_contains($url, '/storage/') || str_contains($url, '/uploads/');
    }

    function convertToStoragePath($url)
    {
        // Example: http://yoursite.com/storage/uploads/file.pdf → uploads/file.pdf
        $relative = parse_url($url, PHP_URL_PATH);
        return ltrim(str_replace('/storage/', 'public/', $relative), '/');
    }




   public function sendleademail(Request $request)
    {
        

            $validated = $request->validate([
                'to' => 'required|email',
                'subject' => 'required|string|max:255',
                'body' => 'required|string',
            ]);


    
            $attachments = $this->extractFilePathsFromHtml($request->body);

            $ccemails=explode(',',$request->cc);

            Mail::to($validated['to'])
                ->cc($ccemails)
                ->queue(new LeadEmail($validated['subject'], $validated['body'], $attachments));

            // Save to database after successful email send
            $emaillog = new EmailLog();
            $emaillog->email = $request->to;
            $emaillog->cc = $request->cc;
            $emaillog->subject = $request->subject;
            $emaillog->body = $request->body;
            $emaillog->lead_id = $request->leadid;
            $emaillog->save();

            return response()->json(['success' => 'Email sent successfully.']);
        
    }

    public function leaduploadImage(Request $request)
    {
        $file = $request->file('file');
        $filename = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('uploads/email'), $filename);

        return response()->json([
            'link' => asset('uploads/email/' . $filename)
        ]);
    }

    public function leaduploadFile(Request $request)
    {
        $file = $request->file('file');
        $filename = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('uploads/email'), $filename);

        return response()->json([
            'link' => asset('uploads/email/' . $filename)
        ]);
    }




}
