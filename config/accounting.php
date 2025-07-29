<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Accounting Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the accounting module
    | including default settings, number formats, and business rules.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'currency' => env('ACCOUNTING_DEFAULT_CURRENCY', 'USD'),
        'tax_rate' => env('ACCOUNTING_DEFAULT_TAX_RATE', 0.0),
        'payment_terms' => env('ACCOUNTING_DEFAULT_PAYMENT_TERMS', 'net_30'),
        'invoice_prefix' => env('ACCOUNTING_INVOICE_PREFIX', 'INV-'),
        'expense_prefix' => env('ACCOUNTING_EXPENSE_PREFIX', 'EXP-'),
        'customer_prefix' => env('ACCOUNTING_CUSTOMER_PREFIX', 'CUST'),
        'vendor_prefix' => env('ACCOUNTING_VENDOR_PREFIX', 'VEND'),
        'product_prefix' => env('ACCOUNTING_PRODUCT_PREFIX', 'SKU'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Number Formatting
    |--------------------------------------------------------------------------
    */
    'formatting' => [
        'decimal_places' => 2,
        'thousands_separator' => ',',
        'decimal_separator' => '.',
        'currency_symbol_position' => 'before', // 'before' or 'after'
        'negative_format' => 'parentheses', // 'parentheses', 'minus', 'red'
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    */
    'invoices' => [
        'auto_number' => true,
        'number_format' => 'INV-{YYYY}{MM}{NNNN}', // Year, Month, Sequential Number
        'due_date_days' => 30,
        'overdue_grace_days' => 0,
        'late_fee_percentage' => 0.0,
        'auto_send_reminders' => false,
        'reminder_days' => [7, 3, 1], // Days before due date
        'pdf_template' => 'default',
        'email_template' => 'default',
        'allow_partial_payments' => true,
        'require_approval' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Expense Settings
    |--------------------------------------------------------------------------
    */
    'expenses' => [
        'auto_number' => true,
        'number_format' => 'EXP-{YYYY}{MM}{NNNN}',
        'require_approval' => true,
        'approval_limit' => 1000.00, // Amount requiring approval
        'auto_categorize' => false,
        'receipt_required' => false,
        'mileage_rate' => 0.56, // Per mile/km
        'per_diem_rates' => [
            'domestic' => 50.00,
            'international' => 75.00,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    */
    'payments' => [
        'methods' => [
            'cash' => 'Cash',
            'check' => 'Check',
            'credit_card' => 'Credit Card',
            'bank_transfer' => 'Bank Transfer',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'other' => 'Other',
        ],
        'auto_apply' => true, // Auto-apply payments to oldest invoices
        'overpayment_handling' => 'credit', // 'credit', 'refund', 'ask'
        'payment_terms' => [
            'due_on_receipt' => 'Due on Receipt',
            'net_15' => 'Net 15 Days',
            'net_30' => 'Net 30 Days',
            'net_45' => 'Net 45 Days',
            'net_60' => 'Net 60 Days',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tax Settings
    |--------------------------------------------------------------------------
    */
    'taxes' => [
        'enabled' => true,
        'inclusive' => false, // Tax inclusive pricing
        'compound' => false, // Compound tax calculations
        'rounding' => 'round', // 'round', 'up', 'down'
        'default_rates' => [
            'sales_tax' => 8.25,
            'vat' => 15.00,
            'gst' => 5.00,
        ],
        'exemption_codes' => [
            'RESALE' => 'Resale Certificate',
            'NONPROFIT' => 'Non-Profit Organization',
            'GOVERNMENT' => 'Government Entity',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency Settings
    |--------------------------------------------------------------------------
    */
    'currencies' => [
        'enabled' => ['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'SAR', 'AED'],
        'base_currency' => 'USD',
        'auto_update_rates' => true,
        'rate_provider' => 'fixer', // 'fixer', 'openexchangerates', 'currencylayer'
        'rate_update_frequency' => 'daily', // 'hourly', 'daily', 'weekly'
        'symbols' => [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'SAR' => 'ر.س',
            'AED' => 'د.إ',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Inventory Settings
    |--------------------------------------------------------------------------
    */
    'inventory' => [
        'tracking_enabled' => true,
        'costing_method' => 'fifo', // 'fifo', 'lifo', 'average'
        'negative_stock' => false,
        'auto_reorder' => false,
        'reorder_notification' => true,
        'barcode_enabled' => false,
        'serial_tracking' => false,
        'lot_tracking' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Reporting Settings
    |--------------------------------------------------------------------------
    */
    'reports' => [
        'fiscal_year_start' => '01-01', // MM-DD format
        'comparison_periods' => ['previous_period', 'previous_year', 'budget'],
        'default_format' => 'pdf',
        'auto_email' => false,
        'email_schedule' => 'monthly',
        'retention_days' => 365,
        'cache_enabled' => true,
        'cache_duration' => 3600, // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Payroll Settings
    |--------------------------------------------------------------------------
    */
    'payroll' => [
        'enabled' => true,
        'pay_frequencies' => [
            'weekly' => 'Weekly',
            'bi_weekly' => 'Bi-Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
        ],
        'overtime_rate' => 1.5,
        'tax_tables' => [
            'federal' => 'us_federal_2024',
            'state' => 'auto_detect',
        ],
        'benefits' => [
            'health_insurance' => 0.0,
            'dental_insurance' => 0.0,
            'retirement_401k' => 0.0,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'security' => [
        'audit_enabled' => true,
        'data_retention_days' => 2555, // 7 years
        'encryption_enabled' => true,
        'backup_frequency' => 'daily',
        'access_log_enabled' => true,
        'failed_login_attempts' => 5,
        'session_timeout' => 3600, // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration Settings
    |--------------------------------------------------------------------------
    */
    'integrations' => [
        'email' => [
            'provider' => 'smtp', // 'smtp', 'mailgun', 'ses'
            'from_address' => env('MAIL_FROM_ADDRESS'),
            'from_name' => env('MAIL_FROM_NAME'),
        ],
        'sms' => [
            'provider' => 'twilio', // 'twilio', 'nexmo'
            'enabled' => false,
        ],
        'payment_gateways' => [
            'stripe' => [
                'enabled' => false,
                'public_key' => env('STRIPE_PUBLIC_KEY'),
                'secret_key' => env('STRIPE_SECRET_KEY'),
            ],
            'paypal' => [
                'enabled' => false,
                'client_id' => env('PAYPAL_CLIENT_ID'),
                'client_secret' => env('PAYPAL_CLIENT_SECRET'),
                'sandbox' => env('PAYPAL_SANDBOX', true),
            ],
        ],
        'banking' => [
            'plaid' => [
                'enabled' => false,
                'client_id' => env('PLAID_CLIENT_ID'),
                'secret' => env('PLAID_SECRET'),
                'environment' => env('PLAID_ENV', 'sandbox'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'pagination_size' => 25,
        'search_limit' => 100,
        'cache_queries' => true,
        'lazy_loading' => true,
        'compress_responses' => true,
        'cdn_enabled' => false,
        'asset_versioning' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization Settings
    |--------------------------------------------------------------------------
    */
    'localization' => [
        'supported_locales' => ['en', 'ar'],
        'default_locale' => 'en',
        'fallback_locale' => 'en',
        'rtl_locales' => ['ar'],
        'date_formats' => [
            'en' => 'M d, Y',
            'ar' => 'd/m/Y',
        ],
        'number_formats' => [
            'en' => [
                'decimal_separator' => '.',
                'thousands_separator' => ',',
            ],
            'ar' => [
                'decimal_separator' => '.',
                'thousands_separator' => ',',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    */
    'features' => [
        'multi_company' => false,
        'project_accounting' => false,
        'time_tracking' => false,
        'advanced_inventory' => false,
        'manufacturing' => false,
        'pos_integration' => false,
        'ecommerce_sync' => false,
        'api_access' => true,
        'mobile_app' => false,
        'advanced_reporting' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */
    'validation' => [
        'customer_email_unique' => true,
        'vendor_email_unique' => true,
        'product_sku_unique' => true,
        'invoice_number_unique' => true,
        'expense_number_unique' => true,
        'max_invoice_items' => 100,
        'max_file_size' => 10240, // KB
        'allowed_file_types' => ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'xls', 'xlsx'],
    ],
];
