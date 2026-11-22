# Dokumentasi Pengerjaan Proyek — PesanAja

Dokumen ini merangkum seluruh pengerjaan proyek **PesanAja** (Sistem Pemesanan Makanan & Kasir Restoran) dari awal hingga kondisi saat ini, beserta perbandingan terhadap baseline [Proposal-PesanAja-PBWL.pdf](file:///c:/Users/Eko%20Rismanto/Downloads/Proposal-PesanAja-PBWL.pdf).

---

## 1. Kesesuaian dengan Baseline Proposal

| Aspek | Baseline Proposal | Implementasi Saat Ini | Status |
| :--- | :--- | :--- | :--- |
| **Role & Hak Akses** | 3 role (`customer`, `kasir`, `manajer`) via kolom `role` di `users` + middleware | `Role` enum, kolom `users.role`, middleware `EnsureRole` (`role:...`) | Sesuai |
| **Database Inti** | 6 tabel (`users`, `categories`, `menus`, `orders`, `order_items`, `payments`) | 6 tabel dengan relasi Eloquent, `price` bertipe integer, snapshot harga di `order_items` | Sesuai |
| **Status Pesanan** | 5 status via PHP Enum (`OrderStatus`) | `OrderStatus` enum (`menunggu_bayar`, `diproses`, `siap`, `selesai`, `batal`) + validasi transisi | Sesuai |
| **Nomor Antrian** | Dibuat saat bayar, reset harian (`A-001`, ...), `lockForUpdate()` | `OrderService::markPaid()` dengan `DB::transaction()` + `lockForUpdate()` | Sesuai |
| **Autentikasi** | Login/Register + redirect per role | `AuthController` (login, register, logout, redirect per role) | Sesuai |
| **UI/UX** | Mobile-first (Tailwind + Alpine.js), 3 role view | Layout `app.blade.php`, komponen `status-badge` & `price`, view `customer/`, `kasir/`, `manajer/` | Sesuai |
| **CRUD Manajer** | Kategori, Menu, Akun Kasir | `CategoryController`, `MenuController`, `KasirController` + view masing-masing | Sesuai |
| **Pembayaran Online** | Midtrans Snap Sandbox + Webhook | **Disesuaikan:** Virtual Payment Sandbox (`MockPaymentController` + `PaymentService`) untuk simulasi QRIS, VA BCA, GoPay, OVO tanpa API eksternal | Disesuaikan |
| **Database Engine** | Supabase PostgreSQL | SQLite lokal (`.env`) untuk pengembangan cepat; migrasi standar Laravel siap dipindah ke PostgreSQL | Disesuaikan |

---

## 2. Daftar File yang Dibuat / Diubah

### Database & Enums
- `app/Enums/Role.php`
- `app/Enums/OrderStatus.php`
- `database/migrations/2026_11_22_000001_add_role_to_users_table.php`
- `database/migrations/2026_11_22_000002_create_categories_table.php`
- `database/migrations/2026_11_22_000003_create_menus_table.php`
- `database/migrations/2026_11_22_000004_create_orders_table.php`
- `database/migrations/2026_11_22_000005_create_order_items_table.php`
- `database/migrations/2026_11_22_000006_create_payments_table.php`
- `database/seeders/DatabaseSeeder.php`

### Models
- `app/Models/User.php`
- `app/Models/Category.php`
- `app/Models/Menu.php`
- `app/Models/Order.php`
- `app/Models/OrderItem.php`
- `app/Models/Payment.php`

### Middleware, Services & Controllers
- `app/Http/Middleware/EnsureRole.php` (didaftarkan di `bootstrap/app.php`)
- `app/Services/OrderService.php`
- `app/Services/PaymentService.php`
- `app/Http/Controllers/Auth/AuthController.php`
- `app/Http/Controllers/Customer/MenuController.php`
- `app/Http/Controllers/Customer/CheckoutController.php`
- `app/Http/Controllers/Customer/MockPaymentController.php`
- `app/Http/Controllers/Customer/OrderController.php`
- `app/Http/Controllers/Kasir/QueueController.php`
- `app/Http/Controllers/Kasir/PaymentController.php`
- `app/Http/Controllers/Manajer/DashboardController.php`
- `app/Http/Controllers/Manajer/CategoryController.php`
- `app/Http/Controllers/Manajer/MenuController.php`
- `app/Http/Controllers/Manajer/KasirController.php`

### Views & Routing
- `routes/web.php` (28 route terverifikasi)
- `resources/views/layouts/app.blade.php`
- `resources/views/components/status-badge.blade.php`
- `resources/views/components/price.blade.php`
- `resources/views/auth/login.blade.php` & `register.blade.php`
- `resources/views/customer/menu.blade.php`, `orders.blade.php`, `order-status.blade.php`, `payment-mock.blade.php`
- `resources/views/kasir/queue.blade.php`
- `resources/views/manajer/dashboard.blade.php`, `categories.blade.php`, `menus.blade.php`, `kasir.blade.php`

---

## 3. Akun Demo (Seeder)
Password untuk semua akun demo: `password`
- **Manajer:** `manajer@pesanaja.test`
- **Kasir:** `kasir@pesanaja.test`
- **Customer:** `customer@pesanaja.test`

---

## 4. Catatan Menjalankan Proyek
1. Jalankan `npm install && npm run build` untuk mengompilasi aset Tailwind CSS.
2. Jalankan `php artisan migrate:fresh --seed` jika ingin mereset database dengan data awal.
3. Jalankan `php artisan serve` untuk membuka aplikasi di browser.
