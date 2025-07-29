# Connect Pure ERP System

A modern, bilingual (Arabic/English) ERP system built with Laravel 12, designed specifically for pharmacies and healthcare businesses.

## Features

- **Bilingual Support**: Full Arabic and English localization with RTL layout support
- **Modern UI**: Built with Tailwind CSS for a responsive, attractive design
- **Modular Architecture**: Organized into distinct modules for different business functions
- **Real-time Language Switching**: Switch between Arabic and English without page reload
- **Responsive Design**: Works seamlessly on desktop, tablet, and mobile devices

## Modules

1. **CRM (Customer Relationship Management)** - Manage customer relationships, leads, and sales pipeline
2. **Support System** - Handle customer support tickets and inquiries
3. **Accounting & Finance** - Financial management, invoicing, and reporting
4. **Performance Analytics** - Analytics and performance metrics dashboard
5. **Usage Tracker** - Track system usage and user activity
6. **Human Resources** - Employee management and HR functions
7. **Roles & Permissions** - User roles and permission management

## Technology Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Database**: SQLite (development) / MySQL (production)
- **Frontend**: Blade templates with Tailwind CSS
- **Build Tools**: Vite for asset compilation
- **Fonts**: Inter (Latin) and Noto Sans Arabic (Arabic)

## Quick Start

The application is already set up and ready to run! Just follow these steps:

1. **Install PHP dependencies**
   ```bash
   composer install
   ```

2. **Install Node.js dependencies**
   ```bash
   npm install
   ```

3. **Build Assets**
   ```bash
   npm run build
   ```

4. **Start the Development Server**
   ```bash
   php artisan serve
   ```

5. **Open in Browser**
   Visit: http://127.0.0.1:8000

## Database Configuration

### Development (Current Setup)
The system is configured to use SQLite for easy development:
```
DB_CONNECTION=sqlite
```

### Production (MySQL)
For production, update your `.env` file to use MySQL:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=connect_pure_erp
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Then create the database and run migrations:
```bash
php artisan migrate
```

## Features Showcase

### Language Switching
- Click the language dropdown in the top navigation
- Switch between English and العربية (Arabic)
- Notice the RTL layout change for Arabic

### Responsive Design
- Resize your browser window
- Test on mobile devices
- Sidebar collapses on smaller screens

### Module Navigation
- Click on any module card from the dashboard
- Use the sidebar navigation
- Each module has its own dedicated interface

## Development

### Adding New Modules
1. Create controller in `app/Modules/{ModuleName}/Controllers/`
2. Create views in `resources/views/modules/{module-name}/`
3. Add routes in `routes/web.php`
4. Add navigation link in `resources/views/layouts/sidebar.blade.php`
5. Add translations in `lang/en/erp.php` and `lang/ar/erp.php`

### Customizing UI
- Main layout: `resources/views/layouts/app.blade.php`
- Sidebar: `resources/views/layouts/sidebar.blade.php`
- Custom CSS: `resources/css/app.css`
- Tailwind config: `tailwind.config.js`

## RTL Support

The system automatically switches to RTL layout when Arabic is selected:
- Text direction changes
- Layout mirroring
- Icon and navigation adjustments
- Proper Arabic font loading

## License

This project is licensed under the MIT License.
# Connect-Pure
