<x-guest-layout>
    <h2 class="text-lg font-bold text-gray-900 mb-1">สมัครสมาชิก</h2>
    <p class="text-sm text-gray-500 mb-4">สำหรับนักศึกษา/บุคลากรของมหาวิทยาลัยราชภัฏสงขลา</p>

    <form method="POST" action="{{ route('register') }}" x-data="{ type: '{{ old('user_type', 'student') }}' }">
        @csrf

        <div>
            <x-input-label value="ประเภทผู้ใช้" />
            <div class="mt-1 flex gap-2">
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="user_type" value="student" class="sr-only peer" x-model="type" required>
                    <span class="block text-center px-3 py-2 text-sm border rounded-md peer-checked:bg-blue-700 peer-checked:text-white peer-checked:border-blue-700 border-gray-300">นักศึกษา</span>
                </label>
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="user_type" value="employee" class="sr-only peer" x-model="type">
                    <span class="block text-center px-3 py-2 text-sm border rounded-md peer-checked:bg-blue-700 peer-checked:text-white peer-checked:border-blue-700 border-gray-300">บุคลากร</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('user_type')" class="mt-2" />
        </div>

        <div class="mt-3">
            <x-input-label for="username" value="ชื่อผู้ใช้ (ภาษาอังกฤษ)" />
            <x-text-input id="username" class="block mt-1 w-full" name="username" :value="old('username')" required autofocus />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <div class="mt-3">
            <x-input-label for="name" value="ชื่อ-นามสกุล (ภาษาไทย)" />
            <x-text-input id="name" class="block mt-1 w-full" name="name" :value="old('name')" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-3" x-show="type === 'student'">
            <x-input-label for="student_id" value="รหัสนักศึกษา" />
            <x-text-input id="student_id" class="block mt-1 w-full" name="student_id" :value="old('student_id')" />
            <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
        </div>

        <div class="mt-3" x-show="type === 'employee'">
            <x-input-label for="employee_id" value="เลขประจำตัวบุคลากร" />
            <x-text-input id="employee_id" class="block mt-1 w-full" name="employee_id" :value="old('employee_id')" />
            <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
        </div>

        <div class="mt-3">
            <x-input-label for="email" value="อีเมล" />
            <x-text-input id="email" type="email" class="block mt-1 w-full" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-3">
            <x-input-label for="faculty" value="คณะ" />
            <x-text-input id="faculty" class="block mt-1 w-full" name="faculty" :value="old('faculty', 'คณะวิทยาศาสตร์และเทคโนโลยี')" required />
            <x-input-error :messages="$errors->get('faculty')" class="mt-2" />
        </div>

        <div class="mt-3">
            <x-input-label for="department" value="หลักสูตร/ภาควิชา/หน่วยงาน" />
            <x-text-input id="department" class="block mt-1 w-full" name="department" :value="old('department')" required />
            <x-input-error :messages="$errors->get('department')" class="mt-2" />
        </div>

        <div class="mt-3">
            <x-input-label for="phone" value="เบอร์โทรศัพท์" />
            <x-text-input id="phone" class="block mt-1 w-full" name="phone" :value="old('phone')" required />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="mt-3" x-show="type === 'student'">
            <x-input-label for="default_advisor" value="อาจารย์ที่ปรึกษา" />
            <x-text-input id="default_advisor" class="block mt-1 w-full" name="default_advisor" :value="old('default_advisor')" />
            <x-input-error :messages="$errors->get('default_advisor')" class="mt-2" />
        </div>

        <div class="mt-3" x-data="{ show: false }">
            <x-input-label for="password" value="รหัสผ่าน" />
            <div class="relative mt-1">
                <input id="password" name="password" required autocomplete="new-password"
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

        <div class="mt-3" x-data="{ show: false }">
            <x-input-label for="password_confirmation" value="ยืนยันรหัสผ่าน" />
            <div class="relative mt-1">
                <input id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                    :type="show ? 'text' : 'password'"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full pr-10">
                <button type="button" @click="show = !show" tabindex="-1"
                    class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700"
                    :aria-label="show ? 'ซ่อนรหัสผ่าน' : 'แสดงรหัสผ่าน'">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="text-sm text-blue-700 hover:text-blue-900" href="{{ route('login') }}">มีบัญชีอยู่แล้ว?</a>
            <button type="submit" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold rounded-md shadow">สมัครสมาชิก</button>
        </div>
    </form>
</x-guest-layout>
