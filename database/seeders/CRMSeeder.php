<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Modules\CRM\Models\FollowUp;

class CRMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample contacts
        $contacts = [
            [
                'name' => 'Ahmed Al-Rashid',
                'company' => 'Al-Shifa Pharmacy',
                'email' => 'ahmed@alshifa.com',
                'phone' => '+971-50-123-4567',
                'type' => 'client',
                'status' => 'closed_won',
                'potential_value' => 15000.00,
                'source' => 'Website',
                'assigned_to' => 'Sales Rep 1',
                'notes' => 'Large pharmacy chain interested in our ERP solution.',
            ],
            [
                'name' => 'Sarah Mohammed',
                'company' => 'Noor Healthcare',
                'email' => 'sarah@noorhc.com',
                'phone' => '+971-55-987-6543',
                'type' => 'lead',
                'status' => 'qualified',
                'potential_value' => 8500.00,
                'source' => 'Referral',
                'assigned_to' => 'Sales Rep 2',
                'notes' => 'Small clinic looking for basic CRM features.',
                'next_follow_up' => now()->addDays(3),
            ],
            [
                'name' => 'Omar Hassan',
                'company' => 'Seha Medical Center',
                'email' => 'omar@seha.ae',
                'phone' => '+971-52-456-7890',
                'type' => 'lead',
                'status' => 'proposal',
                'potential_value' => 25000.00,
                'source' => 'Trade Show',
                'assigned_to' => 'Sales Rep 1',
                'notes' => 'Large medical center requiring full ERP implementation.',
                'next_follow_up' => now()->addDays(1),
            ],
            [
                'name' => 'Fatima Al-Zahra',
                'company' => 'Dubai Pharmacy',
                'email' => 'fatima@dubaipharmacy.com',
                'phone' => '+971-56-789-0123',
                'type' => 'lead',
                'status' => 'contacted',
                'potential_value' => 12000.00,
                'source' => 'Cold Call',
                'assigned_to' => 'Sales Rep 3',
                'notes' => 'Interested in accounting module specifically.',
                'next_follow_up' => now()->addDays(7),
            ],
            [
                'name' => 'Khalid Al-Mansoori',
                'company' => 'Emirates Health',
                'email' => 'khalid@emirateshealth.ae',
                'phone' => '+971-50-345-6789',
                'type' => 'lead',
                'status' => 'new',
                'potential_value' => 30000.00,
                'source' => 'LinkedIn',
                'assigned_to' => 'Sales Rep 2',
                'notes' => 'Large healthcare provider, potential for enterprise deal.',
            ],
        ];

        foreach ($contacts as $contactData) {
            $contact = Contact::create($contactData);

            // Add sample communications
            Communication::create([
                'contact_id' => $contact->id,
                'type' => 'email',
                'subject' => 'Initial Contact',
                'content' => 'Sent initial information about our ERP solution.',
                'communication_date' => now()->subDays(rand(1, 30)),
                'created_by' => $contactData['assigned_to'],
            ]);

            if (rand(0, 1)) {
                Communication::create([
                    'contact_id' => $contact->id,
                    'type' => 'call',
                    'subject' => 'Follow-up Call',
                    'content' => 'Discussed requirements and answered questions about pricing.',
                    'communication_date' => now()->subDays(rand(1, 15)),
                    'created_by' => $contactData['assigned_to'],
                ]);
            }

            // Add sample follow-ups
            if ($contact->status !== 'closed_won' && $contact->status !== 'closed_lost') {
                FollowUp::create([
                    'contact_id' => $contact->id,
                    'title' => 'Follow-up on proposal',
                    'description' => 'Check on decision timeline and address any concerns.',
                    'scheduled_date' => $contact->next_follow_up ?? now()->addDays(rand(1, 14)),
                    'priority' => ['low', 'medium', 'high'][rand(0, 2)],
                    'assigned_to' => $contactData['assigned_to'],
                ]);
            }
        }
    }
}
