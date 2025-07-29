<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index(Request $request)
    {
        $query = Tax::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('country_code')) {
            $query->where('country_code', $request->country_code);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $taxes = $query->orderBy('name')->paginate(20);

        $summary = [
            'total_taxes' => Tax::count(),
            'active_taxes' => Tax::active()->count(),
            'default_tax' => Tax::default()->first(),
            'tax_types' => Tax::distinct('type')->pluck('type')->count(),
        ];

        $taxTypes = Tax::getTaxTypes();
        $countries = Tax::distinct('country_code')->whereNotNull('country_code')->pluck('country_code');

        return view('modules.accounting.taxes.index', compact('taxes', 'summary', 'taxTypes', 'countries'));
    }

    public function create()
    {
        $taxTypes = Tax::getTaxTypes();
        $calculationMethods = Tax::getCalculationMethods();
        $appliesTo = Tax::getAppliesTo();

        return view('modules.accounting.taxes.create', compact('taxTypes', 'calculationMethods', 'appliesTo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:accounting_taxes,code',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:' . implode(',', array_keys(Tax::getTaxTypes())),
            'description' => 'nullable|string',
            'country_code' => 'nullable|string|size:2',
            'region' => 'nullable|string|max:255',
            'applies_to' => 'nullable|array',
            'applies_to.*' => 'in:' . implode(',', array_keys(Tax::getAppliesTo())),
            'calculation_method' => 'required|in:' . implode(',', array_keys(Tax::getCalculationMethods())),
            'compound_tax' => 'boolean',
            'inclusive' => 'boolean',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $tax = Tax::create($request->all());

        return redirect()->route('modules.accounting.taxes.index')
                       ->with('success', __('accounting.tax_created_successfully'));
    }

    public function show(Tax $tax)
    {
        $tax->load(['invoices', 'expenses']);
        
        // Get usage statistics
        $stats = [
            'invoices_count' => $tax->invoices()->count(),
            'expenses_count' => $tax->expenses()->count(),
            'total_tax_collected' => $tax->invoices()->sum('pivot.tax_amount'),
            'total_tax_paid' => $tax->expenses()->sum('pivot.tax_amount'),
        ];

        return view('modules.accounting.taxes.show', compact('tax', 'stats'));
    }

    public function edit(Tax $tax)
    {
        $taxTypes = Tax::getTaxTypes();
        $calculationMethods = Tax::getCalculationMethods();
        $appliesTo = Tax::getAppliesTo();

        return view('modules.accounting.taxes.edit', compact('tax', 'taxTypes', 'calculationMethods', 'appliesTo'));
    }

    public function update(Request $request, Tax $tax)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:accounting_taxes,code,' . $tax->id,
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:' . implode(',', array_keys(Tax::getTaxTypes())),
            'description' => 'nullable|string',
            'country_code' => 'nullable|string|size:2',
            'region' => 'nullable|string|max:255',
            'applies_to' => 'nullable|array',
            'applies_to.*' => 'in:' . implode(',', array_keys(Tax::getAppliesTo())),
            'calculation_method' => 'required|in:' . implode(',', array_keys(Tax::getCalculationMethods())),
            'compound_tax' => 'boolean',
            'inclusive' => 'boolean',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $tax->update($request->all());

        return redirect()->route('modules.accounting.taxes.index')
                       ->with('success', __('accounting.tax_updated_successfully'));
    }

    public function destroy(Tax $tax)
    {
        // Check if tax is in use
        if ($tax->invoices()->exists() || $tax->expenses()->exists()) {
            return back()->with('error', __('accounting.tax_in_use_cannot_delete'));
        }

        $tax->delete();

        return redirect()->route('modules.accounting.taxes.index')
                       ->with('success', __('accounting.tax_deleted_successfully'));
    }

    public function setDefault(Tax $tax)
    {
        // Update all taxes to not be default
        Tax::where('is_default', true)->update(['is_default' => false]);
        
        // Set this tax as default
        $tax->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => __('accounting.default_tax_updated')
        ]);
    }

    public function toggleStatus(Tax $tax)
    {
        $tax->update(['is_active' => !$tax->is_active]);

        return response()->json([
            'success' => true,
            'message' => $tax->is_active 
                ? __('accounting.tax_activated') 
                : __('accounting.tax_deactivated')
        ]);
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'tax_id' => 'required|exists:accounting_taxes,id',
            'amount' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $tax = Tax::findOrFail($request->tax_id);
        $quantity = $request->quantity ?? 1;
        
        $taxAmount = $tax->calculateTax($request->amount, $quantity);
        $totalAmount = $request->amount * $quantity;
        
        if ($tax->inclusive) {
            $netAmount = $totalAmount - $taxAmount;
        } else {
            $netAmount = $totalAmount;
            $totalAmount += $taxAmount;
        }

        return response()->json([
            'success' => true,
            'tax_amount' => round($taxAmount, 2),
            'net_amount' => round($netAmount, 2),
            'total_amount' => round($totalAmount, 2),
            'tax_rate' => $tax->rate,
            'tax_name' => $tax->name,
            'is_inclusive' => $tax->inclusive,
        ]);
    }

    public function getByCountry(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string|size:2',
        ]);

        $taxes = Tax::active()
                   ->effective()
                   ->byCountry($request->country_code)
                   ->get(['id', 'name', 'code', 'rate', 'type']);

        return response()->json([
            'success' => true,
            'taxes' => $taxes
        ]);
    }

    public function duplicate(Tax $tax)
    {
        $newTax = $tax->replicate();
        $newTax->name = $tax->name . ' (Copy)';
        $newTax->code = $tax->code . '_COPY';
        $newTax->is_default = false;
        $newTax->save();

        return redirect()->route('modules.accounting.taxes.edit', $newTax)
                       ->with('success', __('accounting.tax_duplicated_successfully'));
    }

    public function export()
    {
        $taxes = Tax::with(['invoices', 'expenses'])->get();
        
        $filename = 'taxes_' . now()->format('Y_m_d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($taxes) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Tax Name', 'Tax Code', 'Rate (%)', 'Type', 'Country', 'Region',
                'Calculation Method', 'Is Active', 'Is Default', 'Effective Date',
                'Expiry Date', 'Invoices Count', 'Expenses Count'
            ]);

            // CSV data
            foreach ($taxes as $tax) {
                fputcsv($file, [
                    $tax->name,
                    $tax->code,
                    $tax->rate,
                    $tax->type_text,
                    $tax->country_code,
                    $tax->region,
                    $tax->calculation_method_text,
                    $tax->is_active ? 'Active' : 'Inactive',
                    $tax->is_default ? 'Yes' : 'No',
                    $tax->effective_date?->format('Y-m-d'),
                    $tax->expiry_date?->format('Y-m-d'),
                    $tax->invoices->count(),
                    $tax->expenses->count(),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
