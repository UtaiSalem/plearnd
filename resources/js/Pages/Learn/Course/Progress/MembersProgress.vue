<script setup>
import { ref, computed } from 'vue';
import { usePage } from "@inertiajs/vue3";

import CourseLayout from '@/Layouts/CourseLayout.vue';
import StaggeredFade from '@/PlearndComponents/accessories/StaggeredFade.vue';
import MembersProgress from '@/PlearndComponents/learn/courses/progress/MembersProgress.vue';

const props = defineProps({
    course: Object,
    lessons: Object,
    groups: Object,
    isCourseAdmin: Boolean,
    courseMemberOfAuth: Object,
});

const activeGroupTab = ref(usePage().props.courseMemberOfAuth ? usePage().props.courseMemberOfAuth.last_accessed_group_tab || 0 : 0);

async function setActiveGroupTab(tab) {
    activeGroupTab.value = tab;

    if ((tab < props.groups.data.length)){
        let resp = await axios.post(`/courses/${usePage().props.course.data.id}/members/${usePage().props.courseMemberOfAuth.id}/set-active-group-tab`, {group_tab: activeGroupTab.value});
        if (resp.data.success) {
            usePage().props.courseMemberOfAuth ? usePage().props.courseMemberOfAuth.last_accessed_group_tab = tab : null;
        }
    }
}

const unGroupedMembers = ref(usePage().props.members.data.filter((member) => !member.group).filter(member => member.user.id !== usePage().props.course.data.user.id));

const activeGroupMembers = computed(() => {
    if (activeGroupTab.value < usePage().props.groups.data.length) {
        return usePage().props.groups.data[activeGroupTab.value].members;
    } else {
        return unGroupedMembers.value;
    }
});

</script>

<template>
    <div>
        <CourseLayout
            :course="props.course"
            :lessons="props.lessons"
            :groups="props.groups"
            :isCourseAdmin="props.isCourseAdmin"
            :courseMemberOfAuth="props.courseMemberOfAuth"
            :activeTab="10"
        >
            <template #courseContent>
                <div class=" md:-ml-4 md:mr-4">
                    <section class="">
                        <div class="bg-white mt-4 p-2 rounded-lg" id="tab-panel-1a" aria-hidden="false"
                            role="tabpanel" aria-labelledby="tab-label-1a" tabindex="-1">
                            <staggered-fade :duration="50" tag="ul" class="flex flex-col w-full ">
                                <MembersProgress 
                                :groupName="$page.props.groups.data[activeGroupTab] ? $page.props.groups.data[activeGroupTab].name : 'ไม่มีกลุ่ม'"
                                :members="activeGroupMembers" 
                                />
                            </staggered-fade>
                        </div>
                    </section>
                </div>
            </template>
        </CourseLayout>
    </div>
</template>
