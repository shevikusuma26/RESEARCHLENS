<?php

namespace App\Http\Controllers\Api;

use App\Models\FinalProject;
use App\Models\SimilarityResult;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Get admin dashboard statistics
     */
    public function getDashboardStats(Request $request)
    {
        try {
            $totalUsers = User::where('role', 'mahasiswa')->count();
            $totalProjects = FinalProject::count();
            $totalCategories = Category::count();
            $totalSimilarityAnalysis = SimilarityResult::count();

            $avgNoveltyScore = round(FinalProject::avg('novelty_score'), 2);
            $avgSimilarityScore = round(FinalProject::avg('similarity_score'), 2);

            // Recent projects
            $recentProjects = FinalProject::with(['user', 'category'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Top categories
            $topCategories = Category::withCount('finalProjects')
                ->orderBy('final_projects_count', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Dashboard statistics retrieved successfully',
                'data' => [
                    'total_users' => $totalUsers,
                    'total_projects' => $totalProjects,
                    'total_categories' => $totalCategories,
                    'total_analysis' => $totalSimilarityAnalysis,
                    'average_novelty_score' => $avgNoveltyScore,
                    'average_similarity_score' => $avgSimilarityScore,
                    'recent_projects' => $recentProjects,
                    'top_categories' => $topCategories,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard statistics: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all users
     */
    public function getUsers(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $limit = $request->get('limit', 15);

            $query = User::where('role', 'mahasiswa');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                });
            }

            $users = $query->withCount('finalProjects')
                ->paginate($limit);

            return response()->json([
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data' => $users,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all projects (admin view)
     */
    public function getAllProjects(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $categoryId = $request->get('category_id', null);
            $limit = $request->get('limit', 15);

            $query = FinalProject::with(['user', 'category']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                        ->orWhere('abstract', 'like', "%$search%");
                });
            }

            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            $projects = $query->orderBy('created_at', 'desc')->paginate($limit);

            return response()->json([
                'success' => true,
                'message' => 'Projects retrieved successfully',
                'data' => $projects,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve projects: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all categories
     */
    public function getCategories(Request $request)
    {
        try {
            $categories = Category::withCount('finalProjects')
                ->orderBy('final_projects_count', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data' => $categories,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create new category
     */
    public function createCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|unique:categories,category_name',
            'description' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $category = Category::create($request->only(['category_name', 'description']));

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get similarity reports
     */
    public function getSimilarityReports(Request $request)
    {
        try {
            $limit = $request->get('limit', 15);
            $minSimilarity = $request->get('min_similarity', 0);

            $reports = SimilarityResult::with(['project', 'comparedProject'])
                ->where('similarity_percentage', '>=', $minSimilarity)
                ->orderBy('similarity_percentage', 'desc')
                ->paginate($limit);

            return response()->json([
                'success' => true,
                'message' => 'Similarity reports retrieved successfully',
                'data' => $reports,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve similarity reports: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get high similarity alerts
     */
    public function getHighSimilarityAlerts(Request $request)
    {
        try {
            $threshold = $request->get('threshold', 70);

            $alerts = SimilarityResult::with(['project', 'comparedProject'])
                ->where('similarity_percentage', '>=', $threshold)
                ->orderBy('similarity_percentage', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'High similarity alerts retrieved successfully',
                'data' => [
                    'threshold' => $threshold,
                    'alerts_count' => count($alerts),
                    'alerts' => $alerts,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve high similarity alerts: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete project (admin only)
     */
    public function deleteProject(Request $request, $projectId)
    {
        try {
            $project = FinalProject::find($projectId);

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found',
                ], 404);
            }

            $project->delete();

            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete project: ' . $e->getMessage(),
            ], 500);
        }
    }
}
