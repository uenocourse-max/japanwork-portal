@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6">{{ $student ? 'Edit Profil' : 'Lengkapi Profil' }}</h1>

    <form method="POST" action="{{ route('student.profile.update') }}">
        @csrf
        @method('PUT')

        <h3 class="text-lg font-semibold text-gray-700 mb-3 mt-6">Data Pribadi</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                <input type="text" name="full_name" value="{{ old('full_name', $student->full_name ?? '') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('full_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Umur *</label>
                <input type="number" name="age" value="{{ old('age', $student->age ?? '') }}" required min="15" max="60"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('age') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir *</label>
                <input type="text" name="birth_place" value="{{ old('birth_place', $student->birth_place ?? '') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('birth_place') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir *</label>
                <input type="date" name="birth_date" value="{{ old('birth_date', isset($student) ? $student->birth_date->format('Y-m-d') : '') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('birth_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat *</label>
                <textarea name="address" required rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('address', $student->address ?? '') }}</textarea>
                @error('address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi Badan (cm) *</label>
                <input type="number" name="height_cm" value="{{ old('height_cm', $student->height_cm ?? '') }}" required min="100" max="250"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('height_cm') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Berat Badan (kg) *</label>
                <input type="number" name="weight_kg" value="{{ old('weight_kg', $student->weight_kg ?? '') }}" required min="30" max="200"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('weight_kg') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Golongan Darah *</label>
                <select name="blood_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Pilih</option>
                    @foreach(['A', 'B', 'AB', 'O'] as $type)
                        <option value="{{ $type }}" {{ old('blood_type', $student->blood_type ?? '') === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
                @error('blood_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin *</label>
                <select name="gender" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Pilih</option>
                    <option value="male" {{ old('gender', $student->gender ?? '') === 'male' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="female" {{ old('gender', $student->gender ?? '') === 'female' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('gender') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Perkawinan *</label>
                <select name="marital_status" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Pilih</option>
                    <option value="single" {{ old('marital_status', $student->marital_status ?? '') === 'single' ? 'selected' : '' }}>Single</option>
                    <option value="married" {{ old('marital_status', $student->marital_status ?? '') === 'married' ? 'selected' : '' }}>Married</option>
                </select>
                @error('marital_status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon *</label>
                <input type="text" name="phone_number" value="{{ old('phone_number', $student->phone_number ?? '') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('phone_number') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <h3 class="text-lg font-semibold text-gray-700 mb-3 mt-6">Kemampuan Bahasa Jepang</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Score JFT</label>
                <input type="number" name="jft_score" value="{{ old('jft_score', $student->jft_score ?? '') }}" min="0" max="480"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('jft_score') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Level JLPT</label>
                <select name="jlpt_level" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Tidak Ada</option>
                    @foreach(['N5', 'N4', 'N3', 'N2', 'N1', 'JFT Basic A2'] as $level)
                        <option value="{{ $level }}" {{ old('jlpt_level', $student->jlpt_level ?? '') === $level ? 'selected' : '' }}>{{ $level }}</option>
                    @endforeach
                </select>
                @error('jlpt_level') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lama Belajar (bulan) *</label>
                <input type="number" name="japanese_learning_months" value="{{ old('japanese_learning_months', $student->japanese_learning_months ?? '') }}" required min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('japanese_learning_months') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <h3 class="text-lg font-semibold text-gray-700 mb-3 mt-6">Informasi Program</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Peserta *</label>
                <select name="participant_status" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Pilih</option>
                    <option value="ex" {{ old('participant_status', $student->participant_status ?? '') === 'ex' ? 'selected' : '' }}>Eks</option>
                    <option value="new_comer" {{ old('participant_status', $student->participant_status ?? '') === 'new_comer' ? 'selected' : '' }}>New Comer</option>
                </select>
                @error('participant_status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jalur *</label>
                <select name="pathway" required id="pathway" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Pilih</option>
                    <option value="mandiri" {{ old('pathway', $student->pathway ?? '') === 'mandiri' ? 'selected' : '' }}>Mandiri</option>
                    <option value="lpk" {{ old('pathway', $student->pathway ?? '') === 'lpk' ? 'selected' : '' }}>LPK</option>
                </select>
                @error('pathway') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div id="lpk-field" style="{{ old('pathway', $student->pathway ?? '') !== 'lpk' ? 'display:none' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama LPK *</label>
                <input type="text" name="lpk_name" value="{{ old('lpk_name', $student->lpk_name ?? '') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('lpk_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <h3 class="text-lg font-semibold text-gray-700 mb-3 mt-6">Kategori SSW *</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
            @foreach ($sswCategories as $category)
                <label class="flex items-center gap-2 p-2 border rounded-md hover:bg-gray-50">
                    <input type="checkbox" name="ssw_categories[]" value="{{ $category->id }}"
                        {{ in_array($category->id, old('ssw_categories', isset($student) ? $student->sswCategories->pluck('id')->toArray() : [])) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                    <span class="text-sm">{{ $category->name }}</span>
                </label>
            @endforeach
        </div>
        @error('ssw_categories') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

        <h3 class="text-lg font-semibold text-gray-700 mb-3 mt-6">Status Matching</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Matching</label>
                <select name="matching_status" id="matching_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                    @foreach(['not_matched' => 'Belum Matched', 'process_matching' => 'Proses Matching', 'waiting_result' => 'Menunggu Hasil', 'matched' => 'Matched', 'cancelled' => 'Dibatalkan'] as $value => $label)
                        <option value="{{ $value }}" {{ old('matching_status', $student->matching_status ?? 'not_matched') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('matching_status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div id="company-field" style="{{ old('matching_status', $student->matching_status ?? '') !== 'matched' ? 'display:none' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>
                <input type="text" name="matched_company_name" value="{{ old('matched_company_name', $student->matched_company_name ?? '') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('matched_company_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <h3 class="text-lg font-semibold text-gray-700 mb-3 mt-6">Dokumen</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Link Foto (Google Drive)</label>
                <input type="url" name="photo_drive_url" value="{{ old('photo_drive_url', $student->photo_drive_url ?? '') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('photo_drive_url') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Link CV (Google Drive)</label>
                <input type="url" name="cv_drive_url" value="{{ old('cv_drive_url', $student->cv_drive_url ?? '') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('cv_drive_url') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-6 flex gap-4">
            <button type="submit" class="bg-amber-500 text-white px-6 py-2 rounded-md hover:bg-amber-600 transition">
                Simpan Profil
            </button>
            <a href="{{ route('student.profile') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 transition">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
document.getElementById('pathway').addEventListener('change', function() {
    document.getElementById('lpk-field').style.display = this.value === 'lpk' ? 'block' : 'none';
});
document.getElementById('matching_status').addEventListener('change', function() {
    document.getElementById('company-field').style.display = this.value === 'matched' ? 'block' : 'none';
});
</script>
@endsection
