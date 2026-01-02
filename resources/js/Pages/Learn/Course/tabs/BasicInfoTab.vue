<script setup>
import { computed } from 'vue';
import { useCourseStore } from '@/stores/course';
import { storeToRefs } from 'pinia';

import CourseInfomation from '@/PlearndComponents/learn/courses/info/CourseInfomation.vue';

const props = defineProps({
    course: Object,
});

const store = useCourseStore();
const { isLoading, isCourseAdmin } = storeToRefs(store);

</script>

<template>
    <div>
        <div v-if="isLoading" class="flex justify-center p-8">
            กำลังโหลดข้อมูลรายวิชา...
        </div>

        <div v-else>
            <CourseInfomation
                :course="course" 
                :isCourseAdmin="isCourseAdmin"
            />
        </div>
    </div>
</template>
