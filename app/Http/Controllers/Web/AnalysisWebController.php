<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FinalProject;
use App\Models\ResearchSource;
use App\Models\SimilarityResult;
use App\Models\Recommendation;
use App\Models\UserNotification;
use App\Services\CosineSimilarityService;
use App\Services\AcademicApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AnalysisWebController extends Controller
{
    private CosineSimilarityService $similarityService;
    private AcademicApiService $apiService;

    public function __construct(CosineSimilarityService $similarityService, AcademicApiService $apiService)
    {
        $this->similarityService = $similarityService;
        $this->apiService = $apiService;
    }

    /**
     * Recursively sanitize data for UTF-8 to prevent JSON malformed errors
     */
    private function sanitizeData($data)
    {
        if (is_string($data)) {
            return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        } elseif (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitizeData($value);
            }
        }
        return $data;
    }

    /**
     * POST /dashboard/analyze/similarity
     */
    public function similarity(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:final_projects,id',
        ]);

        $project = FinalProject::with(['keywords', 'user', 'category'])
            ->findOrFail($request->project_id);

        if (Auth::user()->role === 'mahasiswa' && $project->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Search real research papers
            $papers = $this->apiService->searchPapers($project->title, 10);

            if (empty($papers)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Kami sedang mengalami kendala saat menghubungi repository akademik global. Silakan coba beberapa saat lagi.'
                ], 503);
            }

            $researchSources = [];
            foreach ($papers as $paper) {
                // Deduplicate
                $source = ResearchSource::updateOrCreate(
                    ['external_id' => $paper['external_id'] ?? $paper['title']],
                    [
                        'title'            => $paper['title'],
                        'abstract'         => $paper['abstract'] ?? 'Abstract not available.',
                        'authors'          => $paper['authors'],
                        'publication_year' => $paper['year'],
                        'source_name'      => $paper['venue'],
                        'source_url'       => $paper['url'],
                        'keywords'         => $paper['keywords'],
                    ]
                );
                $researchSources[] = $source;
            }

            // Run similarity
            $result = $this->similarityService->compareProjectWithResearchSources($project, $researchSources);

            // Sanitize result to prevent UTF-8 encoding errors
            $sanitizedResult = $this->sanitizeData($result);

            return response()->json(['success' => true, 'data' => $sanitizedResult]);

        } catch (\Exception $e) {
            Log::error('Similarity Analysis Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menjalankan analisis: ' . $e->getMessage()], 500);
        }
    }

    /**
     * POST /dashboard/analyze/novelty
     */
    public function novelty(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:final_projects,id',
        ]);

        $project = FinalProject::with(['keywords'])->findOrFail($request->project_id);

        if (Auth::user()->role === 'mahasiswa' && $project->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $hasResults = SimilarityResult::where('project_id', $project->id)
                ->whereNotNull('research_source_id')
                ->exists();

            if (!$hasResults) {
                // Return error asking to run similarity first
                return response()->json([
                    'success' => false, 
                    'message' => 'Harap jalankan Analisis Similarity terlebih dahulu untuk mendapatkan skor novelty yang akurat.'
                ], 400);
            }

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

            return response()->json([
                'success' => true,
                'data'    => [
                    'novelty_score'   => $project->novelty_score,
                    'recommendations' => $recommendations,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghitung novelty: ' . $e->getMessage()], 500);
        }
    }
}
