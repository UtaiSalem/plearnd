<script setup>
import { ref, computed, shallowRef } from 'vue';
import { useCourseStore } from '@/stores/course';
import { storeToRefs } from 'pinia';
import Swal from 'sweetalert2';

import QuizzesListViewer from '@/PlearndComponents/learn/courses/quizzes/QuizzesListViewer.vue';
import CreateNewCourseQuize from '@/PlearndComponents/learn/courses/quizzes/CreateNewCourseQuize.vue';
import UpdateCourseQuize from '@/PlearndComponents/learn/courses/quizzes/UpdateCourseQuize.vue';
import GenericListSkeleton from '@/PlearndComponents/accessories/skeletons/GenericListSkeleton.vue';

const props = defineProps({
    course: Object,
});

const store = useCourseStore();
const { quizzes, isLoading, isCourseAdmin } = storeToRefs(store);

const quizIndex = ref(null);
const showUpdateQuizModal = ref(false);

function handleAddNewQuiz(newQuiz){
    quizzes.value = [...quizzes.value, newQuiz];
}

function handleUpdateQuiz(qId, qIndex){
    quizIndex.value = qIndex;
    showUpdateQuizModal.value = true;
}

async function handleDeleteQuiz(qId, qIndex){
    try {
        let deleteResp = await axios.delete(`/courses/${props.course.id}/quizzes/${qId}`);
        if (deleteResp.data.success) {
            quizzes.value = quizzes.value.filter((_, index) => index !== qIndex);
            
            Swal.fire({
                title: 'ลบแบบทดสอบสำเร็จ',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                timerProgressBar: true,
            });
        }
    } catch (error) {
        console.log(error);
        Swal.fire({
            title: 'ลบแบบทดสอบไม่สำเร็จ',
            text: 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง',
            icon: 'error',
        });
    }
}

function handleUpdateQuizData(updatedQuiz) {
    quizzes.value = quizzes.value.map((quiz, index) =>
        index === quizIndex.value ? updatedQuiz : quiz
    );
}

</script>

<template>
    <div class="relative">
        <GenericListSkeleton v-if="isLoading && !quizzes.length" :rows="3" :showAvatar="false" />

        <template v-else>
            <QuizzesListViewer
                :quizzes="quizzes"
                :quizzesApiRoute="`/courses/${course.id}`"
                @update-quiz="(qId, qIndex) => handleUpdateQuiz(qId, qIndex)"
                @delete-quiz="(qId, qIndex) => handleDeleteQuiz(qId, qIndex)"
            />

            <Teleport to="body">
                <Transition name="modal-fade">
                    <UpdateCourseQuize v-if="showUpdateQuizModal && quizIndex !== null"
                        :quiz="quizzes[quizIndex]"
                        :course_id="course.id"
                        @update-quiz="handleUpdateQuizData"
                        @close-update-quiz-modal="showUpdateQuizModal = false; quizIndex = null;"
                    />
                </Transition>
            </Teleport>

            <CreateNewCourseQuize v-if="isCourseAdmin"
                :course_id="course.id"
                @add-new-quiz="handleAddNewQuiz"
            />
        </template>
    </div>
</template>
