@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $statusLabels = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'reviewed' => 'bg-blue-100 text-blue-800',
            'accepted' => 'bg-indigo-100 text-indigo-800',
            'interview_scheduled' => 'bg-purple-100 text-purple-800',
            'company_accepted' => 'bg-green-100 text-green-800',
            'not_passed' => 'bg-red-100 text-red-800',
            'rejected' => 'bg-red-100 text-red-800',
            'withdrawn' => 'bg-gray-100 text-gray-600',
        ];

        $statusLabel = fn (?string $status): string => \App\Enums\ApplicationStatus::tryFrom($status ?? '')?->label() ?? $status;

        $matchingLabels = [
            'not_matched' => 'bg-gray-100 text-gray-600',
            'process_matching' => 'bg-blue-100 text-blue-800',
            'waiting_result' => 'bg-yellow-100 text-yellow-800',
            'matched' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];

        $matchingLabel = fn (?string $status): string => \App\Enums\MatchingStatus::tryFrom($status ?? '')?->label() ?? $status;
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Selamat datang, {{ $student->full_name }}</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-3xl font-bold text-amber-600">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Lamaran</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] + $stats['reviewed'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Dalam Proses</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-3xl font-bold text-purple-600">{{ $stats['interview_scheduled'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Jadwal Interview</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-3xl font-bold text-green-600">{{ $stats['company_accepted'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Diterima</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Status Profil</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Nama Lengkap</span>
                    <span class="text-sm {{ $student->full_name ? 'text-green-600' : 'text-red-500' }}">
                        {{ $student->full_name ? 'Terisi' : 'Belum' }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Nomor Telepon</span>
                    <span class="text-sm {{ $student->phone_number ? 'text-green-600' : 'text-red-500' }}">
                        {{ $student->phone_number ? 'Terisi' : 'Belum' }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Tanggal Lahir</span>
                    <span class="text-sm {{ $student->birth_date ? 'text-green-600' : 'text-red-500' }}">
                        {{ $student->birth_date ? 'Terisi' : 'Belum' }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">SSW Category</span>
                    <span class="text-sm {{ $student->sswCategories->isNotEmpty() ? 'text-green-600' : 'text-red-500' }}">
                        {{ $student->sswCategories->isNotEmpty() ? $student->sswCategories->pluck('name')->join(', ') : 'Belum' }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Status Kelengkapan</span>
                    @if ($student->isProfileComplete())
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">Lengkap</span>
                    @else
                        <a href="{{ route('student.profile.edit') }}" class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-medium hover:bg-red-200">Lengkapi Profil</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Status Matching</h2>
            @php
                $matching = [
                    'label' => $matchingLabel($student->matching_status),
                    'color' => $matchingLabels[$student->matching_status] ?? 'bg-gray-100 text-gray-600',
                ];
            @endphp
            <div class="text-center py-4">
                <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold {{ $matching['color'] }}">
                    {{ $matching['label'] }}
                </span>
                @if ($student->matching_status === \App\Enums\MatchingStatus::Matched->value && $student->matched_company_name)
                    <p class="text-sm text-gray-600 mt-2">Perusahaan: <strong>{{ $student->matched_company_name }}</strong></p>
                @endif
            </div>
            @if ($student->matching_status !== \App\Enums\MatchingStatus::Matched->value)
                <p class="text-xs text-gray-400 text-center">Status matching akan diperbarui otomatis saat ada perusahaan yang menerima lamaran Anda.</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Lamaran Terbaru</h2>
            <a href="{{ route('student.applications.index') }}" class="text-sm text-amber-600 hover:underline">Lihat Semua</a>
        </div>
        @if ($recentApplications->isEmpty())
            <div class="text-center py-8 text-sm text-gray-500">
                Belum ada lamaran. <a href="{{ route('student.jobs.index') }}" class="text-amber-600 hover:underline">Cari lowongan</a>
            </div>
        @else
            <div class="divide-y">
                @foreach ($recentApplications as $app)
                    <div class="py-3 flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $app->jobListing->title }}</p>
                            <p class="text-xs text-gray-500">{{ $app->jobListing->company_name }} &middot; {{ $app->applied_at->format('d M Y') }}</p>
                        </div>
                        @php $status = ['label' => $statusLabel($app->status), 'color' => $statusLabels[$app->status] ?? 'bg-gray-100 text-gray-600']; @endphp
                        <span class="text-xs px-2 py-1 rounded-full font-medium {{ $status['color'] }}">{{ $status['label'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Notifikasi</h2>
            @if ($unreadNotifications > 0)
                <form method="POST" action="{{ route('student.notifications.markAllRead') }}">
                    @csrf
                    <button type="submit" class="text-sm text-amber-600 hover:underline">Tandai semua dibaca</button>
                </form>
            @endif
        </div>
        <div class="mt-3">
            @if ($unreadNotifications > 0)
                <p class="text-sm text-gray-600">Anda memiliki <strong>{{ $unreadNotifications }}</strong> notifikasi yang belum dibaca.</p>
            @else
                <p class="text-sm text-gray-500">Tidak ada notifikasi baru.</p>
            @endif
        </div>
    </div>
@endsection
