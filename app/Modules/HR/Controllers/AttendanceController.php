<?php

namespace App\Modules\HR\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HR\Models\Attendance;
use App\Modules\HR\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records.
     */
    public function index(Request $request): View
    {
        $query = Attendance::with(['employee.department']);

        // Apply filters
        if ($request->filled('search')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->search($request->search);
            });
        }

        if ($request->filled('employee_id')) {
            $query->byEmployee($request->employee_id);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        } else {
            // Default to current month
            $query->currentMonth();
        }

        // Sort
        $sortBy = $request->get('sort_by', 'date');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $attendanceRecords = $query->paginate(20)->withQueryString();

        // Get filter options
        $employees = Employee::active()->orderBy('first_name')->get();

        // Get attendance statistics
        $stats = Attendance::getAttendanceStats(
            $request->date_from ? Carbon::parse($request->date_from) : null,
            $request->date_to ? Carbon::parse($request->date_to) : null
        );

        return view('modules.hr.attendance.index', compact(
            'attendanceRecords',
            'employees',
            'stats'
        ));
    }

    /**
     * Show the form for creating attendance records.
     */
    public function create(): View
    {
        $employees = Employee::active()->orderBy('first_name')->get();
        $today = Carbon::today();

        return view('modules.hr.attendance.create', compact('employees', 'today'));
    }

    /**
     * Store a newly created attendance record.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'date' => 'required|date',
            'scheduled_in' => 'required|date_format:H:i',
            'scheduled_out' => 'required|date_format:H:i|after:scheduled_in',
            'actual_in' => 'nullable|date_format:H:i',
            'actual_out' => 'nullable|date_format:H:i|after:actual_in',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after:break_start',
            'status' => 'required|in:present,absent,late,half_day,on_leave,holiday,weekend,sick,excused',
            'notes' => 'nullable|string',
            'location' => 'nullable|string|max:255',
        ]);

        // Check if record already exists
        $existing = Attendance::where('employee_id', $validated['employee_id'])
            ->where('date', $validated['date'])
            ->first();

        if ($existing) {
            return back()->withErrors(['date' => 'Attendance record already exists for this date.'])
                ->withInput();
        }

        $attendance = Attendance::create($validated);

        // Calculate working hours if both times are provided
        if ($attendance->actual_in && $attendance->actual_out) {
            $attendance->calculateWorkingHours();
        }

        return redirect()->route('modules.hr.attendance.show', $attendance)
            ->with('success', __('hr.attendance_recorded_successfully'));
    }

    /**
     * Display the specified attendance record.
     */
    public function show(Attendance $attendance): View
    {
        $attendance->load(['employee.department', 'employee.role', 'approver']);

        return view('modules.hr.attendance.show', compact('attendance'));
    }

    /**
     * Show the form for editing the attendance record.
     */
    public function edit(Attendance $attendance): View
    {
        $employees = Employee::active()->orderBy('first_name')->get();

        return view('modules.hr.attendance.edit', compact('attendance', 'employees'));
    }

    /**
     * Update the specified attendance record.
     */
    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        $validated = $request->validate([
            'scheduled_in' => 'required|date_format:H:i',
            'scheduled_out' => 'required|date_format:H:i|after:scheduled_in',
            'actual_in' => 'nullable|date_format:H:i',
            'actual_out' => 'nullable|date_format:H:i|after:actual_in',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after:break_start',
            'status' => 'required|in:present,absent,late,half_day,on_leave,holiday,weekend,sick,excused',
            'notes' => 'nullable|string',
            'location' => 'nullable|string|max:255',
        ]);

        $attendance->update($validated);

        // Recalculate working hours
        if ($attendance->actual_in && $attendance->actual_out) {
            $attendance->calculateWorkingHours();
        }

        return redirect()->route('modules.hr.attendance.show', $attendance)
            ->with('success', 'Attendance record updated successfully.');
    }

    /**
     * Remove the specified attendance record.
     */
    public function destroy(Attendance $attendance): RedirectResponse
    {
        $attendance->delete();

        return redirect()->route('modules.hr.attendance.index')
            ->with('success', 'Attendance record deleted successfully.');
    }

    /**
     * Check in an employee.
     */
    public function checkIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'check_in_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $today = Carbon::today();
        $checkInTime = $validated['check_in_time']
            ? Carbon::createFromFormat('H:i', $validated['check_in_time'])
            : now();

        // Get or create attendance record for today
        $attendance = Attendance::firstOrCreate(
            [
                'employee_id' => $validated['employee_id'],
                'date' => $today,
            ],
            [
                'scheduled_in' => '09:00:00',
                'scheduled_out' => '17:00:00',
                'status' => 'absent',
            ]
        );

        // Check in
        $attendance->checkIn(
            $checkInTime,
            $request->has('latitude') && $request->has('longitude')
                ? ['lat' => $request->latitude, 'lng' => $request->longitude]
                : null,
            $request->ip()
        );

        if ($validated['location']) {
            $attendance->update(['location' => $validated['location']]);
        }

        if ($validated['notes']) {
            $attendance->update(['notes' => $validated['notes']]);
        }

        return back()->with('success', 'Check-in recorded successfully.');
    }

    /**
     * Check out an employee.
     */
    public function checkOut(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'check_out_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        $today = Carbon::today();
        $checkOutTime = $validated['check_out_time']
            ? Carbon::createFromFormat('H:i', $validated['check_out_time'])
            : now();

        $attendance = Attendance::where('employee_id', $validated['employee_id'])
            ->where('date', $today)
            ->first();

        if (! $attendance) {
            return back()->with('error', 'No check-in record found for today.');
        }

        if (! $attendance->actual_in) {
            return back()->with('error', 'Employee must check in first.');
        }

        // Check out
        $attendance->checkOut(
            $checkOutTime,
            $request->has('latitude') && $request->has('longitude')
                ? ['lat' => $request->latitude, 'lng' => $request->longitude]
                : null
        );

        if ($validated['notes']) {
            $attendance->update(['notes' => $validated['notes']]);
        }

        return back()->with('success', 'Check-out recorded successfully.');
    }

    /**
     * Approve attendance record.
     */
    public function approve(Attendance $attendance): RedirectResponse
    {
        // For demo purposes, use the first manager
        $approverId = Employee::whereHas('role', function ($query) {
            $query->whereIn('level', ['manager', 'lead']);
        })->first()->id ?? 1;

        $attendance->approve($approverId);

        return back()->with('success', 'Attendance record approved successfully.');
    }

    /**
     * Generate daily attendance records for all employees.
     */
    public function generateDaily(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($validated['date']);

        // Skip weekends
        if (in_array($date->dayOfWeek, [5, 6])) { // Friday and Saturday
            return back()->with('error', 'Cannot generate attendance for weekends.');
        }

        Attendance::createDailyAttendance($date);

        return back()->with('success', 'Daily attendance records generated successfully.');
    }

    /**
     * Get employee attendance summary.
     */
    public function employeeSummary(Request $request, Employee $employee)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $summary = Attendance::getEmployeeAttendanceSummary($employee->id, $year, $month);

        return response()->json($summary);
    }

    /**
     * Get attendance calendar data.
     */
    public function calendar(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $employeeId = $request->get('employee_id');

        $query = Attendance::with('employee')
            ->whereYear('date', $year)
            ->whereMonth('date', $month);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $attendanceRecords = $query->get();

        $calendarData = $attendanceRecords->map(function ($attendance) {
            return [
                'date' => $attendance->date->format('Y-m-d'),
                'employee' => $attendance->employee->display_name,
                'status' => $attendance->status,
                'check_in' => $attendance->check_in_time,
                'check_out' => $attendance->check_out_time,
                'total_hours' => $attendance->formatted_total_hours,
                'overtime_hours' => $attendance->formatted_overtime_hours,
            ];
        });

        return response()->json($calendarData);
    }

    /**
     * Export attendance records to CSV.
     */
    public function export(Request $request)
    {
        $query = Attendance::with(['employee.department']);

        // Apply same filters as index
        if ($request->filled('employee_id')) {
            $query->byEmployee($request->employee_id);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        $attendanceRecords = $query->get();

        $filename = 'attendance_'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($attendanceRecords) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Date',
                'Employee',
                'Department',
                'Scheduled In',
                'Scheduled Out',
                'Actual In',
                'Actual Out',
                'Total Hours',
                'Overtime Hours',
                'Late Minutes',
                'Status',
                'Location',
            ]);

            // CSV data
            foreach ($attendanceRecords as $record) {
                fputcsv($file, [
                    $record->date->format('Y-m-d'),
                    $record->employee->display_name,
                    $record->employee->department->display_name,
                    $record->scheduled_in->format('H:i'),
                    $record->scheduled_out->format('H:i'),
                    $record->check_in_time ?? 'N/A',
                    $record->check_out_time ?? 'N/A',
                    $record->formatted_total_hours,
                    $record->formatted_overtime_hours,
                    $record->late_minutes,
                    $record->status_text,
                    $record->location ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
