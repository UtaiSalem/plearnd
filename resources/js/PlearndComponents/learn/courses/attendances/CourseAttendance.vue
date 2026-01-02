<script setup>
import { ref, computed, onMounted, watch, onUnmounted } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';
import Swal from 'sweetalert2';

import '@vuepic/vue-datepicker/dist/main.css'

import StaggeredFade from '@/PlearndComponents/accessories/StaggeredFade.vue';
import MembersAttendanceDetails from './MembersAttendanceDetails.vue';
import CreateNewCourseAttendanceForm from './CreateNewCourseAttendanceForm.vue';
import ShowCourseAttendanceForm from './ShowCourseAttendanceForm.vue';
import { useAttendanceStore } from '@/stores/attendance';

const props = defineProps({
    groups: Object,
    course: Object,           // รับจาก parent หรือ usePage
    isCourseAdmin: Boolean,   // รับจาก parent หรือ usePage
    courseMemberOfAuth: Object, // รับจาก parent หรือ usePage
});

// Use attendance store
const attendanceStore = useAttendanceStore();

// Fallback to usePage props if not passed directly (backward compatibility)
const courseData = computed(() => props.course || usePage().props.course?.data);
const isAdmin = computed(() => props.isCourseAdmin ?? usePage().props.isCourseAdmin);
const authMember = computed(() => props.courseMemberOfAuth || usePage().props.courseMemberOfAuth);

// ใช้กลุ่มแรกเสมอ (เพราะ parent ส่งกลุ่มเดียวมา)
const currentGroup = computed(() => props.groups?.[0]);
const currentGroupId = computed(() => currentGroup.value?.id);

const openCreateNewAttendanceForm = ref(false);
const attendanceResource = ref(null);

const isShowCurrentAttendance = ref(false);
const currentAttendance = ref(null);
const currentAttendanceIndex = ref(null);
const isRefreshingAllStatuses = ref(false);

// Computed properties using store
const isLoadingAttendances = computed(() => {
    return attendanceStore.isLoading(`group_${currentGroupId.value}`);
});

const groupAttendances = computed(() => {
    return attendanceStore.getGroupAttendances(currentGroupId.value) || [];
});

const showAttendanceResource = computed(()=> {
    return !isLoadingAttendances.value;
});

const activeGroupMembers = computed(()=> {
    return currentGroup.value?.members?.sort((a,b) => a.order_number - b.order_number) || [];
});

const attendanceError = computed(() => {
    return attendanceStore.getError(`group_${currentGroupId.value}`);
});

async function loadGroupAttendances() {
    if (!currentGroup.value || !courseData.value?.id) return;
    
    try {
        await attendanceStore.fetchGroupAttendances(courseData.value.id, currentGroup.value.id, isAdmin.value);
    } catch (error) {
        console.error('Failed to load group attendances:', error);
        Swal.fire({
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถโหลดข้อมูลการเข้าเรียนได้ กรุณาลองใหม่',
            icon: 'error'
        });
    }
}

onMounted(async ()=>{
    await loadGroupAttendances();
});

// Watch for group changes from parent and load data accordingly
watch(currentGroupId, async (newGroupId, oldGroupId) => {
    if (newGroupId && newGroupId !== oldGroupId) {
        await loadGroupAttendances();
    }
});

const handleAddNewAttendance = async (atdn) => {
    attendanceResource.value = null;
    // Store will handle adding the attendance to the correct group
    if (currentGroupId.value) {
        attendanceStore.addAttendance(currentGroupId.value, atdn);
    }
    handleCancleCreateNewAttendance();
}

const handleCancleCreateNewAttendance = () => {
    openCreateNewAttendanceForm.value = false;
}

const handleUpdateAttendance = async (atdn) => {
    // Store will handle updating the attendance
    await attendanceStore.updateAttendance(atdn.id, atdn);
    handleCloseAttendance();
}

