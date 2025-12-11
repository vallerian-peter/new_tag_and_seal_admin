# Tag & Seal Admin Panel Setup Guide

## Overview
This is the Filament v4 admin panel for the Tag & Seal Livestock Management System. It uses the same database and models as the backend API.

## Installation & Configuration

### 1. Requirements
- PHP 8.2+
- MySQL/MariaDB
- Composer
- Laravel 12.x
- Filament v4.1.10

### 2. Database Configuration
The admin panel shares the same database with the backend API:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tag_and_seal_new
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Models & Enums
All models and enums are copied from `new_tag_and_seal_backend` to ensure consistency:

**Models:**
- User, SystemUser, Farmer
- Farm, Livestock
- Breed, Specie, LivestockType, LivestockObtainedMethod
- Country, Region, District, Ward, Village, Street, Division
- LegalStatus, IdentityCardType, SchoolLevel

**Enums:**
- UserRole (systemUser, farmer, extensionOfficer, vet, farmInvitedUser)
- UserStatus (active, notActive)

**Important:** All models use **camelCase** for field names (e.g., `firstName`, `farmerId`, `livestockTypeId`), NOT snake_case.

### 4. Authentication
**Login Configuration:**
- Custom login page at `/admin` path
- Login with **username** (not email)
- Only users with `role = 'systemUser'` and `status = 'active'` can access the admin panel

**User Model Implementation:**
```php
public function canAccessPanel(Panel $panel): bool
{
    return $this->role === UserRole::SYSTEM_USER 
        && $this->status === UserStatus::ACTIVE;
}
```

### 5. Admin User Creation
Run the seeder to create the default admin user:

```bash
php artisan db:seed --class=AdminUserSeeder
```

**Default Credentials:**
- Username: `admin`
- Email: `admin@tagandseals.com`
- Password: `password` (change in production!)
- Role: System User

### 6. Branding & UI
**Brand Name:** Tag & Seal

**Color Scheme:**
- Primary: Amber
- Secondary: Gray
- Tertiary: Indigo
- Success: Green
- Warning: Yellow
- Danger: Red
- Info: Blue

**Navigation Groups:**
1. Geographical (Countries, Regions, Districts, Wards, Villages, etc.)
2. People & Users (Users, System Users, Farmers, Extension Officers, Vets)
3. Livestock & Data (Livestock, Farms, Breeds, Species, Livestock Types)
4. Logs & Events (Activity logs, system events)
5. System & Configuration (Settings, reference data)

## Filament Resources Created

### Core Resources
- **User Resource** - User management with role-based access
- **Farmer Resource** - Farmer profiles
- **SystemUser Resource** - System user profiles
- **Farm Resource** - Farm management
- **Livestock Resource** - Livestock records

### Reference Data Resources
- **Breed Resource** - Animal breeds
- **Specie Resource** - Animal species
- **LivestockType Resource** - Types of livestock
- **LivestockObtainedMethod Resource** - How livestock was obtained

### Location Resources
- **Country Resource** - Country management
- **Region Resource** - Regional divisions
- **District Resource** - District management
- **Ward Resource** - Ward management

## Key Features

### 1. Role-Based Access Control
- Only `systemUser` role can access the admin panel
- User status must be `active`
- Implemented in `User` model via `canAccessPanel()` method

### 2. Consistent Data Models
- Models are identical to backend API models
- All field names use camelCase
- Foreign key relationships properly configured
- UUID support for offline-first mobile app sync

### 3. Auto-Generated Resources
- Filament resources generated with `--generate` flag
- Forms and tables auto-configured based on model structure
- Can be customized per resource as needed

## Development Workflow

### Starting the Server
```bash
php artisan serve
```

Access the admin panel at: `http://localhost:8000/admin`

### Creating New Resources
```bash
php artisan make:filament-resource ModelName --generate
```

### Clearing Cache
```bash
php artisan filament:optimize-clear
php artisan optimize:clear
```

### Publishing Assets
```bash
php artisan filament:assets
```

## Model Field Convention

**IMPORTANT:** All models use **camelCase** for field names to match the backend API:

✅ **Correct:**
- `firstName`, `lastName`, `middleName`
- `farmerId`, `livestockTypeId`, `breedId`
- `dateOfBirth`, `createdBy`, `updatedBy`

❌ **Incorrect (DO NOT USE):**
- `first_name`, `last_name`, `middle_name`
- `farmer_id`, `livestock_type_id`, `breed_id`
- `date_of_birth`, `created_by`, `updated_by`

## Troubleshooting

### Issue: Cannot login
**Solution:** Ensure:
1. User exists in database
2. User has `role = 'systemUser'`
3. User has `status = 'active'`
4. Password is correct

### Issue: Foreign key constraints
**Solution:** Ensure proper order when seeding:
1. Create reference data first (countries, regions, etc.)
2. Create system users
3. Create farmers
4. Create farms and livestock

### Issue: Models not found
**Solution:** Run `composer dump-autoload`

## Security Considerations

1. **Change Default Password:** Change the default admin password immediately in production
2. **Environment Variables:** Never commit `.env` file to version control
3. **Database Credentials:** Use strong passwords for database access
4. **Role Verification:** Always verify user role before granting admin access
5. **HTTPS:** Use HTTPS in production environments

## Maintenance

### Regular Tasks
1. Clear cache regularly: `php artisan optimize:clear`
2. Monitor logs: `storage/logs/laravel.log`
3. Backup database regularly
4. Keep Filament and Laravel updated

### Updating Filament
```bash
composer update filament/filament --with-all-dependencies
php artisan filament:upgrade
```

## Support & Documentation

- **Filament Docs:** https://filamentphp.com/docs
- **Laravel Docs:** https://laravel.com/docs
- **Backend API Docs:** See `new_tag_and_seal_backend/API_DOCUMENTATION.md`

## Project Structure

```
new_tag_and_seal_admin/
├── app/
│   ├── Enums/              # UserRole, UserStatus enums
│   ├── Filament/
│   │   ├── Auth/           # CustomLogin page
│   │   ├── Resources/      # Auto-generated resources
│   │   ├── Pages/          # Custom pages
│   │   └── Widgets/        # Dashboard widgets
│   ├── Models/             # Eloquent models (from backend)
│   └── Providers/
│       └── Filament/       # AdminPanelProvider
├── database/
│   └── seeders/
│       └── AdminUserSeeder.php  # Creates default admin user
├── .env                    # Environment configuration
└── ADMIN_PANEL_SETUP.md    # This file
```

## Notes

- This admin panel shares the same database as `new_tag_and_seal_backend`
- Models are kept in sync between admin panel and backend API
- All API endpoints in backend are accessible for external integrations
- The mobile app (`new_tag_and_seal_flutter_app`) syncs with the backend API

## Version Information

- **Laravel:** 12.x
- **Filament:** 4.1.10
- **PHP:** 8.2+
- **Database:** MySQL (shared with backend: `tag_and_seal_new`)

