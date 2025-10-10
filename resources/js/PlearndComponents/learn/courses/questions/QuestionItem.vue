<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import Swal from 'sweetalert2';
import QuestionImagesViewer from "@/PlearndComponents/learn/courses/questions/QuestionImagesViewer.vue";
import QuestionOptionForm from '@/PlearndComponents/learn/courses/questions/QuestionOptionForm.vue';
import VerticalDotsDropdownMenu from '@/PlearndComponents/accessories/VerticalDotsDropdownMenu.vue';
import EditQuestionModal from '@/PlearndComponents/learn/courses/questions/EditQuestionModal.vue';

const props = defineProps({
    questionApiRoute: String,
    question: Object,
    indexNumber: Number,
    isCourseOwner: Boolean,
    startQuizAt: Date,
});

const emit = defineEmits([
    'delete-question', 
    'update-question',
]);

const selectedOption = ref(props.question.isAnsweredByAuth);
const correctAnswer = ref(props.question.correct_option_id ?? null);
const showConfirmAnswerButton = ref(props.question.isAnsweredByAuth ? false : true);
const canEditAnswer = ref(props.question.isAnsweredByAuth ? true : false);

const isLoading = ref(false);
const optionActionIndex = ref(null);
const isOptionImageLoading = ref(false);
const deletingOptionImageIndex = ref(null);

const showEditModal = ref(false);
const isOpen = ref(!props.isCourseOwner);
const showEditAnswerButton = computed(() => canEditAnswer.value && !showConfirmAnswerButton.value);

function setCanEditAnswer(){
    // ตรวจสอบว่าผู้ใช้งานมีแต้มสะสมน้อยกว่า 120 แต้ม ให้แจ้งเตือนและออกจากการทำงานนี้
    // if (props.question.pp_fine<1){
    //     canEditAnswer.value = false;
    //     showConfirmAnswerButton.value = true;
    //     return;
    // }
    
    if (usePage().props.auth.user.pp < props.question.pp_fine) {
        Swal.fire({
            title: 'ขออภัย! คุณมีแต้มสะสมไม่เพียงพอ',
            text: "ต้องใช้แต้มสะสม " + props.question.pp_fine + " แต้ม",
            icon: 'warning',
        });
        canEditAnswer.value = false;
        showConfirmAnswerButton.value = true;
        return;
    }

    Swal.fire({
        title: 'แก้ไขคำตอบ',
        text: "ต้องใช้แต้มสะสม " + props.question.pp_fine + " แต้ม",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#7c3aed',
        cancelButtonColor: '#f87171',
        confirmButtonText: 'ยืนยันการแก้ไข'
    }).then((result) => {
        if (result.isConfirmed) {
            canEditAnswer.value = false;
            showConfirmAnswerButton.value = true;
        }
    })
}

async function handleConfirmAnswer() {
    try {
        let resultResp = null;
        isLoading.value = true;
        if (props.question.isAnsweredByAuth === null) { 
            resultResp = await axios.post(`/quizs/${props.question.questionable_id}/questions/${props.question.id}/answers`,
                { 
                    course_id: usePage().props.course.data.id,
                    answer_id: selectedOption.value,
                    started_at: props.startQuizAt
                });
        }else{ 
            if (usePage().props.auth.user.pp < props.question.pp_fine) {
                Swal.fire('ขออภัย! แต้มสะสมของคุณไม่เพียงพอ',
                'ไม่สามารถแก้ไขคำตอบได้ <br /> ต้องสะสมแต้มเพิ่มเติม <br /> ก่อนทำการแก้ไขคำตอบ',
                'error'
                );
                showConfirmAnswerButton.value = false;
                canEditAnswer.value = false;
                return;
            }else{
                resultResp = await axios.patch(`/quizs/${props.question.questionable_id}/questions/${props.question.id}/answers/${props.question.authAnswerQuestion}`,
                { 
                    course_id: usePage().props.course.data.id,
                    answer_id: selectedOption.value,
                });
            }
        }

        if (resultResp.data.success) {
            props.question.authAnswerQuestion = resultResp.data.authAnswerQuestion
            props.question.isAnsweredByAuth = true;
            showConfirmAnswerButton.value = false;
            canEditAnswer.value = true;
            
            Swal.fire('สำเร็จ', resultResp.data.message + 'คำตอบเสร็จเรียบร้อยแล้ว.','success' );
            isLoading.value = false;
        }else{
            Swal.fire('บันทึกล้มเหลว','กรุณาลองใหม่','error' );
            isLoading.value = false;
        }
        
        if (resultResp.data.message === 'แก้ไข') {
            usePage().props.auth.user.pp -= props.question.pp_fine        
        }

    } catch (error) {
        console.log(error);
        Swal.fire('ล้มเหลว','เกิดข้อผิดพลาด รอการตรวจสอบ','error' );
        isLoading.value = false;
    }
}

