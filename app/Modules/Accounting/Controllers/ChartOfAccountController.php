<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Models\ChartOfAccount;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ChartOfAccountController extends Controller
{
    public function index()
    {
        $accounts = ChartOfAccount::orderBy('account_code')->paginate(15);
        return view('modules.accounting.chart-of-accounts.index', compact('accounts'));
    }

    public function create()
    {
        $parentAccounts = ChartOfAccount::whereNull('parent_id')->get();
        return view('modules.accounting.chart-of-accounts.create', compact('parentAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_code' => 'required|string|unique:chart_of_accounts',
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        ChartOfAccount::create($validated);

        return redirect()->route('modules.accounting.chart-of-accounts.index')
            ->with('success', 'Account created successfully.');
    }

    public function show(ChartOfAccount $account)
    {
        $account->load(['parentAccount', 'subAccounts']);
        return view('modules.accounting.chart-of-accounts.show', compact('account'));
    }

    public function edit(ChartOfAccount $account)
    {
        $parentAccounts = ChartOfAccount::whereNull('parent_id')
            ->where('id', '!=', $account->id)
            ->get();

        return view('modules.accounting.chart-of-accounts.edit', compact('account', 'parentAccounts'));
    }

    public function update(Request $request, ChartOfAccount $account)
    {
        $validated = $request->validate([
            'account_code' => 'required|string|unique:chart_of_accounts,account_code,' . $account->id,
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $account->update($validated);

        return redirect()->route('modules.accounting.chart-of-accounts.index')
            ->with('success', 'Account updated successfully.');
    }

    public function destroy(ChartOfAccount $account)
    {
        $account->delete();

        return redirect()->route('modules.accounting.chart-of-accounts.index')
            ->with('success', 'Account deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $accounts = ChartOfAccount::where('account_name', 'like', "%{$query}%")
            ->orWhere('account_code', 'like', "%{$query}%")
            ->where('is_active', true)
            ->limit(10)
            ->get();

        return response()->json($accounts);
    }

    public function export()
    {
        $accounts = ChartOfAccount::with('parentAccount')
                                 ->orderBy('account_code')
                                 ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'Account Code', 'Account Name', 'Account Type', 'Normal Balance',
            'Parent Account', 'Current Balance', 'Opening Balance', 'Status', 'Description'
        ];

        $sheet->fromArray($headers, null, 'A1');

        // Add data
        $row = 2;
        foreach ($accounts as $account) {
            $data = [
                $account->account_code,
                $account->account_name,
                ucfirst($account->account_type),
                ucfirst($account->normal_balance),
                $account->parentAccount ? $account->parentAccount->account_name : '',
                number_format($account->current_balance, 2),
                number_format($account->opening_balance, 2),
                $account->is_active ? 'Active' : 'Inactive',
                $account->description ?? ''
            ];

            $sheet->fromArray($data, null, 'A' . $row);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Style headers
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle('A1:I1')->getFill()
              ->setFillType(Fill::FILL_SOLID)
              ->getStartColor()->setARGB('FFE2E8F0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $filename = 'chart_of_accounts_' . date('Y-m-d_H-i-s') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
