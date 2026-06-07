# DOKUMENTASI SISTEM — SUMMIT PEAK ADVENTURE (GKDL)
> Aplikasi Rental Alat Outdoor berbasis Laravel 11

---

## 1. TECH STACK

| Layer | Teknologi |
|---|---|
| Framework | Laravel 11 (PHP 8.2) |
| Database | MySQL (SQLite dev) |
| Auth | Laravel Auth + Google OAuth (Socialite) |
| Notifikasi | Laravel Notifications |
| Export | CSV native PHP, PDF via HTML view |
| Frontend | Blade + Vite |

---

## 2. STRUKTUR ROLE & AKSES

Ada **3 role** dalam sistem:

```
user        → pelanggan umum
admin       → staf operasional toko
superadmin  → pemilik / executive
```

### Alur Redirect Login:
```
Login → cek peran
  superadmin → /superadmin/dashboard
  admin      → /admin/dashboard
  user       → /dashboard
```

---

## 3. MIDDLEWARE KEAMANAN

| Middleware | File | Fungsi |
|---|---|---|
| `auth` | Laravel built-in | Wajib login |
| `is_admin` | `IsAdmin.php` | Cek `peran` IN (admin, superadmin). Jika bukan → logout & redirect login |
| `is_superadmin` | `IsSuperAdmin.php` | Cek `peran = superadmin`. Jika admin biasa → redirect admin dashboard |
| `redirect_if_admin` | `RedirectIfAdmin.php` | Jika admin coba akses halaman user → redirect ke dashboard admin mereka |

---

## 4. DATABASE — TABEL & ATRIBUT

### 4.1 `users`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama_lengkap | string | |
| email | string UNIQUE | |
| nomor_telepon | string nullable | |
| password | string (hashed) | |
| peran | enum(user,admin,superadmin) | default: user |
| status_verifikasi | boolean | default: false |
| dokumen_identitas | string nullable | path file KTP/SIM |
| status_akun | enum(aktif,nonaktif,banned) | default: aktif |
| google_id | string nullable | ID dari Google OAuth |
| avatar | string nullable | URL foto dari Google |
| email_verified_at | timestamp nullable | |
| remember_token | string | |
| timestamps | — | created_at, updated_at |

### 4.2 `categories`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama_kategori | string | Contoh: Tenda, Tas, Sepatu |
| timestamps | — | |

### 4.3 `products`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| category_id | FK → categories | |
| nama_produk | string | |
| deskripsi | text nullable | |
| spesifikasi_teknis | text nullable | |
| harga_sewa | decimal(12,2) | harga per hari |
| stok_tersedia | integer | stok yang belum disewa |
| total_stok | integer | total unit yang dimiliki |
| url_gambar | string nullable | URL gambar produk |
| timestamps | — | |

### 4.4 `transactions`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | FK → users (cascade delete) | |
| tanggal_mulai | date | |
| tanggal_selesai | date | |
| tanggal_kembali_aktual | date nullable | diisi saat pengembalian |
| total_biaya | decimal(12,2) | termasuk biaya admin Rp2.500 |
| denda | decimal(12,2) default 0 | 50% harga sewa × hari telat × jumlah |
| perpanjangan_hari | integer default 0 | |
| status_perpanjangan | enum(none,pending,approved,rejected) | |
| status_transaksi | enum(menunggu, menunggu_admin, diproses, dikirim, selesai, dibatalkan) | |
| metode_pengambilan | enum(pickup,deliver) | |
| alamat_pengiriman | text nullable | wajib jika deliver |
| foto_ktp | string nullable | path file KTP |
| jenis_jaminan | enum(ktp, deposit_uang, ktp_dan_deposit) | |
| status_jaminan | enum(pending,verified,rejected) | |
| timestamps | — | |

### 4.5 `transaction_details`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| transaction_id | FK → transactions | |
| product_id | FK → products | |
| jumlah | integer | qty item |
| timestamps | — | |

