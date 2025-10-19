<?php

namespace App\Policies;

use App\Models\User;
use App\Modules\Accounting\Models\Invoice;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return true;
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return true;
    }
}
