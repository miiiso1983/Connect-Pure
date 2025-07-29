# 🚀 Accounting Module Setup Guide

## 📋 Overview

This guide will help you set up the comprehensive Accounting Module for your Laravel application. The module provides QuickBooks Online-style functionality with full bilingual support (English/Arabic).

## ✅ Prerequisites

- Laravel 10+ or Laravel 11
- PHP 8.1+
- MySQL 8.0+ or PostgreSQL 13+
- Node.js 16+ (for frontend assets)
- Composer 2.0+

## 🔧 Installation Steps

### 1. Database Setup

Run the migrations to create all accounting tables:

```bash
php artisan migrate
```

This will create the following tables:
- `chart_of_accounts` - Account hierarchy
- `customers` - Customer management
- `vendors` - Vendor/supplier management
- `products` - Product and service catalog
- `invoices` & `invoice_items` - Invoice management
- `expenses` - Expense tracking
- `payments` & `payment_applications` - Payment processing
- `employees` & `payroll_runs` - Payroll management
- `journal_entries` & `journal_entry_lines` - Manual entries
- `tax_rates` - Tax configuration
- `recurring_transactions` - Automated transactions
- `bank_accounts` & `bank_transactions` - Banking
- `currency_rates` - Multi-currency support

### 2. Seed Sample Data

Populate the database with sample data for testing:

```bash
php artisan db:seed --class=AccountingSeeder
```

This will create:
- Basic chart of accounts structure
- Sample customers and vendors
- Product and service catalog
- Sample invoices and expenses
- Tax rate configurations

### 3. Install Frontend Dependencies

Install Chart.js for dashboard analytics:

```bash
npm install chart.js
npm run build
```

### 4. Configure Environment

Add these variables to your `.env` file:

```env
# Accounting Module Configuration
ACCOUNTING_DEFAULT_CURRENCY=USD
ACCOUNTING_DEFAULT_TAX_RATE=8.25
ACCOUNTING_DEFAULT_PAYMENT_TERMS=net_30
ACCOUNTING_INVOICE_PREFIX=INV-
ACCOUNTING_EXPENSE_PREFIX=EXP-
ACCOUNTING_CUSTOMER_PREFIX=CUST
ACCOUNTING_VENDOR_PREFIX=VEND

# Email Configuration (for invoice sending)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourcompany.com
MAIL_FROM_NAME="Your Company Name"

# Optional: Payment Gateway Integration
STRIPE_PUBLIC_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...
PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_CLIENT_SECRET=your_paypal_secret
PAYPAL_SANDBOX=true

# Optional: Banking Integration
PLAID_CLIENT_ID=your_plaid_client_id
PLAID_SECRET=your_plaid_secret
PLAID_ENV=sandbox
```

### 5. Clear Cache

Clear all caches to ensure the new module is properly loaded:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## 🌐 Accessing the Module

### Dashboard
Visit: `/modules/accounting/`

The dashboard provides:
- Financial overview with key metrics
- Interactive charts (Revenue vs Expenses, Expense Categories)
- Recent activity (invoices, expenses, payments)
- Smart alerts and notifications
- Quick action buttons

### Main Sections

1. **Invoices** (`/modules/accounting/invoices/`)
   - Create, edit, and manage invoices
   - Send invoices via email
   - Track payment status
   - Generate PDF invoices

2. **Customers** (`/modules/accounting/customers/`)
   - Customer profiles and contact information
   - Payment terms and credit limits
   - Customer statements and aging reports

3. **Vendors** (`/modules/accounting/vendors/`)
   - Vendor management
   - Purchase tracking
   - Vendor aging reports

4. **Products & Services** (`/modules/accounting/products/`)
   - Product catalog management
   - Inventory tracking
   - Pricing and cost management

5. **Expenses** (`/modules/accounting/expenses/`)
   - Expense recording and categorization
   - Approval workflows
   - Receipt management

6. **Payments** (`/modules/accounting/payments/`)
   - Payment recording
   - Payment allocation to invoices
   - Multiple payment methods

7. **Reports** (`/modules/accounting/reports/`)
   - Profit & Loss Statement
   - Balance Sheet
   - Cash Flow Statement
   - Trial Balance
   - Customer/Vendor Aging
   - Custom date ranges and filters

8. **Chart of Accounts** (`/modules/accounting/chart-of-accounts/`)
   - Account hierarchy management
   - Account balances and transactions
   - Account type configuration

9. **Payroll** (`/modules/accounting/payroll/`)
   - Employee management
   - Payroll processing
   - Tax calculations

## 🔐 Permissions Setup

The module includes comprehensive permission system. Add these permissions to your user roles:

### Basic Permissions
- `accounting.access` - Access to accounting module
- `accounting.dashboard.view` - View dashboard

### Invoice Permissions
- `accounting.invoices.view` - View invoices
- `accounting.invoices.create` - Create invoices
- `accounting.invoices.edit` - Edit invoices
- `accounting.invoices.delete` - Delete invoices
- `accounting.invoices.send` - Send invoices

