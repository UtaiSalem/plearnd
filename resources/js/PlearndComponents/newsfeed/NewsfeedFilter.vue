<script setup>
import { ref } from 'vue';

const props = defineProps({
    activeFilter: {
        type: String,
        default: 'all'
    }
});

const emit = defineEmits(['filter-change', 'close']);

const filterOptions = [
    { id: 'all', label: 'ทั้งหมด', icon: 'heroicons-outline:globe-alt' },
    { id: 'posts', label: 'โพสต์ทั่วไป', icon: 'heroicons-outline:document-text' },
    { id: 'academy', label: 'แหล่งเรียนรู้', icon: 'heroicons-outline:academic-cap' },
    { id: 'courses', label: 'รายวิชา', icon: 'heroicons-outline:book-open' },
    { id: 'donations', label: 'การบริจาค', icon: 'heroicons-outline:heart' }
];

const selectFilter = (filterId) => {
    emit('filter-change', filterId);
};
</script>

<template>
    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-lg font-semibold text-gray-800">กรองเนื้อหา</h3>
            <button 
                @click="emit('close')"
                class="text-gray-500 hover:text-gray-700 transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <button
                v-for="option in filterOptions"
                :key="option.id"
                @click="selectFilter(option.id)"
                :class="[
                    'flex flex-col items-center justify-center p-3 rounded-lg border-2 transition-all duration-200',
                    activeFilter === option.id
                        ? 'border-blue-500 bg-blue-50 text-blue-700'
                        : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300 hover:bg-gray-50'
                ]"
            >
                <component :is="option.icon" class="h-6 w-6 mb-1" />
                <span class="text-sm font-medium">{{ option.label }}</span>
            </button>
        </div>
    </div>
</template>