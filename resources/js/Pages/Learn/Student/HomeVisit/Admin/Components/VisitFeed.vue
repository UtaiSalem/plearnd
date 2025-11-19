<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow-sm rounded-lg p-6 flex items-center justify-between">
      <div>
        <h2 class="text-xl font-bold text-gray-900 flex items-center">
          <i class="fas fa-stream text-indigo-600 mr-2"></i>
          ฟีดการเยี่ยมบ้านนักเรียน
        </h2>
        <p class="text-gray-600 mt-1 text-sm">ไทม์ไลน์แสดงความคืบหน้าการเยี่ยมบ้านทั้งหมดเรียงตามเวลาล่าสุด</p>
      </div>
      <div class="flex space-x-2">
        <button @click="refreshFeed" class="px-3 py-2 text-sm rounded-md bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
          <i class="fas fa-sync-alt mr-1"></i> รีเฟรช
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-lg p-4">
      <div class="grid md:grid-cols-4 gap-4">
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">สถานะ</label>
          <select v-model="filters.status" class="w-full rounded-md border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">ทั้งหมด</option>
            <option value="pending">รอดำเนินการ</option>
            <option value="in-progress">กำลังดำเนินการ</option>
            <option value="completed">เสร็จสิ้น</option>
            <option value="cancelled">ยกเลิก</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">โซน</label>
          <select v-model="filters.zone" class="w-full rounded-md border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">ทั้งหมด</option>
            <option v-for="z in zones" :key="z.id" :value="z.id">{{ z.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">วันที่เริ่ม</label>
          <input type="date" v-model="filters.startDate" class="w-full rounded-md border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">วันที่สิ้นสุด</label>
          <input type="date" v-model="filters.endDate" class="w-full rounded-md border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
      </div>
      <div class="mt-4 flex items-center justify-between">
        <div class="flex space-x-2">
          <button @click="clearFilters" class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-600 hover:bg-gray-200">ล้างตัวกรอง</button>
        </div>
        <div class="text-xs text-gray-500">พบ {{ filteredVisits.length }} รายการ</div>
      </div>
    </div>

    <!-- Timeline Feed -->
    <div class="space-y-6">
      <div v-if="!filteredVisits.length" class="bg-white rounded-lg shadow p-10 text-center text-gray-500 text-sm">
        ไม่พบข้อมูลการเยี่ยมบ้านตามเงื่อนไขที่เลือก
      </div>

      <div v-for="(visit, index) in paginatedVisits" :key="visit.id" class="relative">
        <!-- Vertical line -->
        <div v-if="index !== paginatedVisits.length - 1" class="absolute left-5 top-12 w-px h-full bg-gray-200"></div>
        <div class="flex items-start">
          <!-- Icon circle -->
            <div class="flex flex-col items-center">
              <div :class="['w-10 h-10 rounded-full flex items-center justify-center shadow text-white', statusColor(visit.visit_status)]">
                <i :class="statusIcon(visit.visit_status)"></i>
              </div>
            </div>
          <!-- Card -->
          <div class="ml-6 flex-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
              <div class="p-5">
                <div class="flex justify-between items-start">
                  <div>
                    <h3 class="text-sm font-semibold text-gray-900">
                      นักเรียน: {{ visit.student?.first_name }} {{ visit.student?.last_name }}
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                      ชั้น {{ visit.student?.classroom || '-' }} | ครูผู้รับผิดชอบ: {{ visit.teacher_name || '-' }}
                    </p>
                  </div>
                  <div class="text-right">
                    <div class="text-xs text-gray-400">
                      {{ formatDate(visit.visit_date) }}
                    </div>
                    <span :class="['inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium', statusBadge(visit.visit_status)]">
                      {{ statusText(visit.visit_status) }}
                    </span>
                  </div>
                </div>

                <!-- Summary / Notes -->
                <div v-if="visit.notes || visit.summary" class="mt-3 text-sm text-gray-700 whitespace-pre-line">
                  {{ visit.summary || visit.notes }}
                </div>

                <!-- Images Gallery Preview -->
                <div v-if="visit.images && visit.images.length" class="mt-4 grid grid-cols-3 gap-2">
                  <div
                    v-for="(img, i) in visit.images.slice(0,3)"
                    :key="i"
                    class="aspect-square overflow-hidden rounded-md cursor-pointer group relative"
                    @click="openDetails(visit)"
                  >
                    <img :src="img.url || img" class="object-cover w-full h-full group-hover:scale-105 transition" />
                    <div v-if="i === 2 && visit.images.length > 3" class="absolute inset-0 bg-black/40 flex items-center justify-center text-white text-xs">
                      +{{ visit.images.length - 3 }}
                    </div>
                  </div>
                </div>

                <!-- Meta Data -->
                <div class="mt-4 grid sm:grid-cols-4 gap-3 text-xs">
                  <div class="flex items-center space-x-2">
                    <i class="fas fa-map-marker-alt text-indigo-500"></i>
                    <span class="text-gray-600">โซน: {{ visit.zone?.name || '-' }}</span>
                  </div>
                  <div class="flex items-center space-x-2">
                    <i class="fas fa-user-friends text-indigo-500"></i>
                    <span class="text-gray-600">ผู้เยี่ยม: {{ visit.visitor_name || visit.teacher_name || 'N/A' }}</span>
                  </div>
                  <div class="flex items-center space-x-2" v-if="visit.duration">
                    <i class="fas fa-hourglass-half text-indigo-500"></i>
                    <span class="text-gray-600">ระยะเวลา: {{ visit.duration }} นาที</span>
                  </div>
                  <div class="flex items-center space-x-2" v-if="visit.next_schedule">
                    <i class="fas fa-calendar-plus text-indigo-500"></i>
                    <span class="text-gray-600">ครั้งต่อไป: {{ formatDate(visit.next_schedule) }}</span>
                  </div>
                </div>

                <!-- Actions -->
                <div class="mt-4 flex items-center justify-between">
                  <div class="flex space-x-4 text-xs text-gray-500">
                    <button @click="openDetails(visit)" class="flex items-center hover:text-indigo-600">
                      <i class="fas fa-eye mr-1"></i> ดูรายละเอียด
                    </button>
                    <button class="flex items-center hover:text-indigo-600" @click="toggleExpand(visit.id)">
                      <i :class="['mr-1', expanded.has(visit.id) ? 'fas fa-chevron-up' : 'fas fa-chevron-down']"></i>
                      {{ expanded.has(visit.id) ? 'ย่อ' : 'ขยาย' }}
                    </button>
                  </div>
                  <div class="text-[10px] text-gray-400">
                    ID: {{ visit.id }}
                  </div>
                </div>

                <!-- Expanded Extra Content -->
                <transition name="fade">
                  <div v-if="expanded.has(visit.id)" class="mt-4 border-t pt-4 space-y-3">
                    <div v-if="visit.risks" class="text-xs">
                      <div class="font-medium text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        ประเด็นที่พบ:
                      </div>
                      <ul class="list-disc pl-5 space-y-1">
                        <li v-for="(r,i) in (Array.isArray(visit.risks) ? visit.risks : [])" :key="i" class="text-gray-600">
                          {{ r }}
                        </li>
                      </ul>
                    </div>
                    <div v-if="visit.recommendations" class="text-xs">
                      <div class="font-medium text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                        ข้อเสนอแนะ:
                      </div>
                      <ul class="list-disc pl-5 space-y-1">
                        <li v-for="(r,i) in (Array.isArray(visit.recommendations) ? visit.recommendations : [])" :key="i" class="text-gray-600">
                          {{ r }}
                        </li>
                      </ul>
                    </div>
                    <div v-if="visit.follow_up_actions" class="text-xs">
                      <div class="font-medium text-gray-700 mb-1 flex items-center">
                        <i class="fas fa-tasks text-indigo-500 mr-2"></i>
                        การติดตามผล:
                      </div>
                      <ul class="list-disc pl-5 space-y-1">
                        <li v-for="(a,i) in (Array.isArray(visit.follow_up_actions) ? visit.follow_up_actions : [])" :key="i" class="text-gray-600">
                          {{ a }}
                        </li>
                      </ul>
                    </div>
                  </div>
                </transition>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="filteredVisits.length" class="flex justify-center pt-4">
        <nav class="inline-flex items-center space-x-1" aria-label="Pagination">
          <button
            @click="prevPage"
            :disabled="currentPage === 1"
            class="px-3 py-1 rounded-md text-xs font-medium border bg-white hover:bg-gray-50 disabled:opacity-40"
          >
            ก่อนหน้า
          </button>
          <span class="px-2 text-xs text-gray-600">หน้า {{ currentPage }} / {{ totalPages }}</span>
          <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-3 py-1 rounded-md text-xs font-medium border bg-white hover:bg-gray-50 disabled:opacity-40"
          >
            ถัดไป
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  visits: { type: Array, default: () => [] },
  zones: { type: Array, default: () => [] }
})

