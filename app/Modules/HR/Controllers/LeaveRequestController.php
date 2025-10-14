<?php

namespace App\Modules\HR\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HR\Models\Employee;
use App\Modules\HR\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of leave requests.
     */
    public function index(Request $request): View
    {
        $query = LeaveRequest::with(['employee.department', 'approver']);

        // Apply filters
        if ($request->filled('search')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->search($request->search);
            });
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('leave_type')) {
            $query->byLeaveType($request->leave_type);
        }

        if ($request->filled('employee_id')) {
            $query->byEmployee($request->employee_id);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $leaveRequests = $query->paginate(15)->withQueryString();

        // Get summary statistics
        $summary = [
            'total_requests' => LeaveRequest::count(),
            'pending_requests' => LeaveRequest::where('status', 'pending')->count(),
            'approved_requests' => LeaveRequest::where('status', 'approved')->count(),
            'rejected_requests' => LeaveRequest::where('status', 'rejected')->count(),
            'current_month_requests' => LeaveRequest::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'employees_on_leave' => LeaveRequest::where('status', 'approved')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->count(),
        ];

        // Get filter options
        $employees = Employee::active()->orderBy('first_name')->get();

        // Get status options
        $statuses = [
            'pending' => __('hr.pending'),
            'approved' => __('hr.approved'),
            'rejected' => __('hr.rejected'),
            'cancelled' => __('hr.cancelled'),
        ];

        // Get leave type options
        $leaveTypes = [
            'annual' => __('hr.annual_leave'),
            'sick' => __('hr.sick_leave'),
            'emergency' => __('hr.emergency_leave'),
            'maternity' => __('hr.maternity_leave'),
            'paternity' => __('hr.paternity_leave'),
            'unpaid' => __('hr.unpaid_leave'),
            'study' => __('hr.study_leave'),
            'hajj' => __('hr.hajj_leave'),
            'bereavement' => __('hr.bereavement_leave'),
            'other' => __('hr.other_leave'),
        ];

        return view('modules.hr.leave-requests.index', compact(
            'leaveRequests',
            'employees',
            'summary',
            'statuses',
            'leaveTypes'
        ));
    }

    /**
     * Show the form for creating a new leave request.
     */
    public function create(): View
    {
        $employees = Employee::active()->orderBy('first_name')->get();

        return view('modules.hr.leave-requests.create', compact('employees'));
    }

    /**
     * Store a newly created leave request.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'leave_type' => 'required|in:annual,sick,emergency,maternity,paternity,unpaid,study,hajj,bereavement,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_half_day' => 'boolean',
            'half_day_period' => 'nullable|required_if:is_half_day,true|in:morning,afternoon',
            'reason' => 'required|string',
            'reason_ar' => 'nullable|string',
            'contact_during_leave' => 'nullable|string|max:255',
            'attachments.*' => 'nullable|file|max:5120', // 5MB max per file
        ]);

        // Generate request number
        $validated['request_number'] = LeaveRequest::generateRequestNumber();

        // Calculate total days
        $leaveRequest = new LeaveRequest($validated);
        $validated['total_days'] = $leaveRequest->calculateTotalDays();

        // Check leave balance
        $employee = Employee::find($validated['employee_id']);
        if (in_array($validated['leave_type'], ['annual', 'sick', 'emergency'])) {
            $balance = $employee->getLeaveBalance($validated['leave_type']);
            if ($balance < $validated['total_days']) {
                return back()->withErrors(['leave_type' => __('hr.insufficient_leave_balance')])
                    ->withInput();
            }
        }

        // Check for overlapping requests
        $overlapping = LeaveRequest::where('employee_id', $validated['employee_id'])
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($subQuery) use ($validated) {
                        $subQuery->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                    });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['start_date' => __('hr.overlapping_leave_request')])
                ->withInput();
        }

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('leave-requests/attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                ];
            }
            $validated['attachments'] = $attachments;
        }

        $leaveRequest = LeaveRequest::create($validated);

        return redirect()->route('modules.hr.leave-requests.show', $leaveRequest)
            ->with('success', __('hr.leave_request_submitted_successfully'));
    }

    /**
     * Display the specified leave request.
     */
    public function show(LeaveRequest $leaveRequest): View
    {
        $leaveRequest->load(['employee.department', 'employee.role', 'approver', 'processor']);

        return view('modules.hr.leave-requests.show', compact('leaveRequest'));
    }

    /**
     * Show the form for editing the leave request.
     */
    public function edit(LeaveRequest $leaveRequest): View
    {
        // Only allow editing pending requests
        if ($leaveRequest->status !== 'pending') {
            abort(403, 'Cannot edit non-pending leave request.');
        }

        $employees = Employee::active()->orderBy('first_name')->get();

        return view('modules.hr.leave-requests.edit', compact('leaveRequest', 'employees'));
    }

    /**
     * Update the specified leave request.
     */
    public function update(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        // Only allow updating pending requests
        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Cannot update non-pending leave request.');
        }

        $validated = $request->validate([
            'leave_type' => 'required|in:annual,sick,emergency,maternity,paternity,unpaid,study,hajj,bereavement,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_half_day' => 'boolean',
            'half_day_period' => 'nullable|required_if:is_half_day,true|in:morning,afternoon',
            'reason' => 'required|string',
            'reason_ar' => 'nullable|string',
            'contact_during_leave' => 'nullable|string|max:255',
            'attachments.*' => 'nullable|file|max:5120',
        ]);

        // Calculate total days
        $tempRequest = new LeaveRequest($validated);
        $validated['total_days'] = $tempRequest->calculateTotalDays();

        // Check leave balance
        if (in_array($validated['leave_type'], ['annual', 'sick', 'emergency'])) {
            $balance = $leaveRequest->employee->getLeaveBalance($validated['leave_type']);
            if ($balance < $validated['total_days']) {
                return back()->withErrors(['leave_type' => __('hr.insufficient_leave_balance')])
                    ->withInput();
            }
        }

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            // Delete old attachments
            if ($leaveRequest->attachments) {
                foreach ($leaveRequest->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment['path']);
                }
            }

            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('leave-requests/attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                ];
            }
            $validated['attachments'] = $attachments;
        }

        $leaveRequest->update($validated);

        return redirect()->route('modules.hr.leave-requests.show', $leaveRequest)
            ->with('success', 'Leave request updated successfully.');
    }

    /**
     * Approve the leave request.
     */
    public function approve(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Can only approve pending requests.');
        }

        $validated = $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        // For demo purposes, use the first manager or admin
        $approverId = Employee::whereHas('role', function ($query) {
            $query->whereIn('level', ['manager', 'lead']);
        })->first()->id ?? 1;

        $leaveRequest->approve($approverId, $validated['approval_notes'] ?? null);

        return back()->with('success', __('hr.leave_request_approved_successfully'));
    }

    /**
     * Reject the leave request.
     */
    public function reject(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Can only reject pending requests.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        // For demo purposes, use the first manager or admin
        $approverId = Employee::whereHas('role', function ($query) {
            $query->whereIn('level', ['manager', 'lead']);
        })->first()->id ?? 1;

        $leaveRequest->reject($approverId, $validated['rejection_reason']);

        return back()->with('success', __('hr.leave_request_rejected_successfully'));
    }

    /**
     * Cancel the leave request.
     */
    public function cancel(LeaveRequest $leaveRequest): RedirectResponse
    {
        if (! $leaveRequest->can_be_cancelled) {
            return back()->with('error', 'Cannot cancel this leave request.');
        }

        $leaveRequest->cancel();

        return back()->with('success', 'Leave request cancelled successfully.');
    }

    /**
     * Download attachment.
     */
    public function downloadAttachment(LeaveRequest $leaveRequest, $index)
    {
        if (! $leaveRequest->attachments || ! isset($leaveRequest->attachments[$index])) {
            abort(404);
        }

        $attachment = $leaveRequest->attachments[$index];

        if (! Storage::disk('public')->exists($attachment['path'])) {
            abort(404);
        }

        return response()->download(storage_path('app/public/'.$attachment['path']), $attachment['name']);
    }

    /**
     * Get employee leave balance (AJAX).
     */
    public function getEmployeeLeaveBalance(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $employee = Employee::find($employeeId);

        if (! $employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        return response()->json([
            'annual_leave_balance' => $employee->annual_leave_balance,
            'sick_leave_balance' => $employee->sick_leave_balance,
            'emergency_leave_balance' => $employee->emergency_leave_balance,
        ]);
    }

    /**
     * Export leave requests to CSV.
     */
    public function export(Request $request)
    {
        $query = LeaveRequest::with(['employee.department', 'approver']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('leave_type')) {
            $query->byLeaveType($request->leave_type);
        }

        $leaveRequests = $query->get();

        $filename = 'leave_requests_'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($leaveRequests) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Request Number',
                'Employee',
                'Department',
                'Leave Type',
                'Start Date',
                'End Date',
                'Total Days',
                'Status',
                'Approver',
                'Approved At',
            ]);

            // CSV data
            foreach ($leaveRequests as $request) {
                fputcsv($file, [
                    $request->request_number,
                    $request->employee->display_name,
                    $request->employee->department->display_name,
                    $request->leave_type_text,
                    $request->start_date->format('Y-m-d'),
                    $request->end_date->format('Y-m-d'),
                    $request->total_days,
                    $request->status_text,
                    $request->approver ? $request->approver->display_name : 'N/A',
                    $request->approved_at ? $request->approved_at->format('Y-m-d H:i') : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
