<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import InfiniteLoading from "v3-infinite-loading";

import CourseLayout from "@/Layouts/CourseLayout.vue";
import CreateCoursePost from "@/PlearndComponents/learn/courses/posts/CreateCoursePost.vue";
import OptimizedCoursePostViewer from '@/PlearndComponents/learn/courses/posts/OptimizedCoursePostViewer.vue';
import PostLoadingSkeleton from '@/PlearndComponents/accessories/PostLoadingSkeleton.vue';
import LoadingPage from '@/PlearndComponents/accessories/LoadingPage.vue';

const props = defineProps({
    academy: Object,
    course: Object,
    courseMemberOfAuth: Object,
    isCourseAdmin: Boolean,
    initialActivities: Array,
    initialPagination: Object,
});

// State management
const isDarkMode = ref(false);
const isMobile = ref(false);
const isLoading = ref(false);
const isLoadingCoursePosts = ref(false);
const courseActivities = ref(props.initialActivities);
const pagination = ref(props.initialPagination);

// Intersection Observer for infinite scroll
let observer = null;
const loadMoreTrigger = ref(null);

// Visibility API for detecting when user returns to the tab
let visibilityChangeHandler = null;

// Methods
const fetchMorePosts = async () => {
    if (isLoadingCoursePosts.value || pagination.value.current_page >= pagination.value.last_page) {
        return;
    }

    try {
        isLoadingCoursePosts.value = true;
        
        const response = await axios.get(`/api/courses/${props.course.id}/feeds/paginated`, {
            params: {
                page: pagination.value.current_page + 1,
                per_page: 5
            }
        });
        
        if (response.data.success) {
            response.data.activities.forEach(activity => {
                courseActivities.value.push(activity);
            });
            
            pagination.value = response.data.pagination;
        }
    } catch (error) {
        console.error('Error fetching more posts:', error);
    } finally {
        isLoadingCoursePosts.value = false;
    }
};

const handleAddNewActivity = (newActivity) => {
    courseActivities.value = [newActivity, ...courseActivities.value];
};

// Refresh activities
const refreshActivities = async () => {
    if (isLoading.value) return;
    
    isLoading.value = true;
    try {
        const response = await axios.get(`/api/courses/${props.course.id}/feeds/paginated?page=1`);
        
        if (response.data.success) {
            courseActivities.value = response.data.activities;
            pagination.value = response.data.pagination;
        }
    } catch (error) {
        console.error('Error refreshing activities:', error);
    } finally {
        isLoading.value = false;
    }
};

// Setup intersection observer for infinite scroll
const setupIntersectionObserver = () => {
    if (!loadMoreTrigger.value || !('IntersectionObserver' in window)) {
        return;
    }

    observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !isLoadingCoursePosts.value) {
                fetchMorePosts();
            }
        });
    }, { 
        threshold: 0.1,
        rootMargin: '100px' // Start loading 100px before the trigger is in view
    });

    observer.observe(loadMoreTrigger.value);
};

// Setup visibility change handler to refresh content when user returns to tab
const setupVisibilityHandler = () => {
    visibilityChangeHandler = () => {
        if (!document.hidden && courseActivities.value.length > 0) {
            // Only refresh if content might be stale (more than 5 minutes old)
            const lastActivityTime = new Date(courseActivities.value[0].created_at);
            const now = new Date();
            const diffInMinutes = (now - lastActivityTime) / (1000 * 60);
            
            if (diffInMinutes > 5) {
                refreshActivities();
            }
        }
    };
    
    document.addEventListener('visibilitychange', visibilityChangeHandler);
};

// Cleanup
onMounted(() => {
    if ('ontouchstart' in window) { 
        isMobile.value = true; 
    }
    
    // Setup intersection observer
    setupIntersectionObserver();
    
    // Setup visibility handler
    setupVisibilityHandler();
});

onUnmounted(() => {
    if (observer) {
        observer.disconnect();
    }
    
    if (visibilityChangeHandler) {
        document.removeEventListener('visibilitychange', visibilityChangeHandler);
    }
});
</script>

<template>
    <CourseLayout
        :course
        :isCourseAdmin
        :courseMemberOfAuth
        :activeTab="11"
    >
        <template #courseContent>
            <div class="mt-4">
                <!-- Refresh button -->
                <div class="flex justify-end mb-4">
                    <button 
                        @click="refreshActivities"
                        :disabled="isLoading"
                        class="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors duration-200"
                    >
                        <svg v-if="isLoading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V4a10 10 0 00-10 10h2zm2 8a8 8 0 018-8h2a10 10 0 00-10-10v2zm8 2a8 8 0 01-8-8h-2a10 10 0 0010 10v-2zm-8-2a8 8 0 01-8 8V20a10 10 0 0010-10h-2z"></path>
                        </svg>
                        <svg v-else class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>รีเฟรช</span>
                    </button>
                </div>
                
                <!-- Loading overlay -->
                <LoadingPage v-if="isLoading" />
                
                <!-- Create Course Post -->
                <CreateCoursePost v-if="courseMemberOfAuth" 
                    :course_id="props.course.data.id" 
                    @add-new-post="handleAddNewActivity"
                />

                <!-- Course Activities Feed -->
                <div class="space-y-4">
                    <div v-for="(activity, index) in courseActivities" :key="activity.id">
                        <OptimizedCoursePostViewer 
                            :postKey="`post-${activity.id}`"
                            :postIndex="index"
                            :activity="activity"
                            @delete-post="courseActivities.splice(index, 1)"
                        />
                    </div>
                </div>

                <!-- Loading skeleton for more posts -->
                <div v-if="isLoadingCoursePosts" class="mt-4">
                    <PostLoadingSkeleton />
                </div>
                
                <!-- Infinite scroll trigger -->
                <div v-if="pagination.current_page < pagination.last_page" 
                    ref="loadMoreTrigger" 
                    class="flex justify-center py-4">
                    <div v-if="isLoadingCoursePosts" class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>
                
                <!-- Infinite scroll component -->
                <InfiniteLoading @distance="1" @infinite="fetchMorePosts" v-if="pagination.total > 0">
                    <template #spinner>
                        <div class="flex justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        </div>
                    </template>
                    <template #complete>
                        <div class="text-center text-gray-500 mt-4">
                            <p>ไม่มีโพสต์เพิ่มเติม</p>
                        </div>
                    </template>
                    <template #error>
                        <div class="text-center">
                            <button @click="fetchMorePosts" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                โหลดซ้ำ
                            </button>
                        </div>
                    </template>
                </InfiniteLoading>
            </div>
        </template>
    </CourseLayout> 
</template>