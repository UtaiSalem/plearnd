<script setup>
import { computed } from 'vue';

const props = defineProps({
    member_id: Number,
    user_id: Number,
    quiz: Object,
    results: Array,
});

const emit = defineEmits(['handleEmptyPoints']);

// Pre-filter results for this member to avoid repeated filtering
const memberResult = computed(() => {
    if (!props.results || !props.results.length) return null;
    return props.results.find(result => result.course_quiz_id === props.quiz.id);
});

// Check for empty result and emit error
const hasResult = computed(() => {
    return memberResult.value !== null;
});

// Watch for changes and emit error if needed
if (!hasResult.value) {
    emit('handleEmptyPoints');
}
</script>

<template>
    <div v-if="memberResult" class="text-center">
        <div v-if="memberResult.score" class="text-gray-900 py-1 px-2 rounded-md text-sm font-semibold border-2 shadow-sm"
        :class="memberResult.score >= quiz.total_score/2 ? 'bg-teal-200 text-teal-800 border-teal-400' : memberResult.score > 0 ? 'bg-teal-100 text-teal-700 border-teal-300' : 'bg-teal-50 text-teal-600 border-teal-200'">
            {{ memberResult.score }}
        </div>
        <div v-else class="bg-slate-200 text-slate-700 py-1 px-2 rounded-md text-sm font-medium border-2 border-slate-400 shadow-sm">
            {{ 'รต' }}
        </div>
    </div>
    <div v-else class="bg-slate-200 text-slate-700 py-1 px-2 rounded-md text-sm font-medium border-2 border-slate-400 shadow-sm">
        {{ 'รต' }}
    </div>
</template>