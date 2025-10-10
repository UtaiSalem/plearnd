import { ref, onMounted, onUnmounted } from 'vue';

export default function useScreenSize() {
  const screenWidth = ref(window.innerWidth);
  const screenHeight = ref(window.innerHeight);

  const handleResize = () => {
    screenWidth.value = window.innerWidth;
    screenHeight.value = window.innerHeight;
  };

  onMounted(() => {
    window.addEventListener('resize', handleResize);
  });

  onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
  });

  return {
    screenWidth,
    screenHeight,
  };
}