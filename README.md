# 🎯 Gun Range Management System

An internal staff-only management system for gun ranges, built with Laravel 11.

## Stack

| Component | Version |
|-----------|---------|
| PHP | 8.2+ |
| Laravel | 11.x |
| Auth Scaffold | Laravel Breeze (Blade + Tailwind CSS) |
| Roles & Permissions | spatie/laravel-permission ^6.0 |
| Database | MySQL 8.0 (SQLite supported for local dev) |
| Testing | PHPUnit 11 |

---

## Roles

| Role | Permissions |
|------|-------------|
| **admin** | Full access to all modules |
| **cashier** | POS, customers, memberships, rentals, receipts, reports |
| **inventory** | Products CRUD, stock adjust, inventory movements |

---

## Modules

- **Dashboard** — key stats at a glance
- **POS** (cashier/admin) — scan barcode, add items, checkout, print receipt
- **Customers** (cashier/admin) — manage customer records
- **Memberships** (cashier/admin) — create & renew 1-year memberships
- **Rentals** (cashier/admin) — view open rentals, process returns
- **Products** (inventory/admin) — CRUD, stock-in, stock adjust, movement log
- **Reports** — sales history, inventory levels

---

## Installation

### Prerequisites

- PHP 8.2+
- Composer 2.x
- Node.js 18+ & npm
- MySQL 8.0 (or SQLite for local dev)

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/narongsak-dev/rang-management.git
cd rang-management

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies and build assets
npm install && npm run build

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure your database in .env
# For MySQL:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gunrange_db
DB_USERNAME=root
DB_PASSWORD=your_password

# For SQLite (quick local dev):
DB_CONNECTION=sqlite
# (no other DB_ vars needed — uses database/database.sqlite)
touch database/database.sqlite

# 7. Run migrations and seed data
php artisan migrate --seed

# 8. Start the server
php artisan serve
```

Open http://localhost:8000 in your browser.

---

## Default Users (after seeding)

| Email | Password | Role |
|-------|----------|------|
| admin@gunrange.local | password | admin |
| cashier@gunrange.local | password | cashier |
| inventory@gunrange.local | password | inventory |

---

## Running Tests

```bash
php artisan test
```

Tests use an **in-memory SQLite** database (configured in `phpunit.xml`), so no separate test database is needed.

### Test Coverage

| Test Class | Tests |
|------------|-------|
| `POSTest` | Scan product, checkout sale/rental, insufficient stock |
| `MembershipTest` | Create, renew, duplicate prevention |
| `InventoryTest` | Stock in, adjust, role access |
| `RentalReturnTest` | Return item, rental status |
| `Auth/*` | Login, register, password reset |

---

## Business Rules

### POS Flow
1. (Optional) Search customer by citizen ID
2. Scan product barcode → product added to cart
3. `sale` type → reduces `stock_qty` on checkout
4. `rent` type → reduces `available_qty` only; creates `rental` + `rental_items`
5. `service`/`fee` type → no stock change
6. Cannot rent if `available_qty < qty` requested
7. Checkout creates `sale`, `sale_items`, `receipt`

### Membership
- New membership = 1 year from today
- Renew extends `expires_at` by 1 year (from current expiry if still active, else from today)
- Active badge shown in POS when customer found

### Rental Return
- Select pending items → process return
- `available_qty` restored per item
- Rental status set to `returned` when all items are returned

### Inventory
- **Stock In**: increases `stock_qty`; also increases `available_qty` for rent-type products
- **Adjust**: sets new `stock_qty`; adjusts `available_qty` proportionally for rent-type
- All movements logged in `inventory_movements`
- All significant actions logged in `audit_logs`

---

## Project Structure

```
app/
├── Http/Controllers/
│   ├── Auth/                   # Breeze auth controllers
│   ├── CustomerController.php
│   ├── DashboardController.php
│   ├── MembershipController.php
│   ├── POSController.php
│   ├── ProductController.php
│   ├── RentalController.php
│   └── ReportController.php
├── Models/
│   ├── AuditLog.php
│   ├── Customer.php
│   ├── InventoryMovement.php
│   ├── Membership.php
│   ├── Product.php
│   ├── Receipt.php
│   ├── Rental.php
│   ├── RentalItem.php
│   ├── Sale.php
│   ├── SaleItem.php
│   └── User.php
├── Policies/
│   └── ProductPolicy.php
├── Services/
│   ├── AuditLogService.php
│   ├── InventoryService.php
│   ├── MembershipService.php
│   ├── POSService.php
│   ├── ReceiptService.php
│   └── RentalService.php
database/
├── migrations/
├── seeders/
│   ├── DatabaseSeeder.php
│   ├── RoleSeeder.php       # Creates roles + default users
│   ├── ProductSeeder.php    # Sample gun range products
│   └── CustomerSeeder.php  # Sample customers with memberships
resources/views/
├── customers/
├── memberships/
├── pos/
├── products/
├── rentals/
└── reports/
tests/Feature/
├── InventoryTest.php
├── MembershipTest.php
├── POSTest.php
└── RentalReturnTest.php
```

---

## Security

- All routes require authentication (`auth` middleware)
- Role-based access enforced via `role:` middleware on route groups
- `ProductPolicy` restricts create/update/adjust to `inventory`/`admin` roles
- `Gate::before` allows `admin` to bypass all policy checks
- CSRF protection on all POST/PUT/DELETE forms
- Input validation on all controller methods
