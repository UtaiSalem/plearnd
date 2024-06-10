<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

import QuizItem from '@/PlearndComponents/learn/courses/quizzes/QuizItem.vue';

const props = defineProps({
    quizzes: Object,
    quizzesApiRoute: String
});

const emit = defineEmits([
    'update-quiz',
]);

const isLoading = ref(false);
function handleUpdateQuiz(qId, qIndex) {
    emit('update-quiz', qId, qIndex);
}

async function handleDeleteQuiz(qId, qIndex) {
    try {
        isLoading.value = true;
        let deleteResp = await axios.delete(`/courses/${usePage().props.course.data.id}/quizzes/${qId}`);
        if (deleteResp.status == 200) {
            usePage().props.quizzes.data.splice(qIndex, 1);
            isLoading.value = false;
            Swal.fire({
                title: 'สำเร็จ',
                text: 'ลบแบบทดสอบเรียบร้อย',
                icon: 'success',
                timerProgressBar: true,
            });
        }
    } catch (error) {
        console.log(error);
        isLoading.value = false;
    }
}

</script>

<template>
    <div class="">
        <div class="flex items-center justify-center bg-white border-t-4 border-blue-600 rounded-lg overflow-hidden shadow-lg">
            <div class="tabs flex flex-col justify-center p-4">
                <div class="tabs-header px-4 w-full flex flex-col items-center justify-center">
                    <div class="text-base font-normal" v-if="!quizzes.length">
                        (ไม่มีแบบทดสอบ)
                    </div>
                    <div class="text-xl font-medium">
                        แบบทดสอบ ( {{ quizzes.length }} ) ชุด
                    </div>
                </div>
            </div>
        </div>
        <div v-for="(quiz, index) in props.quizzes" :key="index">
            <QuizItem 
                :quiz="quiz"
                :quizzesApiRoute="props.quizzesApiRoute+'/quizzes/'+quiz.id"

                @update-quiz="handleUpdateQuiz(quiz.id, index)"
                @delete-quiz="handleDeleteQuiz(quiz.id, index)"
            />
        </div>
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