const handleAddNewOption = (newOption) => {
    props.question.options.push(newOption);
    props.question.options.sort((a, b) => a.id - b.id);
};


async function deleteOption(id, idx) {
    try {
        optionActionIndex.value = idx;
        await axios.delete(`/options/${id}`);
        props.question.options.splice(idx, 1);
    } catch (error) {
        console.error('Error deleting option:', error);
    } finally {
        optionActionIndex.value = null;
    }
}

async function setCorrectAnswer(aid) {
    if (aid) {
        correctAnswer.value = aid;
        await axios.patch(`/questions/${props.question.id}/set_correct_option`, { answer: aid });
    } else {
        Swal.fire('ยังไม่เลือกคำตอบ','กรุณาเลือกคำตอบ','error' );
    }
}

const handleAnswerChange = (answerId) => {
    selectedOption.value = answerId;
};

const toggleAnswer = () => {
    isOpen.value = !isOpen.value;
};

const handleUpdateQuestion = (updated_question) => {
    props.question.text = updated_question.text;
    props.question.points = updated_question.points;
    props.question.pp_fine = updated_question.pp_fine;

    emit('update-question', updated_question);
};

const handleDeleteQuestionImage = (questionImageIndex) => {
    props.question.images.splice(questionImageIndex, 1);
};

const handleDeleteQuestion = () => {
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "คุณไม่สามารถย้อนกลับการกระทำนี้ได้!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            emit('delete-question');
        }
    });
};

const deleteOptionImage = async (questionOptionIndex, optionImageIndex)=> {
    try {
        isOptionImageLoading.value = true;
        deletingOptionImageIndex.value = optionImageIndex;
        const imageId = props.question.options[questionOptionIndex].images[optionImageIndex].id;
        const delImgResp = await axios.delete(`/questions/${props.question.id}/images/${imageId}`);
        if (delImgResp.data.success) {
            props.question.images.splice(optionImageIndex, 1);
        } else {
            Swal.fire('ลบรูปภาพ', 'ลบรูปภาพไม่สำเร็จ', 'error');
        }      
    } catch (error) {
        console.log(error);
    } finally {
        isOptionImageLoading.value = false;
        deletingOptionImageIndex.value = false;
    }
}
</script>

