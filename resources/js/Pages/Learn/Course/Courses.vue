<script setup>
import { ref } from 'vue';
import CoursesLayout from '@/Layouts/CoursesLayout.vue';
import CourseCard from '@/PlearndComponents/learn/courses/CourseCard.vue';
import InfiniteLoading from "v3-infinite-loading";
import CoursesLoading from '@/PlearndComponents/accessories/CoursesLoadingSkeleton.vue'
const props = defineProps({
    courses: Object,
});

const headerTitle = ref('รายวิชาทั้งหมด');
const loading = ref(false);
const currentPage = ref(props.courses.meta.current_page||1);
const lastPage = ref(props.courses.meta.last_page||1);

async function fetchMoreCourses(){
    try {
        loading.value = true;
        if (currentPage.value < lastPage.value) {
            currentPage.value++;
            let coursesResp = await axios.get('/api/courses?page='+ currentPage.value);
            if (coursesResp.data.success) {
                coursesResp.data.courses.forEach(course => {
                    props.courses.data.push(course)
                });
            }
        }
        loading.value = false;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
};

</script>

<template>
        <CoursesLayout coursePageTitle="รวมรายวิชา">
            <template #coursesMainContent>
                <div class="section-header my-4 p-4 bg-white rounded-xl shadow-lg">
                    <div class="section-header-info">               
                        <h2 class="sm:section-title font-mali md:text-xl">{{ headerTitle }} {{ ' ' + (props.courses.meta.total ? props.courses.meta.total:0) + ' วิชา' }}</h2>
                    </div>
                </div>
                <div class="flex flex-wrap justify-between lg:justify-start gap-4 ">
                    <div v-for="(course, index) in props.courses.data" :key="index" class="w-full sm:w-[48%]"> 
                        <CourseCard :course="course" />
                    </div>
                </div>
                <!-- <div v-if="loading"> -->
                <div v-if="loading" class="flex flex-wrap justify-between gap-4">
                    <div v-for="(index) in 3" :key="index" class="mt-4 w-full sm:w-[48%]">
                        <CoursesLoading />
                    </div>
                </div>
                <InfiniteLoading @distance="1" @infinite="fetchMoreCourses" />
            </template>
        </CoursesLayout>
</template>
