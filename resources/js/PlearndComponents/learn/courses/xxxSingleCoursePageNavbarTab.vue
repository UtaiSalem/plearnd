<script setup>
import { ref } from 'vue';
import { Icon } from '@iconify/vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    isCourseAdmin: Boolean,
    course: Object,
    courseMemberOfAuth: Object,
    activeTab: Number,
});

const isLoading = ref(false);

const setActiveTab = async (tab) => {
    // await axios.post(`/courses/${props.course.data.id}/members/${props.courseMemberOfAuth.id}/set-active-tab`, {tab: tab});
    switch (tab) {
        case 1:
            isLoading.value = true;
            router.visit('/courses/'+props.course.data.id+'/lessons');
            break;
        case 2:
            // router.visit('/courses/'+props.course.data.id+'/exercises');
            isLoading.value = true;
            router.visit('/courses/'+props.course.data.id+'/assignments');
            break;
        case 3:
            // router.visit('/courses/'+props.course.data.id+'/exams');
            isLoading.value = true;
            router.visit('/courses/'+props.course.data.id+'/quizzes');
            break;
        case 4:
            isLoading.value = true;
            router.visit('/courses/'+props.course.data.id+'/members');
            break;
        case 5:
            isLoading.value = true;
            router.visit('/courses/'+props.course.data.id+'/groups');
            break;
        case 6:
            // router.visit('/courses/'+props.course.data.id+'/member-requests');
            isLoading.value = true;
            router.visit(`/courses/${props.course.data.id}/members/member-requesters`);
            break;
        case 7:
            isLoading.value = true;
            router.visit('/courses/'+props.course.data.id+'/attendances');
            break;
        case 8:
            isLoading.value = true;
            router.visit('/courses/'+props.course.data.id+'/settings');
            break;
        case 9:
            isLoading.value = true;
            router.visit('/courses/'+props.course.data.id+'/members/'+props.courseMemberOfAuth.id+'/member-settings');
            break;
        case 10:
            isLoading.value = true;
            router.visit('/courses/'+props.course.data.id+'/progress');
            break;
        default:
            isLoading.value = true;
            router.visit('/courses/'+props.course.data.id+'/lessons');
            break;
    }
};
</script>

