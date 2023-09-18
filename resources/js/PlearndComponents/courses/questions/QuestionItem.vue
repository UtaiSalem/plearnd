<script setup>
import { onMounted, ref,computed, reactive } from "vue";
import QuestionImagesViewer from "./QuestionImagesViewer.vue";
import QuestionOptionForm from '@/PlearndComponents/courses/questions/QuestionOptionForm.vue'
import VerticalDotsDropdownMenu from '@/PlearndComponents/accessories/VerticalDotsDropdownMenu.vue';
// import { Icon } from '@iconify/vue';
import { usePage } from "@inertiajs/vue3";
import Swal from 'sweetalert2';

const props = defineProps({
    question: Object,
    indexNumber: Number,
    isCourseOwner: Boolean,
});

const emit = defineEmits(['delete-question']);

// const optionInputBoxActive = ref(false);
// const optionInputText = ref("");
const selectedOption = ref(null);
const correctAnswer = ref(null);

const showConfirmAnswerButton = ref(false);
const showEditAnswerButton = ref(false);
const canEditAnswer = ref(false);

onMounted(() => {
    selectedOption.value = props.question.isAnsweredByAuth;
    if(props.question.isAnsweredByAuth !== null){
        showEditAnswerButton.value = true;
        canEditAnswer.value = true;
    }else{
        showConfirmAnswerButton.value = true;
        showEditAnswerButton.value = false;
    }
    initCorrectAnswer();
});


function setCanEditAnswer(){
    Swal.fire({
        title: 'แก้ไขคำตอบ',
        text: "ต้องถูกตัดแต้มสะสม " + 3 + "แต้ม",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#7c3aed',
        cancelButtonColor: '#f87171',
        confirmButtonText: 'ยืนยันการแก้ไข'
    }).then((result) => {
        if (result.isConfirmed) {
            canEditAnswer.value = false;
            showConfirmAnswerButton.value = true;
            showEditAnswerButton.value=false;
            // props.question.isAnsweredByAuth = false;
        }
    })
}

// async function handleAddOptionButtonClick() {
//     optionInputBoxActive.value = !optionInputBoxActive.value;
//     if (optionInputText.value.trim().length > 0) {
//         let newOptionResponsed = await axios.post(`/questions/${props.question.id}/options`, {
//             text: optionInputText.value,
//         });
//         props.question.options.push(newOptionResponsed.data.option);
//         optionInputText.value = "";
//     }
// }

async function handleConfirmAnswer() {
    try {
        let resultResp = null;
        if (props.question.isAnsweredByAuth == null) {
            resultResp = await axios.post(`/users/${usePage().props.auth.user.id}/answers/${selectedOption.value}/questions`,
                { 
                    question_id: props.question.id,
                });
        }else{
            if (usePage().props.auth.user.pp<3) {
                Swal.fire('เสียใจด้วย','แต้มสะสมของคุณมีน้อยเกินไป <br /> ไม่สามารถแก้ไขคำตอบได้ <br /> ต้องหาแต้มเพิ่มเติม','error' );
                showConfirmAnswerButton.value = false;
                showEditAnswerButton.value = false;
                canEditAnswer.value = false;
            }else{
                resultResp = await axios.patch(`/users/${usePage().props.auth.user.id}/answers/${selectedOption.value}/questions/${props.question.id}`);
            }
        }

        if (resultResp.data.success) {
            Swal.fire('สำเร็จ', resultResp.data.message + 'คำตอบเสร็จเรียบร้อยแล้ว.','success' );
            props.question.isAnsweredByAuth = true;
            showConfirmAnswerButton.value = false;
            showEditAnswerButton.value = true;
            canEditAnswer.value = true;
        }else{
            Swal.fire('บันทึกล้มเหลว','กรุณาลองใหม่','error' );
        }
        
        if (resultResp.data.message === 'แก้ไข') {
            usePage().props.auth.user.pp -= 3        
        }
    } catch (error) {
        console.log(error);
    }
}

async function deleteOption(id, idx) {
    await axios.delete(`/options/${id}`);
    props.question.options.splice(idx, 1);
}

async function setCorrectAnswer(aid) {
    if (aid) {
        await axios.patch(`/questions/${props.question.id}`, { answer: aid });
        correctAnswer.value = aid;
    } else {
        Swal.fire('ยังไม่เลือกคำตอบ','กรุณาเลือกคำตอบ','error' );
    }
}

