<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';
import { useAttendanceStore } from '@/stores/attendance';

const props = defineProps({
    status: Number,
    attendance: Object,
    memberId: Number,
});

const attendanceStore = useAttendanceStore();
const refStatus = ref(props.status);
const isPending = ref(new Date() < new Date(props.attendance.finish_at) && (props.status === null || props.status === undefined));
const isCheckingStatus = ref(false);
const refreshInterval = ref(null);

// Watch for props.status changes to update refStatus
watch(() => props.status, (newStatus) => {
    if (newStatus !== null && newStatus !== undefined) {
        refStatus.value = newStatus;
        isPending.value = false;
    }
}, { immediate: false });

const attendanceStatus = computed(()=>{
    switch (refStatus.value) {
        case 0:
            return {icon:'heroicons-outline:x-circle', color:'text-red-500'};
        case 1:
        return {icon:'heroicons-outline:check-circle', color:'text-green-500'};
        case 2:
            return {icon:'heroicons-outline:check-circle', color:'text-yellow-500'};
        case 3:
            return {icon:'heroicons-outline:x-circle', color:'text-red-500'};
        default:
            return {icon:null, color:null};
    }
});

// Check if the current user is a course admin
const isCourseAdmin = computed(() => {
    return usePage().props.isCourseAdmin || false;
});

// Function to check attendance status from backend
const checkAttendanceStatus = async () => {
    // Only check if attendance is still active and status is not set
    if (!isPending.value || isCheckingStatus.value) return;
    
    isCheckingStatus.value = true;
    
    try {
        // Use the attendance store to fetch member join status
        const status = await attendanceStore.fetchMemberJoinStatus(props.attendance.id, props.memberId);
        
        // Update status if it has changed
        if (status !== null && status !== undefined && status !== refStatus.value) {
            refStatus.value = status;
            // Update isPending based on new status
            isPending.value = false;
            
            // Also update the status in the store for consistency
            attendanceStore.updateMemberStatusInAttendance(props.attendance.id, props.memberId, status);
        }
    } catch (error) {
        // Handle errors silently in production
        if (error.response?.status === 404) {
            // Attendance or member data not found - stop polling to prevent spam
            console.warn('Attendance data not found - stopping auto-refresh');
            isPending.value = false;
            cleanup();
        } else if (error.response?.status === 403) {
            // Permission denied - stop polling
            console.warn('Permission denied - stopping auto-refresh');
            isPending.value = false;
            cleanup();
        }
        // For other errors, continue polling (might be temporary network issue)
    } finally {
        isCheckingStatus.value = false;
    }
};

// Manual refresh function for course admins
const handlePendingClick = async () => {
    if (isCourseAdmin.value) {
        await checkAttendanceStatus();
    }
};

// Set up auto-refresh interval
const setupAutoRefresh = () => {
    // Only set up auto-refresh for pending attendances
    if (isPending.value) {
        // สุ่มเวลาระหว่าง 1,000 - 6,000 มิลลิวินาที (1-6 วินาที)
        // เพื่อป้องกันการโหลดข้อมูลพร้อมกันทั้งหมด
        const randomInterval = Math.floor(Math.random() * (6000 - 1000 + 1)) + 1000;
        
        refreshInterval.value = setInterval(() => {
            checkAttendanceStatus();
        }, randomInterval);
        
        // console.log(`Auto-refresh ตั้งค่าทุก ${randomInterval / 1000} วินาที`);
    }
};

// Clean up interval when component is unmounted
const cleanup = () => {
    if (refreshInterval.value) {
        clearInterval(refreshInterval.value);
        refreshInterval.value = null;
    }
};

// Set up auto-refresh when component mounts
onMounted(() => {
    setupAutoRefresh();
});

// Clean up when component unmounts
onUnmounted(() => {
    cleanup();
});

// Watch for changes in isPending to start/stop auto-refresh
const stopWatcher = watch(isPending, (newValue) => {
    cleanup();
    if (newValue) {
        setupAutoRefresh();
    }
});

