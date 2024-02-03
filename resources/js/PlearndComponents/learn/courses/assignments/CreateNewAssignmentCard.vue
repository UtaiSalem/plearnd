<script setup>
import { ref, reactive } from "vue";
import { Icon } from '@iconify/vue';
import { useObjectUrl } from '@vueuse/core';
import Swal from 'sweetalert2';

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const props = defineProps({
    assignmentableType: String,
    assignmentableId: Number,
    assignmentApiRoute: String,
    assignmentNameTh: String
});

const emit = defineEmits(['add-new-assignment']);

const inputAsmImages = ref(null);
const tempImages = reactive([]);

const increasePPWhenCorrect = ref(false);
const increasePoints = ref(0);

const decreasePPWhenWrong = ref(false);
const decreasePoints = ref(0);

const hasDueDate = ref(false);
const asmDueDate = ref(new Date());

const form = ref({
    title: '',
    points: 0,
    due_date: asmDueDate.value ?? null,
    status: true,
});

const showCreateNewAssignmentForm = ref(false);

const browseInputAsmImages = () => inputAsmImages.value.click();

async function onSubmitFormHandler() {
    try {
        const config = { headers: { 'Content-Type': 'multipart/form-data' } };
        let newAsmForm = new FormData();
        newAsmForm.append('title', form.value.title);
        newAsmForm.append('points', form.value.points);
        newAsmForm.append('decreasePoints', increasePoints.value);
        newAsmForm.append('increasePoints', decreasePoints.value);
        newAsmForm.append('due_date', form.value.due_date ? new Date(form.value.due_date).toUTCString() : null);
        newAsmForm.append('status', form.value.status ? 1 : 0);

        Array.from(tempImages).forEach((image)=>{
            newAsmForm.append('images[]', image.file);
        });

        let asmResp = await axios.post(`${props.assignmentApiRoute}/assignments`, newAsmForm, config);
        if (asmResp.status===200) {
            emit('add-new-assignment', asmResp.data.assignment);
            // console.log(asmResp.data.assignment);
            // tempImages.splice(0);
            Swal.fire(
                'เรียบร้อย',
                'เพิ่มแบบฝึกหัดใหม่เสร็จสมบูรณ์',
                'success'
            )
        }

        onCancleHandler();
        // form.points = 0;  
        // showCreateNewAssignmentForm.value = false;
    } catch (err) {
        console.error(err);
    }
}
function onInputImageChange(e) {
    Array.from(e.target.files).forEach(image => {
        tempImages.push({ file: image, url: useObjectUrl(image) });
    });
}
function onCancleHandler() {
    inputAsmImages.files = [];
    form.value.title = '';
    form.value.points = 0;
    form.value.due_date = new Date();
    form.value.status = true;
    
    increasePPWhenCorrect.value = false;
    increasePoints.value = 0;

    decreasePPWhenWrong.value = false;
    decreasePoints.value = 0;

    hasDueDate.value = false;
    asmDueDate.value = new Date();

    tempImages.splice(0);
    showCreateNewAssignmentForm.value = false;
}

function deleteTempImage(index) { tempImages.splice(index, 1); }

function handleDueDateSelection(modelData){
  asmDueDate.value = modelData;
  form.value.due_date = asmDueDate.value ? new Date(asmDueDate.value) : null;
}

// function handleStartDateSelection(modelData){
//   asmStartDate.value = modelData;
//   form.value.start_date = asmStartDate.value ? new Date(asmStartDate.value) : null;
// }
// function handleEndDateSelection(modelData){
//   asmEndDate.value = modelData;
//   form.value.end_date = asmEndDate.value ? new Date(asmEndDate.value) : null;
// }

