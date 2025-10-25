<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import axios from 'axios';
import Timer from './Components/Timer.vue';
import QuestionDisplay from './Components/QuestionDisplay.vue';
import ScoreDisplay from './Components/ScoreDisplay.vue';
import Leaderboard from './Components/Leaderboard.vue';
import AnswerInput from './Components/AnswerInput.vue';
import LoadingSpinner from './Components/LoadingSpinner.vue';
import GameLayout from '@/Layouts/GameLayout.vue';

// Game state
const gameState = ref('home'); // home, countdown, playing, paused, gameover
const difficulty = ref('easy'); // easy, medium, hard
const isPracticeMode = ref(false);
const isDailyChallenge = ref(false);

// Player info
const playerName = ref('');
const showNameInput = ref(false);

// Game data
const currentQuestion = ref(null);
const userAnswer = ref(null);
const score = ref(0);
const combo = ref(0);
const questionsAnswered = ref(0);
const correctAnswers = ref(0);
const gameStartTime = ref(null);
const questionStartTime = ref(null);
const totalGameTime = ref(0);

// UI state
const showResult = ref(false);
const isCorrect = ref(null);
const showLeaderboard = ref(false);
const timerActive = ref(false);
const timeLimit = ref(10);
const showThemeDropdown = ref(false);
const isLoading = ref(false);
const countdown = ref(0);
const answerInputRef = ref(null);

// Sound effects
const playSound = (type) => {
  // Create audio context for sound effects
  const audioContext = new (window.AudioContext || window.webkitAudioContext)();
  const oscillator = audioContext.createOscillator();
  const gainNode = audioContext.createGain();
  
  oscillator.connect(gainNode);
  gainNode.connect(audioContext.destination);
  
  switch (type) {
    case 'correct':
      oscillator.frequency.value = 800;
      gainNode.gain.value = 0.3;
      oscillator.start();
      oscillator.stop(audioContext.currentTime + 0.1);
      break;
    case 'incorrect':
      oscillator.frequency.value = 200;
      gainNode.gain.value = 0.3;
      oscillator.start();
      oscillator.stop(audioContext.currentTime + 0.2);
      break;
    case 'warning':
      oscillator.frequency.value = 400;
      gainNode.gain.value = 0.2;
      oscillator.start();
      oscillator.stop(audioContext.currentTime + 0.05);
      break;
  }
};

// Difficulty settings
const difficultySettings = {
  easy: { timeLimit: 10, label: 'Easy', color: 'green' },
  medium: { timeLimit: 7, label: 'Medium', color: 'yellow' },
  hard: { timeLimit: 5, label: 'Hard', color: 'red' },
};

// Computed properties
const accuracy = computed(() => {
  if (questionsAnswered.value === 0) return 0;
  return Math.round((correctAnswers.value / questionsAnswered.value) * 100);
});

const currentDifficultySettings = computed(() => {
  return difficultySettings[difficulty.value];
});

// Game methods
const startGame = (mode = 'normal') => {
  if (mode === 'practice') {
    isPracticeMode.value = true;
    isDailyChallenge.value = false;
  } else if (mode === 'daily') {
    isPracticeMode.value = false;
    isDailyChallenge.value = true;
  } else {
    isPracticeMode.value = false;
    isDailyChallenge.value = false;
  }
  
  if (!playerName.value.trim()) {
    showNameInput.value = true;
    return;
  }
  
  resetGame();
  startCountdown();
};

const resetGame = () => {
  score.value = 0;
  combo.value = 0;
  questionsAnswered.value = 0;
  correctAnswers.value = 0;
  totalGameTime.value = 0;
  currentQuestion.value = null;
  userAnswer.value = null;
  showResult.value = false;
  isCorrect.value = null;
  timerActive.value = false;
  countdown.value = 0;
};

const startCountdown = () => {
  gameState.value = 'countdown';
  countdown.value = 3;
  
  const countdownInterval = setInterval(() => {
    countdown.value--;
    
    if (countdown.value <= 0) {
      clearInterval(countdownInterval);
      gameState.value = 'playing';
      gameStartTime.value = Date.now();
      fetchNewQuestion();
    }
  }, 1000);
};

