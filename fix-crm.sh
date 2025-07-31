#!/bin/bash

echo "🔧 Fixing CRM Module 500 Error"
echo "==============================="

# Clear all caches
echo "🧹 Clearing all caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check if CRM tables exist
echo "🗄️ Checking CRM database tables..."
php artisan tinker --execute="
try {
    \$contacts = \DB::table('contacts')->count();
    echo 'Contacts table exists with ' . \$contacts . ' records\n';
} catch (Exception \$e) {
    echo 'Contacts table missing: ' . \$e->getMessage() . '\n';
}

try {
    \$communications = \DB::table('communications')->count();
    echo 'Communications table exists with ' . \$communications . ' records\n';
} catch (Exception \$e) {
    echo 'Communications table missing: ' . \$e->getMessage() . '\n';
}

try {
    \$followups = \DB::table('follow_ups')->count();
    echo 'Follow-ups table exists with ' . \$followups . ' records\n';
} catch (Exception \$e) {
    echo 'Follow-ups table missing: ' . \$e->getMessage() . '\n';
}
"

# Check if CRM routes are registered
echo "🛣️ Checking CRM routes..."
php artisan route:list | grep crm || echo "No CRM routes found"

# Test CRM controller
echo "🎮 Testing CRM controller..."
php artisan tinker --execute="
try {
    \$controller = new \App\Modules\CRM\Controllers\CRMController();
    echo 'CRM Controller loaded successfully\n';
} catch (Exception \$e) {
    echo 'CRM Controller error: ' . \$e->getMessage() . '\n';
}
"

# Rebuild caches
echo "⚡ Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check if CRM models work
echo "📊 Testing CRM models..."
php artisan tinker --execute="
try {
    \$contact = new \App\Modules\CRM\Models\Contact();
    echo 'Contact model loaded successfully\n';
} catch (Exception \$e) {
    echo 'Contact model error: ' . \$e->getMessage() . '\n';
}

try {
    \$communication = new \App\Modules\CRM\Models\Communication();
    echo 'Communication model loaded successfully\n';
} catch (Exception \$e) {
    echo 'Communication model error: ' . \$e->getMessage() . '\n';
}

try {
    \$followup = new \App\Modules\CRM\Models\FollowUp();
    echo 'FollowUp model loaded successfully\n';
} catch (Exception \$e) {
    echo 'FollowUp model error: ' . \$e->getMessage() . '\n';
}
"

echo ""
echo "✅ CRM Module Fix Complete!"
echo "=========================="
echo ""
echo "🌐 Test CRM module at: /modules/crm"
echo ""
echo "If still getting 500 error, check:"
echo "1. Laravel logs: tail -f storage/logs/laravel.log"
echo "2. Web server logs"
echo "3. Database connection"
echo ""
echo "🔧 Manual fixes if needed:"
echo "1. Run: php artisan migrate --force"
echo "2. Run: php artisan db:seed --class=SuperAdminSeeder"
echo "3. Check file permissions: chmod -R 755 storage bootstrap/cache"
