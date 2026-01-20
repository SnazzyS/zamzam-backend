# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Zamzam Backend is a Hajj/Umrah trip management system built with Laravel 11 + Vue 3 + Inertia.js + Tailwind CSS. It manages trips, customers, buses, flights, hotels, rooms, payments, and invoices.

## Commands

### Development
```bash
npm run dev              # Start Vite dev server with hot reload
php artisan serve        # Start Laravel dev server at localhost:8000
```

### Build
```bash
npm run build            # Build frontend assets for production
```

### Database
```bash
php artisan migrate              # Run pending migrations
php artisan migrate:fresh --seed # Reset database and seed
php artisan db:seed              # Seed database
```

### Testing
```bash
php artisan test                 # Run all tests
php artisan test tests/Unit      # Run unit tests only
php artisan test tests/Feature   # Run feature tests only
php artisan test --filter=TestName  # Run specific test
```

### Linting
```bash
./vendor/bin/pint        # Fix code style (Laravel Pint)
./vendor/bin/pint --test # Check without fixing
```

## Architecture

### Stack
- **Backend:** Laravel 11.9 (PHP 8.2+)
- **Frontend:** Vue 3 (Composition API) with Inertia.js
- **Styling:** Tailwind CSS 4.1
- **Auth:** Laravel Sanctum

### Key Patterns

**Inertia.js Server-Side Routing:** Controllers return `Inertia::render('Page/Component', $props)` instead of JSON. Vue components receive data as props. No client-side router.

**Action Classes:** Business logic in `app/Actions/` (e.g., `PhotoUploadAction`, `UmrahIDGenerator`, `ReceiptNumberGenerator`). Controllers stay thin.

**Form Request Validation:** All validation in `app/Http/Requests/` classes.

**Nested Resource Routes:** RESTful structure like `/trips/{trip}/customers/{customer}/photos`.

**Pivot Tables with Extra Data:** The `customer_trip` pivot stores `bus_id`, `flight_id`, `umrah_id`, `group_id`.

**Polymorphic Invoices:** Invoices can belong to either Trips or Customers via `invoiceable_type`.

### Directory Structure
```
app/
  Actions/       # Business logic classes
  Http/
    Controllers/ # Thin controllers
    Requests/    # Form request validation
  Models/        # Eloquent models
  Observers/     # Model lifecycle hooks
resources/js/
  Components/    # Reusable Vue components
  Layouts/       # Layout components (AppLayout)
  Pages/         # Page components matching routes
    Dashboard.vue
    Hotels/
    Trips/
      Buses/
      Customers/
      Flights/
      Hotels/
  utils/         # Utilities (lattinMapping for Dhivehi keyboard)
```

### Key Models & Relationships
- **Trip** hasMany Buses, Flights, Groups; belongsToMany Customers (with pivot)
- **Customer** belongsToMany Trips, Rooms; hasMany Photos, Invoices
- **CustomerTrip** pivot with bus_id, flight_id, umrah_id, group_id
- **Invoice** morphTo invoiceable (Trip or Customer)

### Dhivehi Language Support
The app uses Dhivehi (Maldivian) language. `resources/js/utils/lattinMapping.js` provides Latin-to-Dhivehi keyboard mapping for text inputs.