const fetchNewQuestion = async () => {
  try {
    isLoading.value = true;
    showResult.value = false;
    timerActive.value = false;
    
    const response = await axios.get('/play/game/mental-math/api/question', {
      params: {
        difficulty: difficulty.value,
        daily_challenge: isDailyChallenge.value,
      },
    });
    
    currentQuestion.value = response.data;
    timeLimit.value = response.data.time_limit;
    
    await nextTick();
    timerActive.value = true;
    questionStartTime.value = Date.now();
    isLoading.value = false;
    
    // Focus the answer input
    if (answerInputRef.value) {
      answerInputRef.value.focusInput();
    }
  } catch (error) {
    console.error('Error fetching question:', error);
    isLoading.value = false;
    // Show error message to user
    alert('Failed to load question. Please try again.');
    // Try to fetch a new question after a short delay
    setTimeout(() => {
      if (gameState.value === 'playing') {
        fetchNewQuestion();
      }
    }, 2000);
  }
};

const submitAnswer = (answer) => {
  if (!currentQuestion.value || showResult.value) return;
  
  timerActive.value = false;
  userAnswer.value = parseInt(answer);
  const correct = userAnswer.value === currentQuestion.value.answer;
  isCorrect.value = correct;
  showResult.value = true;
  
  questionsAnswered.value++;
  
  if (correct) {
    correctAnswers.value++;
    combo.value++;
    
    // Calculate score based on speed and difficulty
    const timeTaken = (Date.now() - questionStartTime.value) / 1000;
    const timeBonus = Math.max(0, timeLimit.value - timeTaken) * 10;
    const difficultyBonus = difficulty.value === 'easy' ? 10 : difficulty.value === 'medium' ? 20 : 30;
    const comboBonus = combo.value > 1 ? (combo.value - 1) * 5 : 0;
    const dailyBonus = currentQuestion.value.bonus_points || 0;
    
    const points = Math.round(10 + timeBonus + difficultyBonus + comboBonus + dailyBonus);
    score.value += points;
    
    playSound('correct');
  } else {
    combo.value = 0;
    playSound('incorrect');
  }
  
  // Auto-advance to next question after a short delay
  setTimeout(() => {
    if (gameState.value === 'playing') {
      fetchNewQuestion();
    }
  }, 1500);
};

const handleTimeUp = () => {
  if (!currentQuestion.value || showResult.value) return;
  
  timerActive.value = false;
  userAnswer.value = null;
  isCorrect.value = false;
  showResult.value = true;
  combo.value = 0;
  questionsAnswered.value++;
  
  playSound('incorrect');
  
  setTimeout(() => {
    if (gameState.value === 'playing') {
      fetchNewQuestion();
    }
  }, 1500);
};

const handleTimerWarning = () => {
  playSound('warning');
};

const pauseGame = () => {
  gameState.value = 'paused';
  timerActive.value = false;
};

const resumeGame = () => {
  gameState.value = 'playing';
  timerActive.value = true;
};

const endGame = async () => {
  gameState.value = 'gameover';
  timerActive.value = false;
  totalGameTime.value = Math.round((Date.now() - gameStartTime.value) / 1000);
  
  if (!isPracticeMode.value && questionsAnswered.value > 0) {
    try {
      await axios.post('/play/game/mental-math/api/score', {
        player_name: playerName.value,
        score: score.value,
        difficulty: difficulty.value,
        combo: combo.value,
        questions_answered: questionsAnswered.value,
        accuracy: accuracy.value,
        time_spent: totalGameTime.value,
        is_practice_mode: isPracticeMode.value,
        is_daily_challenge: isDailyChallenge.value,
      });
    } catch (error) {
      console.error('Error saving score:', error);
      // Show error message but don't block the game flow
      alert('Failed to save your score. Please check your internet connection.');
    }
  }
};

const changeDifficulty = (newDifficulty) => {
  difficulty.value = newDifficulty;
  if (gameState.value === 'playing') {
    endGame();
  }
};

const setPlayerName = () => {
  if (playerName.value.trim()) {
    showNameInput.value = false;
    startGame(isPracticeMode.value ? 'practice' : isDailyChallenge.value ? 'daily' : 'normal');
  }
};

