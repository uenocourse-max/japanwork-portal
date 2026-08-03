@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <a href="{{ route('student.jobs.index') }}" class="text-sm text-amber-600 hover:underline mb-4 inline-block">← Kembali ke Daftar Lowongan</a>

    <div class="flex justify-between items-start mb-6">
        <div class="flex items-start gap-4">
            @if ($job->thumbnail_display_url)
                <img src="{{ $job->thumbnail_display_url }}" alt="{{ $job->company_name }}" class="w-16 h-16 rounded-xl object-cover shadow-md flex-shrink-0">
            @endif
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $job->title }}</h1>
                <p class="text-gray-600 mt-1">{{ $job->company_name }} · {{ $job->location }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            @if ($job->deadline && $job->deadline->diffInDays(now()) <= 7)
                <span class="bg-red-100 text-red-700 text-xs px-3 py-1 rounded-full">Closing {{ $job->deadline->diffForHumans() }}</span>
            @endif
            <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full">Dibuka</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Deskripsi Pekerjaan</h2>
                <p class="text-gray-600 whitespace-pre-line">{{ $job->description }}</p>
            </div>

            @if ($job->requirements)
                <div>
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Persyaratan</h2>
                    <p class="text-gray-600 whitespace-pre-line">{{ $job->requirements }}</p>
                </div>
            @endif

            @if ($job->company_description)
                <div>
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Tentang Perusahaan</h2>
                    <p class="text-gray-600 whitespace-pre-line">{{ $job->company_description }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-700 mb-3">Informasi Lowongan</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Kategori SSW</dt>
                        <dd class="font-medium">{{ $job->sswCategory?->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Jenis Lowongan</dt>
                        <dd class="font-medium">{{ \App\Enums\JobType::tryFrom($job->job_type)?->label() ?? $job->job_type }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">JLPT Diperlukan</dt>
                        <dd class="font-medium">{{ $job->jlpt_level_required ?? 'Tidak ditentukan' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status Peserta</dt>
                        <dd class="font-medium">{{ match($job->participant_status_required) { 'ex' => 'Eks', 'new_comer' => 'New Comer', default => 'Semua' } }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Gaji</dt>
                        <dd class="font-medium text-green-700">
                            @if ($job->salary_min && $job->salary_max)
                                ¥{{ number_format($job->salary_min) }} - ¥{{ number_format($job->salary_max) }}
                            @elseif ($job->salary_min)
                                Mulai ¥{{ number_format($job->salary_min) }}
                            @elseif ($job->salary_max)
                                S.d ¥{{ number_format($job->salary_max) }}
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Total Pelamar</dt>
                        <dd class="font-medium">{{ $job->applications_count }} orang</dd>
                    </div>
                    @if ($job->deadline)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Deadline</dt>
                            <dd class="font-medium">{{ $job->deadline->format('d M Y') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-700 mb-2">Diposting oleh</h3>
                <p class="text-sm text-gray-600">{{ $job->poster?->company_name ?? $job->poster?->name ?? '-' }}</p>
            </div>

            <div class="mt-4 space-y-3">
                @if ($hasApplied)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                        <p class="text-blue-700 font-medium">Anda sudah melamar</p>
                        @if ($application)
                            <p class="text-sm text-blue-600 mt-1">Status:
                                <span class="font-semibold">{{ \App\Enums\ApplicationStatus::tryFrom($application->status)?->label() ?? $application->status }}</span>
                            </p>
                        @endif
                    </div>
                @else
                    <form method="POST" action="{{ route('student.jobs.apply', $job) }}" id="apply-form">
                        @csrf
                        <input type="hidden" name="confirmed" value="1">
                        <button type="submit" class="w-full bg-amber-500 text-white py-3 rounded-lg font-semibold hover:bg-amber-600 transition" id="apply-btn">
                            Lamar Sekarang
                        </button>
                    </form>
                @endif
                <form method="POST" action="{{ route('student.jobs.bookmark', $job) }}">
                    @csrf
                    <button type="submit" class="w-full border border-gray-300 text-gray-700 py-2.5 rounded-lg font-medium hover:bg-gray-50 transition flex items-center justify-center gap-2">
                        @if ($isSaved)
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                            Hapus Bookmark
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                            Bookmark
                        @endif
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@if ($sswMismatch)
    <div id="ssw-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Perhatian</h3>
            </div>
            <p class="text-gray-600 text-sm leading-relaxed">
                Lowongan <strong>TG (Tokutei Ginou)</strong> ini membutuhkan sertifikat SSW <strong>{{ $job->sswCategory?->name }}</strong>, namun SSW Anda tidak sesuai. Anda tetap bisa melamar, namun <strong>kemungkinan tidak akan diproses</strong> oleh recruiter.
            </p>
            <div class="flex gap-3 mt-6">
                <button id="modal-cancel" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">Batal</button>
                <button id="modal-confirm" class="flex-1 px-4 py-2.5 bg-amber-500 text-white rounded-lg hover:bg-amber-600 font-medium text-sm transition">Ya, Lamar Saja</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('apply-form');
            const modal = document.getElementById('ssw-modal');
            const confirmBtn = document.getElementById('modal-confirm');
            const cancelBtn = document.getElementById('modal-cancel');

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                modal.classList.remove('hidden');
            });

            confirmBtn.addEventListener('click', function () {
                modal.classList.add('hidden');
                form.submit();
            });

            cancelBtn.addEventListener('click', function () {
                modal.classList.add('hidden');
            });
        });
    </script>
    @endpush
@endif

@endsection
