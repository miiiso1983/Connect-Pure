<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Models\ChartOfAccount;
use App\Modules\Accounting\Models\JournalEntry;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function index()
    {
        $journalEntries = JournalEntry::with('account')->paginate(15);

        return view('modules.accounting.journal-entries.index', compact('journalEntries'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::where('is_active', true)->get();

        return view('modules.accounting.journal-entries.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'reference_number' => 'required|string',
            'description' => 'required|string',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:chart_of_accounts,id',
            'entries.*.debit_amount' => 'nullable|numeric|min:0',
            'entries.*.credit_amount' => 'nullable|numeric|min:0',
            'entries.*.description' => 'nullable|string',
        ]);

        $journalEntry = JournalEntry::create($validated);

        return redirect()->route('modules.accounting.journal-entries.index')
            ->with('success', 'Journal entry created successfully.');
    }

    public function show(JournalEntry $journalEntry)
    {
        return view('modules.accounting.journal-entries.show', compact('journalEntry'));
    }

    public function edit(JournalEntry $journalEntry)
    {
        $accounts = ChartOfAccount::where('is_active', true)->get();

        return view('modules.accounting.journal-entries.edit', compact('journalEntry', 'accounts'));
    }

    public function update(Request $request, JournalEntry $journalEntry)
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'reference_number' => 'required|string',
            'description' => 'required|string',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:chart_of_accounts,id',
            'entries.*.debit_amount' => 'nullable|numeric|min:0',
            'entries.*.credit_amount' => 'nullable|numeric|min:0',
            'entries.*.description' => 'nullable|string',
        ]);

        $journalEntry->update($validated);

        return redirect()->route('modules.accounting.journal-entries.index')
            ->with('success', 'Journal entry updated successfully.');
    }

    public function destroy(JournalEntry $journalEntry)
    {
        $journalEntry->delete();

        return redirect()->route('modules.accounting.journal-entries.index')
            ->with('success', 'Journal entry deleted successfully.');
    }

    public function post(JournalEntry $journalEntry)
    {
        $journalEntry->update(['status' => 'posted']);

        return redirect()->route('modules.accounting.journal-entries.show', $journalEntry)
            ->with('success', 'Journal entry posted successfully.');
    }

    public function reverse(JournalEntry $journalEntry)
    {
        $journalEntry->update(['status' => 'reversed']);

        return redirect()->route('modules.accounting.journal-entries.show', $journalEntry)
            ->with('success', 'Journal entry reversed successfully.');
    }
}
