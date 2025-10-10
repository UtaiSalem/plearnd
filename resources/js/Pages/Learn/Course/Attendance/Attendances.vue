<script setup>

import CourseLayout from '@/Layouts/CourseLayout.vue';
import CourseAttendance from '@/PlearndComponents/learn/courses/attendances/CourseAttendance.vue';
import CourseMemberAttendanceCard from '@/PlearndComponents/learn/courses/attendances/CourseMemberAttendanceCard.vue';

const props = defineProps({
    course: Object,
    groups: Object,
    isCourseAdmin: Boolean,
    courseMemberOfAuth: Object,
    attendances: Object,
});


</script>

<template>
    <div>
        <CourseLayout 
            :course 
            :groups
            :isCourseAdmin
            :courseMemberOfAuth
            :activeTab="7"
        >
            <template #courseContent>

                <div class=" mt-4" v-if="props.isCourseAdmin">
                    <CourseAttendance :groups="props.groups.data" />
                </div>
                
                <div class=" mt-4" v-if="!props.isCourseAdmin && props.courseMemberOfAuth">
                    <div class="w-full mx-auto bg-white rounded-lg shadow-md p-4">
                        <p class=" text-lg font-semibold">การลงชื่อเข้าเรียน</p>
                    </div>
                    <div class="w-full mx-auto bg-white rounded-lg shadow-md p-4 mt-4">
                        <CourseMemberAttendanceCard 
                            :attendances
                            :courseMemberOfAuth
                            :courseMember="groups.data.filter( grp => grp.id == props.courseMemberOfAuth.group_id)[0].members.filter(member => member.user.id === props.courseMemberOfAuth.user_id)[0]"
                        />
                    </div>
                </div>
            </template>
        </CourseLayout>
    </div>
</template>
