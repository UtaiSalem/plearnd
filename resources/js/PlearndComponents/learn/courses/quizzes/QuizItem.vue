<script setup>
import { ref, computed } from 'vue';
import DotsDropdownMenu from '@/PlearndComponents/accessories/DotsDropdownMenu.vue';
import QuestionsListViewer from '@/PlearndComponents/learn/courses/questions/QuestionsListViewer.vue';
import CreateNewQuestion from '@/PlearndComponents/learn/courses/questions/CreateNewQuestion.vue';

import Swal from 'sweetalert2';
import { Icon } from '@iconify/vue';

const props = defineProps({
    quiz: Object,
    quizzesApiRoute: String
});

const emit = defineEmits([
    'update-quiz',
    'delete-quiz'
]);

const showQuestions = ref(false);

const questionsLength = computed(() => props.quiz.questions.length);
const startQuiz = ref(false);
const endQuiz = ref(false);
const timeCountdown = ref(null);
const limitTimeOut = ref(null);
const isLoading = ref(false);

async function handleStartQuiz() {
    try {
        // let qResultResp = await axios.post(`/courses/${props.quiz.course_id}/quizzes/${props.quiz.id}/results`, {});
        // if (qResultResp.data && qResultResp.data.success) {
            // startCountdown();
        // }
        showQuestions.value = true;
        timeCountdown.value = props.quiz.time_limit * 60;
        startQuiz.value = true;
        limitTimeOut.value = setInterval(() => {
            timeCountdown.value -= 1;
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

function toggleShowQuestions(){
    showQuestions.value = !showQuestions.value;
}

</script>

<template>
    <div class="bg-white border-t-4 border-blue-600 shadow-lg rounded-lg mt-4 relative">

        <div class="absolute top-1 right-2 overflow-visible" v-if="$page.props.isCourseAdmin">
            <DotsDropdownMenu 
                @edit-model="emit('update-quiz')"    
                @delete-model="emit('delete-quiz')"
            >
                <template #editModel>
                    <div>แก้ไข</div>
                </template>
                <template #deleteModel>
                    <div>ลบแบบทดสอบ</div>
                </template>
            </DotsDropdownMenu>
        </div>

        <div class="mx-4">
            <div class="flex flex-wrap justify-center items-center sm:justify-between">
                <div class="pt-4 font-semibold text-gray-600 dark:text-gray-300 ">
                    <span class="text-lg text-blue-600">{{ quiz.title }}</span>
                    <div class="space-x-2">
                        <span>
                            {{ (quiz.questions ? quiz.questions.length : 0) + " ข้อ " }} {{ quiz.total_score || 0 }}คะแนน
                        </span>
                        <span>เวลา {{ quiz.time_limit }} นาที</span>
                    </div>
                    <span class="text-sm text-red-500" v-if="quiz.start_date">{{ quiz.start_date }}</span> {{ ' - ' }}
                    <span class="text-sm text-red-500" v-if="quiz.end_date">{{ quiz.end_date }}</span>
                </div>

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

            <hr />

            <div class="flex justify-between items-center my-2" :class="{ 'pb-2': !$page.props.isCourseAdmin }">
                <p class="">{{ quiz.description }}</p>
                <button @click.prevent="toggleShowQuestions" v-if="$page.props.isCourseAdmin" class="bg-gray-400/60 hover:bg-green-500/60 p-3 rounded-full max-w-fit">
                    <Icon :icon="`bi:chevron-${showQuestions ? 'up': 'down'}`" class=" w-4 h-4" />
                </button>
            </div>
        </div>
        
        <hr v-if="$page.props.isCourseAdmin" class="border-b border-blue-500" />

        <div class="" v-if="showQuestions" >
            <QuestionsListViewer 
                :questionableType="'courses'" 
                :questionableId="$page.props.course.data.id"
                :questionNameTh="'รายวิชา'"
                :questionApiRoute="`/courses/${$page.props.course.data.id}/quizzes/${props.quiz.id}`"
                :questions="props.quiz.questions" 
                v-model="startQuiz" 

                @delete-question="(qid,qidx)=> handleDeleteQuestion(qid,qidx)"
            />
        </div>

        <!-- <hr v-if="$page.props.isCourseAdmin" class="border border-blue-500" /> -->

        <div v-if="$page.props.isCourseAdmin">
            <CreateNewQuestion 
                :questionApiRoute="`/courses/${$page.props.course.data.id}/quizzes/${props.quiz.id}`"
                @add-new-question="(newQuestion) => addNewQuestion(newQuestion)"
            />
        </div>
    </div>
</template>
