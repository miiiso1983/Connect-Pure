<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Accounting\Models\ChartOfAccount;
use App\Modules\Accounting\Models\Customer;
use App\Modules\Accounting\Models\Vendor;
use App\Modules\Accounting\Models\Product;
use App\Modules\Accounting\Models\Expense;

class SimpleAccountingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createBasicAccounts();
        $this->createSampleCustomers();
        $this->createSampleVendors();
        $this->createSampleProducts();
        $this->createSampleExpenses();
    }

    /**
     * Create basic chart of accounts
     */
    private function createBasicAccounts(): void
    {
        $accounts = [
            // Assets
            ['account_code' => '1000', 'account_name' => 'Cash', 'account_type' => 'asset', 'account_subtype' => 'current_asset'],
            ['account_code' => '1100', 'account_name' => 'Accounts Receivable', 'account_type' => 'asset', 'account_subtype' => 'current_asset'],
            ['account_code' => '1200', 'account_name' => 'Inventory', 'account_type' => 'asset', 'account_subtype' => 'current_asset'],

            // Liabilities
            ['account_code' => '2000', 'account_name' => 'Accounts Payable', 'account_type' => 'liability', 'account_subtype' => 'current_liability'],
            ['account_code' => '2100', 'account_name' => 'Sales Tax Payable', 'account_type' => 'liability', 'account_subtype' => 'current_liability'],

            // Equity
            ['account_code' => '3000', 'account_name' => 'Owner\'s Equity', 'account_type' => 'equity', 'account_subtype' => 'equity'],

            // Revenue
            ['account_code' => '4000', 'account_name' => 'Sales Revenue', 'account_type' => 'revenue', 'account_subtype' => 'operating_revenue'],
            ['account_code' => '4100', 'account_name' => 'Service Revenue', 'account_type' => 'revenue', 'account_subtype' => 'operating_revenue'],

            // Expenses
            ['account_code' => '6000', 'account_name' => 'Office Supplies', 'account_type' => 'expense', 'account_subtype' => 'operating_expense'],
            ['account_code' => '6100', 'account_name' => 'Rent Expense', 'account_type' => 'expense', 'account_subtype' => 'operating_expense'],
            ['account_code' => '6200', 'account_name' => 'Utilities Expense', 'account_type' => 'expense', 'account_subtype' => 'operating_expense'],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::create($account);
        }
    }

    /**
     * Create sample customers
     */
    private function createSampleCustomers(): void
    {
        $customers = [
            [
                'customer_number' => 'CUST1001',
                'company_name' => 'Acme Corporation',
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith@acme.com',
                'phone' => '+1-555-0101',
                'billing_address' => '123 Business St',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10001',
                'country' => 'US',
                'payment_terms' => 'net_30',
                'credit_limit' => 50000.00,
            ],
            [
                'customer_number' => 'CUST1002',
                'company_name' => 'Tech Solutions Inc',
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah@techsolutions.com',
                'phone' => '+1-555-0102',
                'billing_address' => '456 Innovation Ave',
                'city' => 'San Francisco',
                'state' => 'CA',
                'postal_code' => '94105',
                'country' => 'US',
                'payment_terms' => 'net_15',
                'credit_limit' => 75000.00,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }

    /**
     * Create sample vendors
     */
    private function createSampleVendors(): void
    {
        $vendors = [
            [
                'vendor_number' => 'VEND1001',
                'company_name' => 'Office Supplies Co',
                'contact_name' => 'Mike Wilson',
                'email' => 'mike@officesupplies.com',
                'phone' => '+1-555-0201',
                'address' => '789 Supply Street',
                'city' => 'Chicago',
                'state' => 'IL',
                'postal_code' => '60601',
                'country' => 'US',
                'payment_terms' => 'net_30',
            ],
            [
                'vendor_number' => 'VEND1002',
                'company_name' => 'Cloud Services Ltd',
                'contact_name' => 'Lisa Chen',
                'email' => 'lisa@cloudservices.com',
                'phone' => '+1-555-0202',
                'address' => '321 Cloud Ave',
                'city' => 'Seattle',
                'state' => 'WA',
                'postal_code' => '98101',
                'country' => 'US',
                'payment_terms' => 'net_15',
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }
    }

    /**
     * Create sample products
     */
    private function createSampleProducts(): void
    {
        $salesAccount = ChartOfAccount::where('account_code', '4000')->first();
        $serviceAccount = ChartOfAccount::where('account_code', '4100')->first();

        $products = [
            [
                'sku' => 'SKU1001',
                'name' => 'Premium Software License',
                'description' => 'Annual software license with premium features',
                'type' => 'service',
                'category' => 'Software',
                'unit_price' => 1200.00,
                'income_account_id' => $serviceAccount->id,
                'tax_rate' => 8.25,
            ],
            [
                'sku' => 'SKU1002',
                'name' => 'Consulting Services',
                'description' => 'Professional consulting services per hour',
                'type' => 'service',
                'category' => 'Consulting',
                'unit_price' => 150.00,
                'unit_of_measure' => 'hour',
                'income_account_id' => $serviceAccount->id,
                'tax_rate' => 8.25,
            ],
            [
                'sku' => 'SKU1003',
                'name' => 'Hardware Device',
                'description' => 'Professional grade hardware device',
                'type' => 'product',
                'category' => 'Hardware',
                'unit_price' => 500.00,
                'cost_price' => 300.00,
                'quantity_on_hand' => 50,
                'reorder_point' => 10,
                'income_account_id' => $salesAccount->id,
                'tax_rate' => 8.25,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }

    /**
     * Create sample expenses
     */
    private function createSampleExpenses(): void
    {
        $vendors = Vendor::all();
        $expenseAccounts = ChartOfAccount::where('account_type', 'expense')->get();

        $expenses = [
            [
                'expense_number' => 'EXP-1001',
                'vendor_id' => $vendors->first()->id,
                'expense_account_id' => $expenseAccounts->where('account_code', '6000')->first()->id,
                'expense_date' => now()->subDays(5),
                'amount' => 250.00,
                'total_amount' => 250.00,
                'description' => 'Office supplies purchase',
                'status' => 'paid',
                'payment_method' => 'credit_card',
            ],
            [
                'expense_number' => 'EXP-1002',
                'vendor_id' => $vendors->last()->id,
                'expense_account_id' => $expenseAccounts->where('account_code', '6100')->first()->id,
                'expense_date' => now()->subDays(10),
                'amount' => 3000.00,
                'total_amount' => 3000.00,
                'description' => 'Monthly rent payment',
                'status' => 'approved',
                'payment_method' => 'bank_transfer',
            ],
            [
                'expense_number' => 'EXP-1003',
                'vendor_id' => $vendors->first()->id,
                'expense_account_id' => $expenseAccounts->where('account_code', '6200')->first()->id,
                'expense_date' => now()->subDays(3),
                'amount' => 450.00,
                'total_amount' => 450.00,
                'description' => 'Utility bills',
                'status' => 'pending',
                'payment_method' => 'check',
            ],
        ];

        foreach ($expenses as $expense) {
            Expense::create($expense);
        }
    }
}
