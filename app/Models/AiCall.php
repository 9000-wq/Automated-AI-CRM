<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiCall extends Model
{
    use HasFactory;

    protected $table = 'ai_calls'; 

    protected $fillable = [
        'contact_id',
        'lead_id',
        'direction',
        'transcript',
        'sentiment',
        'outcome',
        'audio_link',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}