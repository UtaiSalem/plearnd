<script setup>
import CourseContainer from './CourseContainer.vue';

const props = defineProps({
    course: Object,
    activeTab: {
        type: String,
        default: 'feeds'
    }
});
</script>

<template>
    <CourseContainer 
        :courseId="course.data.id" 
        :initialCourse="course.data"
        :initialTab="activeTab"
    />
</template>