// Theme management
const currentTheme = ref('classroom');
const themes = {
  classroom: {
    name: 'Classroom',
    bg: 'bg-blue-50',
    primary: 'blue',
    secondary: 'gray',
  },
  space: {
    name: 'Space',
    bg: 'bg-purple-900',
    primary: 'purple',
    secondary: 'indigo',
  },
  ninja: {
    name: 'Ninja',
    bg: 'bg-gray-900',
    primary: 'red',
    secondary: 'gray',
  },
};

const changeTheme = (themeName) => {
  currentTheme.value = themeName;
};

onMounted(() => {
  // Check for daily challenge
  const today = new Date().toDateString();
  const lastDailyChallenge = localStorage.getItem('lastDailyChallenge');
  if (lastDailyChallenge !== today) {
    // Reset daily challenge for today
    localStorage.setItem('lastDailyChallenge', today);
  }
  
  // Add keyboard shortcuts
  document.addEventListener('keydown', handleKeyPress);
});

const handleKeyPress = (event) => {
  // Escape key to pause/resume
  if (event.key === 'Escape') {
    if (gameState.value === 'playing') {
      pauseGame();
    } else if (gameState.value === 'paused') {
      resumeGame();
    }
  }
  
  // Enter key to submit answer (handled in AnswerInput component)
  // Space key to start game (only on home screen)
  if (event.key === ' ' && gameState.value === 'home') {
    event.preventDefault();
    startGame('normal');
  }
};
</script>

