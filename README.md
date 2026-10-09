# 🚀 PAKAINI - Social Media Exchange Platform
> **Platform Tukar Interaksi Media Sosial #1 di Indonesia — Siap Deploy ke Railway & Supabase (PostgreSQL)**

Website **PAKAINI** dibangun menggunakan arsitektur **PHP 8.3 OOP & Clean MVC**, antarmuka modern responsif berbasis **Tailwind CSS**, dan telah dimigrasikan dari MySQL ke **PostgreSQL (Supabase)** dengan dukungan deploy otomatis melalui **Railway & GitHub**.

---

## 📦 Keunggulan Arsitektur Terbaru (PostgreSQL + Railway)
* **Zero Error 500 & Loading Kilat:** Menggunakan infrastruktur cloud container Railway dan performa tinggi PostgreSQL di Supabase (menghilangkan limitasi shared hosting InfinityFree).
* **PDO PostgreSQL Native:** Menggunakan driver `pdo_pgsql` dengan prepared statements anti SQL Injection.
* **Tunggal Connection String:** Cukup tempel satu baris URI koneksi Supabase (`DATABASE_URL`), sistem langsung aktif!
* **Struktur Tabel Lengkap (PostgreSQL):** Dilengkapi tabel `users`, `tasks`, `task_completions`, `task_history`, `transactions`, `reports`, `settings`, dan `referral_claims`.
* **CI/CD Cloud Ready:** Otomatis mendeteksi `Dockerfile`, `railway.json`, `nixpacks.toml`, dan `.htaccess`.

---

## 🛠️ Langkah Migrasi & Deploy ke Supabase & Railway

### 1. Persiapan Database Supabase
1. Buka dan buat proyek gratis baru di [Supabase.com](https://supabase.com).
2. Setelah proyek siap, buka menu **SQL Editor** di sidebar Supabase.
3. Buka file [`database.sql`](file:///e:/aplikasi%20pakaini/database.sql), salin seluruh isinya, tempel ke SQL Editor Supabase, lalu klik tombol **Run**.
4. Seluruh tabel, indeks, sequence, dan data akun awal akan otomatis terbentuk.
5. Buka menu **Project Settings** &rarr; **Database** &rarr; cari bagian **Connection String**.
6. Pilih tab **URI** (atau Pooling URI port 6543 / Direct port 5432) dan salin URI tersebut:
   ```
   postgresql://postgres.[PROJECT-REF]:[PASSWORD]@aws-0-ap-southeast-1.pooler.supabase.com:6543/postgres
   ```

### 2. Konfigurasi Lokal (Opsional)
Buka file [`config/database.php`](file:///e:/aplikasi%20pakaini/config/database.php), lalu tempel URI Supabase Anda pada baris:
```php
$supabase_url = 'postgresql://postgres.xxx:password@aws-0-ap-southeast-1.pooler.supabase.com:6543/postgres';
```

### 3. Unggah ke GitHub
Jadikan folder proyek ini satu repositori utuh di GitHub:
```bash
git init
git add .
git commit -m "feat: migrate pakaini to postgresql supabase and railway deployment"
git branch -M main
git remote add origin https://github.com/USERNAME-ANDA/pakaini.git
git push -u origin main
```

### 4. Deploy Otomatis di Railway
1. Masuk ke [Railway.app](https://railway.app) dan login dengan akun GitHub Anda.
2. Klik tombol **New Project** &rarr; pilih **Deploy from GitHub repo**.
3. Pilih repositori **pakaini** yang baru saja Anda push.
4. Railway akan otomatis mendeteksi [`Dockerfile`](file:///e:/aplikasi%20pakaini/Dockerfile) atau [`railway.json`](file:///e:/aplikasi%20pakaini/railway.json) bawaan paket ini.
5. Buka tab **Variables** di proyek Railway Anda, lalu tambahkan:
   * **`DATABASE_URL`** : *(Tempelkan Connection String URI dari Supabase)*
6. Buka tab **Settings** &rarr; bagian **Networking** &rarr; klik **Generate Domain** untuk mendapatkan URL website publik Anda (contoh: `pakaini-production.up.railway.app`).
7. Selesai! Website Anda langsung aktif dan 100% bebas error.

---

## 🔐 Akun Pengujian Bawaan (Default Seed Data)
* **Super Admin Utama:**
  * Username: `adminzul` atau `admin`
  * Password: `admin123`
  * Role: Super Admin (Akses Panel Admin, Saldo 50.000 Koin, Status VIP Sultan)
* **Member VIP Demo:**
  * Username: `sultan_vip`
  * Password: `admin123`
  * Role: Member Biasa (Status VIP Sultan, Saldo 12.500 Koin)
* **Member Biasa Demo:**
  * Username: `member_biasa`
  * Password: `admin123`
  * Role: Member Reguler (Batas 50 klik/hari, Saldo 850 Koin)

---

## 🗄️ Struktur File & Folder Proyek

```text
aplikasi-pakaini/
├── app/
│   ├── Controllers/             # Kontroller MVC (Admin, Auth, Task, Shop, Report, dll)
│   ├── Core/                    # Core Router, Model, Auth, Controller Engine
│   └── Models/                  # Model PostgreSQL (User, Task, Report, Setting, Transaction)
├── config/
│   ├── config.php               # Konfigurasi konstanta ekonomi & paket koin
│   └── database.php             # Mesin PDO PostgreSQL terpusat (Supabase URL Parser)
├── views/                       # Blade-style PHP Views (Admin, Tasks, Auth, Shop, Community)
├── storage/                     # Penyimpanan cache & session log
├── assets/                      # Asset CSS, JS, logo, dan font
├── Dockerfile                   # Konfigurasi container PHP 8.3 Apache + pdo_pgsql untuk Railway
├── entrypoint.sh                # Script binding dynamic PORT Railway
├── railway.json                 # Pengaturan deployment otomatis Railway
├── nixpacks.toml                # Konfigurasi builder alternatif Nixpacks
├── Procfile                     # Runner proses web cloud
├── database.sql                 # Skema tabel & seed data PostgreSQL untuk Supabase
├── install.php                  # Web installer interaktif 1-klik untuk Supabase
├── index.php                    # Front Controller & Landing Page
├── login.php                    # Halaman Otentikasi Masuk
├── register.php                 # Halaman Pendaftaran Akun Baru
├── dashboard.php                # Dashboard Member & Ringkasan Koin
├── earn_coins.php               # Halaman Kerja Tugas & Klaim Hadiah
├── add_task.php                 # Halaman Pasang Kampanye Iklan
├── panel_pusat_pakaini_99x.php  # Panel Darurat Super Admin
├── .gitignore                   # Aturan pengabaian file sensitif / cache
└── .env.example                 # Contoh template environment variables
```

---

## 🛡️ Kebijakan Ekonomi & Keamanan Sistem
1. **1 Dompet Tunggal (Koin Murni):** Seluruh perputaran saldo menggunakan 1 kolom tunggal `coins` yang fleksibel dan transparan.
2. **Anti-Tuyul & Anti-Double Claim:** Pengerjaan tugas dilindungi oleh tabel `task_history` dan `task_completions` dengan constraint unique index permanen.
3. **Anti-Unfollow & Laporan:** Pembuat tugas dapat melaporkan kecurangan melalui tabel `reports`. Verifikasi admin mengenakan denda 3x lipat CPC ke pelaku.
4. **Buku Kas Lengkap:** Setiap koin masuk dan keluar otomatis tercatat ke tabel `transactions`.
