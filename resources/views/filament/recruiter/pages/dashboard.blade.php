<x-filament-panels::page>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Selamat datang, {{ auth()->user()->name }}
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Kelola lowongan dan lamaran dari LPK/TSK Anda.
        </p>
    </div>

    {{-- Quick Actions --}}
    <div class="mb-6 flex flex-wrap gap-3">
        <x-filament::button
            tag="a"
            :href="\App\Filament\Recruiter\Resources\RecruiterJobListingResource::getUrl('create')"
            icon="heroicon-m-plus"
            size="sm"
        >
            Buat Lowongan Baru
        </x-filament::button>
        <x-filament::button
            tag="a"
            :href="\App\Filament\Recruiter\Resources\RecruiterApplicationResource::getUrl('index')"
            icon="heroicon-m-clipboard-document-list"
            color="gray"
            size="sm"
        >
            Lihat Semua Lamaran
        </x-filament::button>
        <x-filament::button
            tag="a"
            :href="\App\Filament\Recruiter\Resources\RecruiterJobListingResource::getUrl('index')"
            icon="heroicon-m-briefcase"
            color="gray"
            size="sm"
        >
            Kelola Lowongan
        </x-filament::button>
    </div>

    {{-- Stats Overview --}}
    @livewire(\App\Filament\Recruiter\Widgets\RecruiterStatsOverview::class)

    {{-- Charts Row --}}
    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        @livewire(\App\Filament\Recruiter\Widgets\RecruiterApplicationsChart::class)
        @livewire(\App\Filament\Recruiter\Widgets\RecruiterApplicationsTrendChart::class)
    </div>

    {{-- Data Tables Row --}}
    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        @livewire(\App\Filament\Recruiter\Widgets\RecruiterUpcomingInterviewsWidget::class)
        @livewire(\App\Filament\Recruiter\Widgets\RecruiterTopJobsWidget::class)
    </div>

    {{-- Recent Applications --}}
    @php
        $recentApplications = \App\Models\JobApplication::whereHas('jobListing', fn ($q) => $q->where('posted_by', auth()->id()))
            ->with('student', 'jobListing')
            ->latest('applied_at')
            ->limit(8)
            ->get();
    @endphp

    <div class="mt-8">
        <x-filament::section>
            <x-slot name="heading">Lamaran Terbaru</x-slot>
            <x-slot name="description">8 lamaran terakhir yang masuk</x-slot>

            @if ($recentApplications->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-white/10">
                                <th class="pb-3 font-medium text-gray-500 dark:text-gray-400">Siswa</th>
                                <th class="pb-3 font-medium text-gray-500 dark:text-gray-400">Lowongan</th>
                                <th class="pb-3 font-medium text-gray-500 dark:text-gray-400">JLPT</th>
                                <th class="pb-3 font-medium text-gray-500 dark:text-gray-400">Status</th>
                                <th class="pb-3 font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                            @foreach ($recentApplications as $application)
                                <tr class="group">
                                    <td class="py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-50 text-xs font-medium text-primary-700 dark:bg-primary-500/10 dark:text-primary-400">
                                                {{ strtoupper(substr($application->student->full_name ?? 'N', 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">{{ $application->student->full_name ?? '-' }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $application->student->phone ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <p class="text-gray-900 dark:text-white">{{ $application->jobListing->title ?? '-' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $application->jobListing->location ?? '-' }}</p>
                                    </td>
                                    <td class="py-3">
                                        @if ($application->student?->jlpt_level)
                                            <x-filament::badge size="sm" color="info">
                                                {{ $application->student->jlpt_level }}
                                            </x-filament::badge>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <x-filament::badge :color="\App\Enums\ApplicationStatus::tryFrom($application->status)?->color() ?? 'gray'">
                                            {{ \App\Enums\ApplicationStatus::tryFrom($application->status)?->label() ?? ucfirst($application->status) }}
                                        </x-filament::badge>
                                    </td>
                                    <td class="py-3 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $application->applied_at?->diffForHumans() ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    Belum ada lamaran masuk.
                </p>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
