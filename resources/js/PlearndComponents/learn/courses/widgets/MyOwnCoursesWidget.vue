<script setup>
import { ref, computed } from 'vue';
import { Icon } from '@iconify/vue';
import { router, usePage } from '@inertiajs/vue3';
import CourseCard from '@/PlearndComponents/learn/courses/CourseCard.vue';

const props = defineProps({
    courses: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['go-to-course']);

const displayCount = ref(5);

const displayedCourses = computed(() => {
    return props.courses.slice(0, displayCount.value);
});

const hasMore = computed(() => {
    return props.courses.length > displayCount.value;
});

const remainingCount = computed(() => {
    return props.courses.length - displayCount.value;
});

const loadMore = () => {
    displayCount.value += 5;
};

const handleGoToCourse = (courseId) => {
    emit('go-to-course', courseId);
};

const authUser = usePage().props.auth.user;
</script>

<template>
    <div class="bg-white rounded-xl shadow-lg border border-indigo-100 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 p-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <Icon icon="mdi:shield-crown" class="w-5 h-5 text-white" />
                    <h3 class="text-sm font-semibold text-white">รายวิชาของฉัน</h3>
                </div>
                <span v-if="courses.length > 0" class="px-2 py-0.5 text-xs font-medium text-indigo-700 bg-white rounded-full">
                    {{ courses.length }} วิชา
                </span>
            </div>
        </div>
        
        <!-- Content -->
        <div class="p-2">
            <!-- Empty State -->
            <div v-if="courses.length === 0" class="py-4 text-center">
                <Icon icon="mdi:book-plus" class="w-10 h-10 mx-auto text-gray-300" />
                <p class="mt-2 text-sm text-gray-500">ยังไม่มีรายวิชาของตัวเอง</p>
                <button v-if="authUser.pp > 120000"
                    @click="router.visit('/courses/create')"
                    class="mt-2 px-3 py-1 text-xs bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors">
                    สร้างรายวิชาใหม่
                </button>
            </div>
            
            <!-- Course Cards -->
            <div v-else class="space-y-2" :class="displayedCourses.length > 5 ? 'max-h-[600px] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300' : ''">
                <div v-for="course in displayedCourses" :key="course.id">
                    <CourseCard :course="course" @loading-page="handleGoToCourse(course.id)" />
                </div>
                
                <!-- Load More -->
                <button v-if="hasMore" 
                    @click="loadMore"
                    class="w-full py-2 text-sm text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-colors border border-dashed border-indigo-300">
                    <span class="flex items-center justify-center gap-1">
                        <Icon icon="mdi:plus" class="w-4 h-4" />
                        <span>โหลดเพิ่ม (+{{ remainingCount }})</span>
                    </span>
                </button>
            </div>
            
            <!-- Create New Course Button -->
            <div v-if="authUser.pp > 120000 && courses.length > 0" class="mt-3 pt-3 border-t border-gray-100">
                <button @click="router.visit('/courses/create')"
                    class="w-full flex items-center justify-center gap-2 py-2 text-sm text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-colors">
                    <Icon icon="mdi:plus-circle" class="w-4 h-4" />
                    <span>สร้างรายวิชาใหม่</span>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.scrollbar-thin {
    scrollbar-width: thin;
}
.scrollbar-thin::-webkit-scrollbar {
    width: 4px;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background-color: #d1d5db;
    border-radius: 2px;
}
</style>
