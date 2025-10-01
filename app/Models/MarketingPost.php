<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image',
        'caption',
        'hashtags',
        'fb_username',
        'fb_password',
        'schedule_time',
        'status',
        'Link'
    ];

    protected $casts = [
        'schedule_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
