<script setup>
import AuthMemberAttendanceDetail  from './AuthMemberAttendanceDetail.vue';

const props = defineProps({
    attendances: {
        type: Object,
        required: true,
    },
    courseMemberOfAuth: {
        type: Object,
        required: true,
    },
    courseMember:{
        type: Object,
        required: true,
    },
});


</script>

<template>
    <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                    <table class="min-w-full">
                        <thead class="border-b">
                            <tr>
                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">#</th>
                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">วันที่</th>
                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">เริ่ม</th>
                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">สิ้นสุด</th>
                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">คำอธิบาย</th>
                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">การเข้าเรียน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <AuthMemberAttendanceDetail 
                                v-for="(attendance, aIndx) in attendances.data" :key="attendance.id"
                                :attendance="attendance"
                                :aIndx
                                :aDate="attendance.date"
                                :startAt="attendance.start_at"
                                :finishAt="attendance.finish_at"
                                :lateTime="attendance.late_time"
                                :aDescription="attendance.description"
                                :authMemberAttendance="attendance.authAtttendanceStatus"
                            />
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
