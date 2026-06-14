<!DOCTYPE html>
<html lang="th">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style> body { font-family: 'Sarabun', 'Segoe UI', sans-serif; } </style>
    </head>
    <body class="antialiased text-gray-900">
        <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 p-4">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-yellow-400 text-blue-900 font-bold text-2xl shadow-lg">SKRU</div>
                <h1 class="mt-4 text-white text-xl font-bold">ระบบจองห้องปฏิบัติการวิทยาศาสตร์</h1>
                <p class="text-blue-100 text-sm">คณะวิทยาศาสตร์และเทคโนโลยี · มหาวิทยาลัยราชภัฏสงขลา</p>
            </div>
            <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-xl rounded-xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
