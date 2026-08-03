# Database Schema

## Overview

Database menggunakan **PostgreSQL** (Neon). Semua tabel menggunakan BigIncrements untuk primary key. Soft deletes aktif di semua model utama.

---

## Tables

### 1. users
Tabel authentication untuk semua role (admin, student, recruiter).

| Column | Type | Notes |
|--------|------|-------|
| id | BIGSERIAL PK | |
| name | VARCHAR(255) NOT NULL | |
| email | VARCHAR(255) UNIQUE NOT NULL | |
| password | VARCHAR(255) NOT NULL | |
| role | VARCHAR(255) DEFAULT 'student' | 'admin', 'student', 'recruiter' (NOT mass-assignable) |
| phone_number | VARCHAR(255) NULL | |
| company_name | VARCHAR(255) NULL | Untuk recruiter |
| location | VARCHAR(255) NULL | Untuk recruiter |
| deleted_at | TIMESTAMP NULL | Soft deletes |
| created_at | TIMESTAMP NULL | |
| updated_at | TIMESTAMP NULL | |

**Security Note:** `role` field is NOT in the Fillable array to prevent mass assignment attacks. Role can only be changed via admin panel.

### 2. students
Profil lengkap siswa. Relasi 1:1 dengan users via `user_id`.

| Column | Type | Notes |
|--------|------|-------|
| id | BIGSERIAL PK | |
| user_id | BIGINT FK → users(id) | CASCADE delete |
| full_name | VARCHAR(255) NOT NULL | |
| age | INTEGER NOT NULL | |
| birth_place | VARCHAR(255) NOT NULL | |
| birth_date | DATE NOT NULL | |
| address | TEXT NOT NULL | |
| height_cm | INTEGER NOT NULL | |
| weight_kg | INTEGER NOT NULL | |
| blood_type | VARCHAR(255) NOT NULL | |
| gender | VARCHAR(255) NOT NULL | 'male', 'female' |
| marital_status | VARCHAR(255) NOT NULL | 'single', 'married' |
| phone_number | VARCHAR(255) UNIQUE NOT NULL | |
| participant_status | VARCHAR(255) NOT NULL | 'ex', 'new_comer' |
| jft_score | INTEGER NULL | |
| jlpt_level | VARCHAR(255) NULL | `JlptLevel` enum: 'N5','N4','N3','N2','N1','JFT Basic A2' |
| japanese_learning_months | INTEGER NOT NULL | |
| pathway | VARCHAR(255) NOT NULL | 'mandiri', 'lpk' |
| lpk_name | VARCHAR(255) NULL | |
| matching_status | VARCHAR(255) DEFAULT 'not_matched' | `MatchingStatus` enum: 'not_matched','process_matching','waiting_result','matched','cancelled' |
| matched_company_name | VARCHAR(255) NULL | |
| photo_drive_url | VARCHAR(255) NULL | Google Drive link |
| cv_drive_url | VARCHAR(255) NULL | Google Drive link |
| deleted_at | TIMESTAMP NULL | Soft deletes |

**Indexes:** user_id, matching_status, jlpt_level, pathway, participant_status, phone_number (unique)

### 3. ssw_categories
Kategori Specified Skilled Worker (9 jenis).

**Default data:** Kaigo, Food Service, Agriculture, Construction, Manufacturing, Building Cleaning, Hotel, Aviation, Fishery

### 4. student_ssw_category (Pivot)
Many-to-many students ↔ ssw_categories. Composite PK: (student_id, ssw_category_id).

### 5. lpk_tsks
Data LPK/TSK. Linked ke users table via user_id untuk autentikasi recruiter.

### 6. job_listings
Lowongan kerja yang diposting oleh recruiter.

| Column | Type | Notes |
|--------|------|-------|
| id | BIGSERIAL PK | |
| title | VARCHAR(255) NOT NULL | |
| description | TEXT NOT NULL | |
| requirements | TEXT NULL | |
| salary_min | INTEGER NULL | JPY |
| salary_max | INTEGER NULL | JPY |
| location | VARCHAR(255) NOT NULL | 47 prefektur Jepang |
| company_name | VARCHAR(255) NOT NULL | |
| company_description | TEXT NULL | |
| thumbnail_url | VARCHAR(255) NULL | Google Drive / URL |
| ssw_category_id | BIGINT FK → ssw_categories(id) | Required untuk job_type = 'tg' |
| jlpt_level_required | VARCHAR(255) NULL | `JlptLevel` enum |
| participant_status_required | VARCHAR(255) NULL | 'ex', 'new_comer', 'any' |
| status | VARCHAR(255) DEFAULT 'draft' | `JobStatus` enum: 'draft', 'open', 'closed', 'filled' |
| job_type | VARCHAR(255) DEFAULT 'tg' | `JobType` enum: 'magang', 'tg', 'engineer' |
| posted_by | BIGINT FK → users(id) | |
| deadline | DATE NULL | |
| deleted_at | TIMESTAMP NULL | Soft deletes |

**Indexes:** status, ssw_category_id, posted_by, deadline, title, location, company_name

### 7. job_applications
Aplikasi/lamaran siswa ke lowongan.

| Column | Type | Notes |
|--------|------|-------|
| id | BIGSERIAL PK | |
| job_listing_id | BIGINT FK → job_listings(id) | CASCADE delete |
| student_id | BIGINT FK → students(id) | CASCADE delete |
| status | VARCHAR(255) DEFAULT 'pending' | `ApplicationStatus` enum — lihat status workflow |
| notes | TEXT NULL | |
| applied_at | TIMESTAMP NOT NULL | |
| reviewed_at | TIMESTAMP NULL | |
| interview_type | VARCHAR(255) NULL | 'online', 'offline' |
| interview_date | TIMESTAMP NULL | |
| interview_location | VARCHAR(255) NULL | |
| interview_notes | TEXT NULL | |
| deleted_at | TIMESTAMP NULL | Soft deletes |

**Status Workflow:**
- `pending` — Menunggu review (bisa di-withdraw student)
- `reviewed` — Sudah direview recruiter
- `accepted` — Diterima untuk interview (preliminary)
- `interview_scheduled` — Interview sudah dijadwalkan
- `company_accepted` — Diterima perusahaan (FINAL, auto-matching)
- `not_passed` — Tidak lolos (FINAL)
- `rejected` — Ditolak
- `withdrawn` — Ditarik siswa (hanya dari pending)

**Unique:** (job_listing_id, student_id)

### 8. saved_jobs
Bookmark lowongan oleh siswa.

| Column | Type | Notes |
|--------|------|-------|
| id | BIGSERIAL PK | |
| student_id | BIGINT FK → students(id) | |
| job_listing_id | BIGINT FK → job_listings(id) | |
| deleted_at | TIMESTAMP NULL | Soft deletes |

**Unique:** (student_id, job_listing_id)

---

## Relationships

```
users ──────1:1────── students
users ──────1:1────── lpk_tsks
users ──────1:N────── job_listings.posted_by

students ────M:N────── ssw_categories  (via student_ssw_category)
students ────1:N────── job_applications
students ────1:N────── saved_jobs

ssw_categories ──1:N── job_listings

job_listings ──1:N──── job_applications
job_listings ──1:N──── saved_jobs
```

---

## Performance Indexes

13 indexes across students (5), job_listings (4), job_applications (3 + 1 unique).