### 4.6 `payments`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| transaction_id | FK → transactions (cascade delete) | |
| metode_pembayaran | enum(transfer_bank, qris, bayar_di_toko) | |
| status_pembayaran | enum(menunggu, menunggu_verifikasi, terverifikasi, ditolak) | |
| jumlah_bayar | decimal(12,2) | |
| bukti_pembayaran | string nullable | path file bukti transfer |
| timestamps | — | |

### 4.7 `carts`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | FK → users | |
| product_id | FK → products | |
| quantity | integer | |
| days | integer | jumlah hari sewa |
| timestamps | — | |

### 4.8 `wishlists`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | FK → users | |
| product_id | FK → products | |
| timestamps | — | |

### 4.9 `activity_logs`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | FK → users (null on delete) | siapa yang melakukan |
| aksi | string | kode aksi: tambah_admin, export_pdf, dll |
| deskripsi | string | keterangan human-readable |
| target_type | string nullable | model yang jadi target |
| target_id | bigint nullable | ID record target |
| ip_address | string(45) nullable | |
| timestamps | — | |

### 4.10 `payment_settings`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama_bank | string | contoh: BCA |
| nomor_rekening | string | |
| atas_nama | string | |
| is_active | boolean default true | |
| timestamps | — | |

### 4.11 `notifications` (Laravel default)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | uuid PK | |
| type | string | class Notification |
| notifiable_type | string | |
| notifiable_id | bigint | |
| data | text | JSON payload |
| read_at | timestamp nullable | |
| timestamps | — | |

---

## 5. RELASI ANTAR ENTITAS (ERD)

```
users ──< transactions ──< transaction_details >── products >── categories
                │
                └── payments
                
users ──< carts >── products
users ──< wishlists >── products
users ──< activity_logs
users ──< notifications
```

**Detail relasi:**
- `User` hasMany `Transaction`
- `Transaction` hasMany `TransactionDetail`
- `Transaction` hasOne `Payment`
- `TransactionDetail` belongsTo `Product`
- `Product` belongsTo `Category`
- `User` hasMany `Cart`, `Wishlist`, `ActivityLog`

---

## 6. FITUR LENGKAP & FUNGSINYA

### 6.1 AUTENTIKASI (semua akses)

| Fitur | Route | Controller | Fungsi |
|---|---|---|---|
| Form Login | GET /login | AuthController@showLoginForm | Tampil form, redirect jika sudah login |
| Proses Login | POST /login | AuthController@login | Validasi kredensial, buat session, redirect berdasar peran |
| Form Register | GET /register | AuthController@showRegisterForm | Form registrasi user baru |
| Proses Register | POST /register | AuthController@register | Buat user baru (peran=user), redirect ke login |
| Logout | POST /logout | AuthController@logout | Hapus session, redirect ke login |
| Login Google | GET /auth/google | SocialiteController@redirectToGoogle | Redirect ke OAuth Google |
| Callback Google | GET /auth/google/callback | SocialiteController@handleGoogleCallback | Terima data Google, buat/update user, auto-login |

**Logika Google OAuth:**
1. Cari user by email di DB
2. Jika ada → update google_id & avatar, login
3. Jika tidak ada → buat akun baru dengan random password

---

### 6.2 FITUR USER (middleware: auth + redirect_if_admin)

#### Dashboard User
- **Route:** `GET /dashboard`
- **Controller:** `OrderController@dashboard`
- **Data ditampilkan:** stat cards (sewa aktif, total pesanan, selesai, menunggu bayar), sewa aktif saat ini, 5 transaksi terakhir

#### Keranjang Belanja
| Aksi | Route | Fungsi |
|---|---|---|
| Lihat | GET /keranjang | Tampil semua item cart user |
| Tambah | POST /keranjang/{product} | Tambah produk ke cart |
| Direct Checkout | POST /keranjang/{product}/checkout | Langsung checkout 1 produk |
| Update | PUT /keranjang/{cart} | Ubah qty/hari |
| Hapus | DELETE /keranjang/{cart} | Hapus item dari cart |

