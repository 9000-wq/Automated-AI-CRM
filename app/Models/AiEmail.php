<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiEmail extends Model
{
    protected $fillable = [
        'contact_id',
        'sequence_id',
        'content',
        'subject',
        'status',
        'opened_at',
        'clicked_at',
        'replied_at',
        'created_at'
    ];

    public $timestamps = false;

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
