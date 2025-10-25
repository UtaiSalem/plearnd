<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const page = usePage();
const isSidebarOpen = ref(true);

// Game menu items
const gameMenuItems = [
  {
    name: 'Plearnd',
    icon: '🏠',
    route: 'home',
    url: '/'
  },
  {
    name: 'ศูนย์รวมเกม',
    icon: '🎮',
    route: 'game.dashboard',
    url: '/play/games'
  },
  {
    name: 'เกมทายตัวเลข',
    icon: '🔢',
    route: 'game.quessing_number',
    url: '/play/games/guessing-number'
  },
  {
    name: 'เกม XO',
    icon: '⭕',
    route: 'game.xo',
    url: '/play/games/xo'
  },
  {
    name: 'เกมงู',
    icon: '🐍',
    route: 'game.snake',
    url: '/play/games/snake'
  },
  {
    name: 'Mental Math',
    icon: '🧮',
    route: 'mental-math',
    url: '/play/games/mental-math'
  }
];

// Check if current page matches the menu item
const isActive = (url) => {
  return page.url === url || page.url.startsWith(url + '/');
};

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};
</script>

<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <div
      class="bg-gradient-to-b from-blue-600 to-blue-800 text-white transition-all duration-300 ease-in-out shadow-xl"
      :class="isSidebarOpen ? 'w-64' : 'w-16'"
    >
      <!-- Sidebar Header -->
      <div class="p-4 border-b border-blue-700">
        <div class="flex items-center justify-between">
          <h2 v-if="isSidebarOpen" class="text-xl font-bold">เมนูเกม</h2>
          <button
            @click="toggleSidebar"
            class="p-2 rounded-lg hover:bg-blue-700 transition-colors focus:outline-none"
          >
            <svg v-if="isSidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="p-4">
        <ul class="space-y-2">
          <li v-for="item in gameMenuItems" :key="item.name">
            <Link
              :href="item.url"
              :class="[
                'flex items-center p-3 rounded-lg transition-all duration-200',
                isActive(item.url)
                  ? 'bg-blue-700 shadow-md transform scale-105'
                  : 'hover:bg-blue-700 hover:translate-x-1'
              ]"
            >
              <span class="text-xl flex-shrink-0">{{ item.icon }}</span>
              <span v-if="isSidebarOpen" class="ml-3 font-medium">{{ item.name }}</span>
            </Link>
          </li>
        </ul>
      </nav>

      <!-- Sidebar Footer -->
      <div v-if="isSidebarOpen" class="absolute bottom-0 left-0 right-0 p-4 border-t border-blue-700">
        <div class="text-sm text-blue-200">
          <p>เลือกเกมที่คุณชื่นชอบ</p>
          <p class="text-xs mt-1">และเริ่มต้นความสนุก!</p>
        </div>
      </div>
    </div>

    <!-- Main content area -->
    <div class="flex-1 overflow-auto">
      <slot></slot>
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar for sidebar */
.bg-gradient-to-b {
  scrollbar-width: thin;
  scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
}

.bg-gradient-to-b::-webkit-scrollbar {
  width: 6px;
}

.bg-gradient-to-b::-webkit-scrollbar-track {
  background: transparent;
}

.bg-gradient-to-b::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.2);
  border-radius: 3px;
}

.bg-gradient-to-b::-webkit-scrollbar-thumb:hover {
  background-color: rgba(255, 255, 255, 0.3);
}
</style>
