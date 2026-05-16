<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResearchSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'title',
        'abstract',
        'authors',
        'publication_year',
        'source_name',
        'source_url',
        'keywords',
    ];

    protected $casts = [
        'authors' => 'array',
        'keywords' => 'array',
    ];

    public function similarityResults(): HasMany
    {
        return $this->hasMany(SimilarityResult::class, 'research_source_id');
    }
}
