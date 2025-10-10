<script setup>
import { ref, computed } from 'vue';
import DotsDropdownMenu from '@/PlearndComponents/accessories/DotsDropdownMenu.vue';
import QuestionsListViewer from '@/PlearndComponents/learn/courses/questions/QuestionsListViewer.vue';
import CreateNewQuestion from '@/PlearndComponents/learn/courses/questions/CreateNewQuestion.vue';

import Swal from 'sweetalert2';
import { Icon } from '@iconify/vue';

const props = defineProps({
    quiz: Object,
    quizIndex: Number,
    quizzesApiRoute: String
});

const emit = defineEmits([
    'update-quiz',
    'delete-quiz'
]);

const showQuestions = ref(false);

const questionsLength = computed(() => props.quiz.questions.length);
const startQuiz = ref(false);
const startQuizAt = ref(null);
const endQuiz = ref(false);
const timeCountdown = ref(null);
const limitTimeOut = ref(null);
const progressPercentage = computed(() => (timeCountdown.value / (props.quiz.time_limit * 60)) * 100);


const progressColor = computed(() => {
    if (progressPercentage.value > 50) return '#4CAF50'; // Green
    if (progressPercentage.value > 20) return '#FFC107'; // Yellow
    return '#FF5252'; // Red
});

const radialProgressStyle = computed(() => ({
    background: `radial-gradient(closest-side, white 79%, transparent 80% 100%),
                 conic-gradient(${progressColor.value} ${progressPercentage.value}%, #e0e0e0 0)`,
    width: '100px',
    height: '100px',
    borderRadius: '50%',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center'
}));

const handleAddQuizPoints = (points) => {
    props.quiz.total_score += points;
}

const handleDeleteQuizPoints = (points) => {
    props.quiz.total_score -= points;
}

function handleStartQuiz() {
    try {
        showQuestions.value = true;
        startQuizAt.value = new Date();
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

async function handleEndQuiz() {
    endQuiz.value = true;
    startQuiz.value = false;
    clearInterval(limitTimeOut.value);
    await axios.post(`${props.quizzesApiRoute}/efficiency`);
}

async function addNewQuestion(question){
    props.quiz.questions.push(question);
    props.quiz.total_score += parseInt(question.points);
    Swal.fire({
        title: 'บันทึกสำเร็จ',
        text: 'เพิ่มคำถามเสร็จเรียบร้อย.',
        icon: 'success',
        timerProgressBar: true,
        timer: 1000
    });
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

        <div class="m-2 pb-2">
            <div class="flex flex-wrap justify-center items-center sm:justify-between bg-violet-200 p-2 rounded-lg">
                <div class="pt-4 text-lg font-semibold text-violet-600 dark:text-gray-300 space-x-4">
                    <span>{{ quizIndex + '. ' + quiz.title }}</span>
                    <!-- <span>{{ (quiz.questions ? quiz.questions.length : 0) + " ข้อ " }} {{ quiz.total_score || 0 }} -->
                    <span>{{ ( questionsLength ) + " ข้อ " }} {{ (quiz.total_score || 0) +" " }}คะแนน</span>
                    <span>เวลา {{ quiz.time_limit }} นาที</span>
                </div>
                <div v-if="!$page.props.isCourseAdmin && questionsLength" class="flex items-center justify-center">
                    <div v-if="startQuiz" class="relative mt-2">
                        <div class="radial-progress" :style="radialProgressStyle">
                            <span class="text-lg font-semibold">
                                {{ Math.floor(timeCountdown / 60).toString().padStart(2, '0') }}:{{ (timeCountdown % 60).toString().padStart(2, '0') }}
                            </span>
                        </div>
                        <button @click.prevent="handleEndQuiz" class="mt-2 bg-green-500 text-white font-semibold rounded-lg p-2 w-full">
                            เสร็จแล้ว
                        </button>
                    </div>
                    <button v-else @click.prevent="handleStartQuiz" class="plearnd-btn-primary w-32 my-2">
                        เริ่มทดสอบ
                    </button>
                </div>
            </div>
        </div>

        <!-- เพิ่มปุ่มสำหรับวซ่อน/แสดงคำถาม สำหรับผู้ดูแลรายวิชา ซึ่งเป็นปุ่มกลมมีไอคอนลูกศรชี้ลงอยู่ภายใน -->
        <div v-if="$page.props.isCourseAdmin" class="flex justify-center m-2">
            <!-- Component: Base primary button with trailing icon  -->
            <button @click.prevent="toggleShowQuestions"  class="inline-flex items-center justify-center gap-2 px-5 py-2 text-sm font-medium tracking-wide text-white transition duration-300 rounded-full focus-visible:outline-none whitespace-nowrap bg-emerald-500 hover:bg-emerald-600 focus:bg-emerald-700 disabled:cursor-not-allowed disabled:border-emerald-300 disabled:bg-emerald-300 disabled:shadow-none">
                <span class="text-sm">{{ showQuestions ? 'ซ่อนคำถาม' : 'แสดงคำถาม' }}</span>
                <Icon icon="heroicons-solid:arrow-circle-down" 
                    class="w-4 h-4 duration-500 transform" :class="{ 'rotate-180': showQuestions }"
                />
            </button>
            <!-- End Base primary button with trailing icon  -->
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
                :startQuizAt="startQuizAt"

                @increase-quiz-points="(question_points) => handleAddQuizPoints(question_points)"
                @decrease-quiz-points="(question_points) => handleDeleteQuizPoints(question_points)"
            />
        </div>

        <hr v-if="$page.props.isCourseAdmin" class="border border-blue-500" />

        <div v-if="$page.props.isCourseAdmin">
            <CreateNewQuestion 
                :questionApiRoute="`/courses/${$page.props.course.data.id}/quizzes/${props.quiz.id}`"
                :quizId="props.quiz.id"
                @add-new-question="(newQuestion) => addNewQuestion(newQuestion)"
            />
        </div>    

    </div>
</template>

<style>
.radial-progress {
    transition: background 0.3s ease;
}
</style>