#### Wishlist
| Aksi | Route | Fungsi |
|---|---|---|
| Lihat | GET /wishlist | Tampil semua wishlist |
| Toggle | POST /wishlist/{product} | Tambah/hapus dari wishlist |

#### Alur Checkout (3 langkah):
```
Step 1: GET /checkout
  → Tampil item cart + form tanggal sewa, metode pengambilan

Step 2: POST /checkout (store)
  → Simpan data ke SESSION (belum buat transaksi)
  → Redirect ke /pembayaran

Step 3: GET /pembayaran
  → Tampil ringkasan + pilih metode bayar (transfer_bank/qris/bayar_di_toko)

Step 4: POST /pembayaran (storePembayaran) ← TRANSAKSI DIBUAT DI SINI
  → DB::transaction:
     1. Buat record transactions
     2. Buat transaction_details per item cart
     3. Decrement stok_tersedia tiap produk
     4. Upload bukti (jika ada) → simpan ke storage/bukti-pembayaran
     5. Buat record payments
     6. Update status jika bukti ada → menunggu_admin
  → Hapus cart + session checkout
  → Kirim notifikasi OrderStatusUpdated
  → Redirect ke /konfirmasi/{id}

Step 5: GET /konfirmasi/{id}
  → Tampil ringkasan pesanan final
```

#### Manajemen Pesanan
| Route | Fungsi |
|---|---|
| GET /riwayat | Semua histori transaksi user |
| GET /pesanan/{id} | Detail pesanan |
| GET /pesanan/{id}/nota | Download nota digital (print) |
| POST /pesanan/{id}/batal | Batalkan pesanan (status: menunggu/menunggu_admin saja) → kembalikan stok |
| POST /pembayaran/{id}/upload | Upload ulang bukti pembayaran |

#### Perpanjangan Sewa (FR-USR-033)
| Route | Fungsi |
|---|---|
| GET /pesanan/{id}/perpanjangan | Form perpanjangan |
| POST /pesanan/{id}/perpanjangan | User ajukan perpanjangan (simpan hari + status=pending) |
| POST /pesanan/{id}/perpanjangan/approve | Admin setujui → tambah tanggal_selesai + total_biaya |
| POST /pesanan/{id}/perpanjangan/reject | Admin tolak → reset perpanjangan_hari ke 0 |

#### Konfirmasi Pengembalian & Denda (FR-USR-034)
- **Route:** `POST /pesanan/{id}/pengembalian`
- **Rumus denda:** `50% × harga_sewa_harian × jumlah_item × hari_terlambat`
- Hanya berlaku jika `tanggal_kembali_aktual > tanggal_selesai`
- Status transaksi → `selesai`

---

### 6.3 FITUR ADMIN (middleware: auth + is_admin, prefix: /admin)

#### Dashboard Admi n
- **Route:** `GET /admin/dashboard`
- Menampilkan: transaksi menunggu verifikasi, approve/reject cepat dari dashboard

#### Manajemen Inventaris
| Route | Fungsi |
|---|---|
| GET /admin/inventory | Daftar produk dengan filter kategori, stok status, search |
| GET /admin/inventory/create | Form tambah produk |
| POST /admin/inventory | Simpan produk baru (stok_tersedia = total_stok otomatis) |
| GET /admin/inventory/{id}/edit | Form edit |
| PUT /admin/inventory/{id} | Update produk (validasi stok_tersedia ≤ total_stok) |
| DELETE /admin/inventory/{id} | Hapus produk |
| POST /admin/inventory/bulk-delete | Hapus banyak produk sekaligus |
| GET /admin/inventory/export | Export CSV inventaris |

