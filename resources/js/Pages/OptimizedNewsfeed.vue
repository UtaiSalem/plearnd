<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import InfiniteLoading from "v3-infinite-loading";

import Swal from 'sweetalert2';

import CreatePost from '@/PlearndComponents/widgets/CreatePost.vue';
import OptimizedNewsFeedPostsViewer from '@/PlearndComponents/play/posts/OptimizedNewsFeedPostsViewer.vue';
import AcademyPostViewer from '@/PlearndComponents/learn/academies/posts/AcademyPostViewer.vue';
import CoursePostViewer from '@/PlearndComponents/learn/courses/posts/CoursePostViewer.vue';
import ActivitiesLoadingSkeleton from '@/PlearndComponents/accessories/AcademiesLoadingSkeleton.vue';
import PeopleYouMayKnowWidget from '@/PlearndComponents/widgets/PeopleYouMayKnowWidget.vue';
import PendingFriendsWidget from '@/PlearndComponents/widgets/friends/PendingFriendsWidget.vue';
import DonationListWidget from '@/PlearndComponents/widgets/DonationListWidget.vue';
import AdvertiseListWidget from '@/PlearndComponents/widgets/AdvertiseListWidget.vue';
import DonationsViewer from '@/PlearndComponents/earn/donates/DonationsViewer.vue';
import DonateRecipientViewer from '@/PlearndComponents/earn/donates/DonateRecipientViewer.vue';
import LoadingPage from '@/PlearndComponents/accessories/LoadingPage.vue';

const props = defineProps({
    initialActivities: Array,
    initialPagination: Object,
    peopleMayKnow: Object,
    pendingFriends: Object,
    donates: Object,  
    advertises: Object,  
});

// State management
const isLoadingActivities = ref(false);
const isLoading = ref(false);
const currentPage = ref(props.initialPagination.current_page);
const lastPage = ref(props.initialPagination.last_page);
const newsfeedActivities = ref(props.initialActivities);
const isLoadingNewContent = ref(false);

// Visibility API for detecting when user returns to the tab
let visibilityChangeHandler = null;

// Load more activities for infinite scroll
const loadMoreActivities = async ($state) => {
    if (isLoadingActivities.value || currentPage.value >= lastPage.value) {
        $state.complete();
        return;
    }

    try {
        isLoadingActivities.value = true;
        currentPage.value++;
        
        const response = await axios.get(`/api/newsfeed/activities?page=${currentPage.value}`);
        
        if (response.data.success) {
            if (response.data.activities.length > 0) {
                response.data.activities.forEach(activity => {
                    newsfeedActivities.value.push(activity);
                });
                $state.loaded();
            } else {
                $state.complete();
            }
        } else {
            $state.error();
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด! กรุณาลองใหม่อีกครั้ง',
                text: response.data.message,
            });
        }
    } catch (error) {
        console.error('Error loading more activities:', error);
        $state.error();
    } finally {
        isLoadingActivities.value = false;
    }
};

// Refresh activities
const refreshActivities = async () => {
    if (isLoadingNewContent.value) return;
    
    isLoadingNewContent.value = true;
    try {
        const response = await axios.get(`/api/newsfeed/activities?page=1`);
        
        if (response.data.success) {
            newsfeedActivities.value = response.data.activities;
            currentPage.value = 1;
            lastPage.value = response.data.pagination.last_page;
        }
    } catch (error) {
        console.error('Error refreshing activities:', error);
    } finally {
        isLoadingNewContent.value = false;
    }
};

// Handle adding new activity to the top of the feed
const handleAddNewActivity = (activity) => {
    newsfeedActivities.value.unshift(activity);
};

// Handle deleting an activity
const handleDeleteActivity = (index) => {
    newsfeedActivities.value.splice(index, 1);
};

// Handle navigation to other pages
const handleLinkToPage = (href) => {
    isLoading.value = true;
    router.visit(href);
};

