<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import GameLayout from '@/Layouts/GameLayout.vue'

// State
const searchQuery = ref('')
const selectedCategory = ref('all')
const isLoading = ref(true)

// Games data
const games = ref([
  {
    id: 1,
    title: 'เกมทายตัวเลข',
    description: 'ทายตัวเลขระหว่าง 1-100 ทดสอบความสามารถในการคาดเดา',
    thumbnail: '/storage/images/games/guessing-number.svg',
    icon: '🔢',
    category: 'logic',
    route: 'game.quessing_number',
    color: '#00BFFF',
    difficulty: 'ง่าย',
    players: '1 คน',
    rating: 4.5
  },
  {
    id: 2,
    title: 'OX Game',
    description: 'เกม OX คลาสสิกสำหรับ 2 ผู้เล่น ลองสร้างแถว 3 ช่องเพื่อชนะ',
    thumbnail: '/storage/images/games/xo-game.svg',
    icon: '⭕',
    category: 'strategy',
    route: 'game.xo',
    color: '#FFD700',
    difficulty: 'ง่าย',
    players: '2 คน',
    rating: 4.2
  },
  {
    id: 3,
    title: 'Snake Game',
    description: 'เกมงูคลาสสิก ควบคุมงูให้กินอาหารและเติบโตยาวขึ้น',
    thumbnail: '/storage/images/games/snake-game.svg',
    icon: '🐍',
    category: 'arcade',
    route: 'game.snake',
    color: '#32CD32',
    difficulty: 'ปานกลาง',
    players: '1 คน',
    rating: 4.7
  },
  {
    id: 4,
    title: 'Mental Math',
    description: 'ทดสอบทักษะคณิตศาสตร์ในใจ ตอบคำถามคณิตศาสตร์ให้เร็วที่สุด',
    thumbnail: '/storage/images/games/mental-math.svg',
    icon: '🧮',
    category: 'education',
    route: 'mental-math',
    color: '#FF69B4',
    difficulty: 'ปานกลาง',
    players: '1 คน',
    rating: 4.8
  }
])

// Categories
const categories = ref([
  { id: 'all', name: 'ทั้งหมด', color: 'bg-gray-500' },
  { id: 'logic', name: 'ตรรกะ', color: 'bg-blue-500' },
  { id: 'strategy', name: 'กลยุทธ์', color: 'bg-purple-500' },
  { id: 'arcade', name: 'อาร์เคด', color: 'bg-green-500' },
  { id: 'education', name: 'การศึกษา', color: 'bg-pink-500' }
])

// Computed properties
const filteredGames = computed(() => {
  let result = games.value

  // Filter by category
  if (selectedCategory.value !== 'all') {
    result = result.filter(game => game.category === selectedCategory.value)
  }

  // Filter by search query
  if (searchQuery.value.trim() !== '') {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(game => 
      game.title.toLowerCase().includes(query) || 
      game.description.toLowerCase().includes(query)
    )
  }

  return result
})

// Methods
const playGame = (gameRoute) => {
  router.visit(route(gameRoute))
}

const viewDetails = (game) => {
  // For now, just show an alert. In a real app, this could open a modal or navigate to a details page
  alert(`รายละเอียดเกม: ${game.title}\n\n${game.description}\n\nความยาก: ${game.difficulty}\nจำนวนผู้เล่น: ${game.players}\nคะแนน: ${game.rating}/5`)
}

const getCategoryColor = (categoryId) => {
  const category = categories.value.find(cat => cat.id === categoryId)
  return category ? category.color : 'bg-gray-500'
}

const getDifficultyColor = (difficulty) => {
  switch (difficulty) {
    case 'ง่าย': return 'text-green-600 bg-green-100'
    case 'ปานกลาง': return 'text-yellow-600 bg-yellow-100'
    case 'ยาก': return 'text-red-600 bg-red-100'
    default: return 'text-gray-600 bg-gray-100'
  }
}

const renderStars = (rating) => {
  const fullStars = Math.floor(rating)
  const hasHalfStar = rating % 1 !== 0
  const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0)
  
  let stars = ''
  
  // Full stars
  for (let i = 0; i < fullStars; i++) {
    stars += '★'
  }
  
  // Half star
  if (hasHalfStar) {
    stars += '☆'
  }
  
  // Empty stars
  for (let i = 0; i < emptyStars; i++) {
    stars += '☆'
  }
  
  return stars
}

