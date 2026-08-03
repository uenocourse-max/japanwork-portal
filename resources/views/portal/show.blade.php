@extends('layouts.portal')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-5">
        <a href="{{ route('portal.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 transition mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Lowongan
        </a>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-8">
                <div class="flex items-start gap-4">
                    @if ($job->thumbnail_display_url)
                        <img src="{{ $job->thumbnail_display_url }}" alt="{{ $job->company_name }}" class="w-16 h-16 rounded-2xl object-cover flex-shrink-0 shadow-lg">
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center flex-shrink-0 shadow-lg">
                            <span class="text-white font-bold text-2xl">{{ substr($job->company_name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ $job->title }}</h1>
                        <p class="text-blue-100 mt-1 text-lg">{{ $job->company_name }}</p>
                        <div class="flex flex-wrap items-center gap-3 mt-3">
                            <span class="inline-flex items-center gap-1.5 text-sm text-blue-100/90">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                {{ $job->location }}
                            </span>
                            @if ($job->sswCategory)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white">{{ $job->sswCategory->name }}</span>
                            @endif
                            @if ($job->deadline && $job->deadline->diffInDays(now()) <= 7)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500 text-white">Closing {{ $job->deadline->diffForHumans() }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-6">
                <div class="prose prose-gray max-w-none">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Deskripsi Pekerjaan
                    </h2>
                    <div class="text-gray-600 whitespace-pre-line mt-3 leading-relaxed">{{ $job->description }}</div>

                    @if ($job->requirements)
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2 mt-8">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            Persyaratan
                        </h2>
                        <div class="text-gray-600 whitespace-pre-line mt-3 leading-relaxed">{{ $job->requirements }}</div>
                    @endif

                    @if ($job->company_description)
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2 mt-8">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Tentang Perusahaan
                        </h2>
                        <div class="text-gray-600 whitespace-pre-line mt-3 leading-relaxed">{{ $job->company_description }}</div>
                    @endif
                </div>
            </div>
        </div>

        @if ($relatedJobs->count() > 0)
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="font-bold text-gray-900 mb-4">Lowongan Serupa</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($relatedJobs as $related)
                        <a href="{{ route('portal.show', $related) }}" class="block border border-gray-100 rounded-lg p-4 hover:shadow-md hover:border-blue-300 transition group">
                            <div class="flex items-start gap-3">
                                @if ($related->thumbnail_display_url)
                                    <img src="{{ $related->thumbnail_display_url }}" alt="{{ $related->company_name }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0" loading="lazy">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-bold text-sm">{{ substr($related->company_name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="font-semibold text-gray-800 text-sm group-hover:text-blue-600 transition line-clamp-1">{{ $related->title }}</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $related->company_name }}</p>
                                    @if ($related->sswCategory)
                                        <span class="inline-block mt-1.5 bg-blue-50 text-blue-700 text-[11px] px-2 py-0.5 rounded-md font-medium">{{ $related->sswCategory->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden sticky top-20">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Ringkasan Lowongan</h3>
            </div>
            <div class="px-6 py-5">
                <dl class="space-y-4">
                    <div class="flex justify-between items-center">
                        <dt class="text-sm text-gray-500">Perusahaan</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $job->company_name }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-sm text-gray-500">Lokasi</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $job->location }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-sm text-gray-500">Kategori SSW</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $job->sswCategory?->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-sm text-gray-500">Jenis</dt>
                        <dd>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $job->job_type === \App\Enums\JobType::Magang->value ? 'bg-blue-50 text-blue-700 border border-blue-100' : ($job->job_type === \App\Enums\JobType::TokuteiGinou->value ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-green-50 text-green-700 border border-green-100') }}">
                                {{ \App\Enums\JobType::tryFrom($job->job_type)?->label() ?? $job->job_type }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-sm text-gray-500">JLPT</dt>
                        <dd>
                            @if ($job->jlpt_level_required)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-100">{{ $job->jlpt_level_required }}</span>
                            @else
                                <span class="text-sm text-gray-400">Tidak ditentukan</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-sm text-gray-500">Status Peserta</dt>
                        <dd>
                            <span class="text-sm font-semibold text-gray-900">
                                {{ match($job->participant_status_required) { 'ex' => 'Eks', 'new_comer' => 'New Comer', default => 'Semua' } }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-sm text-gray-500">Gaji / bulan</dt>
                        <dd class="text-sm font-bold text-green-700">
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
                    <div class="flex justify-between items-center">
                        <dt class="text-sm text-gray-500">Pelamar</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $job->applications_count }} orang</dd>
                    </div>
                    @if ($job->deadline)
                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-gray-500">Deadline</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $job->deadline->format('d M Y') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="px-6 pb-6 space-y-3">
                @auth
                    @if (Auth::user()->role === 'student')
                        <form method="POST" action="{{ route('student.jobs.apply', $job) }}" id="portal-apply-form">
                            @csrf
                            <input type="hidden" name="confirmed" value="1">
                            <button type="submit" class="w-full bg-blue-600 text-white py-3.5 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-600/25 flex items-center justify-center gap-2" id="portal-apply-btn">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Lamar Sekarang
                            </button>
                        </form>
                        <form method="POST" action="{{ route('student.jobs.bookmark', $job) }}">
                            @csrf
                            <button type="submit" class="w-full border-2 border-gray-200 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-50 hover:border-gray-300 transition flex items-center justify-center gap-2">
                                @if ($isSaved)
                                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                    Hapus Bookmark
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                    Bookmark
                                @endif
                            </button>
                        </form>
                    @else
                        <p class="text-sm text-gray-500 text-center py-2">Hanya siswa yang bisa melamar</p>
                    @endif
                @else
                    <div class="space-y-3">
                        <a href="{{ route('student.register') }}" class="block w-full bg-blue-600 text-white py-3.5 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-600/25 text-center flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            Daftar & Lamar
                        </a>
                        <a href="{{ route('student.login') }}" class="block w-full border-2 border-gray-200 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-50 hover:border-gray-300 transition text-center">
                            Masuk untuk Melamar
                        </a>
                        <p class="text-xs text-gray-400 text-center">Daftar gratis dalam 2 menit</p>
                    </div>
                @endauth
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h4 class="font-bold text-gray-900 text-sm mb-3">Diposting oleh</h4>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $job->poster?->company_name ?? $job->poster?->name }}</p>
                    <p class="text-xs text-gray-500">Recruiter</p>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($sswMismatch)
    <div id="portal-ssw-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
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
                <button id="portal-modal-cancel" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">Batal</button>
                <button id="portal-modal-confirm" class="flex-1 px-4 py-2.5 bg-amber-500 text-white rounded-lg hover:bg-amber-600 font-medium text-sm transition">Ya, Lamar Saja</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('portal-apply-form');
            const modal = document.getElementById('portal-ssw-modal');
            const confirmBtn = document.getElementById('portal-modal-confirm');
            const cancelBtn = document.getElementById('portal-modal-cancel');

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
