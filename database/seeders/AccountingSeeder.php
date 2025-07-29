<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Accounting\Models\Account;
use App\Modules\Accounting\Models\Customer;
use App\Modules\Accounting\Models\Vendor;
use App\Modules\Accounting\Models\Employee;
use App\Modules\Accounting\Models\Invoice;
use App\Modules\Accounting\Models\InvoiceItem;
use App\Modules\Accounting\Models\Expense;
use App\Modules\Accounting\Models\Payment;
use App\Modules\Accounting\Models\TaxRate;

class AccountingSeeder extends Seeder
{
    public function run()
    {
        // Create Chart of Accounts
        $this->createAccounts();
        
        // Create Tax Rates
        $this->createTaxRates();
        
        // Create Customers
        $this->createCustomers();
        
        // Create Vendors
        $this->createVendors();
        
        // Create Employees
        $this->createEmployees();
        
        // Create Sample Invoices
        $this->createInvoices();
        
        // Create Sample Expenses
        $this->createExpenses();
        
        // Create Sample Payments
        $this->createPayments();
    }

    private function createAccounts()
    {
        $accounts = [
            // Assets
            ['account_number' => '1000', 'name' => 'Cash', 'name_ar' => 'النقد', 'type' => 'asset', 'subtype' => 'current_asset'],
            ['account_number' => '1100', 'name' => 'Accounts Receivable', 'name_ar' => 'حسابات القبض', 'type' => 'asset', 'subtype' => 'current_asset'],
            ['account_number' => '1200', 'name' => 'Inventory', 'name_ar' => 'المخزون', 'type' => 'asset', 'subtype' => 'current_asset'],
            ['account_number' => '1500', 'name' => 'Equipment', 'name_ar' => 'المعدات', 'type' => 'asset', 'subtype' => 'fixed_asset'],
            
            // Liabilities
            ['account_number' => '2000', 'name' => 'Accounts Payable', 'name_ar' => 'حسابات الدفع', 'type' => 'liability', 'subtype' => 'current_liability'],
            ['account_number' => '2100', 'name' => 'Credit Card Payable', 'name_ar' => 'مستحقات بطاقة ائتمان', 'type' => 'liability', 'subtype' => 'current_liability'],
            ['account_number' => '2500', 'name' => 'Long-term Debt', 'name_ar' => 'ديون طويلة الأجل', 'type' => 'liability', 'subtype' => 'long_term_liability'],
            
            // Equity
            ['account_number' => '3000', 'name' => 'Owner\'s Equity', 'name_ar' => 'حقوق الملكية', 'type' => 'equity', 'subtype' => 'equity'],
            ['account_number' => '3100', 'name' => 'Retained Earnings', 'name_ar' => 'الأرباح المحتجزة', 'type' => 'equity', 'subtype' => 'equity'],
            
            // Revenue
            ['account_number' => '4000', 'name' => 'Sales Revenue', 'name_ar' => 'إيرادات المبيعات', 'type' => 'revenue', 'subtype' => 'income'],
            ['account_number' => '4100', 'name' => 'Service Revenue', 'name_ar' => 'إيرادات الخدمات', 'type' => 'revenue', 'subtype' => 'income'],
            ['account_number' => '4200', 'name' => 'Interest Income', 'name_ar' => 'دخل الفوائد', 'type' => 'revenue', 'subtype' => 'other_income'],
            
            // Expenses
            ['account_number' => '5000', 'name' => 'Cost of Goods Sold', 'name_ar' => 'تكلفة البضائع المباعة', 'type' => 'expense', 'subtype' => 'cost_of_goods_sold'],
            ['account_number' => '6000', 'name' => 'Office Supplies', 'name_ar' => 'لوازم المكتب', 'type' => 'expense', 'subtype' => 'expense'],
            ['account_number' => '6100', 'name' => 'Rent Expense', 'name_ar' => 'مصروف الإيجار', 'type' => 'expense', 'subtype' => 'expense'],
            ['account_number' => '6200', 'name' => 'Utilities Expense', 'name_ar' => 'مصروف المرافق', 'type' => 'expense', 'subtype' => 'expense'],
            ['account_number' => '6300', 'name' => 'Marketing Expense', 'name_ar' => 'مصروف التسويق', 'type' => 'expense', 'subtype' => 'expense'],
            ['account_number' => '6400', 'name' => 'Travel Expense', 'name_ar' => 'مصروف السفر', 'type' => 'expense', 'subtype' => 'expense'],
        ];

        foreach ($accounts as $accountData) {
            Account::create($accountData);
        }
    }

