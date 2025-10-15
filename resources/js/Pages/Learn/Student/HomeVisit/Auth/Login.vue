<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'

const activeTab = ref('student')
const isSubmitting = ref(false)

// Student login form
const studentForm = useForm({
    national_id: '',
    student_id: '',
})

// Teacher login form  
const teacherForm = useForm({
    username: '',
    password: '',
})

// Admin login form  
const adminForm = useForm({
    username: '',
    password: '',
})

const switchTab = (tab) => {
    activeTab.value = tab
    // Clear any previous errors
    studentForm.clearErrors()
    teacherForm.clearErrors()
    adminForm.clearErrors()
}

const handleStudentLogin = () => {
    isSubmitting.value = true
    studentForm.post(route('homevisit.student.login'), {
        onFinish: () => {
            isSubmitting.value = false
        }
    })
}

const handleTeacherLogin = () => {
    isSubmitting.value = true
    teacherForm.post(route('homevisit.teacher.login'), {
        onFinish: () => {
            isSubmitting.value = false
        }
    })
}

const handleAdminLogin = () => {
    isSubmitting.value = true
    adminForm.post(route('homevisit.admin.login'), {
        onFinish: () => {
            isSubmitting.value = false
        }
    })
}
</script>

<template>
    <Head title="ระบบเยี่ยมบ้านนักเรียน - เข้าสู่ระบบ" />
    
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 flex items-center justify-center p-4">
        <div class="max-w-md w-full">
            <!-- Logo and Title -->
            <div class="text-center mb-8">
                <div class="bg-white rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center shadow-lg">
                    <span class="text-4xl">🏠</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">ระบบเยี่ยมบ้านนักเรียน</h1>
                <p class="text-gray-600">โรงเรียนจริยธรรมศึกษามูลนิธิ</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Tab Headers -->
                <div class="flex">
                    <button 
                        @click="switchTab('student')"
                        :class="[
                            'flex-1 py-3 px-4 text-center font-semibold transition-all duration-200',
                            activeTab === 'student' 
                                ? 'bg-blue-500 text-white' 
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                        ]"
                    >
                        <span class="block text-xl mb-1">👨‍🎓</span>
                        นักเรียน
                    </button>
                    <button 
                        @click="switchTab('teacher')"
                        :class="[
                            'flex-1 py-3 px-4 text-center font-semibold transition-all duration-200',
                            activeTab === 'teacher' 
                                ? 'bg-green-500 text-white' 
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                        ]"
                    >
                        <span class="block text-xl mb-1">👩‍🏫</span>
                        ครู
                    </button>
                    <button 
                        @click="switchTab('admin')"
                        :class="[
                            'flex-1 py-3 px-4 text-center font-semibold transition-all duration-200',
                            activeTab === 'admin' 
                                ? 'bg-purple-500 text-white' 
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                        ]"
                    >
                        <span class="block text-xl mb-1">⚙️</span>
                        แอดมิน
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="p-8">
                    <!-- Student Login Tab -->
                    <div v-if="activeTab === 'student'">
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">เข้าสู่ระบบสำหรับนักเรียน</h2>
                            <p class="text-gray-600 text-sm">ใช้หมายเลขบัตรประชาชนและรหัสนักเรียนในการเข้าสู่ระบบ</p>
                        </div>

                        <form @submit.prevent="handleStudentLogin" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    หมายเลขบัตรประจำตัวประชาชน <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="studentForm.national_id"
                                    type="text" 
                                    class="w-full px-4 py-3 border placeholder:text-gray-400 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="เช่น 1234567890123"
                                    maxlength="13"
                                    required
                                />
                                <div v-if="studentForm.errors.national_id" class="text-red-500 text-sm mt-1">
                                    {{ studentForm.errors.national_id }}
                                </div>
                            </div>

                            <div>
                                <label class="block marker: text-sm font-medium text-gray-700 mb-2">
                                    รหัสนักเรียน <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="studentForm.student_id"
                                    type="text" 
                                    class="w-full px-4 py-3 border placeholder:text-gray-400 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="เช่น 12345"
                                    required
                                />
                                <div v-if="studentForm.errors.student_id" class="text-red-500 text-sm mt-1">
                                    {{ studentForm.errors.student_id }}
                                </div>
                            </div>

                            <div v-if="studentForm.errors.credentials" class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <p class="text-red-700 text-sm">{{ studentForm.errors.credentials }}</p>
                            </div>

                            <button 
                                type="submit"
                                :disabled="studentForm.processing || isSubmitting"
                                class="w-full bg-blue-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200"
                            >
                                <span v-if="studentForm.processing || isSubmitting">กำลังตรวจสอบ...</span>
                                <span v-else>เข้าสู่ระบบ</span>
                            </button>
                        </form>
                    </div>

                    <!-- Teacher Login Tab -->
                    <div v-if="activeTab === 'teacher'">
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">เข้าสู่ระบบสำหรับครู</h2>
                            <p class="text-gray-600 text-sm">ใช้ชื่อผู้ใช้และรหัสผ่านในการเข้าสู่ระบบ</p>
                        </div>

                        <form @submit.prevent="handleTeacherLogin" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    ชื่อผู้ใช้ <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="teacherForm.username"
                                    type="text" 
                                    class="w-full px-4 py-3 placeholder:text-gray-400 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    placeholder="ใส่ชื่อผู้ใช้"
                                    required
                                />
                                <div v-if="teacherForm.errors.username" class="text-red-500 text-sm mt-1">
                                    {{ teacherForm.errors.username }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    รหัสผ่าน <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="teacherForm.password"
                                    type="password" 
                                    class="w-full px-4 py-3 border placeholder:text-gray-400 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    placeholder="รหัสผ่าน"
                                    required
                                />
                                <div v-if="teacherForm.errors.password" class="text-red-500 text-sm mt-1">
                                    {{ teacherForm.errors.password }}
                                </div>
                            </div>

                            <div v-if="teacherForm.errors.credentials" class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <p class="text-red-700 text-sm">{{ teacherForm.errors.credentials }}</p>
                            </div>

                            <button 
                                type="submit"
                                :disabled="teacherForm.processing || isSubmitting"
                                class="w-full bg-green-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200"
                            >
                                <span v-if="teacherForm.processing || isSubmitting">กำลังตรวจสอบ...</span>
                                <span v-else>เข้าสู่ระบบ</span>
                            </button>
                        </form>
                    </div>

                    <!-- Admin Login Tab -->
                    <div v-if="activeTab === 'admin'">
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">เข้าสู่ระบบแอดมิน</h2>
                            <p class="text-gray-600 text-sm">สำหรับผู้ดูแลระบบเท่านั้น</p>
                        </div>

                        <form @submit.prevent="handleAdminLogin" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    ชื่อผู้ใช้ <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="adminForm.username"
                                    type="text" 
                                    class="w-full px-4 py-3 border placeholder:text-gray-400 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="ใส่ชื่อผู้ใช้แอดมิน"
                                    required
                                />
                                <div v-if="adminForm.errors.username" class="text-red-500 text-sm mt-1">
                                    {{ adminForm.errors.username }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    รหัสผ่าน <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="adminForm.password"
                                    type="password" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="ใส่รหัสผ่าน"
                                    required
                                />
                                <div v-if="adminForm.errors.password" class="text-red-500 text-sm mt-1">
                                    {{ adminForm.errors.password }}
                                </div>
                            </div>

                            <div v-if="adminForm.errors.credentials" class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <p class="text-red-700 text-sm">{{ adminForm.errors.credentials }}</p>
                            </div>

                            <button 
                                type="submit"
                                :disabled="adminForm.processing || isSubmitting"
                                class="w-full bg-purple-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-purple-600 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200"
                            >
                                <span v-if="adminForm.processing || isSubmitting">กำลังตรวจสอบ...</span>
                                <span v-else>เข้าสู่ระบบ</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-8 py-4 text-center">
                    <p class="text-xs text-gray-500">
                        © 2025 โรงเรียนจริยธรรมศึกษามูลนิธิ<br>
                        ระบบเยี่ยมบ้านนักเรียน
                    </p>
                </div>
            </div>

            <!-- Help Text -->
            <div class="mt-6 text-center">
                <div class="bg-blue-50 rounded-lg p-4 text-sm text-blue-700">
                    <div class="font-medium mb-2">📋 คำแนะนำการใช้งาน</div>
                    <div class="space-y-1 text-left">
                        <p><strong>นักเรียน:</strong> ใช้หมายเลขบัตรประชาชนและรหัสนักเรียนเพื่อดูและแก้ไขข้อมูลส่วนตัว</p>
                        <p><strong>ครู:</strong> ใช้บัญชีครูเพื่อค้นหาและจัดการข้อมูลนักเรียนทุกคน</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Add any additional custom styles here */
</style>