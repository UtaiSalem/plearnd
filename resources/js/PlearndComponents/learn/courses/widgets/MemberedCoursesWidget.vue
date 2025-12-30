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
    <div class="bg-white rounded-xl shadow-lg border border-cyan-100 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-cyan-500 to-blue-500 p-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <Icon icon="mdi:account-group" class="w-5 h-5 text-white" />
                    <h3 class="text-sm font-semibold text-white">รายวิชาที่เป็นสมาชิก</h3>
                </div>
                <span v-if="courses.length > 0" class="px-2 py-0.5 text-xs font-medium text-cyan-700 bg-white rounded-full">
                    {{ courses.length }} วิชา
                </span>
            </div>
        </div>
        
        <!-- Content -->
        <div class="p-2">
            <!-- Empty State -->
            <div v-if="courses.length === 0" class="py-4 text-center">
                <Icon icon="mdi:book-open-blank-variant" class="w-10 h-10 mx-auto text-gray-300" />
                <p class="mt-2 text-sm text-gray-500">ยังไม่มีรายวิชาที่เป็นสมาชิก</p>
            </div>
            
            <!-- Course Cards -->
            <div v-else class="space-y-2" :class="displayedCourses.length > 5 ? 'max-h-[600px] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300' : ''">
                <div v-for="course in displayedCourses" :key="course.id">
                    <CourseCard :course="course" @loading-page="handleGoToCourse(course.id)" />
                </div>
                
                <!-- Load More -->
                <button v-if="hasMore" 
                    @click="loadMore"
                    class="w-full py-2 text-sm text-cyan-600 hover:text-cyan-800 hover:bg-cyan-50 rounded-lg transition-colors border border-dashed border-cyan-300">
                    <span class="flex items-center justify-center gap-1">
                        <Icon icon="mdi:plus" class="w-4 h-4" />
                        <span>โหลดเพิ่ม (+{{ remainingCount }})</span>
                    </span>
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
