
<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  student: {
    type: Object,
    required: true
  },
  studentCard: {
    type: Object,
    default: () => ({})
  },
  isLoading: {
    type: Boolean,
    default: false
  },
  context: {
    type: String,
    default: 'student' // 'student' or 'teacher'
  }
})

const emit = defineEmits(['save', 'update'])

// Removed unnecessary computed properties since we now have complete data from database

// Helper function to format date for input[type="date"] (yyyy-MM-dd format)
const formatDateForInput = (dateString) => {
  if (!dateString) return ''
  
  try {
    const date = new Date(dateString)
    // Return YYYY-MM-DD format
    return date.toISOString().split('T')[0]
  } catch (error) {
    console.warn('Date formatting error:', error)
    return ''
  }
}

// Simplified: Use direct data from database since we already populated the students table

// Create reactive form data using direct database values (already populated)
const form = reactive({
  citizen_id: props.student?.citizen_id || props.studentCard?.national_id || '',
  student_id: props.student?.student_id || props.studentCard?.student_number || '',
  title_prefix_th: props.student?.title_prefix_th || props.studentCard?.title_name || '',
  first_name_th: props.student?.first_name_th || props.studentCard?.first_name_thai || '',
  last_name_th: props.student?.last_name_th || props.studentCard?.last_name_thai || '',
  middle_name_th: props.student?.middle_name_th || '',
  title_prefix_en: props.student?.title_prefix_en || '',
  first_name_en: props.student?.first_name_en || '',
  last_name_en: props.student?.last_name_en || '',
  middle_name_en: props.student?.middle_name_en || '',
  nickname: props.student?.nickname || '',
  gender: props.student?.gender !== undefined && props.student?.gender !== null ? props.student.gender : null, // 0, 1, or null
  date_of_birth: formatDateForInput(props.student?.date_of_birth || props.studentCard?.birth_date) || '',
  nationality: props.student?.nationality || 'ไทย',
  religion: props.student?.religion || '',
  profile_image: props.student?.profile_image || props.studentCard?.profile_image || '',
  status: props.student?.status || 'active',
  enrollment_date: formatDateForInput(props.student?.enrollment_date) || '',
  class_level: props.student?.class_level || props.studentCard?.class_level || '',
  class_section: props.student?.class_section || props.studentCard?.class_section || ''
})

// Computed property for current gender display
const currentGenderText = computed(() => {
  if (form.gender === null || form.gender === undefined) return ''
  
  // แปลงจากตัวเลขเป็นข้อความ: 0=หญิง, 1=ชาย
  const genderMap = {
    0: 'หญิง',
    1: 'ชาย'
  }
  
  return genderMap[form.gender] || 'ไม่ระบุ'
})

// Computed property for class display
const classDisplayText = computed(() => {
  if (!form.class_level || !form.class_section) return ''
  return `ม.${form.class_level}/${form.class_section}`
})

// Save functionality
const isSaving = ref(false)
const saveStatus = ref(null)

// Student photo management
const studentPhoto = ref(null)
const photoInput = ref(null)
const showPhotoModal = ref(false)

// Computed property to get student photo URL
const getStudentPhotoUrl = computed(() => {
  if (form.profile_image) {
    // Priority order: students table profile_image, then studentCard profile_image
    const possiblePaths = []
    
    // If profile_image from students table exists
    if (props.student?.profile_image) {
      possiblePaths.push(`/storage/images/students/${props.student?.class_level}/${props.student?.class_section}/${props.student.profile_image}`)
    }
    
    // If profile_image from studentCard exists
    if (props.studentCard?.profile_image && props.studentCard?.class_level && props.studentCard?.class_section) {
      possiblePaths.push(`/storage/images/students/${props.studentCard.class_level}/${props.studentCard.class_section}/${props.studentCard.profile_image}`)
    }
    
    return possiblePaths
  }
  return null
})

// Load student photo on component mount
onMounted(() => {
  loadStudentPhoto()
})

// Function to load student photo
const loadStudentPhoto = async () => {
  if (getStudentPhotoUrl.value) {
    for (const photoPath of getStudentPhotoUrl.value) {
      try {
        const response = await fetch(photoPath, { method: 'HEAD' })
        if (response.ok) {
          studentPhoto.value = photoPath
          break
        }
      } catch (error) {
        // Continue to next path
        continue
      }
    }
  }
}

// Function to open photo modal
const openPhotoModal = () => {
  if (studentPhoto.value) {
    showPhotoModal.value = true
  }
}

