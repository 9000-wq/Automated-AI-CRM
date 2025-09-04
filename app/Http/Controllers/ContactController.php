<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactRole;
use App\Models\Lead;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Company;
use Auth;
use Carbon\Carbon;



class ContactController extends Controller
{
    public function getContactsByLead($lead_id)
    {
        // Validate lead_id exists
        $lead = Lead::find($lead_id);
        if (!$lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }

        // Get contacts for the lead with role relationship loaded
        $contacts = Contact::with('role')->where('lead_id', $lead_id)->get();

        return response()->json(['contacts' => $contacts]);
    }

    // Get all contacts
    public function index(Request $request)
    {
        if ($request->ajax()) {


        
                if(auth()->user()->user_role =='super admin'){
                    $contacts = Contact::with(['role', 'leads']);
                }else{
                    $contacts = Contact::with(['role', 'leads'])->where('company_id',auth()->user()->company_id);
                }

                return DataTables::of($contacts)
                    
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('contacts.show', $row->id) . '" class="btn  btn-warning"><i class="fas fa-edit"></i></a> ';
                    $btn .= '<button id="' . $row->id . '" class="deletebtn btn  btn-danger"><i class="fas fa-trash-alt"></i></button>';
                    return $btn;
                })
                ->editColumn('leadname', function ($row) {
                    return $row->leads->first()->name ?? '';
                })     
                ->filterColumn('leadname', function($query, $keyword) {
                    $query->whereHas('leads', function($q) use ($keyword) {
                        $q->where('leads.name', 'like', "%{$keyword}%");
                    });
                })
                
                ->rawColumns(['status', 'action']) 
                ->make(true);

        }

        return view('contacts.index');
    }

    // Store a new contact
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'birthday'         => 'nullable|date',
            'phone'            => 'nullable|string|max:20',
            'address'          => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'lead_id'          => 'required|exists:leads,id',
            'contact_role_id'  => 'required|exists:contact_roles,id',
        ]);

        $validated['company_id'] = auth()->user()->company_id; 

        $contact = Contact::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Contact created successfully',
            'data' => $contact
        ], 201);
    }

    // Get a single contact
    public function show(Contact $contact)
    {
        $contact->load(['role', 'lead']);
        return view('contacts.show')->with('contact',$contact);
    }

    // Update a contact
    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'birthday'         => 'nullable|date',
            'phone'            => 'nullable|string|max:20',
            'address'          => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'role'  => 'required',
        ]);

        $rolevalue = ContactRole::firstOrCreate(
            ['label' => $request->role]
        ); 

        $contact->update([
                'name'=>$request->name,
                'email'=>$request->email,
                'birthday'=>$request->birthday,
                'phone'=>$request->phone,
                'address'=>$request->address,
                'description'=>$request->description,
                'contact_role_id'=>$rolevalue->id,
                'company_id'=>auth()->user()->company_id

            ]);

            return redirect()->back()->with('success', 'Contact updated successfully');

    }

    // Delete a contact
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully'
        ]);
    }


    public function ScrapContacts()
    {

        $companyInfo = Company::find(Auth::user()->company_id);

        // Access specific columns
        $scrapper = $companyInfo->scrapper;
        $scrapperTime = $companyInfo->scrapper_date_time;

        // Convert to Carbon instance
        $scrapperTime = Carbon::parse($scrapperTime);

        // Get human-readable difference
        $scrapperTime= $scrapperTime->diffForHumans();  



        return view('ScrapContacts')->with('scrapper',$scrapper)->with('scrapperTime',$scrapperTime);
    }


   
    public function StartScrapper(Request $request)
    {
        $companyId = $request->companyid;

        // Find the company
        $company = Company::find($companyId);
        if (!$company) {
            return response()->json(['error' => 'Company not found'], 404);
        }

        // Prepare the data to send to the API
        $payload = [
            'companyid' => $companyId,
            'data' => $company  // Sending the whole company object
        ];

        try {
            // Call external API
            // $response = Http::post('https://example.com/your-api-endpoint', $payload);

            // // Check if API call was successful
            // if ($response->successful()) {
                // Update scrapper column after successful API call
                $company->update([
                    'scrapper' => 1
                ]);

                return response()->json([
                    'message' => 'Scrapper started and API called successfully',
                    'company' => $company
                ]);
            // } else {
            //     return response()->json([
            //         'message' => 'API call failed',
            //         'response' => $response->body()
            //     ], 500);
            // }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error calling API',
                'error' => $e->getMessage()
            ], 500);
        }
    }




}
