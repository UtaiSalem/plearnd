<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900">แก้ไขข้อมูลการจองห้องปฏิบัติการ (Admin)</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if ($errors->any())
                        <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-6 p-4 bg-blue-50 rounded-md border border-blue-100">
                        <p class="text-sm text-blue-800 font-medium mb-1">ข้อมูลผู้ขอใช้:</p>
                        <p class="text-sm text-blue-900">{{ $booking->requester_name }} ({{ $booking->department }})</p>
                    </div>

                    <form method="POST" action="{{ route('admin.bookings.update', $booking) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-4">
                            <div>
                                <label for="room_id" class="block text-sm font-medium text-gray-700">ห้องปฏิบัติการ *</label>
                                <select name="room_id" id="room_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">-- เลือกห้องปฏิบัติการ --</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" {{ old('room_id', $booking->room_id) == $room->id ? 'selected' : '' }}>
                                            {{ $room->name }} ({{ $room->code }}) - ความจุ {{ $room->capacity }} คน
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="start_at" class="block text-sm font-medium text-gray-700">เริ่มเวลา *</label>
                                    <input type="datetime-local" name="start_at" id="start_at" value="{{ old('start_at', $booking->start_at->format('Y-m-d\TH:i')) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="end_at" class="block text-sm font-medium text-gray-700">ถึงเวลา *</label>
                                    <input type="datetime-local" name="end_at" id="end_at" value="{{ old('end_at', $booking->end_at->format('Y-m-d\TH:i')) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="attendees" class="block text-sm font-medium text-gray-700">จำนวนผู้เข้าร่วม (คน) *</label>
                                <input type="number" name="attendees" id="attendees" min="1" value="{{ old('attendees', $booking->attendees) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="purpose" class="block text-sm font-medium text-gray-700">วัตถุประสงค์ *</label>
                                <textarea name="purpose" id="purpose" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('purpose', $booking->purpose) }}</textarea>
                            </div>

                            <div>
                                <label for="requirements" class="block text-sm font-medium text-gray-700">ความต้องการเพิ่มเติม (อุปกรณ์/เครื่องมือ)</label>
                                <textarea name="requirements" id="requirements" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('requirements', $booking->requirements) }}</textarea>
                            </div>

                            <div class="pt-4 border-t border-gray-200">
                                <label for="admin_note" class="block text-sm font-medium text-gray-700">หมายเหตุจากผู้ดูแลระบบ (Admin Note)</label>
                                <p class="text-xs text-gray-500 mb-1">ข้อมูลนี้จะถูกส่งไปพร้อมกับอีเมลแจ้งเตือนการแก้ไข</p>
                                <textarea name="admin_note" id="admin_note" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="เช่น ต้องย้ายห้องเนื่องจากห้องเดิมมีปัญหา...">{{ old('admin_note', $booking->admin_note) }}</textarea>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex items-center justify-end gap-3">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">ยกเลิก</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">บันทึกข้อมูลและส่งแจ้งเตือน</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
