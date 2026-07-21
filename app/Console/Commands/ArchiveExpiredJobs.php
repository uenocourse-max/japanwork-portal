<?php

namespace App\Console\Commands;

use App\Models\JobListing;
use Illuminate\Console\Command;

class ArchiveExpiredJobs extends Command
{
    protected $signature = 'jobs:archive-expired';

    protected $description = 'Tutup lowongan yang sudah melewati deadline';

    public function handle(): int
    {
        $count = JobListing::where('status', 'open')
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->update(['status' => 'closed']);

        $this->info("{$count} lowongan expired telah diarsipkan (status: closed).");

        return self::SUCCESS;
    }
}
