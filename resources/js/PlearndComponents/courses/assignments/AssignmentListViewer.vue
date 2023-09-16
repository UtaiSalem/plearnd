<script setup>
import { ref, reactive } from 'vue';
import { Icon } from '@iconify/vue';
import { usePage } from '@inertiajs/vue3';
// import { useObjectUrl } from '@vueuse/core';

import AssignmentImagesViewer from '@/PlearndComponents/courses/assignments/AssignmentImagesViewer.vue'
import DotsDropdownMenu from '@/PlearndComponents/accessories/DotsDropdownMenu.vue';
import AssignmentAnswerViewer from './answers/AssignmentAnswerViewer.vue';
import AssignmentAnswerForm from './AssignmentAnswerForm.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    assignmentableType: String,
    assignmentableId: Number,
    assignmentNameTh: String,
    assignmentApiRoute: String,
    assignments: Object,
});
// const rtAssignments = reactive(props.assignments);

function handleDeleteAssignment(idx, asmId){
    Swal.fire({
        title: 'แน่ใจหรือไม่?',
        text: "ที่จะลบหัวข้อนี้อย่างถาวร!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'ยกเลิก',
        confirmButtonText: 'ใช่, ยืนยัน!'
        }).then(async (result) => {
        if (result.isConfirmed) {
            await axios.delete(`/assignments/${asmId}`);
            props.assignments.splice(idx, 1);
            Swal.fire('ลบเสร็จสมบูรณ์!','ลบหัวข้อบทเรียนแล้ว.','success' )
        }
    });
}


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
function onAddNewAnswerHandler(newAnswer,images) {
    console.log(newAnswer);
    console.log(images);
}

function onDeleteTempImagesHandler(index) {
    tempImagesFile.splice(index, 1);
    tempImagesUrl.splice(index, 1);
}

function onCancleEditAsm() {
    tempImagesFile.splice(0);
    tempImagesUrl.splice(0);
    addAsmImagesInput.value.files = [];
    showEditAsmForm.value = false;
}
 
