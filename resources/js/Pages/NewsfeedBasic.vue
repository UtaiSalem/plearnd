<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    activities: Array,
    pagination: Object,
});

// State management
const isLoading = ref(false);
const activities = ref(props.activities || []);
const currentPage = ref(props.pagination?.current_page || 1);
const lastPage = ref(props.pagination?.last_page || 1);

// Debug initial state
console.log('NewsfeedBasic - Initial state', {
    activities: props.activities,
    pagination: props.pagination,
    activitiesCount: activities.value.length,
    currentPage: currentPage.value,
    lastPage: lastPage.value,
});

// Load more activities
const loadMoreActivities = async () => {
    if (currentPage.value >= lastPage.value) return;
    
    isLoading.value = true;
    try {
        currentPage.value++;
        
        console.log('Fetching page', currentPage.value);
        const response = await axios.get(`/api/newsfeed-basic/activities?page=${currentPage.value}&per_page=5`);
        
        console.log('Response received', {
            success: response.data.success,
            activitiesCount: response.data.activities?.length || 0,
            pagination: response.data.pagination
        });
        
        if (response.data.success) {
            const newActivities = Array.isArray(response.data.activities) ? response.data.activities : [];
            
            console.log('Adding activities', {
                currentCount: activities.value.length,
                newCount: newActivities.length
            });
            
            if (newActivities.length > 0) {
                newActivities.forEach(activity => {
                    activities.value.push(activity);
                });
                
                // Update pagination info from response
                if (response.data.pagination) {
                    lastPage.value = response.data.pagination.last_page;
                    console.log('Updated lastPage to', lastPage.value);
                }
            }
        } else {
            console.error('API returned error', response.data);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด! กรุณาลองใหม่อีกครั้ง',
                text: response.data.message || 'ไม่สามารถโหลดข้อมูลได้',
            });
        }
    } catch (error) {
        console.error('Error loading more activities:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด!',
            text: 'ไม่สามารถโหลดข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
        });
    } finally {
        isLoading.value = false;
    }
};

// Handle navigation to other pages
const handleLinkToPage = (href) => {
    isLoading.value = true;
    router.visit(href);
};

// Format date
const formatDate = (dateString) => {
    if (!dateString) return 'ไม่ทราบ';
    
    const date = new Date(dateString);
    const now = new Date();
    const diffInMinutes = Math.floor((now - date) / (1000 * 60));
    
    if (diffInMinutes < 1) {
        return 'เมื่อสักครู่';
    } else if (diffInMinutes < 60) {
        return `${diffInMinutes} นาทีที่แล้ว`;
    } else if (diffInMinutes < 1440) {
        return `${Math.floor(diffInMinutes / 60)} ชั่วโมงที่แล้ว`;
    } else {
        return `${Math.floor(diffInMinutes / 1440)} วันที่แล้ว`;
    }
};

// Get activity type label
const getActivityTypeLabel = (actionTo) => {
    switch (actionTo) {
        case 'Post':
            return 'โพสต์ทั่วไป';
        case 'AcademyPost':
            return 'แหล่งเรียนรู้';
        case 'CoursePost':
            return 'รายวิชา';
        case 'Donate':
            return 'การบริจาค';
        case 'DonateRecipient':
            return 'การได้รับการสนับสนุน';
        default:
            return 'กิจกรรม';
    }
};

// Get activity type color
const getActivityTypeColor = (actionTo) => {
    switch (actionTo) {
        case 'Post':
            return 'bg-blue-100 text-blue-800';
        case 'AcademyPost':
            return 'bg-green-100 text-green-800';
        case 'CoursePost':
            return 'bg-purple-100 text-purple-800';
        case 'Donate':
            return 'bg-pink-100 text-pink-800';
        case 'DonateRecipient':
            return 'bg-yellow-100 text-yellow-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

onMounted(() => {
    console.log('NewsfeedBasic mounted with', activities.value.length, 'activities');
});
</script>

<template>
    <div class="">
        <MainLayout title="Newsfeed Basic">
            <template #coverProfileCard>
                <div class="hidden md:flex items-center mx-auto mt-2 mb-4 shadow-lg bg-[url('/storage/images/banner/banner-bg.png')] bg-cover bg-no-repeat rounded-lg">
                    <img class="section-banner-icon " :src="'/storage/images/banner/forums-icon.png'" alt="forums-icon">
                    <p class="text-white font-bold text-xl">กระดานข่าว (ฐาน)</p>
                </div>
            </template>

            <template #mainContent>
                <div>
                    <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">ข้อมูลพื้นฐานจากฐานข้อมูล</h2>
                        
                        <div class="mb-4 p-3 bg-gray-100 rounded">
                            <p class="text-sm text-gray-600">จำนวน Activities: {{ activities.length }}</p>
                            <p class="text-sm text-gray-600">หน้าปัจจุบัน: {{ currentPage }} / {{ lastPage }}</p>
                        </div>
                        
                        <button
                            v-if="currentPage < lastPage"
                            @click="loadMoreActivities"
                            :disabled="isLoading"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                        >
                            <span v-if="isLoading">กำลังโหลด...</span>
                            <span v-else>โหลดเพิ่ม</span>
                        </button>
                    </div>

                    <!-- Activities List -->
                    <div class="space-y-4">
                        <div v-if="activities.length === 0" class="text-center py-8 bg-white rounded-lg shadow">
                            <p class="text-gray-500">ไม่มีข้อมูล activities</p>
                        </div>
                        
                        <div v-for="activity in activities" :key="activity.id" class="bg-white rounded-lg shadow-md p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <img 
                                        :src="activity.action_by?.avatar || '/storage/images/plearnd-logo.png'" 
                                        class="w-10 h-10 rounded-full object-cover"
                                        :alt="activity.action_by?.name || 'User'"
                                    >
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ activity.action_by?.name || 'Unknown User' }}</h4>
                                        <p class="text-sm text-gray-500">{{ formatDate(activity.created_at) }}</p>
                                    </div>
                                </div>
                                
                                <span :class="['px-2 py-1 text-xs font-medium rounded-full', getActivityTypeColor(activity.action_to)]">
                                    {{ getActivityTypeLabel(activity.action_to) }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <p class="text-sm text-gray-600">
                                    <strong>Action:</strong> {{ activity.action }} 
                                    <strong>Action To:</strong> {{ activity.action_to }}
                                </p>
                            </div>
                            
                            <div v-if="activity.target_resource" class="p-3 bg-gray-50 rounded">
                                <h5 class="font-medium text-gray-800 mb-2">Target Resource:</h5>
                                <pre class="text-xs text-gray-600 overflow-x-auto">{{ JSON.stringify(activity.target_resource, null, 2) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </MainLayout>
    </div>
</template>