<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

import CoursesLayout from '@/Layouts/CoursesLayout.vue';
import CourseCard from '@/PlearndComponents/learn/courses/CourseCard.vue';
import InfiniteLoading from "v3-infinite-loading";
import CoursesLoadingSkeleton from '@/PlearndComponents/accessories/CoursesLoadingSkeleton.vue'
import LoadingPage from '@/PlearndComponents/accessories/LoadingPage.vue';
import MemberedCoursesWidget from '@/PlearndComponents/learn/courses/widgets/MemberedCoursesWidget.vue';
import MyOwnCoursesWidget from '@/PlearndComponents/learn/courses/widgets/MyOwnCoursesWidget.vue';
import MobileCoursesWidget from '@/PlearndComponents/learn/courses/widgets/MobileCoursesWidget.vue';
import { Icon } from '@iconify/vue';


const props = defineProps({
    courses: Object,
    memberedCourses: [Array, Object], // รายวิชาที่เป็นสมาชิก
    myCourses: [Array, Object], // รายวิชาของฉัน/ที่เป็นแอดมิน
});

const loading = ref(false);
const currentPage = ref(1);
const lastPage = ref(props.courses.meta.last_page);
const allCourses = ref(props.courses.data);
const isLoadingPage = ref(false);

// รายวิชาที่เป็นสมาชิก - ใช้ข้อมูลจาก props โดยตรง
const memberedCoursesList = computed(() => {
    if (!props.memberedCourses) return [];
    if (Array.isArray(props.memberedCourses)) return props.memberedCourses;
    if (props.memberedCourses.data) return props.memberedCourses.data;
    return [];
});

// รายวิชาของฉัน/ที่เป็นแอดมิน
const myCoursesList = computed(() => {
    if (!props.myCourses) return [];
    if (Array.isArray(props.myCourses)) return props.myCourses;
    if (props.myCourses.data) return props.myCourses.data;
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
        <!-- Left Sidebar: รายวิชาที่เป็นสมาชิก -->
        <template #leftSideWidget>
            <MemberedCoursesWidget 
                :courses="memberedCoursesList" 
                @go-to-course="handleLoadingPage"
            />
        </template>

        <!-- Right Sidebar: รายวิชาของฉัน -->
        <template #rightSideWidget>
            <MyOwnCoursesWidget 
                :courses="myCoursesList" 
                @go-to-course="handleLoadingPage"
            />
        </template>

        <template #coursesMainContent>

            <LoadingPage v-if="isLoadingPage" />

            <!-- Mobile Widget: แสดงเฉพาะบนมือถือ -->
            <MobileCoursesWidget 
                :membered-courses="memberedCoursesList"
                :my-courses="myCoursesList"
                @go-to-course="handleLoadingPage"
            />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                <div v-for="(course, index) in allCourses" :key="course.id || index"> 
                    <CourseCard :course="course" @loading-page="handleLoadingPage(course.id)" />
                </div>
            </div>

            <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 mt-4">
                <div v-for="(index) in 2" :key="index">
                    <CoursesLoadingSkeleton />
                </div>
            </div>
            <InfiniteLoading @distance="1" @infinite="fetchMoreCourses" /> 
        </template>
    </CoursesLayout>
</template>
