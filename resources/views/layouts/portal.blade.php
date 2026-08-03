<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Portal Lowongan Kerja Jepang' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('portal.index') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-lg font-bold text-gray-900">JapanWork</span>
                        <span class="hidden sm:block text-[11px] text-gray-500 -mt-1">Portal Lowongan Kerja Jepang</span>
                    </div>
                </a>

                <div class="flex items-center gap-3">
                    @auth
                        @if (Auth::user()->role === 'admin')
                            <a href="{{ url('/admin') }}" class="text-sm text-gray-600 hover:text-blue-600 font-medium">Admin</a>
                        @elseif (Auth::user()->role === 'student')
                            <a href="{{ route('student.profile') }}" class="text-sm text-gray-600 hover:text-blue-600 font-medium">Dashboard</a>
                        @elseif (Auth::user()->role === 'recruiter')
                            <a href="{{ url('/recruiter') }}" class="text-sm text-gray-600 hover:text-blue-600 font-medium">Recruiter</a>
                        @endif
                        <div class="h-4 w-px bg-gray-300 hidden sm:block"></div>
                        <span class="text-sm text-gray-500 hidden sm:inline">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('student.logout') }}" onsubmit="return confirm('Yakin ingin logout?')">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-red-600 transition">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('student.login') }}" class="text-sm text-gray-700 hover:text-blue-600 font-medium px-3 py-2">Masuk</a>
                        <a href="{{ route('student.register') }}" class="bg-blue-600 text-white text-sm px-5 py-2.5 rounded-lg hover:bg-blue-700 transition font-medium shadow-sm">Daftar Gratis</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @yield('hero')

    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <footer class="bg-gray-900 text-gray-400 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-white font-bold">JapanWork</span>
                    </div>
                    <p class="text-sm">Portal lowongan kerja untuk siswa program kerja di Jepang. Temukan peluang karir terbaik Anda.</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold text-sm mb-3">Kategori Populer</h4>
                    <ul class="space-y-2 text-sm">
                        @php
                            $footerCategories = \App\Models\SswCategory::orderBy('name')->limit(4)->get();
                        @endphp
                        @foreach ($footerCategories as $cat)
                            <li><a href="{{ route('portal.index', ['ssw_category_id' => $cat->id]) }}" class="hover:text-white transition">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold text-sm mb-3">Akun</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('student.login') }}" class="hover:text-white transition">Masuk</a></li>
                        <li><a href="{{ route('student.register') }}" class="hover:text-white transition">Daftar Akun Baru</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-6 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} JapanWork. All rights reserved.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
