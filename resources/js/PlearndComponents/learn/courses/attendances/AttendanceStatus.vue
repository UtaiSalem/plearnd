<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';
import { useAttendanceStore } from '@/stores/attendance';
import axios from 'axios';

// Click outside directive
const vClickOutside = {
    mounted(el, binding) {
        el.clickOutsideEvent = (event) => {
            if (!(el === event.target || el.contains(event.target))) {
                binding.value(event);
            }
        };
        document.addEventListener('click', el.clickOutsideEvent);
    },
    unmounted(el) {
        document.removeEventListener('click', el.clickOutsideEvent);
    }
};

const props = defineProps({
    status: {
        type: Number,
        default: null
    },
    attendance: {
        type: Object,
        required: true
    },
    memberId: {
        type: Number,
        required: true
    },
});

const attendanceStore = useAttendanceStore();
const refStatus = ref(props.status);
const isPending = ref(computeIsPending());
const isCheckingStatus = ref(false);
const refreshInterval = ref(null);
const hasError = ref(false);
const errorMessage = ref('');
const showStatusMenu = ref(false);
const isUpdatingStatus = ref(false);
const menuButtonRef = ref(null);
const dropdownPosition = ref({ top: 0, left: 0 });

// Helper function to compute pending status
function computeIsPending() {
    if (!props.attendance || !props.attendance.finish_at) return false;
    const now = new Date();
    const finishTime = new Date(props.attendance.finish_at);
    const statusNotSet = props.status === null || props.status === undefined;
    return now < finishTime && statusNotSet;
}


// Watch for props.status changes to update refStatus
watch(() => props.status, (newStatus) => {
    if (newStatus !== null && newStatus !== undefined) {
        refStatus.value = newStatus;
        isPending.value = false;
        hasError.value = false;
        errorMessage.value = '';
    }
}, { immediate: false });

// Watch for attendance changes to recompute pending status
watch(() => props.attendance, () => {
    isPending.value = computeIsPending();
}, { deep: true });

const attendanceStatus = computed(()=>{
    switch (refStatus.value) {
        case 0:
            return {
                icon:'heroicons-outline:x-circle',
                color:'text-red-500',
                label: 'ขาด',
                bgColor: 'bg-red-50'
            };
        case 1:
            return {
                icon:'heroicons-outline:check-circle',
                color:'text-green-500',
                label: 'มา',
                bgColor: 'bg-green-50'
            };
        case 2:
            return {
                icon:'heroicons-outline:clock',
                color:'text-yellow-500',
                label: 'สาย',
                bgColor: 'bg-yellow-50'
            };
        case 3:
            return {
                icon:'heroicons-outline:document-text',
                color:'text-blue-500',
                label: 'ลา',
                bgColor: 'bg-blue-50'
            };
        default:
            return {
                icon:'heroicons-outline:minus-circle',
                color:'text-gray-500',
                label: 'ไม่มีข้อมูล',
                bgColor: 'bg-gray-50'
            };
    }
});

const statusOptions = [
    { value: 1, label: 'มา', icon: 'heroicons-outline:check-circle', color: 'text-green-500', bgColor: 'bg-green-50' },
    { value: 2, label: 'สาย', icon: 'heroicons-outline:clock', color: 'text-yellow-500', bgColor: 'bg-yellow-50' },
    { value: 3, label: 'ลา', icon: 'heroicons-outline:document-text', color: 'text-blue-500', bgColor: 'bg-blue-50' },
    { value: 0, label: 'ขาด', icon: 'heroicons-outline:x-circle', color: 'text-red-500', bgColor: 'bg-red-50' },
];

// Accessibility text for screen readers
const accessibilityText = computed(() => {
    if (hasError.value) {
        return `สถานะการเช็คชื่อ: ${errorMessage.value}`;
    }
    if (isPending.value) {
        return `รอการประมวลผลสถานะการเช็คชื่อ${isCheckingStatus.value ? ' กำลังตรวจสอบ' : ''}`;
    }
    return `สถานะการเช็คชื่อ: ${attendanceStatus.value.label}`;
});

// Check if the current user is a course admin
const isCourseAdmin = computed(() => {
    return usePage().props.isCourseAdmin || false;
});

