<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900">แบบฟอร์มขอจองห้องปฏิบัติการ</h2>
        <p class="text-sm text-gray-500 mt-1">เมื่อส่งคำขอ ระบบจะแจ้งหัวหน้าห้องเพื่อพิจารณาอนุมัติทางอีเมล</p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <form method="POST" action="{{ route('bookings.store') }}" class="md:col-span-2 bg-white shadow-sm rounded-lg p-6 space-y-4">
                @csrf
                @if ($errors->any())
                    <div class="rounded bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                        @foreach ($errors->all() as $error)<div>· {{ $error }}</div>@endforeach
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm bg-gray-50 rounded p-3 border border-gray-100">
                    <div><span class="text-gray-500">ผู้ขอใช้:</span> <strong>{{ $user->name }}</strong></div>
                    <div><span class="text-gray-500">{{ $user->user_type === 'student' ? 'รหัสนักศึกษา' : 'เลขประจำตัวบุคลากร' }}:</span> {{ $user->identifier() ?? '—' }}</div>
                    <div><span class="text-gray-500">คณะ:</span> {{ $user->faculty }}</div>
                    <div><span class="text-gray-500">หลักสูตร/หน่วยงาน:</span> {{ $user->department }}</div>
                </div>

                <div>
                    <x-input-label for="room_id" value="เลือกห้อง" />
                    <select id="room_id" name="room_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">— กรุณาเลือกห้อง —</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }} {{ $room->status === 'maintenance' ? 'disabled' : '' }}>
                                {{ $room->code }} · {{ $room->name }}{{ $room->status === 'maintenance' ? ' (ปิดปรับปรุง)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="date" value="วันที่ใช้งาน" />
                        <x-text-input id="date" type="date" name="date" :value="old('date', now()->addDay()->toDateString())" min="{{ now()->toDateString() }}" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label for="attendees" value="จำนวนผู้เข้าใช้ (คน)" />
                        <x-text-input id="attendees" type="number" min="1" name="attendees" :value="old('attendees', 4)" class="mt-1 block w-full" required />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="start_time" value="เวลาเริ่ม" />
                        <x-text-input id="start_time" type="time" name="start_time" :value="old('start_time', '09:00')" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label for="end_time" value="เวลาสิ้นสุด" />
                        <x-text-input id="end_time" type="time" name="end_time" :value="old('end_time', '11:00')" class="mt-1 block w-full" required />
                    </div>
                </div>

                <div>
                    <x-input-label for="phone" value="เบอร์โทรศัพท์ติดต่อ" />
                    <x-text-input id="phone" type="text" name="phone" :value="old('phone', $user->phone)" class="mt-1 block w-full" required />
                </div>

                @if ($user->user_type === 'student')
                    <div>
                        <x-input-label for="advisor_name" value="อาจารย์ที่ปรึกษา/ผู้ควบคุม (จำเป็น)" />
                        <x-text-input id="advisor_name" name="advisor_name" :value="old('advisor_name', $user->default_advisor)" class="mt-1 block w-full" required />
                    </div>
                @endif

                <div>
                    <x-input-label for="purpose" value="วัตถุประสงค์การใช้ห้อง" />
                    <textarea id="purpose" name="purpose" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="ระบุวัตถุประสงค์/รายวิชา/โครงการ">{{ old('purpose') }}</textarea>
                </div>

                <div>
                    <x-input-label for="requirements" value="อุปกรณ์/ความต้องการพิเศษ (ถ้ามี)" />
                    <textarea id="requirements" name="requirements" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="เช่น ขอใช้ตู้ดูดควัน 2 ตู้, ขอใช้กล้องจุลทรรศน์ 5 ตัว">{{ old('requirements') }}</textarea>
                </div>

                <button type="submit" class="px-5 py-2 bg-blue-700 text-white text-sm font-semibold rounded-md hover:bg-blue-800 shadow">ส่งคำขอจอง</button>
            </form>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-3">รายการห้องปฏิบัติการ</h3>
                <div class="space-y-4">
                    @foreach ($rooms as $room)
                        @php $b = $room->statusBadge(); @endphp
                        <div class="pb-3 border-b border-gray-100 last:border-0">
                            <div class="flex items-center justify-between">
                                <div class="font-medium text-gray-900">{{ $room->name }}</div>
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs {{ $b['class'] }}">{{ $b['label'] }}</span>
                            </div>
                            <div class="text-xs text-gray-500">{{ $room->code }} · {{ $room->building }} ชั้น {{ $room->floor }}</div>
                            <p class="mt-1 text-xs text-gray-600">{{ $room->summary }}</p>
                            <p class="mt-1 text-xs text-gray-500">รองรับ {{ $room->capacity }} คน · เวลา {{ $room->open_hours }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
