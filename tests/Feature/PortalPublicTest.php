<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\SswCategory;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalPublicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SswCategory::factory()->create(['name' => 'Perawatan']);
    }

    public function test_homepage_redirects_to_portal_jobs(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('portal.index'));
    }

    public function test_index_can_filter_by_jlpt_level(): void
    {
        JobListing::factory()->open()->create([
            'title' => 'Butuh N3',
            'jlpt_level_required' => 'N3',
        ]);
        JobListing::factory()->open()->create([
            'title' => 'Butuh N5',
            'jlpt_level_required' => 'N5',
        ]);

        $response = $this->get(route('portal.index', ['jlpt_level' => 'N3']));

        $response->assertSee('Butuh N3');
        $response->assertSee('1 lowongan ditemukan');
        $response->assertDontSee('2 lowongan ditemukan');
    }

    public function test_index_shows_empty_state_when_no_jobs_match(): void
    {
        JobListing::factory()->open()->create([
            'title' => 'Job Lain',
            'location' => 'Tokyo',
        ]);

        $response = $this->get(route('portal.index', ['location' => 'Osaka']));

        $response->assertStatus(200);
        $response->assertSee('Tidak ada lowongan ditemukan');
        $response->assertSee('0 lowongan ditemukan');
    }

    public function test_index_shows_popular_jobs_section(): void
    {
        $job = JobListing::factory()->open()->create([
            'title' => 'Lowongan Terpopuler',
            'deadline' => now()->addMonth(),
        ]);

        $response = $this->get(route('portal.index'));

        $response->assertStatus(200);
        $response->assertSee('Lowongan Terpopuler');
    }

    public function test_guest_sees_register_and_login_links_instead_of_apply_form(): void
    {
        $job = JobListing::factory()->open()->create([
            'title' => 'Job Tanpa Login',
            'deadline' => now()->addMonth(),
        ]);

        $response = $this->get(route('portal.show', $job));

        $response->assertStatus(200);
        $response->assertSee('Daftar & Lamar', false);
        $response->assertSee('Masuk untuk Melamar');
        $response->assertDontSee('Lamar Sekarang');
    }

    public function test_recruiter_sees_message_that_only_students_can_apply(): void
    {
        $recruiter = User::factory()->create(['role' => 'recruiter']);
        $job = JobListing::factory()->open()->create([
            'title' => 'Job Recruiter',
            'deadline' => now()->addMonth(),
        ]);

        $response = $this->actingAs($recruiter)->get(route('portal.show', $job));

        $response->assertStatus(200);
        $response->assertSee('Hanya siswa yang bisa melamar');
        $response->assertDontSee('Lamar Sekarang');
    }

    public function test_student_can_bookmark_job_from_portal_detail_page(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $student = Student::factory()->create(['user_id' => $user->id]);
        $job = JobListing::factory()->open()->create([
            'title' => 'Job Bookmark Portal',
            'deadline' => now()->addMonth(),
        ]);

        $this->actingAs($user)->get(route('portal.show', $job));

        $response = $this->actingAs($user)->post(route('student.jobs.bookmark', $job));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('saved_jobs', [
            'student_id' => $student->id,
            'job_listing_id' => $job->id,
        ]);
    }
}
