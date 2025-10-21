<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { saveAs } from 'file-saver';
import * as XLSX from 'xlsx';

import CourseLayout from '@/Layouts/CourseLayout.vue';
import StaggeredFade from '@/PlearndComponents/accessories/StaggeredFade.vue';
import OptimizedMembersProgress from '@/PlearndComponents/learn/courses/progress/OptimizedMembersProgress.vue';
import LoadingPage from '@/PlearndComponents/accessories/LoadingPage.vue';

const props = defineProps({
    course: Object,
    groups: Object,
    assignments: Object,
    quizzes: Object,
    initialMembers: Array,
    initialPagination: Object,
    isCourseAdmin: Boolean,
    courseMemberOfAuth: Object,
});

// State management
const isLoading = ref(false);
const isLoadingMore = ref(false);
const activeGroupTab = ref(props.courseMemberOfAuth ? props.courseMemberOfAuth.last_accessed_group_tab || 0 : 0);
const members = ref(props.initialMembers);
const pagination = ref(props.initialPagination);
const unGroupedMembers = ref([]);

// Intersection Observer for infinite scroll
let observer = null;
const loadMoreTrigger = ref(null);

// Computed properties
const activeGroupMembers = computed(() => {
    if (activeGroupTab.value < props.groups.length) {
        const groupId = props.groups[activeGroupTab.value].id;
        return members.value.filter(member => member.group_id === groupId);
    } else {
        return unGroupedMembers.value;
    }
});

const groupName = computed(() => {
    if (activeGroupTab.value < props.groups.length) {
        return props.groups[activeGroupTab.value].name;
    } else {
        return 'ไม่มีกลุ่ม';
    }
});

// Methods
const setActiveGroupTab = async (tab) => {
    activeGroupTab.value = tab;

    // Reset members list
    members.value = [];
    pagination.value = { current_page: 0, last_page: 1, total: 0 };
    unGroupedMembers.value = [];

    // Load members for the selected group
    await loadMembers(1, true);

    // Update user preference
    if (props.courseMemberOfAuth && tab < props.groups.length) {
        try {
            await axios.post(`/courses/${props.course.id}/members/${props.courseMemberOfAuth.id}/set-active-group-tab`, { 
                group_tab: activeGroupTab.value 
            });
        } catch (error) {
            console.error('Error setting active group tab:', error);
        }
    }
};

const loadMembers = async (page = 1, reset = false) => {
    if (isLoading.value || (isLoadingMore.value && !reset)) return;

    if (reset) {
        isLoading.value = true;
    } else {
        isLoadingMore.value = true;
    }

    try {
        const groupId = activeGroupTab.value < props.groups.length ? props.groups[activeGroupTab.value].id : null;
        const response = await axios.get(`/api/courses/${props.course.id}/members/paginated`, {
            params: {
                page: page,
                per_page: 20,
                group_id: groupId
            }
        });

        if (response.data.success) {
            if (reset) {
                members.value = response.data.members;
            } else {
                members.value = [...members.value, ...response.data.members];
            }

            pagination.value = response.data.pagination;

            // Separate ungrouped members
            if (groupId === null) {
                unGroupedMembers.value = members.value;
            }
        }
    } catch (error) {
        console.error('Error loading members:', error);
    } finally {
        isLoading.value = false;
        isLoadingMore.value = false;
    }
};

const loadMoreMembers = async () => {
    if (pagination.value.current_page < pagination.value.last_page) {
        await loadMembers(pagination.value.current_page + 1);
    }
};

const exportTableToExcel = async () => {
    isLoading.value = true;
    
    try {
        const groupId = activeGroupTab.value < props.groups.length ? props.groups[activeGroupTab.value].id : null;
        const response = await axios.get(`/api/courses/${props.course.id}/export/excel`, {
            params: {
                group_id: groupId
            }
        });

        if (response.data.success) {
            const data = response.data.data;
            const worksheet = XLSX.utils.aoa_to_sheet(data);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, groupName.value.replace(/[:\\\/?*\[\]]/g, ''));
            const excelBuffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
            const blob = new Blob([excelBuffer], { type: 'application/octet-stream' });
            saveAs(blob, Date.now() + '.xlsx');
        }
    } catch (error) {
        console.error('Error exporting to Excel:', error);
    } finally {
        isLoading.value = false;
    }
};

// Setup intersection observer for infinite scroll
const setupIntersectionObserver = () => {
    if (!loadMoreTrigger.value || !('IntersectionObserver' in window)) {
        return;
    }

    observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !isLoadingMore.value) {
                loadMoreMembers();
            }
        });
    }, { 
        threshold: 0.1,
        rootMargin: '100px' // Start loading 100px before the trigger is in view
    });

    observer.observe(loadMoreTrigger.value);
};

// Cleanup
onMounted(() => {
    // Initialize ungrouped members
    unGroupedMembers.value = members.value.filter(member => !member.group_id);
    
    // Setup intersection observer
    setupIntersectionObserver();
});

