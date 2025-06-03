<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Note extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'lead_id', 'user_id', 'notes'
    ];
}
