<x-filament-panels::page>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Selamat datang, {{ auth()->user()->name }}
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Kelola lowongan dan lamaran dari LPK/TSK Anda.
        </p>
    </div>

    @livewire(\App\Filament\Recruiter\Widgets\RecruiterStatsOverview::class)

    @php
        $recentApplications = $this->getRecentApplications();
        $myRecentJobs = $this->getMyRecentJobs();
    @endphp

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-filament::section>
            <x-slot name="heading">Lamaran Terbaru</x-slot>

            @forelse ($recentApplications as $application)
                <div class="flex items-center justify-between py-3 {{ $loop->last ? '' : 'border-b border-gray-200 dark:border-white/10' }}">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                            {{ $application->student->full_name ?? '-' }}
                        </p>
                        <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                            {{ $application->jobListing->title ?? '-' }}
                        </p>
                    </div>
                    <x-filament::badge :color="match($application->status) {
                        'pending' => 'warning',
                        'reviewed' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }">
                        {{ ucfirst($application->status) }}
                    </x-filament::badge>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    Belum ada lamaran masuk.
                </p>
            @endforelse
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Lowongan Saya</x-slot>

            @forelse ($myRecentJobs as $job)
                <div class="flex items-center justify-between py-3 {{ $loop->last ? '' : 'border-b border-gray-200 dark:border-white/10' }}">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                            {{ $job->title }}
                        </p>
                        <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                            {{ $job->applications_count }} lamaran · {{ ucfirst($job->status) }}
                        </p>
                    </div>
                    <x-filament::link :href="\App\Filament\Recruiter\Resources\RecruiterJobListingResource::getUrl('edit', ['record' => $job->id])">
                        Edit
                    </x-filament::link>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    Belum ada lowongan. <a href="{{ \App\Filament\Recruiter\Resources\RecruiterJobListingResource::getUrl('create') }}" class="text-primary-600 hover:underline">Buat sekarang</a>
                </p>
            @endforelse
        </x-filament::section>
    </div>
</x-filament-panels::page>
