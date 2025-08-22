<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {


        $request->validate([
            'companyName' => ['required'],
            'company_email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.Company::class],
            'companyAddress' => ['required'],
            'country' => ['required'],
            'companyDescription' => ['required'],
            'businessType' => ['required'],

            // 👇 Conditional rules
            'service_knowledge' => ['required_if:businessType,service'],
            'product_knowledge' => ['required_if:businessType,product'],

            'priceGuidelines' => ['required'],
            
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        
        if( $request->businessType =='service'){

            $company = Company::create([
                'company_name'   => $request->companyName,
                'business_type'  => $request->businessType,
                'company_email'  => $request->company_email,
                'company_address'=> $request->companyAddress,
                'country'        => $request->country,
                'company_description'=>$request->companyDescription,
                'price_guidelines'=>$request->priceGuidelines,
                'bussiness_knowledge'=>$request->service_knowledge,
            ]);

        }else{
            $company = Company::create([
                'company_name'   => $request->companyName,
                'business_type'  => $request->businessType,
                'company_email'  => $request->company_email,
                'company_address'=> $request->companyAddress,
                'country'        => $request->country,
                'company_description'=>$request->companyDescription,
                'price_guidelines'=>$request->priceGuidelines,
                'bussiness_knowledge'=>$request->product_knowledge,
            ]);
        }

      
        
        $companyId = $company->id;

        $user = User::create([
            'name' => $request->firstname.' '.$request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name'=>$request->firstname,
            'last_name'=>$request->lastname,
            'user_role'=>'admin',
            'company_id'=>$companyId,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return response()->json(['Success'=>'User Registered Successfully.']);
    }
}
