<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\lead;
use App\Models\contact;

class LeadContact extends Model
{
  

    protected $fillable = [
        'lead_id',
        'account_id',
        'contact_id',
    ];


    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }


}