// Setup visibility change handler to refresh content when user returns to tab
const setupVisibilityHandler = () => {
    visibilityChangeHandler = () => {
        if (!document.hidden && newsfeedActivities.value.length > 0) {
            // Only refresh if content might be stale (more than 5 minutes old)
            const lastActivityTime = new Date(newsfeedActivities.value[0].created_at);
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
    setupVisibilityHandler();
});

onUnmounted(() => {
    if (visibilityChangeHandler) {
        document.removeEventListener('visibilitychange', visibilityChangeHandler);
    }
});
</script>

<template>
    <div class="">
        <MainLayout title="Newsfeed">
            <template #coverProfileCard>
                <div class="hidden md:flex items-center mx-auto mt-2 mb-4 shadow-lg bg-[url('/storage/images/banner/banner-bg.png')] bg-cover bg-no-repeat rounded-lg">
                    <img class="section-banner-icon" :src="'/storage/images/banner/forums-icon.png'" alt="forums-icon">
                    <p class="text-white font-bold text-xl">กระดานข่าว</p>
                </div>
            </template>

            <template #leftSideWidget>
                <!-- Advertisement List -->
                <AdvertiseListWidget v-if="props.advertises.data.length" :advertises="advertises" />
                
                <hr v-if="props.advertises.data.length" class="my-2 border-t-1 border-gray-400"/>

                <!-- Donation List -->
                <DonationListWidget 
                    v-if="donates.data.length"
                    :donates="donates"
                    @add-new-donate-activity="handleAddNewActivity"
                    @go-to-create-donate="handleLinkToPage('/supports/donates/create')"
                />
            </template>

            <template #rightSideWidget>
                <!-- People you may know -->
                <PeopleYouMayKnowWidget :peopleMayKnow="peopleMayKnow" />
                <div class="my-4"></div>
                <!-- Pending friend requests -->
                <PendingFriendsWidget 
                    v-if="props.pendingFriends.data.length" 
                    :pendingFriends="props.pendingFriends" 
                />
            </template>

            <template #mainContent>
                <div>
                    <LoadingPage v-if="isLoading" />
                    
                    <!-- Refresh button -->
                    <div class="flex justify-end mb-4">
                        <button 
                            @click="refreshActivities"
                            :disabled="isLoadingNewContent"
                            class="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors duration-200"
                        >
                            <svg v-if="isLoadingNewContent" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V4a10 10 0 00-10 10h2zm2 8a8 8 0 018-8h2a10 10 0 00-10-10v2zm8 2a8 8 0 01-8-8h-2a10 10 0 0010 10v-2zm-8-2a8 8 0 01-8 8V20a10 10 0 0010-10h-2z"></path>
                            </svg>
                            <svg v-else class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>รีเฟรช</span>
                        </button>
                    </div>

                    <!-- Create Post -->
                    <CreatePost @add-new-post-activity="handleAddNewActivity"/>

                    <!-- Activities Feed -->
                    <div class="space-y-4">
                        <div v-for="(activity, index) in newsfeedActivities" :key="activity.id">
                            <!-- Post activities -->
                            <div v-if="activity.action_to === 'Post'">
                                <OptimizedNewsFeedPostsViewer 
                                    :activity="activity"
                                    @delete-post="handleDeleteActivity(index)" 
                                />
                            </div>

                            <!-- Academy Post activities -->
                            <div v-else-if="activity.action_to === 'AcademyPost'">
                                <AcademyPostViewer 
                                    :activity="activity" 
                                    @delete-post="handleDeleteActivity(index)" 
                                />
                            </div>

                            <!-- Course Post activities -->
                            <div v-else-if="activity.action_to === 'CoursePost'">
                                <CoursePostViewer 
                                    :activity="activity" 
                                    @delete-post="handleDeleteActivity(index)" 
                                />
                            </div>

                            <!-- Donation activities -->
                            <div v-else-if="activity.action_to === 'Donate'">
                                <DonationsViewer :activity="activity" />
                            </div>

                            <!-- Donation Recipient activities -->
                            <div v-else-if="activity.action_to === 'DonateRecipient'">
                                <DonateRecipientViewer :activity="activity" />
                            </div>

                            <!-- Poll activities -->
                            <div v-else-if="activity.action_to === 'Poll'">
                                'Poll Viewer'
                            </div>
                        </div>

                        <!-- Loading skeleton for more activities -->
                        <div v-if="isLoadingActivities" class="mt-4">
                            <ActivitiesLoadingSkeleton />
                        </div>
                    </div>

                    <!-- Infinite scroll loader -->
                    <div v-if="initialPagination.total > 0" class="mt-8">
                        <InfiniteLoading @distance="1" @infinite="loadMoreActivities">
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
                                    <button @click="loadMoreActivities" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                        โหลดซ้ำ
                                    </button>
                                </div>
                            </template>
                        </InfiniteLoading>
                    </div>
                </div>
            </template>
        </MainLayout>
    </div>
</template>