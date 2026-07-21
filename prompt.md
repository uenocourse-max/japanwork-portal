# PROJECT: Sistem Database dan Matching Siswa Program Kerja Jepang

## Project Overview

Buatkan aplikasi web full-stack untuk manajemen database siswa program kerja Jepang dan proses matching dengan perusahaan Jepang.

Aplikasi memiliki 2 role utama:

1. **Admin**
2. **Siswa**

Gunakan arsitektur yang mudah dikembangkan dan scalable.

---

# IMPLEMENTATION PLAN

## Phase 1 - Authentication

Implementasi sistem autentikasi:

### Admin

* Login menggunakan email dan password.
* Admin dibuat melalui seeder atau manual database.
* Tidak ada fitur registrasi admin dari frontend.

### Siswa

* Registrasi menggunakan:

  * Email
  * Password
* Login menggunakan email dan password.
* Setelah login pertama kali, siswa diarahkan ke halaman melengkapi profil.

---

## Phase 2 - Student Profile Management

Siswa dapat:

* Melihat data diri sendiri.
* Mengubah data diri sendiri.
* Upload link dokumen Google Drive.
* Mengubah password akun.

Admin dapat:

* Melihat seluruh siswa.
* Menambah data siswa.
* Mengubah data siswa.
* Menghapus data siswa.
* Melakukan pencarian dan filtering.

---

## Phase 3 - Matching Management

Admin dapat mengubah status matching siswa:

* Proses Matching
* Matched
* Menunggu Hasil
* Belum Matching

Jika status adalah "Matched", admin wajib memilih perusahaan tujuan.

---

## Phase 4 - Dashboard dan Statistik

Admin dashboard menampilkan:

* Total siswa.
* Total siswa matched.
* Total siswa menunggu hasil.
* Total siswa proses matching.
* Total siswa belum matching.
* Statistik berdasarkan:

  * Jalur
  * LPK
  * Level JLPT
  * Jenis SSW

---

# FUNCTIONAL SPECIFICATION

## Authentication Module

### Siswa

Fitur:

* Register
* Login
* Logout
* Forgot Password
* Reset Password

### Admin

Fitur:

* Login
* Logout

---

## Student Module

### Data yang harus disimpan

### Data Pribadi

* Nama Lengkap
* Umur
* Tempat Lahir
* Tanggal Lahir
* Alamat Tinggal
* Tinggi Badan
* Berat Badan
* Golongan Darah
* Status Perkawinan
* Nomor Telepon
* Email

### Status Peserta

* Status Peserta

  * Eks
  * New Comer

### Kemampuan Bahasa Jepang

* Score JFT
* Level JLPT

  * N5
  * N4
  * N3
  * N2
  * N1
* Lama Belajar Bahasa Jepang (bulan)

### Informasi Program

* Jenis SSW

  * Bisa lebih dari satu pilihan
* Jalur

  * Mandiri
  * LPK
* Jika jalur = LPK:

  * Nama LPK

### Dokumen

* Foto (Google Drive Link)
* CV (Google Drive Link)

### Matching Status

* Proses Matching
* Matched
* Menunggu Hasil
* Belum Matching

Jika status:

* Matched

maka wajib menyimpan:

* Nama perusahaan Jepang.

---

## Admin Module

### Dashboard

* Total siswa
* Statistik matching
* Statistik JLPT
* Statistik SSW
* Statistik jalur

### Student Management

* Create
* Read
* Update
* Delete
* Search
* Filter
* Export CSV/Excel

Filter:

* JLPT
* SSW
* Jalur
* LPK
* Matching Status
* Status Peserta

---

## Student Module

Siswa hanya dapat:

* Melihat profil sendiri.
* Mengubah profil sendiri.
* Mengubah password sendiri.

Siswa tidak boleh:

* Melihat data siswa lain.
* Menghapus akun.
* Mengakses dashboard admin.

---

# DATABASE SCHEMA

## users

| field      | type                |
| ---------- | ------------------- |
| id         | bigint              |
| name       | varchar             |
| email      | varchar unique      |
| password   | varchar             |
| role       | enum(admin,student) |
| created_at | timestamp           |
| updated_at | timestamp           |

