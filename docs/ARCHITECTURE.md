# Japan Work Program — Arsitektur Aplikasi

## Ringkasan

Portal lowongan kerja Jepang (SSW/Tokutei Ginou, Magang, Engineer/Gijinkoku) dengan 3 peran:
**Student** (Blade), **Recruiter** (Filament), **Admin** (Filament).

---

## Stack

| Layer | Teknologi |
|---|---|
| Framework | Laravel 13 |
| PHP | 8.5 |
| Database | PostgreSQL (Neon Cloud) |
| Admin Panel | Filament v5 |
| Frontend | Blade + Tailwind CSS v4 |
| Testing | PHPUnit 12 |
| Queue | Database (default) |
| Cache | Database |
| Auth | Session-based (Laravel Breeze-style) |

---

## Peran & Akses

| Peran | Panel | Masuk | Fitur Utama |
|---|---|---|---|
| **Admin** | `/admin` (Filament) | Via login form | CRUD semua entitas, widget statistik |
| **Recruiter** | `/recruiter` (Filament) | Via login form | Kelola lowongan sendiri, review pelamar |
| **Student** | Blade `/student/*` | Via `/login` | Cari/lamar/ bookmark lowongan, dashboard, notifikasi |

---

## Routes

### Public
| Method | URI | Controller | Nama Route |
|---|---|---|---|
| GET | `/` | Closure → redirect `/jobs` | — |
| GET | `/jobs` | `JobPortalController@index` | `portal.index` |
| GET | `/jobs/{job}` | `JobPortalController@show` | `portal.show` |
| GET | `/register` | `StudentRegisterController@showRegistrationForm` | `student.register` |
| POST | `/register` | `StudentRegisterController@register` | `student.register.store` |
| GET | `/login` | `StudentLoginController@showLoginForm` | `student.login` |
| POST | `/login` | `StudentLoginController@login` | `student.login.store` |
| POST | `/logout` | `StudentLoginController@logout` | `student.logout` |
| GET/POST | `/forgot-password` | `ForgotPasswordController` | `password.*` |
| GET/POST | `/reset-password` | `ResetPasswordController` | `password.*` |

### Student (middleware: `auth`, `EnsureStudentProfileComplete`)
| Method | URI | Controller | Nama Route |
|---|---|---|---|
| GET | `/student/dashboard` | `StudentDashboardController@index` | `student.dashboard` |
| GET | `/student/profile` | `StudentProfileController@show` | `student.profile` |
| GET | `/student/profile/edit` | `StudentProfileController@edit` | `student.profile.edit` |
| PUT | `/student/profile` | `StudentProfileController@update` | `student.profile.update` |
| GET | `/student/password` | `StudentProfileController@changePassword` | `student.password` |
| PUT | `/student/password` | `StudentProfileController@updatePassword` | `student.password.update` |
| GET | `/student/jobs` | `JobController@index` | `student.jobs.index` |
| GET | `/student/jobs/{job}` | `JobController@show` | `student.jobs.show` |
| POST | `/student/jobs/{job}/apply` | `JobController@apply` | `student.jobs.apply` |
| POST | `/student/jobs/{job}/bookmark` | `SavedJobController@toggle` | `student.jobs.bookmark` |
| GET | `/student/applications` | `ApplicationController@index` | `student.applications.index` |
| GET | `/student/bookmarks` | `SavedJobController@index` | `student.bookmarks.index` |
| GET | `/student/notifications` | `ApplicationController@notifications` | `student.notifications.index` |
| POST | `/student/notifications/mark-all-read` | `ApplicationController@markAllNotificationsRead` | `student.notifications.markAllRead` |

### Admin (Filament, `/admin/*`)
Resource list: `students`, `job-listings`, `applications`, `ssw-categories`, `lpk-tsks`, `users`
Widget: `StudentStatsOverview`, `ApplicationStatsOverview`, `MatchingChart`, `JlptChart`, `JobsBySswChart`, `SswChart`, `PathwayChart`

### Recruiter (Filament, `/recruiter/*`)
Resource list: `recruiter-job-listings`, `recruiter-applications`
Pages: `Dashboard`, `Profile`
Widget: `RecruiterStatsOverview`, `RecruiterApplicationsChart`, `RecruiterApplicationsTrendChart`, `RecruiterTopJobsWidget`, `RecruiterUpcomingInterviewsWidget`

### Portal Publik (`/jobs`)
Browse lowongan tanpa autentikasi. Sidebar menampilkan:
- **Lowongan Favorit** — Top 5 lowongan berdasarkan jumlah pelamar (independent dari filter pencarian)
- **Kategori SSW** — Daftar kategori dengan jumlah lowongan aktif

---

## Models

