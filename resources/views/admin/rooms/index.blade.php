<x-app-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-900">จัดการห้องปฏิบัติการ</h2>
                <p class="text-sm text-gray-500 mt-1">เพิ่ม/แก้ไขข้อมูลห้อง และมอบหมายผู้ดูแล</p>
            </div>
            <a href="{{ route('admin.rooms.index') }}" class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50">+ เพิ่มห้องใหม่</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            @if (session('status'))
                <div class="md:col-span-3 rounded-md bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ $edit?->id ? route('admin.rooms.update', $edit) : route('admin.rooms.store') }}" class="md:col-span-2 bg-white shadow-sm rounded-lg p-6 space-y-3">
                @csrf
                @if ($edit?->id) @method('PUT') @endif

                <h3 class="font-semibold text-gray-800">{{ $edit?->id ? 'แก้ไขข้อมูลห้อง' : 'เพิ่มห้องใหม่' }}</h3>
                @if ($errors->any())
                    <div class="rounded bg-red-50 border border-red-200 text-red-800 px-3 py-2 text-sm">@foreach ($errors->all() as $e)<div>· {{ $e }}</div>@endforeach</div>
                @endif

                <div class="grid grid-cols-2 gap-3">
                    <div><x-input-label value="รหัสห้อง"/><x-text-input name="code" :value="old('code', $edit->code ?? '')" class="mt-1 block w-full" required placeholder="เช่น SCI-A301"/></div>
                    <div><x-input-label value="หมวดหมู่"/><x-text-input name="category" :value="old('category', $edit->category ?? '')" class="mt-1 block w-full" required/></div>
                    <div class="col-span-2"><x-input-label value="ชื่อห้อง"/><x-text-input name="name" :value="old('name', $edit->name ?? '')" class="mt-1 block w-full" required/></div>
                    <div><x-input-label value="อาคาร"/><x-text-input name="building" :value="old('building', $edit->building ?? '')" class="mt-1 block w-full" required/></div>
                    <div><x-input-label value="ชั้น"/><x-text-input name="floor" :value="old('floor', $edit->floor ?? '')" class="mt-1 block w-full" required/></div>
                    <div><x-input-label value="ความจุ (คน)"/><x-text-input type="number" name="capacity" :value="old('capacity', $edit->capacity ?? 24)" class="mt-1 block w-full" required/></div>
                    <div>
                        <x-input-label value="สถานะ"/>
                        <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @foreach (['available' => 'เปิดให้บริการ', 'limited' => 'จำกัดการใช้งาน', 'maintenance' => 'ปิดปรับปรุง'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $edit->status ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="หัวหน้าห้อง (ผู้อนุมัติ)"/>
                        <select name="manager_user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">— ไม่ระบุ —</option>
                            @foreach ($managers as $m)
                                <option value="{{ $m->id }}" {{ (string) old('manager_user_id', $edit->manager_user_id ?? '') === (string) $m->id ? 'selected' : '' }}>{{ $m->name }} ({{ $m->department }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div><x-input-label value="ชื่อผู้ดูแล (ข้อความ)"/><x-text-input name="manager_name" :value="old('manager_name', $edit->manager_name ?? '')" class="mt-1 block w-full"/></div>
                    <div><x-input-label value="เบอร์ติดต่อ"/><x-text-input name="contact" :value="old('contact', $edit->contact ?? '')" class="mt-1 block w-full"/></div>
                </div>
                <div><x-input-label value="เวลาให้บริการ"/><x-text-input name="open_hours" :value="old('open_hours', $edit->open_hours ?? '08:30-16:30')" class="mt-1 block w-full" required/></div>
                <div><x-input-label value="รายละเอียดห้อง"/><textarea name="summary" rows="2" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('summary', $edit->summary ?? '') }}</textarea></div>
                <div><x-input-label value="เครื่องมือ/อุปกรณ์ (คั่นด้วย , )"/><textarea name="equipment" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('equipment', $edit->equipment ?? '') }}</textarea></div>

                <button type="submit" class="px-5 py-2 bg-blue-700 text-white text-sm font-semibold rounded-md hover:bg-blue-800 shadow">{{ $edit?->id ? 'อัปเดตข้อมูลห้อง' : 'บันทึกห้องใหม่' }}</button>
            </form>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-3">รายการห้องทั้งหมด</h3>
                <div class="space-y-4">
                    @foreach ($rooms as $room)
                        @php $b = $room->statusBadge(); @endphp
                        <div class="pb-3 border-b border-gray-100 last:border-0">
                            <div class="flex items-center justify-between">
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs {{ $b['class'] }}">{{ $b['label'] }}</span>
                                <span class="text-xs text-gray-400">{{ $room->code }}</span>
                            </div>
                            <div class="mt-1 font-medium text-gray-900">{{ $room->name }}</div>
                            <div class="text-xs text-gray-500">{{ $room->building }} ชั้น {{ $room->floor }}</div>
                            <div class="text-xs text-gray-500">ผู้ดูแล: {{ $room->manager?->name ?? $room->manager_name ?? '—' }}</div>
                            <a href="{{ route('admin.rooms.edit', $room) }}" class="inline-block mt-1 text-xs text-blue-700 hover:text-blue-900">แก้ไข →</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
