<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900">คำขอจองห้องที่ต้องพิจารณา</h2>
        <p class="text-sm text-gray-500 mt-1">รายการคำขอจองห้องที่อยู่ในความรับผิดชอบของท่าน</p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-md bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">{{ session('status') }}</div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-yellow-400"><div class="text-xs uppercase tracking-wider text-gray-500">รอพิจารณา</div><div class="mt-1 text-3xl font-bold text-yellow-700">{{ $pending->count() }}</div></div>
                <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-green-500"><div class="text-xs uppercase tracking-wider text-gray-500">อนุมัติแล้ววันนี้</div><div class="mt-1 text-3xl font-bold text-green-700">{{ $todayApproved->count() }}</div></div>
                <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-blue-500"><div class="text-xs uppercase tracking-wider text-gray-500">ห้องที่ดูแล</div><div class="mt-1 text-3xl font-bold text-blue-700">{{ $roomCount }}</div></div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-3">คำขอรอพิจารณา</h3>
                <div class="space-y-3">
                    @forelse ($pending as $booking)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $booking->room->name }} <span class="text-xs text-gray-400">({{ $booking->room->code }})</span></div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $booking->requester_name }} · {{ $booking->requester_type === 'student' ? 'นศ.' : 'บุคลากร' }}{{ $booking->requester_identifier ? ' '.$booking->requester_identifier : '' }} · {{ $booking->department }}
                                    </div>
                                    <div class="text-xs text-gray-500">📅 {{ $booking->start_at->format('d/m/Y') }} · ⏰ {{ $booking->start_at->format('H:i') }}—{{ $booking->end_at->format('H:i') }} · 👥 {{ $booking->attendees }} คน</div>
                                </div>
                                <a href="{{ route('bookings.show', $booking) }}" class="text-xs text-blue-700 hover:text-blue-900">ดูรายละเอียดทั้งหมด →</a>
                            </div>
                            <p class="mt-2 text-sm text-gray-700">{{ $booking->purpose }}</p>
                            @if ($booking->advisor_name)
                                <p class="text-xs text-gray-500 mt-1">อาจารย์ที่ปรึกษา: {{ $booking->advisor_name }}</p>
                            @endif
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <form method="POST" action="{{ route('bookings.review', $booking) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button class="px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-500">✓ อนุมัติ</button>
                                </form>
                                <form method="POST" action="{{ route('bookings.review', $booking) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <input name="reason" placeholder="ระบุเหตุผล" class="px-2 py-1 text-sm border border-gray-300 rounded-md" required>
                                    <button class="px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-500">✗ ไม่อนุมัติ</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">ไม่มีคำขอรอพิจารณา</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-3">การใช้ห้องวันนี้ (อนุมัติแล้ว)</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50"><tr class="text-left text-xs uppercase tracking-wider text-gray-500">
                        <th class="px-3 py-2">ห้อง</th><th class="px-3 py-2">ผู้ขอใช้</th><th class="px-3 py-2">เวลา</th><th class="px-3 py-2">สถานะ</th><th class="px-3 py-2">อัปเดต</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse ($todayApproved as $booking)
                            @php $b = $booking->statusBadge(); @endphp
                            <tr>
                                <td class="px-3 py-2 font-medium text-gray-900">{{ $booking->room->name }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $booking->requester_name }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $booking->start_at->format('H:i') }}—{{ $booking->end_at->format('H:i') }}</td>
                                <td class="px-3 py-2"><span class="inline-block px-2 py-0.5 rounded-full text-xs {{ $b['class'] }}">{{ $b['label'] }}</span></td>
                                <td class="px-3 py-2">
                                    <form method="POST" action="{{ route('bookings.staffStatus', $booking) }}" class="flex flex-wrap gap-1">
                                        @csrf
                                        @foreach (['ready' => 'พร้อม', 'in_use' => 'ใช้งาน', 'cleanup' => 'ทำสะอาด', 'issue' => 'มีปัญหา'] as $val => $label)
                                            <button name="staff_status" value="{{ $val }}" class="px-2 py-1 text-xs border border-gray-300 rounded-md hover:bg-gray-50">{{ $label }}</button>
                                        @endforeach
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-3 py-3 text-center text-gray-400">ไม่มีการใช้ห้องวันนี้</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
