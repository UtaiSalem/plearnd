<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useCourseStore } from '@/stores/course';
import { storeToRefs } from 'pinia';
import { Icon } from '@iconify/vue';
import axios from 'axios';

import CourseAttendance from '@/PlearndComponents/learn/courses/attendances/CourseAttendance.vue';
import CourseMemberAttendanceCard from '@/PlearndComponents/learn/courses/attendances/CourseMemberAttendanceCard.vue';
import GenericListSkeleton from '@/PlearndComponents/accessories/skeletons/GenericListSkeleton.vue';

const store = useCourseStore();
const { attendances, isLoading, isCourseAdmin, groups: storeGroups } = storeToRefs(store);

// ใช้ course data จาก store แทน props
const courseData = computed(() => store.course);
const courseMemberOfAuth = computed(() => store.course?.auth_member);

// ใช้ groups จาก store ถ้ามี หรือจาก course.groups (fallback)
const groups = computed(() => {
    // ลำดับความสำคัญ: store.groups > course.groups
    if (storeGroups.value?.length) return storeGroups.value;
    if (store.course?.groups?.length) return store.course.groups;
    return [];
});

// Active group tab - กำหนดค่าเริ่มต้นจาก last_accessed_group_tab (เก็บ group_id)
const activeGroupTab = ref(0);

// หา index จาก group_id
function findGroupIndexById(groupId) {
    if (!groupId || !groups.value.length) return 0;
    const index = groups.value.findIndex(g => g.id === groupId);
    return index >= 0 ? index : 0;
}

// เลือกกลุ่ม
async function setActiveGroupTab(index) {
    activeGroupTab.value = index;
    
    // บันทึก group_id ลงใน last_accessed_group_tab
    const selectedGroup = groups.value[index];
    if (courseMemberOfAuth.value && courseData.value?.id && selectedGroup) {
        try {
            await axios.post(`/courses/${courseData.value.id}/members/${courseMemberOfAuth.value.id}/set-active-group-tab`, {
                group_tab: selectedGroup.id  // ส่ง group_id แทน index
            });
        } catch (error) {
            console.error('Failed to save active group tab:', error);
        }
    }
}

// โหลด groups จาก API ถ้าไม่มีข้อมูล และกำหนดค่าเริ่มต้น
onMounted(async () => {
    if (!groups.value.length && store.course?.id) {
        await store.fetchGroups(store.course.id);
    }
    
    // กำหนดค่าเริ่มต้นจาก last_accessed_group_tab (ซึ่งเก็บ group_id)
    if (courseMemberOfAuth.value?.last_accessed_group_tab) {
        const savedGroupId = courseMemberOfAuth.value.last_accessed_group_tab;
        // หา index จาก group_id ที่บันทึกไว้
        activeGroupTab.value = findGroupIndexById(savedGroupId);
    }
});

// Watch groups เพื่อกำหนด activeGroupTab เมื่อ groups โหลดเสร็จ
watch(groups, (newGroups) => {
    if (newGroups.length > 0) {
        // ถ้ามี last_accessed_group_tab ให้หา index จาก group_id
        if (courseMemberOfAuth.value?.last_accessed_group_tab) {
            const savedGroupId = courseMemberOfAuth.value.last_accessed_group_tab;
            activeGroupTab.value = findGroupIndexById(savedGroupId);
        } else if (activeGroupTab.value >= newGroups.length) {
            activeGroupTab.value = 0;
        }
    }
});
</script>

<template>
    <div>
        <GenericListSkeleton v-if="isLoading && !attendances.length" :rows="5" />

        <template v-else>
            <!-- Admin View -->
            <div v-if="isCourseAdmin">
                <!-- Banner สำหรับหน้าการเข้าเรียน -->
                <div class="bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 rounded-2xl shadow-xl p-6 mb-6 relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
                    </div>
                    
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg">
                                <Icon icon="heroicons:clipboard-document-check" class="w-9 h-9 text-white" />
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-white mb-1">การเข้าเรียน</h1>
                                <p class="text-violet-100 text-sm">จัดการการเช็คชื่อและติดตามการเข้าเรียนของนักเรียน</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                                <p class="text-white text-sm font-medium">จำนวนกลุ่ม</p>
                                <p class="text-2xl font-bold text-white">{{ groups.length }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Groups Warning -->
                <div v-if="!groups || groups.length === 0" class="bg-gradient-to-r from-amber-50 via-orange-50 to-yellow-50 border-l-4 border-amber-500 rounded-xl p-8 mb-6 shadow-lg">
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

                <!-- Group Tabs & Attendance Content -->
                <template v-else>
                    <!-- Group Tabs -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-3 mb-6">
                        <div class="grid gap-2" :class="[
                            groups.length <= 4 ? `grid-cols-${groups.length}` : '',
                            groups.length === 1 ? 'grid-cols-1' : '',
                            groups.length === 2 ? 'grid-cols-2' : '',
                            groups.length === 3 ? 'grid-cols-3' : '',
                            groups.length === 4 ? 'grid-cols-4' : '',
                            groups.length >= 5 ? 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5' : ''
                        ]">
                            <button
                                v-for="(group, index) in groups"
                                :key="group.id"
                                @click="setActiveGroupTab(index)"
                                class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-300 border-2"
                                :class="[
                                    activeGroupTab === index
                                        ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white border-violet-600 shadow-lg'
                                        : 'bg-white text-gray-600 border-gray-200 hover:border-violet-300 hover:bg-violet-50 hover:text-violet-600'
                                ]"
                            >
                                <Icon icon="heroicons:user-group" class="w-5 h-5 flex-shrink-0" />
                                <span class="truncate">{{ group.name }}</span>
                                <span 
                                    class="px-2 py-0.5 rounded-full text-xs font-bold flex-shrink-0"
                                    :class="activeGroupTab === index ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500'"
                                >
                                    {{ group.members?.length || 0 }}
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Selected Group Attendance -->
                    <CourseAttendance 
                        :groups="[groups[activeGroupTab]]" 
                        :course="courseData"
                        :isCourseAdmin="isCourseAdmin"
                        :courseMemberOfAuth="courseMemberOfAuth"
                    />
                </template>
            </div>
            
            <!-- Member View -->
            <div v-else-if="courseMemberOfAuth">
                <!-- Show warning if member has no group -->
                <div v-if="!courseMemberOfAuth.group_id" class="bg-gradient-to-r from-amber-50 via-orange-50 to-yellow-50 border-l-4 border-amber-500 rounded-xl p-8 mb-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0 w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center animate-pulse">
                                <Icon icon="heroicons:exclamation-triangle" class="h-8 w-8 text-amber-600" />
                            </div>
                            <div>
                                <p class="text-xl font-bold text-amber-800">คุณยังไม่มีกลุ่มสังกัด</p>
                                <p class="text-amber-600 mt-1">กรุณาเข้าร่วมกลุ่มก่อนเพื่อดูข้อมูลการเข้าเรียน</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Show attendance card if member has a group -->
                <div v-else class="w-full mx-auto">
                    <CourseMemberAttendanceCard :courseMemberOfAuth="courseMemberOfAuth" />
                </div>
            </div>
        </template>
    </div>
</template>
