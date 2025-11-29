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

### 1. Clone Repository
Buka **Git Bash**:
cd /c/xampp/htdocs
git clone https://github.com/suhastra13/Website-Arsip-Surat.git
cd Website-Arsip-Surat

2. Install Dependency
composer install
npm install
npm run build   # atau "npm run dev" saat pengembangan

4. Konfigurasi Environment
Jika file .env belum ada, salin dari contoh:
cp .env.example .env
Lalu buka .env dan sesuaikan nilai penting:
APP_NAME="Arsip Surat"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=arsip_surat
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE boleh diganti nama lain, wajib sama dengan nama database di phpMyAdmin.

DB_USERNAME & DB_PASSWORD sesuaikan dengan setting MySQL di komputer masing-masing.

4. Generate APP_KEY
php artisan key:generate

6. Siapkan Database
Buka http://localhost/phpmyadmin
Klik Databases
Tambah database baru, misalnya dengan nama: arsip_surat
Setelah database dibuat, jalankan migrasi:
php artisan migrate
Kalau kamu punya file .sql berisi contoh data:
Import file .sql tersebut ke database arsip_surat lewat phpMyAdmin, atau
Tambahkan perintah php artisan db:seed di sini jika sudah membuat seeder.

6. Akun Login Default
Jika tabel users sudah diisi (misalnya manual lewat phpMyAdmin), berikan contoh akun di README:
Email : admin@arsip.test
Password: password123
Pengguna lain bisa mengubah / menambah user langsung dari database atau dari fitur manajemen user (kalau ada di aplikasi).

7. Menjalankan Aplikasi
php artisan serve
Lalu buka di browser:
http://127.0.0.1:8000
