<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3'
import CoursesLayout from '@/Layouts/CoursesLayout.vue';
import CourseCard from '@/PlearndComponents/learn/courses/CourseCard.vue';

const user = ref(usePage().props.auth.user);
const courses = ref([]);
const headerTitle = ref('รายวิชาทั้งหมด');
const isLoading = ref(false);

async function fetchMyCourses(){
    try {
        isLoading.value = true;
        let coursesResp = await axios.get(`/api/courses/users/${user.value.id}/my-courses`);
        if (coursesResp.data && coursesResp.data.success) {
            courses.value = coursesResp.data.courses;
        }
        isLoading.value = false;
    } catch (error) {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchMyCourses();
});

</script>

<template>
        <CoursesLayout>
            <template #coursesMainContent>
                <div class="flex flex-wrap justify-between gap-4 mt-4" v-if="isLoading">
                    <div v-for="index in 2" :key="index" class="w-full sm:w-[48%]">
                        <!-- <div class="p-4 md:w-1/3"> -->
                            <div class="w-full shadow-xl rounded-lg overflow-hidden">
                                <div class=" bg-gray-400/40 h-36 w-full object-cover object-center"></div>
                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-400"></div>
                                        <div class="flex-1 py-2 space-y-4 mx-2">
                                            <div class="leading-relaxed w-full h-3 rounded animate-pulse bg-gray-400"></div>
                                            <div class="leading-relaxed w-5/6 h-3 rounded animate-pulse bg-gray-400"></div>
                                        </div>
                                    </div>
                                    <p class="leading-relaxed rounded mb-3 w-full h-3 animate-pulse bg-gray-400"></p>
                                    <p class="leading-relaxed rounded mb-3 w-2/3 h-3 animate-pulse bg-gray-400"></p>
                                    <p class="leading-relaxed rounded mb-3 w-1/2 h-3 animate-pulse bg-gray-400"></p>
                                </div>
                            </div>
                        <!-- </div> -->
                    </div>
                </div>

                <div class="section-header my-4 p-4 bg-white rounded-xl shadow-lg" v-if="!isLoading">
                    <div class="section-header-info">               
                        <h2 class="section-title font-mali"> {{ headerTitle }} {{ ' ' + courses.length + ' วิชา' || 'ยังไม่มีรายวิชา' }}</h2>
                    </div>
                </div>

                <div class="flex flex-wrap justify-between gap-4 " v-if="!isLoading">
                    <div v-for="(course, index) in courses" :key="index" class="w-full sm:w-[48%]"> 
                        <CourseCard :course="course" />
                    </div>
                </div>
            </template>
        </CoursesLayout>
</template>
