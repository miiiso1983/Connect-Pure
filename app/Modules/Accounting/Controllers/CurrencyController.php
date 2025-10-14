<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        $query = Currency::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('symbol', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $currencies = $query->orderBy('name')->paginate(20);

        $summary = [
            'total_currencies' => Currency::count(),
            'active_currencies' => Currency::active()->count(),
            'base_currency' => Currency::baseCurrency()->first(),
            'last_updated' => Currency::latest('updated_at')->first()?->updated_at,
        ];

        return view('modules.accounting.currencies.index', compact('currencies', 'summary'));
    }

    public function create()
    {
        return view('modules.accounting.currencies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:3|unique:accounting_currencies,code',
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.000001|max:999999.999999',
            'decimal_places' => 'required|integer|between:0,6',
            'symbol_position' => 'required|in:before,after',
            'thousands_separator' => 'required|string|size:1',
            'decimal_separator' => 'required|string|size:1',
            'is_active' => 'boolean',
            'is_base_currency' => 'boolean',
        ]);

        $currency = Currency::create($request->all());

        return redirect()->route('modules.accounting.currencies.index')
            ->with('success', __('accounting.currency_created_successfully'));
    }

    public function show(Currency $currency)
    {
        $currency->load(['invoices', 'expenses']);

        // Get usage statistics
        $stats = [
            'invoices_count' => $currency->invoices()->count(),
            'expenses_count' => $currency->expenses()->count(),
            'total_invoice_amount' => $currency->invoices()->sum('total_amount'),
            'total_expense_amount' => $currency->expenses()->sum('amount'),
        ];

        return view('modules.accounting.currencies.show', compact('currency', 'stats'));
    }

    public function edit(Currency $currency)
    {
        return view('modules.accounting.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'code' => 'required|string|size:3|unique:accounting_currencies,code,'.$currency->id,
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.000001|max:999999.999999',
            'decimal_places' => 'required|integer|between:0,6',
            'symbol_position' => 'required|in:before,after',
            'thousands_separator' => 'required|string|size:1',
            'decimal_separator' => 'required|string|size:1',
            'is_active' => 'boolean',
            'is_base_currency' => 'boolean',
        ]);

        $currency->update($request->all());

        return redirect()->route('modules.accounting.currencies.index')
            ->with('success', __('accounting.currency_updated_successfully'));
    }

    public function destroy(Currency $currency)
    {
        // Check if currency is in use
        if ($currency->invoices()->exists() || $currency->expenses()->exists()) {
            return back()->with('error', __('accounting.currency_in_use_cannot_delete'));
        }

        // Prevent deletion of base currency
        if ($currency->is_base_currency) {
            return back()->with('error', __('accounting.cannot_delete_base_currency'));
        }

        $currency->delete();

        return redirect()->route('modules.accounting.currencies.index')
            ->with('success', __('accounting.currency_deleted_successfully'));
    }

    public function updateExchangeRates(Request $request)
    {
        try {
            // Get exchange rates from external API (example using exchangerate-api.com)
            $baseCurrency = Currency::baseCurrency()->first();

            if (! $baseCurrency) {
                return response()->json([
                    'success' => false,
                    'message' => __('accounting.no_base_currency_set'),
                ]);
            }

            // This is a mock implementation - replace with actual API
            $response = Http::get("https://api.exchangerate-api.com/v4/latest/{$baseCurrency->code}");

            if ($response->successful()) {
                $rates = $response->json()['rates'];

                $updated = 0;
                foreach (Currency::where('is_base_currency', false)->get() as $currency) {
                    if (isset($rates[$currency->code])) {
                        $currency->update(['exchange_rate' => $rates[$currency->code]]);
                        $updated++;
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => __('accounting.exchange_rates_updated', ['count' => $updated]),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('accounting.failed_to_fetch_exchange_rates'),
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('accounting.error_updating_exchange_rates'),
            ]);
        }
    }

    public function setBaseCurrency(Currency $currency)
    {
        // Update all currencies to not be base currency
        Currency::where('is_base_currency', true)->update(['is_base_currency' => false]);

        // Set this currency as base currency with exchange rate 1
        $currency->update([
            'is_base_currency' => true,
            'exchange_rate' => 1.000000,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('accounting.base_currency_updated'),
        ]);
    }

    public function toggleStatus(Currency $currency)
    {
        // Prevent deactivating base currency
        if ($currency->is_base_currency && $currency->is_active) {
            return response()->json([
                'success' => false,
                'message' => __('accounting.cannot_deactivate_base_currency'),
            ]);
        }

        $currency->update(['is_active' => ! $currency->is_active]);

        return response()->json([
            'success' => true,
            'message' => $currency->is_active
                ? __('accounting.currency_activated')
                : __('accounting.currency_deactivated'),
        ]);
    }

    public function convert(Request $request)
    {
        $request->validate([
            'from_currency' => 'required|exists:accounting_currencies,code',
            'to_currency' => 'required|exists:accounting_currencies,code',
            'amount' => 'required|numeric|min:0',
        ]);

        $fromCurrency = Currency::where('code', $request->from_currency)->first();
        $toCurrency = Currency::where('code', $request->to_currency)->first();

        $convertedAmount = $fromCurrency->convertTo($toCurrency, $request->amount);

        return response()->json([
            'success' => true,
            'converted_amount' => $convertedAmount,
            'formatted_amount' => $toCurrency->formatAmount($convertedAmount),
            'exchange_rate' => $fromCurrency->exchange_rate / $toCurrency->exchange_rate,
        ]);
    }

    public function exportRates()
    {
        $currencies = Currency::active()->get();

        $filename = 'exchange_rates_'.now()->format('Y_m_d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($currencies) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Currency Code', 'Currency Name', 'Symbol', 'Exchange Rate',
                'Is Base Currency', 'Is Active', 'Last Updated',
            ]);

            // CSV data
            foreach ($currencies as $currency) {
                fputcsv($file, [
                    $currency->code,
                    $currency->name,
                    $currency->symbol,
                    $currency->exchange_rate,
                    $currency->is_base_currency ? 'Yes' : 'No',
                    $currency->is_active ? 'Active' : 'Inactive',
                    $currency->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
