<script setup>
import { ref, computed } from 'vue';
import { Icon } from '@iconify/vue';

// import Swal from 'sweetalert2';
import MainLayout from "@/Layouts/MainLayout.vue";
import CourseProfileCover from '@/PlearndComponents/learn/courses/CourseProfileCover.vue';
import SingleCoursePageNavbarTab from '@/PlearndComponents/learn/courses/SingleCoursePageNavbarTab.vue';
import { useCourseProfileStore } from '@/stores/courseProfile.js';

const props = defineProps({
    course: Object,
    groups: Object,
    lessons: Object,
    isCourseAdmin: Boolean,
    courseMemberOfAuth: Object,
    activeTab: Number,
});

// const activeTab = ref(1);

// Initialize store
const courseProfileStore = useCourseProfileStore();

const compCover = computed(() => {
    return props.course.data.cover ? '/storage/images/courses/covers/'+props.course.data.cover:'/storage/images/courses/covers/default_cover.jpg';
})

const tempLogo = ref(props.course.data.user.avatar);
const tempCover = ref(compCover);
const tempHeader = ref(props.course.data.name);
const tempSubheader = ref(props.course.data.code);

// Check if user is not a member of course
const isNotMember = computed(() => {
    return !props.courseMemberOfAuth || props.courseMemberOfAuth.status !== 'active';
});

async function onCoverImageChange(coverFile) {
    const config = { headers: { 'content-type': 'multipart/form-data' } };
    const courseCoverUpdate = new FormData();
    courseCoverUpdate.append('cover', coverFile);
    courseCoverUpdate.append('_method', 'patch');
    await axios.post(`/courses/${props.course.data.id}`, courseCoverUpdate, config );
    
    // usePage().props.course.data.cover = resp.data.course.cover;
}

async function onLogoImageChange(logoFile) {
    const config = { headers: { 'content-type': 'multipart/form-data' } };
    const courseLogoUpdate = new FormData();
    courseLogoUpdate.append('logo', logoFile);
    courseLogoUpdate.append('_method', 'patch');
    await axios.post(`/courses/${props.course.data.id}`, courseLogoUpdate, config );
}

async function onHeaderChange(courseName) {
    await axios.patch(`/courses/${props.course.data.id}`, { name: courseName });
}

async function onSubheaderChange(courseCode) {
    await axios.patch(`/courses/${props.course.data.id}`, { code: courseCode });
}

async function onRequestToBeAMember(groupId = null, groupIndex = null) {
    try {
        // Use the store's requestToBeMember function
        await courseProfileStore.requestToBeMember(props.course.data.id, groupId);
        // The store handles UI updates, so no need to refresh the page
    } catch (error) {
        console.error('Error joining course:', error);
    }
}

async function onRequestToBeUnMember(memberId) {
    try {
        // Use the store's requestToBeUnmember function
        await courseProfileStore.requestToBeUnmember(props.course.data.id, memberId);
        // The store handles UI updates, so no need to refresh the page
    } catch (error) {
        console.error('Error leaving course:', error);
    }
}

</script>

<template>
    <!-- <Head title="รายวิชา" /> -->

    <MainLayout :title="props.course.data.name">

        <!-- Notification bar for non-members -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
            <div class="flex items-center justify-between">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            คุณยังไม่ได้เป็นสมาชิกของรายวิชา "{{ props.course.data.name }}" กรุณาสมัครเข้าร่วมรายวิชาเพื่อเข้าถึงเนื้อหาทั้งหมด
                        </p>
                    </div>
                </div>
                <div class="flex-shrink-0 ml-4">
                    <button @click="onRequestToBeAMember" class="bg-yellow-400 hover:bg-yellow-500 text-yellow-900 font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                        สมัครเข้าร่วม
                    </button>
                </div>
            </div>
        </div>

        <template #coverProfileCard>
            <CourseProfileCover
                    :coverImage="tempCover"
                    :logoImage="tempLogo"
                    v-model:cover-header="tempHeader"
                    v-model:cover-subheader="tempSubheader"

                    :model="'Course'"
                    :modelTable="'courses'"
                    :modelableId="props.course.data.id"
                    :modelableType="'app/models/Course'"
                    :modelableRoute="`/courses/${props.course.data.id}`"
                    :modelData="props.course.data"

                    :subModelNameTh="'บทเรียน'"

                    :courseMemberOfAuth="props.courseMemberOfAuth"

                    @cover-image-change="(data) => { onCoverImageChange(data); }"
                    @logo-image-change="(data) => { onLogoImageChange(data); }"
                    @header-change="(data)=>{ onHeaderChange(data) }"
                    @subheader-change="(data)=> { onSubheaderChange(data) }"
                    @request-tobe-member="(groupId,groupIndex)=> { onRequestToBeAMember(groupId,groupIndex) }"
                    @request-tobe-unmember="(crsMbrId)=> { onRequestToBeUnMember(crsMbrId) }"
            >
            </CourseProfileCover>

            <!-- Non-member notification banner -->
            <div v-if="!props.courseMemberOfAuth && !props.isCourseAdmin" class="w-full p-3 mb-4 flex flex-col items-center bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded-md">
                <div class="flex items-center">
                    <Icon icon="heroicons-outline:information-circle" class="w-12 h-12 mr-2" />
                    <p class="text-sm font-medium">คุณยังไม่ได้เป็นสมาชิกของรายวิชานี้</p>
                </div>
                <p class="text-xs mt-1 ml-7">กรุณาสมัครเป็นสมาชิกเพื่อเข้าถึงเนื้อหาทั้งหมด</p>
            </div>

            <SingleCoursePageNavbarTab
                :course
                :courseMemberOfAuth
                :isCourseAdmin
                :activeTab
            />

        </template>

        <template #leftSideWidget>
            <div class="space-y-4">
                <!-- <CourseCardWidget :course="$page.props.course.data"/> -->

                <!-- <CourseGroupProfileWidget /> -->
            </div>
        </template>

        <template #mainContent>
            <div class="mt-4">
                <div v-if="$slots.courseContent">
                    <slot name="courseContent"></slot>
                </div>
            </div>
        </template>

        <template #rightSideWidget>
            <div>
                <!-- <CourseCardWidget :course="$page.props.course.data"/> -->
            </div>
        </template>
    </MainLayout>
</template>
