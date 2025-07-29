<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Performance\Controllers\PerformanceController;

// Performance Module Routes
Route::middleware('auth')->prefix('modules/performance')->name('modules.performance.')->group(function () {
    Route::get('/', [PerformanceController::class, 'index'])->name('index');
    Route::get('/dashboard', [PerformanceController::class, 'dashboard'])->name('dashboard');
    Route::get('/export', [PerformanceController::class, 'export'])->name('export');

    // Tasks
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [PerformanceController::class, 'tasks'])->name('index');
        Route::get('/create', [PerformanceController::class, 'createTask'])->name('create');
        Route::post('/', [PerformanceController::class, 'storeTask'])->name('store');
        Route::get('/{task}', [PerformanceController::class, 'showTask'])->name('show');
        Route::get('/{task}/edit', [PerformanceController::class, 'editTask'])->name('edit');
        Route::put('/{task}', [PerformanceController::class, 'updateTask'])->name('update');
        Route::delete('/{task}', [PerformanceController::class, 'destroyTask'])->name('destroy');

        // Task Actions
        Route::post('/{task}/assign', [PerformanceController::class, 'assignTask'])->name('assign');
        Route::patch('/{task}/status', [PerformanceController::class, 'updateTaskStatus'])->name('update-status');
        Route::post('/{task}/comment', [PerformanceController::class, 'addTaskComment'])->name('add-comment');
        Route::post('/{task}/attachment', [PerformanceController::class, 'addTaskAttachment'])->name('add-attachment');

        // Bulk operations
        Route::post('/bulk-assign', [PerformanceController::class, 'bulkAssignTasks'])->name('bulk-assign');
        Route::patch('/bulk-update', [PerformanceController::class, 'bulkUpdateTasks'])->name('bulk-update');
        Route::delete('/bulk-delete', [PerformanceController::class, 'bulkDeleteTasks'])->name('bulk-delete');
    });

    // Task Assignments
    Route::prefix('assignments')->name('assignments.')->group(function () {
        Route::get('/', [PerformanceController::class, 'assignments'])->name('index');
        Route::put('/{assignment}', [PerformanceController::class, 'updateAssignment'])->name('update');
        Route::delete('/{assignment}', [PerformanceController::class, 'removeAssignment'])->name('remove');
        Route::post('/{assignment}/complete', [PerformanceController::class, 'completeAssignment'])->name('complete');
    });

    // Performance Metrics
    Route::prefix('metrics')->name('metrics.')->group(function () {
        Route::get('/', [PerformanceController::class, 'metrics'])->name('index');
        Route::post('/', [PerformanceController::class, 'storeMetric'])->name('store');
        Route::get('/{metric}', [PerformanceController::class, 'showMetric'])->name('show');
        Route::put('/{metric}', [PerformanceController::class, 'updateMetric'])->name('update');
        Route::delete('/{metric}', [PerformanceController::class, 'destroyMetric'])->name('destroy');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [PerformanceController::class, 'reports'])->name('index');
        Route::get('/summary', [PerformanceController::class, 'summaryReport'])->name('summary');
        Route::get('/detailed', [PerformanceController::class, 'detailedReport'])->name('detailed');
        Route::get('/productivity', [PerformanceController::class, 'productivityReport'])->name('productivity');
        Route::get('/efficiency', [PerformanceController::class, 'efficiencyReport'])->name('efficiency');
        Route::post('/export', [PerformanceController::class, 'export'])->name('export');
    });

    // Analytics
    Route::get('/analytics', [PerformanceController::class, 'analytics'])->name('analytics');
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/charts', [PerformanceController::class, 'getChartData'])->name('charts');
        Route::get('/kpis', [PerformanceController::class, 'getKPIs'])->name('kpis');
        Route::get('/trends', [PerformanceController::class, 'getTrends'])->name('trends');
    });

    // Team Performance
    Route::prefix('team')->name('team.')->group(function () {
        Route::get('/', [PerformanceController::class, 'teamOverview'])->name('overview');
        Route::get('/{user}/performance', [PerformanceController::class, 'userPerformance'])->name('user-performance');
        Route::post('/{user}/goals', [PerformanceController::class, 'setUserGoals'])->name('set-goals');
    });
});
