<?php

namespace App\Modules\Accounting\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $model = $this->route('invoice');

        return (bool) ($this->user()?->can('update', $model));
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:accounting_customers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'currency' => 'required|string|size:3',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'exchange_rate' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'po_number' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
        ];
    }
}
