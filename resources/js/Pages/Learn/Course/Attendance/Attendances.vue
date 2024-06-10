<script setup>

import CourseLayout from '@/Layouts/CourseLayout.vue';
import CourseAttendance from '@/PlearndComponents/learn/courses/attendances/CourseAttendance.vue';

const props = defineProps({
    course: Object,
    groups: Object,
    isCourseAdmin: Boolean,
    courseMemberOfAuth: Object,
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
                <div class=" mt-4">
                    <CourseAttendance :groups="props.groups.data" />
                </div>
            </template>
        </CourseLayout>
    </div>
</template>
