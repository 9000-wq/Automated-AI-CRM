<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadContact extends Model
{
  

    protected $fillable = [
        'lead_id',
        'account_id',
        'contact_id',
    ];
}
