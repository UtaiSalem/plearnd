<script setup>
import { ref, nextTick, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  disabled: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: 'Enter your answer',
  },
});

const emit = defineEmits(['submit', 'input']);

const answer = ref('');
const inputRef = ref(null);

const submitAnswer = () => {
  if (answer.value !== null && answer.value !== '' && !props.disabled) {
    emit('submit', answer.value.toString());
    answer.value = '';
  }
};

const handleKeydown = (event) => {
  if (event.key === 'Enter') {
    event.preventDefault();
    submitAnswer();
  }
};

const focusInput = () => {
  nextTick(() => {
    if (inputRef.value) {
      inputRef.value.focus();
    }
  });
};

const clearInput = () => {
  answer.value = '';
};

onMounted(() => {
  focusInput();
});

defineExpose({
  focusInput,
  clearInput,
});
</script>

<template>
  <div class="answer-input-container">
    <div class="relative">
      <input
        ref="inputRef"
        v-model="answer"
        type="number"
        :disabled="disabled"
        :placeholder="placeholder"
        class="w-full px-6 py-4 text-2xl font-semibold text-center border-4 border-blue-300 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-200 transition-all duration-200"
        :class="{
          'bg-gray-100 text-gray-400 cursor-not-allowed': disabled,
          'bg-white text-gray-800': !disabled,
        }"
        @keydown="handleKeydown"
        @input="emit('input', $event.target.value)"
      />
      
      <button
        v-if="answer !== null && answer !== ''"
        @click="submitAnswer"
        :disabled="disabled"
        class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
        :class="{
          'opacity-50 cursor-not-allowed': disabled,
          'hover:bg-blue-700': !disabled,
        }"
      >
        Submit
      </button>
    </div>
    
    <div class="mt-4 text-center">
      <p class="text-sm text-gray-600">Press Enter or click Submit to answer</p>
    </div>
  </div>
</template>

<style scoped>
.answer-input-container {
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
}
</style>