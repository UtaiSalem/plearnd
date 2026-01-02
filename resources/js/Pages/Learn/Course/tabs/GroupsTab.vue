<script setup>
import { computed } from 'vue';
import { useCourseStore } from '@/stores/course';
import { storeToRefs } from 'pinia';

import CourseGroupList from '@/PlearndComponents/learn/courses/groups/CourseGroupList.vue';
import GenericListSkeleton from '@/PlearndComponents/accessories/skeletons/GenericListSkeleton.vue';

const props = defineProps({
    course: Object,
});

const store = useCourseStore();
const { isLoading, isCourseAdmin } = storeToRefs(store);

const groups = computed({
    get: () => store.course?.groups || [],
    set: (val) => {
        if (store.course) {
            store.course.groups = val;
        }
    }
});

const courseMemberOfAuth = computed(() => store.course?.auth_member);

function handleAddNewGroup(group) {
    groups.value = [...groups.value, group];
}

function handleDeleteGroup(index) {
    groups.value = groups.value.filter((_, i) => i !== index);
}

</script>

<template>
    <div>
        <GenericListSkeleton v-if="isLoading && !groups.length" :rows="3" />

        <div v-else>
            <CourseGroupList 
                :course="course" 
                :groups="groups" 
                :courseMemberOfAuth="courseMemberOfAuth"
                @add-new-group="handleAddNewGroup"
                @delete-group="handleDeleteGroup"
            />
        </div>
    </div>
</template>
