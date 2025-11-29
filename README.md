# Website Arsip Surat

Aplikasi manajemen **arsip surat masuk & keluar** berbasis web, dibangun dengan Laravel.

---

## ✨ Fitur Utama

- Autentikasi user (login / logout)
- Role user:
  - **Admin** – bisa upload, edit, hapus surat
  - **Staf** – hanya melihat data (sesuai aturan yang diatur)
- Manajemen surat:
  - Surat **Masuk**
  - Surat **Keluar**
- Upload dokumen surat (PDF / Word)
- Kategori surat (Undangan, Surat Edaran, Laporan, dll.)
- Pencarian & filter surat
- Dashboard:
  - Jumlah total surat masuk & keluar
  - Ringkasan per kategori
  - Diagram perbandingan (Chart.js):
    - Surat Masuk vs Surat Keluar
    - Distribusi per kategori

---

## 🛠 Tech Stack

- **Backend**  : Laravel 12, PHP 8.2  
- **Frontend** : Blade, Tailwind CSS, Chart.js  
- **Database** : MySQL / MariaDB (XAMPP)  
- **Tools**    : Composer, NPM, Git  

> Catatan: sesuaikan versi Laravel di atas dengan `composer.json` proyek kamu.

---

## 🚀 Cara Menjalankan di Lokal (Windows + XAMPP)

# === 1. Clone Repository ===
cd /c/xampp/htdocs
git clone https://github.com/suhastra13/Website-Arsip-Surat.git
cd Website-Arsip-Surat

# === 2. Install Dependency ===
composer install
npm install
# Untuk build sekali:
npm run build
# (opsional saat develop, pakai:)
# npm run dev

# === 3. Siapkan file .env ===
# Jika .env belum ada, salin dari contoh:
cp .env.example .env

# Setelah itu EDIT file .env secara manual,
# isi minimal bagian ini (di editor, bukan lewat terminal):
# APP_NAME="Arsip Surat"
# APP_ENV=local
# APP_KEY=
# APP_DEBUG=true
# APP_URL=http://127.0.0.1:8000
#
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=arsip_surat
# DB_USERNAME=root
# DB_PASSWORD=
#
# (DB_DATABASE harus sama dengan nama database di phpMyAdmin)

# === 4. Generate APP_KEY ===
php artisan key:generate

# === 5. Siapkan Database ===
# Buat dahulu database kosong "arsip_surat" di phpMyAdmin:
#   - Buka http://localhost/phpmyadmin
#   - Klik "Databases"
#   - Buat database baru: arsip_surat

# Jalankan migrasi tabel:
php artisan migrate

# (Opsional) Kalau punya seeder:
# php artisan db:seed

# === 6. Jalankan Aplikasi ===
php artisan serve

# Lalu buka di browser:
# http://127.0.0.1:8000

