<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">เปลี่ยนรหัสผ่าน</h2>
        <p class="mt-1 text-sm text-gray-600">
            เพื่อความปลอดภัย ควรใช้รหัสผ่านที่ยาวและไม่สามารถคาดเดาได้
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        @foreach ([
            ['id' => 'update_password_current_password', 'name' => 'current_password', 'label' => 'รหัสผ่านปัจจุบัน', 'autocomplete' => 'current-password', 'errKey' => 'current_password'],
            ['id' => 'update_password_password', 'name' => 'password', 'label' => 'รหัสผ่านใหม่', 'autocomplete' => 'new-password', 'errKey' => 'password'],
            ['id' => 'update_password_password_confirmation', 'name' => 'password_confirmation', 'label' => 'ยืนยันรหัสผ่านใหม่', 'autocomplete' => 'new-password', 'errKey' => 'password_confirmation'],
        ] as $f)
            <div x-data="{ show: false }">
                <x-input-label :for="$f['id']" :value="$f['label']" />
                <div class="relative mt-1">
                    <input :id="$f['id']" id="{{ $f['id'] }}" name="{{ $f['name'] }}" autocomplete="{{ $f['autocomplete'] }}"
                        :type="show ? 'text' : 'password'"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full pr-10">
                    <button type="button" @click="show = !show" tabindex="-1"
                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700"
                        :aria-label="show ? 'ซ่อนรหัสผ่าน' : 'แสดงรหัสผ่าน'">
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->updatePassword->get($f['errKey'])" class="mt-2" />
            </div>
        @endforeach

        <div class="flex items-center gap-4">
            <x-primary-button>บันทึก</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">บันทึกแล้ว</p>
            @endif
        </div>
    </form>
</section>
