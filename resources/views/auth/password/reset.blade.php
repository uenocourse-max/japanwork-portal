@extends('layouts.guest')

@section('content')
<div class="bg-white rounded-lg shadow-md p-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Reset Password</h1>
        <p class="text-sm text-gray-500 mt-1">Masukkan password baru Anda.</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ $email ?? old('email') }}"
                required
                autofocus
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500"
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
            <input
                type="password"
                id="password"
                name="password"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500"
            >
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500"
            >
        </div>

        <button type="submit" class="w-full bg-amber-500 text-white py-2 px-4 rounded-md hover:bg-amber-600 transition font-medium">
            Reset Password
        </button>
    </form>
</div>
@endsection
