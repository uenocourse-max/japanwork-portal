@extends('layouts.guest')

@section('content')
<div class="bg-white rounded-lg shadow-md p-8">
    <h1 class="text-2xl font-bold text-center mb-6">Login Siswa</h1>

    <form method="POST" action="{{ route('student.login.store') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <div class="flex justify-between items-center mb-1">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <a href="{{ route('password.request') }}" class="text-xs text-amber-600 hover:underline">Lupa password?</a>
            </div>
            <input type="password" id="password" name="password" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-amber-500 text-white py-2 rounded-md hover:bg-amber-600 transition">
            Login
        </button>
    </form>

    <p class="text-center text-sm text-gray-600 mt-4">
        Belum punya akun? <a href="{{ route('student.register') }}" class="text-amber-600 hover:underline">Daftar</a>
    </p>
    <div class="mt-4 pt-4 border-t border-gray-200 text-center">
        <p class="text-xs text-gray-400">LPK/TSK?</p>
        <a href="{{ url('/recruiter/login') }}" class="text-sm text-blue-600 hover:underline font-medium">Login sebagai Recruiter</a>
    </div>
</div>
@endsection
