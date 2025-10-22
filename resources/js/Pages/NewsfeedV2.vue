<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import InfiniteLoading from "v3-infinite-loading";
import Swal from 'sweetalert2';

import CreatePost from '@/PlearndComponents/widgets/CreatePost.vue';
import NewsFeedPostsViewer from '@/PlearndComponents/play/posts/NewsFeedPostsViewer.vue';
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

// New components for enhanced features
import NewsfeedFilter from '@/PlearndComponents/newsfeed/NewsfeedFilter.vue';
import NewsfeedSearch from '@/PlearndComponents/newsfeed/NewsfeedSearch.vue';
import NewsfeedNotification from '@/PlearndComponents/newsfeed/NewsfeedNotification.vue';
import NewsfeedPostCard from '@/PlearndComponents/newsfeed/NewsfeedPostCard.vue';

const props = defineProps({
    peopleMayKnow: Object,
    pendingFriends: Object,
    donates: Object,
    advertises: Object,
    notifications: Array,
});

// State management
const isLoadingActivities = ref(false);
const isLoading = ref(false);
const currentPage = ref(0); // Start with 0 since we're not fetching initial data
const lastPage = ref(1);
const newsfeedActivities = ref([]);
const isLoadingNewContent = ref(false);
const isInitialLoad = ref(true); // Mark as initial load to trigger infinite scroll

// New state for enhanced features
const activeFilter = ref('all'); // all, posts, academy, courses, donations
const searchQuery = ref('');
const showNotifications = ref(false);
const notifications = ref(props.notifications || []);
const showBackToTop = ref(false);
const isFilterMenuOpen = ref(false);
const isSearchMenuOpen = ref(false);

// Ensure props have default values to prevent undefined errors
const safeProps = {
    advertises: props.advertises && props.advertises.data ? props.advertises : { data: [] },
    donates: props.donates && props.donates.data ? props.donates : { data: [] },
    pendingFriends: props.pendingFriends && props.pendingFriends.data ? props.pendingFriends : { data: [] },
    peopleMayKnow: props.peopleMayKnow && props.peopleMayKnow.data ? props.peopleMayKnow : { data: [] },
    initialPagination: props.initialPagination || { total: 0, current_page: 1, last_page: 1 }
};

// Computed properties
const filteredActivities = computed(() => {
    if (activeFilter.value === 'all') return newsfeedActivities.value;
    
    return newsfeedActivities.value.filter(activity => {
        switch (activeFilter.value) {
            case 'posts':
                return activity.action_to === 'Post';
            case 'academy':
                return activity.action_to === 'AcademyPost';
            case 'courses':
                return activity.action_to === 'CoursePost';
            case 'donations':
                return activity.action_to === 'Donate' || activity.action_to === 'DonateRecipient';
            default:
                return true;
        }
    });
});

const searchedActivities = computed(() => {
    if (!searchQuery.value.trim()) return filteredActivities.value;
    
    const query = searchQuery.value.toLowerCase();
    return filteredActivities.value.filter(activity => {
        // Search in post content
        if (activity.target_resource?.content) {
            return activity.target_resource.content.toLowerCase().includes(query);
        }
        // Search in user name
        if (activity.action_by?.name) {
            return activity.action_by.name.toLowerCase().includes(query);
        }
        return false;
    });
});

// Debug initial state
console.log('NewsfeedV2 - Initial state', {
    currentPage: currentPage.value,
    lastPage: lastPage.value,
    newsfeedActivitiesCount: newsfeedActivities.value.length,
    activeFilter: activeFilter.value,
    searchQuery: searchQuery.value
});

// Visibility API for detecting when user returns to the tab
let visibilityChangeHandler = null;
let scrollHandler = null;

