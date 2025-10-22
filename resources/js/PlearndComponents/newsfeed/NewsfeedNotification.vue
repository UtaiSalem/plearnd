<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close']);

const notifications = ref([]);
const isLoading = ref(false);
const hasUnread = ref(false);

// Mock notifications data - in a real app, this would come from an API
const mockNotifications = [
    {
        id: 1,
        type: 'like',
        message: 'John Doe ไลค์โพสต์ของคุณ',
        time: '5 นาทีที่แล้ว',
        read: false,
        avatar: '/storage/images/avatars/user1.jpg',
        link: '/posts/123'
    },
    {
        id: 2,
        type: 'comment',
        message: 'Jane Smith คอมเมนต์บนโพสต์ของคุณ: "เยี่ยมมาก!"',
        time: '15 นาทีที่แล้ว',
        read: false,
        avatar: '/storage/images/avatars/user2.jpg',
        link: '/posts/123#comment-456'
    },
    {
        id: 3,
        type: 'follow',
        message: 'Mike Johnson ติดตามคุณ',
        time: '1 ชั่วโมงที่แล้ว',
        read: true,
        avatar: '/storage/images/avatars/user3.jpg',
        link: '/profiles/mike-johnson'
    },
    {
        id: 4,
        type: 'mention',
        message: 'Sarah Williams กล่าวถึงคุณในคอมเมนต์',
        time: '2 ชั่วโมงที่แล้ว',
        read: true,
        avatar: '/storage/images/avatars/user4.jpg',
        link: '/posts/789#comment-123'
    },
    {
        id: 5,
        type: 'post_approved',
        message: 'โพสต์ของคุณในแหล่งเรียนรู้ "JavaScript Basics" ได้รับการอนุมัติแล้ว',
        time: '3 ชั่วโมงที่แล้ว',
        read: true,
        avatar: '/storage/images/plearnd-logo.png',
        link: '/academies/js-basics/posts/456'
    }
];

// Computed properties
const unreadCount = computed(() => {
    return notifications.value.filter(n => !n.read).length;
});

const sortedNotifications = computed(() => {
    return [...notifications.value].sort((a, b) => {
        // Sort by read status first (unread first), then by time
        if (a.read !== b.read) {
            return a.read ? 1 : -1;
        }
        return 0; // In a real app, you'd sort by actual timestamp
    });
});

// Load notifications
const loadNotifications = async () => {
    isLoading.value = true;
    try {
        // In a real app, you'd fetch from an API
        // const response = await axios.get('/api/notifications');
        // notifications.value = response.data.notifications;
        
        // For now, use mock data
        await new Promise(resolve => setTimeout(resolve, 500));
        notifications.value = mockNotifications;
        hasUnread.value = unreadCount.value > 0;
    } catch (error) {
        console.error('Error loading notifications:', error);
    } finally {
        isLoading.value = false;
    }
};

// Mark notification as read
const markAsRead = (notificationId) => {
    const notification = notifications.value.find(n => n.id === notificationId);
    if (notification) {
        notification.read = true;
        hasUnread.value = unreadCount.value > 0;
        
        // In a real app, you'd send this to the server
        // axios.put(`/api/notifications/${notificationId}/read`);
    }
};

// Mark all notifications as read
const markAllAsRead = () => {
    notifications.value.forEach(notification => {
        notification.read = true;
    });
    hasUnread.value = false;
    
    // In a real app, you'd send this to the server
    // axios.put('/api/notifications/read-all');
};

// Handle notification click
const handleNotificationClick = (notification) => {
    markAsRead(notification.id);
    emit('close');
    
    // Navigate to the notification link
    if (notification.link) {
        window.location.href = notification.link;
    }
};

// Get notification icon based on type
const getNotificationIcon = (type) => {
    switch (type) {
        case 'like':
            return 'heroicons-outline:heart';
        case 'comment':
            return 'heroicons-outline:chat-alt-2';
        case 'follow':
            return 'heroicons-outline:user-add';
        case 'mention':
            return 'heroicons-outline:at-symbol';
        case 'post_approved':
            return 'heroicons-outline:check-circle';
        default:
            return 'heroicons-outline:bell';
    }
};

// Get notification color based on type
const getNotificationColor = (type) => {
    switch (type) {
        case 'like':
            return 'text-red-500';
        case 'comment':
            return 'text-blue-500';
        case 'follow':
            return 'text-green-500';
        case 'mention':
            return 'text-purple-500';
        case 'post_approved':
            return 'text-yellow-500';
        default:
            return 'text-gray-500';
    }
};

// Close notification panel
const closeNotifications = () => {
    emit('close');
};

// Load notifications when component is mounted
onMounted(() => {
    if (props.show) {
        loadNotifications();
    }
});
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-hidden">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-25" @click="closeNotifications"></div>
        
        <!-- Notification Panel -->
        <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-xl overflow-hidden">
            <div class="flex flex-col h-full">
                <!-- Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-white">
                    <h2 class="text-lg font-semibold text-gray-900">การแจ้งเตือน</h2>
                    <div class="flex items-center space-x-2">
                        <button
                            v-if="unreadCount > 0"
                            @click="markAllAsRead"
                            class="text-sm text-blue-600 hover:text-blue-800"
                        >
                            ทำเครื่องหมายว่าอ่านทั้งหมด
                        </button>
                        <button
                            @click="closeNotifications"
                            class="p-1 rounded-full hover:bg-gray-100 transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Notifications List -->
                <div class="flex-1 overflow-y-auto">
                    <!-- Loading State -->
                    <div v-if="isLoading" class="flex items-center justify-center h-32">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    </div>
                    
                    <!-- Empty State -->
                    <div v-else-if="notifications.length === 0" class="flex flex-col items-center justify-center h-32 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <p>ไม่มีการแจ้งเตือน</p>
                    </div>
                    
                    <!-- Notifications -->
                    <div v-else>
                        <div
                            v-for="notification in sortedNotifications"
                            :key="notification.id"
                            @click="handleNotificationClick(notification)"
                            :class="[
                                'flex items-start p-4 border-b border-gray-100 cursor-pointer transition-colors',
                                notification.read ? 'bg-white hover:bg-gray-50' : 'bg-blue-50 hover:bg-blue-100'
                            ]"
                        >
                            <div class="flex-shrink-0 mr-3">
                                <img
                                    :src="notification.avatar"
                                    alt="Avatar"
                                    class="h-10 w-10 rounded-full object-cover"
                                >
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mr-2 mt-0.5">
                                        <component
                                            :is="getNotificationIcon(notification.type)"
                                            :class="['h-5 w-5', getNotificationColor(notification.type)]"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-900">{{ notification.message }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ notification.time }}</p>
                                    </div>
                                    <div v-if="!notification.read" class="flex-shrink-0 ml-2">
                                        <span class="inline-block h-2 w-2 rounded-full bg-blue-600"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>