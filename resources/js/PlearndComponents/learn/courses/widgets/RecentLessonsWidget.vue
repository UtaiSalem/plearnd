<script setup>
import { ref, computed, onMounted } from 'vue';
import { Icon } from '@iconify/vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    lessons: Array,
    isCourseAdmin: Boolean,
});

const isExpanded = ref(true);
const isLoading = ref(false);
const recentLessons = ref([]);

// Get recent lessons (limit to 5)
onMounted(() => {
    if (props.lessons && props.lessons.length > 0) {
        recentLessons.value = props.lessons.slice(0, 5);
    }
});

const navigateToLesson = (lessonId) => {
    isLoading.value = true;
    router.visit(`/courses/${props.course.data.id}/lessons/${lessonId}`);
};

const navigateToLessons = () => {
    isLoading.value = true;
    router.visit(`/courses/${props.course.data.id}/lessons`);
};
</script>

<template>
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Header -->
        <button 
            @click="isExpanded = !isExpanded"
            class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-3 flex items-center justify-between"
        >
            <h3 class="text-white font-semibold flex items-center gap-2">
                <Icon icon="mdi:book-open-page-variant" class="w-5 h-5" />
                บทเรียนล่าสุด
            </h3>
            <Icon 
                :icon="isExpanded ? 'mdi:chevron-up' : 'mdi:chevron-down'" 
                class="w-5 h-5 text-white transition-transform duration-200"
            />
        </button>
        
        <!-- Lessons List -->
        <div v-show="isExpanded" class="divide-y divide-gray-100">
            <template v-if="recentLessons.length > 0">
                <button
                    v-for="(lesson, index) in recentLessons"
                    :key="lesson.id"
                    @click="navigateToLesson(lesson.id)"
                    class="w-full px-4 py-3 flex items-center gap-3 hover:bg-gray-50 transition-colors text-left"
                >
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm font-semibold">
                        {{ index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ lesson.name || lesson.title }}</p>
                        <p class="text-xs text-gray-500">{{ lesson.topics_count || 0 }} หัวข้อ</p>
                    </div>
                    <Icon icon="mdi:chevron-right" class="w-5 h-5 text-gray-400" />
                </button>
            </template>
            
            <div v-else class="px-4 py-6 text-center text-gray-500">
                <Icon icon="mdi:book-off-outline" class="w-10 h-10 mx-auto mb-2 text-gray-300" />
                <p class="text-sm">ยังไม่มีบทเรียน</p>
            </div>

            <!-- View All Button -->
            <button
                @click="navigateToLessons"
                class="w-full px-4 py-3 text-center text-sm font-medium text-emerald-600 hover:bg-emerald-50 transition-colors"
            >
                ดูบทเรียนทั้งหมด
                <Icon icon="mdi:arrow-right" class="w-4 h-4 inline ml-1" />
            </button>
        </div>

        <!-- Loading Overlay -->
        <div v-if="isLoading" class="absolute inset-0 bg-white/80 flex items-center justify-center">
            <Icon icon="svg-spinners:bars-rotate-fade" class="w-8 h-8 text-emerald-500" />
        </div>
    </div>
</template>
