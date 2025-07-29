<?php

namespace App\Modules\Performance\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Modules\Performance\Models\Task;
use App\Models\Modules\Performance\Models\TaskAssignment;
use App\Models\Modules\Performance\Models\PerformanceMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;

use Carbon\Carbon;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        $employee = $request->get('employee');

        $stats = $this->getOverallStats($period, $employee);
        $recentTasks = $this->getRecentTasks($employee);
        $topPerformers = $this->getTopPerformers($period);
        $chartData = $this->getChartData($period, $employee);
        $kpis = $this->getKPIs($period, $employee);
        $employees = $this->getEmployeeList();

        return view('modules.performance.index', compact(
            'stats', 'recentTasks', 'topPerformers', 'chartData', 'kpis', 'period', 'employee', 'employees'
        ));
    }

    public function dashboard(Request $request)
    {
        $userRole = $request->get('role', 'employee'); // In real app, get from auth
        $employeeName = $request->get('employee', 'Current User'); // In real app, get from auth

        if ($userRole === 'manager') {
            return $this->managerDashboard();
        } else {
            return $this->employeeDashboard($employeeName);
        }
    }

    private function managerDashboard($period = 'month')
    {
        $stats = $this->getOverallStats($period);
        $teamPerformance = $this->getTeamPerformanceData();
        $topPerformers = $this->getTopPerformers($period);
        $performanceTrends = $this->getPerformanceTrends();
        $chartData = $this->getChartData($period);
        $kpis = $this->getKPIs($period);

        return view('modules.performance.dashboard.manager', compact(
            'stats', 'teamPerformance', 'topPerformers', 'performanceTrends', 'chartData', 'kpis', 'period'
        ));
    }

    private function employeeDashboard($employeeName)
    {
        $myTasks = Task::whereHas('assignments', function ($query) use ($employeeName) {
            $query->where('employee_name', $employeeName);
        })->with('assignments')->get();

        $myStats = $this->getEmployeeStats($employeeName);
        $myPerformance = $this->getEmployeePerformanceData($employeeName);

        return view('modules.performance.dashboard.employee', compact(
            'myTasks', 'myStats', 'myPerformance', 'employeeName'
        ));
    }

    public function tasks(Request $request)
    {
        $query = Task::with(['assignments']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('employee')) {
            $query->byEmployee($request->employee);
        }

        if ($request->filled('project')) {
            $query->byProject($request->project);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%");
            });
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('modules.performance.tasks.index', compact('tasks'));
    }

    public function createTask()
    {
        return view('modules.performance.tasks.create');
    }

    public function storeTask(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|in:development,design,testing,documentation,meeting,research,other',
            'project_name' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after:start_date',
            'estimated_hours' => 'nullable|integer|min:1',
            'tags' => 'nullable|string',
            'notes' => 'nullable|string',
            'assigned_employees' => 'nullable|array',
            'assigned_employees.*' => 'string|max:255',
        ]);

        // Process tags
        if ($validated['tags']) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        $validated['created_by'] = 'System'; // In real app, use auth()->user()->name

        $task = Task::create($validated);

        // Create assignments if employees are specified
        if (!empty($validated['assigned_employees'])) {
            foreach ($validated['assigned_employees'] as $employeeName) {
                TaskAssignment::create([
                    'task_id' => $task->id,
                    'employee_name' => $employeeName,
                    'employee_email' => strtolower(str_replace(' ', '.', $employeeName)) . '@company.com',
                    'assigned_by' => $validated['created_by'],
                    'assigned_at' => now(),
                ]);
            }
        }

        return redirect()->route('modules.performance.tasks.show', $task)
            ->with('success', __('erp.task_created_successfully'));
    }

    public function showTask(Task $task)
    {
        $task->load(['assignments']);

        return view('modules.performance.tasks.show', compact('task'));
    }

    public function editTask(Task $task)
    {
        $task->load(['assignments']);

        return view('modules.performance.tasks.edit', compact('task'));
    }

    public function updateTask(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled,on_hold',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|in:development,design,testing,documentation,meeting,research,other',
            'project_name' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|integer|min:1',
            'actual_hours' => 'nullable|integer|min:0',
            'completion_percentage' => 'nullable|numeric|min:0|max:100',
            'tags' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Process tags
        if ($validated['tags']) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        // Set completed_at timestamp if status changed to completed
        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        return redirect()->route('modules.performance.tasks.show', $task)
            ->with('success', __('erp.task_updated_successfully'));
    }

    public function destroyTask(Task $task)
    {
        $task->delete();

        return redirect()->route('modules.performance.tasks.index')
            ->with('success', __('erp.task_deleted_successfully'));
    }

    public function assignTask(Request $request, Task $task)
    {
        $validated = $request->validate([
            'employee_name' => 'required|string|max:255',
            'employee_email' => 'required|email|max:255',
            'employee_role' => 'nullable|string|max:255',
            'assignment_notes' => 'nullable|string',
        ]);

        $validated['task_id'] = $task->id;
        $validated['assigned_by'] = 'System'; // In real app, use auth()->user()->name
        $validated['assigned_at'] = now();

        TaskAssignment::create($validated);

        return redirect()->route('modules.performance.tasks.show', $task)
            ->with('success', __('erp.task_assigned_successfully'));
    }

    public function updateAssignment(Request $request, TaskAssignment $assignment)
    {
        $validated = $request->validate([
            'assignment_status' => 'required|in:assigned,accepted,in_progress,completed,rejected',
            'assignment_notes' => 'nullable|string',
        ]);

        // Set timestamps based on status
        if ($validated['assignment_status'] === 'in_progress' && $assignment->assignment_status !== 'in_progress') {
            $validated['started_at'] = now();
        } elseif ($validated['assignment_status'] === 'completed' && $assignment->assignment_status !== 'completed') {
            $validated['completed_at'] = now();
        }

        $assignment->update($validated);

        return redirect()->route('modules.performance.tasks.show', $assignment->task)
            ->with('success', 'Assignment updated successfully');
    }



    public function analytics(Request $request)
    {
        $period = $request->get('period', 'month');
        $employee = $request->get('employee');

        $analyticsData = $this->getAnalyticsData($period, $employee);
        $employees = $this->getEmployeeList();

        return view('modules.performance.analytics', compact('analyticsData', 'period', 'employee', 'employees'));
    }

    /**
     * Generate performance reports.
     */
    public function reports(Request $request)
    {
        $period = $request->get('period', 'month');
        $employee = $request->get('employee');
        $reportType = $request->get('type', 'summary');

        $reportData = $this->generateReport($reportType, $period, $employee);
        $employees = $this->getEmployeeList();

        return view('modules.performance.reports', compact('reportData', 'period', 'employee', 'reportType', 'employees'));
    }

    /**
     * Export performance data.
     */
    public function export(Request $request)
    {
        $period = $request->get('period', 'month');
        $employee = $request->get('employee');
        $format = $request->get('format', 'csv');

        $data = $this->getExportData($period, $employee);

        if ($format === 'pdf') {
            return $this->exportToPDF($data, $period, $employee);
        } else {
            return $this->exportToCSV($data, $period, $employee);
        }
    }

    /**
     * Update task status via AJAX.
     */
    public function updateTaskStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled,on_hold',
            'completion_percentage' => 'nullable|numeric|min:0|max:100',
            'actual_hours' => 'nullable|numeric|min:0',
        ]);

        $task->update($validated);

        if ($validated['status'] === 'completed') {
            $task->update(['completed_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully',
            'task' => $task->fresh()
        ]);
    }

    /**
     * Bulk update tasks.
     */
    public function bulkUpdateTasks(Request $request)
    {
        $validated = $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:tasks,id',
            'action' => 'required|in:complete,cancel,assign,update_priority',
            'value' => 'nullable|string',
        ]);

        $tasks = Task::whereIn('id', $validated['task_ids']);

        switch ($validated['action']) {
            case 'complete':
                $tasks->update(['status' => 'completed', 'completed_at' => now()]);
                break;
            case 'cancel':
                $tasks->update(['status' => 'cancelled']);
                break;
            case 'update_priority':
                $tasks->update(['priority' => $validated['value']]);
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Tasks updated successfully',
            'updated_count' => count($validated['task_ids'])
        ]);
    }

    // Helper methods for statistics
    private function getOverallStats($period = 'month', $employee = null): array
    {
        $cacheKey = "performance_stats_{$period}_{$employee}";

        return Cache::remember($cacheKey, 600, function () use ($period, $employee) {
            $dateRange = $this->getDateRange($period);

            $tasksQuery = Task::whereBetween('created_at', $dateRange);
            $assignmentsQuery = TaskAssignment::whereBetween('assigned_at', $dateRange);

            if ($employee) {
                $tasksQuery->byEmployee($employee);
                $assignmentsQuery->byEmployee($employee);
            }

            $totalTasks = $tasksQuery->count();
            $completedTasks = $tasksQuery->completed()->count();
            $overdueTasks = $tasksQuery->overdue()->count();
            $activeTasks = $tasksQuery->active()->count();

            $totalAssignments = $assignmentsQuery->count();
            $completedAssignments = $assignmentsQuery->completed()->count();

            return [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'active_tasks' => $activeTasks,
                'overdue_tasks' => $overdueTasks,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
                'total_assignments' => $totalAssignments,
                'completed_assignments' => $completedAssignments,
                'assignment_completion_rate' => $totalAssignments > 0 ? round(($completedAssignments / $totalAssignments) * 100, 1) : 0,
                'total_employees' => TaskAssignment::distinct('employee_name')->count(),
                'avg_completion_time' => $this->getAverageCompletionTime($period, $employee),
                'productivity_trend' => $this->getProductivityTrend($period, $employee),
                'avg_efficiency' => $this->calculateAverageEfficiency($period, $employee),
            ];
        });
    }

    private function getEmployeeStats($employeeName): array
    {
        $tasks = Task::byEmployee($employeeName);

        return [
            'total_tasks' => $tasks->count(),
            'active_tasks' => $tasks->active()->count(),
            'completed_tasks' => $tasks->completed()->count(),
            'overdue_tasks' => $tasks->overdue()->count(),
            'completion_rate' => $this->calculateEmployeeCompletionRate($employeeName),
            'efficiency_rate' => $this->calculateEmployeeEfficiency($employeeName),
            'avg_task_duration' => $this->calculateAverageTaskDuration($employeeName),
        ];
    }

    private function getTeamPerformanceData(): array
    {
        $employees = TaskAssignment::select('employee_name')
            ->distinct()
            ->pluck('employee_name');

        $performanceData = [];

        foreach ($employees as $employee) {
            $stats = $this->getEmployeeStats($employee);
            $performanceData[] = [
                'name' => $employee,
                'stats' => $stats,
                'score' => $this->calculatePerformanceScore($stats),
            ];
        }

        return collect($performanceData)->sortByDesc('score')->values()->all();
    }



    private function getPerformanceTrends(): array
    {
        $last6Months = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthData = [
                'month' => $date->format('M Y'),
                'tasks_completed' => Task::completed()
                    ->whereMonth('completed_at', $date->month)
                    ->whereYear('completed_at', $date->year)
                    ->count(),
                'avg_efficiency' => $this->calculateMonthlyEfficiency($date),
            ];
            $last6Months->push($monthData);
        }

        return $last6Months->toArray();
    }

    private function getEmployeePerformanceData($employeeName): array
    {
        $last30Days = collect();

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayData = [
                'date' => $date->format('M j'),
                'tasks_completed' => Task::byEmployee($employeeName)
                    ->completed()
                    ->whereDate('completed_at', $date)
                    ->count(),
                'hours_worked' => $this->calculateDailyHours($employeeName, $date),
            ];
            $last30Days->push($dayData);
        }

        return $last30Days->toArray();
    }

    // Calculation methods
    private function calculateAverageCompletionRate(): float
    {
        $totalTasks = Task::count();
        $completedTasks = Task::completed()->count();

        return $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
    }



    private function calculateEmployeeCompletionRate($employeeName): float
    {
        $totalTasks = Task::byEmployee($employeeName)->count();
        $completedTasks = Task::byEmployee($employeeName)->completed()->count();

        return $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
    }

    private function calculateEmployeeEfficiency($employeeName): float
    {
        $tasks = Task::byEmployee($employeeName)
            ->completed()
            ->whereNotNull('estimated_hours')
            ->whereNotNull('actual_hours')
            ->get();

        if ($tasks->isEmpty()) {
            return 0;
        }

        $totalEfficiency = $tasks->sum(function ($task) {
            return $task->efficiency_rate ?? 0;
        });

        return $totalEfficiency / $tasks->count();
    }

    private function calculateAverageTaskDuration($employeeName): ?string
    {
        $tasks = Task::byEmployee($employeeName)
            ->completed()
            ->whereNotNull('start_date')
            ->whereNotNull('completed_at')
            ->get();

        if ($tasks->isEmpty()) {
            return null;
        }

        $totalMinutes = $tasks->sum(function ($task) {
            if ($task->start_date && $task->completed_at) {
                return Carbon::parse($task->start_date)->diffInMinutes(Carbon::parse($task->completed_at));
            }
            return 0;
        });

        $avgMinutes = $totalMinutes / $tasks->count();
        $hours = floor($avgMinutes / 60);
        $minutes = $avgMinutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . round($minutes) . 'm';
        } else {
            return round($minutes) . 'm';
        }
    }

    private function calculatePerformanceScore($stats): float
    {
        $completionWeight = 0.4;
        $efficiencyWeight = 0.3;
        $timelinessWeight = 0.3;

        $completionScore = $stats['completion_rate'];
        $efficiencyScore = $stats['efficiency_rate'];
        $timelinessScore = $stats['total_tasks'] > 0 ?
            (($stats['total_tasks'] - $stats['overdue_tasks']) / $stats['total_tasks']) * 100 : 0;

        return ($completionScore * $completionWeight) +
               ($efficiencyScore * $efficiencyWeight) +
               ($timelinessScore * $timelinessWeight);
    }

    private function calculateMonthlyEfficiency($date): float
    {
        $tasks = Task::completed()
            ->whereMonth('completed_at', $date->month)
            ->whereYear('completed_at', $date->year)
            ->whereNotNull('estimated_hours')
            ->whereNotNull('actual_hours')
            ->get();

        if ($tasks->isEmpty()) {
            return 0;
        }

        $totalEfficiency = $tasks->sum(function ($task) {
            return $task->efficiency_rate ?? 0;
        });

        return $totalEfficiency / $tasks->count();
    }

    private function calculateDailyHours($employeeName, $date): int
    {
        return Task::byEmployee($employeeName)
            ->whereDate('completed_at', $date)
            ->sum('actual_hours') ?? 0;
    }

    private function getWeeklyReport($employeeName = null): array
    {
        $startDate = now()->startOfWeek();
        $endDate = now()->endOfWeek();

        $query = Task::whereBetween('created_at', [$startDate, $endDate]);

        if ($employeeName) {
            $query->byEmployee($employeeName);
        }

        return [
            'period' => $startDate->format('M j') . ' - ' . $endDate->format('M j, Y'),
            'tasks_created' => $query->count(),
            'tasks_completed' => $query->completed()->count(),
            'tasks_overdue' => $query->overdue()->count(),
            'avg_completion_time' => $this->calculateAverageCompletionTime($query->completed()->get()),
        ];
    }

    private function getMonthlyReport($employeeName = null): array
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        $query = Task::whereBetween('created_at', [$startDate, $endDate]);

        if ($employeeName) {
            $query->byEmployee($employeeName);
        }

        return [
            'period' => $startDate->format('F Y'),
            'tasks_created' => $query->count(),
            'tasks_completed' => $query->completed()->count(),
            'tasks_overdue' => $query->overdue()->count(),
            'avg_completion_time' => $this->calculateAverageCompletionTime($query->completed()->get()),
        ];
    }

    private function calculateAverageCompletionTime($tasks): ?string
    {
        if ($tasks->isEmpty()) {
            return null;
        }

        $totalMinutes = $tasks->sum(function ($task) {
            return Carbon::parse($task->created_at)->diffInMinutes(Carbon::parse($task->completed_at));
        });

        $avgMinutes = $totalMinutes / $tasks->count();
        $days = floor($avgMinutes / 1440);
        $hours = floor(($avgMinutes % 1440) / 60);

        if ($days > 0) {
            return $days . 'd ' . $hours . 'h';
        } else {
            return $hours . 'h';
        }
    }

    private function getAnalyticsData($period, $employee): array
    {
        // This would return data for charts and analytics
        return [
            'productivity_chart' => $this->getProductivityChartData($period, $employee),
            'efficiency_chart' => $this->getEfficiencyChartData($period, $employee),
            'task_distribution' => $this->getTaskDistributionData($period, $employee),
        ];
    }

    /**
     * Get chart data for dashboard.
     */
    private function getChartData($period, $employee = null): array
    {
        return $this->getAnalyticsData($period, $employee);
    }

    /**
     * Generate performance report.
     */
    private function generateReport($reportType, $period, $employee = null): array
    {
        switch ($reportType) {
            case 'detailed':
                return $this->getDetailedReport($period, $employee);
            case 'summary':
            default:
                return $this->getSummaryReport($period, $employee);
        }
    }

    /**
     * Get export data.
     */
    private function getExportData($period, $employee = null): array
    {
        return $this->generateReport('detailed', $period, $employee);
    }

    /**
     * Export to PDF.
     */
    private function exportToPDF($data, $period, $employee = null)
    {
        $filename = "performance_report_{$period}" . ($employee ? "_{$employee}" : '') . '_' . date('Y-m-d') . '.pdf';

        $pdf = Pdf::loadView('modules.performance.pdf.report', [
            'data' => $data,
            'period' => $period,
            'employee' => $employee
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download($filename);
    }

    /**
     * Export to CSV.
     */
    private function exportToCSV($data, $period, $employee = null)
    {
        $filename = "performance_report_{$period}" . ($employee ? "_{$employee}" : '') . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            if (!empty($data)) {
                fputcsv($file, array_keys($data[0]));
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get detailed report.
     */
    private function getDetailedReport($period, $employee = null): array
    {
        return [
            'tasks' => $this->getRecentTasks($employee),
            'metrics' => $this->getKPIs($period, $employee),
            'analytics' => $this->getAnalyticsData($period, $employee),
        ];
    }

    /**
     * Get summary report.
     */
    private function getSummaryReport($period, $employee = null): array
    {
        return [
            'stats' => $this->getOverallStats($period, $employee),
            'top_performers' => $this->getTopPerformers($period),
        ];
    }

    private function getProductivityChartData($period, $employee): array
    {
        $dateRange = $this->getDateRange($period);
        $query = PerformanceMetric::whereBetween('metric_date', $dateRange);

        if ($employee) {
            $query->byEmployee($employee);
        }

        $metrics = $query->orderBy('metric_date')->get();

        return [
            'labels' => $metrics->pluck('metric_date')->map(function ($date) {
                return $date->format('M d');
            })->toArray(),
            'datasets' => [
                [
                    'label' => 'Productivity Score',
                    'data' => $metrics->pluck('productivity_score')->toArray(),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                ]
            ]
        ];
    }

    private function getEfficiencyChartData($period, $employee): array
    {
        $dateRange = $this->getDateRange($period);
        $query = PerformanceMetric::whereBetween('metric_date', $dateRange);

        if ($employee) {
            $query->byEmployee($employee);
        }

        $metrics = $query->orderBy('metric_date')->get();

        return [
            'labels' => $metrics->pluck('metric_date')->map(function ($date) {
                return $date->format('M d');
            })->toArray(),
            'datasets' => [
                [
                    'label' => 'Efficiency Rate',
                    'data' => $metrics->pluck('efficiency_rate')->toArray(),
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                ]
            ]
        ];
    }

    private function getTaskDistributionData($period, $employee): array
    {
        $dateRange = $this->getDateRange($period);
        $query = Task::whereBetween('created_at', $dateRange);

        if ($employee) {
            $query->byEmployee($employee);
        }

        return $query->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
    }

    /**
     * Get date range based on period.
     */
    private function getDateRange($period): array
    {
        return match($period) {
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'quarter' => [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }

    /**
     * Get recent tasks with filtering.
     */
    private function getRecentTasks($employee = null)
    {
        $query = Task::with('assignments')->latest();

        if ($employee) {
            $query->byEmployee($employee);
        }

        return $query->limit(10)->get();
    }

    /**
     * Get top performers for a period.
     */
    private function getTopPerformers($period = 'month')
    {
        $dateRange = $this->getDateRange($period);

        return PerformanceMetric::whereBetween('metric_date', $dateRange)
            ->topPerformers(10)
            ->get();
    }

    /**
     * Get KPIs for dashboard.
     */
    private function getKPIs($period = 'month', $employee = null): array
    {
        $dateRange = $this->getDateRange($period);

        $query = PerformanceMetric::whereBetween('metric_date', $dateRange);

        if ($employee) {
            $query->byEmployee($employee);
        }

        $metrics = $query->get();

        if ($metrics->isEmpty()) {
            return [
                'avg_productivity' => 0,
                'avg_efficiency' => 0,
                'avg_quality' => 0,
                'avg_overall' => 0,
            ];
        }

        return [
            'avg_productivity' => round($metrics->avg('productivity_score'), 1),
            'avg_efficiency' => round($metrics->avg('efficiency_rate'), 1),
            'avg_quality' => round($metrics->avg('quality_score'), 1),
            'avg_overall' => round($metrics->avg('overall_score'), 1),
        ];
    }

    /**
     * Get employee list for filtering.
     */
    private function getEmployeeList(): array
    {
        return TaskAssignment::select('employee_name')
            ->distinct()
            ->orderBy('employee_name')
            ->pluck('employee_name')
            ->toArray();
    }

    /**
     * Get average completion time.
     */
    private function getAverageCompletionTime($period = 'month', $employee = null): string
    {
        $dateRange = $this->getDateRange($period);
        $query = Task::completed()->whereBetween('completed_at', $dateRange);

        if ($employee) {
            $query->byEmployee($employee);
        }

        $tasks = $query->whereNotNull('start_date')->whereNotNull('completed_at')->get();

        if ($tasks->isEmpty()) {
            return '0h';
        }

        $totalMinutes = $tasks->sum(function ($task) {
            return $task->start_date->diffInMinutes($task->completed_at);
        });

        $avgMinutes = $totalMinutes / $tasks->count();
        $hours = round($avgMinutes / 60, 1);

        return $hours . 'h';
    }

    /**
     * Get productivity trend.
     */
    private function getProductivityTrend($period = 'month', $employee = null): string
    {
        $dateRange = $this->getDateRange($period);
        $previousRange = $this->getPreviousDateRange($period);

        $currentQuery = PerformanceMetric::whereBetween('metric_date', $dateRange);
        $previousQuery = PerformanceMetric::whereBetween('metric_date', $previousRange);

        if ($employee) {
            $currentQuery->byEmployee($employee);
            $previousQuery->byEmployee($employee);
        }

        $currentAvg = $currentQuery->avg('productivity_score') ?? 0;
        $previousAvg = $previousQuery->avg('productivity_score') ?? 0;

        if ($previousAvg == 0) {
            return 'neutral';
        }

        $change = (($currentAvg - $previousAvg) / $previousAvg) * 100;

        if ($change > 5) {
            return 'up';
        } elseif ($change < -5) {
            return 'down';
        } else {
            return 'neutral';
        }
    }

    /**
     * Get previous date range for comparison.
     */
    private function getPreviousDateRange($period): array
    {
        return match($period) {
            'week' => [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()],
            'month' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'quarter' => [Carbon::now()->subQuarter()->startOfQuarter(), Carbon::now()->subQuarter()->endOfQuarter()],
            'year' => [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()],
            default => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
        };
    }

    /**
     * Calculate average efficiency with period filtering.
     */
    private function calculateAverageEfficiency($period = 'month', $employee = null): float
    {
        $dateRange = $this->getDateRange($period);
        $query = PerformanceMetric::whereBetween('metric_date', $dateRange);

        if ($employee) {
            $query->byEmployee($employee);
        }

        return round($query->avg('efficiency_rate') ?? 0, 1);
    }



    /**
     * Generate summary report.
     */
    private function generateSummaryReport($dateRange, $employee): array
    {
        $query = Task::whereBetween('created_at', $dateRange);

        if ($employee) {
            $query->byEmployee($employee);
        }

        $tasks = $query->get();

        return [
            'total_tasks' => $tasks->count(),
            'completed_tasks' => $tasks->where('status', 'completed')->count(),
            'overdue_tasks' => $tasks->filter->is_overdue->count(),
            'avg_completion_time' => $this->calculateAverageCompletionTimeFromTasks($tasks),
            'task_distribution' => $tasks->groupBy('category')->map->count(),
            'priority_distribution' => $tasks->groupBy('priority')->map->count(),
            'status_distribution' => $tasks->groupBy('status')->map->count(),
        ];
    }

    /**
     * Generate detailed report.
     */
    private function generateDetailedReport($dateRange, $employee): array
    {
        $query = Task::with('assignments')->whereBetween('created_at', $dateRange);

        if ($employee) {
            $query->byEmployee($employee);
        }

        return [
            'tasks' => $query->get(),
            'summary' => $this->generateSummaryReport($dateRange, $employee),
        ];
    }

    /**
     * Generate productivity report.
     */
    private function generateProductivityReport($dateRange, $employee): array
    {
        $query = PerformanceMetric::whereBetween('metric_date', $dateRange);

        if ($employee) {
            $query->byEmployee($employee);
        }

        $metrics = $query->get();

        return [
            'avg_productivity' => $metrics->avg('productivity_score'),
            'productivity_trend' => $this->calculateTrend($metrics, 'productivity_score'),
            'top_performers' => $metrics->sortByDesc('productivity_score')->take(10),
            'productivity_by_employee' => $metrics->groupBy('employee_name')->map(function ($group) {
                return $group->avg('productivity_score');
            }),
        ];
    }

    /**
     * Generate efficiency report.
     */
    private function generateEfficiencyReport($dateRange, $employee): array
    {
        $query = PerformanceMetric::whereBetween('metric_date', $dateRange);

        if ($employee) {
            $query->byEmployee($employee);
        }

        $metrics = $query->get();

        return [
            'avg_efficiency' => $metrics->avg('efficiency_rate'),
            'efficiency_trend' => $this->calculateTrend($metrics, 'efficiency_rate'),
            'most_efficient' => $metrics->sortByDesc('efficiency_rate')->take(10),
            'efficiency_by_employee' => $metrics->groupBy('employee_name')->map(function ($group) {
                return $group->avg('efficiency_rate');
            }),
        ];
    }







    /**
     * Calculate trend from metrics.
     */
    private function calculateTrend($metrics, $field): string
    {
        if ($metrics->count() < 2) {
            return 'neutral';
        }

        $sorted = $metrics->sortBy('metric_date');
        $first = $sorted->first()->{$field};
        $last = $sorted->last()->{$field};

        if ($first == 0) {
            return 'neutral';
        }

        $change = (($last - $first) / $first) * 100;

        if ($change > 5) {
            return 'up';
        } elseif ($change < -5) {
            return 'down';
        } else {
            return 'neutral';
        }
    }

    /**
     * Calculate average completion time from task collection.
     */
    private function calculateAverageCompletionTimeFromTasks($tasks): string
    {
        $completedTasks = $tasks->filter(function ($task) {
            return $task->start_date && $task->completed_at;
        });

        if ($completedTasks->isEmpty()) {
            return '0h';
        }

        $totalMinutes = $completedTasks->sum(function ($task) {
            return $task->start_date->diffInMinutes($task->completed_at);
        });

        $avgMinutes = $totalMinutes / $completedTasks->count();
        $hours = round($avgMinutes / 60, 1);

        return $hours . 'h';
    }
}
