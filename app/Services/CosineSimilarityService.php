<?php

namespace App\Services;

use App\Models\FinalProject;
use App\Models\ResearchSource;
use App\Models\SimilarityResult;

class CosineSimilarityService
{
    /**
     * Extended Indonesian + English stopwords
     */
    private array $stopwords = [
        // English
        'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for',
        'of', 'with', 'by', 'from', 'is', 'are', 'was', 'were', 'be', 'been',
        'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would',
        'could', 'should', 'may', 'might', 'can', 'this', 'that', 'these',
        'those', 'i', 'you', 'he', 'she', 'it', 'we', 'they', 'what', 'which',
        'who', 'when', 'where', 'why', 'how', 'not', 'as', 'if', 'up', 'so',
        'no', 'its', 'our', 'your', 'their', 'my', 'me', 'him', 'her', 'us',
        // Indonesian
        'dan', 'atau', 'dengan', 'yang', 'di', 'ke', 'dari', 'oleh', 'ini',
        'itu', 'ada', 'tidak', 'sudah', 'untuk', 'pada', 'dalam', 'akan',
        'juga', 'dapat', 'bisa', 'serta', 'karena', 'namun', 'tetapi',
        'bahwa', 'agar', 'seperti', 'tersebut', 'yaitu', 'adalah', 'merupakan',
        'antara', 'hingga', 'maka', 'telah', 'sedang', 'maupun', 'apabila',
        'sebagai', 'sesuai', 'melalui', 'sebuah', 'setiap', 'sangat', 'lebih',
        'jika', 'saat', 'hal', 'cara', 'hasil', 'proses', 'terhadap', 'secara',
        'sehingga', 'pula', 'hanya', 'ketika', 'selain', 'sementara',
    ];

    /**
     * Simple Indonesian stemming prefixes/suffixes
     */
    private function stem(string $word): string
    {
        // Remove common Indonesian suffixes
        $suffixes = ['kan', 'an', 'i', 'nya'];
        foreach ($suffixes as $suffix) {
            if (strlen($word) > strlen($suffix) + 3 && str_ends_with($word, $suffix)) {
                $word = substr($word, 0, strlen($word) - strlen($suffix));
                break;
            }
        }

        // Remove common Indonesian prefixes
        $prefixes = ['me', 'ber', 'ter', 'pe', 'se', 'di', 'ke'];
        foreach ($prefixes as $prefix) {
            if (strlen($word) > strlen($prefix) + 3 && str_starts_with($word, $prefix)) {
                $word = substr($word, strlen($prefix));
                break;
            }
        }

        return $word;
    }

    /**
     * Preprocess text: lowercase → remove punctuation → tokenize → stopword removal → stem
     */
    public function preprocessText(string $text): array
    {
        // Lowercase
        $text = strtolower($text);

        // Remove URLs
        $text = preg_replace('~https?://\S+~', '', $text);

        // Remove special characters (keep only letters and spaces)
        $text = preg_replace('/[^a-z\s]/', ' ', $text);

        // Tokenize
        $tokens = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Remove stopwords, short words, then stem
        $tokens = array_filter($tokens, function ($token) {
            return !in_array($token, $this->stopwords) && strlen($token) > 2;
        });

        // Apply stemming
        $tokens = array_map(fn($t) => $this->stem($t), $tokens);

        // Remove any tokens that became too short after stemming
        $tokens = array_filter($tokens, fn($t) => strlen($t) > 2);

        return array_values($tokens);
    }

    /**
     * Extract top keywords from text
     */
    public function extractKeywords(string $text, int $limit = 10): array
    {
        $tokens = $this->preprocessText($text);
        $frequencies = array_count_values($tokens);
        arsort($frequencies);
        return array_slice(array_keys($frequencies), 0, $limit);
    }

    /**
     * Create TF vector
     */
    public function createVector(string $text, array $vocabulary): array
    {
        $tokens = $this->preprocessText($text);
        $vector = array_fill_keys($vocabulary, 0);

        foreach ($tokens as $token) {
            if (array_key_exists($token, $vector)) {
                $vector[$token]++;
            }
        }

        return array_values($vector);
    }

    /**
     * Dot product of two vectors
     */
    public function dotProduct(array $v1, array $v2): float
    {
        $sum = 0.0;
        $len = min(count($v1), count($v2));
        for ($i = 0; $i < $len; $i++) {
            $sum += $v1[$i] * $v2[$i];
        }
        return $sum;
    }

    /**
     * Magnitude of a vector
     */
    public function magnitude(array $vector): float
    {
        $sum = 0.0;
        foreach ($vector as $val) {
            $sum += $val * $val;
        }
        return sqrt($sum);
    }

