<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Japan Work Program' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3 min-w-0">
                    <a href="{{ route('student.dashboard') }}" class="text-lg font-bold text-gray-800 whitespace-nowrap">Japan Work Program</a>
                </div>
                <div class="flex items-center gap-4">
                    @php
                        $user = Auth::user();
                        $unreadCount = $user->notifications()->whereNull('read_at')->count();
                        $notifications = $user->notifications()->latest()->limit(10)->get();
                    @endphp
                    <div class="relative">
                        <button onclick="document.getElementById('notif-dropdown').classList.toggle('hidden')" aria-label="Notifikasi" aria-haspopup="true" aria-expanded="false" class="relative text-gray-600 hover:text-amber-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            @if ($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-4 h-4 rounded-full flex items-center justify-center" aria-hidden="true">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                            @endif
                        </button>
                        <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border z-50 max-h-96 overflow-y-auto">
                            <div class="p-3 border-b flex justify-between items-center">
                                <span class="font-semibold text-sm text-gray-700">Notifikasi</span>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('student.notifications.index') }}" class="text-xs text-amber-600 hover:underline">Lihat Semua</a>
                                    @if ($unreadCount > 0)
                                        <form method="POST" action="{{ route('student.notifications.markAllRead') }}">
                                            @csrf
                                            <button type="submit" class="text-xs text-amber-600 hover:underline">Tandai semua dibaca</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            @forelse ($notifications as $notification)
                                @php
                                    $data = $notification->data;
                                    $type = $data['type'] ?? '';
                                @endphp
                                <div class="p-3 border-b hover:bg-gray-50 {{ $notification->read_at ? '' : 'bg-amber-50' }}">
                                    @if ($type === 'application_status_changed')
                                        <p class="text-sm text-gray-800">
                                            Status lamaran <strong>{{ $data['job_title'] }}</strong> diperbarui.
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            → <span class="font-medium">{{ $data['status_label'] }}</span>
                                        </p>
                                    @elseif ($type === 'new_application_received')
                                        <p class="text-sm text-gray-800">
                                            <strong>{{ $data['student_name'] }}</strong> melamar ke <strong>{{ $data['job_title'] }}</strong>.
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">JLPT: {{ $data['jlpt_level'] }}</p>
                                    @else
                                        <p class="text-sm text-gray-800">Notifikasi baru</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            @empty
                                <div class="p-6 text-center text-sm text-gray-500">
                                    Belum ada notifikasi
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <span class="text-sm text-gray-600 hidden sm:inline">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('student.logout') }}" onsubmit="return confirm('Yakin ingin logout?')">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:underline">Logout</button>
                    </form>
                    <button onclick="document.getElementById('mobile-nav').classList.toggle('hidden')" aria-label="Menu" aria-haspopup="true" aria-expanded="false" class="lg:hidden text-gray-600 hover:text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>
            <div id="mobile-nav" class="hidden mt-3 lg:hidden">
                <div class="flex flex-col gap-1">
                    <a href="{{ route('student.dashboard') }}" class="text-sm py-2 {{ request()->routeIs('student.dashboard') ? 'text-amber-600 font-semibold' : 'text-gray-600 hover:text-amber-600' }}">Dashboard</a>
                    <a href="{{ route('student.jobs.index') }}" class="text-sm py-2 {{ request()->routeIs('student.jobs.*') ? 'text-amber-600 font-semibold' : 'text-gray-600 hover:text-amber-600' }}">Lowongan Kerja</a>
                    <a href="{{ route('student.applications.index') }}" class="text-sm py-2 {{ request()->routeIs('student.applications.*') ? 'text-amber-600 font-semibold' : 'text-gray-600 hover:text-amber-600' }}">Lamaran Saya</a>
                    <a href="{{ route('student.bookmarks.index') }}" class="text-sm py-2 {{ request()->routeIs('student.bookmarks.*') ? 'text-amber-600 font-semibold' : 'text-gray-600 hover:text-amber-600' }}">Bookmark</a>
                    <a href="{{ route('student.profile') }}" class="text-sm py-2 {{ request()->routeIs('student.profile') || request()->routeIs('student.profile.edit') || request()->routeIs('student.password') ? 'text-amber-600 font-semibold' : 'text-gray-600 hover:text-amber-600' }}">Profil</a>
                    <span class="text-sm py-2 text-gray-600 sm:hidden">{{ Auth::user()->name }}</span>
                </div>
            </div>
            <div class="hidden lg:flex items-center gap-6 mt-3">
                <a href="{{ route('student.dashboard') }}" class="text-sm {{ request()->routeIs('student.dashboard') ? 'text-amber-600 font-semibold' : 'text-gray-600 hover:text-amber-600' }}">Dashboard</a>
                <a href="{{ route('student.jobs.index') }}" class="text-sm {{ request()->routeIs('student.jobs.*') ? 'text-amber-600 font-semibold' : 'text-gray-600 hover:text-amber-600' }}">Lowongan Kerja</a>
                <a href="{{ route('student.applications.index') }}" class="text-sm {{ request()->routeIs('student.applications.*') ? 'text-amber-600 font-semibold' : 'text-gray-600 hover:text-amber-600' }}">Lamaran Saya</a>
                <a href="{{ route('student.bookmarks.index') }}" class="text-sm {{ request()->routeIs('student.bookmarks.*') ? 'text-amber-600 font-semibold' : 'text-gray-600 hover:text-amber-600' }}">Bookmark</a>
                <a href="{{ route('student.profile') }}" class="text-sm {{ request()->routeIs('student.profile') || request()->routeIs('student.profile.edit') || request()->routeIs('student.password') ? 'text-amber-600 font-semibold' : 'text-gray-600 hover:text-amber-600' }}">Profil</a>
            </div>
        </div>
    </nav>
    <div class="max-w-4xl mx-auto mt-8 px-4">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('click', function(e) {
            var dropdown = document.getElementById('notif-dropdown');
            var btn = e.target.closest('button');
            if (dropdown && !dropdown.contains(e.target) && (!btn || !btn.querySelector('svg'))) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
