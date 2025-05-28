<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    
    protected $fillable = [
        'company_name',
        'business_type',
        'company_email',
        'company_address',
        'country',
    ];

    

}
