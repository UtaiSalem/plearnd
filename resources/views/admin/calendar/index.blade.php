<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900">ปฏิทินการจองห้องปฏิบัติการ</h2>
    </x-slot>

    <!-- Include FullCalendar via CDN -->
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var filterForm = document.getElementById('filter-form');
            var roomFilters = document.querySelectorAll('input[name="room_id[]"]');
            var statusFilters = document.querySelectorAll('input[name="status[]"]');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'th',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                slotMinTime: '08:00:00',
                slotMaxTime: '20:00:00',
                navLinks: true,
                editable: false,
                dayMaxEvents: true,
                events: function(info, successCallback, failureCallback) {
                    var url = new URL('{{ route('admin.calendar.events') }}');
                    url.searchParams.append('start', info.startStr);
                    url.searchParams.append('end', info.endStr);
                    
                    // Add filters
                    var formData = new FormData(filterForm);
                    for (var pair of formData.entries()) {
                        url.searchParams.append(pair[0], pair[1]);
                    }

                    fetch(url)
                        .then(response => response.json())
                        .then(data => successCallback(data))
                        .catch(error => failureCallback(error));
                },
                eventClick: function(info) {
                    // Navigate to booking details
                    window.location.href = '/admin/bookings/' + info.event.id;
                },
                eventContent: function(arg) {
                    return {
                        html: `
                            <div class="overflow-hidden p-1 text-xs">
                                <div class="font-semibold truncate">${arg.event.title}</div>
                                <div class="truncate opacity-80">${arg.event.extendedProps.requester}</div>
                            </div>
                        `
                    };
                }
            });

            calendar.render();

            // Refetch events when filters change
            filterForm.addEventListener('change', function() {
                calendar.refetchEvents();
            });
            
            // Select all / Deselect all handlers
            document.getElementById('select-all-rooms').addEventListener('click', function(e) {
                e.preventDefault();
                roomFilters.forEach(cb => cb.checked = true);
                calendar.refetchEvents();
            });
            
            document.getElementById('deselect-all-rooms').addEventListener('click', function(e) {
                e.preventDefault();
                roomFilters.forEach(cb => cb.checked = false);
                calendar.refetchEvents();
            });
        });
    </script>
    @endpush

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-6">
                
                <!-- Sidebar Filters -->
                <div class="w-full md:w-64 shrink-0">
                    <div class="bg-white p-4 rounded-lg shadow-sm border-t-4 border-blue-500">
                        <h3 class="font-medium text-gray-900 mb-4 border-b pb-2">ตัวกรองข้อมูล</h3>
                        
                        <form id="filter-form">
                            <div class="mb-6">
                                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">สถานะการจอง</h4>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="status[]" value="approved" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700 flex items-center gap-2">
                                            <span class="w-3 h-3 rounded-full bg-blue-500"></span> อนุมัติแล้ว
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="status[]" value="pending" checked class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-500">
                                        <span class="ml-2 text-sm text-gray-700 flex items-center gap-2">
                                            <span class="w-3 h-3 rounded-full bg-yellow-500"></span> รอพิจารณา
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">ห้องปฏิบัติการ</h4>
                                    <div class="text-xs">
                                        <a href="#" id="select-all-rooms" class="text-blue-600 hover:underline">ทั้งหมด</a> |
                                        <a href="#" id="deselect-all-rooms" class="text-gray-500 hover:underline">ล้าง</a>
                                    </div>
                                </div>
                                <div class="space-y-2 max-h-60 overflow-y-auto pr-2">
                                    @foreach($rooms as $room)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="room_id[]" value="{{ $room->id }}" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700 flex items-center gap-2">
                                                <span class="w-3 h-3 rounded-full" style="background-color: {{ $room->color ?? '#3b82f6' }}"></span> 
                                                <span class="truncate" title="{{ $room->name }}">{{ $room->code }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Calendar View -->
                <div class="flex-1">
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <div id="calendar" class="min-h-[600px]"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