// Function to check attendance status from backend (Manual check only)
const checkAttendanceStatus = async () => {
    if (isCheckingStatus.value) return;
    
    isCheckingStatus.value = true;
    hasError.value = false;
    errorMessage.value = '';
    
    try {
        const status = await attendanceStore.fetchMemberJoinStatus(props.attendance.id, props.memberId);
        
        if (status !== null && status !== undefined && status !== refStatus.value) {
            refStatus.value = status;
            isPending.value = false;
            attendanceStore.updateMemberStatusInAttendance(props.attendance.id, props.memberId, status);
        }
    } catch (error) {
        if (error.response?.status === 404) {
             errorMessage.value = 'ไม่พบข้อมูลการเช็คชื่อ';
             hasError.value = true;
             isPending.value = false;
        } else if (error.response?.status === 403) {
             errorMessage.value = 'ไม่มีสิทธิ์เข้าถึงข้อมูล';
             hasError.value = true;
             isPending.value = false;
        } else {
             errorMessage.value = 'เกิดข้อผิดพลาดในการตรวจสอบสถานะ';
             hasError.value = true;
        }
    } finally {
        isCheckingStatus.value = false;
    }
};

const toggleStatusMenu = () => {
    if (isUpdatingStatus.value) return;
    
    if (!showStatusMenu.value && menuButtonRef.value) {
        const rect = menuButtonRef.value.getBoundingClientRect();
        dropdownPosition.value = {
            top: rect.bottom + 8,
            left: rect.right - 192 // 192px is w-48
        };
    }
    
    showStatusMenu.value = !showStatusMenu.value;
};

const closeStatusMenu = () => {
    showStatusMenu.value = false;
};

const updateStatusByAdmin = async (newStatus, event) => {
    if (isUpdatingStatus.value) return;
    
    // Close menu immediately
    showStatusMenu.value = false;
    
    isUpdatingStatus.value = true;
    
    try {
        await attendanceStore.updateMemberStatusInAttendance(props.attendance.id, props.memberId, newStatus);
        refStatus.value = newStatus;
        isPending.value = false;
        
        // Show success toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true
        });
        
        Toast.fire({
            icon: 'success',
            title: 'อัปเดตสถานะเรียบร้อย'
        });
        
    } catch (error) {
        console.error('Failed to update status:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถอัปเดตสถานะได้ กรุณาลองใหม่',
        });
    } finally {
        isUpdatingStatus.value = false;
    }
};

// Manual refresh function for course admins
const handlePendingClick = async () => {
    if (isCourseAdmin.value && !isCheckingStatus.value) {
        await checkAttendanceStatus();
    }
};


</script>

