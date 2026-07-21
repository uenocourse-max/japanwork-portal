<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\SswCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArchiveExpiredJobsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SswCategory::factory()->create(['name' => 'Perawatan']);
    }

    public function test_archives_expired_jobs(): void
    {
        JobListing::factory()->open()->create([
            'deadline' => now()->subDay(),
            'title' => 'Expired',
        ]);
        JobListing::factory()->open()->create([
            'deadline' => now()->subWeek(),
            'title' => 'Week Old',
        ]);

        $this->artisan('jobs:archive-expired')
            ->expectsOutput('2 lowongan expired telah diarsipkan (status: closed).')
            ->assertSuccessful();

        $this->assertEquals(0, JobListing::where('status', 'open')->where('title', 'Expired')->count());
        $this->assertEquals('closed', JobListing::where('title', 'Expired')->first()->status);
    }

    public function test_does_not_archive_non_expired_jobs(): void
    {
        JobListing::factory()->open()->create([
            'deadline' => now()->addMonth(),
            'title' => 'Future Job',
        ]);
        JobListing::factory()->open()->create([
            'deadline' => null,
            'title' => 'No Deadline',
        ]);

        $this->artisan('jobs:archive-expired')
            ->expectsOutput('0 lowongan expired telah diarsipkan (status: closed).')
            ->assertSuccessful();

        $this->assertEquals('open', JobListing::where('title', 'Future Job')->first()->status);
        $this->assertEquals('open', JobListing::where('title', 'No Deadline')->first()->status);
    }

    public function test_does_not_archive_already_closed_jobs(): void
    {
        JobListing::factory()->closed()->create([
            'deadline' => now()->subDay(),
            'title' => 'Already Closed',
        ]);

        $this->artisan('jobs:archive-expired')->assertSuccessful();

        $this->assertEquals('closed', JobListing::where('title', 'Already Closed')->first()->status);
    }
}