async function initCorrectAnswer() {
    if (props.isCourseOwner) {
        if(props.question.options){
            let answerResp = await axios.get(`/questions/${props.question.id}/answers`);
            correctAnswer.value = answerResp.data.answer;
        }else {
            correctAnswer.value = 0;
        }
    }
}

function handleAnswerChange(answerId){
    selectedOption.value = answerId;
}

</script>

<template>
    <div class="hs-accordion-group">
        <div class="hs-accordion active" id="hs-basic-with-title-and-arrow-stretched-heading-one">
            <div class="flex items-center justify-between overflow-visible">
                <button :id="`btn-hs-accordion-group${indexNumber}`"
                    class="bg-violet-200 p-6 rounded-md hs-accordion-toggle hs-accordion-active:text-blue-600 group py-3 inline-flex items-center justify-between gap-x-3 w-full font-medium text-left text-gray-800 transition hover:text-blue-900 dark:hs-accordion-active:text-blue-500 dark:text-gray-200 dark:hover:text-gray-400"
                    aria-controls="hs-basic-with-title-and-arrow-stretched-collapse-one">
                    ข้อที่ {{ indexNumber + 1 }} {{ props.question.text }} {{ ' (' + props.question.points + ' คะแนน)' }}
                    <svg class="hs-accordion-active:hidden hs-accordion-active:text-blue-600 hs-accordion-active:group-hover:text-blue-600 block w-3 h-3 text-gray-600 group-hover:text-gray-500 dark:text-gray-400"
                        width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 5L8.16086 10.6869C8.35239 10.8637 8.64761 10.8637 8.83914 10.6869L15 5"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <svg class="hs-accordion-active:block hs-accordion-active:text-blue-600 hs-accordion-active:group-hover:text-blue-600 hidden w-3 h-3 text-gray-600 group-hover:text-gray-500 dark:text-gray-400"
                        width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 11L8.16086 5.31305C8.35239 5.13625 8.64761 5.13625 8.83914 5.31305L15 11"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>
                <!-- <button class="w-[40px]" v-if="courseOwner" @click.prevent="deleteQuestion(props.question.id)">
                    <i class="pi pi-times text-red-600 font-bold p-3 bg-red-200 rounded-full"></i>
                </button> -->
                <!-- <div class="hs-dropdown relative inline-flex">
                    <button :id="`hs-dropdown-custom-icon-trigger${props.question.id}`" v-if="isCourseOwner" type="button" data-dropdown-offset-distance="0" data-dropdown-offset-skidding="0" class="hs-dropdown-toggle py-2 px-1 ml-1 inline-flex justify-center items-center gap-2 rounded-md border font-medium bg-white  shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-offset-white focus:ring-blue-600 transition-all text-sm dark:bg-slate-900 dark:hover:bg-slate-800 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white dark:focus:ring-offset-gray-800">
                        <Icon icon="prime:ellipsis-v" class="h-6 w-6 text-violet-500" />
                    </button>

                    <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden bg-white border shadow-lg rounded-lg p-2 mt-2 top-0 left-0 right-[420px] min-w-max dark:bg-gray-800 dark:border dark:border-gray-700" :aria-labelledby="`hs-dropdown-custom-icon-trigger${props.question.id}`">
                        <button @click.prevent="emit('delete-question')" :id="`btn-delete-question-${props.question.id}`" class="flex items-center gap-x-3.5 py-2 px-3 rounded-md text-sm text-gray-800 hover:bg-gray-100 focus:ring-2 focus:ring-blue-500 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                            <i class="pi pi-trash text-red-600 font-bold p-2 bg-red-200 rounded-full"></i>
                            ลบคำถาม
                        </button>
                    </div>
                </div> -->
                <div class="" v-if="props.isCourseOwner">
                    <VerticalDotsDropdownMenu @delete-model="emit('delete-question')" />
                </div>
            </div>
            <div v-if="props.question.images.length>0" class="my-2">
                <QuestionImagesViewer 
                    :model_id="props.question.questionable_id" 
                    :edit="false" 
                    :images="props.question.images" 
                />
            </div>
            <div id="hs-basic-with-title-and-arrow-stretched-collapse-one"
                class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300"
                aria-labelledby="hs-basic-with-title-and-arrow-stretched-heading-one">
                <div class="ml-4 mt-4">
                    <div class="grid space-y-2">
                        <div v-for="(option, idx) in props.question.options" :key="option.id">
                            <div class="flex w-full justify-between items-center">
                                <label :for="`${props.question.id}+${option.id}`"
                                    class="bg-slate-100 p-3 rounded-full flex items-center w-full "
                                    :class="[{ 'bg-teal-200': selectedOption == option.id },{'hover:bg-teal-300': showConfirmAnswerButton}]">
                                    <input type="radio" :id="`${props.question.id}+${option.id}`"
                                        :name="`${props.question.id}`" :value="option.id" v-model="selectedOption" :disabled="showEditAnswerButton"
                                        :checked="selectedOption == option.id" @change="handleAnswerChange(option.id)"/>
                                    <p class="text-md text-gray-700 ml-3 dark:text-gray-400">
                                        {{ idx + 1 }} . {{ option.text }}
                                    </p>
                                </label>
                                <button :id="`btn-delete-option-${option.id}`" class="w-[40px]" v-if="isCourseOwner" @click.prevent="deleteOption(option.id,idx)">
                                    <i class="pi pi-trash text-red-600 font-bold p-2 bg-red-200 rounded-full"></i>
                                </button>
                            </div>
                            <div v-if="option.images.length>0" class="mt-1 flex justify-center">
                                <div class="w-4/5 border rounded-lg max-w-fit max-h-fit">
                                    <QuestionImagesViewer
                                        :model_id="option.id" 
                                        :edit="false" 
                                        :images="option.images" 
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="my-2 flex items-center justify-center" v-if="!props.isCourseOwner">
                        <!-- <button class=" m-2 px-4 py-2 bg-blue-400 hover:bg-blue-600 text-md text-gray-700 hover:text-white rounded-full">ยืนยันคำตอบ</button> -->
                        <button type="button" 
                            :id="`btn-confirm-answer${props.question.id}`" 
                            v-if="showConfirmAnswerButton && props.question.options.length" 
                            @click.prevent="handleConfirmAnswer()" 
                            :disabled="!selectedOption"
                            class="m-2 py-2 px-4 inline-flex justify-center items-center gap-2 rounded-full bg-violet-200 border border-violet-700 text-violet-700 ring-offset-white transition-all text-sm dark:focus:ring-offset-gray-800"
                            :class="{'hover:text-white hover:bg-violet-600': selectedOption}">
                            ยืนยันคำตอบ
                        </button>
                        <button type="button" :id="`btn-confirm-answer${props.question.id}`" v-if="canEditAnswer" @click.prevent="setCanEditAnswer"
                            class="m-2 py-2 px-4 inline-flex justify-center items-center gap-2 rounded-full bg-red-200 border border-red-700 text-red-700 hover:text-white hover:bg-red-600 ring-offset-white transition-all text-sm dark:focus:ring-offset-gray-800">
                            แก้ไขคำตอบ
                        </button>
                    </div>

                    <div class="flex justify-between items-center" v-if="isCourseOwner">
                        <!-- <div class="relative w-full">
                            <input type="text" :id="props.question.id"
                                class="p-2 block w-full border-gray-200 rounded-full text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400"
                                placeholder="เพิ่มตัวเลือก" v-model="optionInputText" />
                        </div>
                        <button type="button" :id="`btn-add-question-options${props.question.id}`" @click.prevent="handleAddOptionButtonClick()"
                            class="flex items-center justify-center mx-1 border border-violet-400 bg-violet-200 hover:bg-violet-400 hover:scale-110 focus:ring-violet-500 focus:ring-offset-violet-200 text-violet-600 hover:text-white transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-full">
                            <span v-if="optionInputText.length > 0"><i class="pi pi-save my-1 mx-2 text-base font-bold"></i></span>
                            <span v-else> <i class="pi pi-plus-circle my-1 mx-2 text-base font-bold"></i></span>
                        </button> -->
                        <QuestionOptionForm
                            :questionableType="props.question.questionable_type"
                            :questionableId="props.question.questionable_id" 
                            :question_id="props.question.id"
                            :options="props.question.options"
                            @add-new-option="(newOption) => props.question.options.push(newOption)"
                        />

                    </div>
                    <div class="my-2 mr-[50px]" v-if="isCourseOwner">
                        <select :id="`select-question-correct-answer${props.question.id}`"
                            class="py-1.5 px-4 pr-9 block w-full rounded-full text-md text-gray-700 dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400"
                            name="set-correct-answer-selection"
                            :class="[correctAnswer ? 'border-indigo-400' : 'border-red-500']"
                            @change="setCorrectAnswer(correctAnswer)" v-model="correctAnswer">
                            <option selected :value="false">คำตอบที่ถูกต้องคือ ข้อ</option>
                            <option v-for="(option, i) in props.question.options" :key="option.id"
                                :value="option.id">
                                {{ i + 1 }} {{ option.text }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