#### Manajemen Transaksi
| Route | Fungsi |
|---|---|
| GET /admin/transaksi | Daftar transaksi dengan filter status & search |
| GET /admin/transaksi/{id} | Detail transaksi (JSON untuk modal) |
| POST /admin/transaksi/{id}/approve | Approve → status=diproses, jaminan=verified, payment=terverifikasi |
| POST /admin/transaksi/{id}/reject | Reject → status=dibatalkan, kembalikan stok |
| POST /admin/transaksi/{id}/status | Update status (diproses→dikirim→selesai) |
| POST /admin/transaksi/{id}/lunas | Konfirmasi pembayaran lunas |

**Alur Status Transaksi:**
```
menunggu → menunggu_admin → diproses → dikirim → selesai
                          ↘ dibatalkan
```

#### Manajemen Pengguna
| Route | Fungsi |
|---|---|
| GET /admin/pengguna | Daftar user (filter tab: semua/aktif/diblokir) |
| GET /admin/pengguna/{id} | Detail user (JSON untuk modal) |
| POST /admin/pengguna/{id}/toggle-status | Blokir/aktifkan akun |
| POST /admin/pengguna/{id}/verifikasi | Toggle status verifikasi |
| DELETE /admin/pengguna/{id} | Hapus akun user |

#### Notifikasi Admin
- **Route:** `GET /admin/notifikasi` → Tampil semua notifikasi
- **Route:** `POST /admin/notifikasi/mark-all-read` → Tandai semua sudah dibaca

#### Pengiriman
- **Route:** `GET /admin/pengiriman` → Daftar pesanan dengan metode deliver
- **Route:** `GET /admin/pengiriman/{id}` → Detail pengiriman
- **Route:** `POST /admin/pengiriman/{id}/status` → Update status pengiriman

---

### 6.4 FITUR SUPER ADMIN (middleware: auth + is_superadmin, prefix: /superadmin)

#### Executive Dashboard
- **Route:** `GET /superadmin/dashboard`
- **Data ditampilkan:**
  - Pendapatan hari ini + persentase dibanding hari kemarin
  - Total transaksi aktif, total staf, total barang, jumlah stok tipis
  - Chart pendapatan 7 hari terakhir
  - Top 5 produk terlaris
  - Armada aktif (deliver yang sedang dikirim)
  - Peringatan stok (< 30% dari total)
  - 5 aktivitas terakhir (audit trail)

#### Manajemen Admin/Staf (CRUD)
| Route | Fungsi |
|---|---|
| GET /superadmin/admin | Daftar semua admin & superadmin |
| POST /superadmin/admin | Tambah admin baru → catat ActivityLog |
| PUT /superadmin/admin/{id} | Update data admin → catat ActivityLog |
| DELETE /superadmin/admin/{id} | Hapus admin → catat ActivityLog |
| POST /superadmin/admin/{id}/toggle-status | Aktif/nonaktifkan admin → catat ActivityLog |

> SuperAdmin **tidak bisa** diubah atau dihapus via fitur ini (dilindungi di controller)

#### Audit Trail (Activity Log)
- **Route:** `GET /superadmin/activity-log`
- Menampilkan seluruh log aktivitas sistem
- Log dicatat otomatis oleh `ActivityLog::catat()` saat: tambah/ubah/hapus admin, export laporan, backup database

#### Laporan & Ekspor
| Route | Fungsi |
|---|---|
| GET /superadmin/laporan | Dashboard laporan (filter: mingguan/bulanan/tahunan) |
| GET /superadmin/laporan/export-pdf | Export laporan ke PDF (HTML view) |
| GET /superadmin/laporan/export-excel | Export laporan ke CSV (Excel-compatible, UTF-8 BOM) |

**Data dalam laporan:** total pendapatan, rata-rata pesanan, denda terkumpul, chart bulanan 6 bulan, breakdown metode pembayaran, log penyewaan detail

#### Pengaturan Sistem
- **Route:** `GET/POST /superadmin/pengaturan` → Pengaturan umum toko

