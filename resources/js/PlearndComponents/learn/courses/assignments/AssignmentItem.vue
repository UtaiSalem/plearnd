<script setup>
import { ref, reactive, computed } from 'vue';
import { Icon } from '@iconify/vue';
import { usePage } from '@inertiajs/vue3';

import AssignmentImagesViewer from '@/PlearndComponents/learn/courses/assignments/AssignmentImagesViewer.vue'
import DotsDropdownMenu from '@/PlearndComponents/accessories/DotsDropdownMenu.vue';
import AssignmentAnswerViewer from '@/PlearndComponents/learn/courses/assignments/answers/AssignmentAnswerViewer.vue';
import AssignmentAnswerForm from '@/PlearndComponents/learn/courses/assignments/AssignmentAnswerForm.vue';
import Swal from 'sweetalert2';

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const props = defineProps({
    asmIndex: Number,
    assignment: Object,
    assignmentableType: String,
    assignmentableId: Number,
    assignmentNameTh: String,
    assignmentApiRoute: String,
});

const hasDueDate = computed(() => props.assignment.due_date !== null);
const increasePPWhenCorrect = ref(false);
const increasePoints = ref(0);

const decreasePPWhenWrong = ref(false);
const decreasePoints = ref(0);

const asmDueDate = ref(new Date());

const showEditAsmForm = ref(false);
const addAsmImagesInput = ref(null);
const tempImagesFile = reactive([]);
const tempImagesUrl = reactive([]);
const browseAddAsmImages = () => {
    document.getElementById('addAsmImagesInput').click();
    // inputAsmImages.value.click();
};

function onAddAsmImageInputChange(e){
    Array.from(e.target.files).forEach(image => {
        tempImagesFile.push(image);
        tempImagesUrl.push(URL.createObjectURL(image));
    });
}

function onAddNewAnswerHandler(newAnswer) {
    props.assignment.answers.splice(0);
    props.assignment.answers.push(newAnswer);
}

function onDeleteTempImagesHandler(index) {
    tempImagesFile.splice(index, 1);
    tempImagesUrl.splice(index, 1);
}

function onCancleEditAsm() {
    tempImagesFile.splice(0);
    tempImagesUrl.splice(0);
    addAsmImagesInput.value = [];
    showEditAsmForm.value = false;
}
 
async function onSubmitEditAsm() {
    try {
        const config = { headers: { 'Content-Type': 'multipart/form-data' } };
        let asmUpdateForm = new FormData();
        asmUpdateForm.append('title', props.assignment.title);
        asmUpdateForm.append('points', props.assignment.points);
        asmUpdateForm.append('decreasePoints', increasePoints.value);
        asmUpdateForm.append('increasePoints', decreasePoints.value);
        asmUpdateForm.append('due_date', props.assignment.due_date ? new Date(props.assignment.due_date).toUTCString() : null);
        asmUpdateForm.append('status', props.assignment.status ? 1 : 0);

        Array.from(tempImagesFile).forEach((image)=>{
        asmUpdateForm.append('images[]', image);
        });

        asmUpdateForm.append('_method', 'put');
        let asmResp = await axios.post(`${props.assignmentApiRoute}/assignments/${props.assignment.id}`, asmUpdateForm, config);
        if (asmResp.status===200) {

            onCancleEditAsm();
            Swal.fire(
                'เรียบร้อย',
                'บันทึกการแก้ไขแบบฝึกหัดเสร็จสมบูรณ์',
                'success'
            )
        }  
    } catch (err) {
        console.error(err);
    }
}

function handleDeleteAssignment(idx, asmId){
    Swal.fire({
        title: 'แน่ใจหรือไม่?',
        text: "ที่จะลบแบบฝึกหัดนี้อย่างถาวร!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'ยกเลิก',
        confirmButtonText: 'ใช่, ยืนยัน!'
        }).then(async (result) => {
        if (result.isConfirmed) {
            await axios.delete(`/assignments/${asmId}`);
            usePage().props.assignments.data.splice(idx, 1);
            Swal.fire('ลบเสร็จสมบูรณ์!','ลบแบบฝึกหัดแล้ว.','success' )
        }
    });
}

function handleDueDateSelection(modelData){
//   asmDueDate.value = modelData;
  form.value.due_date = modelData ? new Date(asmDueDate.value) : null;
}
</script>

