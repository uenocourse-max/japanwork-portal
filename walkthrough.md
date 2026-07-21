# Walkthrough: Sistem Portal Lowongan Kerja Jepang

## Prerequisites

- PHP 8.3+
- Composer
- Node.js & npm
- PostgreSQL (atau SQLite untuk development)
- Git

---

## 1. Setup

```bash
# Clone repository
git clone <repo-url>
cd myapp

# Install dependencies PHP
composer install

# Install dependencies JS
npm install

# Copy environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

## 2. Konfigurasi Database

Edit `.env` sesuai environment Anda:

**PostgreSQL (Neon):**
```
DB_CONNECTION=pgsql
DB_HOST=ep-xxx-xxx.region.aws.neon.tech
DB_PORT=5432
DB_DATABASE=myapp
DB_USERNAME=xxx
DB_PASSWORD=xxx
```

**SQLite (development):**
```
DB_CONNECTION=sqlite
```

## 3. Jalankan Migrations & Seeders

```bash
# Buat tabel
php artisan migrate

# Seed admin + SSW categories + LPK/TSK + demo data
php artisan db:seed
```

## 4. Build Frontend

```bash
# Development (hot-reload)
npm run dev

# Production
npm run build
```

## 5. Jalankan Server

```bash
php artisan serve
```

Aplikasi bisa diakses di: `http://localhost:8000`

---

## Akun Demo

### Public Portal (Tanpa Login)
| URL | Deskripsi |
|-----|-----------|
| `http://localhost:8000` | Halaman utama - browse lowongan |
| `http://localhost:8000/jobs` | Daftar semua lowongan |
| `http://localhost:8000/jobs/{id}` | Detail lowongan |

### Admin
| Field | Value |
|-------|-------|
| URL | `http://localhost:8000/admin` |
| Email | `admin@example.com` |
| Password | `password` |

### Recruiter (LPK/TSK)
| Field | Value |
|-------|-------|
| URL | `http://localhost:8000/recruiter/login` |
| Email | `mitrasejati@example.com` |
| Password | `password` |

> **Note:** Recruiter tidak bisa login melalui halaman `/login` (student login). Gunakan `/recruiter/login`.

Email lain:
- `bintangsamudra@example.com`
- `cemerlangabadi@example.com`

### Siswa (15 akun demo)
| Email | Password | JLPT | Matching Status |
|-------|----------|------|-----------------|
| ahmad.rizky@example.com | password | N4 | not_matched |
| siti.nurhaliza@example.com | password | N5 | process_matching |
| budi.santoso@example.com | password | N3 | waiting_result |
| dewi.lestari@example.com | password | N2 | matched |
| eko.prasetyo@example.com | password | - | not_matched |
| fitriani@example.com | password | N5 | not_matched |
| gunawan.wibowo@example.com | password | N4 | process_matching |
| hana.permata@example.com | password | N3 | not_matched |
| indra.kusuma@example.com | password | N1 | matched |
| joko.widodo@example.com | password | N5 | not_matched |
| kartika.sari@example.com | password | JFT Basic A2 | waiting_result |
| lukman.hakim@example.com | password | N4 | not_matched |
| maya.angelina@example.com | password | N3 | process_matching |
| nanda.pratama@example.com | password | - | not_matched |
| omar.daniel@example.com | password | N2 | matched |

---

## Data Demo

