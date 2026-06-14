<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-900">สวัสดี, {{ auth()->user()->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ auth()->user()->roleLabel() }} · {{ auth()->user()->department }}</p>
            </div>
            <a href="{{ route('bookings.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm font-semibold rounded-md hover:bg-blue-800 shadow">
                + จองห้องใหม่
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-md bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">{{ session('status') }}</div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-yellow-400">
                    <div class="text-xs uppercase tracking-wider text-gray-500">รอพิจารณา</div>
                    <div class="mt-1 text-3xl font-bold text-yellow-700">{{ $summary['pending'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-green-500">
                    <div class="text-xs uppercase tracking-wider text-gray-500">อนุมัติแล้ว</div>
                    <div class="mt-1 text-3xl font-bold text-green-700">{{ $summary['approved'] }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-red-500">
                    <div class="text-xs uppercase tracking-wider text-gray-500">ไม่อนุมัติ</div>
                    <div class="mt-1 text-3xl font-bold text-red-700">{{ $summary['rejected'] }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2 bg-white shadow-sm rounded-lg p-5">
                    <h3 class="font-semibold text-gray-800 mb-3">การจองครั้งถัดไป</h3>
                    @if ($next)
                        @php $badge = $next->statusBadge(); @endphp
                        <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                        <p class="mt-2 font-medium text-gray-900">{{ $next->room->name }}</p>
                        <p class="text-sm text-gray-500">{{ $next->start_at->format('d/m/Y H:i') }} — {{ $next->end_at->format('H:i') }} น.</p>
                        <p class="mt-2 text-sm text-gray-700">{{ $next->purpose }}</p>
                        <a href="{{ route('bookings.show', $next) }}" class="inline-block mt-3 text-sm text-blue-700 hover:text-blue-900">ดูรายละเอียด →</a>
                    @else
                        <p class="text-sm text-gray-500">ยังไม่มีการจองที่กำลังจะถึง</p>
                        <a href="{{ route('bookings.create') }}" class="inline-block mt-2 text-sm text-blue-700 hover:text-blue-900">+ ส่งคำขอจองห้องใหม่</a>
                    @endif
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <h3 class="font-semibold text-gray-800 mb-3">ห้องที่ให้บริการ</h3>
                    <ul class="space-y-3">
                        @foreach ($rooms as $room)
                            @php $b = $room->statusBadge(); @endphp
                            <li>
                                <div class="text-sm font-medium text-gray-900">{{ $room->name }}</div>
                                <div class="text-xs text-gray-500">{{ $room->building }} · ชั้น {{ $room->floor }}</div>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs {{ $b['class'] }}">{{ $b['label'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
