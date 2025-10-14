<?php

namespace App\Imports;

use App\Modules\CRM\Models\Contact;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ContactsImport implements SkipsOnError, SkipsOnFailure, ToModel, WithBatchInserts, WithChunkReading, WithHeadingRow, WithValidation
{
    use Importable, SkipsErrors, SkipsFailures;

    private $importedCount = 0;

    private $skippedCount = 0;

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['name'])) {
            $this->skippedCount++;

            return null;
        }

        // Check if contact already exists
        $existingContact = Contact::where('email', $row['email'])
            ->orWhere(function ($query) use ($row) {
                $query->where('name', $row['name'])
                    ->where('company', $row['company']);
            })
            ->first();

        if ($existingContact) {
            $this->skippedCount++;

            return null;
        }

        $this->importedCount++;

        return new Contact([
            'name' => $row['name'],
            'company' => $row['company'] ?? null,
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
            'type' => $this->validateType($row['type'] ?? 'lead'),
            'status' => $this->validateStatus($row['status'] ?? 'new'),
            'notes' => $row['notes'] ?? null,
            'potential_value' => is_numeric($row['potential_value'] ?? null) ? $row['potential_value'] : null,
            'source' => $row['source'] ?? 'Excel Import',
            'assigned_to' => $row['assigned_to'] ?? null,
            'next_follow_up' => $this->parseDate($row['next_follow_up'] ?? null),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'type' => 'nullable|in:lead,client',
            'status' => 'nullable|in:new,contacted,qualified,proposal,negotiation,closed_won,closed_lost',
            'potential_value' => 'nullable|numeric|min:0',
            'source' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|string|max:255',
            'next_follow_up' => 'nullable|date',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'Contact name is required.',
            'email.email' => 'Please provide a valid email address.',
            'type.in' => 'Type must be either "lead" or "client".',
            'status.in' => 'Status must be one of: new, contacted, qualified, proposal, negotiation, closed_won, closed_lost.',
            'potential_value.numeric' => 'Potential value must be a number.',
            'next_follow_up.date' => 'Next follow-up must be a valid date.',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    private function validateType($type): string
    {
        $validTypes = ['lead', 'client'];

        return in_array(strtolower($type), $validTypes) ? strtolower($type) : 'lead';
    }

    private function validateStatus($status): string
    {
        $validStatuses = ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'closed_won', 'closed_lost'];

        return in_array(strtolower($status), $validStatuses) ? strtolower($status) : 'new';
    }

    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($date);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
}
