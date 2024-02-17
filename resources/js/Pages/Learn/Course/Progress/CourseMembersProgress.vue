<script setup>
import { ref, onMounted, computed } from 'vue';
import { usePage } from "@inertiajs/vue3";

import CourseLayout from '@/Layouts/CourseLayout.vue';
import StaggeredFade from '@/PlearndComponents/accessories/StaggeredFade.vue';
import MemberCard from '@/PlearndComponents/learn/courses/members/MemberCard.vue'

const props = defineProps({
    course: Object,
    lessons: Object,
    groups: Object,
    isCourseAdmin: Boolean,
});

const grade_progresss = ref([]);
const isLoading = ref(false);
const activeGroupTab = ref(0);


// const authGroupIndex = computed(()=> {
//     if (usePage().props.isCourseAdmin) return 0;
//     return usePage().props.groups.data.findIndex((group)=> group.id === usePage().props.courseMemberOfAuth.group_id);
// });

const unGroupedMembers = ref(usePage().props.members.data.filter((member)=> !member.group));

const activeGroupMembers = computed(()=> {
    if (activeGroupTab.value < usePage().props.groups.data.length) {
        return props.groups.data[activeGroupTab.value].members;
    }else{
        // return usePage().props.members.data.filter((member)=> !member.group);
        return unGroupedMembers.value;
    }
});

function setActiveGroupTab(tab){
    activeGroupTab.value = tab;
}

// onMounted(async () => {
//     isLoading.value = true;
//     try {
//         let resp = await axios.get(`/courses/${props.course.data.id}/groups/${props.groups.data[activeGroupTab.value].id}/members/progress`);
//         grade_progresss.value = resp.data;
//         isLoading.value = false;
//     } catch (error) {
//         console.log(error);
//         isLoading.value = false;
//     }
// });
</script>

<template>
    <div>
        <CourseLayout
        :course="props.course"
        :lessons="props.lessons"
        :groups="props.groups"
        :isCourseAdmin="props.isCourseAdmin"
        >
            <template #courseContent>
                <div class="w-full mx-auto ">
                    <section class="max-w-full" aria-multiselectable="false">
                        <ul v-if="$page.props.isCourseAdmin" class="flex flex-wrap items-center border-b border-slate-200 plearnd-card" role="tablist">
                            <li v-for="(group, index) in $page.props.groups.data" :key="index" class="w-1/3" role="presentation ">
                                <button @click.prevent="setActiveGroupTab(index)"
                                    class="inline-flex items-center justify-center w-full h-12 gap-2 px-6 mb-2 text-sm tracking-wide transition duration-300 border-b-2 rounded-t focus-visible:outline-none whitespace-nowrap hover:border-violet-600 focus:border-violet-700 text-violet-500 hover:text-violet-600 focus:text-violet-700 hover:bg-violet-50 focus:bg-violet-50 disabled:cursor-not-allowed disabled:border-slate-500 stroke-violet-500 hover:stroke-violet-600 focus:stroke-violet-700"
                                    :class="activeGroupTab === index ? 'border-violet-500 bg-violet-200 font-bold':'font-medium'"
                                    id="tab-label-1a" role="tab" aria-setsize="3" aria-posinset="1" tabindex="0"
                                    aria-controls="tab-panel-1a" aria-selected="true">
                                    <span>{{ group.name }}</span>
                                </button>
                            </li>
                            <li>
                                <button @click.prevent="setActiveGroupTab($page.props.groups.data.length)"
                                    class="inline-flex items-center justify-center w-full h-12 gap-2 px-6 mb-2 text-sm tracking-wide transition duration-300 border-b-2 rounded-t focus-visible:outline-none whitespace-nowrap hover:border-violet-600 focus:border-violet-700 text-violet-500 hover:text-violet-600 focus:text-violet-700 hover:bg-violet-50 focus:bg-violet-50 disabled:cursor-not-allowed disabled:border-slate-500 stroke-violet-500 hover:stroke-violet-600 focus:stroke-violet-700"
                                    :class="activeGroupTab === $page.props.groups.data.length ? 'border-violet-500 bg-violet-200 font-bold':'font-medium'"
                                    id="tab-label-1a" role="tab" aria-setsize="3" aria-posinset="1" tabindex="0"
                                    aria-controls="tab-panel-1a" aria-selected="true">
                                    <span>{{ 'ไม่มีกลุ่ม' }}</span>
                                </button>
                            </li>
                        </ul>
                    </section>
                    <section class="">
                        <div class="" :class="{ 'pt-4': $page.props.isCourseAdmin }" id="tab-panel-1a"
                            aria-hidden="false" role="tabpanel" aria-labelledby="tab-label-1a" tabindex="-1">
                            <staggered-fade :duration="50" tag="ul" class="flex flex-col w-full ">

                                <div class="relative overflow-x-auto rounded-lg">
                                    <table
                                        class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        <thead
                                            class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <th scope="col" class="px-6 py-3">
                                                    เลขที่
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    รหัส
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    ชื่อ - สกุล
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    Color
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    Category
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    Price
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                <th scope="row"
                                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    1
                                                </th>
                                                <td class="px-6 py-4">
                                                    101001
                                                </td>
                                                <td class="px-6 py-4">
                                                    อุทัย สาเหล็ม
                                                </td>
                                                <td class="px-6 py-4">
                                                    Silver
                                                </td>
                                                <td class="px-6 py-4">
                                                    Laptop
                                                </td>
                                                <td class="px-6 py-4">
                                                    $2999
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </staggered-fade>
                        </div>
                    </section>
                    <div class="" :class="{'pt-4': $page.props.isCourseAdmin}" id="tab-panel-1a" aria-hidden="false" role="tabpanel"
                        aria-labelledby="tab-label-1a" tabindex="-1">
                        <staggered-fade :duration="50" tag="ul" class="flex flex-col w-full ">
                            <MemberCard
                                v-for="(member, index) in activeGroupMembers.sort(function(a,b) {return a.order_number - b.order_number})"
                                :key="index" :data-index="index" :member="member"
                                @request-unmember-course="requestToBeUnMember(member.id, index)"
                             />
                        </staggered-fade>
                    </div>
                </div>
            </template>
        </CourseLayout>
    </div>
</template>

