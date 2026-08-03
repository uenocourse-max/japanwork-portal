<?php

namespace Database\Seeders;

use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\SswCategory;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $recruiters = User::where('role', 'recruiter')->get();

        $jobs = [
            [
                'title' => 'Perawat Lansia (Kaigo) - Tokyo',
                'company_name' => 'Nihon Care Service',
                'description' => "Membantu aktivitas sehari-hari lansia di panti jompo. Tugas meliputi membantu makan, mandi, dan aktivitas ringan lainnya.\n\nLingkungan kerja yang mendukung dengan pelatihan bahasa Jepang gratis selama 3 bulan pertama.",
                'requirements' => "- Minimal JLPT N5 atau JFT Basic A2\n- Berusia 18-35 tahun\n- Sehat jasmani dan rohani\n- Bersedia bekerja di Tokyo",
                'salary_min' => 180000,
                'salary_max' => 250000,
                'location' => 'Tokyo',
                'ssw_category' => 'Kaigo',
                'jlpt_level_required' => 'N5',
                'participant_status_required' => 'new_comer',
                'job_type' => 'tg',
            ],
            [
                'title' => 'Staff Restoran Jepang - Osaka',
                'company_name' => 'Sakura Restaurant Group',
                'description' => 'Bekerja di restoran Jepang premium di area Shin-Osaka. Tugas mencakup pelayanan pelanggan, preparation makanan, dan menjaga kebersihan restoran.',
                'requirements' => "- Minimal JLPT N4\n- Pengalaman di bidang F&B lebih disukai\n- Ramah dan komunikatif\n- Bersedia shift",
                'salary_min' => 170000,
                'salary_max' => 230000,
                'location' => 'Osaka',
                'ssw_category' => 'Food Service',
                'jlpt_level_required' => 'N4',
                'participant_status_required' => 'ex',
                'job_type' => 'tg',
            ],
            [
                'title' => 'Pekerja Pertanian - Hokkaido',
                'company_name' => 'Hokkaido Farm Cooperative',
                'description' => 'Bekerja di kebun sayur dan buah di Hokkaido. Musim panen utama adalah Juni hingga Oktober. Fokus pada pertanian organik berkualitas tinggi.',
                'requirements' => "- Tidak wajib JLPT (pelatihan diberikan)\n- Sehat dan kuat bekerja outdoor\n- Bersedia di daerah pedesaan",
                'salary_min' => 160000,
                'salary_max' => 220000,
                'location' => 'Hokkaido',
                'ssw_category' => 'Agriculture',
                'jlpt_level_required' => null,
                'participant_status_required' => 'any',
                'job_type' => 'magang',
            ],
            [
                'title' => 'Konstruksi Bangunan - Nagoya',
                'company_name' => 'Chubu Construction Corp',
                'description' => 'Pekerja konstruksi untuk proyek pembangunan gedung apartemen di area Nagoya. Termasuk pekerjaan struktur, plumbing, dan finishing.',
                'requirements' => "- Minimal JLPT N5\n- Sertifikasi konstruksi lebih disukai\n- Pengalaman minimal 1 tahun",
                'salary_min' => 200000,
                'salary_max' => 300000,
                'location' => 'Aichi',
                'ssw_category' => 'Construction',
                'jlpt_level_required' => 'N5',
                'participant_status_required' => 'ex',
                'job_type' => 'tg',
            ],
            [
                'title' => 'Cleaning Staff - Yokohama',
                'company_name' => 'Yokohama Clean Service',
                'description' => 'Menjadi staff kebersihan di gedung perkantoran dan hotel di Yokohama. Shift pagi dan sore tersedia.',
                'requirements' => "- Tidak wajib JLPT\n- Teliti dan bertanggung jawab\n- Bersedia kerja shift",
                'salary_min' => 155000,
                'salary_max' => 200000,
                'location' => 'Kanagawa',
                'ssw_category' => 'Building Cleaning',
                'jlpt_level_required' => null,
                'participant_status_required' => 'new_comer',
                'job_type' => 'magang',
            ],
            [
                'title' => 'Staff Hotel Bintang 5 - Fukuoka',
                'company_name' => 'Grand Hotel Fukuoka',
                'description' => 'Bekerja di hotel bintang 5 di Fukuoka. Posisi di bagian front desk, housekeeping, dan restaurant service.',
                'requirements' => "- Minimal JLPT N3\n- Penampilan rapi dan profesional\n- Komunikasi bahasa Inggris menjadi nilai tambah",
                'salary_min' => 185000,
                'salary_max' => 260000,
                'location' => 'Fukuoka',
                'ssw_category' => 'Hotel',
                'jlpt_level_required' => 'N3',
                'participant_status_required' => 'ex',
                'job_type' => 'tg',
            ],
            [
                'title' => 'Pekerja Pabrik Elektronik - Sendai',
                'company_name' => 'Tohoku Electronics',
                'description' => 'Assembly line di pabrik elektronik di Sendai. Produksi komponen untuk industri otomotif dan elektronik.',
                'requirements' => "- Minimal JLPT N5\n- Ketelitian tinggi\n- Mampu bekerja dalam tim",
                'salary_min' => 170000,
                'salary_max' => 220000,
                'location' => 'Miyagi',
                'ssw_category' => 'Manufacturing',
                'jlpt_level_required' => 'N5',
                'participant_status_required' => 'new_comer',
                'job_type' => 'tg',
            ],
            [
                'title' => 'Nelayan Perikanan - Shizuoka',
                'company_name' => 'Shizuoka Fisheries Co',
                'description' => 'Bekerja di kapal penangkap ikan di Shizuoka. Fokus pada penangkapan tuna dan ikan lainnya.',
                'requirements' => "- Tidak wajib JLPT\n- Sehat jasmani\n- Tidak takut laut",
                'salary_min' => 190000,
                'salary_max' => 320000,
                'location' => 'Shizuoka',
                'ssw_category' => 'Fishery',
                'jlpt_level_required' => null,
                'participant_status_required' => 'any',
                'job_type' => 'magang',
            ],
            [
                'title' => 'Staff Bandara - Narita',
                'company_name' => 'Narita Airport Services',
                'description' => 'Pekerjaan di bandara internasional Narita. Bagian ground handling, bagasi, dan customer service.',
                'requirements' => "- Minimal JLPT N2\n- TOEIC score minimal 600 lebih disukai\n- Penampilan representatif",
                'salary_min' => 220000,
                'salary_max' => 300000,
                'location' => 'Chiba',
                'ssw_category' => 'Aviation',
                'jlpt_level_required' => 'N2',
                'participant_status_required' => 'ex',
                'job_type' => 'tg',
            ],
            [
                'title' => 'Perawat Lansia - Sapporo',
                'company_name' => 'Hokkaido Care Plus',
                'description' => 'Posisi perawat lansia di Sapporo. Lingkungan kerja yang tenang dengan pemandangan alam Hokkaido.',
                'requirements' => "- Minimal JLPT N4\n- Pengalaman di bidang kesehatan lebih disukai\n- Bersedia kerja shift",
                'salary_min' => 190000,
                'salary_max' => 270000,
                'location' => 'Hokkaido',
                'ssw_category' => 'Kaigo',
                'jlpt_level_required' => 'N4',
                'participant_status_required' => 'new_comer',
                'job_type' => 'tg',
            ],
            [
                'title' => 'Chef Jepang - Kobe',
                'company_name' => 'Kobe Beef Restaurant',
                'description' => 'Bekerja di restoran premium Kobe beef. Akan dilatih memasak masakan Jepang autentik.',
                'requirements' => "- Minimal JLPT N3\n- Pengalaman memasak lebih disukai\n- Passion di dunia kuliner",
                'salary_min' => 210000,
                'salary_max' => 350000,
                'location' => 'Hyogo',
                'ssw_category' => 'Food Service',
                'jlpt_level_required' => 'N3',
                'participant_status_required' => 'ex',
                'job_type' => 'engineer',
            ],
            [
                'title' => 'Warehouse Staff - Hamamatsu',
                'company_name' => 'Chubu Logistics',
                'description' => 'Pekerja gudang di area Hamamatsu. Tugas meliputi packing, sorting, dan loading barang.',
                'requirements' => "- Minimal JLPT N5\n- Fisik kuat\n- Teliti dalam bekerja",
                'salary_min' => 165000,
                'salary_max' => 210000,
                'location' => 'Shizuoka',
                'ssw_category' => 'Manufacturing',
                'jlpt_level_required' => 'N5',
                'participant_status_required' => 'new_comer',
                'job_type' => 'tg',
            ],
        ];

        foreach ($jobs as $index => $jobData) {
            $recruiter = $recruiters[$index % $recruiters->count()];
            $sswCategory = SswCategory::where('name', $jobData['ssw_category'])->first();

            JobListing::create([
                'title' => $jobData['title'],
                'description' => $jobData['description'],
                'requirements' => $jobData['requirements'],
                'salary_min' => $jobData['salary_min'],
                'salary_max' => $jobData['salary_max'],
                'location' => $jobData['location'],
                'company_name' => $jobData['company_name'],
                'company_description' => $jobData['company_name'].' adalah perusahaan terkemuka di bidangnya di Jepang.',
                'ssw_category_id' => $sswCategory?->id,
                'job_type' => $jobData['job_type'] ?? 'tg',
                'jlpt_level_required' => $jobData['jlpt_level_required'],
                'participant_status_required' => $jobData['participant_status_required'],
                'status' => 'open',
                'posted_by' => $recruiter->id,
                'deadline' => now()->addMonths(rand(1, 5)),
            ]);
        }

        $studentData = [
            ['name' => 'Ahmad Rizky', 'jlpt' => 'N4', 'pathway' => 'lpk', 'lpk' => 'LPK Mitra Sejati', 'matching' => 'not_matched'],
            ['name' => 'Siti Nurhaliza', 'jlpt' => 'N5', 'pathway' => 'lpk', 'lpk' => 'LPK Bintang Samudra', 'matching' => 'process_matching'],
            ['name' => 'Budi Santoso', 'jlpt' => 'N3', 'pathway' => 'mandiri', 'lpk' => null, 'matching' => 'waiting_result'],
            ['name' => 'Dewi Lestari', 'jlpt' => 'N2', 'pathway' => 'lpk', 'lpk' => 'LPK Cemerlang Abadi', 'matching' => 'matched', 'company' => 'Nihon Care Service'],
            ['name' => 'Eko Prasetyo', 'jlpt' => null, 'pathway' => 'lpk', 'lpk' => 'LPK Mitra Sejati', 'matching' => 'not_matched'],
            ['name' => 'Fitriani', 'jlpt' => 'N5', 'pathway' => 'mandiri', 'lpk' => null, 'matching' => 'not_matched'],
            ['name' => 'Gunawan Wibowo', 'jlpt' => 'N4', 'pathway' => 'lpk', 'lpk' => 'LPK Bintang Samudra', 'matching' => 'process_matching'],
            ['name' => 'Hana Permata', 'jlpt' => 'N3', 'pathway' => 'lpk', 'lpk' => 'LPK Cemerlang Abadi', 'matching' => 'not_matched'],
            ['name' => 'Indra Kusuma', 'jlpt' => 'N1', 'pathway' => 'mandiri', 'lpk' => null, 'matching' => 'matched', 'company' => 'Narita Airport Services'],
            ['name' => 'Joko Widodo', 'jlpt' => 'N5', 'pathway' => 'lpk', 'lpk' => 'LPK Mitra Sejati', 'matching' => 'not_matched'],
            ['name' => 'Kartika Sari', 'jlpt' => 'JFT Basic A2', 'pathway' => 'lpk', 'lpk' => 'LPK Bintang Samudra', 'matching' => 'waiting_result'],
            ['name' => 'Lukman Hakim', 'jlpt' => 'N4', 'pathway' => 'lpk', 'lpk' => 'LPK Cemerlang Abadi', 'matching' => 'not_matched'],
            ['name' => 'Maya Angelina', 'jlpt' => 'N3', 'pathway' => 'mandiri', 'lpk' => null, 'matching' => 'process_matching'],
            ['name' => 'Nanda Pratama', 'jlpt' => null, 'pathway' => 'lpk', 'lpk' => 'LPK Mitra Sejati', 'matching' => 'not_matched'],
            ['name' => 'Omar Daniel', 'jlpt' => 'N2', 'pathway' => 'lpk', 'lpk' => 'LPK Bintang Samudra', 'matching' => 'matched', 'company' => 'Grand Hotel Fukuoka'],
        ];

        foreach ($studentData as $data) {
            $user = User::make([
                'name' => $data['name'],
                'email' => strtolower(str_replace(' ', '.', $data['name'])).'@example.com',
                'password' => Hash::make('password'),
            ]);
            $user->forceFill(['role' => 'student']);
            $user->save();

            $student = Student::create([
                'user_id' => $user->id,
                'full_name' => $data['name'],
                'age' => rand(20, 32),
                'birth_place' => fake()->city(),
                'birth_date' => fake()->dateTimeBetween('-30 years', '-20 years'),
                'address' => fake()->address(),
                'height_cm' => rand(150, 180),
                'weight_kg' => rand(45, 85),
                'blood_type' => fake()->randomElement(['A', 'B', 'AB', 'O']),
                'marital_status' => 'single',
                'phone_number' => '08'.fake()->numerify('##########'),
                'gender' => fake()->randomElement(['male', 'female']),
                'participant_status' => fake()->randomElement(['ex', 'new_comer']),
                'jft_score' => $data['jlpt'] === 'JFT Basic A2' ? rand(100, 200) : null,
                'jlpt_level' => $data['jlpt'],
                'japanese_learning_months' => rand(1, 24),
                'pathway' => $data['pathway'],
                'lpk_name' => $data['lpk'],
                'matching_status' => $data['matching'],
                'matched_company_name' => $data['company'] ?? null,
            ]);

            $student->sswCategories()->attach(
                SswCategory::inRandomOrder()->limit(rand(1, 3))->pluck('id')
            );
        }

        $students = Student::all();
        $openJobs = JobListing::where('status', 'open')->get();

        foreach ($students->random(min(8, $students->count())) as $student) {
            $job = $openJobs->random();
            if (! JobApplication::where('job_listing_id', $job->id)->where('student_id', $student->id)->exists()) {
                $status = fake()->randomElement(['pending', 'reviewed', 'accepted', 'interview_scheduled', 'company_accepted', 'not_passed', 'rejected']);

                $interviewFields = [];
                if (in_array($status, ['interview_scheduled', 'company_accepted', 'not_passed'])) {
                    $interviewFields = [
                        'interview_type' => fake()->randomElement(['online', 'offline']),
                        'interview_date' => fake()->dateTimeBetween('-1 week', '+2 weeks'),
                        'interview_location' => fake()->randomElement(['https://meet.google.com/abc-defg-hij', 'Kantor Jakarta', 'Kantor Osaka']),
                        'interview_notes' => fake()->optional(0.5)->sentence(),
                    ];
                }

                JobApplication::create(array_merge([
                    'job_listing_id' => $job->id,
                    'student_id' => $student->id,
                    'status' => $status,
                    'applied_at' => fake()->dateTimeBetween('-2 weeks', 'now'),
                    'reviewed_at' => fake()->randomElement([null, fake()->dateTimeBetween('-1 week', 'now')]),
                ], $interviewFields));
            }
        }
    }
}
