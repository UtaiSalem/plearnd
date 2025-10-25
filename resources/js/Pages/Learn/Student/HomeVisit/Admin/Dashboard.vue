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
            <option value="students">จัดการนักเรียน</option>
            <option value="visits">รายงานการเยี่ยมบ้าน</option>
            <option value="settings">ตั้งค่า</option>
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
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-white text-sm"></i>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      จำนวนนักเรียนทั้งหมด
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ stats.total_students }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-home text-white text-sm"></i>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      การเยี่ยมบ้านทั้งหมด
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ stats.total_visits }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar text-white text-sm"></i>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      เยี่ยมบ้านเดือนนี้
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ stats.visits_this_month }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-white text-sm"></i>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      รอดำเนินการ
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ stats.pending_visits }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-white text-sm"></i>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      เสร็จสิ้นแล้ว
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ stats.completed_visits }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Visits -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
          <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              การเยี่ยมบ้านล่าสุด
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
              รายการการเยี่ยมบ้าน 10 รายการล่าสุด
            </p>
          </div>
          <ul class="divide-y divide-gray-200">
            <li
              v-for="visit in recentVisits"
              :key="visit.id"
              class="px-4 py-4 hover:bg-gray-50"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center">
                      <span class="text-white font-medium text-sm">
                        {{ visit.student?.first_name?.charAt(0) || 'N' }}
                      </span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">
                      {{ visit.student?.first_name }} {{ visit.student?.last_name }}
                    </div>
                    <div class="text-sm text-gray-500">
                      ชั้น {{ visit.student?.classroom }} | ครู {{ visit.teacher_name }}
                    </div>
                  </div>
                </div>
                <div class="flex items-center">
                  <span
                    :class="[
                      visit.visit_status === 'completed' ? 'bg-green-100 text-green-800' :
                      visit.visit_status === 'in-progress' ? 'bg-yellow-100 text-yellow-800' :
                      visit.visit_status === 'pending' ? 'bg-gray-100 text-gray-800' :
                      'bg-red-100 text-red-800',
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium'
                    ]"
                  >
                    {{ getStatusText(visit.visit_status) }}
                  </span>
                  <div class="ml-4 text-sm text-gray-500">
                    {{ formatDate(visit.visit_date) }}
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- Students Tab -->
      <div v-if="activeTab === 'students'">
        <div class="text-center py-12">
          <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
          <h3 class="text-lg font-medium text-gray-900 mb-2">จัดการข้อมูลนักเรียน</h3>
          <p class="text-gray-500">ฟีเจอร์นี้จะเปิดใช้งานในเร็วๆ นี้</p>
        </div>
      </div>

      <!-- Visits Tab -->
      <div v-if="activeTab === 'visits'">
        <div class="text-center py-12">
          <i class="fas fa-chart-line text-4xl text-gray-400 mb-4"></i>
          <h3 class="text-lg font-medium text-gray-900 mb-2">รายงานการเยี่ยมบ้าน</h3>
          <p class="text-gray-500">ฟีเจอร์นี้จะเปิดใช้งานในเร็วๆ นี้</p>
        </div>
      </div>

      <!-- Settings Tab -->
      <div v-if="activeTab === 'settings'">
        <div class="text-center py-12">
          <i class="fas fa-cog text-4xl text-gray-400 mb-4"></i>
          <h3 class="text-lg font-medium text-gray-900 mb-2">ตั้งค่าระบบ</h3>
          <p class="text-gray-500">ฟีเจอร์นี้จะเปิดใช้งานในเร็วๆ นี้</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

export default {
  props: {
    stats: Object,
    recentVisits: Array,
    monthlyVisits: Array
  },

  setup(props) {
    const activeTab = ref('dashboard')

    const tabs = [
      { id: 'dashboard', name: 'แดชบอร์ด' },
      { id: 'students', name: 'จัดการนักเรียน' },
      { id: 'visits', name: 'รายงานการเยี่ยมบ้าน' },
      { id: 'settings', name: 'ตั้งค่า' }
    ]

    const getStatusText = (status) => {
      const statusMap = {
        'pending': 'รอดำเนินการ',
        'in-progress': 'กำลังดำเนินการ',
        'completed': 'เสร็จสิ้น',
        'cancelled': 'ยกเลิก'
      }
      return statusMap[status] || status
    }

    const formatDate = (dateString) => {
      if (!dateString) return 'N/A'
      const date = new Date(dateString)
      return date.toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }

    const logout = () => {
      router.post('/home-visit/admin/logout')
    }

    return {
      activeTab,
      tabs,
      getStatusText,
      formatDate,
      logout
    }
  }
}
</script>

<style scoped>
/* Add any custom styles here */
</style>
