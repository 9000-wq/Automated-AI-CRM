<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentEmail extends Model
{
    protected $table = 'sent_emails'; // just to be explicit

    protected $fillable = [
        'user_id',
        'to',
        'cc',
        'subject',
        'body',
    ];
}
