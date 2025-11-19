<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-semibold text-gray-900">
              ระบบจัดการการเยี่ยมบ้านนักเรียน - แอดมิน
            </h1>
          </div>
          <div class="flex items-center space-x-4">
            <button
              @click="logout"
              class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium"
            >
              ออกจากระบบ
            </button>
          </div>
        </div>
      </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Tab Navigation -->
      <div class="mb-6">
        <div class="sm:hidden">
          <select
            v-model="activeTab"
            class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
          >
            <option value="dashboard">แดชบอร์ด</option>
            <option value="feed">ฟีดการเยี่ยมบ้าน</option>
            <option value="settings">ตั้งค่าระบบ</option>
          </select>
        </div>
        <div class="hidden sm:block">
          <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
              <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="activeTab = tab.id"
                :class="[
                  activeTab === tab.id
                    ? 'border-indigo-500 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
                ]"
              >
                {{ tab.name }}
              </button>
            </nav>
          </div>
        </div>
      </div>

      <!-- Dashboard Content -->
      <div v-if="activeTab === 'dashboard'" class="space-y-6">
        <!-- Statistics Cards -->
        <!-- Dashboard Statistics -->
        <DashboardStats :stats="stats" />

        <!-- Visits List Section -->
        <VisitsListSection 
          :visits="allVisits" 
          :zones="zones" 
          @view-details="viewVisitDetails"
        />
      </div>

      <!-- Feed Tab -->
      <div v-if="activeTab === 'feed'" class="space-y-6">
        <VisitFeed 
          :visits="allVisits"
          :zones="zones"
          @view-details="viewVisitDetails"
        />
      </div>

      <!-- Settings Tab -->
      <div v-if="activeTab === 'settings'">
        <AdminSettings :zones="zones" />
      </div>
    </div>

    <!-- Visit Detail Modal -->
    <VisitDetailModal 
      v-if="showDetailModal" 
      :visit="selectedVisit" 
      @close="closeDetailModal"
    />
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import DashboardStats from './Components/DashboardStats.vue'
import VisitsListSection from './Components/VisitsListSection.vue'
import VisitFeed from './Components/VisitFeed.vue'
import AdminSettings from './Components/AdminSettings.vue'
import VisitDetailModal from './Components/VisitDetailModal.vue'

export default {
  components: {
    DashboardStats,
    VisitsListSection,
    VisitFeed,
    AdminSettings,
    VisitDetailModal
  },

  props: {
    stats: Object,
    recentVisits: Array,
    monthlyVisits: Array,
    students: Array,
    allVisits: {
      type: Array,
      default: () => []
    },
    zones: {
      type: Array,
      default: () => []
    }
  },

  setup(props) {
    const activeTab = ref('dashboard')
    const showDetailModal = ref(false)
    const selectedVisit = ref(null)

    const tabs = [
      { id: 'dashboard', name: 'แดชบอร์ด' },
      { id: 'feed', name: 'ฟีดการเยี่ยมบ้าน' },
      { id: 'settings', name: 'ตั้งค่าระบบ' }
    ]

    const viewVisitDetails = (visit) => {
      selectedVisit.value = visit
      showDetailModal.value = true
    }

    const closeDetailModal = () => {
      showDetailModal.value = false
      selectedVisit.value = null
    }

    const logout = () => {
      router.post('/home-visit/admin/logout')
    }

    return {
      activeTab,
      tabs,
      showDetailModal,
      selectedVisit,
      viewVisitDetails,
      closeDetailModal,
      logout
    }
  }
}
</script>

<style scoped>
/* Add any custom styles here */
</style>
