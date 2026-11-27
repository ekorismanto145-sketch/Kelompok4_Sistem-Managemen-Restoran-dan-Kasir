# Dokumentasi Pengerjaan Proyek — PesanAja

Dokumen ini merangkum seluruh pengerjaan proyek **PesanAja** (Sistem Pemesanan Makanan & Kasir Restoran) dari awal hingga kondisi terbaru, beserta perbandingannya terhadap baseline [Proposal-PesanAja-PBWL.pdf](file:///c:/Users/Eko%20Rismanto/Downloads/Proposal-PesanAja-PBWL.pdf).

---

## 1. Kesesuaian dengan Baseline Proposal

| Aspek | Baseline Proposal | Implementasi Saat Ini | Status |
| :--- | :--- | :--- | :--- |
| **Role & Hak Akses** | 3 role (`customer`, `kasir`, `manajer`) | `Role` enum, kolom `users.role`, middleware `EnsureRole` (`role:...`) | Sesuai |
| **Database Inti** | 6 tabel (`users`, `categories`, `menus`, `orders`, `order_items`, `payments`) | 6 tabel inti + kolom `users.avatar` | Sesuai |
| **Status Pesanan** | 5 status via PHP Enum (`OrderStatus`) | `OrderStatus` enum + validasi transisi `canTransitionTo()` | Sesuai |
| **Nomor Antrian** | Dibuat saat bayar, reset harian (`A-001`, ...), `lockForUpdate()` | `OrderService::markPaid()` dengan `DB::transaction()` + `lockForUpdate()` | Sesuai |
| **Autentikasi & Keamanan** | Login/Register + redirect per role | `AuthController` (validasi ketat register), `PasswordResetController` (Virtual OTP Sandbox 1 menit), custom error pages (`403`, `404`, `419`, `422`, `500`) | Ditingkatkan |
| **UI/UX** | Mobile-first (Tailwind + Alpine.js) | Layout responsif, komponen `status-badge` & `price`, sidebar slide-over Customer, POS & Histori Kasir, Dashboard & CRUD grid Manajer | Ditingkatkan |
| **Pembayaran Online** | Midtrans Snap Sandbox + Webhook | **Disesuaikan:** Virtual Payment Sandbox (`MockPaymentController` + `PaymentService`) dengan QRIS image, VA BCA, GoPay, OVO | Disesuaikan |
| **Database Engine** | Supabase PostgreSQL | MySQL lokal (`pesanaja`) di Laragon | Disesuaikan |

---

## 2. Fitur Utama yang Sudah Diimplementasikan

### Autentikasi, Keamanan & Profil
- **Login & Register:** Validasi nama huruf saja, email `@` + `.com`, password minimal 8 karakter, ikon mata (*show/hide*) di semua field password.
- **Lupa Password (Virtual OTP Sandbox):** Input email → kode OTP 6 digit tampil di banner sandbox dengan hitung mundur **60 detik real-time** → verifikasi OTP → buat password baru.
- **Profil (`/profile`):** Upload/hapus foto profil (`storage/app/public/avatars`), edit nama, email, dan role akun, serta tombol **Kembali** sesuai role.
- **Halaman Error HTTP:** `403`, `404`, `419`, `422`, dan `500` di `resources/views/errors/`.

### Customer (`/menu`, `/orders`, `/payment/{order}`)
- **Katalog Menu & Live Search:** Grid responsif (2/3/4 kolom), filter kategori, dan pencarian *real-time* tanpa tekan Enter.
- **Level Pedas & Pajak 10%:** Level 5–6 (+Rp1.000), Level 7–8 (+Rp2.000), serta perhitungan **Subtotal + Pajak (10%) = Total Pembayaran** di client & server (`OrderService`).
- **Checkout & Pembayaran:** Dine-in (nomor meja `1–200`) / Take-away; bayar tunai ke kasir (langsung tampilkan kode pembelian) atau bayar online via **Virtual Payment Sandbox** (gambar QRIS dinamis, VA BCA, GoPay, OVO). Batalkan pesanan langsung menghapus order dan kembali ke `/menu`.
- **Sidebar Slide-Over (☰) & Riwayat:** Menu, Riwayat Transaksi (bisa hapus riwayat yang berstatus `Selesai`), Profile, dan Logout.

### Kasir (`/kasir`, `/kasir/pos`, `/kasir/history`)
- **Antrian Pesanan (`/kasir`):** Papan kanban 3 kolom (*Menunggu Bayar*, *Diproses*, *Siap*) dengan jam *real-time* di header dan urutan pesanan terlama di atas.
- **Daftar Menu / POS (`/kasir/pos`):** Pemesanan langsung untuk pelanggan *walk-in* dengan gambar produk, level pedas, dan pajak 10% yang langsung berstatus dibayar tunai.
- **Histori Transaksi (`/kasir/history`):** Filter tanggal (maksimal hari ini) dan kartu yang bisa diklik untuk melihat detail item & pajak.

### Manajer (`/manajer`, `/manajer/orders`, `/manajer/menus`, `/manajer/categories`, `/manajer/kasir`, `/manajer/reports`)
- **Dashboard:** Kartu ringkasan (*Omzet*, *Pesanan*, *Rata-rata / pesanan*), grafik *Pesanan per jam*, dan daftar *Menu terlaris*.
- **Daftar Pesanan:** Filter tanggal (maksimal hari ini) dan *live search* kode pesanan.
- **CRUD Lengkap:** Kategori, Menu (grid kartu seperti customer + upload foto + edit inline), dan Akun Kasir.
- **Laporan Penjualan:** Filter rentang tanggal (maksimal hari ini), breakdown metode bayar, dan 5 menu terlaris.

---

## 3. Akun Demo (Seeder)
- **Manajer:** `manager@pesanaja.com` / `Manager123`
- **Kasir:** `kasir@pesanaja.com` / `Kasir123`
- **Customer:** `customer@pesanaja.com` / `Customer123`

---

## 4. Menjalankan Proyek
```sh
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

---

## 5. Pembaruan & Perbaikan Terbaru
- **Customer Tanpa Login:** Rute `/register` dihapus; customer langsung diarahkan ke `/menu` dan dapat langsung memesan serta melihat riwayat pesanan berbasis *session* (`guest_order_ids`).
- **Validasi Checkout Customer:** Menambahkan input **Nama Pemesan** (wajib huruf & spasi, tidak boleh spasi saja) dan **Nomor Meja** (wajib hanya untuk *Dine-in*). Keranjang desktop diposisikan di sebelah kanan menu (`lg:col-span-8` & `lg:col-span-4`).
- **Stok Menu & Status Habis:** Menambahkan kolom `stock` pada tabel `menus` (otomatis berkurang saat checkout) dan `customer_name` pada tabel `orders`. Menu dengan `stock <= 0` otomatis berlabel **HABIS** di tampilan customer.
- **Manajer (Stok & Validasi CRUD):** Menampilkan badge stok di setiap kartu menu (**Hijau** `> 100`, **Kuning** `50–100`, **Merah** `< 50`), notifikasi **Stok Menipis (< 50)** di Dashboard Manajer, serta validasi angka (`min:0`) untuk harga dan stok beserta label di form edit menu.
- **Kasir (POS Take-Away, QRIS, Struk, & Mobile Sidebar):** POS Kasir khusus *Take-away* dengan input **Nama Pembeli**, pilihan pembayaran **Tunai** dan **QRIS** (lengkap dengan barcode QRIS), halaman **Cetak Struk** (`/kasir/orders/{order}/receipt`), tampilan nama pembeli di Antrian & Histori, serta **sidebar mobile (☰)** pada semua halaman Kasir.
- **Lupa Password:** Dibatasi khusus untuk akun dengan role **Kasir** dan **Manajer**.
