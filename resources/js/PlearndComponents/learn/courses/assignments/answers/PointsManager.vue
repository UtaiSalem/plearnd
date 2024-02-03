<script setup>
import { ref, computed } from 'vue';
import CyanSeaButton from '@/PlearndComponents/accessories/CyanSeaButton.vue';

const props = defineProps({
    assignment: Object,
    answer: Object,
});

const emit = defineEmits(['set-points']);
const oldPoints = ref(props.answer.points);
const unSave = ref(false);

// const unSave = computed(() => {
//     return props.answer.points !== newPoints.value;
// });

const handleInput = () => {
    // unSave.value = true;
    unSave.value = props.answer.points !== oldPoints.value;
};

const handleSave = () => {
    emit('set-points', props.answer.points);
    unSave.value = false;
};
</script>

<template>
    <div class="flex justify-end md:justify-between items-center space-x-4">
        <input :id="'minmax-range-' + answer.id" type="range" min="0" :max="assignment.points" 
            v-model="answer.points" @input="handleInput"
            class="hidden sm:flex w-full h-2 bg-indigo-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
        <div class="flex items-center">
            <input :id="'assignment-scores-input-' + answer.id" type="number" min="0" :max="assignment.points"
                v-model="answer.points" @input="handleInput" :value="answer.points||0"
                class=" w-[64px] h-8 px-2 text-sm placeholder-transparent transition-all border rounded outline-none focus-visible:outline-none peer border-slate-200 text-slate-500 autofill:bg-white invalid:border-pink-500 invalid:text-pink-500 focus:border-violet-500 focus:outline-none invalid:focus:border-pink-500 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400" />
            <CyanSeaButton @click.prevent="handleSave"
                :class="!answer.points ? 'bg-slate-400 hover:bg-slate-600': unSave ? 'bg-rose-500 hover:bg-rose-600':'bg-cyan-500 hover:bg-cyan-600'"
                class="flex justify-center space-x-1 items-center w-16 h-8 mx-4">
                <!-- <small>{{ answer.points || 0 }}</small> -->
                <small>บันทึก</small>
            </CyanSeaButton>
            <p>คะแนน</p>
        </div>
    </div>
</template>
