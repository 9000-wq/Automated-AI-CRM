<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadScore extends Model
{
    public $incrementing = false;
    protected $primaryKey = null;
    protected $table = 'lead_scores';
    protected $keyType = 'string';

    protected $fillable = [
        'lead_id',
        'contact_id',
        'profile_score',
        'engagement_score',
        'intent_score',
        'external_data_score',
        'total_score',
        'score_band',
        'next_best_action',
        'last_scored_at',
        'explanation',
    ];

    protected $casts = [
        'last_scored_at' => 'datetime',
        'explanation' => 'array',
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

