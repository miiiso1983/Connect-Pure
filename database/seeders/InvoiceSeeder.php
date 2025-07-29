<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Accounting\Models\Invoice;
use App\Modules\Accounting\Models\InvoiceItem;
use App\Modules\Accounting\Models\Customer;
use App\Modules\Accounting\Models\Product;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No customers or products found. Please run SimpleAccountingSeeder first.');
            return;
        }

        $invoices = [
            [
                'invoice_number' => 'INV-1001',
                'customer_id' => $customers->first()->id,
                'invoice_date' => now()->subDays(30),
                'due_date' => now()->subDays(15),
                'subtotal' => 1200.00,
                'tax_amount' => 99.00,
                'total_amount' => 1299.00,
                'paid_amount' => 1299.00,
                'balance_due' => 0.00,
                'status' => 'paid',
                'sent_at' => now()->subDays(30),
                'notes' => 'Premium software license for annual subscription',
                'items' => [
                    [
                        'product_id' => $products->first()->id,
                        'description' => 'Premium Software License - Annual',
                        'quantity' => 1,
                        'unit_price' => 1200.00,
                        'total_amount' => 1200.00,
                    ]
                ]
            ],
            [
                'invoice_number' => 'INV-1002',
                'customer_id' => $customers->last()->id,
                'invoice_date' => now()->subDays(20),
                'due_date' => now()->subDays(5),
                'subtotal' => 3000.00,
                'tax_amount' => 247.50,
                'total_amount' => 3247.50,
                'paid_amount' => 1500.00,
                'balance_due' => 1747.50,
                'status' => 'partial',
                'sent_at' => now()->subDays(20),
                'notes' => 'Consulting services for Q1 project',
                'items' => [
                    [
                        'product_id' => $products->skip(1)->first()->id,
                        'description' => 'Consulting Services - 20 hours',
                        'quantity' => 20,
                        'unit_price' => 150.00,
                        'total_amount' => 3000.00,
                    ]
                ]
            ],
            [
                'invoice_number' => 'INV-1003',
                'customer_id' => $customers->first()->id,
                'invoice_date' => now()->subDays(10),
                'due_date' => now()->addDays(20),
                'subtotal' => 2500.00,
                'tax_amount' => 206.25,
                'total_amount' => 2706.25,
                'paid_amount' => 0.00,
                'balance_due' => 2706.25,
                'status' => 'sent',
                'sent_at' => now()->subDays(10),
                'notes' => 'Hardware devices and consulting package',
                'items' => [
                    [
                        'product_id' => $products->last()->id,
                        'description' => 'Hardware Device - Professional Grade',
                        'quantity' => 5,
                        'unit_price' => 500.00,
                        'total_amount' => 2500.00,
                    ]
                ]
            ],
            [
                'invoice_number' => 'INV-1004',
                'customer_id' => $customers->last()->id,
                'invoice_date' => now()->subDays(5),
                'due_date' => now()->addDays(25),
                'subtotal' => 1800.00,
                'tax_amount' => 148.50,
                'total_amount' => 1948.50,
                'paid_amount' => 0.00,
                'balance_due' => 1948.50,
                'status' => 'sent',
                'sent_at' => now()->subDays(5),
                'notes' => 'Monthly consulting retainer',
                'items' => [
                    [
                        'product_id' => $products->skip(1)->first()->id,
                        'description' => 'Monthly Consulting Retainer - 12 hours',
                        'quantity' => 12,
                        'unit_price' => 150.00,
                        'total_amount' => 1800.00,
                    ]
                ]
            ],
            [
                'invoice_number' => 'INV-1005',
                'customer_id' => $customers->first()->id,
                'invoice_date' => now()->subDays(2),
                'due_date' => now()->addDays(28),
                'subtotal' => 600.00,
                'tax_amount' => 49.50,
                'total_amount' => 649.50,
                'paid_amount' => 0.00,
                'balance_due' => 649.50,
                'status' => 'draft',
                'notes' => 'Additional software license',
                'items' => [
                    [
                        'product_id' => $products->first()->id,
                        'description' => 'Additional Software License - 6 months',
                        'quantity' => 0.5,
                        'unit_price' => 1200.00,
                        'total_amount' => 600.00,
                    ]
                ]
            ],
        ];

        foreach ($invoices as $invoiceData) {
            $items = $invoiceData['items'];
            unset($invoiceData['items']);

            $invoice = Invoice::create($invoiceData);

            foreach ($items as $itemData) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $itemData['product_id'],
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_amount' => $itemData['total_amount'],
                ]);
            }
        }

        $this->command->info('Created ' . count($invoices) . ' sample invoices with items.');
    }
}
