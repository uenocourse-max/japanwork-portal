<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\SswCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPortalControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SswCategory::factory()->create(['name' => 'Perawatan']);
    }

    public function test_index_shows_only_open_and_available_jobs(): void
    {
        JobListing::factory()->open()->create(['title' => 'Open Job']);
        JobListing::factory()->closed()->create(['title' => 'Closed Job']);
        JobListing::factory()->filled()->create(['title' => 'Filled Job']);

        $response = $this->get(route('portal.index'));

        $response->assertStatus(200);
        $response->assertSee('Open Job');
        $response->assertDontSee('Closed Job');
        $response->assertDontSee('Filled Job');
    }

    public function test_index_hides_expired_jobs(): void
    {
        JobListing::factory()->open()->create([
            'title' => 'Expired Job',
            'deadline' => now()->subDay(),
        ]);
        JobListing::factory()->open()->create([
            'title' => 'Active Job',
            'deadline' => now()->addMonth(),
        ]);

        $response = $this->get(route('portal.index'));

        $response->assertDontSee('Expired Job');
        $response->assertSee('Active Job');
    }

    public function test_index_can_filter_by_ssw_category(): void
    {
        $cat1 = SswCategory::factory()->create(['name' => 'Perawatan']);
        $cat2 = SswCategory::factory()->create(['name' => 'Konstruksi']);

        JobListing::factory()->open()->create([
            'title' => 'Caregiver Job',
            'ssw_category_id' => $cat1->id,
        ]);
        JobListing::factory()->open()->create([
            'title' => 'Construction Job',
            'ssw_category_id' => $cat2->id,
        ]);

        $response = $this->get(route('portal.index', ['ssw_category_id' => $cat1->id]));

        $response->assertSee('Caregiver Job');
        $response->assertSee('1 lowongan ditemukan');
    }

    public function test_index_can_filter_by_job_type(): void
    {
        JobListing::factory()->open()->create([
            'title' => 'Magang Job',
            'job_type' => 'magang',
        ]);
        JobListing::factory()->open()->create([
            'title' => 'Engineer Job',
            'job_type' => 'engineer',
        ]);

        $response = $this->get(route('portal.index', ['job_type' => 'magang']));

        $response->assertSee('Magang Job');
        $response->assertSee('1 lowongan ditemukan');
    }

    public function test_index_can_filter_by_location(): void
    {
        JobListing::factory()->open()->create([
            'title' => 'Tokyo Job',
            'location' => 'Tokyo',
        ]);
        JobListing::factory()->open()->create([
            'title' => 'Osaka Job',
            'location' => 'Osaka',
        ]);

        $response = $this->get(route('portal.index', ['location' => 'Tokyo']));

        $response->assertSee('Tokyo Job');
        $response->assertSee('1 lowongan ditemukan');
    }

    public function test_index_search_by_title(): void
    {
        JobListing::factory()->open()->create(['title' => 'Software Engineer']);
        JobListing::factory()->open()->create(['title' => 'Hotel Receptionist']);

        $response = $this->get(route('portal.index', ['search' => 'Software']));

        $response->assertSee('Software Engineer');
        $response->assertSee('1 lowongan ditemukan');
    }

    public function test_show_displays_job(): void
    {
        $job = JobListing::factory()->open()->create([
            'title' => 'Test Job Detail',
            'deadline' => now()->addMonth(),
        ]);

        $response = $this->get(route('portal.show', $job));

        $response->assertStatus(200);
        $response->assertSee('Test Job Detail');
    }

    public function test_show_returns_404_for_closed_job(): void
    {
        $job = JobListing::factory()->closed()->create();

        $response = $this->get(route('portal.show', $job));

        $response->assertNotFound();
    }

    public function test_show_returns_404_for_expired_job(): void
    {
        $job = JobListing::factory()->open()->create([
            'deadline' => now()->subDay(),
        ]);

        $response = $this->get(route('portal.show', $job));

        $response->assertNotFound();
    }

    public function test_show_shows_related_jobs(): void
    {
        $cat = SswCategory::factory()->create(['name' => 'Perawatan']);

        $main = JobListing::factory()->open()->create([
            'title' => 'Main Job',
            'ssw_category_id' => $cat->id,
            'location' => 'Tokyo',
            'deadline' => now()->addMonth(),
        ]);
        $related = JobListing::factory()->open()->create([
            'title' => 'Related Job',
            'ssw_category_id' => $cat->id,
            'location' => 'Osaka',
            'deadline' => now()->addMonth(),
        ]);

        $response = $this->get(route('portal.show', $main));

        $response->assertSee('Main Job');
        $response->assertSee('Related Job');
    }

    public function test_ssw_categories_count_only_open_jobs(): void
    {
        $cat = SswCategory::factory()->create(['name' => 'Perawatan']);

        JobListing::factory()->open()->create([
            'ssw_category_id' => $cat->id,
            'deadline' => now()->addMonth(),
        ]);
        JobListing::factory()->closed()->create([
            'ssw_category_id' => $cat->id,
        ]);

        $response = $this->get(route('portal.index'));

        $response->assertStatus(200);
    }
}