<template>
    <!-- Handle case when attendance data is not available -->
    <div v-if="!props.attendance || !props.memberId" class="flex items-center justify-center p-3">
        <div
            class="text-center bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 shadow-sm border border-gray-200"
            role="status"
            aria-label="ไม่มีข้อมูลการเช็คชื่อ"
        >
            <Icon icon="heroicons-outline:question-mark-circle" width="28" height="28" class="text-gray-400 mx-auto mb-2" aria-hidden="true" />
            <p class="text-xs text-gray-500 font-medium">ไม่มีข้อมูล</p>
        </div>
    </div>
    
    <!-- Normal attendance status display -->
    <div v-else-if="isPending" class="relative inline-flex items-center justify-center gap-1" :aria-label="accessibilityText">
        <!-- Pending status with clock icon (enhanced with animation) -->
        <button
            v-if="isCourseAdmin"
            @click="handlePendingClick"
            :disabled="isCheckingStatus"
            class="relative group p-2 rounded-xl transition-all duration-300 hover:shadow-lg hover:scale-105 bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-opacity-50"
            :title="isCheckingStatus ? 'กำลังตรวจสอบสถานะ...' : 'คลิกเพื่อตรวจสอบสถานะล่าสุด'"
            :aria-label="isCheckingStatus ? 'กำลังตรวจสอบสถานะ' : 'คลิกเพื่อตรวจสอบสถานะล่าสุด'"
            type="button"
        >
            <div class="relative">
                <Icon
                    icon="heroicons-outline:clock"
                    width="28"
                    height="28"
                    :class="[
                        'transition-all duration-300',
                        isCheckingStatus ? 'text-amber-600 animate-pulse' : 'text-amber-500 group-hover:text-amber-600'
                    ]"
                    aria-hidden="true"
                />
                <div v-if="isCheckingStatus" class="absolute inset-0 rounded-full border-2 border-amber-300 animate-ping" aria-hidden="true"></div>
            </div>
            <span class="absolute -top-1 -right-1 w-3 h-3 bg-amber-400 rounded-full animate-pulse" aria-hidden="true"></span>
            <!-- Error indicator -->
            <div v-if="hasError" class="absolute -bottom-1 -right-1 w-3 h-3 bg-red-500 rounded-full" aria-hidden="true" :title="errorMessage"></div>
        </button>
        
        <!-- Non-admin users just see the clock icon -->
        <div
            v-else
            class="relative p-2 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200"
            :title="isCheckingStatus ? 'กำลังตรวจสอบสถานะ...' : 'รอการประมวลผล'"
            role="status"
            :aria-label="isCheckingStatus ? 'กำลังตรวจสอบสถานะ' : 'รอการประมวลผลสถานะการเช็คชื่อ'"
        >
            <div class="relative">
                <Icon
                    icon="heroicons-outline:clock"
                    width="28"
                    height="28"
                    :class="[
                        'transition-colors duration-300',
                        isCheckingStatus ? 'text-blue-600 animate-pulse' : 'text-blue-500'
                    ]"
                    aria-hidden="true"
                />
                <div v-if="isCheckingStatus" class="absolute inset-0 rounded-full border-2 border-blue-300 animate-ping" aria-hidden="true"></div>
            </div>
            <span class="absolute -top-1 -right-1 w-3 h-3 bg-blue-400 rounded-full animate-pulse" aria-hidden="true"></span>
            <!-- Error indicator -->
            <div v-if="hasError" class="absolute -bottom-1 -right-1 w-3 h-3 bg-red-500 rounded-full" aria-hidden="true" :title="errorMessage"></div>
        </div>
        
        <!-- Admin edit button for pending status -->
        <button
            v-if="isCourseAdmin"
            ref="menuButtonRef"
            @click.stop="toggleStatusMenu"
            :disabled="isUpdatingStatus"
            class="p-2 rounded-lg transition-all duration-200 hover:bg-gray-100 active:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50"
            :class="{ 'opacity-50 cursor-not-allowed': isUpdatingStatus }"
            type="button"
            title="กำหนดสถานะการเข้าร่วม"
            aria-label="กำหนดสถานะการเข้าร่วม"
        >
            <Icon
                :icon="isUpdatingStatus ? 'heroicons-outline:arrow-path' : 'heroicons-outline:pencil'"
                width="20"
                height="20"
                :class="[
                    'transition-all duration-200',
                    isUpdatingStatus ? 'text-blue-500 animate-spin' : 'text-gray-600 hover:text-blue-600'
                ]"
                aria-hidden="true"
            />
        </button>
        
        <!-- Status menu dropdown for pending -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div
                    v-if="showStatusMenu && isCourseAdmin && isPending"
                    v-click-outside="closeStatusMenu"
                    class="fixed w-48 bg-white rounded-xl shadow-2xl border border-gray-200 z-[9999] overflow-hidden"
                    :style="{ top: `${dropdownPosition.top}px`, left: `${dropdownPosition.left}px` }"
                    role="menu"
                    aria-orientation="vertical"
                    aria-labelledby="status-menu-button"
                    @click.stop
                >
                    <div class="py-1">
                        <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">
                            เลือกสถานะ
                        </div>
                        <button
                            v-for="option in statusOptions"
                            :key="option.value"
                            @click.stop="updateStatusByAdmin(option.value, $event)"
                            :disabled="refStatus === option.value || isUpdatingStatus"
                            class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors duration-150"
                            :class="[
                                option.bgColor,
                                option.color,
                                refStatus === option.value 
                                    ? 'bg-gray-100 cursor-default opacity-60' 
                                    : 'hover:shadow-sm cursor-pointer',
                                isUpdatingStatus ? 'opacity-50 cursor-not-allowed' : ''
                            ]"
                            type="button"
                            role="menuitem"
                        >
                            <Icon
                                :icon="option.icon"
                                width="20"
                                height="20"
                                aria-hidden="true"
                            />
                            <span class="flex-1 text-left">{{ option.label }}</span>
                            <Icon
                                v-if="refStatus === option.value"
                                icon="heroicons-outline:check"
                                width="16"
                                height="16"
                                class="text-gray-400"
                                aria-hidden="true"
                            />
                        </button>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
    
    <!-- Confirmed status display with enhanced styling -->
    <div v-else class="relative group" :aria-label="accessibilityText">
        <!-- Status display with admin edit button -->
        <div class="relative inline-flex items-center gap-1">
            <div
                class="inline-flex items-center gap-2 rounded-xl text-sm font-bold shadow-md transition-all duration-300 hover:shadow-lg hover:scale-105 px-2 py-1.5"
                :class="[
                    attendanceStatus.bgColor,
                    attendanceStatus.color === 'text-green-500' ? 'border border-green-200' :
                    attendanceStatus.color === 'text-red-500' ? 'border border-red-200' :
                    attendanceStatus.color === 'text-yellow-500' ? 'border border-yellow-200' :
                    attendanceStatus.color === 'text-blue-500' ? 'border border-blue-200' :
                    'border border-gray-200'
                ]"
                role="status"
            >
                <Icon
                    :icon="attendanceStatus.icon"
                    width="24"
                    height="24"
                    :class="attendanceStatus.color"
                    aria-hidden="true"
                />
                <span class="sr-only">{{ attendanceStatus.label }}</span>
            </div>
            
            <!-- Admin edit button - แสดงเสมอสำหรับ course admin -->
            <button
                v-if="isCourseAdmin"
                ref="menuButtonRef"
                @click.stop="toggleStatusMenu"
                :disabled="isUpdatingStatus"
                class="p-2 rounded-lg transition-all duration-200 hover:bg-gray-100 active:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50"
                :class="{ 'opacity-50 cursor-not-allowed': isUpdatingStatus }"
                type="button"
                title="แก้ไขสถานะการเข้าร่วม"
                aria-label="แก้ไขสถานะการเข้าร่วม"
            >
                <Icon
                    :icon="isUpdatingStatus ? 'heroicons-outline:arrow-path' : 'heroicons-outline:pencil'"
                    width="20"
                    height="20"
                    :class="[
                        'transition-all duration-200',
                        isUpdatingStatus ? 'text-blue-500 animate-spin' : 'text-gray-600 hover:text-blue-600'
                    ]"
                    aria-hidden="true"
                />
            </button>
            
            <!-- Status menu dropdown -->
            <Teleport to="body">
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div
                        v-if="showStatusMenu && isCourseAdmin && !isPending"
                        v-click-outside="closeStatusMenu"
                        class="fixed w-48 bg-white rounded-xl shadow-2xl border border-gray-200 z-[9999] overflow-hidden"
                        :style="{ top: `${dropdownPosition.top}px`, left: `${dropdownPosition.left}px` }"
                        role="menu"
                        aria-orientation="vertical"
                        aria-labelledby="status-menu-button"
                        @click.stop
                    >
                        <div class="py-1">
                            <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">
                                เลือกสถานะ
                            </div>
                            <button
                                v-for="option in statusOptions"
                                :key="option.value"
                                @click.stop="updateStatusByAdmin(option.value, $event)"
                                :disabled="refStatus === option.value || isUpdatingStatus"
                                class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors duration-150"
                                :class="[
                                    option.bgColor,
                                    option.color,
                                    refStatus === option.value 
                                        ? 'bg-gray-100 cursor-default opacity-60' 
                                        : 'hover:shadow-sm cursor-pointer',
                                    isUpdatingStatus ? 'opacity-50 cursor-not-allowed' : ''
                                ]"
                                type="button"
                                role="menuitem"
                            >
                                <Icon
                                    :icon="option.icon"
                                    width="20"
                                    height="20"
                                    aria-hidden="true"
                                />
                                <span class="flex-1 text-left">{{ option.label }}</span>
                                <Icon
                                    v-if="refStatus === option.value"
                                    icon="heroicons-outline:check"
                                    width="16"
                                    height="16"
                                    class="text-gray-400"
                                    aria-hidden="true"
                                />
                            </button>
                        </div>
                    </div>
                </Transition>
            </Teleport>
        </div>
        
        <!-- Add a subtle glow effect for confirmed status -->
        <div
            class="absolute inset-0 rounded-xl opacity-0 group-hover:opacity-20 transition-opacity duration-300 pointer-events-none"
            :class="[
                attendanceStatus.color === 'text-green-500' ? 'bg-green-400' :
                attendanceStatus.color === 'text-red-500' ? 'bg-red-400' :
                attendanceStatus.color === 'text-yellow-500' ? 'bg-yellow-400' :
                attendanceStatus.color === 'text-blue-500' ? 'bg-blue-400' :
                'bg-gray-400'
            ]"
            aria-hidden="true"
        ></div>
    </div>
</template>
