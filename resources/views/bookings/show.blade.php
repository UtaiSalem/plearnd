<x-app-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-900">รายละเอียดการจอง #{{ $booking->id }}</h2>
                <p class="text-sm text-gray-500 mt-1">สรุปข้อมูลคำขอและสถานะการพิจารณา</p>
            </div>
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50">← กลับ</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            @php $badge = $booking->statusBadge(); @endphp
            <div class="md:col-span-2 bg-white shadow-sm rounded-lg p-6 space-y-3">
                <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                <h2 class="text-xl font-semibold text-gray-900">{{ $booking->room->name }}</h2>
                <p class="text-sm text-gray-500">{{ $booking->room->code }} · {{ $booking->room->building }} ชั้น {{ $booking->room->floor }}</p>
                <hr>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 text-sm">
                    <div><span class="text-gray-500">วันที่:</span> {{ $booking->start_at->format('d/m/Y') }}</div>
                    <div><span class="text-gray-500">เวลา:</span> {{ $booking->start_at->format('H:i') }} — {{ $booking->end_at->format('H:i') }} น.</div>
                    <div><span class="text-gray-500">ผู้ขอใช้:</span> {{ $booking->requester_name }}</div>
                    <div><span class="text-gray-500">ประเภท:</span> {{ $booking->requester_type === 'student' ? 'นักศึกษา' : 'บุคลากร' }}</div>
                    <div><span class="text-gray-500">รหัส:</span> {{ $booking->requester_identifier ?? '—' }}</div>
                    <div><span class="text-gray-500">เบอร์โทร:</span> {{ $booking->phone ?? '—' }}</div>
                    <div><span class="text-gray-500">คณะ:</span> {{ $booking->faculty }}</div>
                    <div><span class="text-gray-500">หลักสูตร:</span> {{ $booking->department }}</div>
                    @if ($booking->advisor_name)
                        <div class="sm:col-span-2"><span class="text-gray-500">อาจารย์ที่ปรึกษา/ผู้ควบคุม:</span> {{ $booking->advisor_name }}</div>
                    @endif
                    <div><span class="text-gray-500">จำนวนผู้เข้าใช้:</span> {{ $booking->attendees }} คน</div>
                </div>

                <hr>
                <div>
                    <div class="text-sm font-medium text-gray-700">วัตถุประสงค์</div>
                    <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $booking->purpose }}</p>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-700">อุปกรณ์/ความต้องการพิเศษ</div>
                    <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $booking->requirements ?: '—' }}</p>
                </div>

                @if ($booking->rejection_reason)
                    <div class="rounded bg-red-50 border border-red-200 text-red-800 px-3 py-2 text-sm">
                        <strong>เหตุผลที่ไม่อนุมัติ:</strong> {{ $booking->rejection_reason }}
                    </div>
                @endif

                @if ($booking->reviewer)
                    <p class="text-xs text-gray-400">พิจารณาโดย {{ $booking->reviewer->name }} เมื่อ {{ $booking->reviewed_at?->format('d/m/Y H:i') }} น.</p>
                @endif

                @if (auth()->user()->canReview($booking) && $booking->status === 'pending')
                    <div class="border-t pt-4 mt-4 space-y-3">
                        <h3 class="font-semibold text-gray-800">การพิจารณา</h3>
                        <div class="flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('bookings.review', $booking) }}">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-500">✓ อนุมัติ</button>
                            </form>
                            <form method="POST" action="{{ route('bookings.review', $booking) }}" class="flex flex-wrap items-center gap-2">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <input name="reason" placeholder="ระบุเหตุผล" class="px-2 py-1 text-sm border border-gray-300 rounded-md" required>
                                <button class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-md hover:bg-red-500">✗ ไม่อนุมัติ</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6 space-y-2">
                <h3 class="font-semibold text-gray-800">ข้อมูลห้อง</h3>
                <div class="font-medium text-gray-900">{{ $booking->room->name }}</div>
                <div class="text-xs text-gray-500">{{ $booking->room->code }} · {{ $booking->room->category }}</div>
                <p class="text-sm text-gray-700">{{ $booking->room->summary }}</p>
                <div>
                    <div class="text-sm font-medium mt-2">เครื่องมือ/อุปกรณ์</div>
                    <p class="text-sm text-gray-700">{{ $booking->room->equipment }}</p>
                </div>
                @if ($booking->room->manager)
                    <div class="pt-2 border-t mt-2">
                        <div class="text-xs text-gray-500">ผู้ดูแลห้อง</div>
                        <div class="text-sm font-medium">{{ $booking->room->manager->name }}</div>
                        <div class="text-xs text-gray-500">{{ $booking->room->manager->department }}</div>
                        <div class="text-xs text-gray-500">{{ $booking->room->contact }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
