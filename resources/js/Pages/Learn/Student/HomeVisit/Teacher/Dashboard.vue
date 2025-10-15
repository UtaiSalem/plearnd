<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <div class="flex-shrink-0 flex items-center">
              <span class="text-2xl">🏠</span>
              <h1 class="ml-2 text-xl font-semibold text-gray-900">
                ระบบเยี่ยมบ้านนักเรียน - ครู
              </h1>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600">
              สวัสดี, ครู
            </span>
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
      <!-- Quick Stats -->
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-6">
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
      </div>

      <!-- Search Students -->
      <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            ค้นหานักเรียน
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            ค้นหานักเรียนเพื่อดูข้อมูลและจัดการการเยี่ยมบ้าน
          </p>
        </div>
        <div class="px-4 py-5 sm:p-6">
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="ค้นหาด้วยชื่อ, รหัสนักเรียน, หรือเลขบัตรประชาชน"
                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                @keyup.enter="searchStudents"
              >
            </div>
            <div class="flex-shrink-0">
              <select
                v-model="selectedClassroom"
                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                @change="searchStudents"
              >
                <option value="">ทุกชั้นเรียน</option>
                <option v-for="classroom in classrooms" :key="classroom" :value="classroom">
                  ชั้น {{ classroom }}
                </option>
              </select>
            </div>
            <button
              @click="searchStudents"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <i class="fas fa-search mr-2"></i>
              ค้นหา
            </button>
          </div>
        </div>
      </div>

      <!-- Students List -->
      <div v-if="students.data?.length > 0" class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            รายชื่อนักเรียน
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            พบนักเรียน {{ students.data.length }} คน
          </p>
        </div>
        <ul class="divide-y divide-gray-200">
          <li
            v-for="student in students.data"
            :key="student.id"
            class="px-4 py-4 hover:bg-gray-50"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-medium text-sm">
                      {{ student.first_name?.charAt(0) || 'N' }}
                    </span>
                  </div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ student.title_prefix }} {{ student.first_name }} {{ student.last_name }}
                  </div>
                  <div class="text-sm text-gray-500">
                    รหัส: {{ student.student_id }} | ชั้น: {{ student.classroom }} | 
                    เบอร์: {{ student.phone_number || 'ไม่ระบุ' }}
                  </div>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <button
                  @click="viewStudent(student)"
                  class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <i class="fas fa-eye mr-1"></i>
                  ดูข้อมูล
                </button>
                <button
                  @click="createHomeVisit(student)"
                  class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                  <i class="fas fa-plus mr-1"></i>
                  เยี่ยมบ้าน
                </button>
              </div>
            </div>
          </li>
        </ul>

        <!-- Pagination -->
        <div v-if="students.links" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <a
                v-if="students.prev_page_url"
                :href="students.prev_page_url"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                ก่อนหน้า
              </a>
              <a
                v-if="students.next_page_url"
                :href="students.next_page_url"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                ถัดไป
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- No Results -->
      <div v-else-if="searchQuery || selectedClassroom" class="text-center py-12">
        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">ไม่พบนักเรียน</h3>
        <p class="text-gray-500">ลองค้นหาด้วยคำค้นอื่นหรือเปลี่ยนตัวกรองชั้นเรียน</p>
      </div>

      <!-- Initial State -->
      <div v-else class="text-center py-12">
        <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">ค้นหานักเรียน</h3>
        <p class="text-gray-500">กรอกชื่อหรือรหัสนักเรียนเพื่อเริ่มค้นหา</p>
      </div>
    </div>

    <!-- Create Home Visit Modal -->
    <div v-if="showCreateVisitModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" @click="closeCreateVisitModal">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              สร้างการเยี่ยมบ้าน - {{ selectedStudent?.first_name }} {{ selectedStudent?.last_name }}
            </h3>
            
            <form @submit.prevent="submitCreateVisit" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">
                  วันที่เยี่ยม
                </label>
                <input
                  v-model="visitForm.visit_date"
                  type="date"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">
                  หัวข้อการเยี่ยม
                </label>
                <input
                  v-model="visitForm.visit_purpose"
                  type="text"
                  required
                  placeholder="เช่น ติดตามผลการเรียน, เยี่ยมบ้าน"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">
                  รายละเอียด
                </label>
                <textarea
                  v-model="visitForm.visit_notes"
                  rows="3"
                  placeholder="รายละเอียดเพิ่มเติม"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                ></textarea>
              </div>

              <div class="flex justify-end space-x-3 pt-4">
                <button
                  type="button"
                  @click="closeCreateVisitModal"
                  class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  ยกเลิก
                </button>
                <button
                  type="submit"
                  :disabled="visitForm.processing"
                  class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 disabled:opacity-50"
                >
                  <span v-if="visitForm.processing">กำลังบันทึก...</span>
                  <span v-else>บันทึกการเยี่ยม</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'

export default {
  props: {
    stats: Object,
    students: Object,
    classrooms: Array,
    filters: Object
  },

  setup(props) {
    const searchQuery = ref(props.filters?.search || '')
    const selectedClassroom = ref(props.filters?.classroom || '')
    const showCreateVisitModal = ref(false)
    const selectedStudent = ref(null)

    const visitForm = useForm({
      visit_date: '',
      visit_purpose: '',
      visit_notes: '',
      teacher_name: 'ครู' // This should come from auth
    })

    const searchStudents = () => {
      router.get(route('homevisit.teacher.search.students'), {
        search: searchQuery.value,
        classroom: selectedClassroom.value
      }, {
        preserveState: true
      })
    }

    const viewStudent = (student) => {
      router.visit(route('homevisit.teacher.manage.student', student.id))
    }

    const createHomeVisit = (student) => {
      selectedStudent.value = student
      visitForm.visit_date = new Date().toISOString().split('T')[0] // Today's date
      showCreateVisitModal.value = true
    }

    const closeCreateVisitModal = () => {
      showCreateVisitModal.value = false
      selectedStudent.value = null
      visitForm.reset()
    }

    const submitCreateVisit = () => {
      if (!selectedStudent.value) return

      visitForm.post(route('homevisit.teacher.create.home.visit', selectedStudent.value.id), {
        onSuccess: () => {
          closeCreateVisitModal()
          searchStudents() // Refresh the list
        }
      })
    }

    const logout = () => {
      router.post(route('homevisit.logout'))
    }

    return {
      searchQuery,
      selectedClassroom,
      showCreateVisitModal,
      selectedStudent,
      visitForm,
      searchStudents,
      viewStudent,
      createHomeVisit,
      closeCreateVisitModal,
      submitCreateVisit,
      logout
    }
  }
}
</script>