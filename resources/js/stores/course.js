/**
 * Course Store
 * Central state management for Course SPA
 */
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { courseApi } from '@/services/courseApi';

export const useCourseStore = defineStore('course', () => {
    // ============ State ============
    const course = ref(null);
    const activeTab = ref('feeds');
    const isLoading = ref(false);
    const error = ref(null);

    // Tab Data
    const feeds = ref([]);
    const lessons = ref([]);
    const members = ref([]);
    const groups = ref([]);
    const assignments = ref([]);
    const attendances = ref([]);
    const quizzes = ref([]);
    const progress = ref(null);
    const feedsPagination = ref({ currentPage: 1, lastPage: 1, total: 0 });

    // ============ Getters ============
    const isCourseLoaded = computed(() => !!course.value);
    const isCourseAdmin = computed(() => course.value?.is_admin || false);

    // ============ Actions ============
    
    // Generic fetch helper
    async function fetchData(apiCall, targetRef, options = {}) {
        const { skipIfLoaded = true, transform = (d) => d } = options;
        if (skipIfLoaded && targetRef.value?.length > 0) return;
        
        isLoading.value = true;
        try {
            const response = await apiCall();
            targetRef.value = transform(response.data?.data || response.data || []);
        } catch (err) {
            console.error(err);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchCourse(courseId) {
        isLoading.value = true;
        error.value = null;
        try {
            const response = await courseApi.getCourse(courseId);
            course.value = response.data.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to load course';
            console.error(err);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchFeeds(courseId, page = 1) {
        isLoading.value = true;
        try {
            const response = await courseApi.getFeeds(courseId, page);
            const data = response.data?.data || response.data?.activities || [];
            
            feeds.value = page === 1 ? data : [...feeds.value, ...data];
            
            if (response.data?.meta) {
                feedsPagination.value = {
                    currentPage: response.data.meta.current_page || 1,
                    lastPage: response.data.meta.last_page || 1,
                    total: response.data.meta.total || 0
                };
            }
        } catch (err) {
            console.error(err);
        } finally {
            isLoading.value = false;
        }
    }

    const fetchLessons = (id) => fetchData(() => courseApi.getLessons(id), lessons);
    const fetchMembers = (id) => fetchData(() => courseApi.getMembers(id), members);
    const fetchGroups = (id) => fetchData(() => courseApi.getGroups(id), groups);
    const fetchAssignments = (id) => fetchData(() => courseApi.getAssignments(id), assignments);
    const fetchAttendances = (id) => fetchData(() => courseApi.getAttendances(id), attendances);
    const fetchQuizzes = (id) => fetchData(() => courseApi.getQuizzes(id), quizzes);
    const fetchProgress = (id) => fetchData(() => courseApi.getProgress(id), progress, { skipIfLoaded: false });

    function setActiveTab(tab) {
        activeTab.value = tab;
    }

    function resetState() {
        course.value = null;
        activeTab.value = 'feeds';
        feeds.value = [];
        lessons.value = [];
        members.value = [];
        groups.value = [];
        assignments.value = [];
        attendances.value = [];
        quizzes.value = [];
        progress.value = null;
    }

    return {
        // State
        course, activeTab, isLoading, error,
        feeds, lessons, members, groups, assignments, attendances, quizzes, progress,
        feedsPagination,
        // Getters
        isCourseLoaded, isCourseAdmin,
        // Actions
        fetchCourse, fetchFeeds, fetchLessons, fetchMembers, fetchGroups,
        fetchAssignments, fetchAttendances, fetchQuizzes, fetchProgress,
        setActiveTab, resetState
    };
});
