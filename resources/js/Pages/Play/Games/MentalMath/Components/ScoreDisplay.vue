<script setup>
import { computed } from 'vue';

const props = defineProps({
  score: {
    type: Number,
    default: 0,
  },
  combo: {
    type: Number,
    default: 0,
  },
  questionsAnswered: {
    type: Number,
    default: 0,
  },
  accuracy: {
    type: Number,
    default: 0,
  },
});

const comboClass = computed(() => {
  if (props.combo >= 10) return 'text-purple-600 animate-pulse';
  if (props.combo >= 5) return 'text-blue-600';
  if (props.combo >= 3) return 'text-green-600';
  return 'text-gray-600';
});

const comboText = computed(() => {
  if (props.combo >= 10) return 'ON FIRE! 🔥';
  if (props.combo >= 5) return 'GREAT! ⚡';
  if (props.combo >= 3) return 'GOOD! 👍';
  return '';
});

const accuracyColor = computed(() => {
  if (props.accuracy >= 90) return 'text-green-600';
  if (props.accuracy >= 70) return 'text-yellow-600';
  return 'text-red-600';
});
</script>

<template>
  <div class="score-container">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md">
      <div class="grid grid-cols-2 gap-4">
        <div class="text-center">
          <div class="text-3xl font-bold text-blue-600">{{ score }}</div>
          <div class="text-sm text-gray-600">Score</div>
        </div>
        
        <div class="text-center">
          <div class="text-3xl font-bold" :class="comboClass">{{ combo }}</div>
          <div class="text-sm text-gray-600">Combo</div>
          <div v-if="comboText" class="text-xs font-semibold mt-1" :class="comboClass">
            {{ comboText }}
          </div>
        </div>
        
        <div class="text-center">
          <div class="text-2xl font-semibold text-gray-700">{{ questionsAnswered }}</div>
          <div class="text-sm text-gray-600">Questions</div>
        </div>
        
        <div class="text-center">
          <div class="text-2xl font-semibold" :class="accuracyColor">
            {{ accuracy }}%
          </div>
          <div class="text-sm text-gray-600">Accuracy</div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.score-container {
  display: flex;
  justify-content: center;
  margin-bottom: 1rem;
}

@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}

.animate-pulse {
  animation: pulse 1s infinite;
}
</style>