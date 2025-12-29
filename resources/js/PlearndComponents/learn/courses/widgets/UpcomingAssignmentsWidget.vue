<script setup>
import { ref, computed, onMounted } from 'vue';
import { Icon } from '@iconify/vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    assignments: Array,
    isCourseAdmin: Boolean,
});

const isExpanded = ref(true);
const isLoading = ref(false);
const upcomingAssignments = ref([]);

// Get upcoming assignments (not past due, limit to 5)
onMounted(() => {
    if (props.assignments && props.assignments.length > 0) {
        const now = new Date();
        upcomingAssignments.value = props.assignments
            .filter(a => !a.due_date || new Date(a.due_date) >= now)
            .slice(0, 5);
    }
});

const navigateToAssignment = (assignmentId) => {
    isLoading.value = true;
    router.visit(`/courses/${props.course.data.id}/assignments/${assignmentId}`);
};

const navigateToAssignments = () => {
    isLoading.value = true;
    router.visit(`/courses/${props.course.data.id}/assignments`);
};

const formatDueDate = (dateString) => {
    if (!dateString) return 'ไม่มีกำหนด';
    const date = new Date(dateString);
    const now = new Date();
    const diff = date - now;
    const days = Math.ceil(diff / (1000 * 60 * 60 * 24));
    
    if (days === 0) return 'วันนี้';
    if (days === 1) return 'พรุ่งนี้';
    if (days < 7) return `อีก ${days} วัน`;
    return date.toLocaleDateString('th-TH', { day: 'numeric', month: 'short' });
};

const getDueDateColor = (dateString) => {
    if (!dateString) return 'text-gray-500';
    const date = new Date(dateString);
    const now = new Date();
    const diff = date - now;
    const days = Math.ceil(diff / (1000 * 60 * 60 * 24));
    
    if (days <= 1) return 'text-red-500';
    if (days <= 3) return 'text-orange-500';
    return 'text-green-500';
};
</script>

<template>
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Header -->
        <button 
            @click="isExpanded = !isExpanded"
            class="w-full bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-3 flex items-center justify-between"
        >
            <h3 class="text-white font-semibold flex items-center gap-2">
                <Icon icon="mdi:clipboard-text-clock" class="w-5 h-5" />
                งานที่มอบหมาย
            </h3>
            <Icon 
                :icon="isExpanded ? 'mdi:chevron-up' : 'mdi:chevron-down'" 
                class="w-5 h-5 text-white transition-transform duration-200"
            />
        </button>
        
        <!-- Assignments List -->
        <div v-show="isExpanded" class="divide-y divide-gray-100">
            <template v-if="upcomingAssignments.length > 0">
                <button
                    v-for="assignment in upcomingAssignments"
                    :key="assignment.id"
                    @click="navigateToAssignment(assignment.id)"
                    class="w-full px-4 py-3 flex items-center gap-3 hover:bg-gray-50 transition-colors text-left"
                >
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                        <Icon icon="mdi:file-document-edit" class="w-5 h-5" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ assignment.title || assignment.name }}</p>
                        <p class="text-xs" :class="getDueDateColor(assignment.due_date)">
                            <Icon icon="mdi:calendar-clock" class="w-3 h-3 inline mr-1" />
                            {{ formatDueDate(assignment.due_date) }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 text-xs text-gray-500">
                        {{ assignment.max_score || 100 }} คะแนน
                    </div>
                </button>
            </template>
            
            <div v-else class="px-4 py-6 text-center text-gray-500">
                <Icon icon="mdi:clipboard-check" class="w-10 h-10 mx-auto mb-2 text-gray-300" />
                <p class="text-sm">ไม่มีงานที่ต้องทำ</p>
            </div>

            <!-- View All Button -->
            <button
                @click="navigateToAssignments"
                class="w-full px-4 py-3 text-center text-sm font-medium text-amber-600 hover:bg-amber-50 transition-colors"
            >
                ดูงานทั้งหมด
                <Icon icon="mdi:arrow-right" class="w-4 h-4 inline ml-1" />
            </button>
        </div>

        <!-- Loading Overlay -->
        <div v-if="isLoading" class="absolute inset-0 bg-white/80 flex items-center justify-center">
            <Icon icon="svg-spinners:bars-rotate-fade" class="w-8 h-8 text-amber-500" />
        </div>
    </div>
</template>
