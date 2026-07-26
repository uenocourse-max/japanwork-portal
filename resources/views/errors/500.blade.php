<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kesalahan Server</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="mb-6">
            <span class="text-8xl font-extrabold text-gray-200">500</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Kesalahan Server</h1>
        <p class="text-gray-500 mb-8 max-w-md mx-auto">Terjadi kesalahan pada server. Tim kami sudah dikirim notifikasi dan sedang memperbaikinya.</p>
        <div class="flex items-center justify-center gap-4">
            <a href="{{ url('/') }}" class="bg-amber-500 text-white px-6 py-2.5 rounded-lg hover:bg-amber-600 transition font-medium">
                Muat Ulang
            </a>
        </div>
    </div>
</body>
</html>