const handleDeleteAttendance = async (aIndx) => {
    const attendance = groupAttendances.value[aIndx];
    if (!attendance) return;
    
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่ที่จะลบการบันทึกนี้?',
        text: "คุณจะไม่สามารถเรียกดูการบันทึกนี้ได้อีกต่อไป!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ยืนยัน',
        showLoaderOnConfirm: true,
        preConfirm: async () => {
            try {
                await attendanceStore.deleteAttendance(attendance.id);
                return { success: true };
            } catch (error) {
                return { success: false, error: error.message };
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then(async (result) => {
        if (result.isConfirmed) {
            if (result.value.success) {
                Swal.fire(
                    'ลบข้อมูลสำเร็จ!',
                    'บันทึกการมาเรียนถูกลบออกเรียบร้อยแล้ว',
                    'success'
                )
            } else {
                Swal.fire(
                    'เกิดข้อผิดพลาด!',
                    result.value.error || 'เกิดข้อผิดพลาดในการลบการบันทึกนี้ กรุณาลองใหม่อีกครั้ง',
                    'error'
                )
            }
            handleCloseAttendance();
            handleCancleCreateNewAttendance();
        }
    });
}

const handleShowAttendance = (aIndx) => {
    currentAttendance.value = groupAttendances.value[aIndx];
    // console.log(currentAttendance.value);
    currentAttendanceIndex.value = aIndx;
    isShowCurrentAttendance.value = true;
}

const handleCloseAttendance = () => {
    isShowCurrentAttendance.value = false;
    currentAttendance.value = null;
}

const preDeleteAttendance = () => {
    handleDeleteAttendance(currentAttendanceIndex.value);
    handleCloseAttendance();
    handleCancleCreateNewAttendance();
}


const isAutoRefreshEnabled = ref(false);
const refreshInterval = ref(null);

const toggleAutoRefresh = () => {
    isAutoRefreshEnabled.value = !isAutoRefreshEnabled.value;
    
    if (isAutoRefreshEnabled.value) {
        // Start auto refresh - every 15 seconds
        refreshInterval.value = setInterval(() => {
            refreshAllAttendanceStatuses(true); // true = silent mode (no loading indicator)
        }, 15000);
        
        // Also trigger one immediately
        refreshAllAttendanceStatuses(true);
        
        Swal.fire({
            icon: 'success',
            title: 'เปิดการอัปเดตอัตโนมัติ',
            text: 'ระบบจะตรวจสอบสถานะทุกๆ 15 วินาที',
            timer: 2000,
            showConfirmButton: false
        });
    } else {
        // Stop auto refresh
        if (refreshInterval.value) {
            clearInterval(refreshInterval.value);
            refreshInterval.value = null;
        }
        
        // Swal.fire({
        //     icon: 'info',
        //     title: 'ปิดการอัปเดตอัตโนมัติ',
        //     timer: 1500,
        //     showConfirmButton: false
        // });
    }
};

// Clean up interval on unmount
onUnmounted(() => {
    if (refreshInterval.value) {
        clearInterval(refreshInterval.value);
    }
});

const refreshAllAttendanceStatuses = async (silent = false) => {
    if (!isAdmin.value && !isAutoRefreshEnabled.value) return;
    
    if (!silent) {
        isRefreshingAllStatuses.value = true;
    }
    
    try {
        // Get all attendances that are still active (not finished)
        const activeAttendances = groupAttendances.value.filter(attendance => {
            // Check if attendance is active (finish_at is in future or null?)
            // Based on previous logic: now < finish_at
            return new Date() < new Date(attendance.finish_at);
        });
        
        // If no active attendances and auto-refresh is on, maybe we should stop it?
        // But the user might want to keep it on for when they create new ones.
        // For now, let's just proceed.

        // Optimization: Instead of fetchMemberJoinStatus for every member/attendance pair (N*M requests),
        // we should ideally have a bulk endpoint or just reload the group attendances which contains statuses.
        // However, given the current store structure, let's stick to the store action but maybe optimize.
        // Actually, loadGroupAttendances() refreshes the whole list including statuses.
        // This is much more efficient than N*M individual requests if the backend supports it.
        
        // Let's use loadGroupAttendances but silently
        await attendanceStore.fetchGroupAttendances(courseData.value.id, currentGroupId.value, isAdmin.value);
        
        if (!silent) {
            // Show success message only for manual refresh
            Swal.fire({
                icon: 'success',
                title: 'อัปเดตสถานะสำเร็จ',
                text: 'สถานะการเข้าเรียนทั้งหมดได้รับการอัปเดตเรียบร้อยแล้ว',
                timer: 2000,
                showConfirmButton: false
            });
        }
        
    } catch (error) {
        console.error('Failed to refresh attendance statuses:', error);
        if (!silent) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถอัปเดตสถานะการเข้าเรียนได้ กรุณาลองใหม่',
            });
        }
        // If auto-refresh is on and we get critical error, maybe turn it off?
        // For now, keep trying.
    } finally {
        if (!silent) {
            isRefreshingAllStatuses.value = false;
        }
    }
};


</script>

