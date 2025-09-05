<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'birthday',
        'phone',
        'address',
        'description',
        'lead_id',
        'contact_role_id',
        'account_id',
        'company_id'
    ];

    // Relationship: Contact belongs to a Role
    public function ContactRole()
    {
        return $this->belongsTo(ContactRole::class, 'contact_role_id');
    }

    public function Role()
    {
        return $this->belongsTo(ContactRole::class, 'contact_role_id');
    }


    public function leads()
    {
        return $this->belongsToMany(Lead::class, 'lead_contacts', 'contact_id', 'lead_id');
    }
    // Relationship: Contact belongs to a Lead
    public function lead()
    {
        return $this->belongsTo(lead::class);
    }
}
