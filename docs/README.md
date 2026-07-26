# Sistem Portal Lowongan Kerja Jepang

## Overview

Aplikasi **monolitik** untuk mengelola program penempatan siswa Indonesia ke Jepang (SSW/Specified Skilled Worker). Tiga peran — **Admin**, **Recruiter**, **Student** — dalam satu aplikasi Laravel.

## Tech Stack

- **Backend**: Laravel 13 + PHP 8.5
- **Admin & Recruiter Panel**: Filament 5.x
- **Database**: PostgreSQL (Neon)
- **Frontend**: Blade + Tailwind CSS v4
- **Queue**: Database (default)
- **Mail**: Log (development)
- **Cache**: Database
- **Testing**: PHPUnit 12

## Role & Hak Akses

| Role | Panel | Login Via | Fitur |
|------|-------|-----------|-------|
| **Admin** | Filament `/admin` | Login form | CRUD semua data, dashboard statistik (7 widget), kelola lamaran |
| **Recruiter (LPK/TSK)** | Filament `/recruiter` | Login form | Posting lowongan, kelola lamaran masuk (scoped to self) |
| **Student** | Custom Blade | `/login` | Registrasi, profil, cari/lamar/bookmark lowongan, dashboard, notifikasi |

## Fitur Lengkap

### Admin Panel (`/admin`)
- **Dashboard** — 7 widget: statistik siswa & aplikasi, chart matching/JLPT/SSW/pathway
- **Manajemen Siswa** — CRUD dengan profil lengkap, quick-action ubah matching status
- **Manajemen Lowongan** — CRUD + lihat pelamar per lowongan via RelationManager
- **Manajemen Lamaran** — List semua lamaran, workflow penuh
- **Manajemen LPK/TSK** — CRUD (auto-create user recruiter)
- **Manajemen Kategori SSW** — CRUD

### Recruiter Panel (`/recruiter`)
- **Dashboard** — Statistik lowongan & lamaran milik sendiri
- **Lowongan Saya** — CRUD (scoped ke user login)
- **Lamaran Masuk** — Workflow + "Lihat Profil" modal (full profile + action inline)

### Portal Siswa (Blade)
- **Registrasi & Login** — Email + password, forgot/reset password
- **Dashboard** — Stats, profil status, matching status, recent applications, notifications
- **Profil** — View/edit + password change
- **Cari Lowongan** — Filter (SSW, JLPT, job_type, lokasi, keyword), 4 mode tampilan
- **Bookmark** — Simpan lowongan favorit
- **Daftar Lamaran** — Progress tracker + interview detail card, withdraw (khusus pending)
- **Notifikasi** — Database notification, mark all read
- **SSW Mismatch** — Confirmation modal jika melamar TG tanpa SSW sesuai

### Portal Publik (No Auth)
- Browse lowongan dengan search & filter, 4 view modes
- Sidebar "Lowongan Favorit" (top 5 berdasarkan jumlah pelamar)
- Sidebar Kategori SSW (daftar kategori dengan jumlah lowongan aktif)

## Database Tables (17)

`users`, `students`, `ssw_categories`, `student_ssw_category`, `lpk_tsks`, `job_listings`, `job_applications`, `saved_jobs`, `notifications`, `sessions`, `cache`, `cache_locks`, `password_reset_tokens`, `jobs`, `job_batches`, `failed_jobs`, `migrations`

## Akun Default (Seeder)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Recruiter | mitrasejati@example.com | password |
| Recruiter | bintangsamudra@example.com | password |
| Recruiter | cemerlangabadi@example.com | password |
| Students | (15 demo) | password |

## Development

```bash
composer run setup    # Setup awal
composer run dev      # Server + queue + vite
php artisan test      # Test
vendor/bin/pint --format agent  # Code style
```

## Application Workflow

```
pending ──► reviewed ──► accepted ──► interview_scheduled ──► company_accepted (FINAL, auto-matching)
  │            │              │                │
  │            │              │                └── not_passed (FINAL)
  │            │              └── rejected
  │            └── rejected
  └── rejected
  └── withdrawn (oleh student, hanya dari pending)
```

Lihat [FLOW.md](FLOW.md) untuk detail alur lengkap.
Lihat [ARCHITECTURE.md](ARCHITECTURE.md) untuk arsitektur sistem.
Lihat [DATABASE.md](DATABASE.md) untuk skema database.
