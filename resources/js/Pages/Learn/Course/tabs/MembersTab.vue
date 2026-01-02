<script setup>
import { ref, computed } from 'vue';
import { useCourseStore } from '@/stores/course';
import { storeToRefs } from 'pinia';

import StaggeredFade from '@/PlearndComponents/accessories/StaggeredFade.vue';
import GroupedMemberList from '@/PlearndComponents/learn/courses/members/GroupedMemberList.vue';
import GenericListSkeleton from '@/PlearndComponents/accessories/skeletons/GenericListSkeleton.vue';

const props = defineProps({
    course: Object,
});

const store = useCourseStore();
const { members, isLoading, isCourseAdmin } = storeToRefs(store);

// Transform members into groups format if needed
const groupedMembers = computed(() => {
    // If your API returns members grouped by groups, use that
    // Otherwise, you may need to transform here
    return store.course?.groups || [];
});

</script>

<template>
    <div>
        <GenericListSkeleton v-if="isLoading && !members.length" :rows="6" />

        <div v-else-if="!members.length" 
            class="p-4 mb-4 text-base text-gray-600 bg-white border-t-4 border-blue-500 rounded-lg shadow-lg">
            <div class="text-center">
                <p>ยังไม่มีสมาชิกในรายวิชานี้</p>
            </div>
        </div>

        <StaggeredFade v-else :duration="50" tag="ul" class="flex flex-col w-full">
            <GroupedMemberList :groups="groupedMembers" />
        </StaggeredFade>
    </div>
</template>