// Function to close photo modal
const closePhotoModal = () => {
  showPhotoModal.value = false
}

// Handle photo upload
const handlePhotoUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Validate file type
    if (!file.type.startsWith('image/')) {
      alert('กรุณาเลือกไฟล์รูปภาพเท่านั้น')
      return
    }
    
    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
      alert('ขนาดไฟล์ต้องไม่เกิน 5MB')
      return
    }
    
    // Create preview URL
    const reader = new FileReader()
    reader.onload = (e) => {
      studentPhoto.value = e.target.result
    }
    reader.readAsDataURL(file)
    
    // Here you would typically upload the file to the server
    // uploadStudentPhoto(file)
  }
  
  // Reset input
  event.target.value = ''
}

// Handle image load error
const handleImageError = () => {
  studentPhoto.value = null
}

// Handle successful image load
const onImageLoad = () => {
  // Image loaded successfully
}

// Function to upload photo to server (placeholder)
const uploadStudentPhoto = async (file) => {
  const formData = new FormData()
  formData.append('photo', file)
  formData.append('student_id', form.student_id)
  
  try {
    // Replace with actual upload endpoint
    // const response = await fetch('/api/students/upload-photo', {
    //   method: 'POST',
    //   body: formData
    // })
    
    // Photo upload functionality would be implemented here
  } catch (error) {
    console.error('Photo upload error:', error)
    alert('เกิดข้อผิดพลาดในการอัพโหลดรูปภาพ')
  }
}

// Helper function to format birth date in Thai format
const formatBirthDate = (dateString) => {
  if (!dateString) return 'ไม่ระบุ'
  
  try {
    const date = new Date(dateString)
    
    // Thai month names
    const thaiMonths = [
      'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
      'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ]
    
    const day = date.getDate()
    const month = thaiMonths[date.getMonth()]
    const year = date.getFullYear() + 543 // Convert to Buddhist Era
    
    return `${day} ${month} ${year}`
  } catch (error) {
    console.warn('Date formatting error:', error)
    return dateString
  }
}

// Helper function to calculate age
const calculateAge = (dateString) => {
  if (!dateString) return '-'
  
  try {
    const birthDate = new Date(dateString)
    const today = new Date()
    let age = today.getFullYear() - birthDate.getFullYear()
    const monthDiff = today.getMonth() - birthDate.getMonth()
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
      age--
    }
    
    return age
  } catch (error) {
    return '-'
  }
}

// Helper function to format citizen ID
const formatCitizenId = (citizenId) => {
  if (!citizenId) return '-'
  
  // Remove all non-digits
  const digits = citizenId.replace(/\D/g, '')
  
  // If not 13 digits, return as is
  if (digits.length !== 13) return citizenId
  
  // Format as X-XXXX-XXXXX-XX-X
  return `${digits.substring(0, 1)}-${digits.substring(1, 5)}-${digits.substring(5, 10)}-${digits.substring(10, 12)}-${digits.substring(12, 13)}`
}

// Save student data function
const saveStudentData = async () => {
  isSaving.value = true
  saveStatus.value = null
  
  try {
    // Extract only student table data with correct field names
    const studentData = {
      citizen_id: form.citizen_id,
      student_id: form.student_id,
      title_prefix_th: form.title_prefix_th,
      first_name_th: form.first_name_th,
      last_name_th: form.last_name_th,
      middle_name_th: form.middle_name_th,
      title_prefix_en: form.title_prefix_en,
      first_name_en: form.first_name_en,
      last_name_en: form.last_name_en,
      middle_name_en: form.middle_name_en,
      nickname: form.nickname,
      gender: form.gender,
      date_of_birth: form.date_of_birth,
      nationality: form.nationality,
      religion: form.religion,
      profile_image: form.profile_image,
      status: form.status,
      enrollment_date: form.enrollment_date,
      class_level: form.class_level,
      class_section: form.class_section
    }
    
    // Use Inertia.js to save data
    await router.post(route('homevisit.student.update.info'), studentData, {
      preserveScroll: true,
      onSuccess: () => {
        saveStatus.value = 'success'
        // Emit save event to parent component
        emit('save', {
          success: true,
          type: 'students',
          data: studentData
        })
        setTimeout(() => {
          saveStatus.value = null
        }, 3000)
      },
      onError: (errors) => {
        saveStatus.value = 'error'
        // Emit save event with error to parent component
        emit('save', {
          success: false,
          type: 'students',
          message: Object.values(errors).join(', ')
        })
        console.error('Save errors:', errors)
      }
    })
    
  } catch (error) {
    saveStatus.value = 'error'
    console.error('Save error:', error)
  } finally {
    isSaving.value = false
  }
}
</script>

