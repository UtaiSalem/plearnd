<script setup>
import { ref, computed } from 'vue';
import DotsDropdownMenu from '@/PlearndComponents/accessories/DotsDropdownMenu.vue';
import QuestionsListViewer from '@/PlearndComponents/learn/courses/questions/QuestionsListViewer.vue';
import CreateNewQuestion from '@/PlearndComponents/learn/courses/questions/CreateNewQuestion.vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    quiz: Object,
    quizzesApiRoute: String
});
const emit = defineEmits([
    'update-quiz',
    'delete-quiz'
]);

const questionsLength = computed(() => props.quiz.questions.length);
const startQuiz = ref(false);
const endQuiz = ref(false);
const timeCountdown = ref(null);
const limitTimeOut = ref(null);
const isLoading = ref(false);

async function handleStartQuiz() {
    try {
        let qResultResp = await axios.post(`/courses/${props.quiz.course_id}/quizzes/${props.quiz.id}/results`, {});
        if (qResultResp.data && qResultResp.data.success) {
            // startCountdown();
        }
        timeCountdown.value = props.quiz.time_limit * 60;
        startQuiz.value = true;
        limitTimeOut.value = setInterval(() => {
            timeCountdown.value -= 1;
            // console.log(timeCountdown.value);
            if (timeCountdown.value <= 0) {
                handleEndQuiz();
            }
        }, 1000);
    } catch (error) {
        console.log(error);
    }
}

function handleEndQuiz() {
    endQuiz.value = true;
    startQuiz.value = false;
    clearInterval(limitTimeOut.value);
}

async function addNewQuestion(question){
    props.quiz.questions.push(question);
    props.quiz.total_score += parseInt(question.points);
    Swal.fire('บันทึกสำเร็จ','เพิ่มคำถามเสร็จเรียบร้อย.','success' );
}

async function handleDeleteQuestion(qID,qIdx){
    try {
        let response = await axios.delete(`${props.quizzesApiRoute}/questions/${qID}`);
        if (response.status===204) {
            props.quiz.total_score -= props.quiz.questions[qIdx].points;
            props.quiz.questions.splice(qIdx, 1);
            Swal.fire('ลบสำเร็จ','ลบคำถามเสร็จสมบูรณ์','success' );
        }
        
    } catch (error) {
        console.log(error);
    }
}
</script>

<template>
    <div class="bg-white border-t-4 border-blue-600 shadow-lg rounded-lg mt-4 relative">

        <div class="absolute top-1 right-2 overflow-visible" v-if="$page.props.isCourseAdmin">
            <DotsDropdownMenu @delete-model="emit('delete-quiz')">
                <template #deleteModel>
                    <div>ลบแบบทดสอบ</div>
                </template>
            </DotsDropdownMenu>
        </div>

        <div class="m-4">
            <div class="flex flex-wrap justify-center items-center sm:justify-between">
                <div class="pt-4 text-lg font-semibold text-gray-600 dark:text-gray-300 space-x-4">
                    <span>{{ quiz.title }}</span>
                    <span>{{ (quiz.questions ? quiz.questions.length : 0) + " ข้อ " }} {{ quiz.total_score || 0 }}
                        คะแนน</span>
                    <span>เวลา {{ quiz.time_limit }} นาที</span>
                </div>
                <div>
                    <div v-if="!$page.props.isCourseAdmin && questionsLength" class="flex items-center justify-center">
                        <button v-if="startQuiz" @click.prevent="handleEndQuiz"
                            class="bg-green-400 rounded-lg p-2 text-white font-semibold w-32 my-2">
                            {{ Math.floor(timeCountdown / 60) + ' : ' + timeCountdown % 60 }}
                        </button>
                        <button v-else @click.prevent="handleStartQuiz" class="plearnd-btn-primary w-32 my-2">
                            เริ่มทดสอบ
                        </button>
                    </div>
                </div>
            </div>

            <div>

            </div>

            <hr />

            <p class="pt-4">{{ quiz.description }}</p>

        </div>
        
        <hr class="border-b-2 border-blue-500" />

        <div class="">
            <QuestionsListViewer 
                :questionableType="'courses'" 
                :questionableId="$page.props.course.data.id"
                :questionNameTh="'รายวิชา'"
                :questionApiRoute="`/courses/${$page.props.course.data.id}/quizzes/${props.quiz.id}`"
                :questions="props.quiz.questions" 
                v-model="startQuiz" 

                @delete-question="(qid,qidx)=> handleDeleteQuestion(qid,qidx)"
            />
            <!-- v-model:questions="props.quiz.questions"  -->
        </div>

        <!-- <div v-if="!$page.props.isCourseAdmin && startQuiz" class="w-full flex justify-center">
            <button class="plearnd-btn-success w-60 mb-4">บันทึกผล</button>
        </div> -->

        <hr v-if="$page.props.isCourseAdmin" class="border-b-2 border-blue-500" />

        <div v-if="$page.props.isCourseAdmin">
            <CreateNewQuestion 
                :questionApiRoute="`/courses/${$page.props.course.data.id}/quizzes/${props.quiz.id}`"
                @add-new-question="(newQuestion) => addNewQuestion(newQuestion)"
            />
        </div>
    </div>
</template>
