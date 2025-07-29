<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Models\TaxRate;
use Illuminate\Http\Request;

class TaxRateController extends Controller
{
    public function index()
    {
        $taxRates = TaxRate::paginate(15);
        return view('modules.accounting.tax-rates.index', compact('taxRates'));
    }

    public function create()
    {
        return view('modules.accounting.tax-rates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:percentage,fixed',
            'is_active' => 'boolean',
        ]);

        TaxRate::create($validated);

        return redirect()->route('modules.accounting.tax-rates.index')
            ->with('success', 'Tax rate created successfully.');
    }

    public function show(TaxRate $taxRate)
    {
        return view('modules.accounting.tax-rates.show', compact('taxRate'));
    }

    public function edit(TaxRate $taxRate)
    {
        return view('modules.accounting.tax-rates.edit', compact('taxRate'));
    }

    public function update(Request $request, TaxRate $taxRate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:percentage,fixed',
            'is_active' => 'boolean',
        ]);

        $taxRate->update($validated);

        return redirect()->route('modules.accounting.tax-rates.index')
            ->with('success', 'Tax rate updated successfully.');
    }

    public function destroy(TaxRate $taxRate)
    {
        $taxRate->delete();

        return redirect()->route('modules.accounting.tax-rates.index')
            ->with('success', 'Tax rate deleted successfully.');
    }

    public function getByLocation(Request $request)
    {
        $location = $request->get('location');
        $taxRates = TaxRate::where('location', $location)->where('is_active', true)->get();
        
        return response()->json($taxRates);
    }
}
