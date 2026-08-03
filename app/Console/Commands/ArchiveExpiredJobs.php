<?php

namespace App\Console\Commands;

use App\Enums\JobStatus;
use App\Models\JobListing;
use Illuminate\Console\Command;

class ArchiveExpiredJobs extends Command
{
    protected $signature = 'jobs:archive-expired';

    protected $description = 'Tutup lowongan yang sudah melewati deadline';

    public function handle(): int
    {
        $count = JobListing::where('status', JobStatus::Open->value)
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->update(['status' => JobStatus::Closed->value]);

        $this->info("{$count} lowongan expired telah diarsipkan (status: closed).");

        return self::SUCCESS;
    }
}