<template>
  <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-emerald-500 to-green-600 px-4 py-3 sm:px-6 sm:py-4">
      <div class="flex items-center">
        <div class="w-7 h-7 sm:w-8 sm:h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center flex-shrink-0">
          <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>
        <div class="ml-2 sm:ml-3 min-w-0 flex-1">
          <h3 class="text-base sm:text-lg font-semibold text-white truncate">
            ข้อมูลพื้นฐาน
          </h3>
          <p class="text-green-100 text-xs sm:text-sm">ข้อมูลส่วนตัวของนักเรียน</p>
        </div>
      </div>
    </div>

    <!-- Student Photo Section -->
    <div class="bg-gray-50 px-4 py-4 sm:px-6 border-b border-gray-200">
      <div class="flex items-center space-x-4">
        <div class="flex-shrink-0">
          <div class="w-24 h-32 sm:w-32 sm:h-40 bg-gray-200 rounded-md overflow-hidden border-2 border-white shadow-lg" :class="{ 'cursor-pointer hover:shadow-xl transition-shadow': studentPhoto }" @click="openPhotoModal">
            <img
              v-if="studentPhoto"
              :src="studentPhoto"
              :alt="`${form.first_name_th} ${form.last_name_th}`"
              class="w-full h-full object-cover"
              @load="onImageLoad"
              @error="handleImageError"
            />
            <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
              <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="text-sm sm:text-base font-semibold text-gray-900 truncate">
            {{ form.title_prefix_th }} {{ form.first_name_th }} {{ form.last_name_th }}
          </h4>
          <p class="text-xs sm:text-sm text-gray-600">
            รหัส: {{ form.student_id }}
          </p>
          <p v-if="classDisplayText" class="text-xs sm:text-sm text-emerald-600 font-medium">
            ชั้น {{ classDisplayText }}
          </p>
          <div class="mt-2">
            <input
              ref="photoInput"
              type="file"
              accept="image/*"
              @change="handlePhotoUpload"
              class="hidden"
            />
            <button
              type="button"
              @click="$refs.photoInput.click()"
              class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-emerald-500"
            >
              <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              เปลี่ยนรูป
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="p-4 sm:p-6">

        <!-- Names Section from students table -->
      <div class="mt-6 pb-6 ">
        <div class="mb-4">
          <h4 class="text-sm sm:text-base font-semibold text-emerald-800 flex items-center">
            <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a2 2 0 012-2z" />
            </svg>
            ชื่อ-นามสกุล
          </h4>
        </div>

        <!-- Thai Names Section -->
        <div class="mb-6">
          <div class="flex items-center mb-4">
            <span class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2.5 py-0.5 rounded-full">TH</span>
            <span class="ml-2 text-sm font-medium text-gray-700">ชื่อภาษาไทย</span>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Title Prefix Thai -->
            <div class="space-y-2">
              <label class="flex items-center text-xs sm:text-sm font-medium text-gray-700">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="truncate">คำนำหน้า</span>
              </label>
              <input
                type="text"
                v-model="form.title_prefix_th"
                class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm sm:text-base touch-manipulation placeholder-gray-400"
                placeholder="เช่น เด็กชาย, เด็กหญิง"
              >
            </div>

            <!-- First Name Thai -->
            <div class="space-y-2">
              <label class="flex items-center text-xs sm:text-sm font-medium text-gray-700">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="truncate">ชื่อ</span>
              </label>
              <input
                type="text"
                v-model="form.first_name_th"
                class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm sm:text-base touch-manipulation placeholder-gray-400"
                placeholder="ชื่อ"
              >
            </div>

            <!-- Last Name Thai -->
            <div class="space-y-2">
              <label class="flex items-center text-xs sm:text-sm font-medium text-gray-700">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="truncate">นามสกุล</span>
              </label>
              <input
                type="text"
                v-model="form.last_name_th"
                class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm sm:text-base touch-manipulation placeholder-gray-400"
                placeholder="นามสกุล"
              >
            </div>
          </div>
        </div>

        <!-- English Names Section -->
        <div>
          <div class="flex items-center mb-4">
            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">EN</span>
            <span class="ml-2 text-sm font-medium text-gray-700">ชื่อภาษาอังกฤษ</span>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Title Prefix English -->
            <div class="space-y-2">
              <label class="flex items-center text-xs sm:text-sm font-medium text-gray-700">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="truncate">Title</span>
              </label>
              <input
                type="text"
                v-model="form.title_prefix_en"
                class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm sm:text-base touch-manipulation placeholder-gray-400"
                placeholder="e.g. Mr., Ms."
              >
            </div>

            <!-- First Name English -->
            <div class="space-y-2">
              <label class="flex items-center text-xs sm:text-sm font-medium text-gray-700">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="truncate">First Name</span>
              </label>
              <input
                type="text"
                v-model="form.first_name_en"
                class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm sm:text-base touch-manipulation placeholder-gray-400"
                placeholder="First Name"
              >
            </div>

            <!-- Last Name English -->
            <div class="space-y-2">
              <label class="flex items-center text-xs sm:text-sm font-medium text-gray-700">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="truncate">Last Name</span>
              </label>
              <input
                type="text"
                v-model="form.last_name_en"
                class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm sm:text-base touch-manipulation placeholder-gray-400"
                placeholder="Last Name"
              >
            </div>
          </div>
        </div>
      </div>


      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        <!-- Citizen ID Field -->
        <div class="space-y-2">
          <label class="flex items-center text-xs sm:text-sm font-medium text-gray-700">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 011-1h2a2 2 0 011 1v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
            </svg>
            <span class="truncate">เลขประจำตัวประชาชน</span>
          </label>
          <input
            type="text"
            :value="formatCitizenId(form.citizen_id)"
            class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm sm:text-base touch-manipulation"
            readonly
          >
        </div>

        <!-- Student ID Field -->
        <div class="space-y-2">
          <label class="flex items-center text-xs sm:text-sm font-medium text-gray-700">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a2 2 0 012-2z" />
            </svg>
            <span class="truncate">รหัสนักเรียน</span>
          </label>
          <input
            type="text"
            v-model="form.student_id"
            class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm sm:text-base touch-manipulation"
            readonly
          >
        </div>

        <!-- Nickname Field -->
        <div class="space-y-2">
          <label class="flex items-center text-xs sm:text-sm font-medium text-gray-700">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.01M15 10h1.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="truncate">ชื่อเล่น</span>
          </label>
          <input
            type="text"
            v-model="form.nickname"
            class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm sm:text-base touch-manipulation"           
          >
        </div>

        <!-- Gender Field -->
        <div class="space-y-2">
          <label class="flex items-center text-xs sm:text-sm font-medium text-gray-700">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="truncate">เพศ</span>
          </label>
          <div class="space-y-1">
            <select v-model.number="form.gender"
              class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm sm:text-base touch-manipulation"
            >
              <option :value="null" disabled>-- เลือกเพศ --</option>
              <option :value="1">ชาย</option>
              <option :value="0">หญิง</option>
            </select>
            <!-- Debug info (temporary) -->
            <!-- <div class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded">
              Debug: DB={{ props.student?.gender }}, Form={{ form.gender }}, Type={{ typeof form.gender }}
            </div> -->
            
            <!-- Display current gender status -->
            <!-- <div v-if="form.gender !== null && form.gender !== undefined" class="text-xs text-gray-600 bg-emerald-50 px-2 py-1 rounded flex items-center">
              <svg class="w-3 h-3 mr-1 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              เพศปัจจุบัน: {{ currentGenderText }} (จากฐานข้อมูล)
            </div> -->
          </div>
        </div>

        <!-- Date of Birth Field -->
        <div class="space-y-2">
          <label class="flex items-center text-xs sm:text-sm font-medium text-gray-700">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="truncate">วันเกิด</span>
          </label>
          <div class="space-y-2">
            <input
              type="date"
              v-model="form.date_of_birth"
              class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm sm:text-base touch-manipulation"
            >
            <div class="flex items-center justify-between text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded">
              <span class="flex items-center">
                <svg class="w-3 h-3 mr-1 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ formatBirthDate(form.date_of_birth) }}
              </span>
              <span class="flex items-center">
                <svg class="w-3 h-3 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                อายุ {{ calculateAge(form.date_of_birth) }} ปี
              </span>
            </div>
          </div>
        </div>

        <!-- Nationality Field -->
        <div class="space-y-2">
          <label class="flex items-center text-xs sm:text-sm font-medium text-gray-700">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="truncate">สัญชาติ</span>
          </label>
          <input
            type="text"
            v-model="form.nationality"
            class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm sm:text-base touch-manipulation placeholder-gray-400"
            placeholder="สัญชาติ"
          >
        </div>

        <!-- Religion Field -->
        <div class="space-y-2">
          <label class="flex items-center text-xs sm:text-sm font-medium text-gray-700">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
            </svg>
            <span class="truncate">ศาสนา</span>
          </label>
          <input
            type="text"
            v-model="form.religion"
            class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm sm:text-base touch-manipulation placeholder-gray-400"
            placeholder="ศาสนา"
          >
        </div>

        <!-- Class Level Field -->
        <div class="space-y-2">
          <label class="flex items-center text-xs sm:text-sm font-medium text-gray-700">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span class="truncate">ระดับชั้นปัจจุบัน(ม.) </span>
          </label>
          <input
            type="text"
            v-model="form.class_level"
            class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm sm:text-base touch-manipulation"
            readonly
          >
        </div>

        <!-- Class Section Field -->
        <div class="space-y-2">
          <label class="flex items-center text-xs sm:text-sm font-medium text-gray-700">
            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span class="truncate">ห้องเรียน(/)</span>
          </label>
          <input
            type="text"
            v-model="form.class_section"
            class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-sm sm:text-base touch-manipulation"
            readonly
          >
        </div>
      </div>

      <!-- Data Source Info -->
      <div class="mt-6 pt-4 border-t border-gray-200">
        <div class="flex flex-wrap gap-2 text-xs text-gray-500">
          <!-- <div v-if="classDisplayText" class="flex items-center bg-emerald-50 px-2 py-1 rounded">
            <svg class="w-3 h-3 mr-1 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            ห้องเรียน: {{ classDisplayText }}
          </div> -->
          <div class="flex items-center bg-gray-50 px-2 py-1 rounded">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            อัปเดต: {{ new Date().toLocaleDateString('th-TH') }} {{ new Date().toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' }) }}
          </div>
        </div>
      </div>


      <!-- Save Button Section -->
      <div class="px-4 py-4 sm:px-6 sm:py-6 ">
        <div class="flex justify-between items-center">
          <div class="text-xs sm:text-sm text-gray-500">
            <span v-if="isSaving" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              กำลังบันทึก...
            </span>
            <span v-else-if="saveStatus === 'success'" class="flex items-center text-green-600">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              บันทึกสำเร็จ
            </span>
            <span v-else-if="saveStatus === 'error'" class="flex items-center text-red-600">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
              เกิดข้อผิดพลาด
            </span>
            <span v-else>ข้อมูลพื้นฐานของนักเรียน</span>
          </div>
          
          <button
            type="button"
            @click="saveStudentData"
            :disabled="isSaving"
            class="inline-flex items-center px-4 py-2 mt-2 sm:px-6 sm:py-2.5 bg-emerald-600 text-white text-sm sm:text-base font-medium rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors touch-manipulation"
          >
            <svg v-if="!isSaving" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0h8m0 0h2a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4 0V5a2 2 0 011-1h4a2 2 0 011 1v2M8 7V5a2 2 0 011-1h4a2 2 0 011 1v2m0 0v4m0 0h-4m4 0V9H8v4z"></path>
            </svg>
            <svg v-else class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isSaving ? 'กำลังบันทึก...' : 'บันทึกข้อมูลพื้นฐาน' }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Photo Modal -->
  <div v-if="showPhotoModal" class="fixed inset-0 z-50 overflow-y-auto" @click="closePhotoModal">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 transition-opacity bg-black bg-opacity-75" @click="closePhotoModal"></div>

      <!-- Modal content -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full" @click.stop>
        <!-- Header -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 flex items-center justify-between border-b">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            รูปภาพ {{ form.title_prefix_th }} {{ form.first_name_th }} {{ form.last_name_th }}
          </h3>
          <button @click="closePhotoModal" class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition ease-in-out duration-150">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Photo content -->
        <div class="bg-white px-4 py-6 sm:px-6">
          <div class="flex justify-center">
            <img
              :src="studentPhoto"
              :alt="`${form.first_name_th} ${form.last_name_th}`"
              class="max-w-full max-h-96 object-contain rounded-lg shadow-lg"
            />
          </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t">
          <button
            @click="closePhotoModal"
            type="button"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
          >
            ปิด
          </button>
        </div>
      </div>
    </div>
  </div>
</template>


