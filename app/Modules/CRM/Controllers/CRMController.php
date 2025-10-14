<?php

namespace App\Modules\CRM\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CRM\Exports\ContactTemplateExport;
use App\Modules\CRM\Imports\ContactsImport;
use App\Modules\CRM\Models\Communication;
use App\Modules\CRM\Models\Contact;
use App\Modules\CRM\Models\FollowUp;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CRMController extends Controller
{
    public function index()
    {
        $stats = $this->getDashboardStats();
        $recentContacts = Contact::with(['communications', 'followUps'])
            ->latest()
            ->limit(5)
            ->get();

        $funnelData = $this->getFunnelData();

        return view('modules.crm.index', compact('stats', 'recentContacts', 'funnelData'));
    }

    public function dashboard()
    {
        return $this->index();
    }

    public function contacts(Request $request)
    {
        $query = Contact::with(['communications', 'pendingFollowUps']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $contacts = $query->paginate(15);

        return view('modules.crm.contacts.index', compact('contacts'));
    }

    public function createContact()
    {
        return view('modules.crm.contacts.create');
    }

    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'type' => 'required|in:lead,client',
            'status' => 'required|in:new,contacted,qualified,proposal,negotiation,closed_won,closed_lost',
            'notes' => 'nullable|string',
            'next_follow_up' => 'nullable|date',
            'potential_value' => 'nullable|numeric|min:0',
            'source' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|string|max:255',
        ]);

        $contact = Contact::create($validated);

        return redirect()->route('modules.crm.contacts.show', $contact)
            ->with('success', __('erp.success'));
    }

    public function showContact(Contact $contact)
    {
        $contact->load(['communications', 'followUps']);

        return view('modules.crm.contacts.show', compact('contact'));
    }

    public function editContact(Contact $contact)
    {
        return view('modules.crm.contacts.edit', compact('contact'));
    }

    public function updateContact(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'type' => 'required|in:lead,client',
            'status' => 'required|in:new,contacted,qualified,proposal,negotiation,closed_won,closed_lost',
            'notes' => 'nullable|string',
            'next_follow_up' => 'nullable|date',
            'potential_value' => 'nullable|numeric|min:0',
            'source' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|string|max:255',
        ]);

        $contact->update($validated);

        return redirect()->route('modules.crm.contacts.show', $contact)
            ->with('success', __('erp.success'));
    }

    public function destroyContact(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('modules.crm.contacts.index')
            ->with('success', __('erp.success'));
    }

    public function showBulkUpload()
    {
        return view('modules.crm.contacts.bulk-upload');
    }

    public function processBulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            $import = new ContactsImport;
            Excel::import($import, $request->file('file'));

            $importedCount = $import->getImportedCount();
            $skippedCount = 0; // Default value
            $errorCount = count($import->failures());

            $message = __('erp.bulk_upload_success', [
                'imported' => $importedCount,
                'skipped' => $skippedCount,
                'errors' => $errorCount,
            ]);

            if ($errorCount > 0) {
                return redirect()->route('modules.crm.contacts.bulk-upload')
                    ->with('warning', $message)
                    ->with('failures', $import->failures());
            }

            return redirect()->route('modules.crm.contacts.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('modules.crm.contacts.bulk-upload')
                ->with('error', __('erp.bulk_upload_error').': '.$e->getMessage());
        }
    }

    public function storeCommunication(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'type' => 'required|in:call,email,meeting,note,sms',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'communication_date' => 'required|date',
        ]);

        $validated['contact_id'] = $contact->id;
        $validated['created_by'] = 'Current User'; // Replace with actual user

        Communication::create($validated);

        return redirect()->route('modules.crm.contacts.show', $contact)
            ->with('success', __('erp.success'));
    }

    public function storeFollowUp(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_date' => 'required|date',
            'priority' => 'required|in:low,medium,high',
        ]);

        $validated['contact_id'] = $contact->id;
        $validated['assigned_to'] = 'Current User'; // Replace with actual user

        FollowUp::create($validated);

        return redirect()->route('modules.crm.contacts.show', $contact)
            ->with('success', __('erp.success'));
    }

    public function completeFollowUp(Request $request, FollowUp $followUp)
    {
        $validated = $request->validate([
            'completion_notes' => 'nullable|string',
        ]);

        $followUp->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completion_notes' => $validated['completion_notes'] ?? null,
        ]);

        return redirect()->back()->with('success', __('erp.success'));
    }

    public function followUpReminders()
    {
        $overdueFollowUps = FollowUp::with('contact')
            ->overdue()
            ->get();

        $todayFollowUps = FollowUp::with('contact')
            ->dueToday()
            ->get();

        $upcomingFollowUps = FollowUp::with('contact')
            ->pending()
            ->where('due_date', '>', today())
            ->where('due_date', '<=', today()->addDays(7))
            ->orderBy('due_date')
            ->get();

        return view('modules.crm.follow-ups.index', compact(
            'overdueFollowUps',
            'todayFollowUps',
            'upcomingFollowUps'
        ));
    }

    public function salesFunnel()
    {
        $funnelData = $this->getFunnelData();
        $conversionRates = $this->getConversionRates();

        return view('modules.crm.funnel', compact('funnelData', 'conversionRates'));
    }

    private function getDashboardStats()
    {
        return [
            'total_contacts' => Contact::count(),
            'total_leads' => Contact::where('type', 'lead')->count(),
            'total_clients' => Contact::where('type', 'client')->count(),
            'deals_closed' => Contact::where('status', 'closed_won')->count(),
            'pending_follow_ups' => FollowUp::pending()->count(),
            'overdue_follow_ups' => FollowUp::overdue()->count(),
            'total_value' => Contact::where('status', 'closed_won')->sum('potential_value'),
            'pipeline_value' => Contact::whereIn('status', ['qualified', 'proposal', 'negotiation'])->sum('potential_value'),
        ];
    }

    private function getFunnelData()
    {
        return [
            'new' => Contact::where('status', 'new')->count(),
            'contacted' => Contact::where('status', 'contacted')->count(),
            'qualified' => Contact::where('status', 'qualified')->count(),
            'proposal' => Contact::where('status', 'proposal')->count(),
            'negotiation' => Contact::where('status', 'negotiation')->count(),
            'closed_won' => Contact::where('status', 'closed_won')->count(),
            'closed_lost' => Contact::where('status', 'closed_lost')->count(),
        ];
    }

    private function getConversionRates()
    {
        $total = Contact::count();
        if ($total === 0) {
            return [];
        }

        $funnel = $this->getFunnelData();

        return [
            'contacted_rate' => $total > 0 ? round(($funnel['contacted'] / $total) * 100, 1) : 0,
            'qualified_rate' => $funnel['contacted'] > 0 ? round(($funnel['qualified'] / $funnel['contacted']) * 100, 1) : 0,
            'proposal_rate' => $funnel['qualified'] > 0 ? round(($funnel['proposal'] / $funnel['qualified']) * 100, 1) : 0,
            'negotiation_rate' => $funnel['proposal'] > 0 ? round(($funnel['negotiation'] / $funnel['proposal']) * 100, 1) : 0,
            'won_rate' => $funnel['negotiation'] > 0 ? round(($funnel['closed_won'] / $funnel['negotiation']) * 100, 1) : 0,
        ];
    }

    /**
     * Download Excel template for bulk contact upload.
     */
    public function downloadTemplate()
    {
        return Excel::download(new ContactTemplateExport, 'contact_import_template.xlsx');
    }
}
