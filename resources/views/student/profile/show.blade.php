@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Profil Saya</h1>
        <a href="{{ route('student.profile.edit') }}" class="bg-amber-500 text-white px-4 py-2 rounded-md hover:bg-amber-600 transition">
            Edit Profil
        </a>
    </div>

    @if ($student)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Data Pribadi</h3>
            <dl class="space-y-2">
                <div><dt class="text-sm text-gray-500">Nama Lengkap</dt><dd class="font-medium">{{ $student->full_name }}</dd></div>
                <div><dt class="text-sm text-gray-500">Umur</dt><dd class="font-medium">{{ $student->age }} tahun</dd></div>
                <div><dt class="text-sm text-gray-500">Tempat, Tanggal Lahir</dt><dd class="font-medium">{{ $student->birth_place }}, {{ $student->birth_date->format('d M Y') }}</dd></div>
                <div><dt class="text-sm text-gray-500">Alamat</dt><dd class="font-medium">{{ $student->address }}</dd></div>
                <div><dt class="text-sm text-gray-500">Tinggi / Berat</dt><dd class="font-medium">{{ $student->height_cm }} cm / {{ $student->weight_kg }} kg</dd></div>
                <div><dt class="text-sm text-gray-500">Golongan Darah</dt><dd class="font-medium">{{ $student->blood_type }}</dd></div>
                <div><dt class="text-sm text-gray-500">Jenis Kelamin</dt><dd class="font-medium">{{ $student->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</dd></div>
                <div><dt class="text-sm text-gray-500">Status Perkawinan</dt><dd class="font-medium">{{ ucfirst($student->marital_status) }}</dd></div>
                <div><dt class="text-sm text-gray-500">No. Telepon</dt><dd class="font-medium">{{ $student->phone_number }}</dd></div>
            </dl>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Informasi Program</h3>
            <dl class="space-y-2">
                <div><dt class="text-sm text-gray-500">Status Peserta</dt><dd class="font-medium">{{ $student->participant_status === 'ex' ? 'Eks' : 'New Comer' }}</dd></div>
                <div><dt class="text-sm text-gray-500">Score JFT</dt><dd class="font-medium">{{ $student->jft_score ?? '-' }}</dd></div>
                <div><dt class="text-sm text-gray-500">Level JLPT</dt><dd class="font-medium">{{ $student->jlpt_level ?? '-' }}</dd></div>
                <div><dt class="text-sm text-gray-500">Lama Belajar Bahasa Jepang</dt><dd class="font-medium">{{ $student->japanese_learning_months }} bulan</dd></div>
                <div><dt class="text-sm text-gray-500">Jalur</dt><dd class="font-medium">{{ $student->pathway === 'mandiri' ? 'Mandiri' : 'LPK' }}</dd></div>
                @if ($student->lpk_name)
                <div><dt class="text-sm text-gray-500">Nama LPK</dt><dd class="font-medium">{{ $student->lpk_name }}</dd></div>
                @endif
                <div>
                    <dt class="text-sm text-gray-500">Status Matching</dt>
                    <dd class="font-medium">
                        @php
                        $statusColors = [
                            'matched' => 'bg-green-100 text-green-800',
                            'process_matching' => 'bg-yellow-100 text-yellow-800',
                            'waiting_result' => 'bg-blue-100 text-blue-800',
                            'not_matched' => 'bg-gray-100 text-gray-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$student->matching_status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ str_replace('_', ' ', ucfirst($student->matching_status)) }}
                        </span>
                    </dd>
                </div>
                @if ($student->matched_company_name)
                <div><dt class="text-sm text-gray-500">Perusahaan</dt><dd class="font-medium">{{ $student->matched_company_name }}</dd></div>
                @endif
            </dl>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Kategori SSW</h3>
            <div class="flex flex-wrap gap-2">
                @forelse ($student->sswCategories as $category)
                    <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-sm">{{ $category->name }}</span>
                @empty
                    <span class="text-gray-400">Belum ada kategori</span>
                @endforelse
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Dokumen</h3>
            <dl class="space-y-2">
                <div>
                    <dt class="text-sm text-gray-500">Foto</dt>
                    <dd class="font-medium">
                        @if ($student->photo_drive_url)
                            <a href="{{ $student->photo_drive_url }}" target="_blank" class="text-amber-600 hover:underline">Lihat Dokumen</a>
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">CV</dt>
                    <dd class="font-medium">
                        @if ($student->cv_drive_url)
                            <a href="{{ $student->cv_drive_url }}" target="_blank" class="text-amber-600 hover:underline">Lihat Dokumen</a>
                        @else
                            -
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>
    @else
    <p class="text-gray-500">Anda belum melengkapi profil. <a href="{{ route('student.profile.edit') }}" class="text-amber-600 hover:underline">Lengkapi sekarang</a></p>
    @endif

    <div class="mt-6 pt-4 border-t">
        <a href="{{ route('student.password') }}" class="text-sm text-gray-600 hover:text-amber-600">Ubah Password</a>
    </div>
</div>
@endsection
