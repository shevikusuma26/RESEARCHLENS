<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\AnalysisController;
use App\Http\Controllers\Api\RecommendationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ApiKeyController;
use App\Http\Controllers\Api\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Routes (no authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Protected Routes (Sanctum/JWT authentication required)
Route::middleware('auth:sanctum')->group(function () {
    // Authentication Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    // Profile Routes
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile/update', [AuthController::class, 'updateProfile']);
    Route::put('/profile/change-password', [AuthController::class, 'changePassword']);

    // Project Routes (Mahasiswa)
    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index']);
        Route::post('/', [ProjectController::class, 'store']);
        Route::get('/{id}', [ProjectController::class, 'show']);
        Route::put('/{id}', [ProjectController::class, 'update']);
        Route::delete('/{id}', [ProjectController::class, 'destroy']);
    });

    // Analysis Routes
    Route::prefix('analyze')->group(function () {
        Route::post('/similarity', [AnalysisController::class, 'analyzeSimilarity']);
        Route::post('/novelty', [AnalysisController::class, 'analyzeNovelty']);
        Route::get('/trending', [AnalysisController::class, 'getTrending']);
        Route::get('/statistics', [AnalysisController::class, 'getStatistics']);
    });

    // Recommendation Routes
    Route::prefix('recommendation')->group(function () {
        Route::get('/novelty', [RecommendationController::class, 'getAllRecommendations']);
        Route::get('/{projectId}', [RecommendationController::class, 'getProjectRecommendations']);
        Route::get('/{projectId}/type/{type}', [RecommendationController::class, 'getByType']);
    });

    // Notification Routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'getNotifications']);
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount']);
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'delete']);
    });

    // API Key Routes
    Route::prefix('api-keys')->group(function () {
        Route::post('/generate', [ApiKeyController::class, 'generateKey']);
        Route::get('/', [ApiKeyController::class, 'getKeys']);
        Route::put('/{keyId}/deactivate', [ApiKeyController::class, 'deactivateKey']);
        Route::get('/usage', [ApiKeyController::class, 'getUsage']);
    });

    // Legacy alias routes
    Route::post('/generate-key', [ApiKeyController::class, 'generateKey']);
    Route::get('/api-usage', [ApiKeyController::class, 'getUsage']);

    // Top-level analytics aliases
    Route::get('/trending', [AnalysisController::class, 'getTrending']);
    Route::get('/statistics', [AnalysisController::class, 'getStatistics']);

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'getDashboardStats']);
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::get('/projects', [AdminController::class, 'getAllProjects']);
        Route::get('/categories', [AdminController::class, 'getCategories']);
        Route::post('/categories', [AdminController::class, 'createCategory']);
        Route::get('/similarity-reports', [AdminController::class, 'getSimilarityReports']);
        Route::get('/similarity-alerts', [AdminController::class, 'getHighSimilarityAlerts']);
        Route::delete('/projects/{projectId}', [AdminController::class, 'deleteProject']);
    });
});

// API Key Protected Routes (optional alternative authentication)
Route::middleware('api_key')->group(function () {
    // Same routes as above for API key authentication
    Route::get('/api-key/projects', [ProjectController::class, 'index']);
    Route::get('/api-key/projects/{id}', [ProjectController::class, 'show']);
    Route::post('/api-key/analyze/similarity', [AnalysisController::class, 'analyzeSimilarity']);
    Route::post('/api-key/analyze/novelty', [AnalysisController::class, 'analyzeNovelty']);
    Route::get('/api-key/trending', [AnalysisController::class, 'getTrending']);
    Route::get('/api-key/statistics', [AnalysisController::class, 'getStatistics']);
});

// Basic auth endpoint for admin validation
Route::middleware('auth.basic')->get('/admin/basic-status', function () {
    return response()->json([
        'success' => true,
        'message' => 'Admin Basic Auth verification successful',
    ], 200);
});

// Status endpoint
Route::get('/status', function () {
    return response()->json([
        'success' => true,
        'message' => 'ResearchLens API is running',
        'version' => '1.0.0',
    ], 200);
});

