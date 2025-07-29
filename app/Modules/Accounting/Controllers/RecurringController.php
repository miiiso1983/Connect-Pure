<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Models\RecurringProfile;
use App\Modules\Accounting\Models\Customer;
use App\Modules\Accounting\Models\Vendor;
use App\Modules\Accounting\Models\Invoice;
use App\Modules\Accounting\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecurringController extends Controller
{
    public function index(Request $request)
    {
        $query = RecurringProfile::with(['customer', 'vendor']);

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('frequency')) {
            $query->where('frequency', $request->frequency);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('profile_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $profiles = $query->orderBy('next_run_date', 'asc')
                         ->paginate(20);

        $types = RecurringProfile::getTypes();
        $statuses = RecurringProfile::getStatuses();
        $frequencies = RecurringProfile::getFrequencies();

        // Summary statistics
        $summary = [
            'total_profiles' => RecurringProfile::count(),
            'active_profiles' => RecurringProfile::where('status', 'active')->count(),
            'due_for_processing' => RecurringProfile::dueForProcessing()->count(),
            'monthly_revenue' => RecurringProfile::where('type', 'invoice')
                                               ->where('status', 'active')
                                               ->where('frequency', 'monthly')
                                               ->sum('amount'),
        ];

        return view('modules.accounting.recurring.index', compact(
            'profiles', 'types', 'statuses', 'frequencies', 'summary'
        ));
    }

    public function create()
    {
        $customers = Customer::active()->orderBy('name')->get();
        $vendors = Vendor::active()->orderBy('name')->get();
        $types = RecurringProfile::getTypes();
        $frequencies = RecurringProfile::getFrequencies();

        return view('modules.accounting.recurring.create', compact(
            'customers', 'vendors', 'types', 'frequencies'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'profile_name' => 'required|string|max:255',
            'type' => 'required|in:invoice,expense,payment',
            'frequency' => 'required|in:weekly,bi_weekly,monthly,quarterly,semi_annually,annually',
            'interval' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'customer_id' => 'required_if:type,invoice|exists:accounting_customers,id',
            'vendor_id' => 'required_if:type,expense|exists:accounting_vendors,id',
        ]);

        DB::beginTransaction();
        try {
            $profile = RecurringProfile::create([
                'profile_name' => $request->profile_name,
                'type' => $request->type,
                'customer_id' => $request->customer_id,
                'vendor_id' => $request->vendor_id,
                'frequency' => $request->frequency,
                'interval' => $request->interval,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'max_occurrences' => $request->max_occurrences,
                'next_run_date' => $request->start_date,
                'status' => 'active',
                'currency' => $request->currency,
                'amount' => $request->amount,
                'description' => $request->description,
                'template_data' => $request->template_data ?? [],
                'auto_send' => $request->boolean('auto_send'),
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()->route('modules.accounting.recurring.show', $profile)
                           ->with('success', __('accounting.recurring_profile_created_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->withErrors(['error' => __('accounting.error_creating_recurring_profile')]);
        }
    }

    public function show(RecurringProfile $profile)
    {
        $profile->load(['customer', 'vendor', 'invoices', 'expenses']);
        
        // Get related records based on type
        $relatedRecords = collect();
        switch ($profile->type) {
            case 'invoice':
                $relatedRecords = $profile->invoices()->orderBy('created_at', 'desc')->get();
                break;
            case 'expense':
                $relatedRecords = Expense::where('is_recurring', true)
                                        ->where('vendor_id', $profile->vendor_id)
                                        ->orderBy('created_at', 'desc')
                                        ->get();
                break;
        }

        return view('modules.accounting.recurring.show', compact('profile', 'relatedRecords'));
    }

    public function edit(RecurringProfile $profile)
    {
        $customers = Customer::active()->orderBy('name')->get();
        $vendors = Vendor::active()->orderBy('name')->get();
        $types = RecurringProfile::getTypes();
        $frequencies = RecurringProfile::getFrequencies();

        return view('modules.accounting.recurring.edit', compact(
            'profile', 'customers', 'vendors', 'types', 'frequencies'
        ));
    }

    public function update(Request $request, RecurringProfile $profile)
    {
        $request->validate([
            'profile_name' => 'required|string|max:255',
            'frequency' => 'required|in:weekly,bi_weekly,monthly,quarterly,semi_annually,annually',
            'interval' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
        ]);

        $profile->update([
            'profile_name' => $request->profile_name,
            'frequency' => $request->frequency,
            'interval' => $request->interval,
            'end_date' => $request->end_date,
            'max_occurrences' => $request->max_occurrences,
            'currency' => $request->currency,
            'amount' => $request->amount,
            'description' => $request->description,
            'auto_send' => $request->boolean('auto_send'),
            'notes' => $request->notes,
        ]);

        // Recalculate next run date if frequency or interval changed
        if ($request->hasAny(['frequency', 'interval'])) {
            $profile->calculateNextRunDate();
        }

        return redirect()->route('modules.accounting.recurring.show', $profile)
                       ->with('success', __('accounting.recurring_profile_updated_successfully'));
    }

    public function destroy(RecurringProfile $profile)
    {
        $profile->cancel();
        
        return redirect()->route('modules.accounting.recurring.index')
                       ->with('success', __('accounting.recurring_profile_cancelled_successfully'));
    }

    public function pause(RecurringProfile $profile)
    {
        $profile->pause();
        
        return response()->json([
            'success' => true,
            'message' => __('accounting.recurring_profile_paused_successfully'),
            'status' => $profile->status
        ]);
    }

    public function resume(RecurringProfile $profile)
    {
        $profile->resume();
        
        return response()->json([
            'success' => true,
            'message' => __('accounting.recurring_profile_resumed_successfully'),
            'status' => $profile->status
        ]);
    }

    public function processNow(RecurringProfile $profile)
    {
        if (!$profile->shouldProcess()) {
            return response()->json([
                'success' => false,
                'message' => __('accounting.recurring_profile_not_ready_for_processing')
            ]);
        }

        $created = $profile->process();

        if ($created) {
            return response()->json([
                'success' => true,
                'message' => __('accounting.recurring_profile_processed_successfully'),
                'created_type' => $profile->type,
                'created_id' => $created->id
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('accounting.error_processing_recurring_profile')
        ]);
    }

    public function processDue()
    {
        $profiles = RecurringProfile::dueForProcessing()->get();
        $processed = 0;
        $errors = 0;

        foreach ($profiles as $profile) {
            try {
                $created = $profile->process();
                if ($created) {
                    $processed++;
                }
            } catch (\Exception $e) {
                $errors++;
                \Log::error('Error processing recurring profile: ' . $e->getMessage(), [
                    'profile_id' => $profile->id,
                    'profile_name' => $profile->profile_name
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => __('accounting.processed_x_profiles', ['count' => $processed]),
            'processed' => $processed,
            'errors' => $errors,
            'total' => $profiles->count()
        ]);
    }

    public function getDashboardData()
    {
        $data = [
            'due_today' => RecurringProfile::where('next_run_date', today())->count(),
            'due_this_week' => RecurringProfile::whereBetween('next_run_date', [
                today(), 
                today()->addWeek()
            ])->count(),
            'active_profiles' => RecurringProfile::where('status', 'active')->count(),
            'monthly_revenue' => RecurringProfile::where('type', 'invoice')
                                               ->where('status', 'active')
                                               ->where('frequency', 'monthly')
                                               ->sum('amount'),
        ];

        return response()->json($data);
    }
}
