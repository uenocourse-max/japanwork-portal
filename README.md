# Japan Work Portal

Portal lowongan kerja ke Jepang untuk siswa Indonesia yang ingin mengikuti program penempatan kerja di Jepang (SSW/Tokutei Ginou, Magang, dan Engineer/Gijinkoku). Aplikasi monolitik berbasis Laravel dengan tiga peran — Admin, Recruiter (LPK/TSK), dan Student — dalam satu codebase.

## Fitur

### Admin (`/admin`)
- Dashboard dengan 7 widget statistik (siswa, aplikasi, chart matching/JLPT/SSW/pathway)
- CRUD siswa dengan quick-action ubah status matching
- CRUD lowongan kerja dengan tab pelamar
- Manajemen lamaran dengan workflow lengkap
- CRUD LPK/TSK (otomatis membuat akun recruiter)
- Manajemen 9 kategori SSW

### Recruiter (`/recruiter`)
- Dashboard statistik lowongan & lamaran milik sendiri
- Kelola lowongan (scoped ke user login)
- Review pelamar dengan modal profil lengkap dan action inline
- Penjadwalan interview (online/offline)
- Notifikasi otomatis ke siswa setiap perubahan status

### Student (`/student` + portal publik `/jobs`)
- Registrasi & login dengan forgot/reset password
- Dashboard dengan statistik, status profil, matching, dan notifikasi
- Profil lengkap (data diri, SSW, JLPT, pathway, foto, CV)
- Cari lowongan dengan filter (SSW, JLPT, job type, lokasi, keyword) dan 4 mode tampilan
- Bookmark lowongan favorit
- Lamar lowongan dengan konfirmasi SSW mismatch
- Pelacakan progress lamaran + detail interview
- Withdraw lamaran (hanya dari status pending)
- Notifikasi database dengan mark all read

### Portal Publik
- Browse lowongan tanpa autentikasi
- Search & filter, 4 mode tampilan

### Sistem Lamaran
```
pending → reviewed → accepted → interview_scheduled → company_accepted (FINAL, auto-matching)
  │           │             │               │
  │           │             │               └── not_passed (FINAL)
  │           │             └── rejected
  │           └── rejected
  └── withdrawn (oleh siswa, hanya dari pending)
```

## Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Framework | Laravel 13 |
| PHP | 8.5 |
| Admin Panel | Filament 5.x |
| Frontend | Blade + Livewire 4 + Tailwind CSS 4 |
| Database | PostgreSQL |
| Queue | Database |
| Cache | Database |
| Mail | Log (development) |
| Testing | PHPUnit 12 |
| Code Style | Laravel Pint |

## Prerequisites

- PHP 8.5 atau lebih baru
- Composer
- Node.js & npm
- PostgreSQL

## Instalasi

```bash
# Clone repository
git clone <repository-url>
cd myapp

# Install dependencies
composer install
npm install

# Konfigurasi environment
cp .env.example .env
php artisan key:generate

# Set database PostgreSQL di .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=japan_work_portal
DB_USERNAME=<username>
DB_PASSWORD=<password>

# Jalankan migrasi & seed
php artisan migrate
php artisan db:seed

# Build frontend
npm run build
```

### Menjalankan Development Server

```bash
composer run dev
```

Perintah ini menjalankan server, queue worker, dan Vite secara bersamaan.

## Akun Default (Seeder)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Recruiter | mitrasejati@example.com | password |
| Recruiter | bintangsamudra@example.com | password |
| Recruiter | cemerlangabadi@example.com | password |
| Student | 15 akun demo | password |

## Roles & Akses

| Role | URL | Panel | Deskripsi |
|------|-----|-------|-----------|
| Admin | `/admin` | Filament | Akses penuh ke semua data dan pengaturan |
| Recruiter | `/recruiter` | Filament | Kelola lowongan dan lamaran sendiri |
| Student | `/login` | Blade | Portal pencari kerja dengan profil, lamaran, bookmark |

## Testing

Aplikasi menggunakan PHPUnit 12 dengan SQLite in-memory untuk pengujian.

```bash
# Jalankan semua test
php artisan test

# Jalankan test tertentu
php artisan test --filter=JobControllerTest

# Jalankan test di file tertentu
php artisan test tests/Feature/JobControllerTest.php
```

## Project Structure

```
├── app/
│   ├── Enums/              # Enum (JlptLevel, MatchingStatus, dll.)
│   ├── Http/
│   │   ├── Controllers/    # Controller publik & student
│   │   └── Middleware/      # Auth,EnsureStudentProfileComplete
│   ├── Models/             # Eloquent models (User, Student, JobListing, dll.)
│   ├── Notifications/      # NewApplicationReceived, ApplicationStatusChanged
│   └── Console/Commands/   # jobs:archive-expired
├── database/
│   ├── factories/          # Model factories
│   ├── migrations/         # Database migrations
│   └── seeders/            # Database seeders
├── docs/                   # Dokumentasi proyek
├── resources/
│   └── views/
│       ├── layouts/        # Blade layouts
│       ├── portal/         # Portal publik
│       ├── student/        # Portal siswa
│       └── components/     # Blade components
├── routes/
│   └── web.php             # Semua route
├── tests/                  # PHPUnit tests
├── app/Filament/
│   ├── Admin/              # Panel admin (resources, pages, widgets)
│   └── Recruiter/          # Panel recruiter (resources, pages, widgets)
└── config/
```

### Database (17 Tabel)

`users`, `students`, `ssw_categories`, `student_ssw_category`, `lpk_tsks`, `job_listings`, `job_applications`, `saved_jobs`, `notifications`, `sessions`, `cache`, `cache_locks`, `password_reset_tokens`, `jobs`, `job_batches`, `failed_jobs`, `migrations`

### Scheduled Command

| Command | Schedule | Deskripsi |
|---------|----------|-----------|
| `jobs:archive-expired` | Daily | Menutup lowongan yang sudah expired (status → closed) |

## Deployment

Aplikasi ini dapat dideploy menggunakan [Laravel Cloud](https://cloud.laravel.com/). Pastikan queue runner dan scheduler aktif untuk notifikasi dan scheduled command.

## Dokumentasi Lanjutan

- [Arsitektur Aplikasi](docs/ARCHITECTURE.md)
- [Skema Database](docs/DATABASE.md)
- [Alur Aplikasi](docs/FLOW.md)
