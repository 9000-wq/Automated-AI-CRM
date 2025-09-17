<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider',       // gmail, outlook, imap
        'email',

        // OAuth tokens
        'access_token',
        'refresh_token',
        'expires_in',

        // IMAP/SMTP fields
        'imap_host',
        'imap_port',
        'imap_encryption',
        'imap_username',
        'imap_password',
    ];

    // Each account belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
