<?php

namespace App\Http\Controllers\Api;

use App\Models\FinalProject;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RecommendationController extends Controller
{
    /**
     * Get recommendations for a project
     */
    public function getProjectRecommendations(Request $request, $projectId)
    {
        try {
            $project = FinalProject::with('recommendations')->find($projectId);

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found',
                ], 404);
            }

            // Check authorization
            if ($request->user()->role === 'mahasiswa' && $project->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $recommendations = $project->recommendations()
                ->orderBy('recommendation_type')
                ->get()
                ->groupBy('recommendation_type');

            return response()->json([
                'success' => true,
                'message' => 'Recommendations retrieved successfully',
                'data' => [
                    'project_id' => $project->id,
                    'project_title' => $project->title,
                    'novelty_score' => $project->novelty_score,
                    'recommendations' => $recommendations,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve recommendations: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all recommendations for user
     */
    public function getAllRecommendations(Request $request)
    {
        try {
            $projects = FinalProject::where('user_id', $request->user()->id)
                ->with('recommendations')
                ->get();

            $allRecommendations = [];

            foreach ($projects as $project) {
                $recommendations = $project->recommendations()
                    ->get()
                    ->groupBy('recommendation_type');

                $allRecommendations[] = [
                    'project_id' => $project->id,
                    'project_title' => $project->title,
                    'novelty_score' => $project->novelty_score,
                    'recommendations' => $recommendations,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'All recommendations retrieved successfully',
                'data' => $allRecommendations,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve recommendations: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get recommendations by type
     */
    public function getByType(Request $request, $projectId, $type)
    {
        try {
            $project = FinalProject::find($projectId);

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found',
                ], 404);
            }

            // Check authorization
            if ($request->user()->role === 'mahasiswa' && $project->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $recommendations = Recommendation::where('final_project_id', $projectId)
                ->where('recommendation_type', $type)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Recommendations retrieved successfully',
                'data' => [
                    'project_id' => $projectId,
                    'type' => $type,
                    'recommendations' => $recommendations,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve recommendations: ' . $e->getMessage(),
            ], 500);
        }
    }
}
