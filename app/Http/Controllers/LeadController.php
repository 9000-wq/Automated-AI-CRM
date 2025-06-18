<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\ContactRole;
use App\Models\User;
use App\Models\Note;
use App\Models\Account;
use App\Models\LeadContact;
use DataTables;
use Illuminate\Validation\Rule;
use DB;


class LeadController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            if(auth()->user()->user_role =='super admin'){
                $data = Lead::select('leads.*', 'users.name as assigned_user_name')->leftjoin('users', 'leads.assigned_to', '=', 'users.id');
            }else{
                $data =   Lead::select('leads.*', 'users.name as assigned_user_name')->leftjoin('users', 'leads.assigned_to', '=', 'users.id')->where('leads.company_id',auth()->user()->company_id);
            }

          


            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
       
                            $btn = "<a href='".route('leads.show',$row->id)."' class='btn btn-info'><i class='fas fa-eye'></i></a>
                            <a href='".route('leads.edit',$row->id)."' class='btn btn-warning'><i class='fas fa-edit'></i></a>
                            <button class='btn btn-danger deletebtn' id='$row->id'><i class='fas fa-trash'></i></button>
                            <a href='".route('leadcontact')."/lead$row->id' class='btn btn-primary addContact' title='Add Contact' id='$row->id'><i class='fas fa-address-book	'></i></a>";
                            return $btn;
                    })->editColumn('status',function($row){
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
                    ->filterColumn('assigned_user_name', function($query, $keyword) {
                        $query->where('users.name', 'like', "%{$keyword}%");
                    })
                    ->rawColumns(['action','status'])
                    ->make(true);
        }

        return view('leads.index');
    }

    public function create()
    {
        $users=User::select('name','id')->where('company_id',auth()->user()->company_id)->get();
        return view('leads.create')->with('users',$users);
    }

    public function store(Request $request)
    {



        $validatedData = $request->validate([
            'case_ref'   => 'required',
            'name'      => 'required',
            'source'  => 'required',
            'status'    => 'required',

        ]);

        $lead =  Lead::create(array_merge(
            $request->only(['case_ref', 'name', 'source', 'status', 'assigned_to']),
            ['company_id' => auth()->user()->company_id]
        ));


        if($request->account !='' AND  $request->account !=NULL){
            $contactids=Contact::where('account_id',$request->account)->pluck('id');
 
            foreach($contactids as $contactid){
                 LeadContact::create([
                     'lead_id'=>$lead->id,
                     'contact_id'=>$contactid,
                     'account_id'=>$request->account
                 ]);   
            }
        }

        
        //if any user eneter contacts in contact form while creating lead
        if ($request->has('contacts')) {
            
            foreach ($request->contacts as $contact) {


                if($contact['full_name'] != NULL AND $contact['full_name'] !=''){
                    
                    $role = ContactRole::firstOrCreate(
                        ['label' => $contact['role']]
                    );  

                   $contact= Contact::create([
                        'name'=>$contact['full_name'],
                        'email'=>$contact['email'],
                        'birthday'=>$contact['birthday'],
                        'phone'=>$contact['phone'],
                        'description'=>$contact['description'],
                        'address'=>$contact['address'],
                        'contact_role_id'=>$role->id
                    ]);

                    LeadContact::create([
                        'lead_id'=>$lead->id,
                        'contact_id'=>$contact->id
                    ]);
                    

                }              

            }
        }

        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
      
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $users=User::select('name','id')->where('company_id',auth()->user()->company_id)->get();
        return view('leads.edit', compact('lead'))->with('users',$users);
    }

    public function update(Request $request, Lead $lead)
    {
        $lead->update($request->only([
            'case_ref', 'name', 'source', 'status', 'assigned_to'
        ]));

        // Optional: Update contacts logic

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(Request $request)
    {
        $leadid=$request->leadid;
        Lead::find($leadid)->delete();
        return back()->with('success', 'Lead deleted.');
    }

    public function editcontact(Request $request)
    {
        $contact=[];
        $leadid=$request->contact;
        if (
            $request->contact != null &&
            $request->contact != '' &&
            !str_contains($request->contact, 'lead')
        ){
            $contact=Contact::where('id',$request->contact)->get();

        }

        return view('leads.editcontact')->with('contact',$contact)->with('leadid',$leadid);
    }

    public function updatelead(Request $request)
    {
        $contactid=$request->contactid;
        $name=$request->full_name;
        $phone=$request->phone;
        $email=$request->email;
        $role=$request->role;
        $address=$request->address;
        $description=$request->description;
        $birthday=$request->birthday;


        $rules = [
            'full_name'   => 'required',
            'phone'       => 'required|numeric',
            'email'       => ['required'],
            'role'        => 'required',
            'address'     => 'required',
            'description' => 'required',
            'birthday'    => 'required',
        ];
        
        if (str_contains($contactid, 'lead')) {
            $rules['email'][] = Rule::unique('contacts', 'email');
        }
        
        $validatedData = $request->validate($rules);

        

        if(!str_contains($contactid, 'lead')){

            $rolevalue = ContactRole::firstOrCreate(
                ['label' => $role]
            ); 

            $contact=Contact::find($contactid);
            $contact->name=$name;
            $contact->phone=$phone;
            $contact->email=$email;
            $contact->contact_role_id=$rolevalue->id;
            $contact->address=$address;
            $contact->description=$description;
            $contact->birthday=$birthday;
            $contact->update();
        
            return redirect()->route('leads.show', ['lead' => $contact->lead_id]);
        }else{
           
            $contactid = str_replace('lead', '', $contactid);

            $rolevalue = ContactRole::firstOrCreate(['label' => $role]);

            $account_id = $request->input('account_id'); 

            $contact = Contact::create([
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'desscription' => $description,
                'birthday' => $birthday,
                'contact_role_id' => $rolevalue->id,
                'lead_id' => $contactid,
                'account_id' => $account_id, 
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

        LeadContact::where('contact_id',$request->contact)->where('lead_id',$request->lead)->delete();
        return redirect()->back()->with('success', 'Contact deleted successfully');

    }

    public function savenotes(Request $request){

       $notes= $request->notes;

       $validatedData = $request->validate([
        'notes'   => 'required',
        ]);

        Note::create([
            'lead_id'=>$request->leadid,
            'user_id'=>auth()->user()->id,
            'notes'=>$notes
        ]);

        return response()->json(['Success'=>'Notes Added Successfully']);

    }


    public function fetchNotes(Request $request, $leadId)
    {
        $pageno= $request->page;
        $notes = Note::join('users','notes.user_id','users.id')->select('notes.*', 'users.name')->where('lead_id', $leadId)
            ->orderBy('notes.created_at', 'desc')
            ->paginate(10); // Load 10 notes at a time

        // if ($request->ajax()) {
        //     return view('leads.note_items', compact('notes'))->render();
        // }

        return view('leads.shownotes', compact('notes', 'leadId','pageno'));
    }

    public function deletenotes(Request $request)
    {
        $noteid= $request->noteid;
        Note::find($noteid)->delete();

        return response()->json(['success'=>'Note Deleted Successfully.']);
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


}
