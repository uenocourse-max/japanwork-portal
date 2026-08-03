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
- Manajemen akun pengguna (CRUD, reset password, ubah role)

### Recruiter (`/recruiter`)
- Dashboard lengkap dengan:
  - 8 kartu statistik (total lowongan, aktif, total lamaran, menunggu review, diterima, jadwal interview, diterima perusahaan, ditarik)
  - Quick action buttons (Buat Lowongan, Lihat Semua Lamaran, Kelola Lowongan)
  - Doughnut chart distribusi status lamaran
  - Line chart tren lamaran masuk (30 hari terakhir)
  - Tabel interview mendatang (tanggal, tipe, lokasi)
  - Tabel performa lowongan (berdasarkan jumlah pelamar)
  - Tabel lamaran terbaru dengan avatar, JLPT, status badge, dan tanggal
- Kelola lowongan (scoped ke user login)
- Review pelamar dengan modal profil lengkap dan action inline
- Penjadwalan interview (online/offline)
- Edit jadwal interview (dengan notifikasi otomatis ke siswa)
- Profil akun (edit nama, email, password, info perusahaan)
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
- Sidebar "Lowongan Favorit" (top 5 berdasarkan jumlah pelamar)
- Custom error pages (404, 403, 500)

### Sistem Lamaran
```
pending → reviewed → accepted → interview_scheduled → company_accepted (FINAL, auto-matching)
  │           │             │               │
  │           │             │               └── not_passed (FINAL)
  │           │             └── rejected
  │           └── rejected
  └── withdrawn (oleh siswa, hanya dari pending)
```

### Keamanan
- Role `role` tidak termasuk dalam Fillable — hanya bisa diubah melalui admin panel
- Route apply hanya bisa diakses oleh siswa yang sudah melengkapi profil
- Rate limiting pada login dan registrasi
- Custom error pages (404, 403, 500)

### Domain Enums
Semua status/tipe dikelola lewat enum `app/Enums/` sebagai single source of truth:
- `ApplicationStatus` — status lamaran (pending, reviewed, accepted, interview_scheduled, company_accepted, not_passed, rejected, withdrawn)
- `JobStatus` — status lowongan (draft, open, closed, filled)
- `JobType` — jenis lowongan (magang, tg, engineer)
- `MatchingStatus` — status matching siswa
- `JlptLevel` — level JLPT (N1–N5, JFT Basic A2)

Setiap enum menyediakan `label()`, `color()`, dan `options()` untuk dipakai di Filament, Blade, controller, dan notification. Dua trait pendukung di `app/Enums/Concerns/`:
- `HasBadgeClass` — `badgeClass()` → kelas Tailwind untuk badge status (dipakai konsisten di semua halaman student)
- `HasHexColor` — `hexColor()` → kode hex untuk chart Filament (dipakai `ApplicationStatus` & `MatchingStatus`)

### UI/UX & Aksesibilitas
- Badge status memakai warna konsisten dari enum (`badgeClass()`), tidak ada lagi peta warna manual per halaman
- Navigasi responsif (hamburger menu di mobile) dengan `aria-label`, `role="alert"` pada flash message, dan konfirmasi logout
- Warna chart (doughnut/bar) diambil dari `hexColor()` enum sehingga selaras dengan badge di tabel

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

Aplikasi menggunakan PHPUnit 12 dengan SQLite in-memory untuk pengujian (17 file, 98 test, 237 assertions).

```bash
# Jalankan semua test
php artisan test --compact

# Jalankan test tertentu
php artisan test --compact --filter=JobControllerTest

# Jalankan test di file tertentu
php artisan test --compact tests/Feature/JobControllerTest.php
```

## Project Structure

```
├── app/
│   ├── Enums/              # Enum (JlptLevel, MatchingStatus, dll.) + Concerns (HasBadgeClass, HasHexColor)
│   ├── Http/
│   │   ├── Controllers/    # Controller publik & student
│   │   └── Middleware/      # Auth, EnsureStudentProfileComplete, Authenticate
│   ├── Models/             # Eloquent models (User, Student, JobListing, dll.)
│   ├── Notifications/      # ApplicationStatusChanged, InterviewScheduleChanged, NewApplicationReceived
│   └── Console/Commands/   # jobs:archive-expired
├── database/
│   ├── factories/          # Model factories
│   ├── migrations/         # Database migrations
│   └── seeders/            # Database seeders
├── docs/                   # Dokumentasi proyek
├── resources/
│   └── views/
│       ├── errors/         # Custom error pages (404, 403, 500)
│       ├── layouts/        # Blade layouts
│       ├── portal/         # Portal publik
│       ├── student/        # Portal siswa
│       └── components/     # Blade components
├── routes/
│   └── web.php             # Semua route
├── tests/                  # PHPUnit tests
├── app/Filament/
│   ├── Admin/              # Panel admin (resources, pages, widgets)
│   │   └── Resources/      # StudentResource, JobListingResource, ApplicationResource, UserResource, SswCategoryResource, LpkTskResource
│   └── Recruiter/          # Panel recruiter (resources, pages, widgets)
│       ├── Pages/          # Dashboard, Profile
│       └── Resources/      # RecruiterJobListings, RecruiterApplications
└── config/
```

### Database (17 Tabel)

`users`, `students`, `ssw_categories`, `student_ssw_category`, `lpk_tsks`, `job_listings`, `job_applications`, `saved_jobs`, `notifications`, `sessions`, `cache`, `cache_locks`, `password_reset_tokens`, `jobs`, `job_batches`, `failed_jobs`, `migrations`

### Filament Widgets

**Admin Panel (7 widgets):**
- `StudentStatsOverview` — Statistik siswa (total, matched, proses matching, menunggu hasil, belum matching)
- `ApplicationStatsOverview` — Statistik lamaran (total, menunggu, interview, diterima perusahaan, ditarik)
- `MatchingChart` — Doughnut chart status matching
- `JlptChart` — Bar chart distribusi JLPT
- `JobsBySswChart` — Bar chart lowongan per kategori SSW
- `SswChart` — Bar chart siswa per kategori SSW
- `PathwayChart` — Pie chart distribusi jalur (Mandiri/LPK)

**Admin Resources:**
- `StudentResource` — CRUD siswa dengan quick-action ubah status matching
- `JobListingResource` — CRUD lowongan dengan tab lamaran
- `ApplicationResource` — Manajemen lamaran dengan workflow
- `SswCategoryResource` — CRUD 9 kategori SSW
- `LpkTskResource` — CRUD LPK/TSK
- `UserResource` — CRUD pengguna, reset password, ubah role

**Recruiter Panel (5 widgets):**
- `RecruiterStatsOverview` — 8 kartu statistik pribadi
- `RecruiterApplicationsChart` — Doughnut chart status lamaran
- `RecruiterApplicationsTrendChart` — Line chart tren lamaran (30 hari)
- `RecruiterTopJobsWidget` — Tabel performa lowongan
- `RecruiterUpcomingInterviewsWidget` — Tabel interview mendatang

**Recruiter Pages:**
- `Dashboard` — Statistik dan chart
- `Profile` — Edit profil akun

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