<template>
  <GameLayout>
    <div class="min-h-screen transition-all duration-500" :class="themes[currentTheme].bg">
    <!-- Home Screen -->
    <div v-if="gameState === 'home'" class="container mx-auto px-4 py-8">
      <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-5xl font-bold mb-4" :class="currentTheme === 'space' || currentTheme === 'ninja' ? 'text-white' : 'text-gray-800'">Mental Math Game</h1>
        <p class="text-xl mb-8" :class="currentTheme === 'space' || currentTheme === 'ninja' ? 'text-gray-300' : 'text-gray-600'">Test your mental calculation skills!</p>
        
        <!-- Player Name Input -->
        <div v-if="showNameInput" class="mb-8">
          <div class="max-w-md mx-auto">
            <input
              v-model="playerName"
              type="text"
              placeholder="Enter your name"
              class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
              @keyup.enter="setPlayerName"
            />
            <button
              @click="setPlayerName"
              class="mt-4 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors"
            >
              Start Game
            </button>
          </div>
        </div>
        
        <!-- Game Modes -->
        <div v-else class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <button
              @click="startGame('normal')"
              class="p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
            >
              <div class="text-3xl mb-2">🎮</div>
              <h3 class="text-xl font-semibold mb-2">Normal Mode</h3>
              <p class="text-gray-600">Classic mental math challenge</p>
            </button>
            
            <button
              @click="startGame('practice')"
              class="p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
            >
              <div class="text-3xl mb-2">📚</div>
              <h3 class="text-xl font-semibold mb-2">Practice Mode</h3>
              <p class="text-gray-600">No time limit, practice at your own pace</p>
            </button>
            
            <button
              @click="startGame('daily')"
              class="p-6 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
            >
              <div class="text-3xl mb-2">📅</div>
              <h3 class="text-xl font-semibold mb-2">Daily Challenge</h3>
              <p class="text-purple-100">Special challenge with bonus points!</p>
            </button>
          </div>
          
          <!-- Difficulty Selection -->
          <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4" :class="currentTheme === 'space' || currentTheme === 'ninja' ? 'text-gray-300' : 'text-gray-700'">Select Difficulty:</h3>
            <div class="flex justify-center space-x-4">
              <button
                v-for="(settings, level) in difficultySettings"
                :key="level"
                @click="changeDifficulty(level)"
                class="px-6 py-3 rounded-lg font-semibold transition-all duration-200"
                :class="{
                  'bg-green-600 text-white': difficulty === level && level === 'easy',
                  'bg-yellow-600 text-white': difficulty === level && level === 'medium',
                  'bg-red-600 text-white': difficulty === level && level === 'hard',
                  'bg-gray-200 text-gray-700 hover:bg-gray-300': difficulty !== level,
                }"
              >
                {{ settings.label }}
              </button>
            </div>
          </div>
          
          <!-- Other Actions -->
          <div class="flex justify-center space-x-4">
            <button
              @click="showLeaderboard = true"
              class="px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-colors"
            >
              🏆 Leaderboard
            </button>
            
            <!-- Theme Selector -->
            <div class="relative inline-block">
              <button
                @click="showThemeDropdown = !showThemeDropdown"
                class="px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-colors"
              >
                🎨 Theme: {{ themes[currentTheme].name }}
              </button>
              <div v-if="showThemeDropdown" class="absolute top-full mt-2 left-0 bg-white rounded-lg shadow-lg overflow-hidden z-10">
                <button
                  v-for="(theme, name) in themes"
                  :key="name"
                  @click="changeTheme(name); showThemeDropdown = false"
                  class="block w-full px-4 py-2 text-left hover:bg-gray-100 text-gray-800"
                >
                  {{ theme.name }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Countdown Screen -->
    <div v-else-if="gameState === 'countdown'" class="container mx-auto px-4 py-8">
      <div class="max-w-4xl mx-auto text-center">
        <div class="mb-8">
          <h2 class="text-4xl font-bold mb-4" :class="currentTheme === 'space' || currentTheme === 'ninja' ? 'text-white' : 'text-gray-800'">Get Ready!</h2>
          <div class="text-8xl font-bold mb-4" :class="{
            'text-blue-600': currentTheme === 'classroom',
            'text-purple-400': currentTheme === 'space',
            'text-red-400': currentTheme === 'ninja',
          }">
            {{ countdown > 0 ? countdown : 'GO!' }}
          </div>
          <p class="text-xl" :class="currentTheme === 'space' || currentTheme === 'ninja' ? 'text-gray-300' : 'text-gray-600'">
            {{ isPracticeMode ? 'Practice Mode' : isDailyChallenge ? 'Daily Challenge' : 'Normal Mode' }} - {{ currentDifficultySettings.label }}
          </p>
        </div>
      </div>
    </div>
    
    <!-- Game Screen -->
    <div v-else-if="gameState === 'playing' || gameState === 'paused'" class="container mx-auto px-4 py-8">
      <div class="max-w-4xl mx-auto">
        <!-- Game Header -->
        <div class="flex justify-between items-center mb-6">
          <div class="flex items-center space-x-4">
            <button
              @click="gameState === 'playing' ? pauseGame() : resumeGame()"
              class="px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-colors"
            >
              {{ gameState === 'playing' ? '⏸️ Pause' : '▶️ Resume' }}
            </button>
            
            <button
              @click="endGame"
              class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors"
            >
              🛑 End Game
            </button>
          </div>
          
          <div class="text-right">
            <div class="text-sm" :class="currentTheme === 'space' || currentTheme === 'ninja' ? 'text-gray-300' : 'text-gray-600'">Player: {{ playerName }}</div>
            <div class="text-sm" :class="currentTheme === 'space' || currentTheme === 'ninja' ? 'text-gray-300' : 'text-gray-600'">
              Mode: {{ isPracticeMode ? 'Practice' : isDailyChallenge ? 'Daily Challenge' : 'Normal' }}
            </div>
            <div class="text-sm font-semibold" :class="{
              'text-green-600': currentDifficultySettings.color === 'green' && (currentTheme === 'classroom'),
              'text-yellow-600': currentDifficultySettings.color === 'yellow' && (currentTheme === 'classroom'),
              'text-red-600': currentDifficultySettings.color === 'red' && (currentTheme === 'classroom'),
              'text-green-400': currentDifficultySettings.color === 'green' && (currentTheme === 'space' || currentTheme === 'ninja'),
              'text-yellow-400': currentDifficultySettings.color === 'yellow' && (currentTheme === 'space' || currentTheme === 'ninja'),
              'text-red-400': currentDifficultySettings.color === 'red' && (currentTheme === 'space' || currentTheme === 'ninja'),
            }">
              Difficulty: {{ currentDifficultySettings.label }}
            </div>
          </div>
        </div>
        
        <!-- Score Display -->
        <ScoreDisplay
          :score="score"
          :combo="combo"
          :questions-answered="questionsAnswered"
          :accuracy="accuracy"
        />
        
        <!-- Timer (only show if not practice mode) -->
        <div v-if="!isPracticeMode" class="flex justify-center mb-6">
          <Timer
            :time-limit="timeLimit"
            :is-active="timerActive"
            @time-up="handleTimeUp"
            @warning="handleTimerWarning"
          />
        </div>
        
        <!-- Loading State -->
        <div v-if="isLoading" class="mb-8">
          <LoadingSpinner size="large" text="Loading question..." />
        </div>
        
        <!-- Question Display -->
        <div v-else-if="currentQuestion" class="mb-8">
          <QuestionDisplay
            :question="currentQuestion.question"
            :show-result="showResult"
            :is-correct="isCorrect"
            :answer="userAnswer"
          />
        </div>
        
        <!-- Answer Input -->
        <div v-if="currentQuestion && !showResult && !isLoading" class="mb-8">
          <AnswerInput
            ref="answerInputRef"
            :disabled="gameState === 'paused'"
            @submit="submitAnswer"
          />
        </div>
        
        <!-- Daily Challenge Banner -->
        <div v-if="isDailyChallenge" class="mt-8 p-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg text-center">
          <div class="text-lg font-semibold">Daily Challenge</div>
          <div class="text-sm">Complete today's special challenge for bonus points!</div>
        </div>
      </div>
    </div>
    
    <!-- Game Over Screen -->
    <div v-else-if="gameState === 'gameover'" class="container mx-auto px-4 py-8">
      <div class="max-w-2xl mx-auto text-center">
        <h2 class="text-4xl font-bold mb-6" :class="currentTheme === 'space' || currentTheme === 'ninja' ? 'text-white' : 'text-gray-800'">Game Over!</h2>
        
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
          <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
              <div class="text-3xl font-bold text-blue-600">{{ score }}</div>
              <div class="text-gray-600">Final Score</div>
            </div>
            <div>
              <div class="text-3xl font-bold text-green-600">{{ accuracy }}%</div>
              <div class="text-gray-600">Accuracy</div>
            </div>
            <div>
              <div class="text-2xl font-bold text-purple-600">{{ combo }}</div>
              <div class="text-gray-600">Best Combo</div>
            </div>
            <div>
              <div class="text-2xl font-bold text-orange-600">{{ questionsAnswered }}</div>
              <div class="text-gray-600">Questions</div>
            </div>
          </div>
          
          <div class="text-lg" :class="currentTheme === 'space' || currentTheme === 'ninja' ? 'text-gray-300' : 'text-gray-700'">
            Great job, {{ playerName }}! You played for {{ totalGameTime }} seconds.
          </div>
        </div>
        
        <div class="flex justify-center space-x-4">
          <button
            @click="gameState = 'home'"
            class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors"
          >
            🏠 Home
          </button>
          
          <button
            @click="startGame(isPracticeMode ? 'practice' : isDailyChallenge ? 'daily' : 'normal')"
            class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors"
          >
            🔄 Play Again
          </button>
          
          <button
            @click="showLeaderboard = true"
            class="px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-colors"
          >
            🏆 Leaderboard
          </button>
        </div>
      </div>
    </div>
    
    <!-- Leaderboard Modal -->
    <Leaderboard
      :is-visible="showLeaderboard"
      @close="showLeaderboard = false"
    />
    </div>
  </GameLayout>
</template>

<style scoped>
.container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

/* Theme-specific styles */
.bg-purple-900 {
  background: linear-gradient(135deg, #1a1a2e, #16213e);
}

.bg-gray-900 {
  background: linear-gradient(135deg, #0f0f0f, #1a1a1a);
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.container > div {
  animation: fadeIn 0.5s ease-out;
}

/* Responsive design */
@media (max-width: 768px) {
  .text-5xl {
    font-size: 3rem;
  }
  
  .grid-cols-3 {
    grid-template-columns: 1fr;
  }
  
  .grid-cols-2 {
    grid-template-columns: 1fr;
  }
}
</style>
