<script setup>
import { computed } from 'vue';

const props = defineProps({
    member_id: Number,
    user_id: Number,
    assignment: Object,
    answers: Array,
});

const emit = defineEmits(['handleEmptyPoints']);

// Pre-filter answers for this member to avoid repeated filtering
const memberAnswer = computed(() => {
    if (!props.answers || !props.answers.length) return null;
    return props.answers.find(answer => answer.student_id === props.user_id && answer.assignment_id === props.assignment.id);
});

// Check for empty answer and emit error
const hasAnswer = computed(() => {
    return memberAnswer.value !== null;
});

// Watch for changes and emit error if needed
if (!hasAnswer.value) {
    emit('handleEmptyPoints');
}
</script>

<template>
    <div v-if="memberAnswer" class="text-center">
        <div v-if="memberAnswer.points" class="text-gray-900 py-1 px-2 rounded-md text-sm font-semibold border-2 shadow-sm"
        :class="memberAnswer.points >= assignment.points/2 ? 'bg-teal-200 text-teal-800 border-teal-400' : memberAnswer.points > 0 ? 'bg-teal-100 text-teal-700 border-teal-300' : 'bg-teal-50 text-teal-600 border-teal-200'">
            {{ memberAnswer.points }}
        </div>
        <div v-else class="bg-slate-200 text-slate-700 py-1 px-2 rounded-md text-sm font-medium border-2 border-slate-400 shadow-sm">
            {{ 'รต' }}
        </div>
    </div>
    <div v-else class="bg-slate-200 text-slate-700 py-1 px-2 rounded-md text-sm font-medium border-2 border-slate-400 shadow-sm">
        {{ 'รต' }}
    </div>
</template>