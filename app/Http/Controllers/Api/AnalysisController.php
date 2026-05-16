<?php

namespace App\Http\Controllers\Api;

use App\Models\FinalProject;
use App\Models\UserNotification;
use App\Services\CosineSimilarityService;
use App\Models\Category;
use App\Models\Recommendation;
use App\Models\SimilarityResult;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AnalysisController extends Controller
{
    private CosineSimilarityService $similarityService;

    public function __construct(CosineSimilarityService $similarityService)
    {
        $this->similarityService = $similarityService;
    }

    /**
     * Analyze similarity for a project
     */
    public function analyzeSimilarity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:final_projects,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $project = FinalProject::with(['keywords', 'user', 'category'])->find($request->project_id);

            if ($request->user() && $request->user()->role === 'mahasiswa' &&
                $project->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $result = $this->similarityService->compareProjectWithAll($project);

            // Notify user
            if ($request->user()) {
                UserNotification::create([
                    'user_id' => $project->user_id,
                    'title'   => 'Analisis Similarity Selesai',
                    'message' => "Analisis similarity untuk proyek \"{$project->title}\" telah selesai. Skor rata-rata: {$result['average_similarity']}%",
                    'type'    => $result['max_similarity'] >= 70 ? 'warning' : 'success',
                    'is_read' => false,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Similarity analysis completed',
                'data'    => $result,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Similarity analysis failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Analyze novelty for a project
     */
    public function analyzeNovelty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:final_projects,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $project = FinalProject::with(['keywords'])->find($request->project_id);

            if ($request->user() && $request->user()->role === 'mahasiswa' &&
                $project->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $noveltyScore    = $this->similarityService->calculateNoveltyScore($project);
            $recommendations = $this->similarityService->getNoveltyRecommendations($project);

            // Persist recommendations
            Recommendation::where('final_project_id', $project->id)->delete();
            foreach ($recommendations as $rec) {
                foreach ((array) ($rec['suggestions'] ?? []) as $suggestion) {
                    Recommendation::create([
                        'final_project_id'    => $project->id,
                        'recommendation_text' => $suggestion,
                        'recommendation_type' => $rec['type'],
                    ]);
                }
            }

            // Notify user
            if ($request->user()) {
                UserNotification::create([
                    'user_id' => $project->user_id,
                    'title'   => 'Novelty Score Dihitung',
                    'message' => "Novelty score untuk proyek \"{$project->title}\" adalah {$noveltyScore}%. " .
                                 ($noveltyScore >= 70 ? 'Topik Anda sangat unik!' : 'Lihat rekomendasi untuk meningkatkan keunikan.'),
                    'type'    => $noveltyScore >= 70 ? 'success' : ($noveltyScore >= 40 ? 'info' : 'warning'),
                    'is_read' => false,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Novelty analysis completed',
                'data'    => [
                    'novelty_score'   => $noveltyScore,
                    'recommendations' => $recommendations,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Novelty analysis failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get trending topics, keywords, and categories
     */
    public function getTrending(Request $request)
    {
        try {
            $limit = min((int) $request->get('limit', 10), 50);

            $trendingKeywords = DB::table('keywords')
                ->select('keyword', DB::raw('COUNT(*) as count'))
                ->groupBy('keyword')
                ->orderByDesc('count')
                ->limit($limit)
                ->get()
                ->map(fn($k) => ['keyword' => $k->keyword, 'count' => (int) $k->count]);

            $trendingCategories = Category::withCount('finalProjects')
                ->orderBy('final_projects_count', 'desc')
                ->limit($limit)
                ->get()
                ->map(fn($c) => [
                    'category_name' => $c->category_name,
                    'project_count' => $c->final_projects_count,
                ]);

            $highNoveltyProjects = FinalProject::with(['user', 'category'])
                ->where('status', 'analyzed')
                ->orderBy('novelty_score', 'desc')
                ->limit($limit)
                ->get()
                ->map(fn($p) => [
                    'id'            => $p->id,
                    'title'         => $p->title,
                    'novelty_score' => $p->novelty_score,
                    'user_name'     => $p->user?->name,
                    'category_name' => $p->category?->category_name,
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Trending data retrieved',
                'data'    => [
                    'trending_keywords'    => $trendingKeywords,
                    'trending_categories'  => $trendingCategories,
                    'high_novelty_projects' => $highNoveltyProjects,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get trending data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get platform statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $totalProjects   = FinalProject::count();
            $totalUsers      = \App\Models\User::where('role', 'mahasiswa')->count();
            $totalCategories = Category::count();
            $totalAnalysis   = SimilarityResult::count();

            $avgNovelty    = round(FinalProject::avg('novelty_score') ?? 0, 2);
            $avgSimilarity = round(FinalProject::avg('similarity_score') ?? 0, 2);

            $categoryStats = Category::withCount('finalProjects')
                ->get()
                ->map(fn($c) => [
                    'category_name' => $c->category_name,
                    'project_count' => $c->final_projects_count,
                    'avg_novelty'   => round(
                        FinalProject::where('category_id', $c->id)->avg('novelty_score') ?? 0, 2
                    ),
                ]);

            $monthlyTrend = FinalProject::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as count')
            )
                ->where('created_at', '>=', now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved',
                'data'    => [
                    'total_projects'          => $totalProjects,
                    'total_users'             => $totalUsers,
                    'total_categories'        => $totalCategories,
                    'total_analysis'          => $totalAnalysis,
                    'average_novelty_score'   => $avgNovelty,
                    'average_similarity_score' => $avgSimilarity,
                    'category_statistics'     => $categoryStats,
                    'monthly_trend'           => $monthlyTrend,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics: ' . $e->getMessage(),
            ], 500);
        }
    }
}
