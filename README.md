
````markdown
# 📁 Website Arsip Surat

Aplikasi **manajemen arsip surat** berbasis web yang dibangun dengan Laravel.  
Fokusnya untuk mengelola **surat masuk** dan **surat keluar**, mengatur **hak akses per user**, dan memudahkan tracking dokumen di lingkungan kantor / instansi.

---

## ✨ Fitur Utama

- 🔐 **Login & Role User**
  - Role: `admin` dan `staf`
  - Hanya user yang terdaftar yang bisa mengakses sistem

- 📥 **Surat Masuk**
  - Input data surat masuk (nomor, tanggal surat, tanggal terima, asal surat, perihal, ringkasan, penandatangan, tingkat penting, kategori, dll.)
  - Upload file surat (PDF / DOC / DOCX)
  - Pilih **user penerima internal** (satu atau banyak)

- 📤 **Surat Keluar**
  - Input data surat keluar (nomor, tanggal surat, tanggal keluar, tujuan, perihal, dll.)
  - Upload file surat
  - Pilih user penerima internal yang berhak melihat surat tersebut

- 👥 **Kelola User (Admin)**
  - Tambah user baru (nama, email, password, role)
  - Lihat daftar user
  - Hapus user (dengan proteksi: tidak bisa hapus diri sendiri, dan tidak bisa menghapus admin terakhir)

- 🎯 **Akses Surat per User**
  - **Admin**:
    - Bisa melihat semua surat masuk & keluar
    - Bisa mengunggah surat dan menentukan penerima
  - **Staf**:
    - Hanya bisa melihat & mengunduh surat yang memang ditujukan ke akun tersebut

- 📊 **Dashboard**
  - Admin:
    - Ringkasan jumlah surat masuk, surat keluar, total surat
    - Ringkasan per kategori
    - Ringkasan jumlah surat per user (berapa surat yang ditujukan ke tiap user)
  - Staf:
    - Ringkasan jumlah surat yang memang bisa diakses akun tersebut saja

---

## 🧱 Tech Stack

- [Laravel](https://laravel.com/)
- Blade + Tailwind (via Vite)
- MySQL / MariaDB
- PHP 8.x

---

## 🛠 Persyaratan

- PHP 8.1+
- Composer
- Node.js & npm
- MySQL / MariaDB
- Git (opsional, untuk clone repo)

---

## 🚀 Cara Menjalankan di Lokal (Windows / XAMPP)

Langkah berikut diasumsikan dijalankan di Windows (XAMPP), tapi di OS lain konsepnya sama.

### 1. Clone Repository

```bash
git clone https://github.com/suhastral3/Website-Arsip-Surat.git
cd Website-Arsip-Surat
````

### 2. Install Dependency PHP & JS

```bash
composer install
npm install
```

> Kalau pakai npm versi baru dan ada masalah dependency, bisa pakai:
>
> ```bash
> npm install --legacy-peer-deps
> ```

### 3. Buat File `.env`

Salin dari contoh:

```bash
cp .env.example .env
```

Lalu edit `.env` dan sesuaikan:

```env
APP_NAME="Arsip Surat"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000   # atau http://127.0.0.1:8000

# Koneksi database (sesuaikan dengan XAMPP / MySQL di laptop)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=arsip_surat   # buat database ini dulu di phpMyAdmin
DB_USERNAME=root          # default XAMPP
DB_PASSWORD=              # biasanya kosong di XAMPP
```

### 4. Generate App Key

```bash
php artisan key:generate
```

### 5. Jalankan Migrasi Database

```bash
php artisan migrate
```

Ini akan membuat tabel:

* `users` (dengan kolom `role`)
* `kategori_surat`
* `surat`
* `surat_user` (pivot: relasi surat ↔ user penerima)
* dan tabel bawaan Laravel lainnya.

### 6. Buat Link Storage

Agar file surat bisa diakses via URL:

```bash
php artisan storage:link
```

### 7. Jalankan Server & Vite

Di terminal 1:

```bash
php artisan serve
```

Di terminal 2:

```bash
npm run dev
```

Buka di browser:

```text
http://localhost:8000
```

---

## 👤 Akun Admin & Role

Karena fitur register public dimatikan, akun dibuat oleh **admin**.

### Opsi 1: Buat Admin lewat phpMyAdmin

1. Buka `users` di phpMyAdmin.
2. Insert baris baru, isi minimal:

   * `name`  : Admin Arsip
   * `email` : [admin@arsip.test](mailto:admin@arsip.test)
   * `password` : isi password yang sudah di-hash (bisa copy dari user lain atau pakai Tinker).
   * `role` : `admin`

### Opsi 2: Buat via Tinker

```bash
php artisan tinker
```

Lalu di dalam Tinker:

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin Arsip',
    'email' => 'admin@arsip.test',
    'password' => Hash::make('password-admin-anda'),
    'role' => 'admin',
]);
```

Setelah itu, login di halaman utama menggunakan email & password tersebut.

---

## 🧭 Alur Penggunaan Singkat

1. **Admin login**
2. **Tambahkan user**:

   * Menu: **Kelola User → Tambah User**
   * Set role: `staf` atau `admin`
3. **Upload surat**:

   * Menu: **Upload Surat**
   * Pilih tipe: **Surat Masuk** / **Surat Keluar**
   * Isi data surat + upload file
   * Pilih satu atau beberapa **penerima internal**
4. **User staf login**:

   * Hanya akan melihat surat yang ditujukan ke akun tersebut (di daftar surat & dashboard).
5. **Admin** dapat:

   * Melihat semua surat masuk & keluar
   * Mengedit surat (termasuk penerima internal)
   * Menghapus surat
   * Mengelola user

---

## 📂 Struktur Folder (Singkat)

Beberapa bagian penting:

* `app/Models/Surat.php` – model utama surat
* `app/Models/User.php` – relasi user ↔ surat (penerima, pembuat, pengubah)
* `app/Http/Controllers/SuratController.php` – logika CRUD surat & filter
* `app/Http/Controllers/AdminUserController.php` – kelola user (admin)
* `resources/views/surat/*.blade.php` – tampilan daftar, form, dan detail surat
* `resources/views/admin/users/*.blade.php` – tampilan kelola user
* `resources/views/layouts/navigation.blade.php` – menu navigasi utama

---

## 🧪 Catatan Pengembangan

* Default tampilan menggunakan Tailwind + komponen bawaan Breeze yang sudah dimodifikasi.
* Role & akses:

  * Middleware `admin` membatasi fitur tertentu hanya untuk admin.
  * Query di dashboard & daftar surat sudah menyesuaikan role (admin vs staf).
* Pivot `surat_user` digunakan untuk menyimpan **user mana saja** yang boleh mengakses suatu surat.

---

## 📝 Lisensi

Proyek ini dibuat untuk kebutuhan pembelajaran / internal.
Silakan digunakan, dimodifikasi, atau dikembangkan lebih lanjut sesuai kebutuhan.

---

Dibuat dengan ❤️ oleh **Indra Jasa Suhastra**.

```
