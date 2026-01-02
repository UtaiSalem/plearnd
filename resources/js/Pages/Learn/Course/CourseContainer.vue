<script setup>
/**
 * CourseContainer - SPA Container for Course Pages
 * Handles tab-based navigation without full page reloads
 */
import { computed, defineAsyncComponent } from 'vue';
import { useCourse } from '@/composables/useCourse';
import { useCourseStore } from '@/stores/course';
import CourseLayout from "@/Layouts/CourseLayout.vue";

// Lazy-loaded Tab Components
const tabs = {
    feeds: defineAsyncComponent(() => import('./tabs/FeedsTab.vue')),
    lessons: defineAsyncComponent(() => import('./tabs/LessonsTab.vue')),
    members: defineAsyncComponent(() => import('./tabs/MembersTab.vue')),
    assignments: defineAsyncComponent(() => import('./tabs/AssignmentsTab.vue')),
    attendances: defineAsyncComponent(() => import('./tabs/AttendancesTab.vue')),
    quizzes: defineAsyncComponent(() => import('./tabs/QuizzesTab.vue')),
    groups: defineAsyncComponent(() => import('./tabs/GroupsTab.vue')),
    progress: defineAsyncComponent(() => import('./tabs/ProgressTab.vue')),
    settings: defineAsyncComponent(() => import('./tabs/SettingsTab.vue')),
    'basic-info': defineAsyncComponent(() => import('./tabs/BasicInfoTab.vue')),
};

// Tab Index Mapping (for CourseLayout compatibility)
const TAB_MAP = {
    lessons: 1, assignments: 2, quizzes: 3, members: 4, groups: 5,
    'member-requesters': 6, attendances: 7, settings: 8, 
    'member-settings': 9, progress: 10, feeds: 11, 'basic-info': 12
};
const INDEX_TO_TAB = Object.fromEntries(Object.entries(TAB_MAP).map(([k, v]) => [v, k]));

const props = defineProps({
    courseId: [String, Number],
    initialCourse: Object,
    initialTab: { type: String, default: 'feeds' }
});

const store = useCourseStore();

// Initialize store with server-side data
if (props.initialCourse && !store.course) {
    store.course = props.initialCourse;
}

const { course, activeTab, isLoading, setActiveTab } = useCourse(props.courseId || props.initialCourse?.id);

if (props.initialTab) setActiveTab(props.initialTab);

const currentTabComponent = computed(() => tabs[activeTab.value] || tabs.feeds);
const activeTabIndex = computed(() => TAB_MAP[activeTab.value] || 11);
const handleTabChange = (index) => INDEX_TO_TAB[index] && setActiveTab(INDEX_TO_TAB[index]);
</script>

<template>
    <CourseLayout
        v-if="course"
        :course="{ data: course }"
        :isCourseAdmin="course.is_admin"
        :courseMemberOfAuth="course.auth_member"
        :activeTab="activeTabIndex"
        :isSpa="true"
        @tab-change="handleTabChange"
    >
        <template #courseContent>
            <KeepAlive>
                <component :is="currentTabComponent" :course="course" :key="activeTab" />
            </KeepAlive>
        </template>
    </CourseLayout>
    
    <div v-else-if="isLoading" class="flex justify-center items-center min-h-screen">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
    </div>
</template>
