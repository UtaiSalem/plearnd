<script setup>
import { ref } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

import { Icon } from '@iconify/vue';
import MainLayout from '@/Layouts/MainLayout.vue';

defineProps({
    coursePageTitle: String,
});
const authUser = usePage().props.auth.user;
const isLoadingPage = ref(false);

const handleLoadingPage = (option) => {
    isLoadingPage.value = true;
    switch (option){
        case 1:
            router.visit(`/courses/users/${usePage().props.auth.user.id}`);
            break;
        case 2:
            router.visit(`/courses/users/${usePage().props.auth.user.id}/member`);
            break;
        case 3:
            router.visit(`/courses`);
            break;
        case 4:
            router.visit(`/courses/create`);
            break;
        default:
            // router.visit(option);
    };
}
</script>

<template>
    <MainLayout :title="coursePageTitle">
        <template #coverProfileCard>
            <!-- Loading Overlay -->
            <div v-if="isLoadingPage" class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
                <div class="relative flex flex-col items-center justify-center p-8 bg-white rounded-2xl shadow-2xl">
                    <Icon icon="svg-spinners:bars-rotate-fade" class="w-16 h-16 sm:w-24 sm:h-24 text-cyan-500" />
                    <p class="mt-4 text-gray-600 font-medium animate-pulse">กำลังโหลด...</p>
                </div>
            </div>

            <!-- Banner - Mobile version -->
            <div class="flex md:hidden items-center justify-center mt-4 mb-4 py-3 px-4 shadow-md bg-gradient-to-r from-cyan-600 to-blue-600 rounded-lg">
                <Icon icon="fluent-mdl2:publish-course" class="w-6 h-6 text-white mr-2" />
                <p class="text-lg font-bold text-white">รวมรายวิชา</p>
            </div>

            <!-- Banner - Desktop version -->
            <div class="hidden md:flex items-center mt-4 mb-4 shadow-lg bg-[url('/storage/images/banner/banner-bg.png')] bg-cover bg-no-repeat rounded-lg">
                <img class="section-banner-icon" :src="'/storage/images/banner/forums-icon.png'" alt="forums-icon">
                <p class="text-xl lg:text-4xl font-bold text-white">รวมรายวิชา</p>
            </div>

            <!-- Navigation Tabs -->
            <div class="w-full overflow-hidden bg-white rounded-lg shadow-xl border border-gray-100">
                <!-- Scrollable container for mobile -->
                <div class="relative">
                    <!-- Scroll hint gradient on right side (mobile only) -->
                    <div class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-white to-transparent pointer-events-none sm:hidden z-10"></div>
                    
                    <div class="flex flex-row justify-start sm:justify-evenly overflow-x-auto sm:overflow-visible scrollbar-hide">
                        <!-- Tab: รายวิชาของฉัน -->
                        <button type="button" @click="handleLoadingPage(1)"
                            class="flex-shrink-0 min-w-[70px] sm:flex-shrink sm:flex-1 sm:min-w-0 text-center border-b-4 rounded-none transition-all duration-300 ease-in-out transform hover:scale-105"
                            :class="{ 
                                'border-cyan-500 bg-gradient-to-t from-cyan-50 to-white shadow-sm': $page.url===`/courses/users/${$page.props.auth.user.id}`, 
                                'border-transparent hover:border-gray-300 hover:bg-gray-50': $page.url!==`/courses/users/${$page.props.auth.user.id}` 
                            }"
                        >
                            <div class="flex flex-col items-center justify-center py-2 sm:py-3 text-slate-600/80 transition-all duration-300">
                                <Icon icon="lucide:layout-list" 
                                    class="w-5 h-5 sm:w-6 sm:h-6 md:w-8 md:h-8 transition-all duration-300" 
                                    :class="{ 'text-cyan-500 scale-110': $page.url===`/courses/users/${$page.props.auth.user.id}`, 'hover:text-cyan-400': $page.url!==`/courses/users/${$page.props.auth.user.id}` }" 
                                />
                                <span class="text-[10px] sm:text-xs md:text-sm mt-0.5 sm:mt-1 font-medium transition-all duration-300 whitespace-nowrap" 
                                    :class="{ 'text-cyan-500 font-semibold': $page.url===`/courses/users/${$page.props.auth.user.id}` }">
                                    รายวิชาของฉัน
                                </span>
                            </div>
                        </button>

                        <!-- Tab: ที่เป็นสมาชิก -->
                        <button type="button" @click="handleLoadingPage(2)"
                            class="flex-shrink-0 min-w-[70px] sm:flex-shrink sm:flex-1 sm:min-w-0 text-center border-b-4 rounded-none transition-all duration-300 ease-in-out transform hover:scale-105"
                            :class="{ 
                                'border-cyan-500 bg-gradient-to-t from-cyan-50 to-white shadow-sm': $page.url===`/courses/users/${$page.props.auth.user.id}/member`, 
                                'border-transparent hover:border-gray-300 hover:bg-gray-50': $page.url!==`/courses/users/${$page.props.auth.user.id}/member` 
                            }"
                        >
                            <div class="flex flex-col items-center justify-center py-2 sm:py-3 text-slate-600/80 transition-all duration-300">
                                <Icon icon="mdi:account-group" 
                                    class="w-5 h-5 sm:w-6 sm:h-6 md:w-8 md:h-8 transition-all duration-300" 
                                    :class="{ 'text-cyan-500 scale-110': $page.url===`/courses/users/${$page.props.auth.user.id}/member`, 'hover:text-cyan-400': $page.url!==`/courses/users/${$page.props.auth.user.id}/member` }" 
                                />
                                <span class="text-[10px] sm:text-xs md:text-sm mt-0.5 sm:mt-1 font-medium transition-all duration-300 whitespace-nowrap" 
                                    :class="{ 'text-cyan-500 font-semibold': $page.url===`/courses/users/${$page.props.auth.user.id}/member` }">
                                    ที่เป็นสมาชิก
                                </span>
                            </div>
                        </button>

                        <!-- Tab: รวมทั้งหมด -->
                        <button type="button" @click="handleLoadingPage(3)"
                            class="flex-shrink-0 min-w-[70px] sm:flex-shrink sm:flex-1 sm:min-w-0 text-center border-b-4 rounded-none transition-all duration-300 ease-in-out transform hover:scale-105"
                            :class="{ 
                                'border-cyan-500 bg-gradient-to-t from-cyan-50 to-white shadow-sm': $page.url===`/courses`, 
                                'border-transparent hover:border-gray-300 hover:bg-gray-50': $page.url!==`/courses` 
                            }"
                        >
                            <div class="flex flex-col items-center justify-center py-2 sm:py-3 text-slate-600/80 transition-all duration-300">
                                <Icon icon="mdi:view-grid" 
                                    class="w-5 h-5 sm:w-6 sm:h-6 md:w-8 md:h-8 transition-all duration-300" 
                                    :class="{ 'text-cyan-500 scale-110': $page.url===`/courses`, 'hover:text-cyan-400': $page.url!==`/courses` }" 
                                />
                                <span class="text-[10px] sm:text-xs md:text-sm mt-0.5 sm:mt-1 font-medium transition-all duration-300 whitespace-nowrap" 
                                    :class="{ 'text-cyan-500 font-semibold': $page.url===`/courses` }">
                                    รวมทั้งหมด
                                </span>
                            </div>
                        </button>

                        <!-- Tab: เพิ่มรายวิชา (conditional) -->
                        <button type="button" @click="handleLoadingPage(4)" v-if="authUser.pp > 120000"
                            class="flex-shrink-0 min-w-[70px] sm:flex-shrink sm:flex-1 sm:min-w-0 text-center border-b-4 rounded-none transition-all duration-300 ease-in-out transform hover:scale-105"
                            :class="{ 
                                'border-cyan-500 bg-gradient-to-t from-cyan-50 to-white shadow-sm': $page.url===`/courses/create`, 
                                'border-transparent hover:border-gray-300 hover:bg-gray-50': $page.url!==`/courses/create` 
                            }"
                        >
                            <div class="flex flex-col items-center justify-center py-2 sm:py-3 text-slate-600/80 transition-all duration-300">
                                <Icon icon="mdi:plus-circle" 
                                    class="w-5 h-5 sm:w-6 sm:h-6 md:w-8 md:h-8 transition-all duration-300" 
                                    :class="{ 'text-cyan-500 scale-110': $page.url===`/courses/create`, 'hover:text-cyan-400': $page.url!==`/courses/create` }" 
                                />
                                <span class="text-[10px] sm:text-xs md:text-sm mt-0.5 sm:mt-1 font-medium transition-all duration-300 whitespace-nowrap" 
                                    :class="{ 'text-cyan-500 font-semibold': $page.url===`/courses/create` }">
                                    เพิ่มรายวิชา
                                </span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <template #leftSideWidget>
            <slot name="leftSideWidget"></slot>
        </template>

        <template #rightSideWidget>
            <slot name="rightSideWidget"></slot>
        </template>

        <template #mainContent>
            <slot name="coursesMainContent"></slot>
        </template>
    </MainLayout>
</template>

<style scoped>
/* Hide scrollbar but allow scrolling */
.scrollbar-hide {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;  /* Chrome, Safari and Opera */
}
</style>