// Clean up watcher when component unmounts
onUnmounted(() => {
    if (stopWatcher) {
        stopWatcher();
    }
});

</script>

<template>
    <!-- Handle case when attendance data is not available -->
    <div v-if="!props.attendance || !props.memberId" class="flex items-center justify-center p-3">
        <div class="text-center bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 shadow-sm border border-gray-200">
            <Icon icon="heroicons-outline:question-mark-circle" width="28" height="28" class="text-gray-400 mx-auto mb-2" />
            <p class="text-xs text-gray-500 font-medium">ไม่มีข้อมูล</p>
        </div>
    </div>
    
    <!-- Normal attendance status display -->
    <div v-else-if="isPending" class="relative inline-flex items-center justify-center">
        <!-- Pending status with clock icon (enhanced with animation) -->
        <button
            v-if="isCourseAdmin"
            @click="handlePendingClick"
            :disabled="isCheckingStatus"
            class="relative group p-2 rounded-xl transition-all duration-300 hover:shadow-lg hover:scale-105 bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200"
            :title="isCheckingStatus ? 'กำลังตรวจสอบสถานะ...' : 'คลิกเพื่อตรวจสอบสถานะล่าสุด'"
        >
            <div class="relative">
                <Icon
                    icon="heroicons-outline:clock"
                    width="36"
                    height="36"
                    :class="[
                        'transition-all duration-300',
                        isCheckingStatus ? 'text-amber-600 animate-pulse' : 'text-amber-500 group-hover:text-amber-600'
                    ]"
                />
                <div v-if="isCheckingStatus" class="absolute inset-0 rounded-full border-2 border-amber-300 animate-ping"></div>
            </div>
            <span class="absolute -top-1 -right-1 w-3 h-3 bg-amber-400 rounded-full animate-pulse"></span>
        </button>
        
        <!-- Non-admin users just see the clock icon -->
        <div
            v-else
            class="relative p-2 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200"
            :title="isCheckingStatus ? 'กำลังตรวจสอบสถานะ...' : 'รอการประมวลผล'"
        >
            <div class="relative">
                <Icon
                    icon="heroicons-outline:clock"
                    width="36"
                    height="36"
                    :class="[
                        'transition-colors duration-300',
                        isCheckingStatus ? 'text-blue-600 animate-pulse' : 'text-blue-500'
                    ]"
                />
                <div v-if="isCheckingStatus" class="absolute inset-0 rounded-full border-2 border-blue-300 animate-ping"></div>
            </div>
            <span class="absolute -top-1 -right-1 w-3 h-3 bg-blue-400 rounded-full animate-pulse"></span>
        </div>
    </div>
    
    <!-- Confirmed status display with enhanced styling -->
    <div v-else class="relative group">
        <div class="inline-flex items-center gap-2 rounded-xl text-sm font-bold shadow-md transition-all duration-300 hover:shadow-lg hover:scale-105"
             :class="[
                 attendanceStatus.color === 'text-green-500' ? 'bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200' :
                 attendanceStatus.color === 'text-red-500' ? 'bg-gradient-to-br from-red-50 to-pink-50 border border-red-200' :
                 attendanceStatus.color === 'text-yellow-500' ? 'bg-gradient-to-br from-yellow-50 to-amber-50 border border-yellow-200' :
                 'bg-gradient-to-br from-gray-50 to-slate-50 border border-gray-200'
             ]">
            <Icon
                :icon="attendanceStatus.icon"
                width="32"
                height="32"
                :class="attendanceStatus.color"
            />
        </div>
        <!-- Add a subtle glow effect for confirmed status -->
        <div class="absolute inset-0 rounded-xl opacity-0 group-hover:opacity-20 transition-opacity duration-300"
             :class="[
                 attendanceStatus.color === 'text-green-500' ? 'bg-green-400' :
                 attendanceStatus.color === 'text-red-500' ? 'bg-red-400' :
                 attendanceStatus.color === 'text-yellow-500' ? 'bg-yellow-400' :
                 'bg-gray-400'
             ]">
        </div>
    </div>
</template>
