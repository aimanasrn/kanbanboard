<?php

use App\Http\Controllers\GithubWebhookController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\ProjectBoard;
use App\Livewire\ProjectManager;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Auth Routes (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

// Protected Workspace Routes (Auth Required)
Route::middleware('auth')->group(function () {
    // Project dashboard — lists all projects
    Route::get('/', ProjectManager::class)->name('home');
    Route::get('/projects', ProjectManager::class)->name('projects.index');

    // Individual project board
    Route::get('/project/{projectId}', ProjectBoard::class)->name('project.show');
});

// GitHub & GitLab Webhook Integration Endpoint
Route::post('/api/webhooks/github', [GithubWebhookController::class, 'handle'])->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Printable Executive Project Report / PDF View
Route::get('/project/{projectId}/report', function ($projectId) {
    $project = Project::with(['tasks.assignee', 'tasks.labels', 'tasks.checklists'])->findOrFail($projectId);
    $taskService = app(\App\Services\TaskService::class);
    $stats = $taskService->calculateProjectStats($project);

    return view('reports.executive-report', [
        'project' => $project,
        'stats' => $stats,
    ]);
})->name('project.report')->middleware('auth');
