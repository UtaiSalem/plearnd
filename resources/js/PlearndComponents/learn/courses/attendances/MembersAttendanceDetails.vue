<script setup>
import AttendanceStatus from './AttendanceStatus.vue';
import { Icon } from '@iconify/vue';

const props = defineProps({
    member: Object,
    groupAttendances: Object,
});

</script>

<template>
    <tr>
        <td class="p-2 border whitespace-nowrap">
            <div class="flex items-center min-w-fit">
                <img class="w-12 h-12 rounded-full" :src="member.user.avatar" :alt="member.user.name">
                <div class="ml-2">
                    <p class="text-gray-900  tracking-wide text-sm">{{ member.member_name ?? member.user.name}}</p>
                    <div class="flex items-center mt-2 space-x-2 text-sm">
                        <span class="text-slate-700 flex"><Icon icon="fluent:number-symbol-square-20-regular" width="20" height="20" /> {{ ' ' +props.member.order_number?? '#' }}</span>
                        <span class="text-slate-700 flex"><Icon icon="heroicons:user-group" width="20" height="20" class="mr-1.5" /> {{ props.member.group ? props.member.group.name : '-' }}</span>
                    </div>
                </div>
            </div>
        </td>

        <td class="p-2 whitespace-nowrap border text-center" v-for="(groupAttendance) in groupAttendances" :key="groupAttendance.id">
            <AttendanceStatus 
             :status="groupAttendance.details.filter(detail => detail.course_member_id === member.user.id)[0]?.status"
            />
        </td> 
    </tr>
</template>