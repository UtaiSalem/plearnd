<script setup>
import { ref } from 'vue';
import { useCourseStore } from '@/stores/course';
import { storeToRefs } from 'pinia';

import LessonsList from '@/PlearndComponents/learn/courses/lessons/LessonsList.vue';
import CreateNewLesson from '@/PlearndComponents/learn/courses/lessons/CreateNewLesson.vue';
import GenericListSkeleton from '@/PlearndComponents/accessories/skeletons/GenericListSkeleton.vue';

const props = defineProps({
    course: Object,
});

const store = useCourseStore();
const { lessons, isLoading, isCourseAdmin } = storeToRefs(store);

const handleAddNewLesson = (newLesson) => {
    lessons.value.push(newLesson);
};

</script>

<template>
    <div class="space-y-2">
        <div v-if="!lessons.length && !isLoading"
            class="p-4 mb-4 text-base text-gray-600 bg-white border-t-4 border-blue-500 rounded-lg shadow-lg ">
            <div class="text-center">
                <p>ยังไม่มีบทเรียนในรายวิชานี้</p>
            </div>
        </div>

        <LessonsList v-if="lessons.length" :lessons="lessons" />

        <CreateNewLesson v-if="isCourseAdmin"
            @add-new-lesson="handleAddNewLesson" 
        />
        
        <GenericListSkeleton v-if="isLoading && !lessons.length" :rows="4" :showThumbnail="true" />
    </div>
</template>