</script>
<template>
    <div class="bg-white  rounded-lg pt-4 shadow-lg mt-4">
        <div class=" w-full">
            <form @submit.prevent="onSubmitFormHandler" class="m-4 space-y-2" v-if="showCreateNewAssignmentForm">
                <!-- Component: Rounded base size basic textarea -->
                <div class="relative">
                    <textarea id="id-01" type="text" name="id-01" v-model="form.title" placeholder="Write your message"
                        rows="3"
                        class="relative w-full px-4 py-2 text-sm placeholder-transparent transition-all border rounded outline-none focus-visible:outline-none peer border-slate-200 text-slate-500 autofill:bg-white invalid:border-pink-500 invalid:text-pink-500 focus:border-emerald-500 focus:outline-none invalid:focus:border-pink-500 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"></textarea>
                    <label for="id-01"
                        class="cursor-text peer-focus:cursor-default absolute left-2 -top-2 z-[1] px-2 text-xs text-slate-400 transition-all before:absolute before:top-0 before:left-0 before:z-[-1] before:block before:h-full before:w-full before:bg-white before:transition-all peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-sm peer-required:after:text-pink-500 peer-required:after:content-['\00a0*'] peer-invalid:text-pink-500 peer-focus:-top-2 peer-focus:text-xs peer-focus:text-emerald-500 peer-invalid:peer-focus:text-pink-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400 peer-disabled:before:bg-transparent">
                        คำสั่ง/คำจี้แจง
                    </label>

                    <div class=" flex justify-end" data-title="Insert Photo">
                        <input type="file" accept="image/*" multiple ref="inputAsmImages" class="hidden"
                            @change="onInputImageChange">
                        <Icon icon="heroicons:photo"
                            class="w-9 h-9 hover:scale-110 bg-blue-200 hover:bg-blue-300 rounded-full p-2"
                            @click.prevent="browseInputAsmImages" />
                    </div>
                </div>
                <!-- End Rounded base size basic textarea -->
                <div class=" columns-1">
                    <div v-if="tempImages" class="">
                        <div v-for="(image, index) in tempImages" :key="image.url" class="">
                            <div class="relative mb-2 max-h-fit overflow-hidden">
                                <img :src="image.url" class="rounded-lg" />
                                <button v-if="$page.props.isCourseAdmin" @click.prevent="deleteTempImage(index)"
                                    class="absolute w-8 h-8 flex justify-center top-1 right-1 rounded-full cursor-pointer bg-gray-100 p-2">
                                    <Icon icon="fa-solid:trash-alt" class="w-4 h-4 text-red-500" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full flex flex-wrap items-center sm:justify-start">
                    <label for="assignment-point-input" class=" mr-4 mt-1 block text-lg font-medium text-gray-900 dark:text-white">คะแนน</label>
                    <div class="flex items-center justify-center sm:justify-start">
                        <button @click.prevent="form.points <= 0 ? 0 : form.points--;"
                            class="border-violet-600 hover:bg-violet-600 active:bg-violet-700 dark:border-violet-400 flex items-center justify-center rounded-l-xl border-2 p-2 text-xl transition duration-200 hover:cursor-pointer dark:bg-white/5 dark:text-white dark:hover:bg-white/10 dark:active:bg-white/20">
                            <Icon icon="heroicons-solid:minus-sm"
                                class="hover:text-white hover:scale-150 dark:text-white" />
                        </button>
                        <input type="number" min="0" name="" v-model="form.points" id="assignment-point-input"
                            class="w-20 h-10 border-y-2 border-x-0 border-violet-600 text-base font-bold text-center focus:outline-none">
                        <button @click.prevent="form.points++"
                            class="border-violet-600 hover:bg-violet-600 active:bg-violet-700 dark:border-violet-400 flex items-center justify-center rounded-r-xl border-2 p-2 text-xl  transition duration-200 hover:cursor-pointer dark:bg-white/5 dark:text-white dark:hover:bg-white/10 dark:active:bg-white/20">
                            <Icon icon="heroicons-solid:plus-sm"
                                class=" hover:text-white hover:scale-150 dark:text-white" />
                        </button>
                    </div>
                    <p class=" mt-5 block text-sm font-base text-red-500 dark:text-white"> * สำหรับคำนวณเกรด</p>
                </div>

                <div class="flex flex-wrap items-center mt-2">
                    <div class="relative flex items-center justify-start h-10">
                        <input 
                            id="asm-due-date-input" 
                            type="checkbox" 
                            v-model="hasDueDate" 
                            class="w-4 h-4 transition-colors bg-white border-2 rounded appearance-none cursor-pointer focus-visible:outline-none peer border-slate-500 checked:border-emerald-500 checked:bg-emerald-500 checked:hover:border-emerald-600 checked:hover:bg-emerald-600 focus:outline-none checked:focus:border-emerald-700 checked:focus:bg-emerald-700 disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-50" 
                        />
                        <label class="px-2 cursor-pointer text-slate-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400" for="asm-due-date-input">
                            กำหนดวันส่ง
                        </label>
                    </div>
                    <div v-if="hasDueDate" class="flex items-center">
                        <!-- <p class="mr-2">ถึง</p> -->
                        <VueDatePicker 
                            id="assigment-due-date-input"
                            name="assignmentDueDateInput" 
                            :format="'dd/MM/yyyy H:ss'" 
                            :model-value="asmDueDate" 
                            placeholder="สิ้นสุด"
                            @update:model-value="handleDueDateSelection"
                        >
                        </VueDatePicker>
                    </div>
                </div>

                <div class="hidden flex-wrap items-center mt-2">
                    <div class="relative flex items-center justify-start h-10">
                        <input 
                            id="asm-increase-personal-point-input" 
                            type="checkbox" 
                            v-model="increasePPWhenCorrect" 
                            class="w-4 h-4 transition-colors bg-white border-2 rounded appearance-none cursor-pointer focus-visible:outline-none peer border-slate-500 checked:border-emerald-500 checked:bg-emerald-500 checked:hover:border-emerald-600 checked:hover:bg-emerald-600 focus:outline-none checked:focus:border-emerald-700 checked:focus:bg-emerald-700 disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-50" 
                        />
                        <label for="asm-increase-personal-point-input" class="px-2 cursor-pointer text-slate-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400">
                            เพิ่มแต้มสะสมเมื่อตอบถูก <span></span>
                        </label>
                    </div>
                    <div v-if="increasePPWhenCorrect" class="flex items-center">
                        <input type="number" v-model="increasePoints" 
                        id="pp-increased-when-แorrect-input" class="rounded-lg border-2 border-violet-500 w-24"
                        >
                    </div>
                </div>
                <div class="hidden">
                    <span class="text-sm text-red-400">*แต้มสะสมเมื่อตอบถูก จะหักจากแต้มสะสมของรายวิชา</span>
                </div>
                <div class="hidden flex-wrap items-center mt-2">
                    <div class="relative flex items-center justify-start h-10">
                        <input 
                            id="asm-decrease-personal-point-input" 
                            type="checkbox" 
                            v-model="decreasePPWhenWrong" 
                            class="w-4 h-4 transition-colors bg-white border-2 rounded appearance-none cursor-pointer focus-visible:outline-none peer border-slate-500 checked:border-emerald-500 checked:bg-emerald-500 checked:hover:border-emerald-600 checked:hover:bg-emerald-600 focus:outline-none checked:focus:border-emerald-700 checked:focus:bg-emerald-700 disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-50" 
                        />
                        <label for="asm-decrease-personal-point-input" class="px-2 cursor-pointer text-slate-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400">
                            ตัดแต้มสะสมเมื่อตอบผิด/เกินกำหนด
                        </label>
                    </div>
                    <div v-if="decreasePPWhenWrong" class="flex items-center">
                        <input type="number" v-model="decreasePoints" 
                        id="pp-decreased-when-wrong-input" class="rounded-lg border-2 border-violet-500 w-24"
                        >
                    </div>
                </div>
                <div class="hidden">
                    <span class="text-sm text-red-400">*แต้มสะสมเมื่อตอบผิด/เกินกำหนด จะหักจากแต้มสะสมของผู้เรียนให้กับรายวิชา</span>
                </div>
                <div class="flex">
                    <p class="text-gray-600">ร่าง</p>
                    <input type="checkbox" v-model="form.status" class="peer sr-only opacity-0" id="toggle" />
                    <label for="toggle" class="relative flex h-6 w-11 mx-2 cursor-pointer items-center rounded-full bg-gray-400 px-0.5 outline-gray-400 transition-colors before:h-5 before:w-5 before:rounded-full before:bg-white before:shadow before:transition-transform before:duration-300 peer-checked:bg-green-500 peer-checked:before:translate-x-full peer-focus-visible:outline peer-focus-visible:outline-offset-2 peer-focus-visible:outline-gray-400 peer-checked:peer-focus-visible:outline-green-500" >
                        <span class="sr-only">Enable</span>
                    </label>
                    <p :class="form.status ? 'text-green-500 font-semibold':'text-gray-600'">เปิดใช้งาน</p>
                </div>

                <div class="py-2 mt-2  rounded-b flex justify-center items-center">
                    <div v-if="showCreateNewAssignmentForm" class="flex space-x-4">
                        <button @click.prevent="onCancleHandler"
                            class="text-gray-600 bg-red-300 hover:bg-red-400 hover:text-white focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            ยกเลิก
                        </button>
                        <button type="submit" 
                            class="text-white bg-violet-600 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            บันทึก
                        </button>
                    </div>
                </div>
            </form>
            <div v-if="!showCreateNewAssignmentForm" class="plearnd-card w-full flex justify-center mx-auto">
                <button type="button" @click.prevent="showCreateNewAssignmentForm = true"
                    class="text-white bg-violet-600 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    เพิ่มแบบฝึกหัด/ภาระงานใหม่
                </button>
            </div>
        </div>
    </div>
</template>

