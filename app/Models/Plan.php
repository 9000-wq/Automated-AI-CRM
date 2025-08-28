<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\CompanyPlan;

class Plan extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'billing_cycle', 'features'
    ];

    public function companyPlans()
    {
        return $this->hasMany(CompanyPlan::class);
    }


}
