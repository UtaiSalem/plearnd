<script setup>
import { ref } from 'vue';
// import { usePage } from "@inertiajs/vue3";
import Swal from 'sweetalert2';

import CourseLayout from '@/Layouts/CourseLayout.vue';
import QuizzesListViewer from '@/PlearndComponents/learn/courses/quizzes/QuizzesListViewer.vue';  
import CreateNewCourseQuize from '@/PlearndComponents/learn/courses/quizzes/CreateNewCourseQuize.vue'; 
import UpdateCourseQuize from '@/PlearndComponents/learn/courses/quizzes/UpdateCourseQuize.vue'; 

const props = defineProps({
    course: Object,
    quizzes: Object,
    isCourseAdmin: Boolean,
    courseMemberOfAuth: Object,
});

const isLoading = ref(false);
const quizIndex = ref(null);
const showUpdateQuizModal = ref(false);

function handleAddNewQuiz(newQuiz){
    props.quizzes.data.push(newQuiz);
}

function handleUpdateQuiz(qId, qIndex){
    quizIndex.value = qIndex;
    showUpdateQuizModal.value = true;
}

async function handleDeleteQuiz(qId, qIndex){
    try {
        isLoading.value = true;
        let deleteResp = await axios.delete(`/courses/${props.course.data.id}/quizzes/${qId}`);
        if (deleteResp.data.success) {
            spliceQuiz(qIndex);
            Swal.fire({
                title: 'ลบแบบทดสอบสำเร็จ',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                timerProgressBar: true,
            });
        }
        props.quizzes.data.splice(qIndex, 1);
    } catch (error) {
        console.log(error);
        Swal.fire({
            title: 'ลบแบบทดสอบไม่สำเร็จ',
            text: 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง',
            icon: 'error',
        });
    } finally {
        isLoading.value = false;
    }
}

function spliceQuiz(qIndex){
    props.quizzes.data.splice(qIndex, 1);
}

</script>

<template>
    <div class="">
        <CourseLayout 
            :course="props.course" 
            :isCourseAdmin="props.isCourseAdmin"
            :activeTab="3"
        >
            <template #courseContent>

                <div class="mt-4">
                    <QuizzesListViewer 
                        :quizzes="props.quizzes.data"
                        :quizzesApiRoute="`/courses/${props.course.data.id}`"

                        @update-quiz="(qId, qIndex) => handleUpdateQuiz( qId, qIndex)"
                        @delete-quiz="(qId, qIndex) => handleDeleteQuiz(qId, qIndex)"
                    />

                    <UpdateCourseQuize v-if="showUpdateQuizModal"
                        :quiz="props.quizzes.data[quizIndex]"

                        @update-quiz="(updatedQuiz) => props.quizzes.data[quizIndex] = updatedQuiz"
                        @close-update-quiz-modal="showUpdateQuizModal = false; quizIndex = null;"
                    />

                    <CreateNewCourseQuize 
                        :course="props.course.data"

                        @add-new-quiz="(newQuiz) => handleAddNewQuiz(newQuiz)"
                    />               
                </div>
            </template>
        </CourseLayout>
        <div v-if="isLoading" class="fixed inset-0 bg-white bg-opacity-80 z-20 h-full w-full flex items-center justify-center">
            <div class="flex items-center">
                <svg class="animate-spin h-20 w-20 text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>
        </div>
    </div>
</template>
