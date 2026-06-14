<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900">จัดการข้อมูลการจองห้องปฏิบัติการ</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
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

            <!-- Filters -->
            <div class="bg-white rounded-t-lg shadow-sm border-b border-gray-200 p-4">
                <form method="GET" action="{{ route('admin.bookings.index') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label for="q" class="block text-xs font-medium text-gray-700 mb-1">ค้นหา</label>
                        <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="วัตถุประสงค์, ผู้ขอ..." class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label for="room_id" class="block text-xs font-medium text-gray-700 mb-1">ห้องปฏิบัติการ</label>
                        <select name="room_id" id="room_id" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm sm:text-sm">
                            <option value="">ทั้งหมด</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-xs font-medium text-gray-700 mb-1">สถานะ</label>
                        <select name="status" id="status" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm sm:text-sm">
                            <option value="">ทั้งหมด</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอพิจารณา</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>อนุมัติแล้ว</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>ไม่อนุมัติ</option>
                        </select>
                    </div>
                    <div>
                        <label for="staff_status" class="block text-xs font-medium text-gray-700 mb-1">สถานะห้อง</label>
                        <select name="staff_status" id="staff_status" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm sm:text-sm">
                            <option value="">ทั้งหมด</option>
                            <option value="scheduled" {{ request('staff_status') == 'scheduled' ? 'selected' : '' }}>รอดำเนินการ</option>
                            <option value="ready" {{ request('staff_status') == 'ready' ? 'selected' : '' }}>พร้อมใช้งาน</option>
                            <option value="in_use" {{ request('staff_status') == 'in_use' ? 'selected' : '' }}>กำลังใช้งาน</option>
                            <option value="cleanup" {{ request('staff_status') == 'cleanup' ? 'selected' : '' }}>กำลังทำความสะอาด</option>
                            <option value="issue" {{ request('staff_status') == 'issue' ? 'selected' : '' }}>มีปัญหา</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-900">กรองข้อมูล</button>
                        <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50">ล้าง</a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white shadow-sm overflow-x-auto rounded-b-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันเวลาที่จอง</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ห้องปฏิบัติการ</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ผู้ขอใช้</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วัตถุประสงค์</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($bookings as $booking)
                            <tr class="hover:bg-gray-50 {{ $booking->cancelled_at ? 'opacity-60 bg-gray-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->start_at->format('d/m/Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->start_at->format('H:i') }} - {{ $booking->end_at->format('H:i') }} น.</div>
                                    @if($booking->start_at->isPast() && !$booking->cancelled_at)
                                        <div class="text-xs text-red-500 mt-1">ผ่านมาแล้ว</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $booking->room->name }}</div>
                                    <div class="text-sm text-gray-500">({{ $booking->room->code }})</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $booking->requester_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->department }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 line-clamp-2 max-w-xs" title="{{ $booking->purpose }}">{{ $booking->purpose }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php $badge = $booking->statusBadge(); @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                                    
                                    @if($booking->status === 'pending' && !$booking->cancelled_at)
                                        <div class="text-xs text-gray-400 mt-1">รอ {{ $booking->created_at->diffForHumans() }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-2 py-1 rounded inline-block">รายละเอียด</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">ไม่พบข้อมูลการจอง</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $bookings->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
