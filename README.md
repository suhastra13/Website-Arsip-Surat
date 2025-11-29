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

Itu tulisannya besar karena GitHub menganggap baris dengan `=== ... ===` sebagai **heading level 1** (judul paling gede).
Kita ganti jadi heading biasa (`##`, `###`) dan taruh contoh `.env` di dalam code block biar rapi.

Silakan ganti **seluruh bagian “Cara Menjalankan di Lokal”** di README dengan ini:

````markdown
## 🚀 Cara Menjalankan di Lokal (Windows + XAMPP)

### 1. Clone Repository

**Buka Git Bash:**

```bash
cd /c/xampp/htdocs
git clone https://github.com/suhastra13/Website-Arsip-Surat.git
cd Website-Arsip-Surat
````

---

### 2. Install Dependency

```bash
composer install
npm install
# Build sekali:
npm run build
# (opsional saat pengembangan)
# npm run dev
```

---

### 3. Konfigurasi Environment (.env)

Jika file `.env` belum ada, salin dulu dari contoh:

```bash
cp .env.example .env
```

Lalu buka file `.env` dengan editor (VS Code / Notepad++) dan minimal sesuaikan bagian ini:

```env
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
```

* `DB_DATABASE` harus sama dengan **nama database** yang kamu buat di phpMyAdmin.
* `DB_USERNAME` dan `DB_PASSWORD` sesuaikan dengan setting MySQL di komputer masing-masing.

---

### 4. Generate APP_KEY

```bash
php artisan key:generate
```

---

### 5. Siapkan Database

1. Buka phpMyAdmin: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Klik **Databases** → buat database baru, misalnya: `arsip_surat`.

Setelah database ada, jalankan migrasi:

```bash
php artisan migrate
```

Kalau kamu punya seeder, bisa tambahkan:

```bash
php artisan db:seed
```

---

### 6. Akun Login Default

Jika tabel `users` sudah diisi (misalnya manual lewat phpMyAdmin), beri contoh akun di README:

* Email   : `admin@arsip.test`
* Role    : `admin`
* Password: `password123`

Pengguna lain bisa mengubah / menambah user langsung dari database atau dari fitur manajemen user (kalau sudah ada di aplikasi).

---

### 7. Menjalankan Aplikasi

```bash
php artisan serve
```

Lalu buka di browser:

[http://127.0.0.1:8000](http://127.0.0.1:8000)

```

Kalau bagian lama masih ada teks seperti `=== 4. Generate APP_KEY ===`, hapus saja dan ganti dengan versi di atas.  
Setelah itu di tab **Preview** GitHub, ukuran tulisannya akan jauh lebih normal dan rapi.
::contentReference[oaicite:0]{index=0}
```

