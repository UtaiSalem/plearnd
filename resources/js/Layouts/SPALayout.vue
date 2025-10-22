<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import store from '../store';

const route = useRoute();
const router = useRouter();

// State
const isSidebarOpen = ref(false);
const isMobileMenuOpen = ref(false);
const showBackToTop = ref(false);

// Computed
const isAuthenticated = computed(() => store.getters.isAuthenticated);
const currentUser = computed(() => store.state.user);
const pageTitle = computed(() => route.meta.title || 'Plearnd');
const notifications = computed(() => store.state.notifications);
const unreadNotificationsCount = computed(() => {
    return notifications.value.filter(n => !n.read).length;
});

// Methods
const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
};

const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
};

const handleScroll = () => {
    showBackToTop.value = window.pageYOffset > 300;
};

const logout = async () => {
    await store.actions.logout();
    router.push('/login');
};

const navigateTo = (path) => {
    router.push(path);
    closeMobileMenu();
};

// Lifecycle
onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Loading Indicator -->
        <div v-if="$root?.isLoading" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 flex flex-col items-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                <p class="mt-4 text-gray-600">กำลังโหลด...</p>
            </div>
        </div>

        <!-- Navigation Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 fixed top-0 left-0 right-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <img 
                            src="/storage/images/plearnd-logo.png" 
                            alt="Plearnd" 
                            class="h-8 w-auto cursor-pointer"
                            @click="navigateTo('/newsfeed-v2')"
                        >
                    </div>

                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex space-x-8">
                        <a 
                            href="#" 
                            @click="navigateTo('/newsfeed-v2')"
                            :class="route.name === 'newsfeed-v2' ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600'"
                            class="px-3 py-2 text-sm font-medium"
                        >
                            กระดานข่าว
                        </a>
                        <a 
                            href="#" 
                            @click="navigateTo('/academy')"
                            :class="route.name === 'academy' ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600'"
                            class="px-3 py-2 text-sm font-medium"
                        >
                            แหล่งเรียนรู้
                        </a>
                        <a 
                            href="#" 
                            @click="navigateTo('/supports')"
                            :class="route.name === 'supports' ? 'text-blue-600' : 'text-gray-700 hover:text-blue-600'"
                            class="px-3 py-2 text-sm font-medium"
                        >
                            การสนับสนุน
                        </a>
                    </nav>

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative" v-if="isAuthenticated">
                            <button 
                                @click="store.actions.toggleNotifications()"
                                class="p-1 rounded-full text-gray-600 hover:text-gray-900 focus:outline-none"
                            >
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span 
                                    v-if="unreadNotificationsCount > 0" 
                                    class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white"
                                ></span>
                            </button>
                        </div>

                        <!-- User Avatar -->
                        <div class="relative" v-if="isAuthenticated && currentUser">
                            <button 
                                @click="toggleSidebar"
                                class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                <img 
                                    :src="currentUser.avatar || '/storage/images/plearnd-logo.png'" 
                                    :alt="currentUser.name"
                                    class="h-8 w-8 rounded-full object-cover"
                                >
                            </button>
                        </div>

                        <!-- Login/Register Buttons -->
                        <div v-else class="flex space-x-2">
                            <button 
                                @click="navigateTo('/login')"
                                class="px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-500"
                            >
                                เข้าสู่ระบบ
                            </button>
                            <button 
                                @click="navigateTo('/register')"
                                class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                            >
                                สมัครสมาชิก
                            </button>
                        </div>

                        <!-- Mobile menu button -->
                        <div class="md:hidden">
                            <button 
                                @click="toggleMobileMenu"
                                class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none"
                            >
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div v-if="isMobileMenuOpen" class="md:hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a 
                        href="#" 
                        @click="navigateTo('/newsfeed-v2')"
                        :class="route.name === 'newsfeed-v2' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50'"
                        class="block px-3 py-2 rounded-md text-base font-medium"
                    >
                        กระดานข่าว
                    </a>
                    <a 
                        href="#" 
                        @click="navigateTo('/academy')"
                        :class="route.name === 'academy' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50'"
                        class="block px-3 py-2 rounded-md text-base font-medium"
                    >
                        แหล่งเรียนรู้
                    </a>
                    <a 
                        href="#" 
                        @click="navigateTo('/supports')"
                        :class="route.name === 'supports' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50'"
                        class="block px-3 py-2 rounded-md text-base font-medium"
                    >
                        การสนับสนุน
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="pt-16">
            <router-view />
        </main>

        <!-- User Sidebar -->
        <div v-if="isSidebarOpen" class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="toggleSidebar"></div>
                <section class="absolute inset-y-0 right-0 max-w-xs w-full bg-white shadow-xl">
                    <div class="h-full flex flex-col">
                        <!-- Header -->
                        <div class="px-4 py-6 bg-blue-600 sm:px-6">
                            <div class="flex items-center">
                                <img 
                                    :src="currentUser.avatar || '/storage/images/plearnd-logo.png'" 
                                    :alt="currentUser.name"
                                    class="h-10 w-10 rounded-full object-cover"
                                >
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-white">{{ currentUser.name }}</p>
                                    <p class="text-xs font-medium text-blue-200">{{ currentUser.email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div class="flex-1 px-4 py-6 sm:px-6">
                            <nav class="space-y-1">
                                <a 
                                    href="#" 
                                    @click="navigateTo('/profile/' + currentUser.id)"
                                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50"
                                >
                                    โปรไฟล์ของฉัน
                                </a>
                                <a 
                                    href="#" 
                                    @click="navigateTo('/dashboard')"
                                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50"
                                >
                                    แดชบอร์ด
                                </a>
                                <a 
                                    href="#" 
                                    @click="navigateTo('/privacy-policy')"
                                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50"
                                >
                                    นโยบายความเป็นส่วนตัว
                                </a>
                                <a 
                                    href="#" 
                                    @click="navigateTo('/terms-of-service')"
                                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50"
                                >
                                    เงื่อนไขการใช้งาน
                                </a>
                            </nav>
                        </div>

                        <!-- Footer -->
                        <div class="border-t border-gray-200 px-4 py-6 sm:px-6">
                            <button 
                                @click="logout"
                                class="block px-3 py-2 rounded-md text-base font-medium text-white bg-red-600 hover:bg-red-700 w-full text-center"
                            >
                                ออกจากระบบ
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <!-- Back to Top Button -->
        <button
            v-if="showBackToTop"
            @click="scrollToTop"
            class="fixed bottom-8 right-8 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-all duration-300 z-40"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
            </svg>
        </button>
    </div>
</template>

<style scoped>
.loading {
    pointer-events: none;
    opacity: 0.7;
}
</style>