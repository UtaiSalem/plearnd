<script setup>
import { ref, computed } from 'vue';
import { Icon } from '@iconify/vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    isCourseAdmin: Boolean,
});

const isExpanded = ref(true);

// Course statistics
const stats = computed(() => [
    { 
        label: 'บทเรียน', 
        value: props.course.data.lessons_count || 0, 
        icon: 'icon-park-outline:view-grid-detail',
        color: 'text-blue-600 bg-blue-50'
    },
    { 
        label: 'ภาระงาน', 
        value: props.course.data.assignments_count || 0, 
        icon: 'material-symbols:assignment-add-outline',
        color: 'text-green-600 bg-green-50'
    },
    { 
        label: 'แบบทดสอบ', 
        value: props.course.data.quizzes_count || 0, 
        icon: 'healthicons:i-exam-qualification-outline',
        color: 'text-purple-600 bg-purple-50'
    },
    { 
        label: 'สมาชิก', 
        value: props.course.data.enrolled_students || 0, 
        icon: 'ph:users-four',
        color: 'text-orange-600 bg-orange-50'
    },
]);
</script>

<template>
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden sticky top-20">
        <!-- Header -->
        <button 
            @click="isExpanded = !isExpanded"
            class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 flex items-center justify-between"
        >
            <h3 class="text-white font-semibold flex items-center gap-2">
                <Icon icon="mdi:chart-box" class="w-5 h-5" />
                สถิติรายวิชา
            </h3>
            <Icon 
                :icon="isExpanded ? 'mdi:chevron-up' : 'mdi:chevron-down'" 
                class="w-5 h-5 text-white transition-transform duration-200"
            />
        </button>
        
        <!-- Stats Grid -->
        <div v-show="isExpanded" class="p-4">
            <div class="grid grid-cols-2 gap-3">
                <div 
                    v-for="stat in stats" 
                    :key="stat.label"
                    class="rounded-lg p-3 text-center transition-all hover:shadow-md"
                    :class="stat.color"
                >
                    <Icon :icon="stat.icon" class="w-6 h-6 mx-auto mb-1" />
                    <div class="text-2xl font-bold">{{ stat.value }}</div>
                    <div class="text-xs opacity-80">{{ stat.label }}</div>
                </div>
            </div>

            <!-- Course Progress (if member) -->
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">คะแนนรวม</span>
                    <span class="text-sm font-semibold text-gray-800">{{ props.course.data.total_score || 0 }} คะแนน</span>
                </div>
            </div>
        </div>
    </div>
</template>