    /**
     * Calculate cosine similarity between two texts
     */
    public function calculateSimilarity(string $text1, string $text2): float
    {
        if (empty(trim($text1)) || empty(trim($text2))) {
            return 0.0;
        }

        $tokens1 = $this->preprocessText($text1);
        $tokens2 = $this->preprocessText($text2);

        if (empty($tokens1) || empty($tokens2)) {
            return 0.0;
        }

        $vocabulary = array_unique(array_merge($tokens1, $tokens2));
        sort($vocabulary);

        $vector1 = $this->createVector($text1, $vocabulary);
        $vector2 = $this->createVector($text2, $vocabulary);

        $dot   = $this->dotProduct($vector1, $vector2);
        $mag1  = $this->magnitude($vector1);
        $mag2  = $this->magnitude($vector2);

        if ($mag1 == 0 || $mag2 == 0) {
            return 0.0;
        }

        return round($dot / ($mag1 * $mag2), 4);
    }

    /**
     * Get common keywords between two texts for highlighting
     */
    public function getCommonKeywords(string $text1, string $text2): array
    {
        $kw1 = $this->extractKeywords($text1, 20);
        $kw2 = $this->extractKeywords($text2, 20);
        return array_values(array_intersect($kw1, $kw2));
    }

    /**
     * Analyze similarity between project and research source
     * Weights: Title (40%), Abstract (40%), Keywords (20%)
     */
    public function analyzeProjectWithResearchSource(FinalProject $project, ResearchSource $source): array
    {
        $titleSimilarity = $this->calculateSimilarity($project->title, $source->title);

        $abstractSimilarity = $this->calculateSimilarity(
            $project->abstract ?? '',
            $source->abstract ?? ''
        );

        // Keywords for research source are stored as array (cast)
        $sourceKeywordsStr = is_array($source->keywords) ? implode(' ', $source->keywords) : ($source->keywords ?? '');
        $projectKeywordsStr = $project->keywords->pluck('keyword')->implode(' ');
        
        $keywordSimilarity = $this->calculateSimilarity($projectKeywordsStr, $sourceKeywordsStr);

        // Weighted overall (User request: Title 40%, Abstract 40%, Keyword 20%)
        $overallSimilarity = (
            ($titleSimilarity * 0.40) +
            ($abstractSimilarity * 0.40) +
            ($keywordSimilarity * 0.20)
        ) * 100;

        return [
            'title_similarity'    => round($titleSimilarity * 100, 2),
            'abstract_similarity' => round($abstractSimilarity * 100, 2),
            'keyword_similarity'  => round($keywordSimilarity * 100, 2),
            'overall_similarity'  => round($overallSimilarity, 2),
        ];
    }

    /**
     * Compare project against fetched research sources
     */
    public function compareProjectWithResearchSources(FinalProject $project, $sources): array
    {
        $project->load('keywords');

        $results         = [];
        $totalSimilarity = 0.0;
        $maxSimilarity   = 0.0;

        foreach ($sources as $source) {
            $similarity = $this->analyzeProjectWithResearchSource($project, $source);

            // Novelty score is 100 - similarity
            $noveltyScore = round(max(0, 100 - $similarity['overall_similarity']), 2);

            // Store/update in database
            SimilarityResult::updateOrCreate(
                [
                    'project_id'         => $project->id,
                    'research_source_id' => $source->id,
                ],
                [
                    'similarity_percentage' => $similarity['overall_similarity'],
                    'title_similarity'      => $similarity['title_similarity'],
                    'abstract_similarity'   => $similarity['abstract_similarity'],
                    'keyword_similarity'    => $similarity['keyword_similarity'],
                    'novelty_score'         => $noveltyScore,
                    'analysis_type'         => 'external_academic',
                ]
            );

            $results[] = [
                'research_source_id' => $source->id,
                'title'              => $source->title,
                'authors'            => $source->authors,
                'year'               => $source->publication_year,
                'source_name'        => $source->source_name,
                'source_url'         => $source->source_url,
                'abstract_preview'   => substr($source->abstract, 0, 200) . '...',
                'keywords'           => $source->keywords,
                'similarity'         => $similarity,
                'novelty_score'      => $noveltyScore,
                'common_keywords'    => $this->getCommonKeywords(
                    $project->title . ' ' . $project->abstract,
                    $source->title . ' ' . $source->abstract
                ),
            ];

            $totalSimilarity += $similarity['overall_similarity'];
            if ($similarity['overall_similarity'] > $maxSimilarity) {
                $maxSimilarity = $similarity['overall_similarity'];
            }
        }

        $count = count($sources);
        $avgSimilarity = $count > 0 ? round($totalSimilarity / $count, 2) : 0.0;
        $finalNovelty  = round(max(0, 100 - $maxSimilarity), 2);

        // Update project scores
        $project->update([
            'similarity_score' => round($maxSimilarity, 2), // We use Max Similarity as the main score for caution
            'novelty_score'    => $finalNovelty,
            'status'           => 'analyzed',
        ]);

        // Sort by similarity descending
        usort($results, fn($a, $b) => $b['similarity']['overall_similarity'] <=> $a['similarity']['overall_similarity']);

        return [
            'project_id'         => $project->id,
            'project_title'      => $project->title,
            'average_similarity' => $avgSimilarity,
            'max_similarity'     => round($maxSimilarity, 2),
            'novelty_score'      => $finalNovelty,
            'total_comparisons'  => $count,
            'ranked_results'     => $results,
        ];
    }

