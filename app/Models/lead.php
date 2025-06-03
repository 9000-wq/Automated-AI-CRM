<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_ref', 'name', 'source', 'status', 'assigned_to','company_id'
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