    private function createTaxRates()
    {
        $taxRates = [
            [
                'name' => 'Sales Tax',
                'name_ar' => 'ضريبة المبيعات',
                'code' => 'SALES_TAX',
                'rate' => 0.0825, // 8.25%
                'type' => 'sales',
                'jurisdiction' => 'State',
                'country' => 'US',
                'state' => 'CA',
                'effective_date' => now()->subYear(),
                'is_active' => true,
            ],
            [
                'name' => 'VAT',
                'name_ar' => 'ضريبة القيمة المضافة',
                'code' => 'VAT',
                'rate' => 0.15, // 15%
                'type' => 'sales',
                'jurisdiction' => 'Federal',
                'country' => 'SA',
                'effective_date' => now()->subYear(),
                'is_active' => true,
            ],
        ];

        foreach ($taxRates as $taxRateData) {
            TaxRate::create($taxRateData);
        }
    }

    private function createCustomers()
    {
        $customers = [
            [
                'name' => 'John Smith',
                'company_name' => 'Smith Consulting LLC',
                'email' => 'john@smithconsulting.com',
                'phone' => '+1-555-0123',
                'billing_address' => '123 Business St',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'postal_code' => '90210',
                'country' => 'US',
                'currency' => 'USD',
                'payment_terms' => 'net_30',
                'is_active' => true,
            ],
            [
                'name' => 'Sarah Johnson',
                'company_name' => 'Tech Solutions Inc',
                'email' => 'sarah@techsolutions.com',
                'phone' => '+1-555-0456',
                'billing_address' => '456 Innovation Ave',
                'city' => 'San Francisco',
                'state' => 'CA',
                'postal_code' => '94102',
                'country' => 'US',
                'currency' => 'USD',
                'payment_terms' => 'net_15',
                'is_active' => true,
            ],
            [
                'name' => 'Ahmed Al-Rashid',
                'company_name' => 'Al-Rashid Trading',
                'email' => 'ahmed@alrashidtrading.com',
                'phone' => '+966-50-123-4567',
                'billing_address' => 'King Fahd Road',
                'city' => 'Riyadh',
                'state' => 'Riyadh Province',
                'postal_code' => '11564',
                'country' => 'SA',
                'currency' => 'SAR',
                'payment_terms' => 'net_30',
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }
    }

    private function createVendors()
    {
        $vendors = [
            [
                'name' => 'Office Depot',
                'company_name' => 'Office Depot Inc',
                'email' => 'billing@officedepot.com',
                'phone' => '+1-800-463-3768',
                'address' => '6600 North Military Trail',
                'city' => 'Boca Raton',
                'state' => 'FL',
                'postal_code' => '33496',
                'country' => 'US',
                'currency' => 'USD',
                'payment_terms' => 'net_30',
                'is_active' => true,
            ],
            [
                'name' => 'Amazon Web Services',
                'company_name' => 'Amazon Web Services Inc',
                'email' => 'billing@aws.amazon.com',
                'phone' => '+1-206-266-1000',
                'address' => '410 Terry Avenue North',
                'city' => 'Seattle',
                'state' => 'WA',
                'postal_code' => '98109',
                'country' => 'US',
                'currency' => 'USD',
                'payment_terms' => 'due_on_receipt',
                'is_active' => true,
            ],
        ];

        foreach ($vendors as $vendorData) {
            Vendor::create($vendorData);
        }
    }

    private function createEmployees()
    {
        $employees = [
            [
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'email' => 'michael.johnson@company.com',
                'phone' => '+1-555-0789',
                'hire_date' => now()->subMonths(6),
                'status' => 'active',
                'department' => 'Sales',
                'position' => 'Sales Manager',
                'employment_type' => 'full_time',
                'pay_type' => 'salary',
                'pay_rate' => 75000,
                'currency' => 'USD',
                'pay_frequency' => 'bi_weekly',
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.davis@company.com',
                'phone' => '+1-555-0321',
                'hire_date' => now()->subMonths(3),
                'status' => 'active',
                'department' => 'Marketing',
                'position' => 'Marketing Specialist',
                'employment_type' => 'full_time',
                'pay_type' => 'salary',
                'pay_rate' => 55000,
                'currency' => 'USD',
                'pay_frequency' => 'bi_weekly',
            ],
        ];

        foreach ($employees as $employeeData) {
            Employee::create($employeeData);
        }
    }

    private function createInvoices()
    {
        $customers = Customer::all();
        $serviceAccount = Account::where('account_number', '4100')->first();

        foreach ($customers as $customer) {
            // Create 2-3 invoices per customer
            for ($i = 0; $i < rand(2, 3); $i++) {
                $invoice = Invoice::create([
                    'customer_id' => $customer->id,
                    'invoice_date' => now()->subDays(rand(1, 30)),
                    'due_date' => now()->addDays(rand(15, 45)),
                    'currency' => $customer->currency,
                    'payment_terms' => $customer->payment_terms,
                    'status' => ['draft', 'sent', 'paid', 'partial'][rand(0, 3)],
                    'notes' => 'Thank you for your business!',
                ]);

                // Add invoice items
                for ($j = 0; $j < rand(1, 3); $j++) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'name' => ['Consulting Services', 'Web Development', 'SEO Optimization', 'Digital Marketing'][rand(0, 3)],
                        'description' => 'Professional services rendered',
                        'quantity' => rand(1, 10),
                        'rate' => rand(50, 200),
                        'tax_rate' => 8.25,
                        'account_id' => $serviceAccount->id,
                    ]);
                }

                $invoice->calculateTotals();
                
                // Mark some invoices as paid
                if ($invoice->status === 'paid') {
                    $invoice->paid_amount = $invoice->total_amount;
                    $invoice->balance_due = 0;
                    $invoice->paid_at = now()->subDays(rand(1, 15));
                    $invoice->save();
                }
            }
        }
    }

    private function createExpenses()
    {
        $vendors = Vendor::all();
        $employees = Employee::all();
        $expenseAccount = Account::where('account_number', '6000')->first();

        $categories = ['office_supplies', 'travel', 'utilities', 'marketing', 'software'];

        for ($i = 0; $i < 10; $i++) {
            Expense::create([
                'vendor_id' => $vendors->random()->id,
                'employee_id' => $employees->random()->id,
                'account_id' => $expenseAccount->id,
                'expense_date' => now()->subDays(rand(1, 30)),
                'category' => $categories[rand(0, 4)],
                'description' => 'Business expense for ' . $categories[rand(0, 4)],
                'amount' => rand(50, 500),
                'currency' => 'USD',
                'status' => ['draft', 'pending', 'approved', 'paid'][rand(0, 3)],
                'payment_method' => ['credit_card', 'cash', 'check'][rand(0, 2)],
                'is_billable' => rand(0, 1),
                'is_reimbursable' => rand(0, 1),
            ]);
        }
    }

    private function createPayments()
    {
        $invoices = Invoice::where('status', 'paid')->get();
        $cashAccount = Account::where('account_number', '1000')->first();

        foreach ($invoices as $invoice) {
            Payment::create([
                'type' => 'customer_payment',
                'customer_id' => $invoice->customer_id,
                'invoice_id' => $invoice->id,
                'payment_date' => $invoice->paid_at,
                'amount' => $invoice->total_amount,
                'currency' => $invoice->currency,
                'method' => ['cash', 'check', 'credit_card', 'bank_transfer'][rand(0, 3)],
                'status' => 'completed',
                'deposit_account_id' => $cashAccount->id,
                'reference_number' => 'PAY-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
