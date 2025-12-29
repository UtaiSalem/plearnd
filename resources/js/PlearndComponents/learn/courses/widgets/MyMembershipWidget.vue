<script setup>
import { ref, computed } from 'vue';
import { Icon } from '@iconify/vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    courseMemberOfAuth: Object,
    isCourseAdmin: Boolean,
});

const isExpanded = ref(true);

// สถานะการเป็นสมาชิก
const memberStatus = computed(() => {
    if (!props.courseMemberOfAuth) return { label: 'ไม่ได้เป็นสมาชิก', color: 'gray', icon: 'mdi:account-off' };
    
    const status = props.courseMemberOfAuth.course_member_status;
    const statusMap = {
        0: { label: 'รอการอนุมัติ', color: 'yellow', icon: 'mdi:account-clock' },
        1: { label: 'สมาชิก', color: 'green', icon: 'mdi:account-check' },
        2: { label: 'ไม่ใช้งาน', color: 'gray', icon: 'mdi:account-off' },
        3: { label: 'ถูกระงับ', color: 'red', icon: 'mdi:account-cancel' },
    };
    return statusMap[status] ?? { label: 'สมาชิก', color: 'green', icon: 'mdi:account-check' };
});

// Role ของสมาชิก
const memberRole = computed(() => {
    if (props.isCourseAdmin) return { label: 'ผู้ดูแล/ครูผู้สอน', color: 'purple', icon: 'mdi:shield-crown' };
    if (!props.courseMemberOfAuth) return null;
    
    const role = props.courseMemberOfAuth.role;
    const roleMap = {
        'admin': { label: 'ผู้ดูแล', color: 'purple', icon: 'mdi:shield-account' },
        'teacher': { label: 'ครูผู้สอน', color: 'blue', icon: 'mdi:human-male-board' },
        'student': { label: 'นักเรียน', color: 'cyan', icon: 'mdi:school' },
        'member': { label: 'สมาชิก', color: 'teal', icon: 'mdi:account' },
    };
    return roleMap[role] || { label: role || 'นักเรียน', color: 'cyan', icon: 'mdi:school' };
});

// กลุ่มเรียน
const memberGroup = computed(() => {
    return props.courseMemberOfAuth?.group || null;
});

// เลขที่
const orderNumber = computed(() => {
    return props.courseMemberOfAuth?.order_number || null;
});

// รหัสสมาชิก
const memberCode = computed(() => {
    return props.courseMemberOfAuth?.member_code || null;
});

// ชื่อสมาชิก
const memberName = computed(() => {
    return props.courseMemberOfAuth?.member_name || props.courseMemberOfAuth?.user?.name || null;
});

// คะแนนที่ได้
const achievedScore = computed(() => {
    return props.courseMemberOfAuth?.achieved_score || 0;
});

// คะแนนโบนัส
const bonusPoints = computed(() => {
    return props.courseMemberOfAuth?.bonus_points || 0;
});

// คะแนนที่แก้ไข
const editedGrade = computed(() => {
    return props.courseMemberOfAuth?.edited_grade || 0;
});

// คะแนนเต็ม
const totalScore = computed(() => {
    return props.course?.data?.total_score || 100;
});

// คะแนนรวมทั้งหมด (achieved + bonus + edited)
const totalAchieved = computed(() => {
    return achievedScore.value + bonusPoints.value + editedGrade.value;
});

// เปอร์เซ็นต์ความก้าวหน้า
const progressPercent = computed(() => {
    if (totalScore.value === 0) return 0;
    return Math.min(100, Math.round((totalAchieved.value / totalScore.value) * 100));
});

// ความก้าวหน้าการเรียน (grade_progress)
const gradeProgress = computed(() => {
    return props.courseMemberOfAuth?.grade_progress || 0;
});

// ประสิทธิภาพ
const efficiency = computed(() => {
    return props.courseMemberOfAuth?.efficiency || 0;
});

// วันที่ลงทะเบียน
const enrollmentDate = computed(() => {
    if (!props.courseMemberOfAuth?.enrollment_date) return null;
    return new Date(props.courseMemberOfAuth.enrollment_date).toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
});

// วันหมดอายุการเข้าถึง
const accessExpiryDate = computed(() => {
    if (!props.courseMemberOfAuth?.access_expiry_date) return null;
    return new Date(props.courseMemberOfAuth.access_expiry_date).toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
});

// วันที่สำเร็จการศึกษา
const completionDate = computed(() => {
    if (!props.courseMemberOfAuth?.completion_date) return null;
    return new Date(props.courseMemberOfAuth.completion_date).toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
});

