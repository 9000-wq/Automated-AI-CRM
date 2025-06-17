<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use App\Models\Plan;

class CompanyPlan extends Model
{
    
    protected $fillable = [
        'company_id',
        'plan_id',
        'start_date',
        'end_date',
        'custom_price',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

}
