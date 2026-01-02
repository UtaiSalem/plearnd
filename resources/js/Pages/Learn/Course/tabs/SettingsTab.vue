<script setup>
import { computed } from 'vue';
import { useCourseStore } from '@/stores/course';
import { storeToRefs } from 'pinia';
import { router } from '@inertiajs/vue3';

import CourseSettings from '@/PlearndComponents/learn/courses/CourseSettings.vue';

const props = defineProps({
    course: Object,
});

const store = useCourseStore();
const { isLoading, isCourseAdmin } = storeToRefs(store);

async function onUpdateCourseHandler(courseData) {
    try {
        const config = { headers: { 'Content-Type': 'multipart/form-data' } };
        let courseUpdateForm = new FormData();
        courseUpdateForm.append('name', courseData.name);
        courseUpdateForm.append('code', courseData.code);
        courseUpdateForm.append('description', courseData.description);
        courseUpdateForm.append('category', courseData.category);
        courseUpdateForm.append('level', courseData.level);   
        courseUpdateForm.append('credit_units', courseData.credit_units);
        courseUpdateForm.append('hours_per_week', courseData.hours_per_week);
        courseUpdateForm.append('start_date', courseData.start_date);
        courseUpdateForm.append('end_date', courseData.end_date);
        courseUpdateForm.append('auto_accept_members', courseData.auto_accept_members);
        courseUpdateForm.append('tuition_fees', courseData.tuition_fees);
        courseUpdateForm.append('saleable', courseData.saleable);
        courseUpdateForm.append('price', courseData.price);
        courseUpdateForm.append('status', courseData.status);
        
        if (courseData.cover) {
            courseUpdateForm.append('cover', courseData.cover);
        }
        courseUpdateForm.append('_method', 'put');
        
        let resultResp = await axios.put(`/courses/${props.course.id}`, courseUpdateForm, config);

        if (resultResp.data && resultResp.data.success) {
            // Refresh course data in store
            await store.fetchCourse(props.course.id);
        }
    } catch (error) {
        console.log(error);
    }
}

</script>

<template>
    <div>
        <div v-if="isLoading" class="flex justify-center p-8">
            กำลังโหลดข้อมูลการตั้งค่า...
        </div>

        <div v-else-if="isCourseAdmin">
            <CourseSettings 
                :course="course" 
                :isCourseAdmin="isCourseAdmin"
                @update-course="onUpdateCourseHandler"
            />
        </div>

        <div v-else class="p-4 mb-4 text-base text-gray-600 bg-white border-t-4 border-red-500 rounded-lg shadow-lg">
            <div class="text-center">
                <p>คุณไม่มีสิทธิ์เข้าถึงหน้านี้</p>
            </div>
        </div>
    </div>
</template>
