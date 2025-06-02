<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\User;
use DataTables;

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

        if ($request->has('contacts')) {
            foreach ($request->contacts as $contact) {
                $lead->contacts()->create($contact);
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

        $validatedData = $request->validate([
            'full_name'   => 'required',
            'phone'      => 'required|numeric',
            'email'  => 'required',
            'role'  => 'required',
            'address'    => 'required',
        ]);

        if(!str_contains($contactid, 'lead')){

            $contact=Contact::find($contactid);
            $contact->full_name=$name;
            $contact->phone=$phone;
            $contact->email=$email;
            $contact->role=$role;
            $contact->address=$address;
            $contact->update();
        
            return redirect()->route('leads.show', ['lead' => $contact->lead_id]);
        }else{
            $contactid=str_replace('lead','',$contactid);
            Contact::create([
                'full_name'=>$name,
                'phone'=>$phone,
                'email'=>$email,
                'address'=>$address,
                'role'=>$role,
                'lead_id'=>$contactid,
            ]);

            return redirect()->route('leads.index');
        }
        

    }

    public function deleteleadcontact(Request $request)
    {

        Contact::where('id',$request->contact)->delete();
        return redirect()->back()->with('success', 'Contact deleted successfully');

    }

}
