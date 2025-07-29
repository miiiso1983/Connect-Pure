<?php

namespace App\Modules\CRM\Imports;

use App\Modules\CRM\Models\Contact;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Validation\Rule;

class ContactsImport implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation, 
    SkipsOnError, 
    SkipsOnFailure, 
    WithBatchInserts, 
    WithChunkReading
{
    use Importable, SkipsErrors, SkipsFailures;

    private $importedCount = 0;
    private $skippedCount = 0;

    /**
     * Transform a row into a model.
     */
    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['name']) || empty($row['email'])) {
            $this->skippedCount++;
            return null;
        }

        // Check if contact already exists
        $existingContact = Contact::where('email', $row['email'])->first();
        if ($existingContact) {
            $this->skippedCount++;
            return null;
        }

        $this->importedCount++;

        return new Contact([
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'] ?? null,
            'company' => $row['company'] ?? null,
            'position' => $row['position'] ?? null,
            'status' => $this->validateStatus($row['status'] ?? 'lead'),
            'priority' => $this->validatePriority($row['priority'] ?? 'medium'),
            'city' => $row['city'] ?? null,
            'country' => $row['country'] ?? null,
            'notes' => $row['notes'] ?? null,
            'source' => $this->validateSource($row['source'] ?? 'other'),
            'assigned_to' => $this->findAssignedUser($row['assigned_to'] ?? null),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Validation rules for each row.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:contacts,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'company' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['lead', 'prospect', 'customer', 'inactive'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'source' => ['nullable', Rule::in(['website', 'social_media', 'referral', 'advertising', 'cold_call', 'email', 'event', 'other'])],
            'assigned_to' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom attribute names for validation errors.
     */
    public function customValidationAttributes(): array
    {
        return [
            'name' => 'Contact Name',
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'company' => 'Company Name',
            'position' => 'Job Position',
            'status' => 'Contact Status',
            'priority' => 'Priority Level',
            'city' => 'City',
            'country' => 'Country',
            'notes' => 'Notes',
            'source' => 'Lead Source',
            'assigned_to' => 'Assigned To',
        ];
    }

    /**
     * Batch size for bulk inserts.
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk size for reading large files.
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Validate and normalize status value.
     */
    private function validateStatus(?string $status): string
    {
        $validStatuses = ['lead', 'prospect', 'customer', 'inactive'];
        $status = strtolower(trim($status ?? ''));
        
        return in_array($status, $validStatuses) ? $status : 'lead';
    }

    /**
     * Validate and normalize priority value.
     */
    private function validatePriority(?string $priority): string
    {
        $validPriorities = ['low', 'medium', 'high'];
        $priority = strtolower(trim($priority ?? ''));
        
        return in_array($priority, $validPriorities) ? $priority : 'medium';
    }

    /**
     * Validate and normalize source value.
     */
    private function validateSource(?string $source): string
    {
        $validSources = ['website', 'social_media', 'referral', 'advertising', 'cold_call', 'email', 'event', 'other'];
        $source = strtolower(str_replace(' ', '_', trim($source ?? '')));
        
        return in_array($source, $validSources) ? $source : 'other';
    }

    /**
     * Find user ID by name or email for assignment.
     */
    private function findAssignedUser(?string $assignedTo): ?int
    {
        if (empty($assignedTo)) {
            return null;
        }

        // Try to find user by name or email
        $user = \App\Models\User::where('name', 'like', "%{$assignedTo}%")
            ->orWhere('email', $assignedTo)
            ->first();

        return $user ? $user->id : null;
    }

    /**
     * Get import statistics.
     */
    public function getImportStats(): array
    {
        return [
            'imported' => $this->importedCount,
            'skipped' => $this->skippedCount,
            'errors' => count($this->errors()),
            'failures' => count($this->failures()),
        ];
    }

    /**
     * Get detailed error information.
     */
    public function getErrorDetails(): array
    {
        $details = [];

        // Add validation failures
        foreach ($this->failures() as $failure) {
            $details[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
        }

        // Add general errors
        foreach ($this->errors() as $error) {
            $details[] = [
                'row' => 'Unknown',
                'attribute' => 'General',
                'errors' => [$error->getMessage()],
                'values' => [],
            ];
        }

        return $details;
    }

    /**
     * Get success message with statistics.
     */
    public function getSuccessMessage(): string
    {
        $stats = $this->getImportStats();
        
        $message = "Import completed successfully! ";
        $message .= "Imported: {$stats['imported']} contacts";
        
        if ($stats['skipped'] > 0) {
            $message .= ", Skipped: {$stats['skipped']} (duplicates or empty)";
        }
        
        if ($stats['errors'] > 0 || $stats['failures'] > 0) {
            $message .= ", Errors: " . ($stats['errors'] + $stats['failures']);
        }

        return $message;
    }

    /**
     * Check if import has errors.
     */
    public function hasErrors(): bool
    {
        return count($this->errors()) > 0 || count($this->failures()) > 0;
    }

    /**
     * Get imported count for backward compatibility.
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