    /**
     * Get specific novelty recommendations based on the project's keywords and score
     */
    public function getNoveltyRecommendations(FinalProject $project): array
    {
        $noveltyScore = $project->novelty_score ?? 0;
        $recommendations = [];

        if ($noveltyScore < 30) {
            $recommendations[] = [
                'type'        => 'innovation',
                'badge'       => 'High Similarity Warning',
                'message'     => 'Topik memiliki kemiripan tinggi dengan penelitian sebelumnya. Disarankan menambahkan predictive analytics atau AI-based recommendation untuk meningkatkan novelty.',
                'suggestions' => [
                    'Tambahkan algoritma Deep Learning terbaru (Transformer/BERT) jika sebelumnya hanya Machine Learning klasik',
                    'Integrasikan teknik optimasi baru (Genetic Algorithms / PSO)',
                    'Fokus pada case study yang sangat spesifik atau data yang belum pernah diolah',
                    'Tambahkan modul Real-time Visualization atau Automated Alerts'
                ]
            ];
        } elseif ($noveltyScore < 60) {
            $recommendations[] = [
                'type'        => 'enhancement',
                'badge'       => 'Moderate Novelty',
                'message'     => 'Topik Anda memiliki keunikan sedang. Pertimbangkan untuk memperluas cakupan atau memperbarui teknologi.',
                'suggestions' => [
                    'Gunakan arsitektur Cloud-native atau Microservices untuk skalabilitas',
                    'Implementasikan fitur keamanan data tingkat lanjut (Encryption/Blockchain)',
                    'Bandingkan performa dengan 3-5 metode berbeda'
                ]
            ];
        } else {
            $recommendations[] = [
                'type'        => 'strength',
                'badge'       => 'Strong Novelty ✓',
                'message'     => 'Topik penelitian Anda sangat orisinal dan unik. Pertahankan fokus dan kualitas metodologi.',
                'suggestions' => [
                    'Publikasikan hasil penelitian ke jurnal internasional bereputasi',
                    'Lakukan validasi data yang lebih masif untuk memperkuat klaim',
                    'Siapkan dokumentasi yang sangat detail untuk kontribusi ilmiah'
                ]
            ];
        }

        // Add technology-specific suggestions based on keywords
        $keywords = $project->keywords->pluck('keyword')->toArray();
        $techSuggestions = $this->getTechnologySuggestions($keywords);
        if ($techSuggestions) {
            $recommendations[] = $techSuggestions;
        }

        return $recommendations;
    }

    private function getTechnologySuggestions(array $keywords): ?array
    {
        $techMap = [
            'iot' => ['MQTT', 'Edge Computing', 'Digital Twin', 'Low Power Wide Area Network'],
            'web' => ['Next.js', 'WebAssembly', 'Progressive Web Apps', 'Serverless'],
            'image' => ['YOLOv8', 'Vision Transformer', 'Generative Adversarial Networks'],
            'data' => ['Spark Streaming', 'Feature Engineering', 'XGBoost', 'AutoML'],
            'security' => ['Zero Trust Architecture', 'Homomorphic Encryption', 'Intrusion Detection System'],
        ];

        $suggested = [];
        foreach ($keywords as $kw) {
            $kw = strtolower($kw);
            foreach ($techMap as $key => $techs) {
                if (str_contains($kw, $key)) {
                    $suggested = array_merge($suggested, $techs);
                }
            }
        }

        if (empty($suggested)) return null;

        return [
            'type' => 'technology',
            'badge' => 'Tech Stack Suggestions',
            'message' => 'Integrasikan teknologi berikut untuk meningkatkan kualitas teknis:',
            'suggestions' => array_values(array_unique($suggested))
        ];
    }
}
