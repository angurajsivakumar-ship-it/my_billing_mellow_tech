# 🧾 Billing & Inventory Management System (Laravel 12)

A mini billing & inventory management system built using **Laravel 12**, designed to handle invoices,
 products, stock management, tax calculation, denomination handling, PDF generation,
  email notifications, and analytical reports.

---

## 🚀 Features Overview

### ✅ Billing & Invoicing
- Create invoices with multiple products
- Automatic tax calculation per product
- Rounded total & balance return calculation
- Real-time quantity editing before bill generation
- Generate **Invoice PDF** (Dompdf)
- Invoice number auto-generation (daily sequence)

### ✅ Inventory Management
- Real-time stock deduction on billing
- Stock movement logging (`inventory_logs`)
- Prevents billing beyond available stock

### ✅ Denomination Handling
- Cash denomination breakdown displayed on UI
- Stores denomination usage per invoice
- Uses predefined denomination master table

### ✅ Customer Handling
- Auto-detect existing customers via email
- Stores new customers if not found
- Tracks repeat customers

### ✅ Email Notification (Queued)
- Sends invoice email after invoice generation
- Uses Laravel Events, Listeners & Queue
- Mailtrap supported for development testing

### ✅ Invoice Listing
- Paginated invoice list
- Search by invoice number or customer
- Indexed for performance

---

## 🛠️ Tech Stack

- **Backend:** Laravel 12, PHP 8.3
- **Frontend:** Blade, Tailwind CSS, jQuery, Axios
- **Database:** MySQL
- **PDF:** Dompdf
- **Queue:** Database Queue Driver
- **Mail:** Laravel Mail + Mailtrap

---

## 📂 Project Structure (Important Parts)
app/
├── Models/
│ ├── Invoice.php
│ ├── InvoiceItem.php
│ ├── Product.php
│ ├── Customer.php
│ ├── Denomination.php
│ ├── DenominationTransaction.php
│ └── InventoryLog.php
│
├── Services/
│ └── InvoiceService.php
│
├── Events/
│ └── InvoiceGenerated.php
│
├── Listeners/
│ └── SendInvoiceEmail.php
│
├── Http/
│ └── Controllers/
│ └── BillingController.php
│ └── CustomerController.php
│ └── ProductController.php
│ └── InvoiceController.php

---
## 🗄️ Database Design
### Important Relations
- Invoice → hasMany → InvoiceItems
- Invoice → hasMany → DenominationTransactions
- Product → hasMany → InventoryLogs
- Customer → hasMany → Invoices
---

## 📊 Advanced Analytics APIs

### ✅ Case 1: High-Variety Customers
- Customers who purchased **5+ distinct products in a single day**
- Returns **Top 5 customers**
- Includes:
  - Total amount spent
  - Total tax paid
  - Total items purchased

### ✅ Case 2: Stock Forecast
- Average daily sales (last 7 days)
- Estimated days until stock runs out
- Helps in proactive restocking

### ✅ Case 3: Repeat Customer Insights
- Customers who made a second purchase **within 7 days** of first
- Returns:
  - First purchase date
  - Second purchase date
  - Total spend
- Returns **latest 5 customers**

### ✅ Case 4: High-Demand Orders
- Top 5 most sold products (last 30 days)
- Lists **all invoices** that include these products
- Used for auditing & demand tracking

---

## 📈 Performance Optimizations

- Indexed columns:
  - `invoices.invoice_no`
  - `customers.email`
  - `customers.name`
- Aggregation-based reporting
- Minimal recalculation (uses stored totals)

---

## 🧪 Validation & Security

- Laravel Form Validation
- CSRF protected requests
- Queue-safe email dispatch
- Transaction-safe invoice creation

---

## ⚙️ Setup Instructions

```bash
git clone https://github.com/angurajsivakumar-ship-it/my_billing_mellow_tech.git
cd my_billing_mellow_tech

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate

php artisan migrate --seed

php artisan queue:table
php artisan migrate

php artisan serve
php artisan queue:work

---

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=1050c3925ad21a
MAIL_PASSWORD=f174091ad01f5d
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=billing@test.com
MAIL_FROM_NAME="Mellow Tech Billing"
