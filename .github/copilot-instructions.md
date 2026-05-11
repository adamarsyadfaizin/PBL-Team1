# Copilot Instructions for Berlima Guest House

## Quick Start

This is a Laravel 13 + Filament 5 application for managing a guest house with room inventory and admin dashboard.

### Commands

**Development:**
```bash
composer dev          # Runs server, queue, logs, and Vite in parallel
npm run dev          # Vite dev server (asset bundling)
```

**Testing & Quality:**
```bash
composer test        # Run all PHPUnit tests (clears config first)
./vendor/bin/pest    # Alternative test runner (PHPUnit)
./vendor/bin/pint    # Laravel Pint code formatter
```

**Setup (first time):**
```bash
composer setup       # Install deps, generate app key, run migrations, build assets
```

**Other:**
```bash
php artisan migrate          # Run pending migrations
php artisan tinker          # Interactive PHP shell
php artisan queue:listen    # Process queued jobs locally
```

## Architecture

### Core Stack
- **Framework**: Laravel 13 with PSR-4 autoloading
- **Admin UI**: Filament 5 (headless admin panel framework)
- **Frontend**: Vite + Tailwind CSS 4
- **Database**: SQLite (testing) / App configured for env
- **Language**: PHP 8.3+ with strict types enabled

### Project Structure

```
app/
  ├─ Enums/             Backed enums with Filament contracts
  ├─ Filament/          Admin resources (RoomResource, etc.)
  ├─ Http/Controllers/  Route controllers
  ├─ Models/            Eloquent models (User, Room)
  ├─ Policies/          Authorization policies
  └─ Providers/         Service providers
database/
  ├─ factories/         Model factories for testing
  ├─ migrations/        Schema migrations
  └─ seeders/           Database seeders
routes/
  ├─ web.php            Public web routes
  └─ console.php        Artisan commands
resources/
  ├─ css/               Tailwind stylesheets
  ├─ js/                JavaScript/Alpine.js code
  └─ views/             Blade templates
tests/
  ├─ Feature/           Integration tests (HTTP, database)
  └─ Unit/              Unit tests (methods, logic)
```

### Key Models & Relationships

**User**
- Role-based (admin, guest)
- Uses `isAdmin()` method to check admin status
- Password auto-hashed via cast

**Room**
- UUID primary key (not auto-increment)
- Indonesian field names (nomor_kamar, lantai, luas_m2, etc.)
- Status enum with color coding (Tersedia, Terisi, Perbaikan)
- Facilities stored as JSON array
- Price fields: harga_harian, harga_bulanan, deposit (all decimal)
- Publication flag (is_published) for landing page visibility

### Filament Admin Panel

**RoomResource** (`app/Filament/Resources/RoomResource.php`)
- Manages room CRUD via `/admin/kamar`
- Schema-based forms (Filament v5)
- Filters by status, floor level, publication status
- Bulk delete actions
- Displays all rooms (both published and unpublished)

**Routing:**
- Admin routes auto-registered via Filament service provider
- No manual route registration needed for Filament resources

## Code Conventions

### PHP
- **Strict types**: All files start with `declare(strict_types=1);`
- **Type hints**: Comprehensive type hints with PHPDoc blocks
  ```php
  /** @return array<string, string> */
  protected function casts(): array
  ```
- **Namespacing**: PSR-4 with App\ root namespace
- **Comments**: Indonesian comments for domain-specific logic (e.g., "Cek apakah user adalah administrator")

### Enums
- Implement `HasColor` and `HasLabel` for Filament integration
- Use `match()` expressions in `getLabel()` and `getColor()` methods
- Case names in PascalCase (e.g., `Tersedia`, `Terisi`)
- Case values in snake_case with Indonesian terms (e.g., `'tersedia'`, `'terisi'`)

### Models
- Use trait `HasUuids` for UUID keys where needed
- Specify `$table`, `$primaryKey`, `$incrementing`, `$keyType` explicitly if non-standard
- Define `$fillable` for mass assignment
- Use `casts()` method (not `$casts` property) for consistency
- Mark cast return types in PHPDoc: `/** @return array<string, string|class-string> */`

### Controllers
- Simple, focused actions (one public method per action)
- Return `View` type hints
- Use query builder for filtered results
- Order results explicitly (e.g., by floor, then room number)

### Database
- Migrations auto-generated and numbered by timestamp
- Factories in `database/factories/` for test data
- Seeders in `database/seeders/`
- Testing uses in-memory SQLite (`:memory:` in phpunit.xml)

### Views
- Blade templates in `resources/views/`
- Tailwind CSS for styling
- Page routes use `pages.{name}` convention (e.g., `pages.home`, `pages.contact`)

### Testing
- PHPUnit configured in `phpunit.xml`
- Two test suites: `Unit` (tests/Unit/) and `Feature` (tests/Feature/)
- Testing environment vars in phpunit.xml (SQLite :memory:, sync queue, etc.)

## Important Configuration

### Middleware & Auth
- Laravel 13 default middleware stack applied
- Authentication gate/policy pattern (see `Policies/` and `Providers/`)
- Filament auto-checks authorization via Gate::policy() registration

### Filament v5 Specifics
- **Forms**: Use `Schema` (via `form()` method), not deprecated Form Builder
- **Methods**: `EditAction`, `DeleteAction` used; `ViewAction` deprecated
- **Icons**: Heroicon icons (e.g., `heroicon-o-building-office-2`)
- **Colors**: Badge colors use Filament color names (`success`, `danger`, `warning`)
- **Sections**: `Section::make()` groups form fields with descriptions and icons
- **Record URL**: Manually set via `recordUrl()` in table

### Asset Building
- Vite entry points: `resources/css/app.css`, `resources/js/app.js`
- Tailwind CSS v4 configured in `vite.config.js`
- `refresh: true` in Vite config watches for Blade changes
- Ignored paths: `**/storage/framework/views/**`

## Debugging & Logging

- **Pail**: `php artisan pail` streams application logs in real-time
- **Tinker**: `php artisan tinker` for interactive DB queries and method testing
- **Config Cache**: Tests auto-clear config cache (see composer test)
- **Queue**: Sync driver in tests means jobs execute immediately

## Common Tasks

### Adding a New Admin Resource
1. Create model in `app/Models/`
2. Create factory in `database/factories/`
3. Create migration in `database/migrations/`
4. Create resource in `app/Filament/Resources/`
5. Use Schema-based form() and Table-based table()
6. Register policy if authorization needed

### Adding a New Public Page
1. Create route in `routes/web.php`
2. Create view in `resources/views/pages/{name}.blade.php`
3. Use Tailwind classes for styling
4. Return `view()` from controller or closure

### Running Specific Tests
```bash
./vendor/bin/pest tests/Unit/UserTest.php
./vendor/bin/pest --filter=test_user_can_login
```

### Code Formatting
```bash
./vendor/bin/pint --dirty        # Format only changed files
./vendor/bin/pint app/Models/    # Format specific directory
```

## Laravel Boost (Optional Enhancement)

The README mentions Laravel Boost for enhanced AI workflows. To install:
```bash
composer require laravel/boost --dev
php artisan boost:install
```

This provides 15+ tools and skills for agents building Laravel applications.

## Key Files to Review

- `app/Models/Room.php` — Core domain model with UUID, JSON casting
- `app/Filament/Resources/RoomResource.php` — Admin UI schema patterns
- `app/Enums/RoomStatus.php` — Filament enum integration template
- `routes/web.php` — Public route structure
- `composer.json` — Dependencies and scripts
- `phpunit.xml` — Test configuration with SQLite in-memory DB
