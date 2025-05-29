<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use Hash;
use DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
     public function index(Request $request)
     {
        if ($request->ajax()) {

            if(auth()->user()->user_role =='super admin'){
                $data = User::select('*');
            }else{
                $data = User::select('*')->where('company_id',auth()->user()->company_id);
            }

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
       
                            $btn = "<a href='".route('users.edit')."/$row->id' class='btn btn-warning'><i class='fas fa-edit'></i></a>
                            <button class='btn btn-danger deletebtn' id='$row->id'><i class='fas fa-trash'></i></button>";
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        
         return view('users.index');
     }
 
     public function create()
     {
        $comapnyid=auth()->user()->company_id;
        
        if(auth()->user()->user_role =='super admin'){

            $companies= Company::select('company_name','id')->get();

        }else{
            $companies= Company::select('company_name','id')->where('id',$comapnyid)->get();
            
        }

        
        return view('users.create')->with('companies',$companies);
     }
 
     public function store(Request $request)
     {
         $validated = $request->validate([
             'first_name' => 'required|string|max:255',
             'last_name' => 'required|string|max:255',
             'email' => 'required|email|unique:users',
             'user_role'=>'required',
             'company'=>'required',
             'password' => 'required|string|min:6',
         ]);
 

         User::create([
            'name'=> $request->first_name.' '.$request->last_name,
            'email'=>$request->email,
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'company_id'=>$request->company,
            'user_role'=>$request->user_role,
            'password'=> Hash::make($request->pasword)
         ]);
 
         return redirect()->route('users.index')->with('success', 'User created successfully.');
     }
 
     public function edit(User $user)
     {
        $comapnyid=auth()->user()->company_id;
        
        if(auth()->user()->user_role =='super admin'){

            $companies= Company::select('company_name','id')->get();

        }else{
            $companies= Company::select('company_name','id')->where('id',$comapnyid)->get();
            
        }

        return view('users.edit')->with('user',$user)->with('companies',$companies);
     }
 
     public function update(Request $request, User $user)
     {
         $validated = $request->validate([
            'first_name' => 'required|string|max:255',
             'last_name' => 'required|string|max:255',
             'email' => 'required|email',
             'user_role'=>'required',
             'company'=>'required',
         ]);
 
         if ($request->filled('password')) {
             $validated['password'] = Hash::make($validated['password']);
         } else {
             unset($validated['password']);
         }

         $validated['company_id'] = $request->company;
         unset($validated['company']);
 
         $user->update($validated);
 
         return redirect()->route('users.index')->with('success', 'User updated successfully.');
     }
 
     public function destroy(Request $request)
     {
        $userid = $request->userid;
        User::find($userid)->delete();
         return redirect()->route('users.index')->with('success', 'User deleted successfully.');
     }

}