async function onSubmitEditAsm(asm,i) {
    try {
        const config = { headers: { 'Content-Type': 'multipart/form-data' } };
        let asmUpdateForm = new FormData();
        asmUpdateForm.append('title', asm.title);
        asmUpdateForm.append('points', asm.points);

        Array.from(tempImagesFile).forEach((image)=>{
        asmUpdateForm.append('images[]', image);
        });

        asmUpdateForm.append('_method', 'put');
        let asmResp = await axios.post(`${props.assignmentableId}/assignments/${asm.id}`, asmUpdateForm, config);
        if (asmResp.status===200) {
            // emit('update-assignment', asmResp.data.assignment);
            props.assignments[i] = asmResp.data.assignment;
            // tempImagesFile.splice(0);
            onCancleEditAsm();
            Swal.fire(
                'เรียบร้อย',
                'เพิ่มแบบฝึกหัดใหม่เสร็จสมบูรณ์',
                'success'
            )
        }  
        // showCreateNewAssignmentForm.value = false;
    } catch (err) {
        console.error(err);
    }
}
</script>
<template>
    <div class="">
        <div class="flex items-center justify-center bg-white border-t-4 border-blue-600 rounded-lg overflow-hidden shadow-lg">
            <div class="tabs flex flex-col justify-center p-4">
                <div class="tabs-header px-4 w-full flex flex-col items-center justify-center">
                    <div class="text-xl font-medium">
                        แบบฝึกหัด/ภาระงาน ( {{ props.assignments.length }} ) ข้อ
                    </div>
                    <div class="text-base font-normal" v-if="props.assignments.length<1">
                        (ไม่มีแบบฝึกหัด/ภาระงาน)
                    </div>
                </div>
            </div>
        </div>
        <div v-for="(asm, i) in props.assignments" :key="asm.id" class="my-2">
            <div class="relative bg-white border-t-4 border-blue-600 rounded-lg overflow-hidden shadow-lg">
                <div class="absolute top-1 right-2 overflow-visible" v-if="$page.props.isCourseAdmin">
                    <DotsDropdownMenu @delete-model="handleDeleteAssignment(i, asm.id)">
                        <template #delete-description>
                            <div>
                                ลบแบบฝึกหัด
                            </div>
                        </template>
                    </DotsDropdownMenu>
                </div>
                <div class="tabs flex flex-col justify-center pt-4">
                    <div class=" p-4 w-full border-b-2 border-blue-400">
                        <div class="text-xl font-medium">
                            <span class="text-gray-800">
                                แบบฝึกหัด/ภาระงานที่ {{ i+1 }} 
                            </span> <br />
                            <span class="text-red-600">{{ asm.points }}  คะแนน</span>
                            <span class="m-2 text-sm">( ส่งคำตอบแล้ว {{ asm.answers.length }} คำตอบ )</span>
                        </div>
                        <div class="my-2">
                            <form :id="`comment-form-${asm.id}`" v-if="$page.props.isCourseAdmin && showEditAsmForm" class="mt-3 relative dark:border-secondary-800 flex flex-col space-y-2 items-center w-full">
                                <input type="text" v-model="asm.title" :id="`comment-form-comment-input-${asm.id}`" class="text-sm bg-slate-100 w-full dark:text-gray-600 rounded-lg focus:ring-0 border-gray-400">
                                <div class="flex justify-start">
                                    <label for="assignment-point-input" class=" mr-4 mt-1 block text-lg font-medium text-gray-900 dark:text-white">คะแนน</label>
                                    <button @click.prevent="asm.points<=0? 0: asm.points--;" class="border-violet-500 hover:bg-violet-600 active:bg-violet-700 dark:border-violet-400 flex items-center justify-center rounded-l-xl border-2 p-2 text-xl  transition duration-200 hover:cursor-pointer dark:bg-white/5 dark:text-white dark:hover:bg-white/10 dark:active:bg-white/20">
                                        <Icon icon="heroicons-solid:minus-sm" class="hover:text-white hover:scale-150 dark:text-white" />
                                    </button>
                                    <input type="text" name="" disabled v-model="asm.points" id="assignment-point-input" class="w-10 border-x-0 border-y-2 border-violet-500">
                                    <button @click.prevent="asm.points++" class="border-violet-500 hover:bg-violet-600 active:bg-violet-700 dark:border-violet-400 flex items-center justify-center rounded-r-xl border-2 p-2 text-xl  transition duration-200 hover:cursor-pointer dark:bg-white/5 dark:text-white dark:hover:bg-white/10 dark:active:bg-white/20">
                                        <Icon icon="heroicons-solid:plus-sm" class=" hover:text-white hover:scale-150 dark:text-white" />
                                    </button>
                                </div>
                            </form>
                            <!-- <input type="text" v-model="asm.title" v-if="$page.props.isCourseAdmin && showEditAsmForm"> -->
                            <div v-else class="flex flex-col space-y-2">
                                <p class=" bg-slate-100 rounded-lg px-4 py-2 border my-1">{{ asm.title }}</p>
                                <!-- <p class="">
                                    <span class="m-2">คะแนน</span>
                                    <span class="bg-slate-100 rounded-lg px-4 py-2 border max-w-fit">{{ asm.points }}</span>
                                </p> -->
                            </div>
                        </div>
                        <div v-if="asm.images.length>0" class="my-2">
                            <AssignmentImagesViewer 
                                :model_id="assignmentableId" 
                                :edit="$page.props.isCourseAdmin && showEditAsmForm" 
                                :images="asm.images" 
                            />
                        </div>
                        <div v-if="tempImagesUrl.length>0">
                            <div v-for="(image,index) in tempImagesUrl" :key="index" class="">
                                <div class="relative mb-2 max-h-fit overflow-hidden">
                                    <img :src="image" class="rounded-lg"/>
                                    <button @click.prevent="onDeleteTempImagesHandler(index)" v-if="showEditAsmForm"
                                    class="absolute top-1 right-1 rounded-full cursor-pointer bg-gray-100 p-[6px]">
                                        <Icon icon="fa-solid:trash-alt" class="w-4 h-4 text-red-500" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2">
                            <div class="quick-post-footer flex items-center" :class="showEditAsmForm? 'justify-between':'justify-end'">
                                <div v-if="$page.props.isCourseAdmin && showEditAsmForm">
                                    <input type="file" accept="image/*" multiple ref="addAsmImagesInput" id="addAsmImagesInput" class="hidden" @change="onAddAsmImageInputChange">
                                    <button class="" id="" @click.prevent="browseAddAsmImages">
                                        <svg class="icon-20 text-violet-700" width="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M21.9999 14.7024V16.0859C21.9999 16.3155 21.9899 16.5471 21.9699 16.7767C21.6893 19.9357 19.4949 22 16.3286 22H7.67126C6.06806 22 4.71535 21.4797 3.74341 20.5363C3.36265 20.1864 3.042 19.7753 2.7915 19.3041C3.12217 18.9021 3.49291 18.462 3.85363 18.0208C4.46485 17.289 5.05603 16.5661 5.42677 16.0959C5.97788 15.4142 7.43078 13.6196 9.44481 14.4617C9.85563 14.6322 10.2164 14.8728 10.547 15.0833C11.3586 15.6247 11.6993 15.7851 12.2705 15.4743C12.9017 15.1335 13.3125 14.4617 13.7434 13.76C13.9739 13.388 14.2043 13.0281 14.4548 12.6972C15.547 11.2736 17.2304 10.8926 18.6332 11.7348C19.3346 12.1559 19.9358 12.6872 20.4969 13.2276C20.6172 13.3479 20.7374 13.4592 20.8476 13.5695C20.9979 13.7198 21.4989 14.2211 21.9999 14.7024Z" fill="currentColor"></path>
                                            <path opacity="0.4" d="M16.3387 2H7.67134C4.27455 2 2 4.37607 2 7.91411V16.086C2 17.3181 2.28056 18.4119 2.79158 19.3042C3.12224 18.9022 3.49299 18.4621 3.85371 18.0199C4.46493 17.2891 5.05611 16.5662 5.42685 16.096C5.97796 15.4143 7.43086 13.6197 9.44489 14.4618C9.85571 14.6323 10.2164 14.8729 10.5471 15.0834C11.3587 15.6248 11.6994 15.7852 12.2705 15.4734C12.9018 15.1336 13.3126 14.4618 13.7435 13.759C13.9739 13.3881 14.2044 13.0282 14.4549 12.6973C15.5471 11.2737 17.2305 10.8927 18.6333 11.7349C19.3347 12.1559 19.9359 12.6873 20.497 13.2277C20.6172 13.348 20.7375 13.4593 20.8477 13.5696C20.998 13.7189 21.499 14.2202 22 14.7025V7.91411C22 4.37607 19.7255 2 16.3387 2Z" fill="currentColor"></path>
                                            <path d="M11.4543 8.79668C11.4543 10.2053 10.2809 11.3783 8.87313 11.3783C7.46632 11.3783 6.29297 10.2053 6.29297 8.79668C6.29297 7.38909 7.46632 6.21509 8.87313 6.21509C10.2809 6.21509 11.4543 7.38909 11.4543 8.79668Z" fill="currentColor"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="  rounded-b flex justify-center items-center">
                                    <div v-if="showEditAsmForm" class="flex space-x-4">
                                        <button @click.prevent="onCancleEditAsm" class="text-gray-600 bg-red-300 hover:bg-red-400 hover:text-white focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                            ยกเลิก
                                        </button>
                                        <button type="button" @click.prevent="onSubmitEditAsm(asm,i)"  class="text-white bg-green-500 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                            บันทึก
                                        </button>
                                    </div>
                                    <div v-else-if="$page.props.isCourseAdmin" >
                                        <button type="button" @click.prevent="showEditAsmForm=true" class="text-white bg-violet-600 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
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
                        <AssignmentAnswerViewer 
                            :assignment="asm" 
                            :answers="asm.answers" 
                        />
                    </div>

                    <div class="mb-4">
                        <AssignmentAnswerForm
                            :assignmentableType="props.assignmentableType"
                            :assignmentableId="props.assignmentableId" 
                            :assignment_id="asm.id"
                            @add-new-answer="(newAnswer) => asm.answers.push(newAnswer)"
                        />
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>