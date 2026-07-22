@extends('layouts.portal')

@php
    $buildUrl = function ($overrides) {
        $params = request()->except(['view', 'page']);
        foreach ($overrides as $key => $value) {
            if ($value === null) {
                unset($params[$key]);
            } else {
                $params[$key] = $value;
            }
        }
        return route('portal.index', $params);
    };
@endphp

@section('hero')
<div class="bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-72 h-72 bg-blue-400 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-96 h-96 bg-indigo-400 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-16 md:py-24 relative">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            {{-- Left: Text & Search Form --}}
            <div>
                <div class="mb-8">
                    <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 tracking-tight">
                        Cari Kerja di <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400">Jepang</span>
                    </h1>
                    <p class="text-lg text-blue-100/80 max-w-xl">
                        Ribuan lowongan kerja SSW menanti Anda. Temukan posisi terbaik dan mulai karir di Jepang.
                    </p>
                </div>

                <form method="GET" action="{{ route('portal.index') }}" class="max-w-xl" id="searchForm">
                    <input type="hidden" name="view" value="{{ $view }}">
                    <div class="bg-white rounded-2xl p-2 shadow-2xl shadow-black/20">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                            <div class="md:col-span-5 relative">
                                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau perusahaan..."
                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border-0 rounded-xl text-gray-800 text-sm focus:ring-2 focus:ring-blue-500 placeholder-gray-400"
                                    data-auto-submit data-debounce="400">
                            </div>
                            <div class="md:col-span-4">
                                <select name="job_type" class="w-full px-4 py-3 bg-gray-50 border-0 rounded-xl text-gray-700 text-sm focus:ring-2 focus:ring-blue-500 appearance-none" data-auto-submit>
                                    <option value="">Semua Jenis</option>
                                    <option value="magang" {{ request('job_type') === 'magang' ? 'selected' : '' }}>Magang</option>
                                    <option value="tg" {{ request('job_type') === 'tg' ? 'selected' : '' }}>Tokutei Ginou (SSW)</option>
                                    <option value="engineer" {{ request('job_type') === 'engineer' ? 'selected' : '' }}>Engineer / Gijinkoku</option>
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl hover:bg-blue-700 transition font-semibold text-sm shadow-lg shadow-blue-600/25">
                                    Cari
                                </button>
                            </div>
                        </div>
                        <div class="px-2 pb-2 pt-1 grid grid-cols-2 gap-2">
                            <select name="ssw_category_id" class="w-full px-4 py-2.5 bg-gray-50 border-0 rounded-xl text-gray-700 text-sm focus:ring-2 focus:ring-blue-500 appearance-none" data-auto-submit>
                                <option value="">Semua Kategori</option>
                                @foreach ($sswCategories as $category)
                                    <option value="{{ $category->id }}" {{ request('ssw_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <select name="location" class="w-full px-4 py-2.5 bg-gray-50 border-0 rounded-xl text-gray-700 text-sm focus:ring-2 focus:ring-blue-500 appearance-none" data-auto-submit>
                                <option value="">Semua Prefektur</option>
                                @foreach (config('prefectures.all') as $pref)
                                    <option value="{{ $pref }}" {{ request('location') === $pref ? 'selected' : '' }}>{{ $pref }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

                <div class="flex flex-wrap gap-6 mt-8 text-sm">
                    <div class="flex items-center gap-2 text-blue-100/70">
                        <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <span><strong class="text-white">{{ $totalJobs }}</strong> Lowongan Aktif</span>
                    </div>
                    <div class="flex items-center gap-2 text-blue-100/70">
                        <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <span><strong class="text-white">{{ $sswCategories->count() }}</strong> Kategori SSW</span>
                    </div>
                    <div class="flex items-center gap-2 text-blue-100/70">
                        <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                        </div>
                        <span><strong class="text-white">10+</strong> Kota di Jepang</span>
                    </div>
                </div>
            </div>

            {{-- Right: Hero Carousel --}}
            <div class="hidden lg:block relative" id="heroCarousel">
                {{-- Slides --}}
                <div class="relative rounded-2xl overflow-hidden shadow-2xl shadow-black/30 h-80">
                    {{-- Slide 1 --}}
                    <div class="hero-slide absolute inset-0 opacity-100 transition-opacity duration-700 ease-in-out">
                        <img src="{{ asset('images/hero/office-workers.jpg') }}" alt="Tim kerja di Jepang" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-5">
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                <p class="text-white font-bold text-base">Tim Profesional</p>
                                <p class="text-blue-200/80 text-sm mt-0.5">Bergabung dengan perusahaan terkemuka di Jepang</p>
                            </div>
                        </div>
                    </div>
                    {{-- Slide 2 --}}
                    <div class="hero-slide absolute inset-0 opacity-0 transition-opacity duration-700 ease-in-out">
                        <img src="{{ asset('images/jobs/kaigo-tokyo.jpg') }}" alt="Perawat lansia di Tokyo" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-5">
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                <p class="text-white font-bold text-base">Perawat Lansia (Kaigo)</p>
                                <p class="text-blue-200/80 text-sm mt-0.5">Peluang karir di bidang perawatan lansia</p>
                            </div>
                        </div>
                    </div>
                    {{-- Slide 3 --}}
                    <div class="hero-slide absolute inset-0 opacity-0 transition-opacity duration-700 ease-in-out">
                        <img src="{{ asset('images/jobs/construction-nagoya.jpg') }}" alt="Pekerja konstruksi" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-5">
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                <p class="text-white font-bold text-base">Konstruksi & Infrastruktur</p>
                                <p class="text-blue-200/80 text-sm mt-0.5">Bangun masa depan di Jepang</p>
                            </div>
                        </div>
                    </div>
                    {{-- Slide 4 --}}
                    <div class="hero-slide absolute inset-0 opacity-0 transition-opacity duration-700 ease-in-out">
                        <img src="{{ asset('images/jobs/chef-kobe.jpg') }}" alt="Chef kuliner Jepang" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-5">
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                <p class="text-white font-bold text-base">Kuliner Jepang</p>
                                <p class="text-blue-200/80 text-sm mt-0.5">Jadilah bagian dari industri kuliner</p>
                            </div>
                        </div>
                    </div>

                    {{-- Dot indicators --}}
                    <div class="absolute top-4 right-4 z-20 flex flex-col gap-2" id="heroDots">
                        <button data-slide="0" class="hero-dot w-2.5 h-2.5 rounded-full transition-all duration-300 bg-white scale-110 shadow-lg shadow-white/30"></button>
                        <button data-slide="1" class="hero-dot w-2.5 h-2.5 rounded-full transition-all duration-300 bg-white/40 hover:bg-white/60"></button>
                        <button data-slide="2" class="hero-dot w-2.5 h-2.5 rounded-full transition-all duration-300 bg-white/40 hover:bg-white/60"></button>
                        <button data-slide="3" class="hero-dot w-2.5 h-2.5 rounded-full transition-all duration-300 bg-white/40 hover:bg-white/60"></button>
                    </div>

                    {{-- Slide counter --}}
                    <div class="absolute top-4 left-4 z-20 bg-black/30 backdrop-blur-sm rounded-lg px-3 py-1.5 border border-white/10">
                        <span class="text-white text-xs font-medium" id="heroCounter">1 / 4</span>
                    </div>
                </div>

                {{-- Badge overlay --}}
                <div class="absolute -bottom-4 -left-4 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl px-4 py-2 shadow-lg">
                    <p class="text-white text-sm font-bold">1000+ Berhasil Ditempatkan</p>
                </div>
            </div>

            @push('scripts')
            <script>
                (function () {
                    const carousel = document.getElementById('heroCarousel');
                    if (!carousel) return;

                    const slides = carousel.querySelectorAll('.hero-slide');
                    const dots = carousel.querySelectorAll('.hero-dot');
                    const counter = document.getElementById('heroCounter');
                    const total = slides.length;
                    let current = 0;
                    let timer = null;

                    function goTo(index) {
                        slides[current].classList.replace('opacity-100', 'opacity-0');
                        dots[current].classList.replace('bg-white', 'bg-white/40');
                        dots[current].classList.remove('scale-110', 'shadow-lg', 'shadow-white/30');

                        current = index;

                        slides[current].classList.replace('opacity-0', 'opacity-100');
                        dots[current].classList.replace('bg-white/40', 'bg-white');
                        dots[current].classList.add('scale-110', 'shadow-lg', 'shadow-white/30');
                        counter.textContent = (current + 1) + ' / ' + total;
                    }

                    function next() {
                        goTo((current + 1) % total);
                    }

                    function startAutoplay() {
                        stopAutoplay();
                        timer = setInterval(next, 5000);
                    }

                    function stopAutoplay() {
                        if (timer) {
                            clearInterval(timer);
                            timer = null;
                        }
                    }

                    dots.forEach(function (dot) {
                        dot.addEventListener('click', function () {
                            goTo(parseInt(this.dataset.slide));
                            startAutoplay();
                        });
                    });

                    carousel.addEventListener('mouseenter', stopAutoplay);
                    carousel.addEventListener('mouseleave', startAutoplay);

                    startAutoplay();
                })();
            </script>
            @endpush
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="flex flex-col lg:flex-row gap-6">
    <div class="flex-1">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                        @if (request()->hasAny(['search', 'ssw_category_id', 'job_type', 'jlpt_level', 'location']))
                        Hasil Pencarian
                    @else
                        Lowongan Terbaru
                    @endif
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $jobs->total() }} lowongan ditemukan</p>
            </div>

            <div class="flex items-center gap-2">
                <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden bg-white">
                    <a href="{{ $buildUrl(['view' => 'detail']) }}" title="Detail" class="p-1.5 {{ $view === 'detail' ? 'bg-blue-50 text-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    </a>
                    <div class="w-px h-4 bg-gray-200"></div>
                    <a href="{{ $buildUrl(['view' => 'compact']) }}" title="Sederhana" class="p-1.5 {{ $view === 'compact' ? 'bg-blue-50 text-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                    </a>
                    <div class="w-px h-4 bg-gray-200"></div>
                    <a href="{{ $buildUrl(['view' => 'list']) }}" title="List" class="p-1.5 {{ $view === 'list' ? 'bg-blue-50 text-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </a>
                    <div class="w-px h-4 bg-gray-200"></div>
                    <a href="{{ $buildUrl(['view' => 'grid']) }}" title="Kotak" class="p-1.5 {{ $view === 'grid' ? 'bg-blue-50 text-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                    </a>
                </div>
            </div>
        </div>

        @if ($jobs->count() > 0)
            @if ($view === 'detail')
                {{-- DETAIL VIEW --}}
                <div class="space-y-3">
                    @foreach ($jobs as $job)
                        <a href="{{ route('portal.show', $job) }}" class="block bg-white rounded-xl border border-gray-200 p-5 hover:shadow-lg hover:border-blue-300 hover:-translate-y-0.5 transition-all duration-200 group">
                            <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                                @if ($job->thumbnail_display_url)
                                    <img src="{{ $job->thumbnail_display_url }}" alt="{{ $job->company_name }}" class="w-12 h-12 rounded-xl object-cover flex-shrink-0 shadow-md" loading="lazy">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center flex-shrink-0 shadow-md shadow-blue-500/20">
                                        <span class="text-white font-bold text-lg">{{ substr($job->company_name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <h3 class="font-bold text-gray-900 group-hover:text-blue-600 transition text-base line-clamp-1">{{ $job->title }}</h3>
                                            <p class="text-sm text-gray-600 mt-0.5">{{ $job->company_name }}</p>
                                        </div>
                                        @if ($job->deadline && $job->deadline->diffInDays(now()) <= 7)
                                            <span class="flex-shrink-0 bg-red-50 text-red-600 border border-red-200 text-xs px-2.5 py-1 rounded-full font-medium">Closing</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3 mt-2">
                                        <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                                            {{ $job->applications_count }} pelamar
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                            {{ $job->location }}
                                        </span>
                                        @if ($job->sswCategory)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">{{ $job->sswCategory->name }}</span>
                                        @endif
                                        @if ($job->jlpt_level_required)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">JLPT {{ $job->jlpt_level_required }}</span>
                                        @endif
                                        @if ($job->participant_status_required && $job->participant_status_required !== 'any')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $job->participant_status_required === 'ex' ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-green-50 text-green-700 border border-green-100' }}">
                                                {{ $job->participant_status_required === 'ex' ? 'Eks' : 'New Comer' }}
                                            </span>
                                        @endif
                                    </div>
                                    @if ($job->salary_min || $job->salary_max)
                                        <div class="mt-3 flex items-center gap-1.5">
                                            <span class="text-sm font-bold text-green-700">
                                                @if ($job->salary_min && $job->salary_max)
                                                    ¥{{ number_format($job->salary_min) }} - ¥{{ number_format($job->salary_max) }}
                                                @elseif ($job->salary_min)
                                                    Mulai ¥{{ number_format($job->salary_min) }}
                                                @else
                                                    S.d ¥{{ number_format($job->salary_max) }}
                                                @endif
                                            </span>
                                            <span class="text-xs text-gray-400">/ bulan</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="hidden sm:flex items-center">
                                    <svg class="w-5 h-5 text-gray-300 group-hover:text-blue-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

            @elseif ($view === 'compact')
                {{-- COMPACT / SEDERHANA VIEW --}}
                <div class="space-y-2">
                    @foreach ($jobs as $job)
                        <a href="{{ route('portal.show', $job) }}" class="flex items-center gap-3 bg-white rounded-lg border border-gray-200 px-4 py-3 hover:shadow-md hover:border-blue-300 transition group">
                            @if ($job->thumbnail_display_url)
                                <img src="{{ $job->thumbnail_display_url }}" alt="{{ $job->company_name }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0" loading="lazy">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-bold text-sm">{{ substr($job->company_name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition text-sm line-clamp-1">{{ $job->title }}</h3>
                                <div class="flex items-center gap-2 mt-0.5 text-xs text-gray-500">
                                    <span>{{ $job->company_name }}</span>
                                    <span class="text-gray-300">·</span>
                                    <span>{{ $job->location }}</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-1.5 flex-shrink-0">
                                @if ($job->sswCategory)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700">{{ $job->sswCategory->name }}</span>
                                @endif
                                @if ($job->jlpt_level_required)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-purple-50 text-purple-700">{{ $job->jlpt_level_required }}</span>
                                @endif
                                @if ($job->salary_min || $job->salary_max)
                                    <span class="text-xs font-bold text-green-700">
                                        @if ($job->salary_min && $job->salary_max)
                                            ¥{{ number_format($job->salary_min) }}-{{ number_format($job->salary_max) }}
                                        @elseif ($job->salary_min)
                                            ¥{{ number_format($job->salary_min) }}~
                                        @else
                                            ~¥{{ number_format($job->salary_max) }}
                                        @endif
                                    </span>
                                @endif
                            </div>
                            <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-500 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endforeach
                </div>

            @elseif ($view === 'list')
                {{-- LIST VIEW --}}
                <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100 overflow-hidden">
                    @foreach ($jobs as $job)
                        <a href="{{ route('portal.show', $job) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition group">
                            @if ($job->thumbnail_display_url)
                                <img src="{{ $job->thumbnail_display_url }}" alt="{{ $job->company_name }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0" loading="lazy">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-bold text-sm">{{ substr($job->company_name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0 grid grid-cols-1 sm:grid-cols-4 gap-1">
                                <div class="sm:col-span-2">
                                    <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition text-sm line-clamp-1">{{ $job->title }}</h3>
                                    <p class="text-xs text-gray-500">{{ $job->company_name }}</p>
                                </div>
                                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    {{ $job->location }}
                                </div>
                                <div class="flex items-center gap-1.5">
                                    @if ($job->sswCategory)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700">{{ $job->sswCategory->name }}</span>
                                    @endif
                                    @if ($job->jlpt_level_required)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-purple-50 text-purple-700">{{ $job->jlpt_level_required }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-shrink-0 text-right hidden sm:block">
                                @if ($job->salary_min || $job->salary_max)
                                    <div class="text-xs font-bold text-green-700">
                                        @if ($job->salary_min && $job->salary_max)
                                            ¥{{ number_format($job->salary_min) }} - ¥{{ number_format($job->salary_max) }}
                                        @elseif ($job->salary_min)
                                            Mulai ¥{{ number_format($job->salary_min) }}
                                        @else
                                            S.d ¥{{ number_format($job->salary_max) }}
                                        @endif
                                    </div>
                                @endif
                                @if ($job->deadline && $job->deadline->diffInDays(now()) <= 7)
                                    <span class="text-[10px] text-red-500 font-medium">Closing {{ $job->deadline->diffForHumans() }}</span>
                                @endif
                            </div>
                            <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-500 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endforeach
                </div>

            @else
                {{-- GRID / KOTAK VIEW --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($jobs as $job)
                        <a href="{{ route('portal.show', $job) }}" class="block bg-white rounded-xl border border-gray-200 p-4 hover:shadow-lg hover:border-blue-300 transition group">
                            <div class="flex items-start gap-3 mb-3">
                                @if ($job->thumbnail_display_url)
                                    <img src="{{ $job->thumbnail_display_url }}" alt="{{ $job->company_name }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0" loading="lazy">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-bold text-sm">{{ substr($job->company_name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <h3 class="font-bold text-gray-900 group-hover:text-blue-600 transition text-sm line-clamp-2">{{ $job->title }}</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $job->company_name }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-1.5 mb-2">
                                <span class="inline-flex items-center gap-1 text-[11px] text-gray-500">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    {{ $job->location }}
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center gap-1.5">
                                @if ($job->sswCategory)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-100">{{ $job->sswCategory->name }}</span>
                                @endif
                                @if ($job->jlpt_level_required)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-purple-50 text-purple-700 border border-purple-100">{{ $job->jlpt_level_required }}</span>
                                @endif
                                @if ($job->deadline && $job->deadline->diffInDays(now()) <= 7)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-50 text-red-600 border border-red-200">Closing</span>
                                @endif
                            </div>
                            @if ($job->salary_min || $job->salary_max)
                                <div class="mt-2.5 pt-2.5 border-t border-gray-100">
                                    <span class="text-xs font-bold text-green-700">
                                        @if ($job->salary_min && $job->salary_max)
                                            ¥{{ number_format($job->salary_min) }} - ¥{{ number_format($job->salary_max) }}
                                        @elseif ($job->salary_min)
                                            Mulai ¥{{ number_format($job->salary_min) }}
                                        @else
                                            S.d ¥{{ number_format($job->salary_max) }}
                                        @endif
                                        <span class="text-gray-400 font-normal">/ bln</span>
                                    </span>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="mt-8">
                {{ $jobs->links() }}
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 text-center py-16">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <p class="text-gray-600 font-medium mb-1">Tidak ada lowongan ditemukan</p>
                <p class="text-gray-400 text-sm mb-4">Coba ubah filter pencarian Anda</p>
                <a href="{{ route('portal.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Lihat Semua Lowongan</a>
            </div>
        @endif
    </div>

    <aside class="lg:w-80 space-y-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-bold text-gray-900 mb-3">Kategori SSW</h3>
            <div class="space-y-1.5">
                @foreach ($sswCategories as $category)
                    <a href="{{ route('portal.index', ['ssw_category_id' => $category->id]) }}"
                       class="flex items-center justify-between px-3 py-2 rounded-lg text-sm {{ request('ssw_category_id') == $category->id ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }} transition">
                        <span>{{ $category->name }}</span>
                        <span class="text-xs text-gray-400">{{ $category->job_listings_count }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl p-5 text-white">
            <h3 class="font-bold mb-2">Belum Punya Akun?</h3>
            <p class="text-blue-100 text-sm mb-4">Daftar sekarang untuk melamar lowongan kerja di Jepang.</p>
            <a href="{{ route('student.register') }}" class="block text-center bg-white text-blue-700 py-2.5 rounded-lg font-semibold text-sm hover:bg-blue-50 transition">
                Daftar Gratis
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-bold text-gray-900 mb-3">Tips Melamar</h3>
            <ul class="space-y-3 text-sm text-gray-600">
                <li class="flex gap-2">
                    <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 text-xs font-bold">1</span>
                    <span>Lengkapi profil Anda terlebih dahulu</span>
                </li>
                <li class="flex gap-2">
                    <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 text-xs font-bold">2</span>
                    <span>Pilih lowongan sesuai kualifikasi</span>
                </li>
                <li class="flex gap-2">
                    <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 text-xs font-bold">3</span>
                    <span>Kirim lamaran dan pantau statusnya</span>
                </li>
            </ul>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var form = document.getElementById('searchForm');
    var timers = {};

    form.querySelectorAll('[data-auto-submit]').forEach(function (el) {
        var evt = el.tagName === 'SELECT' ? 'change' : 'input';
        var delay = parseInt(el.getAttribute('data-debounce')) || 0;

        el.addEventListener(evt, function () {
            var key = el.getAttribute('name');
            if (timers[key]) clearTimeout(timers[key]);
            timers[key] = setTimeout(function () {
                form.submit();
            }, delay);
        });
    });
})();
</script>
@endpush
