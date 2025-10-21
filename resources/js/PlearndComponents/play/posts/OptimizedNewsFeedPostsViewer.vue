<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

import PostContentViewer from './PostViewerPartials/PostContentViewer.vue';
import LazyPostImagesViewer from './PostViewerPartials/LazyPostImagesViewer.vue';
import LazyPostReactionViewer from './PostViewerPartials/LazyPostReactionViewer.vue';
import NewsFeedPagePostSettingMenuItem from './PostViewerPartials/PostMenuItems/NewsFeedPagePostSettingMenuItem.vue';
import LoadingPage from '@/PlearndComponents/accessories/LoadingPage.vue';

const props = defineProps({
    activity: Object,
});

const emit = defineEmits(['delete-post']);

// State management
const isPostCreation = computed(() => props.activity.action === "createpost");
const isPostOwner = computed(() => props.activity.action_by.id === usePage().props.auth.user.id);
const isDeleting = ref(false);
const isLoadingImages = ref(false);
const isLoadingReactions = ref(false);

// Lazy loaded data
const postImages = ref([]);
const postReactions = ref(null);

// Intersection Observer for lazy loading
let imagesObserver = null;
let reactionsObserver = null;
const imagesContainerRef = ref(null);
const reactionsContainerRef = ref(null);

// Post setting menus
const postSettingMenus = ref([
    {   
        name: 'ดูโพสต์', 
        icon: 'heroicons-outline:book-open', 
        action: 'view', 
        url: props.activity.target_resource.post_url, 
        isAuthor: isPostOwner, 
        color: 'text-blue-500', 
        showMenu: isPostOwner
    },
    { name: 'แก้ไขโพสต์', icon: 'bi:pencil-square', action: 'edit', url: props.activity.target_resource.post_url+'/edit', isAuthor: isPostOwner, color: 'yellow-400'},
    { name: 'ลบโพสต์', icon: 'fa-solid:trash-alt', action: 'delete', url: props.activity.target_resource.post_url, isAuthor: isPostOwner, color: 'red-500'},
    { name: 'แจ้งลบโพสต์', icon: 'bi:exclamation-triangle', action: 'report', url: props.activity.target_resource.post_url, isAuthor: isPostOwner, color: 'red-500'},
]);

// Handle delete post request
const handleDeletePostRequest = () => {
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่ที่จะลบโพสต์นี้และทุกอย่างที่เกี่ยวข้องกับโพสต์นี้?',
        text: "คุณจะไม่สามารถกู้คืนข้อมูลที่ลบไปแล้วได้!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, ยืนยันการลบ!',
        cancelButtonText: 'ยกเลิก'
    }).then(async (result) => {
        if (result.isConfirmed) {
            isDeleting.value = true;
            try {
                let delResp = await axios.delete(`/posts/${props.activity.target_resource.id}`);
                if (delResp.data.success) {
                    Swal.fire(
                        'ลบโพสต์สำเร็จ!',
                        'โพสต์นี้ของคุณและทุกอย่างที่เกี่ยวข้องถูกลบออกแล้ว',
                        'success'
                    );
                    emit('delete-post');
                }
            } catch (error) {
                Swal.fire(
                    'เกิดข้อผิดพลาด!',
                    'ไม่สามารถลบโพสต์ได้ กรุณาลองใหม่อีกครั้ง',
                    'error'
                );
            } finally {
                isDeleting.value = false;
            }
        }
    });
};

// Lazy load post images
const loadPostImages = async () => {
    if (isLoadingImages.value || postImages.value.length > 0) return;
    
    isLoadingImages.value = true;
    try {
        const response = await axios.get(`/api/posts/${props.activity.target_resource.id}/images`);
        if (response.data.success) {
            postImages.value = response.data.images;
        }
    } catch (error) {
        console.error('Error loading post images:', error);
    } finally {
        isLoadingImages.value = false;
    }
};

