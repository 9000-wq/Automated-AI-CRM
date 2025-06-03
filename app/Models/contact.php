<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id', 'full_name', 'role', 'phone', 'email', 'address'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
