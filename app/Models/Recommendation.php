<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'final_project_id',
        'recommendation_text',
        'recommendation_type',
    ];

    /**
     * Get the final project that owns the recommendation.
     */
    public function finalProject(): BelongsTo
    {
        return $this->belongsTo(FinalProject::class);
    }
}
