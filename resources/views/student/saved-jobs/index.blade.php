@extends('layouts.app')

@section('title', 'Bookmark')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Bookmark Tersimpan</h1>
        <p class="text-sm text-gray-500 mt-1">Lowongan yang Anda simpan untuk dilamar nanti</p>
    </div>

    @if ($savedJobs->isEmpty())
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
            <p class="text-gray-500">Belum ada bookmark.</p>
            <a href="{{ route('student.jobs.index') }}" class="text-amber-600 hover:underline text-sm mt-2 inline-block">Cari Lowongan</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($savedJobs as $saved)
                @php $job = $saved->jobListing @endphp
                <div class="bg-white rounded-lg shadow p-5 flex flex-col">
                    <div class="flex items-start gap-3 mb-3">
                        @if ($job->thumbnail_display_url)
                            <img src="{{ $job->thumbnail_display_url }}" alt="" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                        @endif
                        <div class="min-w-0">
                            <a href="{{ route('student.jobs.show', $job) }}" class="font-semibold text-gray-800 hover:text-amber-600 line-clamp-1">{{ $job->title }}</a>
                            <p class="text-xs text-gray-500">{{ $job->company_name }} &middot; {{ $job->location }}</p>
                            @if ($job->sswCategory)
                                <span class="inline-block mt-1 text-xs bg-amber-50 text-amber-700 px-2 py-0.5 rounded">{{ $job->sswCategory->name }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="mt-auto pt-3 border-t flex justify-between items-center">
                        <span class="text-xs text-gray-400">Disimpan {{ $saved->created_at->diffForHumans() }}</span>
                        <form method="POST" action="{{ route('student.jobs.bookmark', $job) }}">
                            @csrf
                            <button type="submit" class="text-xs text-red-500 hover:underline">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $savedJobs->links() }}
        </div>
    @endif
@endsection
