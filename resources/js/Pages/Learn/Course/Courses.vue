<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

import CoursesLayout from '@/Layouts/CoursesLayout.vue';
import CourseCard from '@/PlearndComponents/learn/courses/CourseCard.vue';
import InfiniteLoading from "v3-infinite-loading";
import CoursesLoadingSkeleton from '@/PlearndComponents/accessories/CoursesLoadingSkeleton.vue'
import LoadingPage from '@/PlearndComponents/accessories/LoadingPage.vue';
import { Icon } from '@iconify/vue';


const props = defineProps({
    courses: Object,
    memberedCourses: [Array, Object], // รายวิชาที่เป็นสมาชิก - ส่งมาจาก server (อาจเป็น array หรือ object ที่มี data)
});

const loading = ref(false);
const currentPage = ref(1);
const lastPage = ref(props.courses.meta.last_page);
const allCourses = ref(props.courses.data);
const isLoadingPage = ref(false);

// รายวิชาที่เป็นสมาชิก - ใช้ข้อมูลจาก props โดยตรง (ไม่ต้อง fetch ใหม่)
// รองรับทั้ง array และ object ที่มี data property (Laravel Resource Collection)
const memberedCoursesList = computed(() => {
    if (!props.memberedCourses) return [];
    if (Array.isArray(props.memberedCourses)) return props.memberedCourses;
    if (props.memberedCourses.data) return props.memberedCourses.data;
    return [];
});

async function fetchMoreCourses(){
    try {
        currentPage.value++;
        if (currentPage.value <= lastPage.value) {
            loading.value = true;
            let coursesResp = await axios.get('/api/courses?page='+ currentPage.value);
            if (coursesResp.data.success) {
                coursesResp.data.courses.forEach(course => {
                    allCourses.value.push(course);
                });
            }
            loading.value = false;
        }
    } catch (error) {
        console.log(error);
        loading.value = false;
    }

};

const handleLoadingPage = (courseId) => {
    isLoadingPage.value = true;
    router.visit(`/courses/${courseId}`);
};

</script>

<template>
    <CoursesLayout coursePageTitle="รายวิชาทั้งหมด">
        <template #coursesMainContent>

            <LoadingPage v-if="isLoadingPage" />

            <!-- วิดเจ็ตการ์ดแสดงรายวิชาที่เป็นสมาชิก -->
            <div class="p-3 sm:p-4 my-4 bg-gradient-to-r from-cyan-50 to-blue-50 shadow-lg rounded-xl border border-cyan-200 overflow-hidden">
                <div class="flex items-center justify-between mb-3 gap-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <Icon icon="mdi:account-group" class="w-5 h-5 sm:w-6 sm:h-6 text-cyan-600 flex-shrink-0" />
                        <h3 class="text-sm sm:text-lg font-semibold text-cyan-800 font-prompt truncate">รายวิชาที่ฉันเป็นสมาชิก</h3>
                    </div>
                    <span v-if="memberedCoursesList.length > 0" class="px-2 sm:px-3 py-1 text-xs sm:text-sm font-medium text-cyan-700 bg-cyan-100 rounded-full flex-shrink-0">
                        {{ memberedCoursesList.length }} วิชา
                    </span>
                </div>
                
                <!-- Empty State -->
                <div v-if="memberedCoursesList.length === 0" class="py-6 text-center">
                    <Icon icon="mdi:book-open-blank-variant" class="w-12 h-12 mx-auto text-gray-300" />
                    <p class="mt-2 text-gray-500">ยังไม่มีรายวิชาที่เป็นสมาชิก</p>
                    <p class="text-sm text-gray-400">คุณสามารถสมัครเข้าร่วมรายวิชาได้จากรายการด้านล่าง</p>
                </div>
                
                <!-- Course Cards Horizontal Scroll -->
                <div v-else class="relative -mx-3 sm:mx-0">
                    <div class="flex gap-3 sm:gap-4 px-3 sm:px-0 pb-2 overflow-x-auto scrollbar-thin scrollbar-thumb-cyan-300 scrollbar-track-cyan-100">
                        <div v-for="(course, index) in memberedCoursesList.slice(0, 6)" :key="course.id || index" 
                            class="flex-shrink-0 w-48 sm:w-56 md:w-64 transition-transform duration-200 cursor-pointer hover:scale-105"
                            @click="handleLoadingPage(course.id)">
                            <div class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow-md hover:shadow-lg">
                                <!-- Course Cover -->
                                <div class="relative h-20 sm:h-24 bg-gradient-to-r from-cyan-400 to-blue-500">
                                    <img v-if="course.cover" 
                                        :src="`/storage/images/courses/covers/${course.cover}`" 
                                        :alt="course.name"
                                        class="object-cover w-full h-full" />
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                                </div>
                                <!-- Course Info -->
                                <div class="p-2 sm:p-3">
                                    <div class="flex items-center gap-2 mb-2">
                                        <img v-if="course.logo" 
                                            :src="`/storage/images/courses/logos/${course.logo}`" 
                                            :alt="course.name"
                                            class="flex-shrink-0 object-cover w-6 h-6 sm:w-8 sm:h-8 rounded-full ring-2 ring-cyan-200" />
                                        <div v-else class="flex items-center justify-center flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 text-white rounded-full bg-gradient-to-r from-cyan-500 to-blue-500">
                                            <Icon icon="mdi:school" class="w-3 h-3 sm:w-4 sm:h-4" />
                                        </div>
                                        <h4 class="text-xs sm:text-sm font-medium text-gray-800 line-clamp-2">{{ course.name }}</h4>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <Icon icon="mdi:account-multiple" class="w-3 h-3 sm:w-4 sm:h-4" />
                                            {{ course.enrolled_students || 0 }}
                                        </span>
                                        <span class="px-1.5 sm:px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-[10px] sm:text-xs">สมาชิก</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- View More Card -->
                        <div v-if="memberedCoursesList.length > 6" 
                            class="flex-shrink-0 w-48 sm:w-56 md:w-64 cursor-pointer"
                            @click="router.visit(`/courses/users/${$page.props.auth.user.id}/member`)">
                            <div class="flex flex-col items-center justify-center h-full min-h-[120px] sm:min-h-[140px] overflow-hidden bg-white border-2 border-dashed border-cyan-300 rounded-lg hover:bg-cyan-50 transition-colors">
                                <Icon icon="mdi:arrow-right-circle" class="w-8 h-8 sm:w-10 sm:h-10 text-cyan-500" />
                                <p class="mt-2 text-sm sm:text-base font-medium text-cyan-600">ดูทั้งหมด</p>
                                <p class="text-xs sm:text-sm text-gray-400">+{{ memberedCoursesList.length - 6 }} วิชา</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-3 sm:p-4 my-4 bg-white shadow-lg section-header rounded-xl">
                <div class="section-header-info">               
                    <h2 class="text-base sm:text-lg section-title font-prompt"> รายวิชาทั้งหมด {{ ' ' + props.courses.meta.total + ' วิชา' || 'ยังไม่มีรายวิชา' }}</h2>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div v-for="(course, index) in allCourses" :key="course.id || index"> 
                    <CourseCard :course="course" @loading-page="handleLoadingPage(course.id)" />
                </div>
            </div>

            <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-4">
                <div v-for="(index) in 2" :key="index">
                    <CoursesLoadingSkeleton />
                </div>
            </div>
            <InfiniteLoading @distance="1" @infinite="fetchMoreCourses" /> 
        </template>
    </CoursesLayout>
</template>