---

## students

| field                    | type                                                      |
| ------------------------ | --------------------------------------------------------- |
| id                       | bigint                                                    |
| user_id                  | foreign key                                               |
| full_name                | varchar                                                   |
| age                      | integer                                                   |
| birth_place              | varchar                                                   |
| birth_date               | date                                                      |
| address                  | text                                                      |
| height_cm                | integer                                                   |
| weight_kg                | integer                                                   |
| blood_type               | enum(A,B,AB,O)                                            |
| marital_status           | enum(single,married)                                      |
| phone_number             | varchar                                                   |
| participant_status       | enum(ex,new_comer)                                        |
| jft_score                | integer nullable                                          |
| jlpt_level               | enum(N5,N4,N3,N2,N1) nullable                             |
| japanese_learning_months | integer                                                   |
| pathway                  | enum(mandiri,lpk)                                         |
| lpk_name                 | varchar nullable                                          |
| photo_drive_url          | text                                                      |
| cv_drive_url             | text                                                      |
| matching_status          | enum(process_matching,matched,waiting_result,not_matched) |
| matched_company_name     | varchar nullable                                          |
| created_at               | timestamp                                                 |
| updated_at               | timestamp                                                 |

---

## ssw_categories

| field | type    |
| ----- | ------- |
| id    | bigint  |
| name  | varchar |

Contoh data:

* Kaigo
* Food Service
* Agriculture
* Construction
* Manufacturing
* Building Cleaning
* Hotel
* Aviation
* Fishery

---

## student_ssw_categories

Pivot table many-to-many.

| field           | type        |
| --------------- | ----------- |
| student_id      | foreign key |
| ssw_category_id | foreign key |

Karena satu siswa dapat memiliki lebih dari satu SSW.

---

# RELATIONSHIP

User
hasOne Student

Student
belongsTo User

Student
belongsToMany SSWCategory

SSWCategory
belongsToMany Student

---

# APPLICATION FLOW

## Siswa

Register
↓
Login
↓
Lengkapi Profil
↓
Menunggu Verifikasi Admin
↓
Masuk Database Matching
↓
Status berubah menjadi:

* Proses Matching
* Menunggu Hasil
* Matched
* Belum Matching

---

## Admin

Login
↓
Melihat daftar siswa
↓
Review data siswa
↓
Mengubah status matching
↓
Menambahkan perusahaan tujuan jika matched
↓
Monitoring seluruh progress siswa

---

# BUSINESS RULES

1. Email harus unik.
2. Nomor telepon harus unik.
3. Jika jalur = Mandiri maka LPK harus kosong.
4. Jika jalur = LPK maka nama LPK wajib diisi.
5. Jika matching status = Matched maka nama perusahaan wajib diisi.
6. Siswa hanya dapat mengakses data miliknya sendiri.
7. Admin dapat mengakses seluruh data.
8. Satu siswa dapat memiliki banyak SSW.
9. JFT dan JLPT bersifat opsional karena tidak semua siswa memiliki keduanya.
10. Link Google Drive harus berupa URL valid.

---

# RECOMMENDED TECH STACK

## Backend

* Laravel 12
* PHP 8.3+
* Laravel Sanctum
* Laravel Policy
* Laravel Form Request Validation

## Admin Panel

* Filament v4

## Database

* PostgreSQL atau MySQL

## Frontend

Pilihan:

* Livewire + Volt
  atau
* Vue + Inertia

Rekomendasi:

* Laravel + Filament + Livewire karena lebih cepat untuk aplikasi administrasi.

---

# NON FUNCTIONAL REQUIREMENTS

* Responsive Design
* Role Based Access Control
* Pagination
* Search & Filter
* Export Excel
* Audit Log untuk perubahan data
* Secure Password Hashing
* CSRF Protection
* Rate Limiting
* Validation pada seluruh form
* Clean Architecture
* RESTful API Ready

Buat implementasi dengan clean code, service layer, repository pattern jika diperlukan, serta mengikuti best practice Laravel terbaru.

