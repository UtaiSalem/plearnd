<script setup>
import { ref, onMounted, computed } from 'vue';

import CourseLayout from "@/Layouts/CourseLayout.vue";

const props = defineProps({
    academy: Object,
    course: Object,
    courseMemberOfAuth: Object,
    isCourseAdmin: Boolean,
});

const isDarkMode = ref(false);
const isMobile = ref(false);

onMounted(() => { 
    if ('ontouchstart' in window) { isMobile.value = true; };
});


</script>

<template>
    <CourseLayout
        :course 
        :isCourseAdmin
        :courseMemberOfAuth
        :activeTab="1"
    >
        <template #courseContent>
            <div>

            </div>
        </template>

    </CourseLayout>
</template>
