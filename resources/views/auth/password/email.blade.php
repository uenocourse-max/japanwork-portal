@extends('layouts.guest')

@section('content')
<div class="bg-white rounded-lg shadow-md p-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Lupa Password?</h1>
        <p class="text-sm text-gray-500 mt-1">Masukkan email Anda untuk menerima link reset password.</p>
    </div>

    @if (session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500"
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-amber-500 text-white py-2 px-4 rounded-md hover:bg-amber-600 transition font-medium">
            Kirim Link Reset
        </button>
    </form>

    <div class="mt-6 text-center text-sm">
        <a href="{{ route('student.login') }}" class="text-amber-600 hover:underline">← Kembali ke Login</a>
    </div>
</div>
@endsection
