<script setup>
import CourseLayout from '@/Layouts/CourseLayout.vue';
import SingleCourseLesson from '@/PlearndComponents/learn/courses/lessons/SingleCourseLesson.vue';

const props = defineProps({
    course: Object,
    lesson: Object,
    isCourseAdmin: Boolean,
    courseMemberOfAuth: Object,
    authUserPP: Number,
    groups: Object,
});

</script>

<template>
    <div>
     <CourseLayout
        :course="props.course"
        :groups="props.groups"
        :isCourseAdmin="props.isCourseAdmin"
        :activeTab="1"
     >
        <template #courseContent>
            <div>
                <SingleCourseLesson :lesson="props.lesson.data" :isCourseAdmin="props.isCourseAdmin" />  
            </div>
        </template>
     </CourseLayout>
    </div>
</template>