onUnmounted(() => {
    if (observer) {
        observer.disconnect();
    }
});
</script>

<template>
    <div>
        <CourseLayout
            :course="props.course"
            :isCourseAdmin="props.isCourseAdmin"
            :courseMemberOfAuth="props.courseMemberOfAuth"
            :activeTab="10"
        >
            <template #courseContent>
                <div class="md:-ml-4 md:mr-4">
                    <!-- Loading overlay -->
                    <LoadingPage v-if="isLoading" />
                    
                    <!-- Group tabs -->
                    <section class="" aria-multiselectable="false">
                        <ul v-if="props.isCourseAdmin"
                            class="flex flex-wrap items-center border-b border-slate-200 plearnd-card" role="tablist">
                            <li v-for="(group, index) in props.groups" :key="index" 
                                class="w-1/2 md:w-1/3 lg:w-1/4" role="presentation">
                                <button @click.prevent="setActiveGroupTab(index)"
                                    class="inline-flex items-center justify-center w-full h-12 gap-2 px-6 mb-2 text-sm tracking-wide transition duration-300 border-b-2 rounded-t focus-visible:outline-none whitespace-nowrap hover:border-violet-600 focus:border-violet-700 text-violet-500 hover:text-violet-600 focus:text-violet-700 hover:bg-violet-50 focus:bg-violet-50 disabled:cursor-not-allowed disabled:border-slate-500 stroke-violet-500 hover:stroke-violet-600 focus:stroke-violet-700"
                                    :class="activeGroupTab === index ? 'border-violet-500 bg-violet-200 font-bold' : 'font-medium'"
                                    id="tab-label-1a" role="tab" aria-setsize="3" aria-posinset="1" tabindex="0"
                                    aria-controls="tab-panel-1a" aria-selected="true">
                                    <span>{{ group.name + ' (' + (members.filter(m => m.group_id === group.id).length) + ')' }}</span>
                                </button>
                            </li>
                            <li class="w-1/2 md:w-1/3 lg:w-1/4" role="presentation">
                                <button @click.prevent="setActiveGroupTab(props.groups.length)"
                                    class="inline-flex items-center justify-center w-full h-12 gap-2 px-6 mb-2 text-sm tracking-wide transition duration-300 border-b-2 rounded-t focus-visible:outline-none whitespace-nowrap hover:border-violet-600 focus:border-violet-700 text-violet-500 hover:text-violet-600 focus:text-violet-700 hover:bg-violet-50 focus:bg-violet-50 disabled:cursor-not-allowed disabled:border-slate-500 stroke-violet-500 hover:stroke-violet-600 focus:stroke-violet-700"
                                    :class="activeGroupTab === props.groups.length ? 'border-violet-500 bg-violet-200 font-bold' : 'font-medium'"
                                    id="tab-label-1a" role="tab" aria-setsize="3" aria-posinset="1" tabindex="0"
                                    aria-controls="tab-panel-1a" aria-selected="true">
                                    <span>{{ 'ไม่มีกลุ่ม' + ' (' + unGroupedMembers.length + ')' }}</span>
                                </button>
                            </li>
                        </ul>
                    </section>
                    
                    <!-- Members progress table -->
                    <section class="">
                        <div class="bg-white mt-4 p-2 rounded-lg" id="tab-panel-1a" aria-hidden="false"
                            role="tabpanel" aria-labelledby="tab-label-1a" tabindex="-1">
                            <staggered-fade :duration="50" tag="div" class="flex flex-col w-full">
                                <OptimizedMembersProgress 
                                    :groupName="groupName"
                                    :members="activeGroupMembers"
                                    :isCourseAdmin="props.isCourseAdmin"
                                    :assignments="props.assignments"
                                    :quizzes="props.quizzes"
                                    :course="props.course"
                                    @member-updated="() => {}"
                                />
                            </staggered-fade>
                            
                            <!-- Infinite scroll trigger -->
                            <div v-if="pagination.current_page < pagination.last_page" 
                                ref="loadMoreTrigger" 
                                class="flex justify-center py-4">
                                <div v-if="isLoadingMore" class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Export button -->
                    <div class="flex w-full justify-end my-4">
                        <button @click.prevent="exportTableToExcel" 
                            :disabled="isLoading"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                            <svg v-if="isLoading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V4a10 10 0 00-10 10h2zm2 8a8 8 0 018-8h2a10 10 0 00-10-10v2zm8 2a8 8 0 01-8-8h-2a10 10 0 0010 10v-2zm-8-2a8 8 0 01-8 8V20a10 10 0 0010-10h-2z"></path>
                            </svg>
                            {{ isLoading ? 'กำลังส่งออก...' : 'ดาวน์โหลด Excel' }}
                        </button>
                    </div>
                </div>
            </template>
        </CourseLayout>
    </div>
</template>