// Load more activities for infinite scroll
const loadMoreActivities = async ($state) => {
    console.log('loadMoreActivities called', {
        isLoading: isLoadingActivities.value,
        currentPage: currentPage.value,
        lastPage: lastPage.value,
        shouldStop: currentPage.value >= lastPage.value
    });
    
    if (isLoadingActivities.value || (currentPage.value > 0 && currentPage.value >= lastPage.value)) {
        console.log('Stopping infinite scroll - loading or reached last page');
        $state.complete();
        return;
    }

    try {
        isLoadingActivities.value = true;
        currentPage.value++;
        
        console.log('Fetching page', currentPage.value);
        const response = await axios.get(`/api/newsfeed-v2/activities?page=${currentPage.value}&per_page=5`);
        
        console.log('Response received', {
            success: response.data.success,
            activitiesCount: response.data.activities?.length || 0,
            pagination: response.data.pagination
        });
        
        if (response.data.success) {
            // Ensure newsfeedActivities is an array
            if (!Array.isArray(newsfeedActivities.value)) {
                console.log('Converting newsfeedActivities to array');
                newsfeedActivities.value = [];
            }
            
            // Ensure response.data.activities is an array
            const newActivities = Array.isArray(response.data.activities) ? response.data.activities : [];
            
            console.log('Adding activities', {
                currentCount: newsfeedActivities.value.length,
                newCount: newActivities.length
            });
            
            if (newActivities.length > 0) {
                newActivities.forEach(activity => {
                    newsfeedActivities.value.push(activity);
                });
                $state.loaded();
                
                // Update pagination info from response
                if (response.data.pagination) {
                    lastPage.value = response.data.pagination.last_page;
                    console.log('Updated lastPage to', lastPage.value);
                }
            } else {
                console.log('No more activities to load');
                $state.complete();
            }
        } else {
            console.error('API returned error', response.data);
            $state.error();
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด! กรุณาลองใหม่อีกครั้ง',
                text: response.data.message || 'ไม่สามารถโหลดข้อมูลได้',
            });
        }
    } catch (error) {
        console.error('Error loading more activities:', error);
        $state.error();
    } finally {
        isLoadingActivities.value = false;
        isInitialLoad.value = false;
    }
};

