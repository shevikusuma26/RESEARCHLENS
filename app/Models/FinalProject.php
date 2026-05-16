<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinalProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'abstract',
        'research_method',
        'proposal_file',
        'novelty_score',
        'similarity_score',
        'status',
    ];

    protected $casts = [
        'novelty_score'    => 'float',
        'similarity_score' => 'float',
    ];

    /**
     * Get the user that owns the final project.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the final project.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the keywords for the final project.
     */
    public function keywords(): HasMany
    {
        return $this->hasMany(Keyword::class);
    }

    /**
     * Get the recommendations for the final project.
     */
    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }

    /**
     * Get the similarity results for this project.
     */
    public function similarityResults(): HasMany
    {
        return $this->hasMany(SimilarityResult::class, 'project_id');
    }

    /**
     * Scope: only analyzed projects.
     */
    public function scopeAnalyzed($query)
    {
        return $query->where('status', 'analyzed');
    }

    /**
     * Scope: projects by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: submitted or analyzed projects.
     */
    public function scopeSubmitted($query)
    {
        return $query->whereIn('status', ['submitted', 'analyzed']);
    }

    /**
     * Get novelty label.
     */
    public function getNoveltyLabelAttribute(): string
    {
        if ($this->novelty_score >= 70) return 'High';
        if ($this->novelty_score >= 40) return 'Medium';
        return 'Low';
    }

    /**
     * Get proposal file URL.
     */
    public function getProposalFileUrlAttribute(): ?string
    {
        return $this->proposal_file ? asset('storage/' . $this->proposal_file) : null;
    }
}
