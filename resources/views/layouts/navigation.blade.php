<nav x-data="{ open: false }" class="bg-blue-900 border-b border-blue-950 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-yellow-400 text-blue-900 font-bold text-sm">SKRU</span>
                    <a href="{{ route(Auth::user()->homeRoute()) }}" class="font-bold text-white">
                        ระบบจองห้องปฏิบัติการ
                    </a>
                </div>

                <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex">
                    @if (Auth::user()->role === 'requester')
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">หน้าหลัก</x-nav-link>
                        <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.index') || request()->routeIs('bookings.show')">การจองของฉัน</x-nav-link>
                        <x-nav-link :href="route('bookings.create')" :active="request()->routeIs('bookings.create')">จองห้องใหม่</x-nav-link>
                    @elseif (Auth::user()->role === 'staff')
                        <x-nav-link :href="route('staff.queue')" :active="request()->routeIs('staff.queue')">คำขออนุมัติ</x-nav-link>
                        <x-nav-link :href="route('staff.rooms')" :active="request()->routeIs('staff.rooms')">ห้องที่ดูแล</x-nav-link>
                    @elseif (Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-link>
                        <x-nav-link :href="route('admin.calendar.index')" :active="request()->routeIs('admin.calendar.*')">ปฏิทิน</x-nav-link>
                        <x-nav-link :href="route('admin.bookings.index')" :active="request()->routeIs('admin.bookings.*')">การจองทั้งหมด</x-nav-link>
                        <x-nav-link :href="route('admin.rooms.index')" :active="request()->routeIs('admin.rooms.*')">จัดการห้อง</x-nav-link>
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">ผู้ใช้งาน</x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <span class="me-3 text-xs px-2 py-1 rounded-full bg-yellow-400 text-blue-900 font-semibold">{{ Auth::user()->roleLabel() }}</span>
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-50 hover:text-white focus:outline-none">
                            <div>{{ Auth::user()->name }}</div>
                            <svg class="ms-1 fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-4 py-2 text-xs text-gray-500 border-b">{{ Auth::user()->department }}</div>
                        <x-dropdown-link :href="route('profile.edit')">ข้อมูลส่วนตัว</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">ออกจากระบบ</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-blue-100 hover:text-white hover:bg-blue-800 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-blue-800">
        <div class="pt-2 pb-3 space-y-1">
            @if (Auth::user()->role === 'requester')
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">หน้าหลัก</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.*')">การจองของฉัน</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('bookings.create')" :active="request()->routeIs('bookings.create')">จองห้องใหม่</x-responsive-nav-link>
            @elseif (Auth::user()->role === 'staff')
                <x-responsive-nav-link :href="route('staff.queue')" :active="request()->routeIs('staff.queue')">คำขออนุมัติ</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('staff.rooms')" :active="request()->routeIs('staff.rooms')">ห้องที่ดูแล</x-responsive-nav-link>
            @elseif (Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Dashboard</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.calendar.index')" :active="request()->routeIs('admin.calendar.*')">ปฏิทิน</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.bookings.index')" :active="request()->routeIs('admin.bookings.*')">การจองทั้งหมด</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.rooms.index')" :active="request()->routeIs('admin.rooms.*')">จัดการห้อง</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">ผู้ใช้งาน</x-responsive-nav-link>
            @endif
        </div>
        <div class="pt-4 pb-3 border-t border-blue-700">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-blue-200">{{ Auth::user()->department }} · {{ Auth::user()->roleLabel() }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">ข้อมูลส่วนตัว</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">ออกจากระบบ</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
