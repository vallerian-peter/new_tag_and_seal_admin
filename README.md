# Tag & Seal Admin Panel

**Laravel Filament v4 Admin Panel** for the Tag & Seal Livestock Management System.

## Quick Start

### 1. Install Dependencies
```bash
composer install
```

### 2. Configure Environment
Copy `.env.example` to `.env` and update database credentials:
```env
DB_DATABASE=tag_and_seal_new
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Create Admin User
```bash
php artisan db:seed --class=AdminUserSeeder
```

Default credentials:
- **Username:** `admin`
- **Email:** `admin@tagandseals.com`
- **Password:** `password`

### 5. Start Server
```bash
php artisan serve
```

Access admin panel at: **http://localhost:8000/admin**

## Features

- ✅ **Filament v4.1.10** - Latest version with modern UI
- ✅ **Custom Login** - Username-based authentication
- ✅ **Role-Based Access** - Only systemUser role can access
- ✅ **Shared Database** - Uses same DB as backend API (`tag_and_seal_new`)
- ✅ **CamelCase Models** - Consistent with backend API (no snake_case)
- ✅ **Navigation Groups** - Organized by category
- ✅ **Auto-Generated Resources** - CRUD for all main models

## Resources Available

### Core Management
- Users, System Users, Farmers
- Farms & Livestock
- Breeds, Species, Livestock Types

### Location Management
- Countries, Regions, Districts, Wards

### Reference Data
- Legal Status, Identity Card Types, School Levels
- Livestock Obtained Methods

## Documentation

See **[ADMIN_PANEL_SETUP.md](ADMIN_PANEL_SETUP.md)** for complete setup guide and documentation.

## Important Notes

⚠️ **Model Field Names:**
- All models use **camelCase** (e.g., `firstName`, `farmerId`)
- DO NOT use snake_case (e.g., `first_name`, `farmer_id`)
- Models are synced with backend API

⚠️ **Access Control:**
- Only users with `role = 'systemUser'` can login
- User must have `status = 'active'`

⚠️ **Database:**
- Shares database with `new_tag_and_seal_backend`
- Database name: `tag_and_seal_new`

## Related Projects

- **Backend API:** `/Applications/XAMPP/xamppfiles/htdocs/new_tag_and_seal_backend`
- **Flutter App:** `/Applications/XAMPP/xamppfiles/htdocs/new_tag_and_seal_flutter_app`

## Tech Stack

- Laravel 12.x
- Filament v4.1.10
- PHP 8.2+
- MySQL
- Laravel Sanctum

## Support

For detailed documentation, troubleshooting, and configuration options, see [ADMIN_PANEL_SETUP.md](ADMIN_PANEL_SETUP.md).