// Lazy load post reactions
const loadPostReactions = async () => {
    if (isLoadingReactions.value || postReactions.value !== null) return;
    
    isLoadingReactions.value = true;
    try {
        const response = await axios.get(`/api/posts/${props.activity.target_resource.id}/reactions`);
        if (response.data.success) {
            postReactions.value = response.data.reactions;
        }
    } catch (error) {
        console.error('Error loading post reactions:', error);
    } finally {
        isLoadingReactions.value = false;
    }
};

// Setup intersection observers for lazy loading
const setupIntersectionObservers = () => {
    // Images observer
    if (imagesContainerRef.value && 'IntersectionObserver' in window) {
        imagesObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    loadPostImages();
                    imagesObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        imagesObserver.observe(imagesContainerRef.value);
    }
    
    // Reactions observer
    if (reactionsContainerRef.value && 'IntersectionObserver' in window) {
        reactionsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    loadPostReactions();
                    reactionsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        reactionsObserver.observe(reactionsContainerRef.value);
    }
};

// Cleanup observers
const cleanupObservers = () => {
    if (imagesObserver) {
        imagesObserver.disconnect();
    }
    if (reactionsObserver) {
        reactionsObserver.disconnect();
    }
};

onMounted(() => {
    setupIntersectionObservers();
});

onUnmounted(() => {
    cleanupObservers();
});
</script>

<template>
    <div class="my-4">
        <div class="bg-white shadow-lg rounded-lg dark:bg-dark-card py-2 relative" v-if="activity">
            <div class="absolute flex w-full justify-end pr-4 pt-2">
                <NewsFeedPagePostSettingMenuItem
                    :activity="activity"
                    :postSettingMenus="postSettingMenus"
                    @delete-post="handleDeletePostRequest"
                />
            </div>

            <LoadingPage v-if="isDeleting" />

            <!-- User info section -->
            <div v-if="!isPostCreation" class="px-4 pt-4 flex justify-between">
                <div>
                    <div class="flex justify-center gap-4">
                        <img 
                            :src="activity.action_by.avatar" 
                            class="w-12 h-12 p-[3px] rounded-full ring-1 ring-blue-600 dark:ring-gray-500" 
                            alt=""
                        >
                        <div>
                            <div class="flex items-center gap-4 mb-2">
                                <h6 class="">{{ activity.action_by.name }}</h6>
                                <small class="text-gray-800 mt-1 dark:text-secondary-600">
                                    {{ activity.action }}
                                </small>
                                <small class="text-gray-800 mt-1 dark:text-secondary-600">
                                    {{ activity.action_to }}
                                </small>
                            </div>
                            <p class="text-gray-500 text-xs">{{ activity.diff_humans_created_at }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Post content section -->
            <div :class="!isPostCreation ? 'm-2 border-[1.5px] border-gray-200 rounded-lg':''" class="px-4">
                <div class="text-gray-700 text-base rounded-lg">
                    <PostContentViewer 
                        :post="activity.target_resource" 
                        :action_to_id="activity.action_to_id"
                        :actorId="activity.action_by.id"
                        :acting="activity.action" 
                    />
                </div>
                
                <!-- Lazy loaded images section -->
                <div ref="imagesContainerRef" class="mt-4">
                    <LazyPostImagesViewer 
                        v-if="postImages.length > 0 || isLoadingImages"
                        :images="postImages" 
                        :model_id="activity.target_resource.id" 
                        :edit="activity.target_resource.author.id === $page.props.auth.user.id"
                        :isLoading="isLoadingImages"
                    />
                </div>

                <!-- Lazy loaded reactions section -->
                <div ref="reactionsContainerRef" class="mt-4">
                    <LazyPostReactionViewer 
                        v-if="postReactions !== null || isLoadingReactions"
                        :post="activity.target_resource" 
                        :reactions="postReactions"
                        :model="activity.action_to.toLowerCase()"                
                        :isLoading="isLoadingReactions"
                    />
                </div>
            </div>
        </div>
    </div>
</template>