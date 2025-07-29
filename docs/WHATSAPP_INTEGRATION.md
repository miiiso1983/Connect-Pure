# WhatsApp Integration for Invoice Notifications

## Overview

The Connect Pure ERP system now includes comprehensive WhatsApp integration that automatically sends invoice notifications with PDF attachments to customers when invoices are submitted. This feature uses the WhatsApp Business API to deliver professional invoice notifications directly to customers' WhatsApp accounts.

## Features

### ✅ **Automatic Invoice Notifications**
- **Trigger**: Automatically sends WhatsApp message when invoice is submitted
- **Content**: Professional message with invoice details
- **Attachment**: PDF invoice automatically generated and attached
- **Fallback**: Uses customer phone number if WhatsApp number not provided

### ✅ **PDF Invoice Generation**
- **Professional Layout**: Clean, branded invoice PDF
- **Company Information**: Includes company details and branding
- **Detailed Items**: Complete itemization with taxes and totals
- **Multiple Formats**: Supports various currencies and tax structures

### ✅ **WhatsApp Business API Integration**
- **Meta/Facebook API**: Uses official WhatsApp Business API
- **Media Support**: Sends both text messages and document attachments
- **Message Tracking**: Tracks message delivery status and IDs
- **Error Handling**: Comprehensive error logging and retry mechanisms

### ✅ **Admin Configuration Interface**
- **Easy Setup**: User-friendly configuration interface
- **Test Functionality**: Built-in testing tools
- **Status Monitoring**: Real-time configuration status
- **Security**: Secure token management

## Setup Instructions

### 1. WhatsApp Business API Setup

#### Prerequisites
- Facebook Developer Account
- WhatsApp Business Account
- Verified Business Profile