<template>
    <div class="w-full mx-auto">
        <!-- Warning message when there are no groups -->
        <div v-if="!props.groups || props.groups.length === 0" class="bg-gradient-to-r from-amber-50 via-orange-50 to-yellow-50 border-l-4 border-amber-500 rounded-xl p-8 mb-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center animate-pulse">
                        <Icon icon="heroicons:exclamation-triangle" class="h-8 w-8 text-amber-600" />
                    </div>
                    <div>
                        <p class="text-xl font-bold text-amber-800">ยังไม่มีกลุ่มในรายวิชานี้</p>
                        <p class="text-amber-600 mt-1">กรุณาสร้างกลุ่มก่อนเพื่อจัดการข้อมูลการเข้าเรียน</p>
                    </div>
                </div>
                <a :href="`/courses/${courseData?.id}/groups`"
                   class="px-6 py-3 bg-gradient-to-r from-amber-600 to-orange-600 text-white rounded-xl hover:from-amber-700 hover:to-orange-700 transition-all duration-300 text-sm font-bold flex items-center gap-2 shadow-md hover:shadow-lg transform hover:scale-105">
                    <Icon icon="heroicons:user-group" class="h-5 w-5" />
                    ไปที่เมนูกลุ่ม
                </a>
            </div>
        </div>

        <section v-else class="max-w-full" aria-multiselectable="false">
            <!-- Enhanced Loading State -->
            <div v-if="isLoadingAttendances" class="p-12 text-center bg-gradient-to-br from-violet-50 via-purple-50 to-indigo-50 rounded-2xl shadow-xl border border-violet-100">
                <div class="relative">
                    <div class="w-20 h-20 border-4 border-violet-200 border-t-violet-600 rounded-full animate-spin mx-auto"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <Icon icon="heroicons:academic-cap" class="h-10 w-10 text-violet-600 animate-pulse" />
                    </div>
                    <!-- Add rotating ring effect -->
                    <div class="absolute inset-0 rounded-full border-2 border-violet-300 animate-ping opacity-20"></div>
                </div>
                <p class="mt-8 text-xl font-bold text-gray-700 animate-pulse">กำลังโหลดข้อมูลการเข้าเรียน...</p>
                <p class="mt-2 text-sm text-gray-500">กรุณารอสักครู่</p>
            </div>
            
            <!-- Enhanced Error State -->
            <div v-if="attendanceError" class="mt-6 p-8 bg-gradient-to-br from-red-50 via-pink-50 to-rose-50 border-2 border-red-200 rounded-2xl shadow-xl">
                <div class="flex items-start mb-6">
                    <div class="flex-shrink-0 w-14 h-14 bg-red-100 rounded-full flex items-center justify-center animate-pulse">
                        <Icon icon="heroicons:exclamation-triangle" class="h-8 w-8 text-red-600" />
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-xl font-bold text-red-800 mb-2">เกิดข้อผิดพลาด</h3>
                        <p class="text-red-700 font-medium">{{ attendanceError }}</p>
                    </div>
                </div>
                <button
                    @click="loadGroupAttendances()"
                    class="flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl hover:from-red-700 hover:to-pink-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 font-bold"
                >
                    <Icon icon="heroicons:arrow-path" class="h-5 w-5" />
                    <span>ลองใหม่อีกครั้ง</span>
                </button>
            </div>

            <!-- Enhanced Attendance Resource Display -->
            <div v-if="showAttendanceResource" class="mt-6">
                <!-- Enhanced Refresh button for course admins -->
                <div v-if="isAdmin" class="mb-6 flex justify-end gap-2 items-center bg-white p-2 rounded-xl shadow-sm border border-gray-100 w-fit ml-auto">
                    <!-- Auto Refresh Toggle -->
                    <div class="flex items-center gap-3 px-4 py-2 border-r border-gray-100 mr-2">
                        <span class="text-sm font-medium text-gray-600">อัปเดตอัตโนมัติ</span>
                        <button 
                            @click="toggleAutoRefresh"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-violet-600 focus:ring-offset-2"
                            :class="isAutoRefreshEnabled ? 'bg-violet-600' : 'bg-gray-200'"
                            role="switch"
                            :aria-checked="isAutoRefreshEnabled"
                        >
                            <span
                                aria-hidden="true"
                                class="pointer-events-none inline-block h-5 w-5 transforms rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                :class="isAutoRefreshEnabled ? 'translate-x-5' : 'translate-x-0'"
                            />
                        </button>
                        <span v-if="isAutoRefreshEnabled" class="text-xs text-violet-600 font-bold animate-pulse">15s</span>
                    </div>

                    <button
                        @click="refreshAllAttendanceStatuses(false)"
                        :disabled="isRefreshingAllStatuses"
                        class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed shadow hover:shadow-md transform hover:scale-105 font-bold text-sm"
                    >
                        <Icon
                            :icon="isRefreshingAllStatuses ? 'eos-icons:loading' : 'heroicons:arrow-path'"
                            class="h-4 w-4"
                            :class="{ 'animate-spin': isRefreshingAllStatuses }"
                        />
                        <span>
                            {{ isRefreshingAllStatuses ? 'กำลังอัปเดต...' : 'โหลดใหม่' }}
                        </span>
                    </button>
                </div>
                
                <div id="tab-panel-1a" aria-hidden="false" role="tabpanel" aria-labelledby="tab-label-1a" tabindex="-1">
                    <staggered-fade :duration="50" tag="ul" class="flex flex-col w-full">
                        <div class="bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                            <div class="relative overflow-x-auto overflow-y-auto max-h-[600px]">
                                <table class="w-full">
                                    <!-- Enhanced Table Header -->
                                    <thead class="bg-gradient-to-r from-gray-50 via-gray-100 to-slate-100 border-b-2 border-gray-200 sticky top-0 z-20">
                                        <tr class="text-center">
                                            <th scope="col" class="px-4 py-3 border border-slate-300 font-black text-gray-800 bg-white min-w-[220px] sticky left-0 z-30 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                                <div class="flex items-center justify-center gap-3">
                                                    <div class="w-8 h-8 bg-gradient-to-br from-violet-100 to-purple-100 rounded-xl flex items-center justify-center shadow-sm">
                                                        <Icon icon="heroicons:user-group" class="w-4 h-4 text-violet-600" />
                                                    </div>
                                                    <span class="uppercase tracking-wide">สมาชิก</span>
                                                </div>
                                            </th>

                                            <th scope="col" v-for="(attendance, index) in groupAttendances" :key="attendance.id"
                                                class="px-2 py-3 border border-slate-300 min-w-[80px]">
                                                <button
                                                    @click.prevent="handleShowAttendance(index)"
                                                    class="flex justify-center items-center mx-auto text-sm font-bold text-green-600 hover:text-green-700 hover:bg-green-50 rounded-xl px-3 py-2 transition-all duration-300 transform hover:scale-105 shadow-sm hover:shadow-md"
                                                >
                                                    <span class="mr-2">#{{ index + 1 }}</span>
                                                    <Icon icon="hugeicons:view" width="20" height="20" />
                                                </button>
                                            </th>

                                            <th v-if="!openCreateNewAttendanceForm" scope="col" class="px-3 py-4 border bg-white sticky right-0 z-10">
                                                <button
                                                    @click.prevent="openCreateNewAttendanceForm = true"
                                                    class="flex justify-center items-center mx-auto text-green-600 hover:text-green-700 hover:bg-green-50 rounded-xl p-3 transition-all duration-300 transform hover:scale-105 shadow-sm hover:shadow-md"
                                                    title="เพิ่มการเช็คชื่อใหม่"
                                                >
                                                    <Icon icon="icon-park-outline:add-one" width="24" height="24" />
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    
                                    <!-- Enhanced Table Body -->
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        <MembersAttendanceDetails
                                            v-for="(member) in activeGroupMembers" :key="member.id"
                                            :member="member"
                                            :groupAttendances="groupAttendances"
                                        />
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </staggered-fade>
                </div>
            </div>

            <!-- Enhanced Form Sections -->
            <section v-if="openCreateNewAttendanceForm" class="mt-6">
                <div class="bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <CreateNewCourseAttendanceForm
                        :groupId="currentGroupId"
                        @close-form="openCreateNewAttendanceForm = false"
                        @cancle-create-new-attendance="handleCancleCreateNewAttendance"
                        @add-new-attendance="(atdn)=> handleAddNewAttendance(atdn)"
                    />
                </div>
            </section>

            <section v-if="isShowCurrentAttendance" class="mt-6">
                <div class="bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <ShowCourseAttendanceForm
                        :groupId="currentGroupId"
                        :attendance="currentAttendance"
                        @close-form="isShowCurrentAttendance = false"
                        @delete-attendance="preDeleteAttendance"
                        @updat-attendance="(respAtd)=>handleUpdateAttendance(respAtd)"
                    />
                </div>
            </section>
        </section>
    </div>
</template>
