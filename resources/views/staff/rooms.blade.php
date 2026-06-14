<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900">ห้องปฏิบัติการที่ท่านดูแล</h2>
        <p class="text-sm text-gray-500 mt-1">สถานะการให้บริการของแต่ละห้อง</p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse ($rooms as $room)
                @php $b = $room->statusBadge(); @endphp
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <div class="flex items-center justify-between">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs {{ $b['class'] }}">{{ $b['label'] }}</span>
                        <span class="text-xs text-gray-400">{{ $room->code }}</span>
                    </div>
                    <h3 class="mt-2 font-semibold text-gray-900">{{ $room->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $room->building }} · ชั้น {{ $room->floor }}</p>
                    <p class="mt-2 text-sm text-gray-700">{{ $room->summary }}</p>
                    <p class="mt-2 text-xs text-gray-500">รองรับ {{ $room->capacity }} คน · เวลา {{ $room->open_hours }}</p>
                    <p class="mt-1 text-xs text-gray-500">อุปกรณ์: {{ $room->equipment }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-400 md:col-span-3">ยังไม่มีห้องที่ท่านได้รับมอบหมายให้ดูแล</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
