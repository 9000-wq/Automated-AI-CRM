<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Plan;
use App\Models\CompanyPlan;
use DataTables;

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

}
