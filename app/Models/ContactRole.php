<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
    ];

    // Relationship: A role has many contacts
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
