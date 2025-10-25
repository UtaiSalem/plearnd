<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  question: {
    type: String,
    required: true,
  },
  showResult: {
    type: Boolean,
    default: false,
  },
  isCorrect: {
    type: Boolean,
    default: null,
  },
  answer: {
    type: [Number, String],
    default: null,
  },
});

const questionClass = computed(() => {
  if (!props.showResult) return 'text-gray-800';
  
  return props.isCorrect 
    ? 'text-green-600 animate-correct' 
    : 'text-red-600 animate-incorrect';
});

const displayAnswer = computed(() => {
  if (!props.showResult) return '?';
  
  if (props.isCorrect) {
    return props.answer;
  }
  
  return `${props.answer} (Incorrect)`;
});
</script>

<template>
  <div class="question-container">
    <div 
      class="question-display p-8 bg-white rounded-2xl shadow-lg transition-all duration-300"
      :class="{
        'ring-4 ring-green-400 bg-green-50': showResult && isCorrect,
        'ring-4 ring-red-400 bg-red-50': showResult && !isCorrect,
      }"
    >
      <div class="text-center">
        <div class="text-5xl font-bold mb-4" :class="questionClass">
          {{ question }} = 
        </div>
        <div class="text-4xl font-semibold" :class="questionClass">
          {{ displayAnswer }}
        </div>
      </div>
    </div>
    
    <div v-if="showResult" class="mt-4 text-center">
      <div 
        v-if="isCorrect" 
        class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full font-semibold"
      >
        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        Correct!
      </div>
      <div 
        v-else 
        class="inline-flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-full font-semibold animate-shake"
      >
        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        Incorrect!
      </div>
    </div>
  </div>
</template>

<style scoped>
.question-container {
  display: flex;
  flex-direction: column;
  align-items: center;
}

@keyframes correct {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}

@keyframes incorrect {
  0%, 100% { transform: translateX(0); }
  10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
  20%, 40%, 60%, 80% { transform: translateX(5px); }
}

.animate-correct {
  animation: correct 0.5s ease-in-out;
}

.animate-incorrect {
  animation: incorrect 0.5s ease-in-out;
}

.animate-shake {
  animation: incorrect 0.5s ease-in-out;
}
</style>