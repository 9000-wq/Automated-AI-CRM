<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CompanyPlan;

class Plan extends Model
{
    
    protected $fillable = [
        'name', 'description', 'price', 'billing_cycle', 'features'
    ];

    public function companyPlans()
    {
        return $this->hasMany(CompanyPlan::class);
    }


}
