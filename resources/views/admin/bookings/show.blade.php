<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-900">รายละเอียดการจอง (Admin)</h2>
            <a href="{{ route('admin.bookings.index') }}" class="text-sm text-blue-600 hover:underline">&larr; กลับไปหน้ารวม</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('status'))
                <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start border-b pb-4 mb-4">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $booking->room->name }} ({{ $booking->room->code }})</h3>
                            <p class="text-sm text-gray-500">{{ $booking->room->building }} · ชั้น {{ $booking->room->floor }}</p>
                        </div>
                        @php $badge = $booking->statusBadge(); @endphp
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $badge['class'] }}">
                            {{ $badge['label'] }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">ข้อมูลผู้ขอใช้</h4>
                                <div class="bg-gray-50 p-4 rounded-md text-sm space-y-2">
                                    <p><span class="font-medium">ชื่อ:</span> {{ $booking->requester_name }} ({{ $booking->requester_type == 'student' ? 'นักศึกษา' : 'บุคลากร' }})</p>
                                    @if($booking->requester_identifier)
                                        <p><span class="font-medium">รหัส:</span> {{ $booking->requester_identifier }}</p>
                                    @endif
                                    <p><span class="font-medium">สังกัด:</span> {{ $booking->department }} {{ $booking->faculty }}</p>
                                    @if($booking->phone)
                                        <p><span class="font-medium">เบอร์โทร:</span> {{ $booking->phone }}</p>
                                    @endif
                                    @if($booking->advisor_name)
                                        <p><span class="font-medium">อาจารย์ที่ปรึกษา:</span> {{ $booking->advisor_name }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">รายละเอียดการใช้ห้อง</h4>
                                <div class="text-sm space-y-3">
                                    <p><span class="font-medium">วันที่:</span> {{ $booking->start_at->format('d/m/Y') }}</p>
                                    <p><span class="font-medium">เวลา:</span> <span class="font-bold text-blue-700">{{ $booking->start_at->format('H:i') }} - {{ $booking->end_at->format('H:i') }} น.</span></p>
                                    <p><span class="font-medium">จำนวนผู้เข้าร่วม:</span> {{ $booking->attendees }} คน</p>
                                    <div>
                                        <p class="font-medium mb-1">วัตถุประสงค์:</p>
                                        <p class="text-gray-700 bg-gray-50 p-3 rounded">{{ $booking->purpose }}</p>
                                    </div>
                                    @if($booking->requirements)
                                    <div>
                                        <p class="font-medium mb-1">ความต้องการเพิ่มเติม:</p>
                                        <p class="text-gray-700 bg-gray-50 p-3 rounded">{{ $booking->requirements }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Right Column (Admin Data & Timeline) -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">ไทม์ไลน์การทำรายการ</h4>
                            
                            <ol class="relative border-l border-gray-200 ml-3 space-y-5 text-sm">
                                <li class="ml-4">
                                    <div class="absolute w-3 h-3 bg-gray-300 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                    <p class="font-medium text-gray-900">ส่งคำขอจอง</p>
                                    <p class="text-gray-500">{{ $booking->created_at->format('d/m/Y H:i') }}</p>
                                </li>
                                
                                @if($booking->reviewed_at)
                                <li class="ml-4">
                                    <div class="absolute w-3 h-3 {{ $booking->status === 'approved' ? 'bg-green-500' : 'bg-red-500' }} rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                    <p class="font-medium text-gray-900">{{ $booking->status === 'approved' ? 'อนุมัติการจอง' : 'ไม่อนุมัติการจอง' }}</p>
                                    <p class="text-gray-500">โดย {{ $booking->reviewer->name ?? 'System' }}</p>
                                    <p class="text-gray-500 text-xs">{{ $booking->reviewed_at->format('d/m/Y H:i') }}</p>
                                    @if($booking->status === 'rejected' && $booking->rejection_reason)
                                        <p class="mt-2 p-2 bg-red-50 text-red-700 rounded text-xs">เหตุผล: {{ $booking->rejection_reason }}</p>
                                    @endif
                                </li>
                                @endif

                                @if($booking->updated_by && $booking->updated_at->gt($booking->created_at))
                                <li class="ml-4">
                                    <div class="absolute w-3 h-3 bg-blue-500 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                    <p class="font-medium text-gray-900">แก้ไขข้อมูลโดย Admin</p>
                                    <p class="text-gray-500">โดย {{ $booking->updater->name ?? 'Admin' }}</p>
                                    <p class="text-gray-500 text-xs">{{ $booking->updated_at->format('d/m/Y H:i') }}</p>
                                </li>
                                @endif
                                
                                @if($booking->cancelled_at)
                                <li class="ml-4">
                                    <div class="absolute w-3 h-3 bg-gray-800 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                    <p class="font-medium text-gray-900">ยกเลิกการจอง</p>
                                    <p class="text-gray-500">โดย {{ $booking->canceller->name ?? 'System' }}</p>
                                    <p class="text-gray-500 text-xs">{{ $booking->cancelled_at->format('d/m/Y H:i') }}</p>
                                    @if($booking->admin_note)
                                        <p class="mt-2 p-2 bg-gray-100 text-gray-700 rounded text-xs">หมายเหตุ: {{ $booking->admin_note }}</p>
                                    @endif
                                </li>
                                @endif
                            </ol>

                            @if($booking->status === 'approved' && !$booking->cancelled_at)
                                <div class="mt-6 pt-4 border-t">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">การจัดการสถานะห้อง (Staff View)</h4>
                                    <div class="p-4 bg-gray-50 rounded-md">
                                        <p class="text-sm mb-2"><span class="font-medium">สถานะหน้างาน:</span> {{ $badge['label'] }}</p>
                                        <p class="text-xs text-gray-500 italic">การเปลี่ยนสถานะหน้างาน ให้ดำเนินการผ่านเมนูของ Staff</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Admin Actions -->
                    <div class="mt-8 pt-6 border-t border-gray-200 flex flex-wrap gap-3">
                        @if(!$booking->cancelled_at)
                            <a href="{{ route('admin.bookings.edit', $booking) }}" class="px-4 py-2 bg-blue-50 text-blue-700 rounded-md text-sm font-medium hover:bg-blue-100">
                                แก้ไขข้อมูลการจอง
                            </a>
                            
                            @if($booking->status === 'pending')
                                <form method="POST" action="{{ route('admin.bookings.approve', $booking) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700 shadow-sm" onclick="return confirm('แน่ใจหรือไม่ที่จะอนุมัติการจองนี้?');">
                                        อนุมัติ
                                    </button>
                                </form>
                                <button x-data="" @click.prevent="$dispatch('open-modal', 'reject-booking')" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 shadow-sm">
                                    ไม่อนุมัติ
                                </button>
                            @endif

                            <button x-data="" @click.prevent="$dispatch('open-modal', 'cancel-booking')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50">
                                ยกเลิกการจอง
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <x-modal name="reject-booking" focusable>
        <form method="post" action="{{ route('admin.bookings.reject', $booking) }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">ปฏิเสธคำขอจองห้อง</h2>
            <p class="mt-1 text-sm text-gray-600">กรุณาระบุเหตุผลในการไม่อนุมัติ (ข้อมูลนี้จะถูกส่งให้ผู้ขอใช้)</p>
            <div class="mt-4">
                <textarea name="reason" rows="3" required class="block w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm sm:text-sm" placeholder="เช่น ห้องไม่ว่างในเวลาดังกล่าว..."></textarea>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 mr-3">ยกเลิก</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700">ยืนยันการปฏิเสธ</button>
            </div>
        </form>
    </x-modal>

    <!-- Cancel Modal -->
    <x-modal name="cancel-booking" focusable>
        <form method="post" action="{{ route('admin.bookings.cancel', $booking) }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">ยกเลิกการจอง (โดย Admin)</h2>
            <p class="mt-1 text-sm text-gray-600">กรุณาระบุเหตุผลในการยกเลิก (ข้อมูลนี้จะถูกส่งแจ้งเตือนผู้ใช้งาน)</p>
            <div class="mt-4">
                <textarea name="reason" rows="3" required class="block w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm sm:text-sm" placeholder="เช่น ห้องปิดปรับปรุงฉุกเฉิน..."></textarea>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 mr-3">กลับ</button>
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-900">ยืนยันการยกเลิก</button>
            </div>
        </form>
    </x-modal>

</x-app-layout>
