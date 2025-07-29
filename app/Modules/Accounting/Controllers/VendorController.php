<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::query();

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

        $vendors = $query->orderBy('name')->paginate(20);

        // Summary statistics
        $totalVendors = Vendor::count();
        $activeVendors = Vendor::where('is_active', true)->count();
        $totalBalance = Vendor::sum('balance');

        $summary = [
            'total_vendors' => $totalVendors,
            'active_vendors' => $activeVendors,
            'total_balance' => $totalBalance,
        ];

        return view('modules.accounting.vendors.index', compact('vendors', 'summary'));
    }

    public function create()
    {
        return view('modules.accounting.vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:accounting_vendors,email',
            'phone' => 'nullable|string|max:20',
            'currency' => 'required|string|size:3',
            'payment_terms' => 'required|in:net_15,net_30,net_45,net_60,due_on_receipt',
        ]);

        DB::beginTransaction();
        try {
            $vendor = Vendor::create([
                'name' => $request->name,
                'company_name' => $request->company_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'website' => $request->website,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country ?? 'US',
                'tax_number' => $request->tax_number,
                'currency' => $request->currency,
                'payment_terms' => $request->payment_terms,
                'notes' => $request->notes,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            return redirect()->route('modules.accounting.vendors.show', $vendor)
                           ->with('success', __('accounting.vendor_created_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->withErrors(['error' => __('accounting.error_creating_vendor')]);
        }
    }

    public function show(Vendor $vendor)
    {
        $vendor->load(['expenses', 'payments']);
        
        // Calculate vendor statistics
        $totalExpenses = $vendor->getTotalExpenses();
        $totalPaid = $vendor->getTotalPaid();
        
        $stats = [
            'total_expenses' => $totalExpenses,
            'total_paid' => $totalPaid,
            'current_balance' => $vendor->balance,
        ];

        return view('modules.accounting.vendors.show', compact('vendor', 'stats'));
    }

    public function edit(Vendor $vendor)
    {
        return view('modules.accounting.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:accounting_vendors,email,' . $vendor->id,
            'phone' => 'nullable|string|max:20',
            'currency' => 'required|string|size:3',
            'payment_terms' => 'required|in:net_15,net_30,net_45,net_60,due_on_receipt',
        ]);

        DB::beginTransaction();
        try {
            $vendor->update([
                'name' => $request->name,
                'company_name' => $request->company_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'website' => $request->website,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country ?? 'US',
                'tax_number' => $request->tax_number,
                'currency' => $request->currency,
                'payment_terms' => $request->payment_terms,
                'notes' => $request->notes,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();

            return redirect()->route('modules.accounting.vendors.show', $vendor)
                           ->with('success', __('accounting.vendor_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->withErrors(['error' => __('accounting.error_updating_vendor')]);
        }
    }

    public function destroy(Vendor $vendor)
    {
        // Check if vendor has expenses
        if ($vendor->expenses()->count() > 0) {
            return redirect()->route('modules.accounting.vendors.index')
                           ->withErrors(['error' => __('accounting.cannot_delete_vendor_with_expenses')]);
        }

        DB::beginTransaction();
        try {
            $vendor->delete();
            
            DB::commit();

            return redirect()->route('modules.accounting.vendors.index')
                           ->with('success', __('accounting.vendor_deleted_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('modules.accounting.vendors.index')
                           ->withErrors(['error' => __('accounting.error_deleting_vendor')]);
        }
    }

    public function activate(Vendor $vendor)
    {
        $vendor->update(['is_active' => true]);

        return redirect()->route('modules.accounting.vendors.show', $vendor)
                       ->with('success', __('accounting.vendor_activated_successfully'));
    }

    public function deactivate(Vendor $vendor)
    {
        $vendor->update(['is_active' => false]);

        return redirect()->route('modules.accounting.vendors.show', $vendor)
                       ->with('success', __('accounting.vendor_deactivated_successfully'));
    }

    public function bulkUpload()
    {
        return view('modules.accounting.vendors.bulk-upload');
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
                    $vendorData = [
                        'name' => $row[0] ?? '',
                        'company_name' => $row[1] ?? null,
                        'email' => $row[2] ?? null,
                        'phone' => $row[3] ?? null,
                        'website' => $row[4] ?? null,
                        'address' => $row[5] ?? null,
                        'city' => $row[6] ?? null,
                        'state' => $row[7] ?? null,
                        'postal_code' => $row[8] ?? null,
                        'country' => $row[9] ?? null,
                        'tax_number' => $row[10] ?? null,
                        'currency' => $row[11] ?? 'USD',
                        'payment_terms' => $row[12] ?? 'net_30',
                        'is_active' => !empty($row[13]) ? (strtolower($row[13]) === 'yes' || $row[13] === '1') : true,
                        'notes' => $row[14] ?? null,
                    ];

                    // Validate required fields
                    $validator = Validator::make($vendorData, [
                        'name' => 'required|string|max:255',
                        'email' => 'nullable|email|unique:accounting_vendors,email',
                        'phone' => 'nullable|string|max:20',
                        'currency' => 'required|string|size:3',
                        'payment_terms' => 'required|in:net_15,net_30,net_45,net_60,due_on_receipt',
                    ]);

                    if ($validator->fails()) {
                        $errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                        $errorCount++;
                        continue;
                    }

                    Vendor::create($vendorData);
                    $successCount++;

                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                    $errorCount++;
                }
            }

            DB::commit();

            $message = "Bulk upload completed. {$successCount} vendors imported successfully.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} rows had errors.";
            }

            return redirect()->route('modules.accounting.vendors.index')
                           ->with('success', $message)
                           ->with('upload_errors', $errors);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error processing file: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'Name*', 'Company Name', 'Email', 'Phone', 'Website',
            'Address', 'City', 'State', 'Postal Code', 'Country',
            'Tax Number', 'Currency*', 'Payment Terms*', 'Active (Yes/No)', 'Notes'
        ];

        $sheet->fromArray($headers, null, 'A1');

        // Add sample data
        $sampleData = [
            [
                'ABC Supplies', 'ABC Supplies Inc.', 'contact@abcsupplies.com', '+1234567890', 'https://abcsupplies.com',
                '456 Business Ave', 'Los Angeles', 'CA', '90210', 'USA',
                'TAX789012', 'USD', 'net_30', 'Yes', 'Sample vendor'
            ]
        ];

        $sheet->fromArray($sampleData, null, 'A2');

        // Auto-size columns
        foreach (range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $filename = 'vendors_template.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
