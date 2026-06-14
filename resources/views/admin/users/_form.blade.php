<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Account Info -->
    <div class="space-y-4">
        <h3 class="text-md font-medium text-gray-900 border-b pb-2">ข้อมูลบัญชีผู้ใช้</h3>
        
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">ชื่อผู้ใช้งาน (Username) *</label>
            <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">อีเมล (Email) *</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        @if(!$user->id)
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">รหัสผ่าน (Password) *</label>
                <input type="password" name="password" id="password" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">ยืนยันรหัสผ่าน *</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
        @else
            <div class="text-sm text-gray-500 italic mt-4 pt-4 border-t">
                * หากต้องการเปลี่ยนรหัสผ่าน กรุณาใช้ฟังก์ชันรีเซ็ตรหัสผ่าน (ในอนาคต)
            </div>
        @endif

        <div>
            <label for="role" class="block text-sm font-medium text-gray-700">สิทธิ์การใช้งาน (Role) *</label>
            <select name="role" id="role" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                <option value="requester" {{ old('role', $user->role) === 'requester' ? 'selected' : '' }}>Requester (ผู้ใช้ทั่วไป)</option>
                <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff (เจ้าหน้าที่ดูแลห้อง)</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin (ผู้ดูแลระบบ)</option>
            </select>
            @if($user->id === auth()->id())
                <input type="hidden" name="role" value="{{ $user->role }}">
                <p class="text-xs text-gray-500 mt-1">ไม่สามารถเปลี่ยนสิทธิ์ของตนเองได้</p>
            @endif
        </div>
    </div>

    <!-- Personal Info -->
    <div class="space-y-4" x-data="{ userType: '{{ old('user_type', $user->user_type ?? 'student') }}' }">
        <h3 class="text-md font-medium text-gray-900 border-b pb-2">ข้อมูลส่วนตัว</h3>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">ชื่อ-นามสกุล *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <div>
            <label for="user_type" class="block text-sm font-medium text-gray-700">ประเภทผู้ใช้ *</label>
            <select name="user_type" id="user_type" x-model="userType" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="student">นักศึกษา</option>
                <option value="employee">บุคลากร/อาจารย์</option>
            </select>
        </div>

        <div x-show="userType === 'student'">
            <label for="student_id" class="block text-sm font-medium text-gray-700">รหัสนักศึกษา <span x-show="userType === 'student'" class="text-red-500">*</span></label>
            <input type="text" name="student_id" id="student_id" value="{{ old('student_id', $user->student_id) }}" :required="userType === 'student'" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <div x-show="userType === 'employee'" style="display: none;">
            <label for="employee_id" class="block text-sm font-medium text-gray-700">รหัสบุคลากร <span x-show="userType === 'employee'" class="text-red-500">*</span></label>
            <input type="text" name="employee_id" id="employee_id" value="{{ old('employee_id', $user->employee_id) }}" :required="userType === 'employee'" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <div>
            <label for="faculty" class="block text-sm font-medium text-gray-700">คณะ</label>
            <input type="text" name="faculty" id="faculty" value="{{ old('faculty', $user->faculty ?? 'คณะวิทยาศาสตร์และเทคโนโลยี') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <div>
            <label for="department" class="block text-sm font-medium text-gray-700">สาขาวิชา/งาน</label>
            <input type="text" name="department" id="department" value="{{ old('department', $user->department) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">เบอร์โทรศัพท์</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        </div>
    </div>
</div>
