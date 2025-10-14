<?php

namespace Tests\Feature\HR;

use App\Models\User;
use App\Modules\HR\Models\Department;
use App\Modules\HR\Models\Employee;
use App\Modules\HR\Models\LeaveRequest;
use App\Modules\HR\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LeaveManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected $employee;

    protected $manager;

    protected $department;

    protected $role;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->department = Department::factory()->create();
        $this->role = Role::factory()->create(['department_id' => $this->department->id]);

        $this->manager = Employee::factory()->create([
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
        ]);

        $this->employee = Employee::factory()->create([
            'department_id' => $this->department->id,
            'role_id' => $this->role->id,
            'manager_id' => $this->manager->id,
            'annual_leave_balance' => 21,
            'sick_leave_balance' => 30,
            'emergency_leave_balance' => 5,
        ]);
    }

    /** @test */
    public function user_can_view_leave_requests_index()
    {
        LeaveRequest::factory()->count(3)->create([
            'employee_id' => $this->employee->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('modules.hr.leave-requests.index'));

        $response->assertStatus(200);
        $response->assertViewIs('modules.hr.leave-requests.index');
        $response->assertViewHas('leaveRequests');
    }

    /** @test */
    public function user_can_create_leave_request()
    {
        Storage::fake('public');

        $leaveData = [
            'employee_id' => $this->employee->id,
            'leave_type' => 'annual',
            'start_date' => now()->addDays(10)->format('Y-m-d'),
            'end_date' => now()->addDays(14)->format('Y-m-d'),
            'reason' => 'Family vacation',
            'reason_ar' => 'إجازة عائلية',
            'contact_during_leave' => '+966501234567',
            'attachments' => [
                UploadedFile::fake()->create('document.pdf', 100),
            ],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('modules.hr.leave-requests.store'), $leaveData);

        $response->assertRedirect();
        $this->assertDatabaseHas('hr_leave_requests', [
            'employee_id' => $this->employee->id,
            'leave_type' => 'annual',
            'reason' => 'Family vacation',
            'status' => 'pending',
        ]);

        $leaveRequest = LeaveRequest::where('employee_id', $this->employee->id)->first();
        $this->assertNotNull($leaveRequest->request_number);
        $this->assertTrue(str_starts_with($leaveRequest->request_number, 'LR'));
        $this->assertEquals(5, $leaveRequest->total_days);
    }

    /** @test */
    public function leave_request_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->post(route('modules.hr.leave-requests.store'), []);

        $response->assertSessionHasErrors([
            'employee_id',
            'leave_type',
            'start_date',
            'end_date',
            'reason',
        ]);
    }

    /** @test */
    public function leave_request_validates_sufficient_balance()
    {
        // Set low balance
        $this->employee->update(['annual_leave_balance' => 2]);

        $leaveData = [
            'employee_id' => $this->employee->id,
            'leave_type' => 'annual',
            'start_date' => now()->addDays(10)->format('Y-m-d'),
            'end_date' => now()->addDays(14)->format('Y-m-d'), // 5 days
            'reason' => 'Family vacation',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('modules.hr.leave-requests.store'), $leaveData);

        $response->assertSessionHasErrors(['leave_type']);
    }

    /** @test */
    public function leave_request_validates_no_overlapping_dates()
    {
        // Create existing leave request
        LeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(12),
            'status' => 'approved',
        ]);

        $leaveData = [
            'employee_id' => $this->employee->id,
            'leave_type' => 'annual',
            'start_date' => now()->addDays(11)->format('Y-m-d'), // Overlaps
            'end_date' => now()->addDays(15)->format('Y-m-d'),
            'reason' => 'Another vacation',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('modules.hr.leave-requests.store'), $leaveData);

        $response->assertSessionHasErrors(['start_date']);
    }

    /** @test */
    public function manager_can_approve_leave_request()
    {
        $leaveRequest = LeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'leave_type' => 'annual',
            'total_days' => 3,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('modules.hr.leave-requests.approve', $leaveRequest), [
                'approval_notes' => 'Approved for family time',
            ]);

        $response->assertRedirect();
        $leaveRequest->refresh();

        $this->assertEquals('approved', $leaveRequest->status);
        $this->assertNotNull($leaveRequest->approver_id);
        $this->assertNotNull($leaveRequest->approved_at);
        $this->assertEquals('Approved for family time', $leaveRequest->approval_notes);

        // Check that leave balance was deducted
        $this->employee->refresh();
        $this->assertEquals(18, $this->employee->annual_leave_balance); // 21 - 3
    }

    /** @test */
    public function manager_can_reject_leave_request()
    {
        $leaveRequest = LeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'leave_type' => 'annual',
            'total_days' => 3,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('modules.hr.leave-requests.reject', $leaveRequest), [
                'rejection_reason' => 'Busy period, please reschedule',
            ]);

        $response->assertRedirect();
        $leaveRequest->refresh();

        $this->assertEquals('rejected', $leaveRequest->status);
        $this->assertNotNull($leaveRequest->approver_id);
        $this->assertNotNull($leaveRequest->approved_at);
        $this->assertEquals('Busy period, please reschedule', $leaveRequest->rejection_reason);

        // Check that leave balance was not deducted
        $this->employee->refresh();
        $this->assertEquals(21, $this->employee->annual_leave_balance);
    }

    /** @test */
    public function employee_can_cancel_pending_leave_request()
    {
        $leaveRequest = LeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'leave_type' => 'annual',
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(12),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('modules.hr.leave-requests.cancel', $leaveRequest));

        $response->assertRedirect();
        $leaveRequest->refresh();

        $this->assertEquals('cancelled', $leaveRequest->status);
    }

    /** @test */
    public function employee_can_cancel_approved_leave_request_and_restore_balance()
    {
        $leaveRequest = LeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'leave_type' => 'annual',
            'total_days' => 3,
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(12),
            'status' => 'approved',
        ]);

        // Simulate balance deduction
        $this->employee->update(['annual_leave_balance' => 18]);

        $response = $this->actingAs($this->user)
            ->patch(route('modules.hr.leave-requests.cancel', $leaveRequest));

        $response->assertRedirect();
        $leaveRequest->refresh();

        $this->assertEquals('cancelled', $leaveRequest->status);

        // Check that leave balance was restored
        $this->employee->refresh();
        $this->assertEquals(21, $this->employee->annual_leave_balance); // 18 + 3
    }

    /** @test */
    public function user_can_filter_leave_requests_by_status()
    {
        LeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'status' => 'pending',
        ]);

        LeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('modules.hr.leave-requests.index', ['status' => 'pending']));

        $response->assertStatus(200);
        $leaveRequests = $response->viewData('leaveRequests');
        $this->assertEquals(1, $leaveRequests->count());
    }

    /** @test */
    public function user_can_filter_leave_requests_by_leave_type()
    {
        LeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'leave_type' => 'annual',
        ]);

        LeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'leave_type' => 'sick',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('modules.hr.leave-requests.index', ['leave_type' => 'annual']));

        $response->assertStatus(200);
        $leaveRequests = $response->viewData('leaveRequests');
        $this->assertEquals(1, $leaveRequests->count());
    }

    /** @test */
    public function user_can_download_leave_request_attachment()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 100);
        $path = $file->store('leave-requests/attachments', 'public');

        $leaveRequest = LeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'attachments' => [
                [
                    'name' => 'document.pdf',
                    'path' => $path,
                    'size' => 100,
                ],
            ],
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('modules.hr.leave-requests.attachments.download', [$leaveRequest, 0]));

        $response->assertStatus(200);
    }

    /** @test */
    public function leave_request_calculates_total_days_correctly()
    {
        $leaveRequest = new LeaveRequest([
            'start_date' => '2025-08-04', // Monday
            'end_date' => '2025-08-08',   // Friday
            'is_half_day' => false,
        ]);

        $totalDays = $leaveRequest->calculateTotalDays();
        $this->assertEquals(5, $totalDays); // Monday to Friday (excluding weekends)
    }

    /** @test */
    public function half_day_leave_request_counts_as_one_day()
    {
        $leaveRequest = new LeaveRequest([
            'start_date' => '2025-08-04',
            'end_date' => '2025-08-04',
            'is_half_day' => true,
            'half_day_period' => 'morning',
        ]);

        $totalDays = $leaveRequest->calculateTotalDays();
        $this->assertEquals(1, $totalDays);
    }
}
