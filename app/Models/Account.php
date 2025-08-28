<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'industry', 'email', 'phone', 'website',
        'address', 'city', 'country', 'status','company_id'
    ];

    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
