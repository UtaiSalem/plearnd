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
        case 11:
            isLoading.value = true;
            router.visit('/courses/'+props.course.data.id+'/feeds');
            break;
        default:
            isLoading.value = true;
            router.visit('/courses/'+props.course.data.id+'/lessons');
            break;
    }
};
</script>

<template>
    <div class="w-full mt-4 overflow-hidden bg-white rounded-lg shadow-xl ">
        <div class="flex flex-row justify-around">

            <button type="button" @click.prevent="setActiveTab(11)"
                class="flex-row justify-center w-full text-center border-b-4 rounded-none tab-item hover:border-gray-400 "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 11 }">
                <div class="flex flex-col items-center justify-center py-2 text-slate-600/80 ">
                    <Icon icon="codicon:feedback" class="w-6 h-6 md:w-8 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 11 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 11 }">กระดาน</span>
                </div>
            </button>

            <button type="button" @click.prevent="setActiveTab(1)"
                class="flex-row justify-center w-full text-center border-b-4 rounded-none tab-item hover:border-gray-400 "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 1 }">
                <div class="flex flex-col items-center justify-center py-2 text-slate-600/80 ">
                    <Icon icon="icon-park-outline:view-grid-detail" class="w-6 h-6 md:w-8 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 1 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 1 }">บทเรียน</span>
                </div>
            </button>

            <button type="button" @click.prevent="setActiveTab(2)"
                class="flex-row justify-center w-full text-center border-b-4 rounded-none tab-item hover:border-gray-400 "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 2 }">
                <div class="flex flex-col items-center justify-center py-2 text-slate-600/80 ">
                    <Icon icon="material-symbols:assignment-add-outline" class="w-6 h-6 md:w-8 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 2 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 2 }">แบบฝึกหัด</span>
                </div>
            </button>

            <button type="button" @click.prevent="setActiveTab(3)" v-if="props.courseMemberOfAuth || props.isCourseAdmin"
                class="flex-row justify-center w-full text-center border-b-4 rounded-none tab-item hover:border-gray-400 "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 3 }">
                <div class="flex flex-col items-center justify-center py-2 text-slate-600/80 ">
                    <Icon icon="healthicons:i-exam-qualification-outline" class="w-6 h-6 md:w-8 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 3 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 3 }">แบบทดสอบ</span>
                </div>
            </button>

            <button type="button" @click.prevent="setActiveTab(5)"
                class="flex-row justify-center w-full text-center border-b-4 rounded-none tab-item hover:border-gray-400 "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 5 }">
                <div class="flex flex-col items-center justify-center py-2 text-slate-600/80 ">
                    <Icon icon="heroicons-outline:user-group" class="w-6 h-6 md:w-8 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 5 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 5 }">กลุ่ม</span>
                </div>
            </button>
            
            <button type="button" @click.prevent="setActiveTab(4)" v-if="props.courseMemberOfAuth !== null"
                class="flex-row justify-center w-full text-center border-b-4 rounded-none tab-item hover:border-gray-400 "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 4 }">
                <div class="flex flex-col items-center justify-center py-2 text-slate-600/80 ">
                    <Icon icon="ph:users-four" class="w-6 h-6 md:w-8 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 4 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 4 }">สมาชิก</span>
                </div>
            </button>

            <!-- <button v-if="props.isCourseAdmin" type="button" @click.prevent="setActiveTab(6)"
                class="flex-row justify-center w-full text-center border-b-4 rounded-none tab-item hover:border-gray-400 "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 6 }">
                <div class="flex flex-col items-center justify-center py-2 text-slate-600/80 ">
                    <Icon icon="lets-icons:group-add-light" class="w-6 h-6 md:w-8 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 6 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 6 }">คำขอเป็นสมาชิก</span>
                </div>
            </button> -->

            <button v-if="props.isCourseAdmin" type="button" @click.prevent="setActiveTab(7)"
                class="flex-row justify-center w-full text-center border-b-4 rounded-none tab-item hover:border-gray-400 "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 7 }">
                <div class="flex flex-col items-center justify-center py-2 text-slate-600/80 ">
                    <Icon icon="tabler:calendar-user" class="w-6 h-6 md:w-8 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 7 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 7 }">การเข้าเรียน</span>
                </div>
            </button>

            <button type="button" @click.prevent="setActiveTab(8)" v-if="props.isCourseAdmin"
                class="flex-row justify-center w-full text-center border-b-4 rounded-none tab-item hover:border-gray-400 "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 8 }">
                <div class="flex flex-col items-center justify-center py-2 text-slate-600/80 ">
                    <Icon icon="mdi-light:settings" class="w-6 h-6 md:w-8 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 8 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 8 }">การตั้งค่า</span>
                </div>
            </button>

            <button type="button" @click.prevent="setActiveTab(9)" v-if="!props.isCourseAdmin && props.courseMemberOfAuth"
                class="flex-row justify-center w-full text-center border-b-4 rounded-none tab-item hover:border-gray-400 "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 9 }">
                <div class="flex flex-col items-center justify-center py-2 text-slate-600/80 ">
                    <Icon icon="mdi:graph-box-plus-outline" class="w-6 h-6 md:w-8 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 9 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 9 }">ผลการเรียน</span>
                </div>
            </button>

            <button type="button" @click.prevent="setActiveTab(10)" v-if="props.isCourseAdmin"
                class="flex-row justify-center w-full text-center border-b-4 rounded-none tab-item hover:border-gray-400 "
                :class="{ 'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 10 }">
                <div class="flex flex-col items-center justify-center py-2 text-slate-600/80 ">
                    <Icon icon="mdi:graph-box-plus-outline" class="w-6 h-6 md:w-8 md:h-8"
                        :class="{ 'text-cyan-500': activeTab === 10 }" />
                    <span class="hidden md:block" :class="{ 'text-cyan-500': activeTab === 10 }">ผลการเรียน</span>
                </div>
            </button>
            
            <!-- <Link :href="`/course/${$page.props.course.data.id}/attendaces`"
                class="flex-row justify-center w-full text-center border-b-4 rounded-none tab-item hover:border-gray-400 "
                >
                <div class="flex flex-col items-center justify-center py-2 text-slate-600/80 ">
                    <Icon icon="mdi:graph-box-outline" class="w-6 h-6 md:w-8 md:h-8"
                            />
                    <span class="hidden md:block">ผลการเรียน</span>
                </div>
            </Link> -->

        </div>
    </div>
    <div v-if="isLoading" class="fixed top-0 left-0 z-20 flex items-center justify-center w-full h-full modal">
        <div class="absolute w-full h-full bg-gray-900 opacity-75 modal-overlay"></div>
        <div class="flex items-center justify-center h-64">
            <!-- <div class="animate-spin rounded-full h-32 w-32 border-y-[16px] border-white"></div> -->
            <Icon icon="svg-spinners:bars-rotate-fade" class="z-30 w-32 h-32 text-white" />
        </div>
    </div>
</template>
