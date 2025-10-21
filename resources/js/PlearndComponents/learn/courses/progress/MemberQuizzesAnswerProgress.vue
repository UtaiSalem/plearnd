<script setup>
import { computed, onMounted, watch } from 'vue';
const props = defineProps({
    member_id: Number,
    quiz: Object,
});

const emit = defineEmits(['handleEmptyPoints']);

// Optimize member score lookup with a more efficient approach
const member_score = computed(() => {
    const scores = props.quiz.course_members_score;
    if (!scores || !scores.length) return null;
    
    // Use find instead of filter for better performance
    return scores.find((member) => member.user_id === props.member_id);
})

// Watch for changes in member_score to emit errors
watch(member_score, (newScore) => {
    if (!newScore) {
        emit('handleEmptyPoints');
    }
}, { immediate: true });

</script>

<template>
    <div>
        <div v-if="member_score" class="text-gray-900 py-1 px-2 max-w-fit rounded-md text-sm font-semibold border-2 shadow-sm"
            :class="member_score.score >= quiz.total_score/2 ? 'bg-emerald-200 text-emerald-800 border-emerald-400' : member_score.score > 0 ? 'bg-emerald-100 text-emerald-700 border-emerald-300' : 'bg-emerald-50 text-emerald-600 border-emerald-200'">
            {{ member_score.score }}
        </div>
    </div>
</template>