onMounted(() => {
  // Simulate loading
  setTimeout(() => {
    isLoading.value = false
  }, 500)
})
</script>

<template>
  <GameLayout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-purple-50 py-8 px-4">
    <!-- Header -->
    <div class="max-w-7xl mx-auto mb-8">
      <div class="text-center mb-8">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">ศูนย์รวมเกม</h1>
        <p class="text-lg text-gray-600">เลือกเกมที่คุณสนใจและเริ่มต้นความสนุกได้เลย!</p>
      </div>

      <!-- Search Bar -->
      <div class="max-w-2xl mx-auto mb-6">
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="ค้นหาเกมที่คุณชื่นชอบ..."
            class="w-full px-5 py-3 pr-12 text-gray-700 bg-white border-2 border-gray-200 rounded-full focus:outline-none focus:border-blue-500 shadow-sm"
          />
          <div class="absolute inset-y-0 right-0 flex items-center pr-4">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <!-- Category Filter -->
      <div class="flex flex-wrap justify-center gap-2 mb-8">
        <button
          v-for="category in categories"
          :key="category.id"
          @click="selectedCategory = category.id"
          class="px-4 py-2 rounded-full text-white font-medium transition-all duration-200 transform hover:scale-105"
          :class="[
            category.color,
            selectedCategory === category.id ? 'ring-4 ring-opacity-50 ring-offset-2' : 'opacity-80 hover:opacity-100'
          ]"
          :style="selectedCategory === category.id ? { ringColor: category.color.replace('bg-', '').replace('500', '300') } : {}"
        >
          {{ category.name }}
        </button>
      </div>

      <!-- Leaderboard Link -->
      <div class="text-center mb-8">
        <a href="#" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-yellow-400 to-orange-500 text-white font-bold rounded-full shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
          <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
          </svg>
          ตารางคะแนนรวม
        </a>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
    </div>

    <!-- Games Grid -->
    <div v-else class="max-w-7xl mx-auto">
      <div v-if="filteredGames.length === 0" class="text-center py-12">
        <div class="text-6xl mb-4">🎮</div>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">ไม่พบเกมที่คุณค้นหา</h3>
        <p class="text-gray-500">ลองค้นหาด้วยคำอื่นหรือเลือกหมวดหมู่อื่น</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="game in filteredGames"
          :key="game.id"
          class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-xl"
        >
          <!-- Game Thumbnail -->
          <div class="h-48 bg-gradient-to-br flex items-center justify-center relative overflow-hidden" :style="{ background: `linear-gradient(to bottom right, ${game.color}22, ${game.color}44)` }">
            <div class="absolute inset-0 bg-black opacity-10"></div>
            <!-- Show image if available, otherwise show icon -->
            <img v-if="game.thumbnail" :src="game.thumbnail" :alt="game.title" class="w-full h-full object-cover z-10" @error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='block'" />
            <div v-else class="text-6xl z-10">{{ game.icon }}</div>
            <div class="absolute top-2 right-2 px-2 py-1 bg-white bg-opacity-90 rounded-full text-xs font-semibold" :style="{ color: game.color }">
              {{ getCategoryColor(game.category).replace('bg-', '').replace('-500', '') }}
            </div>
          </div>

          <!-- Game Info -->
          <div class="p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ game.title }}</h3>
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ game.description }}</p>

            <!-- Game Meta -->
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center space-x-2">
                <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getDifficultyColor(game.difficulty)">
                  {{ game.difficulty }}
                </span>
                <span class="text-xs text-gray-500">{{ game.players }}</span>
              </div>
              <div class="flex items-center">
                <span class="text-yellow-400 text-sm">{{ renderStars(game.rating) }}</span>
                <span class="text-xs text-gray-500 ml-1">({{ game.rating }})</span>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2">
              <button
                @click="playGame(game.route)"
                class="flex-1 py-2 px-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-200"
              >
                เริ่มเล่น
              </button>
              <button
                @click="viewDetails(game)"
                class="py-2 px-4 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 transition-all duration-200"
              >
                รายละเอียด
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  </GameLayout>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Custom animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.grid > div {
  animation: fadeIn 0.5s ease-out;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .text-4xl {
    font-size: 2.5rem;
  }
  
  .text-5xl {
    font-size: 3rem;
  }
}
</style>