#### Steps
1. **Create Facebook App**
   - Go to [Facebook Developers](https://developers.facebook.com/)
   - Create a new app with WhatsApp Business API

2. **Configure WhatsApp Business**
   - Add WhatsApp product to your app
   - Set up a phone number
   - Get verification from WhatsApp

3. **Get API Credentials**
   - Access Token: From App Dashboard > WhatsApp > API Setup
   - Phone Number ID: From WhatsApp > API Setup > Phone Numbers
   - Business Account ID: From WhatsApp > API Setup > Configuration

### 2. System Configuration

#### Environment Variables
Add these to your `.env` file:

```env
WHATSAPP_ACCESS_TOKEN=your_access_token_here
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
WHATSAPP_BUSINESS_ACCOUNT_ID=your_business_account_id
WHATSAPP_WEBHOOK_VERIFY_TOKEN=your_webhook_token (optional)
```

#### Admin Interface Configuration
1. Navigate to **Admin Panel > WhatsApp Configuration**
2. Enter your API credentials
3. Test the connection with a test message
4. Verify configuration status

### 3. Customer Setup

#### WhatsApp Numbers
- Add WhatsApp numbers to customer profiles
- Format: Include country code (e.g., +966501234567)
- Fallback: System uses regular phone number if WhatsApp number not provided

## Usage

### Automatic Invoice Notifications

1. **Create Invoice**
   - Go to Accounting > Invoices > Create New
   - Fill in customer details and invoice items
   - Save as draft

2. **Submit Invoice**
   - Click "Send Invoice" button
   - System automatically:
     - Marks invoice as sent
     - Generates PDF
     - Sends WhatsApp message with PDF attachment
     - Logs delivery status

3. **Track Delivery**
   - Check invoice details for WhatsApp delivery status
   - View logs for detailed delivery information

### Manual Testing

1. **Admin Interface Testing**
   - Go to Admin > WhatsApp Configuration
   - Use "Test WhatsApp" section
   - Enter test phone number
   - Send test message

2. **Invoice Testing**
   - Create test invoice for customer with WhatsApp number
   - Submit invoice and monitor logs
   - Verify customer receives message and PDF

## Technical Implementation

### Architecture

```
Invoice Submission → Event (InvoiceSubmitted) → Listener (SendInvoiceWhatsAppMessage)
                                                      ↓
                  PDF Generation ← WhatsApp Service ← Queue Job
                                                      ↓
                                              Message + Attachment Sent
```

### Key Components

#### 1. **WhatsAppService** (`app/Services/WhatsAppService.php`)
- Handles all WhatsApp API communication
- Manages message sending and media uploads
- Provides configuration validation
- Includes error handling and logging

#### 2. **InvoicePdfService** (`app/Services/InvoicePdfService.php`)
- Generates professional PDF invoices
- Handles company branding and formatting
- Manages file storage and cleanup
- Supports multiple currencies and languages

#### 3. **Event System**
- **InvoiceSubmitted Event**: Triggered when invoice is sent
- **SendInvoiceWhatsAppMessage Listener**: Processes WhatsApp notification
- **Queue Support**: Handles background processing

#### 4. **Database Schema**
- **customers.whatsapp_number**: Customer WhatsApp number field
- **invoices.whatsapp_sent_at**: Timestamp of WhatsApp delivery
- **invoices.whatsapp_message_id**: WhatsApp message ID for tracking

### Message Template

The system sends a professional message template:

```
🧾 *Invoice from Connect Pure ERP*

📄 Invoice #: INV-2024-0001
📅 Date: 28/07/2024
⏰ Due Date: 27/08/2024
💰 Amount: 1,500.00 SAR

Dear Customer Name,

We have generated a new invoice for you. Please find the details above.

📎 The PDF invoice is attached to this message.

💳 Payment can be made through:
• Bank Transfer
• Online Payment Portal
• Cash/Cheque

📞 For any questions, please contact us.

Thank you for your business! 🙏
```

## Configuration Options

### WhatsApp Service Configuration

```php
// config/services.php
'whatsapp' => [
    'api_url' => env('WHATSAPP_API_URL', 'https://graph.facebook.com/v18.0'),
    'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
    'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
],
```

### PDF Generation Settings

```php
// Company information for PDF
'company' => [
    'name' => 'Connect Pure ERP',
    'address' => 'Company Address',
    'phone' => '+966 XX XXX XXXX',
    'email' => 'info@company.com',
    'tax_number' => 'TAX123456789',
]
```

## Security Considerations

### API Security
- **Token Protection**: Access tokens are masked in admin interface
- **Environment Variables**: Sensitive data stored in .env file
- **HTTPS Required**: All API calls use secure HTTPS connections

### Data Privacy
- **Customer Consent**: Ensure customers consent to WhatsApp notifications
- **Data Retention**: PDF files are cleaned up after sending
- **Logging**: Sensitive data is not logged in plain text

### Error Handling
- **Graceful Failures**: System continues working if WhatsApp fails
- **Retry Logic**: Failed messages can be retried
- **Fallback Options**: Email notifications as backup

## Troubleshooting

### Common Issues

#### 1. **Configuration Not Working**
- Verify all API credentials are correct
- Check WhatsApp Business API status
- Ensure phone number is verified
- Test with admin interface

#### 2. **Messages Not Sending**
- Check customer WhatsApp number format
- Verify API rate limits not exceeded
- Review error logs for specific issues
- Test with known working number

#### 3. **PDF Generation Fails**
- Check storage permissions
- Verify DomPDF installation
- Review PDF template for errors
- Check available disk space

### Logs and Monitoring

#### Log Locations
- **WhatsApp API**: `storage/logs/laravel.log`
- **Queue Jobs**: Laravel queue logs
- **PDF Generation**: Application logs

#### Monitoring Commands
```bash
# Check queue status
php artisan queue:work

# View recent logs
tail -f storage/logs/laravel.log

# Test WhatsApp service
php artisan tinker
>>> app(\App\Services\WhatsAppService::class)->isConfigured()
```

## API Rate Limits

### WhatsApp Business API Limits
- **Messages per second**: 50 (can be increased)
- **Daily message limit**: Varies by business verification
- **Media upload size**: 100MB maximum
- **Supported formats**: PDF, DOC, DOCX, XLS, XLSX

### Best Practices
- **Queue Processing**: Use queues for high-volume sending
- **Rate Limiting**: Implement delays between messages
- **Error Handling**: Retry failed messages with exponential backoff
- **Monitoring**: Track API usage and limits

## Future Enhancements

### Planned Features
- **Message Templates**: Custom message templates
- **Delivery Reports**: Enhanced delivery tracking
- **Customer Preferences**: Opt-in/opt-out management
- **Multi-language**: Localized message templates
- **Webhook Integration**: Real-time delivery status updates

### Integration Opportunities
- **Payment Links**: Include payment links in messages
- **Reminder System**: Automated payment reminders
- **Customer Support**: Two-way communication
- **Marketing**: Promotional message capabilities

## Support

For technical support or questions about the WhatsApp integration:

1. **Documentation**: Review this guide and system logs
2. **Testing**: Use the built-in test functionality
3. **Logs**: Check application logs for detailed error information
4. **Configuration**: Verify all settings in admin interface

The WhatsApp integration provides a powerful way to enhance customer communication and improve invoice delivery efficiency in your Connect Pure ERP system.
