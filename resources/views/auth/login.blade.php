<x-guest-layout>
    <h2 class="text-lg font-bold text-gray-900 mb-1">เข้าสู่ระบบ</h2>
    <p class="text-sm text-gray-500 mb-4">สำหรับนักศึกษาและบุคลากรของมหาวิทยาลัย</p>

    <div class="mb-4 rounded-lg bg-blue-50 border border-blue-100 p-3 text-xs text-blue-900">
        <div class="font-semibold mb-1">บัญชีทดลอง (รหัสผ่าน: <code>demo123</code>)</div>
        <div class="grid grid-cols-2 gap-1 mt-2">
            <span class="px-2 py-1 rounded bg-white border border-blue-200"><strong>student</strong> — นักศึกษา</span>
            <span class="px-2 py-1 rounded bg-white border border-blue-200"><strong>employee</strong> — บุคลากร</span>
            <span class="px-2 py-1 rounded bg-white border border-blue-200"><strong>chem_head</strong> — หัวหน้าห้อง</span>
            <span class="px-2 py-1 rounded bg-white border border-blue-200"><strong>admin</strong> — ผู้ดูแล</span>
        </div>
    </div>

    @unless($hasUsers)
        <div class="mb-4 rounded-lg bg-amber-50 border border-amber-200 p-3 text-xs text-amber-900">
            ยังไม่มีบัญชีผู้ใช้ในฐานข้อมูลนี้ กรุณารัน <code>php artisan db:seed</code> เพื่อสร้างบัญชีตัวอย่างก่อนเข้าสู่ระบบ
        </div>
    @endunless

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div>
            <x-input-label for="login" value="ชื่อผู้ใช้หรืออีเมล" />
            <x-text-input id="login" class="block mt-1 w-full" type="text" name="login" :value="old('login', 'student')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <div class="mt-4" x-data="{ show: false }">
            <x-input-label for="password" value="รหัสผ่าน" />
            <div class="relative mt-1">
                <input id="password" name="password" value="demo123" required autocomplete="current-password"
                    :type="show ? 'text' : 'password'"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full pr-10">
                <button type="button" @click="show = !show" tabindex="-1"
                    class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700"
                    :aria-label="show ? 'ซ่อนรหัสผ่าน' : 'แสดงรหัสผ่าน'">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-700 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">จดจำการเข้าสู่ระบบ</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="text-sm text-blue-700 hover:text-blue-900" href="{{ route('register') }}">สมัครสมาชิก</a>
            <button type="submit" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold rounded-md shadow">เข้าสู่ระบบ</button>
        </div>
    </form>

    <p class="text-center mt-4 text-xs text-gray-400"><a href="{{ url('/') }}" class="hover:text-gray-600">← กลับหน้าแรก</a></p>
</x-guest-layout>
