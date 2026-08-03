# Alur Aplikasi

## Arsitektur Sistem

```
┌────────────────────────────────────────────────────────────┐
│              PORTAL LOWONGAN KERJA JEPANG                   │
│                 (Satu Aplikasi Monolitik)                    │
├────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌───────────────────┐   ┌───────────────────┐              │
│  │   Admin Panel     │   │  Recruiter Panel  │              │
│  │  (Filament 5.x)   │   │  (Filament 5.x)   │              │
│  │    /admin         │   │   /recruiter      │              │
│  ├───────────────────┤   ├───────────────────┤              │
│  │ • Dashboard (7wg) │   │ • Dashboard stats │              │
│  │ • Siswa CRUD      │   │ • Lowongan Saya   │              │
│  │ • Lowongan CRUD   │   │ • Lamaran Masuk   │              │
│  │ • Lamaran(workflw)│   │   (full workflow) │              │
│  │ • LPK/TSK CRUD    │   └───────────────────┘              │
│  │ • SSW Categories  │                                      │
│  └────────┬──────────┘                                      │
│           │                                                  │
│           ▼                                                  │
│  ┌─────────────────────────────────────────────────┐        │
│  │              Portal Siswa (Blade)                │        │
│  ├─────────────────────────────────────────────────┤        │
│  │ • Register / Login / Forgot Password            │        │
│  │ • Dashboard (stats, profil, matching, recent)   │        │
│  │ • Profil (view/edit + password)                 │        │
│  │ • Cari Lowongan (filter, search, 4 mode)        │        │
│  │ • Bookmark (simpan lowongan favorit)            │        │
│  │ • Daftar Lamaran (progress + withdraw)          │        │
│  │ • Notifikasi (database, mark-all-read)          │        │
│  └─────────────────────┬───────────────────────────┘        │
│                        │                                    │
│                        ▼                                    │
│  ┌─────────────────────────────────────────────────┐        │
│  │         Portal Publik (No Auth)                  │        │
│  │  /jobs — browse, filter, 4 view modes            │        │
│  │  Sidebar: Lowongan Favorit, Kategori SSW         │        │
│  └─────────────────────┬───────────────────────────┘        │
│                        │                                    │
└────────────────────────┼────────────────────────────────────┘
                         │
                         ▼
              ┌─────────────────────┐
              │  PostgreSQL (Neon)  │
              └─────────────────────┘
```

---

## 1. Alur Admin

1. **Login** → `/admin` → email: `admin@example.com`, password: `password`
2. **Dashboard** → 7 widget: total siswa, aplikasi per status, chart matching/JLPT/SSW/pathway
3. **Siswa** → CRUD + "Ubah Status Matching" (quick-action modal)
4. **Lowongan** → CRUD + tab "Lamaran" dengan workflow penuh
5. **Lamaran** → List semua lamaran, filter status, workflow
6. **LPK/TSK** → Create otomatis buat user recruiter + LPK record
7. **SSW Categories** → CRUD 9 kategori
8. **Pengguna** → CRUD user, reset password, ubah role

### Workflow Lamaran (Admin & Recruiter)

```
pending ──► reviewed ──► accepted (preliminary)
  │                           │
  │                           ▼
  │                    interview_scheduled
  │                           │
  │                    ┌──────┴──────┐
  │                    │             │
  │                    ▼             ▼
  │             company_accepted  not_passed
  │             (FINAL)           (FINAL)
  │                    │
  │                    ▼
  │             student.matching_status = 'matched'
  │
  └── withdrawn (oleh siswa, dari pending saja)
```

---

## 2. Alur Recruiter (LPK/TSK)

1. **Login** → `/recruiter` → email LPK (mitrasejati / bintangsamudra / cemerlangabadi)
2. **Dashboard** → Statistik lowongan & lamaran milik sendiri
3. **Lowongan Saya** → CRUD (scoped ke recruiter login)
4. **Lamaran Masuk** → List semua pelamar dari lowongan milik sendiri
5. **Lihat Profil** → Modal full profile student + action inline
6. **Profil Akun** → Edit nama, email, password, info perusahaan
7. Setiap perubahan status → **notifikasi** ke siswa (database + email via queue)

---

## 3. Alur Siswa

### Register & Login
```
/register ──► Buat akun (role=student) ──► auto login
  │
  ▼
Profil lengkap? ──NO──► /student/profile/edit
  │                       (isi semua field wajib)
  YES
  │
  ▼
/student/dashboard ──► Stats + aplikasi terbaru + notifikasi
```

### Fitur Siswa
1. **Dashboard** → `/student/dashboard` — stats lamaran, profil status, matching, recent
2. **Cari Lowongan** → `/student/jobs` — filter + 4 view modes
3. **Lihat Lowongan** → `/student/jobs/{id}` — detail + apply + bookmark
4. **Apply** → POST apply → cek duplikasi → notifikasi recruiter
5. **Bookmark** → POST toggle → simpan/hapus favorit
6. **Lamaran Saya** → `/student/applications` — progress + withdraw (khusus pending)
7. **Notifikasi** → `/student/notifications` — history + mark all read
8. **Profil** → view/edit/password

### Withdraw Lamaran
- Hanya bisa jika status masih `pending` (belum direview recruiter)
- Tombol "Batalkan Lamaran" di halaman daftar lamaran
- Konfirmasi sebelum withdraw
- Status berubah menjadi `withdrawn`

---

## 4. Alur Matching

Matching status siswa hanya berubah saat lamaran mencapai `company_accepted`:

```
company_accepted (FINAL)
  │
  ▼
student.matching_status = 'matched'
student.matched_company_name = job.company_name
```

---

## 5. Alur SSW Mismatch

Saat siswa lihat detail lowongan `job_type = 'tg'`:
- **Punya SSW sesuai** → tombol Lamar langsung aktif
- **Tidak punya SSW sesuai** → confirmation modal (tidak diblokir)

---

## 6. Alur Interview

Recruiter klik "Jadwalkan Interview" (status = accepted):
1. Modal: Jenis, Tanggal, Lokasi/Link, Catatan
2. Status → `interview_scheduled`
3. Notifikasi ke siswa
4. Siswa lihat detail interview di halaman lamaran

---

## Notifikasi

| Trigger | Dari | Ke | Channel |
|---------|------|----|---------|
| Siswa apply | System | Recruiter | database + mail |
| Status berubah | Recruiter | Student | database + mail |
| Jadwal interview diubah | Recruiter | Student | database + mail |

---

## Job Type System (`JobType` enum)

| `job_type` | Label di UI | Syarat Apply |
|------------|-------------|--------------|
| `magang` | Magang | Semua siswa boleh |
| `tg` | Tokutei Ginou (SSW) | Wajib SSW sesuai (confirmation jika mismatch) |
| `engineer` | Engineer / Gijinkoku | Semua siswa boleh |

---

## Console Commands

| Command | Schedule | Deskripsi |
|---------|----------|-----------|
| `jobs:archive-expired` | Daily | Tutup lowongan expired (status → closed) |
