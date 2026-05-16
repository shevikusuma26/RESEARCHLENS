<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimilarityResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'compared_project_id',
        'research_source_id',
        'similarity_percentage',
        'title_similarity',
        'abstract_similarity',
        'keyword_similarity',
        'method_similarity',
        'novelty_score',
        'analysis_type',
    ];

    protected $casts = [
        'similarity_percentage' => 'float',
        'title_similarity'      => 'float',
        'abstract_similarity'   => 'float',
        'keyword_similarity'    => 'float',
        'method_similarity'     => 'float',
        'novelty_score'         => 'float',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(FinalProject::class, 'project_id');
    }

    public function researchSource(): BelongsTo
    {
        return $this->belongsTo(ResearchSource::class, 'research_source_id');
    }

    public function comparedProject(): BelongsTo
    {
        return $this->belongsTo(FinalProject::class, 'compared_project_id');
    }
}
