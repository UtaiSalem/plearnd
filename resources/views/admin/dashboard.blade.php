<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900">แผงควบคุม (Dashboard)</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-400">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">รอพิจารณา</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $pendingCount }}</p>
                        </div>
                        <div class="p-3 bg-yellow-50 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">อนุมัติวันนี้</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $approvedTodayCount }}</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">กำลังใช้งานขณะนี้</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $inUseCount }}</p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats & Trends -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Latest Pending -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">คำขอรอพิจารณาล่าสุด</h3>
                        <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">ดูทั้งหมด &rarr;</a>
                    </div>
                    
                    @if($latestPending->isEmpty())
                        <p class="text-gray-500 text-sm py-4 text-center">ไม่มีคำขอที่รอพิจารณา</p>
                    @else
                        <div class="overflow-hidden">
                            <ul class="divide-y divide-gray-200">
                                @foreach($latestPending as $booking)
                                    <li class="py-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $booking->room->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $booking->requester_name }} · {{ $booking->start_at->format('d/m/Y H:i') }} - {{ $booking->end_at->format('H:i') }}</p>
                                            </div>
                                            <a href="{{ route('admin.bookings.show', $booking) }}" class="text-xs bg-blue-50 text-blue-700 px-3 py-1 rounded-full hover:bg-blue-100 transition">รายละเอียด</a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="space-y-6">
                    <!-- Users by Role -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">สัดส่วนผู้ใช้งาน</h3>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500 uppercase">Requester</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $usersByRole['requester'] ?? 0 }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500 uppercase">Staff</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $usersByRole['staff'] ?? 0 }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500 uppercase">Admin</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $usersByRole['admin'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Top Rooms -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">ห้องที่มีการจองสูงสุด (เดือนนี้)</h3>
                        <div class="space-y-3">
                            @foreach($topRooms as $room)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full" style="background-color: {{ $room->color ?? '#3b82f6' }}"></div>
                                        <span class="text-sm text-gray-700">{{ $room->name }}</span>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $room->bookings_count }} ครั้ง</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
