<script setup>
import { ref, computed } from 'vue';
import { usePage } from "@inertiajs/vue3";

// import Swal from 'sweetalert2';
import MainLayout from "@/Layouts/MainLayout.vue";
import CourseProfileCover from '@/PlearndComponents/learn/courses/CourseProfileCover.vue';
import SingleCoursePageNavbarTab from '@/PlearndComponents/learn/courses/SingleCoursePageNavbarTab.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    course: Object,
    isCourseAdmin: Boolean,
    courseMemberOfAuth: Object,
    activeTab: Number,
});

// const activeTab = ref(1);

const compCover = computed(() => {
    return props.course.data.cover ? '/storage/images/courses/covers/'+props.course.data.cover:'/storage/images/courses/covers/default_cover.jpg';
})

const tempLogo = ref(props.course.data.user.avatar);
const tempCover = ref(compCover);
const tempHeader = ref(props.course.data.name);
const tempSubheader = ref(props.course.data.code);


async function onCoverImageChange(coverFile) {
    const config = { headers: { 'content-type': 'multipart/form-data' } };
    const courseCoverUpdate = new FormData();
    courseCoverUpdate.append('cover', coverFile);
    courseCoverUpdate.append('_method', 'patch');
    await axios.post(`/courses/${props.course.data.id}`, courseCoverUpdate, config );
    
    // usePage().props.course.data.cover = resp.data.course.cover;
}

async function onHeaderChange(courseName) {
    await axios.patch(`/courses/${props.course.data.id}`, { name: courseName });
}

async function onSubheaderChange(courseCode) {
    await axios.patch(`/courses/${props.course.data.id}`, { code: courseCode });
}

</script>

<template>
    <!-- <Head title="รายวิชา" /> -->

    <MainLayout :title="props.course.data.name">

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
