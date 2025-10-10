<script setup>
import { ref, reactive } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { Icon } from '@iconify/vue';
import Textarea from 'primevue/textarea';
import { useDebounceFn } from '@vueuse/core';

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

const props = defineProps({
    course: Object,
});

const emit = defineEmits(['add-new-quiz']);

const isLoading = ref(false);
const isFetchingQuizs = ref(false);
const isCopyingQuizs = ref(false);
const initTimeLimit = ref(1);
const showCreateNewQuizeForm = ref(false);
const tempDate = ref(new Date());
const start_date = ref(new Date());
const end_date = ref(new Date(start_date.value.getTime() + initTimeLimit.value * 60000));
const errors = ref(null);

const oldQuizs = ref(null);

const form = reactive({
  user_id: usePage().props.auth.user.id,
  title: '',
  description: '',
  start_date: start_date.value.toISOString(),
  end_date: end_date.value.toISOString(),    
  shuffle_questions: false,
  time_limit: initTimeLimit.value,
  is_active: true,
  passing_score: 50
})

function handleStartDateSelect(){
    form.start_date = start_date.value == null ? null : start_date.value.toLocaleString();
}
function handleEndDateSelect(){
    tempDate.value = end_date.value;
    form.end_date  = tempDate.value.toISOString();
}

const handleTimeLimitChange = () => {
    end_date.value = new Date(start_date.value.getTime() + initTimeLimit.value * 60000);
    form.end_date = end_date.value.toISOString();
}
const handleTimeLimitInput = () => {
    if (!initTimeLimit.value) return;

    end_date.value = new Date(start_date.value.getTime() + initTimeLimit.value * 60000);
}

// const form = useForm({
//   user_id: usePage().props.auth.user.id,
//   title: 'ทดสอบ',
//   description: 'ไม่มีคำอธิบาย',
//   start_date: new Date(start_date),
//   end_date: new Date(end_date),
//   shuffle_questions: false,
//   time_limit: 10,
//   is_active: true,
//   passing_score: 0
// })

const handleCreateNewQuizFormSubmit = async () => {

    if(isLoading.value) return;

    if (end_date.value <= start_date.value) {
        SwalAlert('ล้มเหลว', 'วันที่และเวลาสิ้นสุดต้องมากกว่าจากวันที่เริ่ม', 'error');
        return;
    }

    try {
        isLoading.value = true;
        const resp = await axios.post(`/courses/${props.course.id}/quizzes`, form);
        if (resp.data && resp.data.success) {

            emit('add-new-quiz', resp.data.quiz);

            SwalAlert('สำเร็จ', 'เพิ่มแบบทดสอบเรียบร้อย', 'success');

            handleCancleCreateNewQuizForm();
        }else{
            console.log(resp.data);
            SwalAlert('ล้มเหลว', 'เกิดข้อผิดพลาด', 'error');
        }
        isLoading.value = false;
    } catch (error) {
        console.log(error);
        isLoading.value = false;
        SwalAlert('ล้มเหลว', 'เกิดข้อผิดพลาด' + error , 'error');
    }
}

const handleCancleCreateNewQuizForm = () => {
    showCreateNewQuizeForm.value = false;
    errors.value = null;
    form.title = '';
    form.description = '';
    form.shuffle_questions = false;
    form.time_limit = 10;
    form.is_active = true;
    form.passing_score = 50;
    start_date.value = new Date();
    form.start_date = start_date.value;
    end_date.value = new Date();
    form.end_date = end_date.value;
    isLoading.value = false;
}

const handleQuizTitleInput = () => {
    if(isFetchingQuizs.value) return;

    isFetchingQuizs.value = true;
    fetchOldQuizzes();
}
const fetchOldQuizzes = useDebounceFn( async () => {
    try {
        const resp = await axios.get(`/quizzes/get-quizzes`, { params: { title: form.title } });
        if (resp.data) {
            oldQuizs.value = resp.data.quizs;
        }
    } catch (error) {
        console.log(error);
    } finally {
        isFetchingQuizs.value = false;
    }
}, 1000);

const handleClickOldQuizs = (quiz) => {
    form.title = quiz.title;
    form.description = quiz.description;
    form.start_date = quiz.start_date;
    form.end_date = quiz.end_date;
    form.shuffle_questions = quiz.shuffle_questions;
    initTimeLimit.value = quiz.time_limit;
    form.is_active = quiz.is_active;
    form.passing_score = quiz.passing_score || 50;

    // clear old quizs value
    oldQuizs.value = null;
}

