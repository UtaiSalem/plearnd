<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900">แก้ไขข้อมูลผู้ใช้งาน: {{ $user->name }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
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

                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        
                        @include('admin.users._form', ['user' => $user])
                        
                        <div class="mt-6 pt-5 border-t border-gray-200">
                            <h3 class="text-md font-medium text-gray-900 mb-4">สถานะบัญชี</h3>
                            <div class="flex items-center gap-6">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="status" value="active" class="text-blue-600 focus:ring-blue-500" {{ old('status', $user->status ?? 'active') === 'active' ? 'checked' : '' }} {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <span class="ml-2 text-gray-700">Active (ใช้งานได้ปกติ)</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="status" value="inactive" class="text-yellow-600 focus:ring-yellow-500" {{ old('status', $user->status) === 'inactive' ? 'checked' : '' }} {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <span class="ml-2 text-gray-700">Inactive (ไม่ได้ใช้งาน)</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="status" value="suspended" class="text-red-600 focus:ring-red-500" {{ old('status', $user->status) === 'suspended' ? 'checked' : '' }} {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <span class="ml-2 text-gray-700">Suspended (ระงับการใช้งาน)</span>
                                </label>
                            </div>
                            @if($user->id === auth()->id())
                                <p class="text-xs text-gray-500 mt-2">* ไม่สามารถเปลี่ยนสถานะบัญชีของตนเองได้</p>
                            @endif
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <div>
                                @if($user->id !== auth()->id())
                                <button type="button" onclick="if(confirm('คุณแน่ใจหรือไม่ที่จะลบผู้ใช้งานนี้?')) { document.getElementById('delete-form').submit(); }" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                    ลบบัญชีผู้ใช้งาน
                                </button>
                                @endif
                            </div>
                            <div class="flex gap-3">
                                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">ยกเลิก</a>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">บันทึกข้อมูล</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            
            @if($user->role === 'staff' && $user->id)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">ห้องปฏิบัติการที่ดูแล ({{ $user->managedRooms()->count() }} ห้อง)</h3>
                    @if($user->managedRooms()->count() > 0)
                        <ul class="divide-y divide-gray-200 border border-gray-200 rounded-md">
                            @foreach($user->managedRooms as $room)
                                <li class="p-3 flex justify-between items-center bg-gray-50">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $room->name }} ({{ $room->code }})</p>
                                        <p class="text-xs text-gray-500">{{ $room->building }}</p>
                                    </div>
                                    <a href="{{ route('admin.rooms.edit', $room) }}" class="text-xs text-blue-600 hover:underline">จัดการห้อง</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500 bg-gray-50 p-4 rounded-md text-center">ยังไม่มีห้องปฏิบัติการที่ดูแล</p>
                    @endif
                </div>
            </div>
            @endif
            
        </div>
    </div>

    @if($user->id !== auth()->id())
    <form id="delete-form" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endif
</x-app-layout>