<template>
    <div class=" bg-white shadow-xl w-full rounded-lg overflow-hidden mt-4">
        <div class="flex flex-row justify-around">
            <button type="button" @click.prevent="setActiveTab(1)"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 1 }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="icon-park-outline:view-grid-detail" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 1 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 1 }">บทเรียน</span>
                </div>
            </button>
            <!-- <Link :href="`/courses/${props.course.data.id}/lessons`"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': $page.url === `/courses/${$page.props.course.data.id}/lessons` }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="icon-park-outline:view-grid-detail" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/lessons` }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/lessons` }">บทเรียน</span>
                </div>
            </Link> -->

            <button type="button" @click.prevent="setActiveTab(2)"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 2 }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="material-symbols:assignment-add-outline" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 2 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 2 }">แบบฝึกหัด</span>
                </div>
            </button>

            <!-- <Link :href="`/courses/${props.course.data.id}/assignments`"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': $page.url === `/courses/${$page.props.course.data.id}/assignments` }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="material-symbols:assignment-add-outline" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/assignments` }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/assignments` }">แบบฝึกหัด</span>
                </div>
            </Link> -->

            <!-- <Link :href="`/courses`" -->
            <button type="button" @click.prevent="setActiveTab(3)"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 3 }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="healthicons:i-exam-qualification-outline" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 3 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 3 }">แบบทดสอบ</span>
                </div>
            </button>
            <!-- <Link :href="`/courses/${props.course.data.id}/quizzes`"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': $page.url === `/courses/${$page.props.course.data.id}/quizzes` }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="healthicons:i-exam-qualification-outline" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/quizzes` }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/quizzes` }">แบบทดสอบ</span>
                </div>
            </Link> -->

            <button type="button" @click.prevent="setActiveTab(4)"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 4 }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="ph:users-four" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 4 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 4 }">สมาชิก</span>
                </div>
            </button>

            <!-- <Link :href="`/courses/${props.course.data.id}/members`"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': $page.url === `/courses/${$page.props.course.data.id}/members` }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="ph:users-four" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/members` }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/members` }">สมาชิก</span>
                </div>
            </Link> -->

            <button type="button" @click.prevent="setActiveTab(5)"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 5 }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="heroicons-outline:user-group" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 5 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 5 }">กลุ่ม</span>
                </div>
            </button>
            <!-- <Link :href="`/courses/${props.course.data.id}/groups`"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': $page.url === `/courses/${$page.props.course.data.id}/groups` }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="heroicons-outline:user-group" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/groups` }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/groups` }">กลุ่ม</span>
                </div>
            </Link> -->

            <button v-if="props.isCourseAdmin" type="button" @click.prevent="setActiveTab(6)"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 6 }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="lets-icons:group-add-light" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 6 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 6 }">คำขอเป็นสมาชิก</span>
                </div>
            </button>
            <!-- <Link :href="`/courses/${props.course.data.id}/members/member-requesters`" v-if="props.isCourseAdmin"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': $page.url === `/courses/${$page.props.course.data.id}/members/member-requesters` }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="lets-icons:group-add-light" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/members/member-requesters` }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/members/member-requesters` }">คำขอเป็นสมาชิก</span>
                </div>
            </Link> -->

            <button v-if="props.isCourseAdmin" type="button" @click.prevent="setActiveTab(7)"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 7 }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="tabler:calendar-user" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 7 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 7 }">การเข้าเรียน</span>
                </div>
            </button>
            <!-- <Link :href="'/courses/'+props.course.data.id+'/attendances'" v-if="props.isCourseAdmin" 
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': $page.url === `/courses/${$page.props.course.data.id}/attendances` }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="tabler:calendar-user" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/attendances` }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/attendances` }">การเข้าเรียน</span>
                </div>
            </Link> -->

            <button type="button" @click.prevent="setActiveTab(8)" v-if="props.isCourseAdmin"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 8 }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="mdi-light:settings" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 8 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 8 }">การตั้งค่า</span>
                </div>
            </button>
            <!-- <Link :href="'/courses/'+props.course.data.id+'/settings'" v-if="props.isCourseAdmin"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': $page.url === `/courses/${$page.props.course.data.id}/settings` }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="mdi-light:settings" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/settings` }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/settings` }">การตั้งค่า</span>
                </div>
            </Link> -->


            <button type="button" @click.prevent="setActiveTab(9)" v-if="!props.isCourseAdmin && props.courseMemberOfAuth"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 9 }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="mdi:graph-box-plus-outline" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 9 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 9 }">ผลการเรียน</span>
                </div>
            </button>

            <!-- <Link :href="'/courses/'+props.course.data.id+'/members/'+props.courseMemberOfAuth.id+'/member-settings'" v-if="!props.isCourseAdmin && props.courseMemberOfAuth"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': $page.url === `/courses/${$page.props.course.data.id}/members/${props.courseMemberOfAuth.id}/member-settings` }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="mdi:graph-box-plus-outline" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/members/${props.courseMemberOfAuth.id}/member-settings` }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/members/${props.courseMemberOfAuth.id}/member-settings` }">ผลการเรียน</span>
                </div>
            </Link> -->

            <!-- <Link :href="`/courses/${props.course.data.id}/results`" v-if="!props.isCourseAdmin"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 9 }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="mdi:graph-box-plus-outline" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 9 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 9 }">ผลการเรียน</span>
                </div>
            </Link> -->

            <!-- <button type="button" @click.prevent="setActiveTab(10)" v-if="props.isCourseAdmin"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 10 }">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="mdi:graph-box-plus-outline" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 10 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 10 }">ผลการเรียน</span>
                </div>
            </button> -->
            <Link :href="`/courses/${$page.props.course.data.id}/progress`" v-if="props.isCourseAdmin"
                class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                :class="{'border-b-4 border-cyan-500 bg-cyan-100': $page.url === `/courses/${$page.props.course.data.id}/progress`}">
                <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                    <Icon icon="mdi:graph-box-plus-outline" class="w-6 md:w-8 h-6 md:h-8"
                        :class="{'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/progress`}" />
                    <span class="hidden md:block" :class="{'text-cyan-500': $page.url === `/courses/${$page.props.course.data.id}/progress`}">ผลการเรียน</span>
                </div>
            </Link>

            <!-- <Link :href="`/course/${$page.props.course.data.id}/attendaces`"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        >
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="mdi:graph-box-outline" class="w-6 md:w-8 h-6 md:h-8"
                                 />
                            <span class="hidden md:block">ผลการเรียน</span>
                        </div>
                    </Link> -->

            <!-- <Link :href="route('course.member.show',[props.course.data.id, courseMemberOfAuth.id])" 
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        >
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="mdi:graph-box-outline" class="w-6 md:w-8 h-6 md:h-8"
                                 />
                            <span class="hidden md:block">ผลการเรียน</span>
                        </div>
                    </Link> -->

        </div>
    </div>
    <div v-if="isLoading" class="modal fixed z-50 w-full h-full top-0 left-0 flex items-center justify-center">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-75"></div>
        <div class="flex items-center justify-center h-64">
            <div class="animate-spin rounded-full h-32 w-32 border-t-2 border-b-2 border-white"></div>
        </div>
    </div>
</template>
