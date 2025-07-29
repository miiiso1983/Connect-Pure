<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SupportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample tickets
        $tickets = [
            [
                'title' => 'Login Issues with Dashboard',
                'description' => 'Customer is unable to access the main dashboard after recent update. Getting error message "Invalid credentials" even with correct password.',
                'status' => 'open',
                'priority' => 'high',
                'category' => 'technical',
                'customer_name' => 'Ahmed Al-Rashid',
                'customer_email' => 'ahmed@alshifa.com',
                'customer_phone' => '+971-50-123-4567',
                'assigned_to' => 'Technical Team',
                'due_date' => now()->addDays(1),
                'tags' => ['login', 'dashboard', 'authentication'],
                'created_at' => now()->subHours(2),
            ],
            [
                'title' => 'Feature Request: Arabic Language Support',
                'description' => 'We would like to request full Arabic language support for the ERP system, including RTL layout and Arabic translations for all modules.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'category' => 'feature_request',
                'customer_name' => 'Sara Mohammed',
                'customer_email' => 'sara@noorhc.com',
                'customer_phone' => '+971-55-987-6543',
                'assigned_to' => 'Development Team',
                'due_date' => now()->addWeeks(2),
                'tags' => ['arabic', 'i18n', 'rtl', 'localization'],
                'created_at' => now()->subHours(5),
            ],
            [
                'title' => 'Payment Gateway Not Responding',
                'description' => 'The payment gateway integration is not working properly. Customers are unable to complete transactions and getting timeout errors.',
                'status' => 'pending',
                'priority' => 'urgent',
                'category' => 'technical',
                'customer_name' => 'Omar Hassan',
                'customer_email' => 'omar@seha.ae',
                'customer_phone' => '+971-52-456-7890',
                'assigned_to' => 'Technical Team',
                'due_date' => now()->subHours(2), // Overdue
                'tags' => ['payment', 'gateway', 'integration', 'urgent'],
                'created_at' => now()->subDay(),
            ],
            [
                'title' => 'Billing Discrepancy in Invoice #INV-2024-001',
                'description' => 'There seems to be a calculation error in the latest invoice. The total amount does not match the sum of line items.',
                'status' => 'resolved',
                'priority' => 'medium',
                'category' => 'billing',
                'customer_name' => 'Fatima Al-Zahra',
                'customer_email' => 'fatima@healthplus.ae',
                'customer_phone' => '+971-56-789-0123',
                'assigned_to' => 'Accounting Team',
                'resolved_at' => now()->subHours(1),
                'resolution_notes' => 'Invoice calculation error was due to incorrect tax rate. Invoice has been corrected and resent to customer.',
                'tags' => ['billing', 'invoice', 'calculation'],
                'created_at' => now()->subDays(2),
            ],
            [
                'title' => 'General Inquiry About System Capabilities',
                'description' => 'We are evaluating your ERP system for our pharmacy chain. Could you provide more information about inventory management features?',
                'status' => 'closed',
                'priority' => 'low',
                'category' => 'general',
                'customer_name' => 'Khalid Al-Mansoori',
                'customer_email' => 'khalid@pharmachain.ae',
                'customer_phone' => '+971-50-345-6789',
                'assigned_to' => 'Sales Team',
                'resolved_at' => now()->subDays(1),
                'resolution_notes' => 'Provided detailed information about inventory management features. Customer satisfied with the response.',
                'tags' => ['inquiry', 'inventory', 'features'],
                'created_at' => now()->subDays(3),
            ],
        ];

        foreach ($tickets as $ticketData) {
            $ticket = Ticket::create($ticketData);

            // Add sample comments for some tickets
            $this->addSampleComments($ticket);
        }
    }

    private function addSampleComments(Ticket $ticket): void
    {
        $comments = [];

        switch ($ticket->status) {
            case 'open':
                $comments = [
                    [
                        'comment' => 'Thank you for reporting this issue. We have received your ticket and our technical team will investigate this login problem.',
                        'author_name' => 'Support Agent',
                        'author_email' => 'support@company.com',
                        'author_type' => 'support',
                        'is_internal' => false,
                        'created_at' => $ticket->created_at->addMinutes(30),
                    ],
                    [
                        'comment' => 'Initial investigation shows this might be related to the recent security update. Escalating to technical team.',
                        'author_name' => 'Support Agent',
                        'author_email' => 'support@company.com',
                        'author_type' => 'support',
                        'is_internal' => true,
                        'created_at' => $ticket->created_at->addHours(1),
                    ],
                ];
                break;

            case 'in_progress':
                $comments = [
                    [
                        'comment' => 'We have reviewed your feature request for Arabic language support. This is indeed a valuable addition to our system.',
                        'author_name' => 'Product Manager',
                        'author_email' => 'product@company.com',
                        'author_type' => 'support',
                        'is_internal' => false,
                        'created_at' => $ticket->created_at->addHours(2),
                    ],
                    [
                        'comment' => 'Development team has started working on the Arabic localization. Estimated completion time is 2 weeks.',
                        'author_name' => 'Dev Team Lead',
                        'author_email' => 'dev@company.com',
                        'author_type' => 'technical',
                        'is_internal' => false,
                        'created_at' => $ticket->created_at->addHours(4),
                    ],
                ];
                break;

            case 'resolved':
                $comments = [
                    [
                        'comment' => 'We have identified the billing discrepancy in your invoice. The issue was caused by an incorrect tax rate configuration.',
                        'author_name' => 'Billing Specialist',
                        'author_email' => 'billing@company.com',
                        'author_type' => 'support',
                        'is_internal' => false,
                        'created_at' => $ticket->created_at->addHours(3),
                    ],
                    [
                        'comment' => 'The corrected invoice has been generated and sent to your email address. Please review and let us know if everything looks correct now.',
                        'author_name' => 'Billing Specialist',
                        'author_email' => 'billing@company.com',
                        'author_type' => 'support',
                        'is_internal' => false,
                        'is_solution' => true,
                        'created_at' => $ticket->created_at->addHours(4),
                    ],
                    [
                        'comment' => 'Perfect! The corrected invoice looks good. Thank you for the quick resolution.',
                        'author_name' => $ticket->customer_name,
                        'author_email' => $ticket->customer_email,
                        'author_type' => 'customer',
                        'is_internal' => false,
                        'created_at' => $ticket->created_at->addHours(5),
                    ],
                ];
                break;
        }

        foreach ($comments as $commentData) {
            $ticket->comments()->create($commentData);
        }
    }
}
