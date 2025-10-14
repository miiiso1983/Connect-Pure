<?php

namespace App\Modules\Support\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Support\Models\Ticket;
use App\Modules\Support\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['comments', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total_tickets' => Ticket::count(),
            'open_tickets' => Ticket::whereIn('status', ['open', 'in_progress', 'pending'])->count(),
            'resolved_tickets' => Ticket::where('status', 'resolved')->count(),
            'urgent_tickets' => Ticket::where('priority', 'urgent')->open()->count(),
            'overdue_tickets' => Ticket::overdue()->count(),
        ];

        return view('modules.support.index', compact('tickets', 'stats'));
    }

    public function dashboard()
    {
        $recentTickets = Ticket::with(['comments', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            'total_tickets' => Ticket::count(),
            'open_tickets' => Ticket::whereIn('status', ['open', 'in_progress', 'pending'])->count(),
            'resolved_today' => Ticket::where('status', 'resolved')
                ->whereDate('resolved_at', today())
                ->count(),
            'avg_response_time' => $this->calculateAverageResponseTime(),
        ];

        $categoryStats = Ticket::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category => $item->count];
            });

        return view('modules.support.dashboard', compact('recentTickets', 'stats', 'categoryStats'));
    }

    public function tickets(Request $request)
    {
        $query = Ticket::with(['comments', 'attachments']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ticket_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('modules.support.tickets.index', compact('tickets'));
    }

    public function createTicket()
    {
        return view('modules.support.tickets.create');
    }

    public function storeTicket(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|in:technical,billing,general,feature_request,bug_report',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'assigned_to' => 'nullable|string|max:255',
            'due_date' => 'nullable|date|after:today',
            'tags' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        // Process tags
        if ($validated['tags']) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        $validated['created_by'] = 'System'; // In real app, use auth()->user()->name

        $ticket = Ticket::create($validated);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $this->storeAttachment($ticket, $file, 'System');
            }
        }

        return redirect()->route('modules.support.tickets.show', $ticket)
            ->with('success', __('erp.ticket_created_successfully'));
    }

    public function showTicket(Ticket $ticket)
    {
        $ticket->load(['comments.attachments', 'attachments']);

        return view('modules.support.tickets.show', compact('ticket'));
    }

    public function editTicket(Ticket $ticket)
    {
        return view('modules.support.tickets.edit', compact('ticket'));
    }

    public function updateTicket(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:open,in_progress,pending,resolved,closed',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|in:technical,billing,general,feature_request,bug_report',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'assigned_to' => 'nullable|string|max:255',
            'due_date' => 'nullable|date',
            'resolution_notes' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        // Process tags
        if ($validated['tags']) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        // Set resolved_at timestamp if status changed to resolved
        if ($validated['status'] === 'resolved' && $ticket->status !== 'resolved') {
            $validated['resolved_at'] = now();
        } elseif ($validated['status'] !== 'resolved') {
            $validated['resolved_at'] = null;
        }

        $ticket->update($validated);

        return redirect()->route('modules.support.tickets.show', $ticket)
            ->with('success', __('erp.ticket_updated_successfully'));
    }

    public function destroyTicket(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('modules.support.tickets.index')
            ->with('success', __('erp.success'));
    }

    public function storeComment(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'author_name' => 'required|string|max:255',
            'author_email' => 'required|email|max:255',
            'author_type' => 'required|in:customer,support,technical',
            'is_internal' => 'boolean',
            'is_solution' => 'boolean',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        $comment = $ticket->comments()->create($validated);

        // Handle file attachments for comment
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $this->storeAttachment($ticket, $file, $validated['author_name'], $comment->id);
            }
        }

        return redirect()->route('modules.support.tickets.show', $ticket)
            ->with('success', __('erp.comment_added_successfully'));
    }

    public function downloadAttachment(TicketAttachment $attachment)
    {
        if (! Storage::exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::download($attachment->file_path, $attachment->original_name);
    }

    public function destroyAttachment(TicketAttachment $attachment)
    {
        $ticketId = $attachment->ticket_id;
        $attachment->delete();

        return redirect()->route('modules.support.tickets.show', $ticketId)
            ->with('success', 'Attachment deleted successfully');
    }

    private function storeAttachment(Ticket $ticket, $file, string $uploadedBy, ?int $commentId = null): TicketAttachment
    {
        $originalName = $file->getClientOriginalName();
        $fileName = Str::uuid().'.'.$file->getClientOriginalExtension();
        $filePath = $file->storeAs('support/attachments', $fileName, 'public');

        return TicketAttachment::create([
            'ticket_id' => $ticket->id,
            'ticket_comment_id' => $commentId,
            'original_name' => $originalName,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $uploadedBy,
        ]);
    }

    private function calculateAverageResponseTime(): string
    {
        $tickets = Ticket::with('comments')->get();
        $totalMinutes = 0;
        $count = 0;

        foreach ($tickets as $ticket) {
            $firstResponse = $ticket->comments()
                ->where('author_type', '!=', 'customer')
                ->first();

            if ($firstResponse) {
                $totalMinutes += $ticket->created_at->diffInMinutes($firstResponse->created_at);
                $count++;
            }
        }

        if ($count === 0) {
            return '0h';
        }

        $avgMinutes = $totalMinutes / $count;
        $hours = floor($avgMinutes / 60);
        $minutes = $avgMinutes % 60;

        if ($hours > 0) {
            return $hours.'h '.round($minutes).'m';
        } else {
            return round($minutes).'m';
        }
    }

    public function assignTicket(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|string|max:255',
        ]);

        $ticket->update($validated);

        return redirect()->route('modules.support.tickets.show', $ticket)
            ->with('success', __('erp.ticket_assigned_successfully'));
    }

    public function resolveTicket(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $ticket->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_notes' => $validated['resolution_notes'],
        ]);

        return redirect()->route('modules.support.tickets.show', $ticket)
            ->with('success', __('erp.ticket_resolved_successfully'));
    }

    public function reopenTicket(Ticket $ticket)
    {
        $ticket->update([
            'status' => 'open',
            'resolved_at' => null,
            'resolution_notes' => null,
        ]);

        return redirect()->route('modules.support.tickets.show', $ticket)
            ->with('success', 'Ticket reopened successfully');
    }

    public function closeTicket(Ticket $ticket)
    {
        $ticket->update(['status' => 'closed']);

        return redirect()->route('modules.support.tickets.show', $ticket)
            ->with('success', 'Ticket closed successfully');
    }
}
