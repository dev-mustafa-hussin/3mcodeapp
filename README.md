# Laravel ERP API

## Introduction
This is the Backend API for the ERP system, built with Laravel 12.
It provides endpoints for managing Customers, Products, Categories, Invoices, and Payments.

## Requirements
- PHP 8.2+
- Composer
- MySQL

## Installation
1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env`
4. Configure database in `.env`
5. Run `php artisan key:generate`
6. Run `php artisan migrate`
7. Run `php artisan db:seed`
8. Serve the application: `php artisan serve`

## Environment Variables
Ensure these are set in your `.env`:
```env
APP_URL=http://your-domain.com
SANCTUM_STATEFUL_DOMAINS=your-frontend-domain.com,localhost:3000
CORS_ALLOWED_ORIGINS=http://localhost:3000,https://your-frontend-domain.com
SESSION_DRIVER=database
CACHE_DRIVER=database
```

## API Documentation
See `API_DOCS.md` for detailed endpoint descriptions.

## Authentication
This API uses Laravel Sanctum for authentication.
- Register: `POST /api/v1/auth/register`
- Login: `POST /api/v1/auth/login`
- Logout: `POST /api/v1/auth/logout` (Bearer Token required)

## Features
- **Customers**: Manage customer data with tax info.
- **Products**: Manage inventory, prices, costs, and stock.
- **Categories**: Hierarchical category system.
- **Invoices**: Create invoices with automatic totals calculation and stock deduction.
- **Payments**: Track payments against invoices.
- **Dashboard**: Stats and reporting endpoints.
