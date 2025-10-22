<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

// Import existing viewers
import NewsFeedPostsViewer from '@/PlearndComponents/play/posts/NewsFeedPostsViewer.vue';
import AcademyPostViewer from '@/PlearndComponents/learn/academies/posts/AcademyPostViewer.vue';
import CoursePostViewer from '@/PlearndComponents/learn/courses/posts/CoursePostViewer.vue';
import DonationsViewer from '@/PlearndComponents/earn/donates/DonationsViewer.vue';
import DonateRecipientViewer from '@/PlearndComponents/earn/donates/DonateRecipientViewer.vue';

const props = defineProps({
    activity: Object,
    index: Number,
});

const emit = defineEmits(['delete-post']);

// State for card interactions
const isExpanded = ref(false);
const showFullContent = ref(false);
const isReporting = ref(false);

// Computed properties
const isPostCreation = computed(() => props.activity?.action === "createpost");
const isPostOwner = computed(() => props.activity?.action_by?.id === usePage().props.auth.user.id);
const hasLongContent = computed(() => {
    if (!props.activity?.target_resource?.content) return false;
    return props.activity.target_resource.content.length > 300;
});

// Get the appropriate viewer component based on activity type
const getViewerComponent = () => {
    switch (props.activity?.action_to) {
        case 'Post':
            return NewsFeedPostsViewer;
        case 'AcademyPost':
            return AcademyPostViewer;
        case 'CoursePost':
            return CoursePostViewer;
        case 'Donate':
            return DonationsViewer;
        case 'DonateRecipient':
            return DonateRecipientViewer;
        default:
            return NewsFeedPostsViewer;
    }
};

// Get activity type label and color
const getActivityTypeInfo = () => {
    switch (props.activity?.action_to) {
        case 'Post':
            return { label: 'โพสต์ทั่วไป', color: 'bg-blue-100 text-blue-800', icon: 'heroicons-outline:document-text' };
        case 'AcademyPost':
            return { label: 'แหล่งเรียนรู้', color: 'bg-green-100 text-green-800', icon: 'heroicons-outline:academic-cap' };
        case 'CoursePost':
            return { label: 'รายวิชา', color: 'bg-purple-100 text-purple-800', icon: 'heroicons-outline:book-open' };
        case 'Donate':
            return { label: 'การบริจาค', color: 'bg-pink-100 text-pink-800', icon: 'heroicons-outline:heart' };
        case 'DonateRecipient':
            return { label: 'การได้รับการสนับสนุน', color: 'bg-yellow-100 text-yellow-800', icon: 'heroicons-outline:gift' };
        default:
            return { label: 'กิจกรรม', color: 'bg-gray-100 text-gray-800', icon: 'heroicons-outline:information-circle' };
    }
};

// Toggle content expansion
const toggleContentExpansion = () => {
    showFullContent.value = !showFullContent.value;
};

// Report post
const reportPost = () => {
    Swal.fire({
        title: 'แจ้งลบโพสต์',
        text: 'กรุณาระบุเหตุผลที่ต้องการแจ้งลบโพสต์นี้',
        input: 'textarea',
        inputPlaceholder: 'กรุณาระบุเหตุผล...',
        inputAttributes: {
            'aria-label': 'กรุณาระบุเหตุผล'
        },
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ส่งรายงาน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            isReporting.value = true;
            // Here you would normally send the report to the server
            setTimeout(() => {
                isReporting.value = false;
                Swal.fire(
                    'ส่งรายงานสำเร็จ!',
                    'เราได้รับรายงานของคุณแล้ว ขอบคุณสำหรับการช่วยกันรักษาสิ่งแวดล้อม',
                    'success'
                );
            }, 1000);
        }
    });
};

// Get preview content
const getPreviewContent = () => {
    if (!props.activity?.target_resource?.content) return '';
    
    const content = props.activity.target_resource.content;
    if (content.length <= 300 || showFullContent.value) {
        return content;
    }
    
    return content.substring(0, 300) + '...';
};
</script>

<template>
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
        <!-- Card Header -->
        <div class="p-4 border-b border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-3">
                    <img
                        :src="activity?.action_by?.avatar || '/storage/images/plearnd-logo.png'"
                        class="w-10 h-10 rounded-full object-cover"
                        :alt="activity?.action_by?.name || 'User'"
                    >
                    <div>
                        <h4 class="font-semibold text-gray-900">{{ activity?.action_by?.name || 'Unknown User' }}</h4>
                        <p class="text-sm text-gray-500">{{ activity?.diff_humans_created_at }}</p>
                    </div>
                </div>
                
                <!-- Activity Type Badge -->
                <div class="flex items-center space-x-2">
                    <span :class="['px-2 py-1 text-xs font-medium rounded-full', getActivityTypeInfo().color]">
                        {{ getActivityTypeInfo().label }}
                    </span>
                    
                    <!-- Menu Dropdown -->
                    <div class="relative">
                        <button 
                            @click="isExpanded = !isExpanded"
                            class="p-1 rounded-full hover:bg-gray-100 transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div v-if="isExpanded" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                            <div class="py-1">
                                <a
                                    :href="activity?.target_resource?.post_url"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    ดูโพสต์
                                </a>
                                <button
                                    v-if="isPostOwner"
                                    @click="reportPost"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    แก้ไขโพสต์
                                </button>
                                <button
                                    v-if="isPostOwner"
                                    @click="$emit('delete-post', index)"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
                                >
                                    ลบโพสต์
                                </button>
                                <button
                                    v-if="!isPostOwner"
                                    @click="reportPost"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
                                >
                                    แจ้งลบโพสต์
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Card Content -->
        <div class="p-4">
            <!-- Content Preview -->
            <div v-if="activity?.target_resource?.content" class="mb-4">
                <p class="text-gray-800 whitespace-pre-wrap">{{ getPreviewContent() }}</p>
                <button 
                    v-if="hasLongContent && !showFullContent"
                    @click="toggleContentExpansion"
                    class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium"
                >
                    อ่านเพิ่มเติม
                </button>
                <button 
                    v-if="hasLongContent && showFullContent"
                    @click="toggleContentExpansion"
                    class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium"
                >
                    แสดงน้อยลง
                </button>
            </div>
            
            <!-- Dynamic Content Viewer -->
            <component 
                :is="getViewerComponent()"
                :activity="activity"
                :useLazyLoading="true"
                @delete-post="$emit('delete-post', index)"
            />
        </div>
        
        <!-- Loading Overlay -->
        <div v-if="isReporting" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-20">
            <div class="flex flex-col items-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="mt-2 text-sm text-gray-600">กำลังส่งรายงาน...</p>
            </div>
        </div>
    </div>
</template>