<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

import CoursePagePostSettingMenuItem from '@/PlearndComponents/learn/courses/posts/CoursePagePostSettingMenuItem.vue';
import CoursePostContentViewer from '@/PlearndComponents/learn/courses/posts/CoursePostContentViewer.vue';
import LazyCoursePostImagesViewer from '@/PlearndComponents/learn/courses/posts/LazyCoursePostImagesViewer.vue';
import LazyCoursePostReactionViewer from '@/PlearndComponents/learn/courses/posts/LazyCoursePostReactionViewer.vue';
import LoadingPage from '@/PlearndComponents/accessories/LoadingPage.vue';

const props = defineProps({
    postKey: String,
    postIndex: Number,
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
const postSettingMenus = reactive([
    {
        name: 'ดูโพสต์',
        icon: 'heroicons-outline:book-open',
        action: 'view',
        url: props.activity.target_resource.post_url,
        isAuthor: isPostOwner.value,
        color: 'text-blue-500',
        showMenu: true
    },
    {
        name: 'แก้ไขโพสต์',
        icon: 'bi:pencil-square',
        action: 'edit',
        url: props.activity.target_resource.post_url + '/edit',
        isAuthor: isPostOwner.value || usePage().props.isCourseAdmin,
        color: 'yellow-400',
        showMenu: isPostOwner.value || usePage().props.isCourseAdmin
    },
    {
        name: 'ลบโพสต์', 
        icon: 'fa-solid:trash-alt', 
        action: 'delete', 
        url: props.activity.target_resource.post_url, 
        isAuthor: isPostOwner.value || usePage().props.isCourseAdmin, 
        color: 'red-500'
    },
    { 
        name: 'แจ้งลบโพสต์', 
        icon: 'bi:exclamation-triangle', 
        action: 'report', 
        url: props.activity.target_resource.post_url, 
        isAuthor: isPostOwner.value, 
        color: 'red-500'
    },
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
                let delResp = await axios.delete(props.activity.target_resource.post_url);
                if (delResp.data.success) {
                    Swal.fire(
                        'ลบโพสต์สำเร็จ!',
                        'โพสต์นี้ของคุณและทุกอย่างที่เกี่ยวข้องถูกลบออกแล้ว',
                        'success'
                    );
                    emit('delete-post');
                } else {
                    Swal.fire(
                        'ลบโพสต์ไม่สำเร็จ!',
                        'โพสต์นี้ของคุณไม่สามารถลบได้ในขณะนี้ กรุณาลองใหม่อีกครั้งในภายหลัง',
                        'error'
                    );
                }
            } catch (error) {
                console.error('Error deleting post:', error);
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
        const response = await axios.get(`/api/courses/${props.activity.target_resource.course_id}/posts/${props.activity.target_resource.id}/images`);
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
        const response = await axios.get(`/api/courses/${props.activity.target_resource.course_id}/posts/${props.activity.target_resource.id}/reactions`);
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
                <CoursePagePostSettingMenuItem
                    :activity="activity"
                    :postSettingMenus="postSettingMenus"
                    @delete-post="handleDeletePostRequest" 
                />
            </div>  

            <LoadingPage v-if="isDeleting" />

            <!-- User info section -->
            <div v-if="!isPostCreation">
                <div class="px-4 pt-4 flex justify-between">
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
                    <CoursePostContentViewer
                        :post="activity.target_resource" 
                        :action_to_id="activity.action_to_id"
                        :actorId="activity.action_by.id"
                        :acting="activity.action" 
                    />
                </div>
                
                <!-- Lazy loaded images section -->
                <div ref="imagesContainerRef" class="mt-4">
                    <LazyCoursePostImagesViewer 
                        v-if="postImages.length > 0 || isLoadingImages || activity.target_resource.images_count > 0"
                        :images="postImages" 
                        :model_id="activity.target_resource.id"
                        :model="activity.target_resource"
                        :edit="activity.target_resource.author.id === $page.props.auth.user.id"
                        :isLoading="isLoadingImages"
                        :imagesCount="activity.target_resource.images_count"
                    />
                </div>

                <!-- Lazy loaded reactions section -->
                <div ref="reactionsContainerRef" class="mt-4">
                    <LazyCoursePostReactionViewer 
                        v-if="postReactions !== null || isLoadingReactions || activity.target_resource.reactions_count > 0"
                        :post="activity.target_resource" 
                        :reactions="postReactions"
                        :model="activity.action_to.toLowerCase()"                
                        :isLoading="isLoadingReactions"
                        :reactionsCount="activity.target_resource.reactions_count"
                    />
                </div>
            </div>
        </div>
    </div>
</template>