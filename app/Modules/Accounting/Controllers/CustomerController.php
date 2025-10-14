<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $customers = $query->orderBy('name')->paginate(20);

        // Summary statistics
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('is_active', true)->count();
        $totalBalance = Customer::sum('balance');

        $summary = [
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'total_balance' => $totalBalance,
        ];

        return view('modules.accounting.customers.index', compact('customers', 'summary'));
    }

    public function create()
    {
        return view('modules.accounting.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:accounting_customers,email',
            'phone' => 'nullable|string|max:20',
            'currency' => 'required|string|size:3',
            'payment_terms' => 'required|in:net_15,net_30,net_45,net_60,due_on_receipt',
        ]);

        DB::beginTransaction();
        try {
            $customer = Customer::create([
                'name' => $request->name,
                'company_name' => $request->company_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'website' => $request->website,
                'billing_address' => $request->billing_address,
                'shipping_address' => $request->shipping_address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country ?? 'US',
                'tax_number' => $request->tax_number,
                'currency' => $request->currency,
                'payment_terms' => $request->payment_terms,
                'credit_limit' => $request->credit_limit,
                'notes' => $request->notes,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            return redirect()->route('modules.accounting.customers.show', $customer)
                ->with('success', __('accounting.customer_created_successfully'));

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withInput()
                ->withErrors(['error' => __('accounting.error_creating_customer')]);
        }
    }

    public function show(Customer $customer)
    {
        $customer->load(['invoices', 'payments']);

        // Calculate customer statistics
        $totalInvoiced = $customer->getTotalInvoiced();
        $totalPaid = $customer->getTotalPaid();
        $overdueInvoices = $customer->getOverdueInvoices();

        $stats = [
            'total_invoiced' => $totalInvoiced,
            'total_paid' => $totalPaid,
            'current_balance' => $customer->balance,
            'overdue_amount' => $overdueInvoices->sum('balance_due'),
            'overdue_count' => $overdueInvoices->count(),
        ];

        return view('modules.accounting.customers.show', compact('customer', 'stats'));
    }

    public function edit(Customer $customer)
    {
        return view('modules.accounting.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:accounting_customers,email,'.$customer->id,
            'phone' => 'nullable|string|max:20',
            'currency' => 'required|string|size:3',
            'payment_terms' => 'required|in:net_15,net_30,net_45,net_60,due_on_receipt',
        ]);

        DB::beginTransaction();
        try {
            $customer->update([
                'name' => $request->name,
                'company_name' => $request->company_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'website' => $request->website,
                'billing_address' => $request->billing_address,
                'shipping_address' => $request->shipping_address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country ?? 'US',
                'tax_number' => $request->tax_number,
                'currency' => $request->currency,
                'payment_terms' => $request->payment_terms,
                'credit_limit' => $request->credit_limit,
                'notes' => $request->notes,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            return redirect()->route('modules.accounting.customers.show', $customer)
                ->with('success', __('accounting.customer_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withInput()
                ->withErrors(['error' => __('accounting.error_updating_customer')]);
        }
    }

    public function destroy(Customer $customer)
    {
        // Check if customer has invoices
        if ($customer->invoices()->count() > 0) {
            return redirect()->route('modules.accounting.customers.index')
                ->withErrors(['error' => __('accounting.cannot_delete_customer_with_invoices')]);
        }

        DB::beginTransaction();
        try {
            $customer->delete();

            DB::commit();

            return redirect()->route('modules.accounting.customers.index')
                ->with('success', __('accounting.customer_deleted_successfully'));

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->route('modules.accounting.customers.index')
                ->withErrors(['error' => __('accounting.error_deleting_customer')]);
        }
    }

    public function activate(Customer $customer)
    {
        $customer->update(['is_active' => true]);

        return redirect()->route('modules.accounting.customers.show', $customer)
            ->with('success', __('accounting.customer_activated_successfully'));
    }

    public function deactivate(Customer $customer)
    {
        $customer->update(['is_active' => false]);

        return redirect()->route('modules.accounting.customers.show', $customer)
            ->with('success', __('accounting.customer_deactivated_successfully'));
    }

    public function bulkUpload()
    {
        return view('modules.accounting.customers.bulk-upload');
    }

    public function processBulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            $header = array_shift($rows);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed header and arrays are 0-indexed

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    $customerData = [
                        'name' => $row[0] ?? '',
                        'company_name' => $row[1] ?? null,
                        'email' => $row[2] ?? null,
                        'phone' => $row[3] ?? null,
                        'website' => $row[4] ?? null,
                        'billing_address' => $row[5] ?? null,
                        'shipping_address' => $row[6] ?? null,
                        'city' => $row[7] ?? null,
                        'state' => $row[8] ?? null,
                        'postal_code' => $row[9] ?? null,
                        'country' => $row[10] ?? null,
                        'tax_number' => $row[11] ?? null,
                        'currency' => $row[12] ?? 'USD',
                        'payment_terms' => $row[13] ?? 'net_30',
                        'credit_limit' => is_numeric($row[14] ?? 0) ? $row[14] : 0,
                        'is_active' => ! empty($row[15]) ? (strtolower($row[15]) === 'yes' || $row[15] === '1') : true,
                        'notes' => $row[16] ?? null,
                    ];

                    // Validate required fields
                    $validator = Validator::make($customerData, [
                        'name' => 'required|string|max:255',
                        'email' => 'nullable|email|unique:accounting_customers,email',
                        'phone' => 'nullable|string|max:20',
                        'currency' => 'required|string|size:3',
                        'payment_terms' => 'required|in:net_15,net_30,net_45,net_60,due_on_receipt',
                        'credit_limit' => 'nullable|numeric|min:0',
                    ]);

                    if ($validator->fails()) {
                        $errors[] = "Row {$rowNumber}: ".implode(', ', $validator->errors()->all());
                        $errorCount++;

                        continue;
                    }

                    Customer::create($customerData);
                    $successCount++;

                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: ".$e->getMessage();
                    $errorCount++;
                }
            }

            DB::commit();

            $message = "Bulk upload completed. {$successCount} customers imported successfully.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} rows had errors.";
            }

            return redirect()->route('modules.accounting.customers.index')
                ->with('success', $message)
                ->with('upload_errors', $errors);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error processing file: '.$e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'Name*', 'Company Name', 'Email', 'Phone', 'Website',
            'Billing Address', 'Shipping Address', 'City', 'State', 'Postal Code',
            'Country', 'Tax Number', 'Currency*', 'Payment Terms*', 'Credit Limit',
            'Active (Yes/No)', 'Notes',
        ];

        $sheet->fromArray($headers, null, 'A1');

        // Add sample data
        $sampleData = [
            [
                'John Doe', 'Doe Enterprises', 'john@example.com', '+1234567890', 'https://example.com',
                '123 Main St', '123 Main St', 'New York', 'NY', '10001',
                'USA', 'TAX123456', 'USD', 'net_30', '5000',
                'Yes', 'Sample customer',
            ],
        ];

        $sheet->fromArray($sampleData, null, 'A2');

        // Auto-size columns
        foreach (range('A', 'Q') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $filename = 'customers_template.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