// หมายเหตุ
const notesComments = computed(() => {
    return props.courseMemberOfAuth?.notes_comments || null;
});

// สถานะกลุ่ม
const groupMemberStatus = computed(() => {
    const status = props.courseMemberOfAuth?.group_member_status;
    if (status === 0) return { label: 'รอการอนุมัติ', color: 'yellow' };
    if (status === 1) return { label: 'ใช้งาน', color: 'green' };
    return null;
});

// เป็นสมาชิกที่ active หรือไม่
const isActiveMember = computed(() => {
    return props.courseMemberOfAuth && props.courseMemberOfAuth.course_member_status === 1;
});

const navigateToProgress = () => {
    router.visit(`/courses/${props.course.data.id}/progress`);
};

const navigateToAttendances = () => {
    router.visit(`/courses/${props.course.data.id}/attendances`);
};

const navigateToSettings = () => {
    router.visit(`/courses/${props.course.data.id}/members/${props.courseMemberOfAuth.id}/settings`);
};
</script>

<template>
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Header -->
        <button 
            @click="isExpanded = !isExpanded"
            class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 flex items-center justify-between"
        >
            <h3 class="text-white font-semibold flex items-center gap-2">
                <Icon icon="mdi:card-account-details" class="w-5 h-5" />
                ข้อมูลของฉัน
            </h3>
            <Icon 
                :icon="isExpanded ? 'mdi:chevron-up' : 'mdi:chevron-down'" 
                class="w-5 h-5 text-white transition-transform duration-200"
            />
        </button>
        
        <!-- Content -->
        <div v-show="isExpanded" class="p-4 space-y-3">
            <!-- สถานะและ Role -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div 
                        class="w-10 h-10 rounded-full flex items-center justify-center"
                        :class="{
                            'bg-green-100 text-green-600': memberStatus.color === 'green',
                            'bg-yellow-100 text-yellow-600': memberStatus.color === 'yellow',
                            'bg-red-100 text-red-600': memberStatus.color === 'red',
                            'bg-gray-100 text-gray-600': memberStatus.color === 'gray',
                        }"
                    >
                        <Icon :icon="memberStatus.icon" class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ memberStatus.label }}</p>
                        <p v-if="memberRole" class="text-xs text-gray-500">{{ memberRole.label }}</p>
                    </div>
                </div>
                
                <!-- Badge สำหรับ Admin -->
                <div v-if="isCourseAdmin" class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">
                    <Icon icon="mdi:crown" class="w-3 h-3 inline mr-1" />
                    Admin
                </div>
            </div>

            <!-- ชื่อสมาชิก -->
            <div v-if="memberName && courseMemberOfAuth" class="bg-gray-50 rounded-lg p-2">
                <p class="text-xs text-gray-500">ชื่อในระบบ</p>
                <p class="text-sm font-medium text-gray-800">{{ memberName }}</p>
            </div>

            <!-- กลุ่มเรียน, เลขที่, รหัสสมาชิก -->
            <div v-if="courseMemberOfAuth" class="grid grid-cols-3 gap-2">
                <div v-if="memberGroup" class="bg-blue-50 rounded-lg p-2 text-center">
                    <p class="text-xs text-blue-600 mb-0.5">กลุ่ม</p>
                    <p class="text-sm font-semibold text-blue-800 truncate">{{ memberGroup.name }}</p>
                </div>
                <div v-if="orderNumber" class="bg-teal-50 rounded-lg p-2 text-center">
                    <p class="text-xs text-teal-600 mb-0.5">เลขที่</p>
                    <p class="text-sm font-semibold text-teal-800">{{ orderNumber }}</p>
                </div>
                <div v-if="memberCode" class="bg-orange-50 rounded-lg p-2 text-center">
                    <p class="text-xs text-orange-600 mb-0.5">รหัส</p>
                    <p class="text-sm font-semibold text-orange-800">{{ memberCode }}</p>
                </div>
            </div>

            <!-- คะแนนและความก้าวหน้า -->
            <div v-if="isActiveMember" class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-3">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs text-indigo-600">คะแนนสะสม</p>
                    <p class="text-sm font-bold text-indigo-700">
                        {{ totalAchieved.toFixed(1) }} / {{ totalScore }}
                    </p>
                </div>
                <div class="w-full bg-indigo-200 rounded-full h-2.5">
                    <div 
                        class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2.5 rounded-full transition-all duration-500"
                        :style="{ width: progressPercent + '%' }"
                    ></div>
                </div>
                <p class="text-xs text-indigo-500 mt-1 text-right">{{ progressPercent }}%</p>
                
                <!-- รายละเอียดคะแนน -->
                <div v-if="bonusPoints > 0 || editedGrade !== 0" class="mt-2 pt-2 border-t border-indigo-100 grid grid-cols-3 gap-1 text-center text-xs">
                    <div>
                        <p class="text-indigo-400">ปกติ</p>
                        <p class="font-medium text-indigo-600">{{ achievedScore.toFixed(1) }}</p>
                    </div>
                    <div v-if="bonusPoints > 0">
                        <p class="text-green-400">โบนัส</p>
                        <p class="font-medium text-green-600">+{{ bonusPoints }}</p>
                    </div>
                    <div v-if="editedGrade !== 0">
                        <p class="text-amber-400">ปรับ</p>
                        <p class="font-medium" :class="editedGrade > 0 ? 'text-green-600' : 'text-red-600'">
                            {{ editedGrade > 0 ? '+' : '' }}{{ editedGrade }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- สถานะเพิ่มเติม -->
            <div v-if="isActiveMember && (gradeProgress > 0 || efficiency > 0)" class="grid grid-cols-2 gap-2">
                <div v-if="gradeProgress > 0" class="bg-cyan-50 rounded-lg p-2 text-center">
                    <p class="text-xs text-cyan-600">ความก้าวหน้า</p>
                    <p class="text-lg font-bold text-cyan-700">{{ gradeProgress }}%</p>
                </div>
                <div v-if="efficiency > 0" class="bg-emerald-50 rounded-lg p-2 text-center">
                    <p class="text-xs text-emerald-600">ประสิทธิภาพ</p>
                    <p class="text-lg font-bold text-emerald-700">{{ efficiency }}%</p>
                </div>
            </div>

            <!-- วันที่ -->
            <div v-if="courseMemberOfAuth" class="space-y-1 text-xs text-gray-500">
                <div v-if="enrollmentDate" class="flex items-center gap-2">
                    <Icon icon="mdi:calendar-check" class="w-4 h-4 text-green-500" />
                    <span>ลงทะเบียน: {{ enrollmentDate }}</span>
                </div>
                <div v-if="accessExpiryDate" class="flex items-center gap-2">
                    <Icon icon="mdi:calendar-alert" class="w-4 h-4 text-orange-500" />
                    <span>หมดอายุ: {{ accessExpiryDate }}</span>
                </div>
                <div v-if="completionDate" class="flex items-center gap-2">
                    <Icon icon="mdi:calendar-star" class="w-4 h-4 text-purple-500" />
                    <span>สำเร็จ: {{ completionDate }}</span>
                </div>
            </div>

            <!-- หมายเหตุ -->
            <div v-if="notesComments" class="bg-yellow-50 rounded-lg p-2 border-l-4 border-yellow-400">
                <p class="text-xs text-yellow-600 mb-1">หมายเหตุ</p>
                <p class="text-sm text-yellow-800">{{ notesComments }}</p>
            </div>

            <!-- Quick Actions -->
            <div v-if="isActiveMember" class="flex gap-2 pt-2 border-t border-gray-100">
                <button 
                    @click="navigateToProgress"
                    class="flex-1 flex items-center justify-center gap-1 px-2 py-2 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-medium hover:bg-indigo-200 transition-colors"
                >
                    <Icon icon="mdi:chart-line" class="w-4 h-4" />
                    ผลการเรียน
                </button>
                <button 
                    @click="navigateToAttendances"
                    class="flex-1 flex items-center justify-center gap-1 px-2 py-2 bg-purple-100 text-purple-700 rounded-lg text-xs font-medium hover:bg-purple-200 transition-colors"
                >
                    <Icon icon="mdi:calendar-account" class="w-4 h-4" />
                    เช็คชื่อ
                </button>
                <button 
                    @click="navigateToSettings"
                    class="flex items-center justify-center px-2 py-2 bg-gray-100 text-gray-600 rounded-lg text-xs font-medium hover:bg-gray-200 transition-colors"
                    title="ตั้งค่า"
                >
                    <Icon icon="mdi:cog" class="w-4 h-4" />
                </button>
            </div>

            <!-- ข้อความสำหรับผู้ที่ยังไม่เป็นสมาชิก -->
            <div v-if="!courseMemberOfAuth" class="text-center py-2">
                <p class="text-sm text-gray-500">คุณยังไม่ได้เป็นสมาชิกของคอร์สนี้</p>
            </div>
        </div>
    </div>
</template>