const emits = defineEmits(['view-details'])

const filters = ref({
  status: '',
  zone: '',
  startDate: '',
  endDate: ''
})

const expanded = ref(new Set())
const currentPage = ref(1)
const perPage = ref(10)

const clearFilters = () => {
  filters.value = { status: '', zone: '', startDate: '', endDate: '' }
}

const formatDate = (d) => {
  if (!d) return '-'
  const dt = new Date(d)
  return dt.toLocaleString('th-TH', { dateStyle: 'medium', timeStyle: 'short' })
}

const statusText = (s) => {
  switch (s) {
    case 'pending': return 'รอดำเนินการ'
    case 'in-progress': return 'กำลังดำเนินการ'
    case 'completed': return 'เสร็จสิ้น'
    case 'cancelled': return 'ยกเลิก'
    default: return 'ไม่ระบุ'
  }
}

const statusColor = (s) => {
  switch (s) {
    case 'pending': return 'bg-gray-400'
    case 'in-progress': return 'bg-yellow-500'
    case 'completed': return 'bg-green-600'
    case 'cancelled': return 'bg-red-500'
    default: return 'bg-indigo-500'
  }
}

const statusIcon = (s) => {
  switch (s) {
    case 'pending': return 'fas fa-clock'
    case 'in-progress': return 'fas fa-spinner'
    case 'completed': return 'fas fa-check'
    case 'cancelled': return 'fas fa-times'
    default: return 'fas fa-info'
  }
}

