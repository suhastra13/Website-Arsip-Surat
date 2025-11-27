# Website Arsip Surat

Aplikasi manajemen arsip surat masuk & keluar berbasis web, dibangun dengan Laravel.

---

## Fitur Utama

- Autentikasi user (login / logout)
- Role user:
  - **Admin**: bisa upload, edit, hapus surat
  - **Staf**: hanya melihat data (sesuai aturan yang diatur)
- Manajemen surat:
  - Surat **Masuk**
  - Surat **Keluar**
- Form upload surat (PDF / Word)
- Kategori surat (Undangan, Surat Edaran, Laporan, dll)
- Pencarian & filter surat
- Dashboard:
  - Jumlah total surat masuk & keluar
  - Ringkasan per kategori
  - Diagram perbandingan (Chart.js):
    - Surat Masuk vs Surat Keluar
    - Distribusi per kategori

---

## Tech Stack

- **Backend** : Laravel 12, PHP 8.2
- **Frontend**: Blade, Tailwind CSS, Chart.js
- **Database**: MySQL / MariaDB (XAMPP)
- **Tools**   : Composer, NPM, Git

---

## Cara Menjalankan di Lokal

Panduan ini untuk Windows dengan XAMPP.

1. Clone Repository

di gitbash
cd c:/xampp/htdocs
git clone https://github.com/suhastra13/Website-Arsip-Surat.git
cd Website-Arsip-Surat

2. Install Dependency
composer install
npm install
npm run build   # atau npm run dev saat pengembangan

3. Konfigurasi Environment
(buat file env kalau belum ada)
cp .env.example .env

edit file .env
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

DB_DATABASE boleh diganti nama lain, nanti harus sama dengan nama database di phpMyAdmin.

DB_USERNAME dan DB_PASSWORD sesuaikan dengan setting MySQL di komputer masing-masing.

4. Generate APP_KEY di terminal
 php artisan key:generate

5. Siapkan Database

Buat database baru di phpMyAdmin (misal arsip_surat):

Buka http://localhost/phpmyadmin

Klik Databases

Tambah database dengan nama: arsip_surat

Setelah itu jalankan migrasi:

php artisan migrate


Kalau kamu menyediakan file .sql contoh data, pengguna juga bisa:

Import file .sql itu ke database arsip_surat lewat phpMyAdmin

Atau tambahkan perintah di sini: php artisan db:seed jika sudah membuat seeder.

6. Akun Login Default

Jika kamu sudah mengisi tabel users (misalnya manual dari phpMyAdmin), berikan contoh akun:

Email   : admin@arsip.test
Role    : admin
Password: (isi dengan password yang kamu pakai)


Pengguna lain bisa mengubah atau menambah user langsung dari database.

7. Menjalankan Aplikasi
php artisan serve


Lalu buka di browser:

http://127.0.0.1:8000

Struktur Proyek Singkat

app/Models
Model Laravel (Surat, Kategori, User, dll)

app/Http/Controllers
Controller untuk surat, dashboard, dan autentikasi.

resources/views
Blade view:

layouts/ – layout utama & sidebar

surat/ – halaman surat masuk, surat keluar, upload, detail, dll

dashboard.blade.php – tampilan dashboard & chart

routes/web.php
Routing halaman web (dashboard, surat, profil, dll).

database/migrations
Definisi struktur tabel database.

Catatan

File .env tidak di-commit ke Git (berisi konfigurasi & password lokal).

Folder vendor/ dan node_modules/ juga tidak disertakan, karena akan di-install lewat composer install dan npm install.

Untuk menjalankan di komputer lain (misal laptop teman), cukup:

Clone repo dari GitHub

Jalankan langkah 2–7 di atas
