<?php

namespace Database\Factories\HR;

use App\Modules\HR\Models\Employee;
use App\Modules\HR\Models\LeaveRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveRequestFactory extends Factory
{
    protected $model = LeaveRequest::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+3 months');
        $endDate = $this->faker->dateTimeBetween($startDate, $startDate->format('Y-m-d').' +14 days');

        $leaveTypes = ['annual', 'sick', 'emergency', 'maternity', 'paternity', 'unpaid', 'study', 'hajj', 'bereavement'];
        $leaveType = $this->faker->randomElement($leaveTypes);

        $reasons = [
            'annual' => ['Family vacation', 'Personal time off', 'Rest and relaxation'],
            'sick' => ['Medical treatment', 'Recovery from illness', 'Doctor appointment'],
            'emergency' => ['Family emergency', 'Urgent personal matter', 'Unexpected situation'],
            'maternity' => ['Maternity leave', 'Childbirth preparation'],
            'paternity' => ['Paternity leave', 'New baby care'],
            'unpaid' => ['Extended personal leave', 'Financial constraints'],
            'study' => ['Educational course', 'Professional development', 'Exam preparation'],
            'hajj' => ['Hajj pilgrimage', 'Religious obligation'],
            'bereavement' => ['Family bereavement', 'Funeral attendance'],
        ];

        $reasonsAr = [
            'annual' => ['إجازة عائلية', 'وقت شخصي', 'راحة واستجمام'],
            'sick' => ['علاج طبي', 'التعافي من المرض', 'موعد طبي'],
            'emergency' => ['طوارئ عائلية', 'أمر شخصي عاجل', 'موقف غير متوقع'],
            'maternity' => ['إجازة أمومة', 'التحضير للولادة'],
            'paternity' => ['إجازة أبوة', 'رعاية المولود الجديد'],
            'unpaid' => ['إجازة شخصية ممتدة', 'ظروف مالية'],
            'study' => ['دورة تعليمية', 'تطوير مهني', 'التحضير للامتحان'],
            'hajj' => ['حج', 'التزام ديني'],
            'bereavement' => ['وفاة في العائلة', 'حضور جنازة'],
        ];

        $reason = $this->faker->randomElement($reasons[$leaveType]);
        $reasonAr = $this->faker->randomElement($reasonsAr[$leaveType]);

        return [
            'request_number' => LeaveRequest::generateRequestNumber(),
            'employee_id' => Employee::factory(),
            'leave_type' => $leaveType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $this->calculateWorkingDays($startDate, $endDate),
            'is_half_day' => false,
            'half_day_period' => null,
            'reason' => $reason,
            'reason_ar' => $reasonAr,
            'contact_during_leave' => $this->faker->optional()->phoneNumber,
            'attachments' => null,
            'status' => 'pending',
            'approver_id' => null,
            'approved_at' => null,
            'approval_notes' => null,
            'rejection_reason' => null,
            'processor_id' => null,
            'processed_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approver_id' => null,
            'approved_at' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approver_id' => Employee::factory(),
            'approved_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'approval_notes' => $this->faker->optional()->sentence,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'approver_id' => Employee::factory(),
            'approved_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'rejection_reason' => $this->faker->sentence,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    public function halfDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_half_day' => true,
            'half_day_period' => $this->faker->randomElement(['morning', 'afternoon']),
            'end_date' => $attributes['start_date'],
            'total_days' => 1,
        ]);
    }

    public function withAttachments(): static
    {
        return $this->state(fn (array $attributes) => [
            'attachments' => [
                [
                    'name' => 'medical_certificate.pdf',
                    'path' => 'leave-requests/attachments/medical_certificate.pdf',
                    'size' => 1024,
                ],
                [
                    'name' => 'supporting_document.jpg',
                    'path' => 'leave-requests/attachments/supporting_document.jpg',
                    'size' => 2048,
                ],
            ],
        ]);
    }

    private function calculateWorkingDays($startDate, $endDate): int
    {
        $start = is_string($startDate) ? new \DateTime($startDate) : $startDate;
        $end = is_string($endDate) ? new \DateTime($endDate) : $endDate;

        $workingDays = 0;
        $current = clone $start;

        while ($current <= $end) {
            // Skip weekends (Friday = 5, Saturday = 6 in Saudi Arabia)
            if (! in_array($current->format('w'), [5, 6])) {
                $workingDays++;
            }
            $current->add(new \DateInterval('P1D'));
        }

        return $workingDays;
    }
}
