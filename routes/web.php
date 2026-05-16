<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\ProjectWebController;
use App\Http\Controllers\Web\AnalysisWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Guest-only routes (redirect if already authenticated)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', function (\Illuminate\Http\Request $request) {
        $request->validate(['email' => 'required|email']);
        $status = \Illuminate\Support\Facades\Password::sendResetLink($request->only('email'));
        return $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? back()->with('status', 'Link reset password telah dikirim ke email Anda.')
            : back()->withErrors(['email' => __($status)]);
    })->name('password.email');

    Route::get('/reset-password/{token}', function (string $token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');

    Route::post('/reset-password', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        $status = \Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => \Illuminate\Support\Facades\Hash::make($password)])
                     ->setRememberToken(\Illuminate\Support\Str::random(60));
                $user->save();
            }
        );
        return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password berhasil direset. Silahkan login.')
            : back()->withErrors(['email' => __($status)]);
    })->name('password.update');
});

// Logout (POST, accessible when authenticated)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// API Documentation (public)
Route::get('/api-docs', function () {
    return view('api-docs');
})->name('api.docs');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Mahasiswa Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Dashboard (role check inside DashboardController)
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.photo');

    // Dashboard sub-pages
    Route::get('/dashboard/projects', function () {
        $user     = auth()->user();
        $projects = \App\Models\FinalProject::where('user_id', $user->id)
            ->with(['category', 'keywords'])->orderBy('created_at', 'desc')->get();
        return view('dashboard.projects', compact('user', 'projects'));
    })->name('dashboard.projects');

    // Web project CRUD (session-authenticated, no Bearer token needed)
    Route::post('/dashboard/projects', [ProjectWebController::class, 'store'])->name('projects.store');
    Route::delete('/dashboard/projects/{id}', [ProjectWebController::class, 'destroy'])->name('projects.destroy');

    // Web analysis routes (session-authenticated, return JSON)
    Route::post('/dashboard/analyze/similarity', [AnalysisWebController::class, 'similarity'])->name('analyze.similarity');
    Route::post('/dashboard/analyze/novelty',    [AnalysisWebController::class, 'novelty'])->name('analyze.novelty');

    Route::get('/dashboard/similarity', function () {
        $user = auth()->user();
        $projects = \App\Models\FinalProject::where('user_id', $user->id)->with('keywords')->get();
        return view('dashboard.similarity', compact('user', 'projects'));
    })->name('dashboard.similarity');

    Route::get('/dashboard/novelty', function () {
        $user = auth()->user();
        $projects = \App\Models\FinalProject::where('user_id', $user->id)->with(['recommendations', 'keywords'])->get();
        return view('dashboard.novelty', compact('user', 'projects'));
    })->name('dashboard.novelty');
});
