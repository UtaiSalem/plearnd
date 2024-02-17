<script setup>
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
    member_info: Object,
    assignment: Object,
    answers: Object,
});

const emit = defineEmits(['handleEmptyPoints']);

onMounted(() => {
    if (!props.answers.length) {
        emit('handleEmptyPoints');
    }
});

</script>

<template>
    <div v-if="answers.length"  class="text-center">
        <div v-for="(answer) in answers" :key="answer.id" class="">
            <div v-if="answer.points" class=" text-white py-1 px-2 rounded-md text-xs font-semibold"
                :class="answer.points >= assignment.points/2 ? 'bg-green-400' : answer.points > 0 ? 'bg-orange-400' : 'bg-red-500'">
                <span>{{ answer.points }}</span>
            </div>
            <div v-else class="bg-slate-500 text-white py-1 px-2 rounded-md text-xs">
                <span>{{ 'รต' }}</span>
            </div>
        </div>
    </div>
</template>