const statusBadge = (s) => {
  switch (s) {
    case 'pending': return 'bg-gray-100 text-gray-800'
    case 'in-progress': return 'bg-yellow-100 text-yellow-800'
    case 'completed': return 'bg-green-100 text-green-800'
    case 'cancelled': return 'bg-red-100 text-red-800'
    default: return 'bg-indigo-100 text-indigo-800'
  }
}

const filteredVisits = computed(() => {
  return props.visits
    .filter(v => {
      if (filters.value.status && v.visit_status !== filters.value.status) return false
      if (filters.value.zone && String(v.zone_id) !== String(filters.value.zone)) return false
      if (filters.value.startDate) {
        const vs = new Date(v.visit_date)
        const st = new Date(filters.value.startDate)
        if (vs < st) return false
      }
      if (filters.value.endDate) {
        const ve = new Date(v.visit_date)
        const en = new Date(filters.value.endDate)
        if (ve > en) return false
      }
      return true
    })
    .sort((a,b) => new Date(b.visit_date) - new Date(a.visit_date))
})

const totalPages = computed(() => Math.max(1, Math.ceil(filteredVisits.value.length / perPage.value)))

const paginatedVisits = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return filteredVisits.value.slice(start, start + perPage.value)
})

const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }
const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }

const toggleExpand = (id) => {
  if (expanded.value.has(id)) {
    expanded.value.delete(id)
  } else {
    expanded.value.add(id)
  }
  expanded.value = new Set(expanded.value)
}

const openDetails = (visit) => {
  emits('view-details', visit)
}

const refreshFeed = () => {
  // Placeholder: In real implementation, fetch latest visits via API
  // Could emit an event or call a composable function
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .2s }
.fade-enter-from, .fade-leave-to { opacity: 0 }
</style>