#### Pengaturan Pembayaran
| Route | Fungsi |
|---|---|
| POST /superadmin/pengaturan/payment | Tambah rekening bank |
| PUT /superadmin/pengaturan/payment/{id} | Edit rekening |
| DELETE /superadmin/pengaturan/payment/{id} | Hapus rekening |
| POST /superadmin/pengaturan/payment/{id}/toggle | Aktif/nonaktifkan rekening |

#### Backup Database
- **Route:** `POST /superadmin/backup-database`
- Primary: jalankan `mysqldump` via `exec()`
- Fallback: generate SQL dump dari PHP jika mysqldump tidak tersedia
- Download file `.sql` langsung ke browser
- Catat ke ActivityLog

---

## 7. SISTEM NOTIFIKASI

Dua class notifikasi di `app/Notifications/`:

| Class | Trigger | Isi |
|---|---|---|
| `OrderStatusUpdated` | Saat transaksi dibuat, dibatalkan, bukti diupload, pengembalian | Status terbaru transaksi |
| `RentalReminder` | Pengingat masa sewa hampir habis | Info tanggal selesai |

Notifikasi disimpan di tabel `notifications` (Laravel default) dan bisa dibaca di panel admin.

---

## 8. ALUR KEAMANAN LENGKAP

```
Request masuk
    ↓
Route matching
    ↓
Middleware stack:
  [1] auth → cek session/token, redirect ke /login jika belum login
  [2] is_admin → cek peran, logout paksa jika bukan admin
  [3] is_superadmin → cek peran=superadmin, redirect admin dashboard jika admin biasa
  [4] redirect_if_admin → jika admin coba akses route user, redirect ke dashboard admin
    ↓
Controller method
    ↓
Ownership check (manual di controller):
  - OrderController cek: transaction->user_id === Auth::id()
  - jika tidak cocok → abort(403)
    ↓
DB::transaction() untuk operasi multi-tabel (checkout)
    ↓
Response
```

**Keamanan tambahan:**
- Password di-hash dengan bcrypt (`Hash::make`)
- File upload divalidasi: `mimes:jpg,jpeg,png,pdf|max:5120`
- Session diregenerasi setelah login (`session()->regenerate()`)
- Session diinvalidasi setelah logout
- CSRF token otomatis pada semua POST/PUT/DELETE form

---

## 9. SEEDER DATA AWAL

| Seeder | Isi |
|---|---|
| `CategorySeeder` | Kategori produk (Tenda, Tas, Sepatu, dll) |
| `ProductSeeder` | Produk contoh lengkap dengan harga & stok |
| `payment_settings` (via migration) | Default rekening BCA — SUMMIT PEAK ADVENTURE |

---

## 10. RINGKASAN CONTROLLER

| Controller | Namespace | Fungsi Utama |
|---|---|---|
| AuthController | App\Http\Controllers | Login, Register, Logout |
| SocialiteController | App\Http\Controllers | Google OAuth |
| OrderController | App\Http\Controllers | Checkout flow, pesanan, perpanjangan, pengembalian |
| CartController | App\Http\Controllers | CRUD keranjang belanja |
| WishlistController | App\Http\Controllers | Toggle wishlist |
| AdminDashboardController | Admin\ | Dashboard + quick approve/reject |
| InventoryController | Admin\ | CRUD produk + export CSV |
| TransactionController | Admin\ | Manajemen transaksi + update status |
| UserController | Admin\ | Manajemen pengguna |
| NotificationController | Admin\ | Notifikasi admin |
| ShippingController | Admin\ | Manajemen pengiriman |
| SuperAdminDashboardController | SuperAdmin\ | Executive dashboard + backup DB |
| LaporanController | SuperAdmin\ | Laporan + export PDF/Excel |
| ManajemenAdminController | SuperAdmin\ | CRUD staf admin |
| PengaturanController | SuperAdmin\ | Setting toko & payment |
| ActivityLogController | SuperAdmin\ | Audit trail log |
