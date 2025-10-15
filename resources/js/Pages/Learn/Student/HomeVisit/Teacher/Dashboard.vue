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
          <div class="space-y-4">
            <!-- Mobile Layout -->
            <div class="block sm:hidden space-y-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ค้นหานักเรียน</label>
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="ชื่อ, รหัส, เลขบัตร..."
                  class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-base"
                  @keyup.enter="searchStudents"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ชั้นเรียน</label>
                <select
                  v-model="selectedClassroom"
                  class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-base"
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
                class="w-full inline-flex justify-center items-center py-3 px-4 border border-transparent shadow-sm text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
              >
                <i class="fas fa-search mr-2"></i>
                ค้นหานักเรียน
              </button>
            </div>

            <!-- Desktop Layout -->
            <div class="hidden sm:flex flex-col sm:flex-row gap-4">
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
            class="px-3 py-4 hover:bg-gray-50 border-l-4 border-transparent hover:border-indigo-400 transition-all duration-200"
          >
            <!-- Mobile Layout (sm and below) -->
            <div class="block sm:hidden">
              <div class="space-y-3">
                <!-- Header Row -->
                <div class="flex items-start space-x-5">
                  <div class="flex-shrink-0">
                    <div v-if="student.profile_image" class="w-32 h-40 rounded-2xl overflow-hidden border-2 border-gray-200 shadow-lg">
                      <img :src="'../../storage/images/students/' + student.class_level + '/' + student.class_section + '/' + student.profile_image" :alt="student.first_name_th" class="w-full h-full object-cover">
                    </div>
                    <div v-else class="w-32 h-40 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center border-2 border-gray-200 shadow-lg">
                      <span class="text-white font-bold text-4xl">
                        {{ student.first_name_th?.charAt(0) || 'N' }}
                      </span>
                    </div>
                  </div>
                  
                  <div class="flex-1 min-w-0 flex flex-col justify-start pt-2">
                    <!-- Name and Nickname -->
                    <div class="flex flex-col space-y-3">
                      <!-- Full Name with responsive sizing -->
                      <div class="space-y-1">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 leading-tight">
                          <span class="block sm:inline">{{ student.title_prefix_th || '' }}</span>
                          <span class="block sm:inline">{{ student.first_name_th }}</span>
                          <span class="block sm:inline">{{ student.last_name_th }}</span>
                        </h3>
                        <!-- Abbreviated name for very long names on mobile -->
                        <div v-if="getFullName(student).length > 30" class="text-sm text-gray-600 font-medium block sm:hidden">
                          {{ student.first_name_th }} {{ student.last_name_th?.charAt(0) }}.
                        </div>
                      </div>
                      
                      <!-- Badges with flexible wrapping -->
                      <div class="flex flex-wrap items-center gap-2">
                        <!-- Nickname Badge -->
                        <div v-if="student.nickname" class="flex-shrink-0">
                          <span class="inline-flex items-center px-2.5 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">
                            <i class="fas fa-star mr-1"></i>
                            <span class="truncate max-w-20">{{ student.nickname }}</span>
                          </span>
                        </div>
                        
                        <!-- Visit Status Badge -->
                        <div class="flex-shrink-0">
                          <div v-if="student.home_visits?.length > 0" class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                            <i class="fas fa-check-circle mr-1"></i>
                            <span class="whitespace-nowrap">เยี่ยม {{ student.home_visits.length }} ครั้ง</span>
                          </div>
                          <div v-else class="inline-flex items-center px-2.5 py-1 bg-orange-100 text-orange-700 text-xs font-medium rounded-full">
                            <i class="fas fa-clock mr-1"></i>
                            <span class="whitespace-nowrap">ยังไม่เคยเยี่ยม</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Information Grid -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 space-y-3 border border-gray-200">
                  <div class="grid grid-cols-2 gap-3">
                    <!-- Student ID -->
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                          <i class="fas fa-id-card text-blue-600 text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                          <p class="text-xs text-gray-500 font-medium">รหัสนักเรียน</p>
                          <p class="text-sm font-bold text-gray-900 break-all">{{ student.student_id }}</p>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Class Level -->
                    <div v-if="student.class_level" class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                          <i class="fas fa-layer-group text-emerald-600 text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                          <p class="text-xs text-gray-500 font-medium">ระดับชั้น</p>
                          <p class="text-sm font-bold text-gray-900">ม.{{ student.class_level }}</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-3">
                    <!-- Class Section -->
                    <div v-if="student.class_section" class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                          <i class="fas fa-graduation-cap text-green-600 text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                          <p class="text-xs text-gray-500 font-medium">ห้อง</p>
                          <p class="text-sm font-bold text-gray-900">{{ student.class_section }}</p>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Current Class (from academic_info) -->
                    <div v-if="student.academic_info?.current_class" class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                          <i class="fas fa-chalkboard-teacher text-teal-600 text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                          <p class="text-xs text-gray-500 font-medium">ชั้นปัจจุบัน</p>
                          <p class="text-sm font-bold text-gray-900 break-words">{{ student.academic_info.current_class }}</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-3" v-if="student.citizen_id || (student.contacts && student.contacts.length > 0)">
                    <!-- Citizen ID -->
                    <div v-if="student.citizen_id" class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                          <i class="fas fa-credit-card text-purple-600 text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                          <p class="text-xs text-gray-500 font-medium">เลขบัตร</p>
                          <p class="text-xs font-bold text-gray-900 break-all">{{ student.citizen_id }}</p>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Phone -->
                    <div v-if="student.contacts && student.contacts.length > 0" class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                          <i class="fas fa-phone text-yellow-600 text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                          <p class="text-xs text-gray-500 font-medium">เบอร์โทร</p>
                          <p class="text-xs font-bold text-gray-900 break-all">{{ student.contacts[0].contact_value || 'ไม่ระบุ' }}</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Last Visit Info -->
                  <div v-if="student.home_visits?.length > 0" class="bg-white rounded-lg p-3 shadow-sm">
                    <div class="flex items-center space-x-2">
                      <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calendar text-indigo-600 text-sm"></i>
                      </div>
                      <div class="min-w-0 flex-1">
                        <p class="text-xs text-gray-500 font-medium">เยี่ยมล่าสุด</p>
                        <p class="text-sm font-bold text-gray-900 break-words">{{ formatDate(student.home_visits[0].visit_date) }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-2">
                  <button
                    @click="viewStudent(student)"
                    class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-indigo-300 text-sm font-medium rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                  >
                    <i class="fas fa-user-graduate mr-2"></i>
                    ดูข้อมูล
                  </button>
                  <button
                    @click="createHomeVisit(student)"
                    class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors"
                  >
                    <i class="fas fa-home mr-2"></i>
                    เยี่ยมบ้าน
                  </button>
                </div>
              </div>
            </div>

            <!-- Desktop Layout (sm and above) -->
            <div class="hidden sm:flex items-center justify-between">
              <div class="flex items-center flex-1">
                <div class="flex-shrink-0">
                  <div v-if="student.profile_image" class="w-24 h-32 rounded-xl overflow-hidden border-2 border-gray-200 shadow-md">
                    <img :src="'../../storage/images/students/' + student.class_level + '/' + student.class_section + '/' + student.profile_image" :alt="student.first_name_th" class="w-full h-full object-cover">
                  </div>
                  <div v-else class="w-24 h-32 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center border-2 border-gray-200 shadow-md">
                    <span class="text-white font-bold text-2xl">
                      {{ student.first_name_th?.charAt(0) || 'N' }}
                    </span>
                  </div>
                </div>
                <div class="ml-4 sm:ml-5 flex-1 min-w-0">
                  <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <div class="text-base font-medium text-gray-900 break-words min-w-0">
                      <span class="inline-block">{{ student.title_prefix_th || '' }}</span>
                      <span class="inline-block">{{ student.first_name_th }}</span>
                      <span class="inline-block">{{ student.last_name_th }}</span>
                    </div>
                    <div v-if="student.nickname" class="flex-shrink-0">
                      <span class="inline-flex items-center text-xs text-purple-700 bg-purple-100 px-2 py-1 rounded-full font-medium">
                        <i class="fas fa-star mr-1"></i>
                        <span class="truncate max-w-20">{{ student.nickname }}</span>
                      </span>
                    </div>
                  </div>
                  
                  <div class="mt-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-1 text-sm text-gray-500">
                    <div class="flex items-center">
                      <i class="fas fa-id-card w-4 mr-2"></i>
                      รหัส: {{ student.student_id }}
                    </div>
                    <div v-if="student.class_level" class="flex items-center">
                      <i class="fas fa-layer-group w-4 mr-2"></i>
                      ระดับ: ม.{{ student.class_level }}
                    </div>
                    <div v-if="student.class_section" class="flex items-center">
                      <i class="fas fa-door-open w-4 mr-2"></i>
                      ห้อง: {{ student.class_section }}
                    </div>
                    <div v-if="student.academic_info?.current_class" class="flex items-center">
                      <i class="fas fa-graduation-cap w-4 mr-2"></i>
                      ชั้นปัจจุบัน: {{ student.academic_info.current_class }}
                    </div>
                    <div v-if="student.citizen_id" class="flex items-center">
                      <i class="fas fa-credit-card w-4 mr-2"></i>
                      เลขบัตร: {{ student.citizen_id }}
                    </div>
                    <div v-if="student.contacts?.length > 0" class="flex items-center">
                      <i class="fas fa-phone w-4 mr-2"></i>
                      เบอร์: {{ student.contacts[0].contact_value || 'ไม่ระบุ' }}
                    </div>
                  </div>

                  <!-- Academic Info -->
                  <div class="mt-2 text-xs text-gray-400 space-x-3">
                    <span v-if="student.academic_info?.school_name">
                      <i class="fas fa-school mr-1"></i>
                      {{ student.academic_info.school_name }}
                    </span>
                    <span v-if="student.academic_info?.education_level">
                      <i class="fas fa-graduation-cap mr-1"></i>
                      การศึกษา: {{ getEducationLevelText(student.academic_info.education_level) }}
                    </span>
                    <span v-if="student.class_level && student.class_section">
                      <i class="fas fa-users mr-1"></i>
                      ม.{{ student.class_level }}/{{ student.class_section }}
                    </span>
                  </div>

                  <!-- Home Visit Status -->
                  <div v-if="student.home_visits?.length > 0" class="mt-2 text-xs">
                    <div class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 rounded">
                      <i class="fas fa-home mr-1"></i>
                      เยี่ยมล่าสุด: {{ formatDate(student.home_visits[0].visit_date) }}
                    </div>
                  </div>
                  <div v-else class="mt-2 text-xs">
                    <div class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-700 rounded">
                      <i class="fas fa-exclamation-circle mr-1"></i>
                      ยังไม่เคยเยี่ยมบ้าน
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="flex flex-col items-end space-y-2 ml-4">
                <!-- Visit Count Badge -->
                <div v-if="student.home_visits?.length > 0" class="text-xs text-center">
                  <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 rounded-full">
                    <i class="fas fa-chart-line mr-1"></i>
                    เยี่ยมแล้ว {{ student.home_visits.length }} ครั้ง
                  </span>
                </div>
                
                <div class="flex flex-col space-y-1">
                  <button
                    @click="viewStudent(student)"
                    class="inline-flex items-center px-3 py-2 border border-indigo-300 shadow-sm text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                  >
                    <i class="fas fa-user-graduate mr-2"></i>
                    ดูข้อมูล
                  </button>
                  <button
                    @click="createHomeVisit(student)"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
                  >
                    <i class="fas fa-home mr-2"></i>
                    เยี่ยมบ้าน
                  </button>
                </div>
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
              สร้างการเยี่ยมบ้าน - {{ selectedStudent?.first_name_th }} {{ selectedStudent?.last_name_th }}
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

    const formatDate = (dateString) => {
      if (!dateString) return 'ไม่ระบุ'
      const date = new Date(dateString)
      return date.toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }

    const getEducationLevelText = (level) => {
      const levelMap = {
        1: 'ประถมศึกษา',
        2: 'มัธยมศึกษาตอนต้น',
        3: 'มัธยมศึกษาตอนปลาย',
        4: 'อุดมศึกษا'
      }
      return levelMap[level] || `ระดับ ${level}`
    }

    const getFullName = (student) => {
      return `${student.title_prefix_th || ''} ${student.first_name_th} ${student.last_name_th}`.trim()
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
      logout,
      formatDate,
      getEducationLevelText,
      getFullName
    }
  }
}
</script>