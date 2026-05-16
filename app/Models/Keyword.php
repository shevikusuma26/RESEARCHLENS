<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'final_project_id',
        'keyword',
    ];

    /**
     * Get the final project that owns the keyword.
     */
    public function finalProject(): BelongsTo
    {
        return $this->belongsTo(FinalProject::class);
    }
}
