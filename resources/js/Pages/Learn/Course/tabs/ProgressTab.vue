<script setup>
import { ref, computed } from 'vue';
import { useCourseStore } from '@/stores/course';
import { storeToRefs } from 'pinia';

import StaggeredFade from '@/PlearndComponents/accessories/StaggeredFade.vue';
import MembersProgress from '@/PlearndComponents/learn/courses/progress/MembersProgress.vue';
import GenericListSkeleton from '@/PlearndComponents/accessories/skeletons/GenericListSkeleton.vue';

const props = defineProps({
    course: Object,
});

const store = useCourseStore();
const { progress, isLoading, isCourseAdmin } = storeToRefs(store);

const groups = computed(() => store.course?.groups || []);
const members = computed(() => store.course?.members || store.members || []);
const courseMemberOfAuth = computed(() => store.course?.auth_member);

// หา index จาก group_id ที่บันทึกไว้
function findGroupIndexById(groupId) {
    if (!groupId || !groups.value?.length) return 0;
    const index = groups.value.findIndex(g => g.id === groupId);
    return index >= 0 ? index : 0;
}

const activeGroupTab = ref(findGroupIndexById(courseMemberOfAuth.value?.last_accessed_group_tab));

async function setActiveGroupTab(tab) {
    activeGroupTab.value = tab;

    if (tab < groups.value.length && courseMemberOfAuth.value) {
        try {
            const selectedGroup = groups.value[tab];
            await axios.post(`/courses/${props.course.id}/members/${courseMemberOfAuth.value.id}/set-active-group-tab`, {
                group_tab: selectedGroup.id
            });
            // อัปเดตค่าใน store
            if (store.course?.auth_member) {
                store.course.auth_member.last_accessed_group_tab = selectedGroup.id;
            }
        } catch (error) {
            console.error(error);
        }
    }
}

const unGroupedMembers = computed(() => {
    return members.value.filter((member) => !member.group)
        .filter(member => member.user?.id !== store.course?.user?.id);
});

const activeGroupMembers = computed(() => {
    if (activeGroupTab.value < groups.value.length) {
        return groups.value[activeGroupTab.value]?.members || [];
    } else {
        return unGroupedMembers.value;
    }
});

</script>

<template>
    <div>
        <GenericListSkeleton v-if="isLoading" :rows="8" />

        <template v-else>
            <section aria-multiselectable="false">
                <ul v-if="isCourseAdmin"
                    class="flex flex-wrap items-center border-b plearnd-card bg-gradient-to-r from-blue-50 via-green-50 to-yellow-50" role="tablist">
                    <li v-for="(group, index) in groups" :key="index" class="w-1/2 md:w-1/3 lg:w-1/4" role="presentation">
                        <button @click.prevent="setActiveGroupTab(index)"
                            class="inline-flex items-center justify-center w-full h-12 gap-2 px-6 mb-2 text-sm tracking-wide transition duration-200 border-b-2 rounded-t focus-visible:outline-none whitespace-nowrap font-medium shadow-sm hover:scale-105"
                            :class="activeGroupTab === index
                                ? 'border-blue-500 bg-gradient-to-r from-blue-200 via-green-100 to-yellow-100 text-blue-900 font-bold shadow-lg'
                                : 'border-transparent bg-white text-gray-600 hover:bg-blue-100 hover:text-blue-700'"
                            role="tab" :aria-selected="activeGroupTab === index">
                            <span>{{ group.name + ' (' + (group.members?.length || 0) + ')' }}</span>
                        </button>
                    </li>
                    <li v-if="unGroupedMembers.length > 0" class="w-1/2 md:w-1/3 lg:w-1/4" role="presentation">
                        <button @click.prevent="setActiveGroupTab(groups.length)"
                            class="inline-flex items-center justify-center w-full h-12 gap-2 px-6 mb-2 text-sm tracking-wide transition duration-200 border-b-2 rounded-t focus-visible:outline-none whitespace-nowrap font-medium shadow-sm hover:scale-105"
                            :class="activeGroupTab === groups.length
                                ? 'border-blue-500 bg-gradient-to-r from-blue-200 via-green-100 to-yellow-100 text-blue-900 font-bold shadow-lg'
                                : 'border-transparent bg-white text-gray-600 hover:bg-blue-100 hover:text-blue-700'"
                            role="tab" :aria-selected="activeGroupTab === groups.length">
                            <span>{{ 'ไม่มีกลุ่ม (' + unGroupedMembers.length + ')' }}</span>
                        </button>
                    </li>
                </ul>
            </section>
            <section>
                <div class="bg-gradient-to-br from-blue-50 via-green-50 to-yellow-50 mt-4 p-4 rounded-xl shadow-lg border border-blue-100" 
                    role="tabpanel">
                    <StaggeredFade :duration="50" tag="ul" class="flex flex-col w-full">
                        <MembersProgress 
                            :groupName="groups[activeGroupTab]?.name || 'ไม่มีกลุ่ม'"
                            :members="activeGroupMembers"
                            :isCourseAdmin="isCourseAdmin"
                        />
                    </StaggeredFade>
                </div>
            </section>
        </template>
    </div>
</template>
