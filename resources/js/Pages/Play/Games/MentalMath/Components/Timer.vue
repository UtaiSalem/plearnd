<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  timeLimit: {
    type: Number,
    required: true,
  },
  isActive: {
    type: Boolean,
    default: false,
  },
  warningThreshold: {
    type: Number,
    default: 3,
  },
});

const emit = defineEmits(['timeUp', 'warning', 'tick']);

const timeLeft = ref(props.timeLimit);
const isWarning = ref(false);
let intervalId = null;

const startTimer = () => {
  if (intervalId) clearInterval(intervalId);
  
  timeLeft.value = props.timeLimit;
  isWarning.value = false;
  
  intervalId = setInterval(() => {
    timeLeft.value--;
    emit('tick', timeLeft.value);
    
    if (timeLeft.value <= props.warningThreshold && !isWarning.value) {
      isWarning.value = true;
      emit('warning', timeLeft.value);
    }
    
    if (timeLeft.value <= 0) {
      clearInterval(intervalId);
      emit('timeUp');
    }
  }, 1000);
};

const stopTimer = () => {
  if (intervalId) {
    clearInterval(intervalId);
    intervalId = null;
  }
};

const resetTimer = () => {
  stopTimer();
  timeLeft.value = props.timeLimit;
  isWarning.value = false;
};

watch(() => props.isActive, (newValue) => {
  if (newValue) {
    startTimer();
  } else {
    stopTimer();
  }
});

watch(() => props.timeLimit, () => {
  resetTimer();
  if (props.isActive) {
    startTimer();
  }
});

onUnmounted(() => {
  stopTimer();
});
</script>

<template>
  <div class="timer-container">
    <div 
      class="timer-display flex items-center justify-center w-20 h-20 rounded-full border-4 transition-all duration-300"
      :class="{
        'border-green-500 bg-green-50': !isWarning,
        'border-red-500 bg-red-50 animate-pulse': isWarning,
      }"
    >
      <span 
        class="text-2xl font-bold"
        :class="{
          'text-green-700': !isWarning,
          'text-red-700': isWarning,
        }"
      >
        {{ timeLeft }}
      </span>
    </div>
    <div class="text-center mt-2">
      <span class="text-sm text-gray-600">seconds</span>
    </div>
  </div>
</template>

<style scoped>
.timer-container {
  display: flex;
  flex-direction: column;
  align-items: center;
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
  100% {
    transform: scale(1);
  }
}

.animate-pulse {
  animation: pulse 1s infinite;
}
</style>