<script setup>
import { provide, ref } from 'vue';
import { useCourseStore } from '@/stores/course';
import { storeToRefs } from 'pinia';

import AssignmentListViewer from '@/PlearndComponents/learn/courses/assignments/AssignmentListViewer.vue'; 
import CreateNewAssignmentCard from '@/PlearndComponents/learn/courses/assignments/CreateNewAssignmentCard.vue';
import LoadingPage from '@/PlearndComponents/accessories/LoadingPage.vue';
import GenericListSkeleton from '@/PlearndComponents/accessories/skeletons/GenericListSkeleton.vue';

const props = defineProps({
    course: Object,
});

const store = useCourseStore();
const { assignments, isLoading, isCourseAdmin } = storeToRefs(store);

const isLoadingPage = ref(false);
const setLoadingPage = (val) => {
    isLoadingPage.value = val;
}

provide('isLoadingPage', {
    isLoadingPage,
    setLoadingPage,
});

const handleAddNewAssignment = (newAsm) => {
    assignments.value.push(newAsm);
};

</script>

<template>
    <div class="relative">
        <LoadingPage v-if="isLoadingPage" />
        
        <GenericListSkeleton v-if="isLoading && !assignments.length" :rows="3" :showAvatar="false" />

        <div v-else class="space-y-3 sm:space-y-4">
            <AssignmentListViewer
                :assignmentableType="'courses'"
                :assignmentableId="course.id"
                :assignmentNameTh="'รายวิชา'"
                :assignmentApiRoute="`/courses/${course.id}`"
                v-model:assignments="assignments"
            />
            <CreateNewAssignmentCard v-if="isCourseAdmin"
                :assignmentableType="'courses'"
                :assignmentableId="course.id"
                :assignmentNameTh="'รายวิชา'"
                :assignmentApiRoute="`/courses/${course.id}`"
                @add-new-assignment="handleAddNewAssignment"
            />
        </div>
    </div>
</template>
