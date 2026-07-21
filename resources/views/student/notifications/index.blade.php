@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Notifikasi</h1>
            <p class="text-sm text-gray-500 mt-1">Riwayat notifikasi lamaran Anda</p>
        </div>
        @if ($notifications->whereNull('read_at')->count() > 0)
            <form method="POST" action="{{ route('student.notifications.markAllRead') }}">
                @csrf
                <button type="submit" class="text-sm text-amber-600 hover:underline">Tandai semua dibaca</button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow divide-y">
        @forelse ($notifications as $notification)
            @php $data = $notification->data @endphp
            <div class="p-4 {{ $notification->read_at ? '' : 'bg-amber-50' }}">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        @if (($data['type'] ?? '') === 'application_status_changed')
                            <p class="text-sm text-gray-800">
                                Status lamaran <strong>{{ $data['job_title'] }}</strong> diperbarui.
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                → <span class="font-medium">{{ $data['status_label'] }}</span>
                            </p>
                        @elseif (($data['type'] ?? '') === 'new_application_received')
                            <p class="text-sm text-gray-800">
                                Lamaran baru dari <strong>{{ $data['student_name'] }}</strong> untuk <strong>{{ $data['job_title'] }}</strong>.
                            </p>
                        @else
                            <p class="text-sm text-gray-800">Notifikasi baru</p>
                        @endif
                    </div>
                    @unless ($notification->read_at)
                        <span class="w-2 h-2 bg-amber-500 rounded-full flex-shrink-0 mt-1.5"></span>
                    @endunless
                </div>
                <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
        @empty
            <div class="p-12 text-center text-sm text-gray-500">
                Belum ada notifikasi.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
@endsection
