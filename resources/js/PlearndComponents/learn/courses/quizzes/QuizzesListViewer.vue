<script setup>
// import { ref } from 'vue';
// import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

import QuizItem from '@/PlearndComponents/learn/courses/quizzes/QuizItem.vue';

const props = defineProps({
    quizzes: Object,
    quizzesApiRoute: String
});

const emit = defineEmits([
    'update-quiz',
    'delete-quiz'
]);

function handleUpdateQuiz(qId, qIndex) {
    emit('update-quiz', qId, qIndex);
}

function handleDeleteQuiz(qId, qIndex) {
    // emit('delete-quiz', qId, qIndex);
    Swal.fire({
        title: 'ยืนยันการลบแบบทดสอบ',
        text: "คุณต้องการลบแบบทดสอบนี้หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            emit('delete-quiz', qId, qIndex);
        }
    });
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
                :quizIndex="index+1"
                @update-quiz="handleUpdateQuiz(quiz.id, index)"
                @delete-quiz="handleDeleteQuiz(quiz.id, index)"
            />
        </div>
        
    </div>
</template>
