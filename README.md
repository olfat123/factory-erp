# Factory ERP — Stock & Manufacturing Management System

A Laravel + FilamentPHP ERP-lite application for managing a factory that manufactures 12 products. The system covers the full production lifecycle: from raw material procurement through manufacturing to finished goods inventory, with full Arabic/English bilingual support.

---

## Features

### Core Modules
- **Materials & Inventory** — Raw materials with categories, units, minimum stock alerts, and average cost tracking
- **Suppliers** — Supplier directory with contact and tax information
- **Purchase Orders** — Full PO workflow (Draft → Approved → Partially Received → Fully Received → Closed)
- **Goods Receiving** — Partial receive support, batch creation, and automatic stock movement recording
- **Bill of Materials (BOM)** — Per-product material lists with quantities and machine assignments
- **Production Orders** — Auto-calculates required materials from BOM, validates stock, consumes materials, and generates finished goods (Draft → Approved → In Production → Completed → Cancelled)
- **Machines** — Machine registry with status tracking (Available / Running / Maintenance / Out of Service)
- **Batch Tracking** — Full traceability for material and production batches
- **Stock Movements** — Immutable ledger for all inventory changes (purchase receive, production consume, adjustments, returns)
- **Settings** — Dynamic key/value settings (approval workflows, default language, etc.)
- **Accounting Integration** — Event-driven abstraction layer ready for future ERP/accounting system integration

### Reports & Exports
- **Inventory Report** — Current stock, low-stock items, total valuation (Excel + PDF)
- **Production Report** — Orders, quantities, material costs (Excel + PDF)
- **Financial Report** — Inventory valuation with per-item cost breakdown (Excel + PDF)
- PDFs fully support Arabic RTL with connected Arabic text rendering (powered by mPDF)

### Admin Panel (FilamentPHP v4)
- Dashboard widgets: low stock alerts, pending orders, recent movements, inventory valuation
- Full table filters, search, bulk actions, and export support
- Relation managers and inline creation in forms
- Role-based access control with Filament Shield

### Multi-language
- English and Arabic with full RTL layout support
- Language switcher in the admin panel

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.4+ |
| Admin Panel | FilamentPHP v4 (Livewire v3) |
| Database | MySQL 8+ |
| Permissions | spatie/laravel-permission + bezhansalleh/filament-shield |
| Activity Log | spatie/laravel-activitylog |
| Excel Export | maatwebsite/excel |
| PDF Export | mpdf/mpdf (Arabic text shaping) |
| Local Dev | Laravel Valet |

---

## Requirements

- PHP 8.4+
- Composer
- MySQL 8+
- Node.js (for asset compilation)
- Laravel Valet (optional, for local `.test` domain)

---

## Installation

```bash
# Clone the repository
git clone <repo-url> factory-erp
cd factory-erp

# Install PHP dependencies
composer install

# Install JS dependencies
npm install && npm run build

# Copy environment file and configure
cp .env.example .env
php artisan key:generate

# Configure your database in .env, then run migrations and seeders
php artisan migrate --seed

# Create the first admin user
php artisan make:filament-user

# Assign super-admin role
php artisan shield:super-admin --user=1

# Generate Shield permissions for all resources
php artisan shield:generate --all
```

### Local development with Valet

```bash
cd factory-erp
valet link factory-erp
valet isolate php@8.4
# Access at http://factory-erp.test
```

---

## Roles & Permissions

| Role | Access |
|---|---|
| Admin | Full access |
| Warehouse Manager | Materials, inventory, stock movements |
| Production Manager | Production orders, BOM, machines |
| Purchasing Officer | Suppliers, purchase orders, goods receiving |
| Accountant | Reports, financial data |
| Viewer | Read-only access |

---

## License

MIT
