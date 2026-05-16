<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FinalProject;
use App\Models\SimilarityResult;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard($user);
        }

        return $this->mahasiswaDashboard($user);
    }

    private function mahasiswaDashboard($user)
    {
        $projects = FinalProject::where('user_id', $user->id)
            ->with(['category', 'keywords'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $totalProjects     = FinalProject::where('user_id', $user->id)->count();
        $analyzedProjects  = FinalProject::where('user_id', $user->id)->where('status', 'analyzed')->count();
        $avgNovelty        = round(FinalProject::where('user_id', $user->id)->avg('novelty_score') ?? 0, 1);
        $avgSimilarity     = round(FinalProject::where('user_id', $user->id)->avg('similarity_score') ?? 0, 1);

        $unreadNotifications = UserNotification::where('user_id', $user->id)
            ->where('is_read', false)->count();

        $recentNotifications = UserNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Category distribution for chart
        $categoryStats = Category::withCount(['finalProjects' => function ($q) use ($user) {
            $q->where('user_id', $user->id);
        }])->get()->filter(fn($c) => $c->final_projects_count > 0);

        // Monthly project trend for chart (last 6 months)
        $monthlyTrend = FinalProject::where('user_id', $user->id)
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Similarity results
        $highSimilarityAlerts = SimilarityResult::whereHas('project', fn($q) => $q->where('user_id', $user->id))
            ->where('similarity_percentage', '>=', 70)
            ->with(['project', 'comparedProject'])
            ->orderBy('similarity_percentage', 'desc')
            ->limit(3)
            ->get();

        return view('dashboard.index', compact(
            'user',
            'projects',
            'totalProjects',
            'analyzedProjects',
            'avgNovelty',
            'avgSimilarity',
            'unreadNotifications',
            'recentNotifications',
            'categoryStats',
            'monthlyTrend',
            'highSimilarityAlerts'
        ));
    }

    private function adminDashboard($user)
    {
        $totalUsers     = User::where('role', 'mahasiswa')->count();
        $totalProjects  = FinalProject::count();
        $totalAnalysis  = SimilarityResult::count();
        $totalCategories = Category::count();

        $avgNovelty     = round(FinalProject::avg('novelty_score') ?? 0, 1);
        $avgSimilarity  = round(FinalProject::avg('similarity_score') ?? 0, 1);

        $recentProjects = FinalProject::with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $topCategories = Category::withCount('finalProjects')
            ->orderBy('final_projects_count', 'desc')
            ->limit(8)
            ->get();

        $highSimilarityAlerts = SimilarityResult::with(['project', 'comparedProject'])
            ->where('similarity_percentage', '>=', 70)
            ->orderBy('similarity_percentage', 'desc')
            ->limit(5)
            ->get();

        $monthlyTrend = FinalProject::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $recentUsers = User::where('role', 'mahasiswa')
            ->withCount('finalProjects')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $unreadNotifications = 0;
        $recentNotifications = collect();

        return view('dashboard.admin', compact(
            'user',
            'totalUsers',
            'totalProjects',
            'totalAnalysis',
            'totalCategories',
            'avgNovelty',
            'avgSimilarity',
            'recentProjects',
            'topCategories',
            'highSimilarityAlerts',
            'monthlyTrend',
            'recentUsers',
            'unreadNotifications',
            'recentNotifications'
        ));
    }
}
