<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'direction',
        'date_start',
        'date_end',
        'duration',
        'parent_type',
        'parent_name',
        'description',
        'assigned_user_name',
        'teams',
        'users',
        'contacts',
        'leads',
        'lead_id',
        'company_id'
    ];

    protected $casts = [
        'date_start' => 'datetime',
        'date_end' => 'datetime',
    ];
    
    // Add these mutators to handle array fields
    public function setTeamsAttribute($value)
    {
        $this->attributes['teams'] = $value ? implode(',', (array)$value) : null;
    }
    
    public function setUsersAttribute($value)
    {
        $this->attributes['users'] = $value ? implode(',', (array)$value) : null;
    }
    
    public function setContactsAttribute($value)
    {
        $this->attributes['contacts'] = $value ? implode(',', (array)$value) : null;
    }
    
    public function setLeadsAttribute($value)
    {
        $this->attributes['leads'] = $value ? implode(',', (array)$value) : null;
    }
    
    public function setRemindersAttribute($value)
    {
        $this->attributes['reminders'] = $value ? implode(',', (array)$value) : null;
    }
}