<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'direction',
        'transcript',
        'sentiment',
        'outcome',
        'audio_link',
    ];

    // Relationship to contact
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}