### User
- **Fillable**: `name`, `email`, `password`, `phone_number`, `company_name`, `location`
- **Relations**: `student()`, `lpkTsk()`, `postedJobs()`
- **Methods**: `isAdmin()`, `isStudent()`, `isRecruiter()`, `canAccessPanel()`
- **Security**: `role` NOT fillable via mass assignment — only changeable via admin panel

### Student
- **Fillable**: `user_id`, `full_name`, `age`, `birth_place`, `birth_date`, `address`, `height_cm`, `weight_kg`, `blood_type`, `marital_status`, `phone_number`, `gender`, `participant_status`, `jft_score`, `jlpt_level`, `japanese_learning_months`, `pathway`, `lpk_name`, `photo_drive_url`, `cv_drive_url`, `matching_status`, `matched_company_name`
- **Relations**: `user()`, `savedJobs()`, `sswCategories()` (pivot `student_ssw_category`)
- **Methods**: `isProfileComplete()`, `hasSswCategory()`

### JobListing
- **Fillable**: 17 fields — title, description, requirements, salary_min/max, location, company_name/description, thumbnail_url, ssw_category_id, jlpt_level_required, participant_status_required, status (draft/open/closed/filled), job_type (magang/tg/engineer), posted_by, deadline
- **Relations**: `poster()`, `sswCategory()`, `applications()`, `savedByStudents()`, `applicants()`
- **Accessors**: `thumbnail_display_url` (Google Drive → direct link)

### JobApplication
- **Status workflow**: `pending` → `reviewed` → `accepted` → `interview_scheduled` → `company_accepted` | `not_passed` | `rejected`
- **Relations**: `jobListing()`, `student()`

### SswCategory
- Simple: `name`
- **Relations**: `students()`, `jobListings()`

### LpkTsk
- **Fillable**: `user_id`, `name`, `email`, `password`, `location`
- **Relation**: `user()`

### SavedJob
- **Fillable**: `student_id`, `job_listing_id`
- **Relations**: `student()`, `jobListing()`
- **Unique**: (student_id, job_listing_id)

---

## Filters (Public & Student Job Listing)

| Filter | Tipe | Query |
|---|---|---|
| `search` | text (judul/perusahaan) | `WHERE title ILIKE %...% OR company_name ILIKE %...%` |
| `ssw_category_id` | select | `WHERE ssw_category_id = ...` |
| `job_type` | select | `WHERE job_type = ...` |
| `jlpt_level` | select | `WHERE jlpt_level_required = ...` |
| `location` | select (47 prefektur) | `WHERE location ILIKE %...%` |
| `view` | toggle | detail/compact/list/grid |
| Sorting | — | `latest()` (created_at DESC) |

**Sidebar widgets (independent dari filter):**
- `popularJobs` — Top 5 lowongan berdasarkan `job_applications.count` (cache 5 menit)

---

## Notifications

### NewApplicationReceived
- **Channel**: database, mail
- **Data**: application_id, job_title, student_name, jlpt_level
- **Dikirim**: Saat student apply ke lowongan (ke poster/recruiter)

### ApplicationStatusChanged
- **Channel**: database, mail
- **Data**: application_id, job_title, company_name, old_status, new_status, notes
- **Dikirim**: Saat recruiter/admin ubah status lamaran (ke student)

### InterviewScheduleChanged
- **Channel**: database, mail
- **Data**: application_id, job_title, company_name, old_schedule, new_schedule
- **Dikirim**: Saat recruiter/admin edit jadwal interview (ke student)

---

## Console

| Command | Schedule | Deskripsi |
|---|---|---|
| `jobs:archive-expired` | Daily (`$schedule->command('jobs:archive-expired')->daily()`) | Tutup lowongan expired (status → closed) |

---

## Testing

- PHPUnit 12
- 10 test files, 70 tests, 140 assertions
- Database: SQLite in-memory (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`)
- RefreshDatabase trait used
- `StudentFactory`: jlpt_level tanpa `JFT Basic A2` (kompatibel SQLite CHECK)
- Coverage: LoginTest (8), RegisterTest (8), JobControllerTest (9), JobPortalControllerTest (11), ApplicationControllerTest (8), SavedJobControllerTest (6), StudentProfileControllerTest (7), StudentDashboardControllerTest (3), EnsureStudentProfileCompleteTest (5), ArchiveExpiredJobsCommandTest (3)
- Run: `php artisan test --compact`

---

## Deployment

- Laravel Cloud (https://cloud.laravel.com/) — fastest way to deploy
- Pastikan queue runner aktif untuk notifikasi
- Pastikan scheduler aktif untuk `jobs:archive-expired`

## Error Pages

Custom error pages tersedia di `resources/views/errors/`:
- `404.blade.php` — Halaman tidak ditemukan
- `403.blade.php` — Akses ditolak
- `500.blade.php` — Kesalahan server
