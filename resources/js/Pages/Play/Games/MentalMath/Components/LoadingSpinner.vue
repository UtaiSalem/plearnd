<script setup>
const props = defineProps({
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value),
  },
  text: {
    type: String,
    default: 'Loading...',
  },
});

const sizeClasses = {
  small: 'w-6 h-6',
  medium: 'w-8 h-8',
  large: 'w-12 h-12',
};
</script>

<template>
  <div class="flex flex-col items-center justify-center p-4">
    <div 
      class="animate-spin rounded-full border-4 border-blue-200 border-t-blue-600"
      :class="sizeClasses[size]"
    ></div>
    <p v-if="text" class="mt-2 text-gray-600">{{ text }}</p>
  </div>
</template>

<style scoped>
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>