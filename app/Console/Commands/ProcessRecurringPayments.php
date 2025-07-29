<?php

namespace App\Console\Commands;

use App\Modules\Accounting\Models\RecurringProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessRecurringPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurring:process {--dry-run : Show what would be processed without actually processing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process due recurring payment profiles and create invoices/expenses/payments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting recurring payments processing...');

        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No actual processing will occur');
        }

        // Get all profiles due for processing
        $profiles = RecurringProfile::dueForProcessing()->get();

        if ($profiles->isEmpty()) {
            $this->info('No recurring profiles are due for processing.');
            return 0;
        }

        $this->info("Found {$profiles->count()} profiles due for processing:");

        $processed = 0;
        $errors = 0;

        foreach ($profiles as $profile) {
            $this->line("Processing: {$profile->profile_name} (ID: {$profile->id})");

            if ($dryRun) {
                $this->line("  - Would create: {$profile->type}");
                $this->line("  - Amount: {$profile->formatted_amount}");
                $this->line("  - Next run date: {$profile->next_run_date_formatted}");
                continue;
            }

            try {
                $created = $profile->process();

                if ($created) {
                    $processed++;
                    $this->info("  ✓ Created {$profile->type} (ID: {$created->id})");

                    Log::info('Recurring profile processed successfully', [
                        'profile_id' => $profile->id,
                        'profile_name' => $profile->profile_name,
                        'created_type' => $profile->type,
                        'created_id' => $created->id,
                        'amount' => $profile->amount
                    ]);
                } else {
                    $this->error("  ✗ Failed to create {$profile->type}");
                    $errors++;
                }

            } catch (\Exception $e) {
                $errors++;
                $this->error("  ✗ Error processing profile: {$e->getMessage()}");

                Log::error('Error processing recurring profile', [
                    'profile_id' => $profile->id,
                    'profile_name' => $profile->profile_name,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        if ($dryRun) {
            $this->info("DRY RUN COMPLETE - {$profiles->count()} profiles would be processed");
        } else {
            $this->info("Processing complete!");
            $this->info("Successfully processed: {$processed}");

            if ($errors > 0) {
                $this->error("Errors encountered: {$errors}");
                return 1;
            }
        }

        return 0;
    }
}
