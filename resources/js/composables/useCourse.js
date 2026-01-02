/**
 * Course Composable
 * Provides reactive course data and handles lazy loading
 */
import { storeToRefs } from 'pinia';
import { useCourseStore } from '@/stores/course';
import { watch } from 'vue';

export function useCourse(courseId) {
    const store = useCourseStore();
    const { course, activeTab, isLoading, error, feeds, lessons, members, 
            assignments, attendances, quizzes, progress, groups } = storeToRefs(store);

    // Initialize if needed
    if (courseId && (!course.value || course.value.id != courseId)) {
        store.resetState();
        store.fetchCourse(courseId);
    }

    // Tab data fetching map
    const tabFetchers = {
        feeds: () => !feeds.value?.length && store.fetchFeeds(course.value?.id),
        lessons: () => store.fetchLessons(course.value?.id),
        members: () => store.fetchMembers(course.value?.id),
        assignments: () => store.fetchAssignments(course.value?.id),
        attendances: async () => {
            // โหลด groups ก่อนถ้ายังไม่มี (จาก store หรือ course.groups)
            const hasGroups = groups.value?.length || course.value?.groups?.length;
            if (!hasGroups) {
                await store.fetchGroups(course.value?.id);
            }
            return store.fetchAttendances(course.value?.id);
        },
        quizzes: () => store.fetchQuizzes(course.value?.id),
        progress: () => store.fetchProgress(course.value?.id),
        groups: () => store.fetchGroups(course.value?.id),
    };

    // Watch tab changes and fetch data
    watch(activeTab, (tab) => {
        if (course.value?.id && tabFetchers[tab]) {
            tabFetchers[tab]();
        }
    }, { immediate: true });

    return {
        course, activeTab, isLoading, error, groups,
        setActiveTab: store.setActiveTab,
        fetchFeeds: store.fetchFeeds
    };
}
