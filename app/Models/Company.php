<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CompanyPlan;

class Company extends Model
{
    
    protected $fillable = [
        'company_name',
        'business_type',
        'company_email',
        'company_address',
        'country',
        'company_description',
        'price_guidelines',
        'bussiness_knowledge',
    ];


    public function companyPlans()
    {
        return $this->hasMany(CompanyPlan::class);
    }


}
