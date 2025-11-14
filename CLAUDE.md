# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

CPS Dragonfly 4.0 is a web application for managing drone inventory and flight missions. It provides RFID-based inventory tracking with check-in/check-out capabilities and drone flight mission management with QR code detection during flights.

**Tech Stack:** Laravel 8, PHP 7.3|8.0, MySQL, Blade templates, Laravel Mix

## Common Commands

### Setup
```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

### Development
```bash
# Start development server
php artisan serve

# Compile assets (watch mode)
npm run watch

# Compile assets for production
npm run production

# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Testing
```bash
# Run all tests
php artisan test
# or
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit --testsuite=Feature
./vendor/bin/phpunit --testsuite=Unit

# Run specific test file
./vendor/bin/phpunit tests/Feature/ExampleTest.php
```

### Database
```bash
# Create new migration
php artisan make:migration create_tablename_table

# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Create new seeder
php artisan make:seeder SeederName
```

## Architecture

### Core Domain Models

**Item** (`app/Models/Item.php`)
- Represents inventory items tracked via RFID
- Uses `kyslik/column-sortable` for table sorting
- Timestamps disabled
- Key fields: `item_name`, `item_code`, `command`, `status`

**Mission** (`app/Models/Mission.php`)
- Represents drone flight missions
- Primary key: `mission_id` (string, e.g., "MSN001")
- Stores flight path as JSON array
- One-to-many relationship with FlightDetail
- Key fields: `mission_id`, `start_time`, `end_time`, `flight_path`

**FlightDetail** (`app/Models/FlightDetail.php`)
- Stores QR code detections during missions
- Belongs to Mission (foreign key: `mission_id`)
- Key fields: `detected_qr_code`, `detected_time`

### Controller Organization

**ItemController** - Inventory management workflow
- `inventory()` - View all items
- `checkin()` - Check-in new items
- `checkout()` - View checked-out items
- `viewitem()` - Item details and editing
- `checkoutitem()` - Check out an item
- `recheckinitem()` - Return an item
- `itempdf()` - Generate PDF report for item

**DroneController** - Flight mission management
- `dronecontrol()` - Mission control interface
- `saveResults()` - Save mission data and flight details (expects JSON payload with mission + flight_details arrays)
- `flightReview()` - View all missions
- `showMission()` - View specific mission with flight details

**ScanController** - RFID scanning operations
- `scaninventory()` - Scan inventory interface
- `scanresult()` - Display scan results
- `setfound()` / `setlost()` - Mark items as found/lost during scanning

**RFIDController** - RFID reader connectivity
- `connectRFID()` - Establish RFID reader connection

### Authentication Flow

Uses custom authentication via `AuthController` (not Laravel Breeze/Jetstream):
- Routes: `/login`, `/registration`, `/logout`, `/dashboard`
- All authenticated routes redirect to `/login` if not authenticated
- Uses Laravel's built-in Auth facade and session management

### Frontend Structure

- **Blade Templates:** `resources/views/`
  - Main layouts in `layouts/`
  - Sidebar components in `sidebar/`
  - Auth views in `auth/`
- **Assets:** Compiled via Laravel Mix
  - JS entry: `resources/js/app.js`
  - CSS entry: `resources/css/app.css`
  - Compiled to: `public/js/` and `public/css/`

### Database Schema

**items table:**
- Inventory items with RFID tracking
- Sortable columns support
- Status field for tracking item state

**missions table:**
- Flight mission records
- `flight_path` stored as JSON
- String-based `mission_id` as primary key

**flight_details table:**
- Child records of missions
- Links QR code detections to missions
- Foreign key: `mission_id` references missions

**users table:**
- Standard Laravel user authentication

### Key Workflows

**Inventory Check-in/Check-out:**
1. Items are created via `/checkin` with RFID codes
2. Items can be checked out via `/checkout` workflow
3. Checked-out items can be returned via `recheckinitem`
4. Items can be edited via `/viewitem{id}`
5. PDF reports can be generated via `/itempdf{id}`

**Flight Mission Workflow:**
1. Create mission via `/dronecontrol` interface
2. During flight, QR codes are detected and timestamped
3. Mission data + flight details saved via POST to `/saveresult` (expects JSON with `mission` and `flight_details` keys)
4. Review missions via `/flightreview`
5. View detailed mission report via `/viewmission{id}`

**RFID Scanning Workflow:**
1. Connect to RFID reader via `/connectrfid`
2. Scan inventory via `/scaninventory`
3. View scan results and mark items as found/lost
4. Reset item status via `/resetitemtatus`

## Important Notes

- **Mission IDs:** The Mission model uses string-based primary keys (format: "MSN###"). When creating missions, ensure mission_id is provided in the request.

- **Flight Path Data:** Mission `flight_path` field is cast to array - pass as array in requests, stored as JSON in database.

- **Sortable Tables:** Item model uses `kyslik/column-sortable` package. When adding sortable columns, update both `$fillable` and `$sortable` arrays.

- **PDF Generation:** Uses `barryvdh/laravel-dompdf`. PDF views should be in Blade templates (e.g., `itempdf.blade.php`).

- **Authentication:** Custom auth implementation. Check `Auth::check()` before showing authenticated views. Redirect to `/login` with appropriate message if not authenticated.

- **Database Seeding:** Sample data available in `database/seeders/` as JSON files (`items.json`, `items2.json`, `missions.json`). Use seeders for development/testing.

## Configuration

- **Environment:** Copy `.env.example` to `.env` and configure database credentials
- **Database:** MySQL required (configured in `.env`)
- **Asset Compilation:** Run `npm run dev` or `npm run watch` during development
- **APP_KEY:** Must be generated via `php artisan key:generate`
