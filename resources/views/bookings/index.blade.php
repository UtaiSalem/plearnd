<x-app-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-900">การจองของฉัน</h2>
                <p class="text-sm text-gray-500 mt-1">รายการคำขอจองห้องปฏิบัติการทั้งหมดของท่าน</p>
            </div>
            <a href="{{ route('bookings.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm font-semibold rounded-md hover:bg-blue-800 shadow">+ จองห้องใหม่</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs uppercase tracking-wider text-gray-500">
                            <th class="px-4 py-3">สถานะ</th>
                            <th class="px-4 py-3">ห้อง</th>
                            <th class="px-4 py-3">วันที่</th>
                            <th class="px-4 py-3">เวลา</th>
                            <th class="px-4 py-3">วัตถุประสงค์</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white text-sm">
                        @forelse ($bookings as $booking)
                            @php $b = $booking->statusBadge(); @endphp
                            <tr>
                                <td class="px-4 py-3"><span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $b['class'] }}">{{ $b['label'] }}</span></td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $booking->room->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $booking->start_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $booking->start_at->format('H:i') }} — {{ $booking->end_at->format('H:i') }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $booking->status === 'rejected' && $booking->rejection_reason ? $booking->rejection_reason : $booking->purpose }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('bookings.show', $booking) }}" class="text-blue-700 hover:text-blue-900 text-sm">ดูรายละเอียด</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">ยังไม่มีการจอง</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
