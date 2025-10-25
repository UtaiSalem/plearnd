<script setup>
import { ref, watch, nextTick } from 'vue';
import { Icon } from '@iconify/vue';

const props = defineProps({
    searchQuery: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['search', 'close']);

const localQuery = ref(props.searchQuery);
const searchInput = ref(null);

// Watch for changes in local query and emit search event
// watch(localQuery, (newQuery) => {
//     if (newQuery.trim()) {
//         emit('search', newQuery);
//     }
// });

// Focus on the input when the component is mounted
nextTick(() => {
    if (searchInput.value) {
        searchInput.value.focus();
    }
});

// Handle keydown events
const handleKeydown = (event) => {
    if (event.key === 'Escape') {
        emit('close');
    }
    // Handle Enter key to trigger search
    if (event.key === 'Enter') {
        // Prevent default form submission behavior
        event.preventDefault();
        // Only emit search if query is not empty
        if (localQuery.value.trim()) {
            emit('search', localQuery.value);
        }
    }
};

// Clear the search
const clearSearch = () => {
    localQuery.value = '';
};
</script>

<template>
    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-lg font-semibold text-gray-800">ค้นหาโพสต์</h3>
            <button 
                @click="emit('close')"
                class="text-gray-500 hover:text-gray-700 transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="flex">

                <input
                ref="searchInput"
                v-model="localQuery"
                type="text"
                placeholder="ค้นหาโพสต์หรือชื่อผู้ใช้..."
                class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                @keydown="handleKeydown"
                />

                <!-- <button @click="handleKeydown" class="ml-2 px-4 py-1.5 bg-blue-500 text-white rounded-lg">Search</button> -->
            </div>

            <div v-if="localQuery" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <button
                    @click="clearSearch"
                    class="text-gray-400 hover:text-gray-600 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293 1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="mt-3 text-sm text-gray-600">
            <p>พิมพ์คำค้นหาเพื่อค้นหาโพสต์หรือชื่อผู้ใช้</p>
            <p class="mt-1">กด ESC เพื่อปิดการค้นหา</p>
        </div>
    </div>
</template>