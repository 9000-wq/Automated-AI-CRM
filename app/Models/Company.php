<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\CompanyPlan;

class Company extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'company_name',
        'business_type',
        'company_email',
        'company_address',
        'country',
        'company_description',
        'price_guidelines',
        'bussiness_knowledge',
        'scrapper',
        'scrapper_date_time',
    ];


    public function companyPlans()
    {
        return $this->hasMany(CompanyPlan::class);
    }

     public function leads()
    {
        return $this->hasMany(CompanyPlan::class);
    }

    //   public function lead()
    // {
    //     return $this->hasMany(Company::class);
    // }
   


}
