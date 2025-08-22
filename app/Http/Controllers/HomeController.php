<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Plan;
use App\Models\CompanyPlan;
use DataTables;
use Illuminate\Validation\Rule;


class HomeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Company::select('*');

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
       
                            $btn = "<a href='".route('newplan')."/$row->id' class='edit btn btn-primary'>New Plan</a>
                            <a href='".route('seeallplans')."/$row->id' class='btn btn-info'>See All Plans</a>";
      
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('prices');
    }

    public function newplan(Request $request)
    {
        $companyid= $request->companyid;
        $plans=Plan::get();

        return view('newplan')->with('companyid',$companyid)->with('plans',$plans);
    }

    public function savenewplan(Request $request){
        
      
        $validated = $request->validate([
            'plnaname'   => 'required|exists:plans,id',  
            'price'      => 'required|numeric|min:0',
            'startdate'  => 'required|date',
            'enddate'    => 'required|date|after_or_equal:startdate',
        ]);
        
        CompanyPlan::create([
            'company_id'=>$request->companyid,
            'plan_id'=> $request->plnaname,
            'start_date'=> $request->startdate,
            'end_date'=> $request->enddate,
            'custom_price'=> $request->price,
        ]);


        return response()->json(['message'=>'Plan Activate Successfully,']);
        
    }

    public function seeallplans(Request $request){
       
       $companyid = $request->companyid;
      
       $plans = CompanyPlan::join('plans', 'company_plans.plan_id', '=', 'plans.id')
       ->where('company_plans.company_id', $companyid) // optional
       ->select(
           'company_plans.*',
           'plans.name as plan_name',
           'plans.price',
           'plans.billing_cycle'
       )
       ->orderBy('company_plans.start_date', 'desc')
       ->get();
   

       return view('seeallplans')->with('plans',$plans);
    }

    public function deletenewplan(Request $request)
    {
        $planid= $request->planid;

        CompanyPlan::find($planid)->delete();
        return response()->json(['message'=>'Plan Inactive Successfully.']);


    }


    public function companyinfo()
    {
        $companyid= auth()->user()->company_id;
        $companyinfo= Company::find($companyid);
        return view('companyinfo')->with('companyinfo',$companyinfo);
    }

    public function updatecompanyinfo(Request $request)
    {

       
        $request->validate([
            'companyName' => ['required'],
            'company_email' => ['required', 'string', 'lowercase', 'email', 'max:255',
            Rule::unique('companies', 'company_email')->ignore($request->companyid, 'id'), // adjust column name if not 'id'
            ],
            'companyAddress' => ['required'],
            'country' => ['required'],
            'companyDescription' => ['required'],
            'business_type' => ['required'],

            // 👇 Conditional rules
            'serviceKnowledge' => ['required_if:business_type,service'],
            'productKnowledge' => ['required_if:business_type,product'],

            'priceGuidelines' => ['required'],
        ]);


        if( $request->business_type == 'service'){

            $company = Company::where('id',$request->companyid)->update([
                'company_name'   => $request->companyName,
                'business_type'  => $request->business_type,
                'company_email'  => $request->company_email,
                'company_address'=> $request->companyAddress,
                'country'        => $request->country,
                'company_description'=>$request->companyDescription,
                'price_guidelines'=>$request->priceGuidelines,
                'bussiness_knowledge'=>$request->serviceKnowledge,
            ]);

        }else{
           
            $company = Company::where('id',$request->companyid)->update([
                'company_name'   => $request->companyName,
                'business_type'  => $request->business_type,
                'company_email'  => $request->company_email,
                'company_address'=> $request->companyAddress,
                'country'        => $request->country,
                'company_description'=>$request->companyDescription,
                'price_guidelines'=>$request->priceGuidelines,
                'bussiness_knowledge'=>$request->productKnowledge,
            ]);
        }


        return redirect()->route('companyinfo')->with('success', 'User updated successfully.');


    }

}
