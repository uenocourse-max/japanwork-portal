@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Lamaran Saya</h1>
        <a href="{{ route('student.jobs.index') }}" class="text-sm text-amber-600 hover:underline">← Lowongan Kerja</a>
    </div>

    @if ($applications->count() > 0)
        <div class="space-y-4">
            @foreach ($applications as $application)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <a href="{{ route('student.jobs.show', $application->jobListing) }}" class="font-semibold text-gray-800 hover:text-amber-600">
                                {{ $application->jobListing->title }}
                            </a>
                            <p class="text-sm text-gray-600">{{ $application->jobListing->company_name }} · {{ $application->jobListing->location }}</p>
                        </div>
                        <div class="ml-4">
                            @php
                                $statusConfig = [
                                    'pending' => ['label' => 'Menunggu', 'class' => 'bg-yellow-100 text-yellow-800'],
                                    'reviewed' => ['label' => 'Sudah Direview', 'class' => 'bg-yellow-100 text-yellow-800'],
                                    'accepted' => ['label' => 'Diterima', 'class' => 'bg-green-100 text-green-800'],
                                    'interview_scheduled' => ['label' => 'Jadwal Interview', 'class' => 'bg-orange-100 text-orange-800'],
                                    'company_accepted' => ['label' => 'Diterima Perusahaan', 'class' => 'bg-blue-100 text-blue-800'],
                                    'not_passed' => ['label' => 'Tidak Lolos', 'class' => 'bg-red-100 text-red-800'],
                                    'rejected' => ['label' => 'Ditolak', 'class' => 'bg-red-100 text-red-800'],
                                    'withdrawn' => ['label' => 'Ditarik', 'class' => 'bg-gray-100 text-gray-500'],
                                ];
                                $status = $statusConfig[$application->status] ?? ['label' => $application->status, 'class' => 'bg-gray-100 text-gray-800'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['class'] }}">
                                {{ $status['label'] }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        @php
                            $timelineStatuses = ['pending', 'reviewed', 'accepted', 'interview_scheduled', 'company_accepted'];
                            $currentIndex = array_search($application->status, $timelineStatuses);
                            $isTerminal = in_array($application->status, ['rejected', 'not_passed', 'withdrawn']);

                            $stepColors = [
                                'pending' => ['active' => 'bg-yellow-400', 'line' => 'bg-yellow-400', 'text' => 'text-yellow-600', 'ring' => 'ring-yellow-200'],
                                'reviewed' => ['active' => 'bg-yellow-500', 'line' => 'bg-yellow-500', 'text' => 'text-yellow-600', 'ring' => 'ring-yellow-200'],
                                'accepted' => ['active' => 'bg-green-500', 'line' => 'bg-green-500', 'text' => 'text-green-600', 'ring' => 'ring-green-200'],
                                'interview_scheduled' => ['active' => 'bg-orange-500', 'line' => 'bg-orange-500', 'text' => 'text-orange-600', 'ring' => 'ring-orange-200'],
                                'company_accepted' => ['active' => 'bg-blue-500', 'line' => 'bg-blue-500', 'text' => 'text-blue-600', 'ring' => 'ring-blue-200'],
                            ];
                            $stepLabels = [
                                'pending' => 'Menunggu',
                                'reviewed' => 'Direview',
                                'accepted' => 'Diterima',
                                'interview_scheduled' => 'Interview',
                                'company_accepted' => 'Lolos',
                            ];
                        @endphp
                        @if ($isTerminal)
                            <div class="flex items-center gap-2 text-sm text-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                <span>
                                    @if ($application->status === 'rejected')
                                        Lamaran ditolak{{ $application->notes ? ': ' . $application->notes : '' }}
                                    @else
                                        Tidak lolos seleksi{{ $application->notes ? ': ' . $application->notes : '' }}
                                    @endif
                                </span>
                            </div>
                        @elseif ($currentIndex !== false)
                            @php $currentStepColor = $stepColors[$timelineStatuses[$currentIndex]] ?? ['active' => 'bg-yellow-400', 'text' => 'text-yellow-600', 'ring' => 'ring-yellow-200']; @endphp
                            {{-- Dots + Lines --}}
                            <div class="relative flex items-center">
                                @foreach ($timelineStatuses as $i => $s)
                                    @php
                                        $isActive = $i <= $currentIndex;
                                        $isCurrent = $i === $currentIndex;
                                        $color = $stepColors[$s];
                                        $position = ($i / (count($timelineStatuses) - 1)) * 100;
                                    @endphp
                                    {{-- Dot --}}
                                    <div class="absolute z-10" style="left: {{ $position }}%; transform: translateX(-50%);">
                                        <div class="w-4 h-4 rounded-full {{ $isActive ? $color['active'] : 'bg-gray-300' }} {{ $isCurrent ? 'ring-4 ' . $color['ring'] . ' scale-110' : '' }} transition-all"></div>
                                    </div>
                                    {{-- Line --}}
                                    @if ($i < count($timelineStatuses) - 1)
                                        @php
                                            $nextIsActive = ($i + 1) <= $currentIndex;
                                            $nextColor = $stepColors[$timelineStatuses[$i + 1]];
                                            $nextPosition = (($i + 1) / (count($timelineStatuses) - 1)) * 100;
                                        @endphp
                                        <div class="absolute h-1 {{ $nextIsActive ? $nextColor['line'] : 'bg-gray-300' }}"
                                            style="left: {{ $position }}%; width: {{ $nextPosition - $position }}%;"></div>
                                    @endif
                                @endforeach
                                {{-- Spacer for height --}}
                                <div class="w-4 h-4"></div>
                            </div>
                            {{-- Labels --}}
                            <div class="relative flex items-start mt-2">
                                @foreach ($timelineStatuses as $i => $s)
                                    @php
                                        $isActive = $i <= $currentIndex;
                                        $color = $stepColors[$s];
                                        $position = ($i / (count($timelineStatuses) - 1)) * 100;
                                    @endphp
                                    <div class="absolute text-center" style="left: {{ $position }}%; transform: translateX(-50%); width: 80px;">
                                        <span class="text-[10px] leading-tight {{ $isActive ? $color['text'] . ' font-semibold' : 'text-gray-400' }}">
                                            {{ $stepLabels[$s] }}
                                        </span>
                                    </div>
                                @endforeach
                                <div class="w-full h-4"></div>
                            </div>
                        @endif
                    </div>
                    <div class="mt-2 flex items-center gap-4 text-xs text-gray-500">
                        <span>Dilamar: {{ $application->applied_at->format('d M Y H:i') }}</span>
                        @if ($application->reviewed_at)
                            <span>Direview: {{ $application->reviewed_at->format('d M Y H:i') }}</span>
                        @endif
                    </div>
                    @if ($application->status === 'pending')
                        <div class="mt-3">
                            <form method="POST" action="{{ route('student.applications.withdraw', $application) }}" onsubmit="return confirm('Yakin ingin membatalkan lamaran ini?')">
                                @csrf
                                <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium">Batalkan Lamaran</button>
                            </form>
                        </div>
                    @endif

                    @if (in_array($application->status, ['interview_scheduled', 'company_accepted', 'not_passed']) && $application->interview_date)
                        <div class="mt-3 border border-amber-200 rounded-lg p-3 bg-amber-50">
                            <div class="text-sm font-medium text-amber-800 mb-2">Detail Interview</div>
                            <div class="grid grid-cols-2 gap-2 text-xs text-amber-700">
                                <div>
                                    <span class="font-medium">Jenis:</span>
                                    {{ $application->interview_type === 'online' ? 'Online (Meeting)' : 'Langsung' }}
                                </div>
                                <div>
                                    <span class="font-medium">Tanggal:</span>
                                    {{ \Carbon\Carbon::parse($application->interview_date)->format('d M Y H:i') }}
                                </div>
                                <div class="col-span-2">
                                    <span class="font-medium">Lokasi / Link:</span>
                                    {{ $application->interview_location }}
                                </div>
                                @if ($application->interview_notes)
                                    <div class="col-span-2">
                                        <span class="font-medium">Catatan:</span>
                                        {{ $application->interview_notes }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if ($application->notes)
                        <div class="mt-3 bg-gray-50 rounded p-3 text-sm text-gray-600">
                            <strong>Catatan:</strong> {{ $application->notes }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $applications->links() }}
        </div>
    @else
        <div class="text-center py-12 text-gray-500">
            <p class="text-lg mb-2">Belum ada lamaran</p>
            <p class="text-sm">Mulai cari lowongan dan kirim lamaran Anda</p>
            <a href="{{ route('student.jobs.index') }}" class="inline-block mt-4 bg-amber-500 text-white px-6 py-2 rounded-md hover:bg-amber-600 transition">
                Lihat Lowongan
            </a>
        </div>
    @endif
</div>
@endsection
