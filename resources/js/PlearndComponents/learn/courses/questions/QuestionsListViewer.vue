<script setup>
import { ref } from 'vue';
import Swal from 'sweetalert2';


import QuestionItem from '@/PlearndComponents/learn/courses/questions/QuestionItem.vue';

const props = defineProps({
    questionableType: String,
    questionableId: Number,
    questionNameTh: String,
    questionApiRoute: String,
    questions: Object,
    startQuizAt: Date,
});

const model = defineModel();

const emit = defineEmits([
    'increase-quiz-points',
    'decrease-quiz-points',
]);

const isLoading = ref(false);

const handleUpdateQuestion = (updatedQuestion, qIdx) => {
    emit('increase-quiz-points', updatedQuestion.points);
}

async function handleDeleteQuestion(qID,qIdx){
    try {
        isLoading.value = true;
        let response = await axios.delete(`${props.questionApiRoute}/questions/${qID}`);
        if (response.status===204) {
            emit('decrease-quiz-points', props.questions[qIdx].points);
            props.questions.splice(qIdx, 1);
            Swal.fire({
                title: 'ลบคำถามสำเร็จ',
                icon: 'success',
                timer: 1500,
                showConfirmButton: true,
                timerProgressBar: true,
            });
        }
        
    } catch (error) {
        console.log(error);
    } finally {
        isLoading.value = false;
    }
}

</script>

<template>
    <div class="w-full ">
        <div class="tabs flex flex-col justify-center w-full">
            <div class="tabs-header px-2 w-full flex flex-col items-center justify-center">
                <div class="text-base font-normal" v-if="!props.questions.length">
                    (ไม่มีคำถาม)
                </div>
                <div v-else-if="model && !$page.props.isCourseAdmin" class="w-full ">
                    <div v-for="(question,idx) in props.questions" :key="question.id" class="w-full border rounded-md p-1 my-2 ">
                        <QuestionItem
                            :questionApiRoute
                            :question="question"
                            :indexNumber="idx"
                            :isCourseOwner="$page.props.isCourseAdmin"
                            :startQuizAt="startQuizAt"

                            @update-question="(updatedQuestion) => handleUpdateQuestion(updatedQuestion, idx)"
                            @delete-question="handleDeleteQuestion(question.id, idx)"
                        />
                    </div>
                </div>
                <div v-if="$page.props.isCourseAdmin" class="w-full ">
                    <div v-for="(question,idx) in props.questions" :key="question.id" class="w-full border rounded-md  my-2 ">
                        <QuestionItem
                            questionApiRoute
                            :question="question"
                            :indexNumber="idx"
                            :isCourseOwner="$page.props.isCourseAdmin"
                            :startQuizAt="startQuizAt"

                            @update-question="(updatedQuestion) => handleUpdateQuestion(updatedQuestion, idx)"
                            @delete-question="handleDeleteQuestion(question.id, idx)"
                        />
                    </div>
                </div>

                <hr>
            </div>
        </div>
        <div v-if="isLoading" class="fixed inset-0 bg-white bg-opacity-80 z-20 h-screen w-screen flex items-center justify-center">
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