### Lowongan Kerja (12 lowongan)
| Judul | Perusahaan | Lokasi | SSW | JLPT | Gaji/bulan |
|-------|------------|--------|-----|------|------------|
| Perawat Lansia (Kaigo) | Nihon Care Service | Tokyo | Kaigo | N5 | ¥180,000 - ¥250,000 |
| Staff Restoran Jepang | Sakura Restaurant Group | Osaka | Food Service | N4 | ¥170,000 - ¥230,000 |
| Pekerja Pertanian | Hokkaido Farm Cooperative | Hokkaido | Agriculture | - | ¥160,000 - ¥220,000 |
| Konstruksi Bangunan | Chubu Construction Corp | Nagoya | Construction | N5 | ¥200,000 - ¥300,000 |
| Cleaning Staff | Yokohama Clean Service | Yokohama | Building Cleaning | - | ¥155,000 - ¥200,000 |
| Staff Hotel Bintang 5 | Grand Hotel Fukuoka | Fukuoka | Hotel | N3 | ¥185,000 - ¥260,000 |
| Pekerja Pabrik Elektronik | Tohoku Electronics | Sendai | Manufacturing | N5 | ¥170,000 - ¥220,000 |
| Nelayan Perikanan | Shizuoka Fisheries Co | Shizuoka | Fishery | - | ¥190,000 - ¥320,000 |
| Staff Bandara | Narita Airport Services | Chiba | Aviation | N2 | ¥220,000 - ¥300,000 |
| Perawat Lansia (Sapporo) | Hokkaido Care Plus | Sapporo | Kaigo | N4 | ¥190,000 - ¥270,000 |
| Chef Jepang | Kobe Beef Restaurant | Kobe | Food Service | N3 | ¥210,000 - ¥350,000 |
| Warehouse Staff | Chubu Logistics | Hamamatsu | Manufacturing | N5 | ¥165,000 - ¥210,000 |

### SSW Categories (9 kategori)
Kaigo, Food Service, Agriculture, Construction, Manufacturing, Building Cleaning, Hotel, Aviation, Fishery

### LPK/TSK (3 LPK)
| Nama | Email | Lokasi |
|------|-------|--------|
| LPK Mitra Sejati | mitrasejati@example.com | Jakarta Selatan |
| LPK Bintang Samudra | bintangsamudra@example.com | Surabaya |
| LPK Cemerlang Abadi | cemerlangabadi@example.com | Bandung |

---

## Fitur per Role

### Public (Guest)
- Browse semua lowongan tanpa login
- Filter berdasarkan kategori SSW, JLPT, lokasi
- 4 mode tampilan: detail, compact, list, grid (via `?view=` param)
- Pagination: 12/24/48 item per halaman (via `?per_page=` param)
- Thumbnail support (Google Drive link auto-converted)
- Lihat detail lengkap lowongan
- CTA "Daftar & Lamar" di halaman detail
- Auto-submit search dengan debounce

### Admin (`/admin`)
| Fitur | Deskripsi |
|-------|-----------|
| Dashboard | Total siswa, statistik matching, JLPT, SSW, grafik lowongan, statistik lamaran per status |
| Students | CRUD siswa, pencarian, filter, upload dokumen |
| Job Listings | Kelola lowongan kerja dari seluruh recruiter + thumbnail + job type |
| Applications | Lihat semua lamaran, review → accept → jadwal interview → input hasil |
| Job Listing Detail | Lihat applicant per lowongan via ApplicationsRelationManager |
| LPK/TSK | Kelola data LPK/TSK (otomatis buat akun recruiter) |
| SSW Categories | Kelola kategori SSW |

### Recruiter (`/recruiter/login`)
| Fitur | Deskripsi |
|-------|-----------|
| Dashboard | Welcome message, statistik lowongan & lamaran + jadwal interview, lamaran terbaru, lowongan saya |
| Job Listings | Kelola lowongan yang diposting sendiri (scoped) + thumbnail + job type |
| Applications | Lihat lamaran untuk lowongan sendiri — review → accept → jadwalkan interview → input hasil interview |

### Siswa (`/student/*`)
| Fitur | URL | Deskripsi |
|-------|-----|-----------|
| Profil | `/student/profile` | Lihat data diri |
| Edit Profil | `/student/profile/edit` | Ubah data diri (JLPT: N5, N4, N3, N2, N1, JFT Basic A2) |
| Ganti Password | `/student/password` | Ubah password |
| Lowongan Kerja | `/student/jobs` | Browse & filter lowongan (4 mode: detail/compact/list/grid) |
| Detail Lowongan | `/student/jobs/{id}` | Lihat detail & melamar + thumbnail |
| Lamaran Saya | `/student/applications` | Riwayat lamaran & status + detail interview jika sudah dijadwalkan |

