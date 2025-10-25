<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
  isVisible: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['close']);

const leaderboard = ref([]);
const selectedDifficulty = ref('all');
const loading = ref(false);

const difficulties = [
  { value: 'all', label: 'All Levels' },
  { value: 'easy', label: 'Easy' },
  { value: 'medium', label: 'Medium' },
  { value: 'hard', label: 'Hard' },
];

const filteredLeaderboard = computed(() => {
  if (selectedDifficulty.value === 'all') {
    return leaderboard.value;
  }
  return leaderboard.value.filter(entry => entry.difficulty === selectedDifficulty.value);
});

const fetchLeaderboard = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/play/game/mental-math/api/leaderboard', {
      params: {
        difficulty: selectedDifficulty.value,
        limit: 10,
      },
    });
    leaderboard.value = response.data;
  } catch (error) {
    console.error('Error fetching leaderboard:', error);
  } finally {
    loading.value = false;
  }
};

const getDifficultyColor = (difficulty) => {
  switch (difficulty) {
    case 'easy': return 'bg-green-100 text-green-800';
    case 'medium': return 'bg-yellow-100 text-yellow-800';
    case 'hard': return 'bg-red-100 text-red-800';
    default: return 'bg-gray-100 text-gray-800';
  }
};

const getRankIcon = (rank) => {
  switch (rank) {
    case 1: return '🥇';
    case 2: return '🥈';
    case 3: return '🥉';
    default: return `#${rank}`;
  }
};

onMounted(() => {
  if (props.isVisible) {
    fetchLeaderboard();
  }
});

const refreshLeaderboard = () => {
  fetchLeaderboard();
};
</script>

<template>
  <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <h2 class="text-2xl font-bold text-gray-800">Leaderboard</h2>
          <button 
            @click="emit('close')"
            class="text-gray-500 hover:text-gray-700 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <div class="mt-4 flex flex-wrap gap-2">
          <button
            v-for="difficulty in difficulties"
            :key="difficulty.value"
            @click="selectedDifficulty = difficulty.value; refreshLeaderboard()"
            class="px-4 py-2 rounded-lg font-medium transition-colors"
            :class="{
              'bg-blue-600 text-white': selectedDifficulty === difficulty.value,
              'bg-gray-200 text-gray-700 hover:bg-gray-300': selectedDifficulty !== difficulty.value,
            }"
          >
            {{ difficulty.label }}
          </button>
        </div>
      </div>
      
      <div class="p-6 overflow-y-auto max-h-[60vh]">
        <div v-if="loading" class="text-center py-8">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <p class="mt-2 text-gray-600">Loading leaderboard...</p>
        </div>
        
        <div v-else-if="filteredLeaderboard.length === 0" class="text-center py-8">
          <p class="text-gray-600">No scores yet. Be the first to play!</p>
        </div>
        
        <div v-else class="space-y-3">
          <div
            v-for="(entry, index) in filteredLeaderboard"
            :key="entry.id"
            class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <div class="flex items-center space-x-4">
              <div class="text-2xl font-bold text-gray-700 w-12 text-center">
                {{ getRankIcon(index + 1) }}
              </div>
              
              <div>
                <div class="font-semibold text-gray-800">{{ entry.player_name }}</div>
                <div class="text-sm text-gray-600">{{ entry.created_at }}</div>
              </div>
            </div>
            
            <div class="flex items-center space-x-4">
              <div class="text-right">
                <div class="text-xl font-bold text-blue-600">{{ entry.score }}</div>
                <div class="text-xs text-gray-600">points</div>
              </div>
              
              <div class="text-right">
                <div class="text-sm font-semibold text-gray-700">{{ entry.combo }}x</div>
                <div class="text-xs text-gray-600">combo</div>
              </div>
              
              <div class="text-right">
                <div class="text-sm font-semibold text-gray-700">{{ entry.accuracy }}%</div>
                <div class="text-xs text-gray-600">accuracy</div>
              </div>
              
              <div class="px-2 py-1 rounded-full text-xs font-medium" :class="getDifficultyColor(entry.difficulty)">
                {{ entry.difficulty }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.leaderboard-container {
  display: flex;
  flex-direction: column;
  height: 100%;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>