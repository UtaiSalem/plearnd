<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — มหาวิทยาลัยราชภัฏสงขลา</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style> body { font-family: 'Sarabun', 'Segoe UI', sans-serif; } </style>
</head>
<body class="antialiased bg-gradient-to-br from-blue-50 to-yellow-50 min-h-screen">

<header class="bg-blue-900 text-white shadow-lg">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="inline-flex items-center justify-center w-11 h-11 rounded-full bg-yellow-400 text-blue-900 font-bold shadow">SKRU</div>
            <div>
                <div class="font-bold">ระบบจองห้องปฏิบัติการวิทยาศาสตร์</div>
                <div class="text-xs text-blue-200">คณะวิทยาศาสตร์และเทคโนโลยี · มหาวิทยาลัยราชภัฏสงขลา</div>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('login') }}" class="px-4 py-2 bg-yellow-400 text-blue-900 font-semibold text-sm rounded-md hover:bg-yellow-300 shadow">เข้าสู่ระบบ</a>
            <a href="{{ route('register') }}" class="hidden sm:inline-block px-4 py-2 bg-blue-800 border border-blue-700 text-white text-sm rounded-md hover:bg-blue-700">สมัครสมาชิก</a>
        </div>
    </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-12">
    <section class="text-center max-w-3xl mx-auto">
        <h1 class="text-3xl sm:text-4xl font-bold text-blue-900 leading-snug">
            จองห้องปฏิบัติการวิทยาศาสตร์ออนไลน์<br>ของคณะวิทยาศาสตร์และเทคโนโลยี
        </h1>
        <p class="mt-4 text-gray-600 text-base sm:text-lg">
            สำหรับนักศึกษา อาจารย์ และบุคลากร เพื่อขอใช้ห้องปฏิบัติการในการเรียนการสอน
            การทำโครงงาน และงานวิจัย ระบบจะส่งแจ้งเตือนทางอีเมลไปยังหัวหน้าห้องอัตโนมัติ
        </p>
        <div class="mt-6 flex justify-center gap-3 flex-wrap">
            <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-700 text-white font-semibold rounded-md hover:bg-blue-800 shadow">เริ่มต้นใช้งาน</a>
            <a href="{{ route('register') }}" class="px-6 py-3 bg-white border border-blue-300 text-blue-800 font-semibold rounded-md hover:bg-blue-50">สมัครสมาชิกใหม่</a>
        </div>
    </section>

    <section class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl p-6 shadow-sm border-t-4 border-blue-700">
            <div class="text-3xl">📝</div>
            <h3 class="font-bold mt-2 text-gray-900">1. ส่งคำขอ</h3>
            <p class="mt-1 text-sm text-gray-600">เลือกห้อง วัน-เวลา ระบุวัตถุประสงค์การใช้งาน นักศึกษาต้องระบุอาจารย์ที่ปรึกษาด้วย</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border-t-4 border-yellow-400">
            <div class="text-3xl">📬</div>
            <h3 class="font-bold mt-2 text-gray-900">2. ระบบแจ้งเตือน</h3>
            <p class="mt-1 text-sm text-gray-600">ระบบส่งอีเมลแจ้งหัวหน้าห้องและเจ้าหน้าที่ผู้ดูแลโดยอัตโนมัติเมื่อมีคำขอใหม่</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border-t-4 border-green-500">
            <div class="text-3xl">✅</div>
            <h3 class="font-bold mt-2 text-gray-900">3. รับผลพิจารณา</h3>
            <p class="mt-1 text-sm text-gray-600">ผู้ขอใช้จะได้รับอีเมลแจ้งผลการอนุมัติ/ไม่อนุมัติ และดูสถานะการใช้ห้องได้ตลอดเวลา</p>
        </div>
    </section>

    <section class="mt-12 bg-white rounded-xl p-6 shadow-sm">
        <h3 class="font-bold text-gray-900 mb-3">บัญชีทดลองใช้งาน</h3>
        <p class="text-sm text-gray-600 mb-4">รหัสผ่านของบัญชีทดลองทั้งหมดคือ <code class="px-2 py-0.5 bg-gray-100 rounded">demo123</code></p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
            <div class="border border-gray-200 rounded-md p-3">
                <div class="font-mono font-semibold text-blue-700">student</div>
                <div class="text-xs text-gray-500 mt-1">นักศึกษา · เคมี</div>
            </div>
            <div class="border border-gray-200 rounded-md p-3">
                <div class="font-mono font-semibold text-blue-700">employee</div>
                <div class="text-xs text-gray-500 mt-1">บุคลากร · ชีววิทยา</div>
            </div>
            <div class="border border-gray-200 rounded-md p-3">
                <div class="font-mono font-semibold text-blue-700">chem_head</div>
                <div class="text-xs text-gray-500 mt-1">หัวหน้าห้องเคมี</div>
            </div>
            <div class="border border-gray-200 rounded-md p-3">
                <div class="font-mono font-semibold text-blue-700">admin</div>
                <div class="text-xs text-gray-500 mt-1">ผู้ดูแลระบบ</div>
            </div>
        </div>
    </section>
</main>

<footer class="mt-12 py-6 text-center text-xs text-gray-500">
    © {{ date('Y') }} คณะวิทยาศาสตร์และเทคโนโลยี มหาวิทยาลัยราชภัฏสงขลา · {{ config('app.name') }}
</footer>

</body>
</html>