---

## Alur Kerja

### Guest → Siswa
```
Browse Lowongan (tanpa login) → Klik "Daftar & Lamar" → Register → Lengkapi Profil → Apply → Cek Status
```

### Recruiter
```
Login (/recruiter/login) → Dashboard → Buat Lowongan → Tunggu Siswa Melamar
→ Lihat Lamaran → Review → Accept → Jadwalkan Interview → Input Hasil
  (Diterima Perusahaan → Auto-matching / Tidak Lolos)
```

### Admin
```
Login → Dashboard → Review Lamaran → Accept → Jadwalkan Interview → Input Hasil
  (Diterima Perusahaan → Auto-matching / Tidak Lolos)
```

---

## Fitur Tambahan

### Thumbnail Support
- Field `thumbnail_url` pada job listings (nullable)
- Google Drive link otomatis di-convert ke direct image URL
- Ditampilkan di portal publik, halaman detail, dan admin/recruiter tables

### View Modes (Portal & Student Jobs)
- `?view=detail` — Card detail dengan thumbnail besar (default)
- `?view=compact` — Card kompak dengan thumbnail kecil
- `?view=list` — List view tanpa thumbnail
- `?view=grid` — Grid view dengan thumbnail kecil

### Interview Workflow
- **Status flow**: pending → reviewed → accepted (preliminary) → interview_scheduled → company_accepted / not_passed
- `accepted` adalah status preliminary (belum update matching)
- `interview_scheduled` menyimpan: interview_type (online/offline), interview_date, interview_location, interview_notes
- `company_accepted` adalah final — otomatis update `matching_status = matched` + `matched_company_name`
- `not_passed` adalah final — tidak ada update matching
- Semua perubahan status mengirim notifikasi ke student (database + email via queue)

### Lihat Profil (Recruiter)
- Tombol "Lihat Profil" di tabel lamaran menampilkan modal full profile student
- Menggunakan `schema()` (bukan `modalContent()`) untuk konten modal
- Menggunakan `extraModalFooterActions()` (bukan `modalFooter()`) untuk action buttons
- Action di dalam profile: Jadwalkan Interview, Terima Perusahaan, Tidak Lolos, Tolak
- Action menyesuaikan status: hanya tombol yang relevan yang tampil

### Job Type
- Setiap lowongan memiliki `job_type`: `magang`, `tg`, `engineer`
- `magang` — semua siswa boleh melamar
- `engineer` — semua siswa boleh melamar
- `tg` — hanya siswa dengan SSW kategori sesuai yang bisa melamar
- Jika siswa melamar `tg` tanpa SSW sesuai: muncul confirmation modal (tidak diblokir)

### PostgreSQL Compatibility
- Check constraints (role, jlpt_level) di-guard dengan `DB::getDriverName() === 'pgsql'`
- Aman digunakan dengan SQLite untuk testing

### Auto-Submit Search
- Search filter di portal dan student jobs auto-submit dengan debounce
- Menggunakan `@push('scripts')` + `@stack('scripts')` pattern

---

## Testing

```bash
# Jalankan semua test
php artisan test

# Jalankan test tertentu
php artisan test --filter=JobListingTest

# Code style check
vendor/bin/pint --test

# Fix code style
vendor/bin/pint
```

---

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| `ViteManifest` error | Jalankan `npm run build` atau `npm run dev` |
| Database connection error | Cek konfigurasi DB di `.env` |
| Migration already exists | Jalankan `php artisan migrate:fresh --seed` |
| Data demo tidak muncul | Jalankan `php artisan db:seed --class=DemoSeeder` |
| LSP errors di IDE | Abaikan — false positive dari generic type inference |
| `modalContent()` error | Di Filament v4, `modalContent()` terima `View|Htmlable|null` — gunakan `schema()` untuk form fields |
| `modalFooter()` error | Di Filament v4, gunakan `extraModalFooterActions()` untuk action buttons di modal footer |