// Refresh activities
const refreshActivities = async () => {
    if (isLoadingNewContent.value) return;
    
    console.log('Refreshing activities');
    isLoadingNewContent.value = true;
    try {
        const response = await axios.get(`/api/newsfeed-v2/activities?page=1&per_page=5`);
        
        console.log('Refresh response', {
            success: response.data.success,
            activitiesCount: response.data.activities?.length || 0,
            pagination: response.data.pagination
        });
        
        if (response.data.success) {
            // Ensure response.data.activities is an array
            newsfeedActivities.value = Array.isArray(response.data.activities) ? response.data.activities : [];
            currentPage.value = 1;
            if (response.data.pagination) {
                lastPage.value = response.data.pagination.last_page;
            }
            console.log('Refresh complete', {
                newCount: newsfeedActivities.value.length,
                currentPage: currentPage.value,
                lastPage: lastPage.value
            });
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

// Handle filter change
const handleFilterChange = (filter) => {
    activeFilter.value = filter;
    isFilterMenuOpen.value = false;
};

// Handle search
const handleSearch = (query) => {
    searchQuery.value = query;
    isSearchMenuOpen.value = false;
};

// Toggle filter menu
const toggleFilterMenu = () => {
    isFilterMenuOpen.value = !isFilterMenuOpen.value;
    isSearchMenuOpen.value = false;
};

// Toggle search menu
const toggleSearchMenu = () => {
    isSearchMenuOpen.value = !isSearchMenuOpen.value;
    isFilterMenuOpen.value = false;
};

// Scroll to top
const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
};

// Handle scroll events
const handleScroll = () => {
    showBackToTop.value = window.pageYOffset > 300;
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

// Setup scroll handler
const setupScrollHandler = () => {
    scrollHandler = handleScroll;
    window.addEventListener('scroll', scrollHandler);
};

// Setup visibility and scroll handlers on mount
onMounted(() => {
    setupVisibilityHandler();
    setupScrollHandler();
});

// Cleanup
onUnmounted(() => {
    if (visibilityChangeHandler) {
        document.removeEventListener('visibilitychange', visibilityChangeHandler);
    }
    if (scrollHandler) {
        window.removeEventListener('scroll', scrollHandler);
    }
});

</script>

<template>
    <div class="">
        <MainLayout title="Newsfeed V2">
            <template #coverProfileCard>
                <div class="hidden md:flex items-center mx-auto mt-2 mb-4 shadow-lg bg-[url('/storage/images/banner/banner-bg.png')] bg-cover bg-no-repeat rounded-lg">
                    <img class="section-banner-icon " :src="'/storage/images/banner/forums-icon.png'" alt="forums-icon">
                    <p class="text-white font-bold text-xl">กระดานข่าว V2</p>
                </div>
            </template>

            <template #leftSideWidget>
                <!-- Donation List -->
                <AdvertiseListWidget v-if="safeProps.advertises.data.length" :advertises="safeProps.advertises" />
                
                <hr v-if="safeProps.advertises.data.length" class="my-2 border-t-1 border-gray-400"/>

                <DonationListWidget
                    v-if="safeProps.donates.data.length"
                    :donates="safeProps.donates"
                    @add-new-donate-activity="handleAddNewActivity"
                    @go-to-create-donate="handleLinkToPage('/supports/donates/create')"
                />
            </template>

            <template #rightSideWidget >
                <!-- User who is maybe friend -->
                <PeopleYouMayKnowWidget :peopleMayKnow="safeProps.peopleMayKnow" />
                <div class="my-4"></div>
                <PendingFriendsWidget :pendingFriends="safeProps.pendingFriends" v-if="safeProps.pendingFriends.data.length"/>
            </template>

            <template #mainContent>
                <div>
                    <LoadingPage v-if="isLoading" />
                    
                    <!-- Enhanced header with filter and search -->
                    <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                            <!-- Filter and Search buttons -->
                            <div class="flex items-center gap-2 w-full md:w-auto">
                                <!-- Notifications Button -->
                                <button
                                    @click="showNotifications = !showNotifications"
                                    class="relative flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors duration-200"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                    </svg>
                                    <span>การแจ้งเตือน</span>
                                    <span v-if="notifications.filter(n => !n.read).length > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                        {{ notifications.filter(n => !n.read).length }}
                                    </span>
                                </button>
                                
                                <button
                                    @click="toggleFilterMenu"
                                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                                    </svg>
                                    <span>ฟิลเตอร์</span>
                                    <span v-if="activeFilter !== 'all'" class="bg-blue-800 text-xs px-2 py-1 rounded-full">
                                        {{ activeFilter === 'posts' ? 'โพสต์' :
                                           activeFilter === 'academy' ? 'แหล่งเรียนรู้' :
                                           activeFilter === 'courses' ? 'รายวิชา' : 'การบริจาค' }}
                                    </span>
                                </button>
                                
                                <button
                                    @click="toggleSearchMenu"
                                    class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                    <span>ค้นหา</span>
                                </button>
                            </div>
                            
                            <!-- Refresh button -->
                            <button
                                @click="refreshActivities"
                                :disabled="isLoadingNewContent"
                                class="flex items-center space-x-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 transition-colors duration-200"
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
                        
                        <!-- Filter Menu -->
                        <NewsfeedFilter 
                            v-if="isFilterMenuOpen"
                            :activeFilter="activeFilter"
                            @filter-change="handleFilterChange"
                            @close="isFilterMenuOpen = false"
                        />
                        
                        <!-- Search Menu -->
                        <NewsfeedSearch 
                            v-if="isSearchMenuOpen"
                            :searchQuery="searchQuery"
                            @search="handleSearch"
                            @close="isSearchMenuOpen = false"
                        />
                    </div>

                    <!-- Create Post -->
                    <CreatePost @add-new-post-activity="handleAddNewActivity"/>

                    <!-- Activities Feed -->
                    <div class="space-y-4">
                        <!-- Show message if no results after filtering/searching -->
                        <div v-if="searchedActivities.length === 0 && (activeFilter !== 'all' || searchQuery)" class="text-center py-8 bg-white rounded-lg shadow">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-gray-500">
                                {{ searchQuery ? `ไม่พบผลลัพธ์สำหรับ "${searchQuery}"` : 'ไม่มีโพสต์ในหมวดหมู่นี้' }}
                            </p>
                            <button 
                                @click="() => { activeFilter = 'all'; searchQuery = ''; }"
                                class="mt-2 text-blue-600 hover:text-blue-800"
                            >
                                แสดงทั้งหมด
                            </button>
                        </div>
                        
                        <!-- Display filtered/searched activities -->
                        <div v-for="activity in searchedActivities" :key="activity.id">
                            <!-- Use the new NewsfeedPostCard component -->
                            <NewsfeedPostCard
                                :activity="activity"
                                :index="newsfeedActivities.findIndex(a => a.id === activity.id)"
                                @delete-post="handleDeleteActivity"
                            />
                        </div>

                        <!-- Loading skeleton for more activities -->
                        <div v-if="isLoadingActivities" class="mt-4">
                            <ActivitiesLoadingSkeleton />
                        </div>
                    </div>

                    <!-- Infinite scroll loader -->
                    <div class="mt-8">
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
                
                <!-- Back to top button -->
                <button
                    v-if="showBackToTop"
                    @click="scrollToTop"
                    class="fixed bottom-8 right-8 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-all duration-300 z-40"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                </button>
                
                <!-- Notifications Panel -->
                <NewsfeedNotification
                    :show="showNotifications"
                    @close="showNotifications = false"
                />
            </template>
        </MainLayout>
    </div>
</template>