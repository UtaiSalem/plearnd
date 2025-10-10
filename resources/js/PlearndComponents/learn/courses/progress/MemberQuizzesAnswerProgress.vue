<script setup>
import { computed, onMounted } from 'vue';
const props = defineProps({
    member_id: Number,
    quiz: Object,
});

const emit = defineEmits(['handleEmptyPoints']);

const member_score = computed(() => {
    return props.quiz.course_members_score.filter((member) => member.user_id === props.member_id)[0];
})

onMounted(() => {
    if (!member_score.value) {
        emit('handleEmptyPoints');
    }
});

</script>

<template>
    <div>
        <div v-if="member_score" class=" text-white py-1 px-2 max-w-fit rounded-md text-xs font-semibold"
            :class="member_score.score >= quiz.total_score/2 ? 'bg-green-700' : member_score.score > 0 ? 'bg-orange-400' : 'bg-red-500'">
            {{ member_score.score }}
        </div>
    </div>
</template>