<template>
    <div>
        <div class="mx-auto space-y-4 p-2">
            <div class="transition-all duration-500">
                <div class="flex">
                    <button type="button" @click="toggleAnswer"
                        class="bg-blue-200 rounded-md flex items-center justify-between w-full p-3">
                        <div class="flex items-center text-lg font-base text-gray-700 text-left w-full">
                            <p>
                                ข้อที่ {{ indexNumber + 1 }} {{ question.text }}
                            </p>
                            <p class="mx-2">
                                <span class="text-sm text-green-500 ">{{ '@'+question?.points + ' คะแนน ' }}</span>
                                <span class="text-sm text-red-500 ">{{ '#'+question?.pp_fine + ' แต้ม' }}</span>
                            </p>
                        </div>
                        <Icon icon="fa6-solid:chevron-down" class="w-4 h-4 text-gray-600 duration-500 transform" :class="{ 'rotate-180': isOpen }" />
                    </button>
                    <div v-if="props.isCourseOwner">
                        <VerticalDotsDropdownMenu
                            @edit-model="showEditModal = true" 
                            @delete-model="handleDeleteQuestion" 
                        >
                            <template #editModel>
                                <span>แก้ไขคำถาม</span>
                            </template>
                            <template #deleteModel>
                                <span>ลบคำถาม</span>
                            </template>
                        </VerticalDotsDropdownMenu>
                    </div>
                </div>

                <div v-show="isOpen" class="transform transition duration-500 ">
                    <QuestionImagesViewer
                        :modelId="question.id"
                        :images="question.images"
                        :isCourseOwner="props.isCourseOwner"

                        @delete-image="(indx)=>handleDeleteQuestionImage(indx)"
                    />

                    <div class="mt-2">
                        <div class="grid space-y-2">
                            <div v-for="(option, qo_idx) in question.options" :key="option.id" class="grid grid-cols-1 gap-4 border p-2 rounded-md">
                                <div class="flex w-full justify-between items-center">
                                    <label :for="`${question.id}+${option.id}`"
                                        class="bg-slate-100 p-3 rounded-full flex items-center w-full"
                                        :class="[{ 'bg-teal-200': selectedOption == option.id },{'hover:bg-teal-300': showConfirmAnswerButton}]">
                                        <input type="radio" :id="`${question.id}+${option.id}`"
                                            :name="`${question.id}`" :value="option.id" v-model="selectedOption" :disabled="showEditAnswerButton"
                                            :checked="selectedOption == option.id" @change="handleAnswerChange(option.id)"/>
                                        <p class="text-md text-gray-700 ml-3 dark:text-gray-400">
                                            {{ qo_idx + 1 }} . {{ option.text }}
                                        </p>
                                    </label>
                                    <button 
                                        v-if="props.isCourseOwner" 
                                        @click.prevent="deleteOption(option.id, qo_idx)"
                                        :disabled="optionActionIndex === qo_idx"
                                        :id="`btn-delete-option-${option.id}`" 
                                        class="w-9 h-9 bg-red-200 flex items-center justify-center rounded-full" 
                                    >
                                        <Icon v-if="optionActionIndex === qo_idx" icon="uiw:loading" class="w-5 h-5 text-red-600 animate-spin" />
                                        <Icon v-else icon="heroicons-outline:trash" class="w-5 h-5  text-red-600 font-bold"/>
                                    </button>
                                </div>

                                <div class="flex flex-wrap justify-center">
                                    <div v-for="(opt_image, oi_index) in option.images" :key="opt_image" class="relative  max-h-fit overflow-hidden">
                                        <img :src="opt_image.url" class="border rounded-lg md:max-w-xs" alt="Question image" />
                                        <div v-if="deletingOptionImageIndex === oi_index" 
                                            class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                            <Icon icon="svg-spinners:6-dots-rotate" class="w-16 h-16 text-white animate-spin" />
                                        </div>
                                        <button v-if="isCourseOwner" 
                                                @click="deleteOptionImage(qo_idx,oi_index)" 
                                                class="absolute top-2 right-2 rounded-full cursor-pointer bg-gray-100 p-[6px]">
                                            <Icon icon="fa-solid:trash-alt" class="w-4 h-4 text-red-500" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="my-2 flex items-center justify-center" v-if="!props.isCourseOwner">
                            <button type="button" 
                                :id="`btn-confirm-answer${question.id}`" 
                                v-if="showConfirmAnswerButton && question.options" 
                                @click.prevent="handleConfirmAnswer()" 
                                :disabled="!selectedOption || isLoading"
                                class="m-2 py-2 px-4 inline-flex justify-center items-center gap-2 rounded-full bg-violet-200 border border-violet-700 text-violet-700 ring-offset-white transition-all text-sm dark:focus:ring-offset-gray-800"
                                :class="{'hover:text-white hover:bg-violet-600': selectedOption}">
                                <Icon v-if="isLoading" icon="uiw:loading" class="animate-spin h-6 w-6 text-white"/>
                                <span>ยืนยันคำตอบ</span>
                            </button>
                            <button type="button" :id="`btn-confirm-answer${question.id}`" v-if="canEditAnswer && $page.props.auth.user.pp >= question.pp_fine" @click.prevent="setCanEditAnswer"
                                class="m-2 py-2 px-4 inline-flex justify-center items-center gap-2 rounded-full bg-red-200 border border-red-700 text-red-700 hover:text-white hover:bg-red-600 ring-offset-white transition-all text-sm dark:focus:ring-offset-gray-800">
                                แก้ไขคำตอบ
                            </button>
                        </div>

                        <div class="flex justify-between items-center" v-if="props.isCourseOwner">
                            <QuestionOptionForm
                                :questionableType="question.questionable_type"
                                :questionableId="question.questionable_id" 
                                :question_id="question.id"
                                :options="question.options"
                                @add-new-option="(newOption) => handleAddNewOption(newOption)"
                            />
                        </div>

                        <div class="my-2 mr-[50px]" v-if="props.isCourseOwner">
                            <select :id="`select-question-correct-answer${question.id}`"
                                class="py-1.5 px-4 pr-9 block w-full rounded-full text-md text-gray-700 dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400"
                                name="set-correct-answer-selection"
                                :class="[correctAnswer ? 'border-indigo-400 bg-green-200' : 'border-red-500 bg-red-200']"
                                @change="setCorrectAnswer(correctAnswer)" v-model="correctAnswer">
                                <option selected :value="false">คำตอบที่ถูกต้องคือ ข้อ</option>
                                <option v-for="(option, i) in question.options" :key="option.id"
                                    :value="option.id">
                                    {{ i + 1 }} {{ option.text }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <EditQuestionModal 
            v-if="props.isCourseOwner"
            :question
            :questionApiRoute
            :images="question.images"
            :isVisible="showEditModal"
            :isCourseOwner="props.isCourseOwner"

            @update-question="(updatedQuestion)=>handleUpdateQuestion(updatedQuestion)"
            @delete-question-image="(image_index)=>handleDeleteQuestionImage(image_index)"
            @close-modal="showEditModal = false"
        />

    </div>
</template>
