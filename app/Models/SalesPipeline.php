<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPipeline extends Model
{
    use HasFactory;

    protected $table = 'sales_pipeline';

    protected $fillable = [
        'lead_id',
        'stage',
        'probability',
        'ai_notes',
        'outcome',
        'closed_at'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}

