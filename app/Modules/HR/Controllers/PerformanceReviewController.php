<?php

namespace App\Modules\HR\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HR\Models\Employee;
use App\Modules\HR\Models\PerformanceReview;
use App\Modules\HR\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerformanceReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = PerformanceReview::with(['employee.department', 'reviewer']);

        // Apply filters
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('review_period')) {
            $query->where('review_period', $request->review_period);
        }

        if ($request->filled('year')) {
            $query->whereYear('review_date', $request->year);
        }

        $reviews = $query->orderBy('review_date', 'desc')->paginate(20);

        // Summary statistics
        $summary = [
            'total_reviews' => PerformanceReview::count(),
            'pending_reviews' => PerformanceReview::where('status', 'pending')->count(),
            'completed_reviews' => PerformanceReview::where('status', 'completed')->count(),
            'avg_overall_rating' => PerformanceReview::where('status', 'completed')->avg('overall_rating'),
        ];

        $employees = Employee::active()->orderBy('first_name')->get();
        $departments = Department::active()->orderBy('name')->get();
        $years = collect(range(now()->year - 2, now()->year + 1));

        return view('modules.hr.performance-reviews.index', compact(
            'reviews', 'summary', 'employees', 'departments', 'years'
        ));
    }

    public function create()
    {
        $employees = Employee::active()->with('department')->orderBy('first_name')->get();
        $reviewers = Employee::active()->orderBy('first_name')->get();
        
        return view('modules.hr.performance-reviews.create', compact('employees', 'reviewers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'reviewer_id' => 'required|exists:hr_employees,id',
            'review_period' => 'required|in:quarterly,semi_annual,annual',
            'review_date' => 'required|date',
            'goals' => 'nullable|array',
            'goals.*' => 'string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $review = PerformanceReview::create([
                'employee_id' => $request->employee_id,
                'reviewer_id' => $request->reviewer_id,
                'review_period' => $request->review_period,
                'review_date' => $request->review_date,
                'goals' => $request->goals ?? [],
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('modules.hr.performance-reviews.show', $review)
                           ->with('success', __('hr.performance_review_created_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->withErrors(['error' => __('hr.error_creating_performance_review')]);
        }
    }

    public function show(PerformanceReview $performanceReview)
    {
        $performanceReview->load(['employee.department', 'reviewer', 'createdBy']);
        
        return view('modules.hr.performance-reviews.show', compact('performanceReview'));
    }

    public function edit(PerformanceReview $performanceReview)
    {
        $employees = Employee::active()->with('department')->orderBy('first_name')->get();
        $reviewers = Employee::active()->orderBy('first_name')->get();
        
        return view('modules.hr.performance-reviews.edit', compact('performanceReview', 'employees', 'reviewers'));
    }

    public function update(Request $request, PerformanceReview $performanceReview)
    {
        $request->validate([
            'technical_skills' => 'nullable|integer|between:1,5',
            'communication_skills' => 'nullable|integer|between:1,5',
            'teamwork' => 'nullable|integer|between:1,5',
            'leadership' => 'nullable|integer|between:1,5',
            'problem_solving' => 'nullable|integer|between:1,5',
            'initiative' => 'nullable|integer|between:1,5',
            'punctuality' => 'nullable|integer|between:1,5',
            'quality_of_work' => 'nullable|integer|between:1,5',
            'achievements' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'goals_next_period' => 'nullable|array',
            'goals_next_period.*' => 'string|max:500',
            'reviewer_comments' => 'nullable|string',
            'employee_comments' => 'nullable|string',
            'overall_rating' => 'nullable|numeric|between:1,5',
        ]);

        // Calculate overall rating if individual ratings are provided
        $ratings = [
            'technical_skills' => $request->technical_skills,
            'communication_skills' => $request->communication_skills,
            'teamwork' => $request->teamwork,
            'leadership' => $request->leadership,
            'problem_solving' => $request->problem_solving,
            'initiative' => $request->initiative,
            'punctuality' => $request->punctuality,
            'quality_of_work' => $request->quality_of_work,
        ];

        $validRatings = array_filter($ratings, function($rating) {
            return !is_null($rating);
        });

        $overallRating = $request->overall_rating;
        if (empty($overallRating) && !empty($validRatings)) {
            $overallRating = array_sum($validRatings) / count($validRatings);
        }

        $performanceReview->update([
            'technical_skills' => $request->technical_skills,
            'communication_skills' => $request->communication_skills,
            'teamwork' => $request->teamwork,
            'leadership' => $request->leadership,
            'problem_solving' => $request->problem_solving,
            'initiative' => $request->initiative,
            'punctuality' => $request->punctuality,
            'quality_of_work' => $request->quality_of_work,
            'achievements' => $request->achievements,
            'areas_for_improvement' => $request->areas_for_improvement,
            'goals_next_period' => $request->goals_next_period ?? [],
            'reviewer_comments' => $request->reviewer_comments,
            'employee_comments' => $request->employee_comments,
            'overall_rating' => $overallRating,
            'status' => $request->has('complete_review') ? 'completed' : 'in_progress',
            'completed_at' => $request->has('complete_review') ? now() : null,
        ]);

        return redirect()->route('modules.hr.performance-reviews.show', $performanceReview)
                       ->with('success', __('hr.performance_review_updated_successfully'));
    }

    public function destroy(PerformanceReview $performanceReview)
    {
        $performanceReview->delete();
        
        return redirect()->route('modules.hr.performance-reviews.index')
                       ->with('success', __('hr.performance_review_deleted_successfully'));
    }

    public function complete(PerformanceReview $performanceReview)
    {
        if ($performanceReview->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => __('hr.performance_review_already_completed')
            ]);
        }

        $performanceReview->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('hr.performance_review_completed_successfully')
        ]);
    }

    public function reports(Request $request)
    {
        $year = $request->get('year', now()->year);
        $departmentId = $request->get('department_id');
        
        // Performance summary by department
        $departmentPerformance = DB::table('hr_performance_reviews')
            ->join('hr_employees', 'hr_performance_reviews.employee_id', '=', 'hr_employees.id')
            ->join('hr_departments', 'hr_employees.department_id', '=', 'hr_departments.id')
            ->where('hr_performance_reviews.status', 'completed')
            ->whereYear('hr_performance_reviews.review_date', $year)
            ->when($departmentId, function ($query) use ($departmentId) {
                return $query->where('hr_departments.id', $departmentId);
            })
            ->selectRaw('
                hr_departments.name as department_name,
                COUNT(*) as total_reviews,
                AVG(overall_rating) as avg_rating,
                AVG(technical_skills) as avg_technical,
                AVG(communication_skills) as avg_communication,
                AVG(teamwork) as avg_teamwork,
                AVG(leadership) as avg_leadership
            ')
            ->groupBy('hr_departments.id', 'hr_departments.name')
            ->get();

        // Top performers
        $topPerformers = PerformanceReview::with(['employee.department'])
            ->where('status', 'completed')
            ->whereYear('review_date', $year)
            ->when($departmentId, function ($query) use ($departmentId) {
                return $query->whereHas('employee', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            })
            ->orderByDesc('overall_rating')
            ->take(10)
            ->get();

        // Performance trends (monthly)
        $performanceTrends = PerformanceReview::where('status', 'completed')
            ->whereYear('review_date', $year)
            ->when($departmentId, function ($query) use ($departmentId) {
                return $query->whereHas('employee', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            })
            ->selectRaw('
                MONTH(review_date) as month,
                AVG(overall_rating) as avg_rating,
                COUNT(*) as review_count
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $departments = Department::active()->orderBy('name')->get();
        $years = collect(range(now()->year - 3, now()->year));

        return view('modules.hr.performance-reviews.reports', compact(
            'departmentPerformance', 'topPerformers', 'performanceTrends',
            'departments', 'years', 'year', 'departmentId'
        ));
    }

    public function bulkCreate(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:hr_employees,id',
            'reviewer_id' => 'required|exists:hr_employees,id',
            'review_period' => 'required|in:quarterly,semi_annual,annual',
            'review_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $created = 0;
            foreach ($request->employee_ids as $employeeId) {
                // Check if review already exists for this period
                $exists = PerformanceReview::where('employee_id', $employeeId)
                    ->where('review_period', $request->review_period)
                    ->whereYear('review_date', Carbon::parse($request->review_date)->year)
                    ->exists();

                if (!$exists) {
                    PerformanceReview::create([
                        'employee_id' => $employeeId,
                        'reviewer_id' => $request->reviewer_id,
                        'review_period' => $request->review_period,
                        'review_date' => $request->review_date,
                        'status' => 'pending',
                        'created_by' => auth()->id(),
                    ]);
                    $created++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('hr.bulk_reviews_created', ['count' => $created])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => __('hr.error_creating_bulk_reviews')
            ]);
        }
    }

    public function export(Request $request)
    {
        $year = $request->get('year', now()->year);
        $departmentId = $request->get('department_id');
        
        $reviews = PerformanceReview::with(['employee.department', 'reviewer'])
            ->where('status', 'completed')
            ->whereYear('review_date', $year)
            ->when($departmentId, function ($query) use ($departmentId) {
                return $query->whereHas('employee', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            })
            ->get();

        // Generate CSV
        $filename = "performance_reviews_{$year}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($reviews) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Employee Name', 'Department', 'Review Period', 'Review Date',
                'Technical Skills', 'Communication', 'Teamwork', 'Leadership',
                'Problem Solving', 'Initiative', 'Punctuality', 'Quality of Work',
                'Overall Rating', 'Status', 'Reviewer'
            ]);

            // CSV data
            foreach ($reviews as $review) {
                fputcsv($file, [
                    $review->employee->full_name,
                    $review->employee->department->name ?? 'N/A',
                    ucfirst(str_replace('_', ' ', $review->review_period)),
                    $review->review_date->format('Y-m-d'),
                    $review->technical_skills ?? 'N/A',
                    $review->communication_skills ?? 'N/A',
                    $review->teamwork ?? 'N/A',
                    $review->leadership ?? 'N/A',
                    $review->problem_solving ?? 'N/A',
                    $review->initiative ?? 'N/A',
                    $review->punctuality ?? 'N/A',
                    $review->quality_of_work ?? 'N/A',
                    $review->overall_rating ?? 'N/A',
                    ucfirst($review->status),
                    $review->reviewer->full_name ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