const handleCopyOldQuizs = async (quiz) => {
    if(isCopyingQuizs.value) return;
    isCopyingQuizs.value = true;
    try {
        const resp = await axios.post(`/quizzes/${quiz.id}/duplicate`, { course_id: props.course.id } );
        if (resp.data && resp.data.success) {

            emit('add-new-quiz', resp.data.quiz);

            SwalAlert('สำเร็จ', 'เพิ่มแบบทดสอบเรียบร้อย', 'success');

            handleCancleCreateNewQuizForm();

        }else{
            console.log(resp.data);
            SwalAlert('ล้มเหลว', 'เกิดข้อผิดพลาด', 'error');
        }
    } catch (error) {
        console.log(error);
    } finally {
        isCopyingQuizs.value = false;
    }
}
const handleSpliceOldQuizs = (old_q_index) => {
    oldQuizs.value.splice(old_q_index, 1);
}

function SwalAlert(title, text, icon) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
    });
}

</script>

<template>
    <div class="mt-4">
        <div v-if="$page.props.isCourseAdmin && !showCreateNewQuizeForm" 
            class="plearnd-card flex justify-center items-center">
            <!-- <h2 class="title font-bold text-lg text-gray-600">เพิ่มแบบทดสอบ</h2> -->
            <button type="button" class=" px-4 py-2 text-white text-semibold rounded-lg bg-violet-500"
                @click="showCreateNewQuizeForm = !showCreateNewQuizeForm">เพิ่มแบบทดสอบ
            </button>
        </div>

        <div v-if="$page.props.isCourseAdmin && showCreateNewQuizeForm" class="plearnd-card mt-4">
            <form @submit.prevent="handleCreateNewQuizFormSubmit" method="POST"
            class="max-w-full mx-auto"
            >
                <div class="mb-2">
                    <label class="block text-gray-500 text-sm font-bold mb-2" for="course-quiz-name-input-form">
                        ชื่อแบบทดสอบ <span class="text-red-500">*</span>
                    </label>
                    <!-- <input
                    class="w-full px-4 py-3 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white"
                        id="title" type="text" required placeholder="ชื่อแบบทดสอบ" /> -->
                    <Textarea v-model="form.title" @input="handleQuizTitleInput"
                        class="w-full px-4 py-3 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white"
                        id="course-quiz-name-input-form" rows="1" required placeholder="ชื่อแบบทดสอบ">
                    </Textarea>
                </div>

                <div  class="mb-2 max-h-40 overflow-y-auto">
                    <Icon icon="svg-spinners:6-dots-rotate" v-if="isFetchingQuizs" class="w-full flex items-center justify-center" />
                    <div v-for="(quiz) in oldQuizs" :key="quiz.id">
                        <div class="w-full flex items-center justify-between">
                            <button type="button" @click.prevent="handleClickOldQuizs(quiz)"
                                class="flex items-center w-full p-3 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white">
                                <Icon icon="solar:card-send-broken" class="w-5 h-5 mx-1" />
                                <span class="mx-1">{{ quiz.title }}</span>
                            </button>
                            <button type="button" @click.prevent="handleCopyOldQuizs(quiz)" class="h-full p-3 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white">
                                <Icon icon="prime:copy" class="w-5 h-5 mx-1 text-green-800 rounded-full" />
                            </button>
                            <button type="button" @click.prevent="handleSpliceOldQuizs(old_q_index)">
                                <Icon icon="bx:bxs-trash" class="w-5 h-5 mx-1 text-red-500" />
                            </button>
                        </div>
                        <hr />
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-500 text-sm font-bold mb-2" for="course-quiz-description-input-form">
                        คำอธิบาย
                    </label>
                    <textarea v-model="form.description"
                        class="w-full px-4 py-3 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white"
                        id="course-quiz-description-input-form" rows="3" placeholder="คำอธิบายเพิ่มเติมถ้ามี">
                    </textarea>
                </div>

                <div class="mb-4 flex items-center space-x-2">
                    <p>เวลาที่ใช้ทำ</p>
                        <input type="number" 
                            id="course-quiz-time_limit-input" 
                            min="0" 
                            v-model="initTimeLimit" 
                            @change="handleTimeLimitChange"
                            @input="handleTimeLimitInput"
                            placeholder="" class="w-16 h-8 px-2  placeholder-transparent transition-all border rounded outline-none focus-visible:outline-none peer border-slate-300 text-slate-500 autofill:bg-white invalid:border-pink-500 invalid:text-pink-500 focus:border-violet-500 focus:outline-none invalid:focus:border-pink-500 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                        />
                    <p>นาที</p>
                </div>

                <div class="mb-4">
                    <p class="block text-gray-500 text-sm font-bold mb-2" >
                        วันที่เริ่ม - สิ้นสุด
                    </p>
                    
                    <div id="course-quiz-duration-date-input-form" class="grid grid-cols-1 sm:grid-cols-2 justify-center md:justify-between gap-2 ">
                        <div>
                            <VueDatePicker id="course-quiz-start-date-input-form" class="w-full md:w-1/2"
                                v-model="start_date" 
                                name="form-start_date" 
                                time-picker-inline
                                :format="'dd/MM/yyyy HH:mm:ss'" 
                                :modelValue="start_date" 
                                @update:modelValue="handleStartDateSelect"
                            />
                        </div>
                        <div>
                            <VueDatePicker id="course-quiz-end-date-input-form" class="w-full md:w-1/2"
                                v-model="end_date" 
                                name="form-end_date" 
                                time-picker-inline
                                :format="'dd/MM/yyyy HH:mm:ss'"
                                :model-value="end_date" 
                                @update:model-value="handleEndDateSelect"
                            />
                        </div>
                    </div>
                    <div v-if="errors">
                        <small v-for="(err, index) in errors" :key="index" class="text-red-500">
                            {{ '#:'+err }}
                        </small>
                    </div>
                </div>

                <div class="mb-4 flex flex-wrap items-center space-x-2">
                    <p>คะแนนผ่านขั้นต่ำ</p>
                    <input id="course-quiz-passing_score-input"  type="number" min="0" v-model="form.passing_score" placeholder="" class="w-16 h-8 px-2  placeholder-transparent transition-all border rounded outline-none focus-visible:outline-none peer border-slate-300 text-slate-500 autofill:bg-white invalid:border-pink-500 invalid:text-pink-500 focus:border-violet-500 focus:outline-none invalid:focus:border-pink-500 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400" />
                    <p>%</p>
                    <small class="text-red-500">*คะแนนรวมจะถูกคำนวนจากแต่ละคำถาม</small>
                    <!-- <label for="course-quiz-passing_score-input" class="cursor-text peer-focus:cursor-default peer-autofill:-top-2 absolute left-2 -top-2 z-[1] px-2 text-xs text-slate-400 transition-all before:absolute before:top-0 before:left-0 before:z-[-1] before:block before:h-full before:w-full before:bg-white before:transition-all peer-placeholder-shown:top-3 peer-placeholder-shown:left-10 peer-placeholder-shown:text-sm peer-required:after:text-pink-500 peer-required:after:content-['\00a0*'] peer-invalid:text-pink-500 peer-focus:-top-2 peer-focus:left-2 peer-focus:text-xs peer-focus:text-violet-500 peer-invalid:peer-focus:text-pink-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400 peer-disabled:before:bg-transparent">
                        ค่าธรรมเนียมสมัครสมาชิก
                    </label> -->
                    <!-- <Icon icon="noto:coin" class="w-6 h-6 top-3 left-4 stroke-slate-400 peer-disabled:cursor-not-allowed" /> -->
                </div>


                <div class="mb-4">
                    <!-- Component: Primary basic checkbox -->
                    <div class="relative flex flex-wrap items-center">
                        <input
                            class="w-4 h-4 transition-colors bg-white border-2 rounded appearance-none cursor-pointer focus-visible:outline-none peer border-slate-500 checked:border-emerald-500 checked:bg-emerald-500 checked:hover:border-emerald-600 checked:hover:bg-emerald-600 focus:outline-none checked:focus:border-emerald-700 checked:focus:bg-emerald-700 disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-50"
                            type="checkbox" id="course-quiz-shuffle-questions-form-input" v-model="form.shuffle_questions" />
                        <label
                            class="pl-2 cursor-pointer text-slate-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400"
                            for="course-quiz-shuffle-questions-form-input">
                            สลับข้อสำหรับผู้สอบแต่ละคน
                        </label>
                    </div>
                    <!-- End Primary basic checkbox -->
                </div>

                <div class="mb-4">
                    <div class="flex flex-wrap items-center space-x-2">
                        <p class="text-slate-500">ร่าง</p>
                        <input type="checkbox" v-model="form.is_active" class="peer sr-only opacity-0" id="course-quize-is_active-form-input" />
                        <label for="course-quize-is_active-form-input" class="relative flex h-6 w-11 cursor-pointer items-center rounded-full bg-gray-400 px-0.5 outline-gray-400 transition-colors before:h-5 before:w-5 before:rounded-full before:bg-white before:shadow before:transition-transform before:duration-300 peer-checked:bg-green-500 peer-checked:before:translate-x-full peer-focus-visible:outline peer-focus-visible:outline-offset-2 peer-focus-visible:outline-gray-400 peer-checked:peer-focus-visible:outline-green-500">
                            <span class="sr-only">Enable</span>
                        </label>
                        <p class="text-slate-500">เปิดสอบ</p>
                    </div>
                </div>

                <div class="flex justify-center space-x-2">
                        <!-- class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" -->
                    <button @click.prevent="handleCancleCreateNewQuizForm"
                        class="plearnd-btn-danger max-w-fit"
                        type="button">
                        ยกเลิก
                    </button>
                    <button :disabled="isLoading"
                        class="plearnd-btn-primary max-w-fit flex items-center"
                        type="submit">
                        <svg v-if="isLoading" width="20" height="20" fill="currentColor" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                            </path>
                        </svg>
                        บันทึก
                    </button>
                </div>
            </form>
        </div>
        <div v-if="isCopyingQuizs" class="fixed inset-0 bg-white bg-opacity-80 z-20 h-full w-full flex items-center justify-center">
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