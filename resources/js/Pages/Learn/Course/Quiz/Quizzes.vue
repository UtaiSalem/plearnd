<script setup>
import { ref } from 'vue';
import { usePage } from "@inertiajs/vue3";

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

const quizIndex = ref(null);
const showUpdateQuizModal = ref(false);

function handleAddNewQuiz(newQuiz){
    props.quizzes.data.push(newQuiz);
}

function handleUpdateQuiz(qId, qIndex){
    quizIndex.value = qIndex;
    showUpdateQuizModal.value = true;
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

                <div class=" mt-4">
                    <QuizzesListViewer 
                        :quizzes="props.quizzes.data"
                        :quizzesApiRoute="`/courses/${props.course.data.id}`"

                        @update-quiz="(qId, qIndex) => handleUpdateQuiz( qId, qIndex)"
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
    </div>
</template>
