<?php

namespace App\Modules\HR\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: integrate with policies in a later step
        return true;
    }

    public function rules(): array
    {
        $employee = $this->route('employee');

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'first_name_ar' => 'nullable|string|max:255',
            'last_name_ar' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('hr_employees', 'email')->ignore($employee?->id),
            ],
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'nationality' => 'nullable|string|max:100',
            'national_id' => 'nullable|string|max:50',
            'passport_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'address_ar' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'department_id' => 'required|exists:hr_departments,id',
            'role_id' => 'required|exists:hr_roles,id',
            'manager_id' => 'nullable|exists:hr_employees,id',
            'hire_date' => 'required|date',
            'probation_end_date' => 'nullable|date|after:hire_date',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'status' => 'required|in:active,inactive,terminated,resigned',
            'termination_date' => 'nullable|date|after:hire_date',
            'termination_reason' => 'nullable|string',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|array',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'iban' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'profile_photo' => 'nullable|image|max:2048',
            'annual_leave_balance' => 'required|integer|min:0',
            'sick_leave_balance' => 'required|integer|min:0',
            'emergency_leave_balance' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ];
    }
}
