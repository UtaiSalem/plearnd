<script setup>
import { computed } from 'vue';
import AttendanceStatus from './AttendanceStatus.vue';
import { Icon } from '@iconify/vue';

const props = defineProps({
    member: Object,
    groupAttendances: Object,
});

</script>

<template>
    <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200">
        <td class="p-1.5 border whitespace-nowrap sticky left-0 z-10 bg-white shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
            <div class="flex items-center min-w-fit group">
                <!-- Enhanced avatar with hover effect -->
                <div class="relative">
                    <img
                        class="w-10 h-10 rounded-full border-2 border-white shadow-md transition-all duration-300 group-hover:scale-105 group-hover:shadow-lg"
                        :src="member.user.avatar"
                        :alt="member.user.name"
                    >
                    <!-- Add status indicator ring -->
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white"></div>
                </div>
                
                <!-- Enhanced member info -->
                <div class="ml-2 flex-1">
                    <p class="text-gray-900 font-bold text-sm tracking-wide group-hover:text-blue-700 transition-colors duration-200">
                        {{ member.member_name ?? member.user.name }}
                    </p>
                    <div class="flex items-center mt-0.5 space-x-2 text-xs">
                        <!-- Enhanced order number badge -->
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-gradient-to-r from-violet-100 to-purple-100 text-violet-700 rounded-full font-medium border border-violet-200">
                            <Icon icon="fluent:number-symbol-square-20-regular" width="12" height="12" />
                            <span>{{ props.member.order_number ?? '#' }}</span>
                        </span>
                        
                        <!-- Enhanced group badge -->
                        <span v-if="props.member.group" class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 rounded-full font-medium border border-blue-200">
                            <Icon icon="heroicons:user-group" width="12" height="12" />
                            <span>{{ props.member.group.name }}</span>
                        </span>
                        <span v-else class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-gradient-to-r from-gray-100 to-slate-100 text-gray-600 rounded-full font-medium border border-gray-200">
                            <Icon icon="heroicons:user-group" width="12" height="12" />
                            <span>ไม่มีกลุ่ม</span>
                        </span>
                    </div>
                </div>
            </div>
        </td>

        <!-- Enhanced attendance status cells -->
        <td v-for="(groupAttendance) in groupAttendances" :key="groupAttendance.id" class="p-2 whitespace-nowrap border text-center">
            <div class="flex justify-center">
                <AttendanceStatus
                 :status="groupAttendance.details.filter(detail => detail.course_member_id === member.id)[0]?.status"
                 :attendance="groupAttendance"
                 :memberId="member.id"
                />
            </div>
        </td>
    </tr>
</template>