<template>
    <div class="relative bg-white border-t-4 border-blue-600 rounded-lg overflow-hidden shadow-lg">
        <div class="absolute top-1 right-2 overflow-visible" v-if="$page.props.isCourseAdmin">
            <DotsDropdownMenu @delete-model="handleDeleteAssignment(props.asmIndex, props.assignment.id)">
                <template #deleteModel>
                    <div>ลบแบบฝึกหัด</div>
                </template>
            </DotsDropdownMenu>
        </div>
        <div class="tabs flex flex-col justify-center pt-4">
            <!-- <div class=" p-4 w-full " :class="{'border-b-2 border-blue-400' : !$page.props.isCourseAdmin}"> -->
            <div class=" p-4 w-full border-b-2 border-blue-400">
                <div class="text-xl font-medium">
                    <span class="text-gray-800">
                        แบบฝึกหัด/ภาระงานที่ {{ props.asmIndex+1 }}
                    </span> <br />
                    <span class="text-red-600">{{ props.assignment.points }} คะแนน</span>
                    <span class="m-2 text-sm">( ส่งคำตอบแล้ว {{ props.assignment.answers.length }} คำตอบ )</span>
                </div>
                <div class="my-2">
                    <form :id="`comment-form-${props.assignment.id}`" v-if="$page.props.isCourseAdmin && showEditAsmForm"
                        class="mt-3 relative dark:border-secondary-800 flex flex-col space-y-2 items-center w-full">
                        <input type="text" v-model="props.assignment.title" :id="`comment-form-comment-input-${props.assignment.id}`"
                            class="text-sm bg-slate-100 w-full dark:text-gray-600 rounded-lg focus:ring-0 border-gray-400">
                    </form>
                    <div v-else class="flex flex-col space-y-2">
                        <p class=" bg-slate-100 rounded-lg px-4 py-2 border my-1">{{ props.assignment.title }}</p>
                    </div>
                </div>
                <div v-if="props.assignment.images.length>0" class="my-2">
                    <AssignmentImagesViewer 
                        :model_id="assignmentableId"
                        :edit="$page.props.isCourseAdmin && showEditAsmForm" 
                        :images="props.assignment.images" 
                    />
                </div>
                <div v-if="tempImagesUrl.length>0">
                    <div v-for="(image,index) in tempImagesUrl" :key="index" class="">
                        <div class="relative mb-2 max-h-fit overflow-hidden">
                            <img :src="image" class="rounded-lg" />
                            <button @click.prevent="onDeleteTempImagesHandler(index)" v-if="showEditAsmForm"
                                class="absolute top-1 right-1 rounded-full cursor-pointer bg-gray-100 p-[6px]">
                                <Icon icon="fa-solid:trash-alt" class="w-4 h-4 text-red-500" />
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="$page.props.isCourseAdmin && showEditAsmForm" class="flex justify-end">
                    <input type="file" accept="image/*" multiple ref="addAsmImagesInput" id="addAsmImagesInput"
                        class="hidden" @change="onAddAsmImageInputChange">
                    <button class="" id="" @click.prevent="browseAddAsmImages">
                        <Icon icon="heroicons:photo"
                            class="w-9 h-9 hover:scale-110 bg-violet-300 hover:bg-blue-300 text-violet-600 rounded-full p-2 mr-auto"
                        />
                    </button>
                </div>
                <div v-if="$page.props.isCourseAdmin && showEditAsmForm" class="flex justify-start">
                    <label for="assignment-point-input"
                        class=" mr-4 mt-1 block text-lg font-medium text-gray-900 dark:text-white">คะแนน</label>
                    <button @click.prevent="props.assignment.points<=0? 0: props.assignment.points--;"
                        class="border-violet-500 hover:bg-violet-600 active:bg-violet-700 dark:border-violet-400 flex items-center justify-center rounded-l-xl border-2 p-2 text-xl  transition duration-200 hover:cursor-pointer dark:bg-white/5 dark:text-white dark:hover:bg-white/10 dark:active:bg-white/20">
                        <Icon icon="heroicons-solid:minus-sm"
                            class="hover:text-white hover:scale-150 dark:text-white" />
                    </button>
                    <input type="number" v-model="props.assignment.points" id="assignment-point-input"
                        class="w-20 border-x-0 border-y-2 border-violet-500">
                    <button @click.prevent="props.assignment.points++"
                        class="border-violet-500 hover:bg-violet-600 active:bg-violet-700 dark:border-violet-400 flex items-center justify-center rounded-r-xl border-2 p-2 text-xl  transition duration-200 hover:cursor-pointer dark:bg-white/5 dark:text-white dark:hover:bg-white/10 dark:active:bg-white/20">
                        <Icon icon="heroicons-solid:plus-sm"
                            class=" hover:text-white hover:scale-150 dark:text-white" />
                    </button>
                </div>

                <div  v-if="$page.props.isCourseAdmin && showEditAsmForm" class="hidden flex-wrap items-center mt-2">
                    <div class="relative flex items-center justify-start h-10">
                        <input 
                            :id="'edit-asm-due-date-input'+props.assignment.id" 
                            type="checkbox" 
                            v-model="hasDueDate" 
                            class="w-4 h-4 transition-colors bg-white border-2 rounded appearance-none cursor-pointer focus-visible:outline-none peer border-slate-500 checked:border-emerald-500 checked:bg-emerald-500 checked:hover:border-emerald-600 checked:hover:bg-emerald-600 focus:outline-none checked:focus:border-emerald-700 checked:focus:bg-emerald-700 disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-50" 
                        />
                        <label class="px-2 cursor-pointer text-slate-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400" 
                            :for="'edit-asm-due-date-input'+props.assignment.id">
                            กำหนดวันส่ง
                        </label>
                    </div>
                    <div v-if="hasDueDate" class="flex items-center">
                        <!-- <p class="mr-2">ถึง</p> -->
                        <VueDatePicker 
                            id="assigment-due-date-input"
                            name="assignmentDueDateInput" 
                            :format="'dd/MM/yyyy H:ss'" 
                            :model-value="props.assignment.due_date" 
                            placeholder="สิ้นสุด"
                            @update:model-value="handleDueDateSelection"
                        >
                        </VueDatePicker>
                    </div>
                </div>

                <div  v-if="$page.props.isCourseAdmin && showEditAsmForm" class="hidden flex-wrap items-center mt-2">
                    <div class="relative flex items-center justify-start h-10">
                        <input 
                            :id="'edit-asm-increase-personal-point-input'+props.assignment.id" 
                            type="checkbox" 
                            v-model="increasePPWhenCorrect" 
                            class="w-4 h-4 transition-colors bg-white border-2 rounded appearance-none cursor-pointer focus-visible:outline-none peer border-slate-500 checked:border-emerald-500 checked:bg-emerald-500 checked:hover:border-emerald-600 checked:hover:bg-emerald-600 focus:outline-none checked:focus:border-emerald-700 checked:focus:bg-emerald-700 disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-50" 
                        />
                        <label :for="'edit-asm-increase-personal-point-input'+props.assignment.id" class="px-2 cursor-pointer text-slate-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400">
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
                <div  v-if="$page.props.isCourseAdmin && showEditAsmForm" class="hidden flex-wrap items-center mt-2">
                    <div class="relative flex items-center justify-start h-10">
                        <input 
                            :id="'edit-asm-decrease-personal-point-input'+props.assignment.id" 
                            type="checkbox" 
                            v-model="decreasePPWhenWrong" 
                            class="w-4 h-4 transition-colors bg-white border-2 rounded appearance-none cursor-pointer focus-visible:outline-none peer border-slate-500 checked:border-emerald-500 checked:bg-emerald-500 checked:hover:border-emerald-600 checked:hover:bg-emerald-600 focus:outline-none checked:focus:border-emerald-700 checked:focus:bg-emerald-700 disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-50" 
                        />
                        <label :for="'edit-asm-decrease-personal-point-input'+props.assignment.id" class="px-2 cursor-pointer text-slate-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400">
                            ตัดแต้มสะสมเมื่อตอบผิด/เกินกำหนด
                        </label>
                    </div>
                    <div v-if="decreasePPWhenWrong" class="flex items-center">
                        <input type="number" v-model="decreasePoints" 
                        :id="'edit-pp-decreased-when-wrong-input'+props.assignment.id" class="rounded-lg border-2 border-violet-500 w-24"
                        >
                    </div>
                </div>
                <div class="hidden">
                    <span class="text-sm text-red-400">*แต้มสะสมเมื่อตอบผิด/เกินกำหนด จะหักจากแต้มสะสมของผู้เรียนให้กับรายวิชา</span>
                </div>
                <div  v-if="$page.props.isCourseAdmin && showEditAsmForm" class="hidden">
                    <p class="text-gray-600">ร่าง</p>
                    <input type="checkbox" v-model="props.assignment.status" class="peer sr-only opacity-0" :id="'toggle-edit-asm-status'+props.assignment.id" />
                    <label :for="'toggle-edit-asm-status'+props.assignment.id" class="relative flex h-6 w-11 mx-2 cursor-pointer items-center rounded-full bg-gray-400 px-0.5 outline-gray-400 transition-colors before:h-5 before:w-5 before:rounded-full before:bg-white before:shadow before:transition-transform before:duration-300 peer-checked:bg-green-500 peer-checked:before:translate-x-full peer-focus-visible:outline peer-focus-visible:outline-offset-2 peer-focus-visible:outline-gray-400 peer-checked:peer-focus-visible:outline-green-500" >
                        <span class="sr-only">Enable</span>
                    </label>
                    <p :class="props.assignment.status ? 'text-green-500 font-semibold':'text-gray-600'">เปิดใช้งาน</p>
                </div>

                <div class="mt-2">
                    <div class="quick-post-footer flex items-center justify-end">
                        <div class="  rounded-b flex justify-center items-center">
                            <div v-if="showEditAsmForm" class="flex space-x-4">
                                <button @click.prevent="onCancleEditAsm"
                                    class="text-gray-600 bg-red-300 hover:bg-red-400 hover:text-white focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    ยกเลิก
                                </button>
                                <button type="button" @click.prevent="onSubmitEditAsm()"
                                    class="text-white bg-green-500 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    บันทึก
                                </button>
                            </div>
                            <div v-else-if="$page.props.isCourseAdmin">
                                <button type="button" @click.prevent="showEditAsmForm=true"
                                    class="text-white bg-violet-600 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    <span class="flex justify-center items-center space-x-2">
                                        <Icon icon="heroicons:pencil-square" class="w-5 h-5 mx-1" />แก้ไข
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-2">
                <div v-if="$page.props.isCourseAdmin">
                    <!-- Component: Icon accordion -->
                    <!-- <div> -->
                    <section class="w-full divide-y rounded divide-slate-200">
                    <details class="p-4 group" v-for="(group, index) in $page.props.groups.data" :key="index">
                        <summary class="[&::-webkit-details-marker]:hidden relative flex gap-4 pr-8  rounded-lg font-medium list-none cursor-pointer text-slate-700 focus-visible:outline-none group-hover:text-slate-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 stroke-emerald-500  shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-labelledby="title-ac05 desc-ac05">
                                <title id="title-ac05">Leading icon</title>
                                <desc id="desc-ac05">Icon that describes the summary</desc>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8.25V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18V8.25m-18 0V6a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 6v2.25m-18 0h18M5.25 6h.008v.008H5.25V6zM7.5 6h.008v.008H7.5V6zm2.25 0h.008v.008H9.75V6z" />
                            </svg>
                            {{ group.name }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-0 w-4 h-4 transition duration-300 top-1 stroke-slate-700 shrink-0 group-open:rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-labelledby="title-ac06 desc-ac06">
                                <title id="title-ac06">Open icon</title>
                                <desc id="desc-ac06">icon that represents the state of the summary</desc>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                        <!-- <Icon icon="lucide:chevron-down-square" class="absolute right-0 w-4 h-4 transition duration-300 top-1 stroke-slate-700 shrink-0 group-open:rotate-45" /> -->
                        </summary>
                        <div class="mt-4 text-slate-500 ">
                            <AssignmentAnswerViewer 
                                :assignment="props.assignment" 
                                :answers="props.assignment.answers.filter((answer)=>answer.course_group===group.id)" 
                            />
                        </div>
                    </details>

                    </section>
                    <!-- End Icon accordion -->
                    <!-- </div> -->
                </div>
                <div v-else>
                    <AssignmentAnswerViewer 
                        :assignment="props.assignment" 
                        :answers="props.assignment.answers" 
                    />
                </div>
            </div>

            <div class="mb-4" v-if="!$page.props.isCourseAdmin">
                <AssignmentAnswerForm :assignmentableType="props.assignmentableType"
                    :assignmentableId="props.assignmentableId" :assignment_id="props.assignment.id"
                    @add-new-answer="(newAnswer) => onAddNewAnswerHandler(newAnswer)" />
            </div>

        </div>
    </div>
</template>
