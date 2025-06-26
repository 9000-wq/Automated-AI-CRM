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
    'company_id',
    'transcript',
    'sentiment',
    'outcome',
    'audio_link'
];

    protected $casts = [
        'date_start' => 'datetime',
        'date_end' => 'datetime',
    ];
    

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
    
}