<?php

namespace App\Policies;

use App\Models\User;
use App\Modules\HR\Models\Employee;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // TODO: refine with real permissions/roles
    }

    public function view(User $user, Employee $employee): bool
    {
        return true; // TODO: refine
    }

    public function create(User $user): bool
    {
        return true; // TODO: refine
    }

    public function update(User $user, Employee $employee): bool
    {
        return true; // TODO: refine
    }

    public function delete(User $user, Employee $employee): bool
    {
        return true; // TODO: refine
    }
}