### Expense Permissions
- `accounting.expenses.view` - View expenses
- `accounting.expenses.create` - Create expenses
- `accounting.expenses.edit` - Edit expenses
- `accounting.expenses.approve` - Approve expenses
- `accounting.expenses.view_all` - View all expenses

### Customer/Vendor Permissions
- `accounting.customers.manage` - Manage customers
- `accounting.vendors.manage` - Manage vendors

### Product Permissions
- `accounting.products.manage` - Manage products

### Report Permissions
- `accounting.reports.financial` - View financial reports
- `accounting.reports.management` - View management reports

### Advanced Permissions
- `accounting.chart_of_accounts.manage` - Manage chart of accounts
- `accounting.payroll.manage` - Manage payroll
- `accounting.payroll.process` - Process payroll
- `accounting.settings.manage` - Manage settings

## 🌍 Language Configuration

### Supported Languages
- English (en) - Left-to-right
- Arabic (ar) - Right-to-left with full RTL support

### Language Files
- `lang/en/accounting.php` - English translations
- `lang/ar/accounting.php` - Arabic translations

### Switching Languages
Users can switch languages using the language selector in the application header.

## 📊 Dashboard Features

### Quick Stats
- Monthly Revenue with growth indicators
- Monthly Expenses with trend analysis
- Net Income calculation
- Outstanding Invoices summary

### Interactive Charts
- Revenue vs Expenses (12-month trend)
- Expense Categories (doughnut chart)
- Cash Flow visualization
- Invoice Status distribution

### Recent Activity
- Latest invoices with status
- Recent expenses with approval status
- Payment history
- System alerts

### Smart Alerts
- Overdue invoices
- Low stock products
- Pending expense approvals
- Draft invoices ready to send

## 🔧 Customization

### Configuration
Edit `config/accounting.php` to customize:
- Default settings (currency, tax rates, payment terms)
- Number formatting
- Invoice and expense settings
- Payment methods
- Tax configuration
- Multi-currency settings
- Security settings
- Feature flags

### Views
Customize views in `resources/views/modules/accounting/`:
- Dashboard layout
- Invoice templates
- Report formats
- Email templates

### Styling
Customize CSS in `public/css/accounting.css`:
- Color schemes
- Layout adjustments
- RTL support
- Responsive design

## 🧪 Testing

### Sample Data
The seeder creates realistic sample data for testing:
- 3 sample customers
- 2 sample vendors
- 3 sample products/services
- Multiple sample invoices
- Various sample expenses

### Test Scenarios
1. Create and send an invoice
2. Record a payment
3. Create and approve an expense
4. Generate financial reports
5. Test multi-currency transactions
6. Test RTL language support

## 🚨 Troubleshooting

### Common Issues

1. **Routes not working**
   ```bash
   php artisan route:clear
   php artisan config:clear
   ```

2. **Views not loading**
   ```bash
   php artisan view:clear
   ```

3. **Translations not showing**
   ```bash
   php artisan cache:clear
   ```

4. **Charts not displaying**
   - Ensure Chart.js is installed: `npm install chart.js`
   - Check browser console for JavaScript errors

5. **Permission denied errors**
   - Verify user has required permissions
   - Check Gate definitions in AccountingServiceProvider

### Database Issues

1. **Migration errors**
   ```bash
   php artisan migrate:rollback
   php artisan migrate
   ```

2. **Foreign key constraints**
   - Ensure proper migration order
   - Check database engine supports foreign keys

### Performance Issues

1. **Slow queries**
   - Add database indexes
   - Enable query caching
   - Optimize relationships

2. **Large datasets**
   - Implement pagination
   - Add search filters
   - Use lazy loading

## 📈 Next Steps

### Recommended Enhancements
1. Set up automated backups
2. Configure email templates
3. Integrate payment gateways
4. Set up recurring transaction automation
5. Configure tax rate automation
6. Implement advanced reporting
7. Set up mobile app access
8. Configure API access for integrations

### Integration Opportunities
1. E-commerce platforms
2. CRM systems
3. HR modules
4. Document management
5. Business intelligence tools

## 📞 Support

For technical support or questions:
1. Check the documentation
2. Review error logs
3. Test with sample data
4. Verify configuration settings
5. Contact development team

---

## ✅ Verification Checklist

- [ ] Database migrations completed
- [ ] Sample data seeded
- [ ] Frontend dependencies installed
- [ ] Environment variables configured
- [ ] Cache cleared
- [ ] Dashboard accessible
- [ ] Invoices can be created
- [ ] Payments can be recorded
- [ ] Reports generate correctly
- [ ] Language switching works
- [ ] RTL support functional
- [ ] Charts display properly
- [ ] Permissions configured
- [ ] Email sending works
- [ ] PDF generation works

**Congratulations! Your Accounting Module is now ready for use! 🎉**
