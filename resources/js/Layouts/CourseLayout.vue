<script setup>
import { computed, watch } from 'vue';
import { Icon } from '@iconify/vue';
import { usePage } from '@inertiajs/vue3';

import MainLayout from "@/Layouts/MainLayout.vue";
import CourseProfileCover from '@/PlearndComponents/learn/courses/CourseProfileCover.vue';
import SingleCoursePageNavbarTab from '@/PlearndComponents/learn/courses/SingleCoursePageNavbarTab.vue';
import { useCourseProfileStore } from '@/stores/courseProfile.js';

// Sidebar Widgets
import CourseQuickNav from '@/PlearndComponents/learn/courses/widgets/CourseQuickNav.vue';
import CourseStatsWidget from '@/PlearndComponents/learn/courses/widgets/CourseStatsWidget.vue';
import RecentLessonsWidget from '@/PlearndComponents/learn/courses/widgets/RecentLessonsWidget.vue';
import UpcomingAssignmentsWidget from '@/PlearndComponents/learn/courses/widgets/UpcomingAssignmentsWidget.vue';
import CourseMembersWidget from '@/PlearndComponents/learn/courses/widgets/CourseMembersWidget.vue';
import MyMembershipWidget from '@/PlearndComponents/learn/courses/widgets/MyMembershipWidget.vue';

const props = defineProps({
    course: Object,
    groups: Object,
    lessons: Object,
    isCourseAdmin: Boolean,
    courseMemberOfAuth: Object,
    activeTab: Number,
});

// Initialize store
const courseProfileStore = useCourseProfileStore();

// Reactive computed properties that update from props
const coverImage = computed(() => {
    return props.course.data.cover 
        ? '/storage/images/courses/covers/' + props.course.data.cover
        : '/storage/images/courses/covers/default_cover.jpg';
});

const logoImage = computed(() => {
    return props.course.data.logo 
        ? '/storage/images/courses/logos/' + props.course.data.logo 
        : props.course.data.user.avatar;
});

const courseName = computed({
    get: () => props.course.data.name,
    set: (value) => {
        props.course.data.name = value;
    }
});

const courseCode = computed({
    get: () => props.course.data.code,
    set: (value) => {
        props.course.data.code = value;
    }
});

// Check if user is not a member of course
const isNotMember = computed(() => {
    return !props.courseMemberOfAuth || props.courseMemberOfAuth.status !== 'active';
});

// Image update handlers - now properly handled by store
async function handleCoverUpdate(newPath) {
    // Update the props to reflect the new image path
    if (newPath) {
        props.course.data.cover = newPath.replace('/storage/images/courses/covers/', '');
    }
}

async function handleLogoUpdate(newPath) {
    // Update the props to reflect the new image path
    if (newPath) {
        props.course.data.logo = newPath.replace('/storage/images/courses/logos/', '');
    }
}

// Header/Subheader handlers - use store methods
async function handleHeaderChange(newName) {
    // Already handled by store in CourseProfileCover
    courseName.value = newName;
}

async function handleSubheaderChange(newCode) {
    // Already handled by store in CourseProfileCover
    courseCode.value = newCode;
}

// Membership handlers
async function handleRequestToBeMember(groupId = null, groupIndex = null) {
    try {
        await courseProfileStore.requestToBeMember(props.course.data.id, groupId);
    } catch (error) {
        console.error('Error joining course:', error);
    }
}

async function handleRequestToBeUnMember(memberId) {
    try {
        await courseProfileStore.requestToBeUnmember(props.course.data.id, memberId);
    } catch (error) {
        console.error('Error leaving course:', error);
    }
}

</script>

<template>
    <MainLayout :title="props.course.data.name">
        <template #coverProfileCard>
            <CourseProfileCover
                :coverImage="coverImage"
                :logoImage="logoImage"
                v-model:cover-header="courseName"
                v-model:cover-subheader="courseCode"

                :model="'Course'"
                :modelTable="'courses'"
                :modelableId="props.course.data.id"
                :modelableType="'app/models/Course'"
                :modelableRoute="`/courses/${props.course.data.id}`"
                :modelData="props.course.data"

                :subModelNameTh="'บทเรียน'"

                :courseMemberOfAuth="props.courseMemberOfAuth"

                @update:coverImage="handleCoverUpdate"
                @update:logoImage="handleLogoUpdate"
                @header-change="handleHeaderChange"
                @subheader-change="handleSubheaderChange"
                @request-tobe-member="handleRequestToBeMember"
                @request-tobe-unmember="handleRequestToBeUnMember"
            />

            <SingleCoursePageNavbarTab
                :course="course"
                :courseMemberOfAuth="courseMemberOfAuth"
                :isCourseAdmin="isCourseAdmin"
                :activeTab="activeTab"
            />
        </template>

        <!-- Left Sidebar Widget -->
        <template #leftSideWidget>
            <div class="space-y-4">
                <MyMembershipWidget
                    :course="course"
                    :courseMemberOfAuth="courseMemberOfAuth"
                    :isCourseAdmin="isCourseAdmin"
                />
                <CourseQuickNav
                    :course="course"
                    :isCourseAdmin="isCourseAdmin"
                    :courseMemberOfAuth="courseMemberOfAuth"
                    :activeTab="activeTab"
                />
                <CourseStatsWidget
                    :course="course"
                    :isCourseAdmin="isCourseAdmin"
                />
            </div>
        </template>

        <template #mainContent>
            <div>
                <div v-if="$slots.courseContent">
                    <slot name="courseContent"></slot>
                </div>
            </div>
        </template>

        <!-- Right Sidebar Widget -->
        <template #rightSideWidget>
            <div class="space-y-4">
                <RecentLessonsWidget
                    :course="course"
                    :lessons="lessons?.data || []"
                    :isCourseAdmin="isCourseAdmin"
                />
                <UpcomingAssignmentsWidget
                    :course="course"
                    :isCourseAdmin="isCourseAdmin"
                />
                <CourseMembersWidget
                    :course="course"
                    :members="course?.data?.members || []"
                    :isCourseAdmin="isCourseAdmin"
                />
            </div>
        </template>
    </MainLayout>
</template>
