<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Icon } from '@iconify/vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    memberedCourses: {
        type: Array,
        default: () => []
    },
    myCourses: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['go-to-course']);

// ตรวจสอบขนาดหน้าจอเพื่อกำหนด default tab
const isMdScreen = ref(false);

const checkScreenSize = () => {
    isMdScreen.value = window.innerWidth >= 768;
};

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

// บน md+ ให้ default เป็น 'my' เพราะ membered มี sidebar แล้ว
const activeTab = ref('membered');

watch(isMdScreen, (newVal) => {
    if (newVal) {
        activeTab.value = 'my';
    }
}, { immediate: true });

const handleGoToCourse = (courseId) => {
    emit('go-to-course', courseId);
};

const authUser = usePage().props.auth.user;
</script>

<template>
    <div class="lg:hidden mb-4">
        <!-- Tab Headers -->
        <div class="flex bg-white rounded-t-xl shadow-sm border border-gray-200">
            <!-- Tab: รายวิชาที่เป็นสมาชิก - ซ่อนบน md+ เพราะมี sidebar แล้ว -->
            <button 
                @click="activeTab = 'membered'"
                class="flex-1 py-3 px-2 text-center text-sm font-medium transition-colors border-b-2 md:hidden"
                :class="activeTab === 'membered' 
                    ? 'text-cyan-600 border-cyan-500 bg-cyan-50' 
                    : 'text-gray-500 border-transparent hover:text-gray-700'"
            >
                <div class="flex items-center justify-center gap-1">
                    <Icon icon="mdi:account-group" class="w-4 h-4" />
                    <span>ที่เป็นสมาชิก</span>
                    <span v-if="memberedCourses.length" class="px-1.5 py-0.5 text-xs bg-cyan-100 text-cyan-700 rounded-full">
                        {{ memberedCourses.length }}
                    </span>
                </div>
            </button>
            <!-- Tab: รายวิชาของฉัน -->
            <button 
                @click="activeTab = 'my'"
                class="flex-1 py-3 px-2 text-center text-sm font-medium transition-colors border-b-2"
                :class="[
                    activeTab === 'my' 
                        ? 'text-indigo-600 border-indigo-500 bg-indigo-50' 
                        : 'text-gray-500 border-transparent hover:text-gray-700',
                    isMdScreen ? 'rounded-xl' : ''
                ]"
            >
                <div class="flex items-center justify-center gap-1">
                    <Icon icon="mdi:shield-crown" class="w-4 h-4" />
                    <span>ของฉัน</span>
                    <span v-if="myCourses.length" class="px-1.5 py-0.5 text-xs bg-indigo-100 text-indigo-700 rounded-full">
                        {{ myCourses.length }}
                    </span>
                </div>
            </button>
        </div>
        
        <!-- Tab Content -->
        <div class="bg-white rounded-b-xl shadow-lg border border-t-0 border-gray-200 p-3">
            <!-- Membered Courses - ซ่อนบน md+ -->
            <div v-if="activeTab === 'membered'" class="md:hidden">
                <div v-if="memberedCourses.length === 0" class="py-6 text-center">
                    <Icon icon="mdi:book-open-blank-variant" class="w-10 h-10 mx-auto text-gray-300" />
                    <p class="mt-2 text-sm text-gray-500">ยังไม่มีรายวิชาที่เป็นสมาชิก</p>
                </div>
                <div v-else class="flex gap-3 overflow-x-auto pb-2 scrollbar-thin">
                    <div v-for="course in memberedCourses.slice(0, 10)" :key="course.id" 
                        class="flex-shrink-0 w-40 cursor-pointer"
                        @click="handleGoToCourse(course.id)">
                        <div class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow hover:shadow-md transition-shadow">
                            <!-- Course Cover -->
                            <div class="relative h-20 bg-gradient-to-r from-cyan-400 to-blue-500">
                                <img v-if="course.cover" 
                                    :src="`/storage/images/courses/covers/${course.cover}`" 
                                    :alt="course.name"
                                    class="object-cover w-full h-full" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                                <span class="absolute top-1 right-1 px-1.5 py-0.5 bg-green-500 text-white rounded text-[10px]">สมาชิก</span>
                            </div>
                            <!-- Course Info -->
                            <div class="p-2">
                                <h4 class="text-xs font-medium text-gray-800 line-clamp-2 mb-1">{{ course.name }}</h4>
                                <div class="flex items-center gap-1 text-[10px] text-gray-500">
                                    <Icon icon="mdi:account-multiple" class="w-3 h-3" />
                                    <span>{{ course.enrolled_students || 0 }} คน</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- View More -->
                    <div v-if="memberedCourses.length > 10" 
                        class="flex-shrink-0 w-40 cursor-pointer"
                        @click="router.visit(`/courses/users/${authUser.id}/member`)">
                        <div class="flex flex-col items-center justify-center h-full min-h-[120px] bg-cyan-50 border-2 border-dashed border-cyan-300 rounded-lg hover:bg-cyan-100 transition-colors">
                            <Icon icon="mdi:arrow-right-circle" class="w-8 h-8 text-cyan-500" />
                            <p class="mt-1 text-xs font-medium text-cyan-600">ดูทั้งหมด</p>
                            <p class="text-[10px] text-gray-400">+{{ memberedCourses.length - 10 }} วิชา</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- My Courses -->
            <div v-if="activeTab === 'my' || isMdScreen">
                <div v-if="myCourses.length === 0" class="py-6 text-center">
                    <Icon icon="mdi:book-plus" class="w-10 h-10 mx-auto text-gray-300" />
                    <p class="mt-2 text-sm text-gray-500">ยังไม่มีรายวิชาของตัวเอง</p>
                    <button v-if="authUser.pp > 120000"
                        @click="router.visit('/courses/create')"
                        class="mt-2 px-3 py-1.5 text-xs bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors">
                        <span class="flex items-center gap-1">
                            <Icon icon="mdi:plus" class="w-3 h-3" />
                            สร้างรายวิชาใหม่
                        </span>
                    </button>
                </div>
                <div v-else class="flex gap-3 overflow-x-auto pb-2 scrollbar-thin">
                    <div v-for="course in myCourses.slice(0, 10)" :key="course.id" 
                        class="flex-shrink-0 w-40 cursor-pointer"
                        @click="handleGoToCourse(course.id)">
                        <div class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow hover:shadow-md transition-shadow">
                            <!-- Course Cover -->
                            <div class="relative h-20 bg-gradient-to-r from-indigo-400 to-purple-500">
                                <img v-if="course.cover" 
                                    :src="`/storage/images/courses/covers/${course.cover}`" 
                                    :alt="course.name"
                                    class="object-cover w-full h-full" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                                <span class="absolute top-1 right-1 px-1.5 py-0.5 bg-indigo-500 text-white rounded text-[10px]">แอดมิน</span>
                            </div>
                            <!-- Course Info -->
                            <div class="p-2">
                                <h4 class="text-xs font-medium text-gray-800 line-clamp-2 mb-1">{{ course.name }}</h4>
                                <div class="flex items-center gap-1 text-[10px] text-gray-500">
                                    <Icon icon="mdi:account-multiple" class="w-3 h-3" />
                                    <span>{{ course.enrolled_students || 0 }} คน</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- View More -->
                    <div v-if="myCourses.length > 10" 
                        class="flex-shrink-0 w-40 cursor-pointer"
                        @click="router.visit(`/courses/users/${authUser.id}`)">
                        <div class="flex flex-col items-center justify-center h-full min-h-[120px] bg-indigo-50 border-2 border-dashed border-indigo-300 rounded-lg hover:bg-indigo-100 transition-colors">
                            <Icon icon="mdi:arrow-right-circle" class="w-8 h-8 text-indigo-500" />
                            <p class="mt-1 text-xs font-medium text-indigo-600">ดูทั้งหมด</p>
                            <p class="text-[10px] text-gray-400">+{{ myCourses.length - 10 }} วิชา</p>
                        </div>
                    </div>
                </div>
                <!-- Create Course Button (when has courses) -->
                <div v-if="myCourses.length > 0 && authUser.pp > 120000" class="mt-3 pt-3 border-t border-gray-100">
                    <button @click="router.visit('/courses/create')"
                        class="w-full flex items-center justify-center gap-2 py-2 text-sm text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-colors">
                        <Icon icon="mdi:plus-circle" class="w-4 h-4" />
                        <span>สร้างรายวิชาใหม่</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.scrollbar-thin {
    scrollbar-width: thin;
}
.scrollbar-thin::-webkit-scrollbar {
    height: 4px;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background-color: #d1d5db;
    border-radius: 2px;
}
</style>
