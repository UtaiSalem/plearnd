<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { Icon } from '@iconify/vue';
import Button from 'primevue/button';
import QuestionItem from './QuestionItem.vue'
import Swal from 'sweetalert2';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    questionableType: String,
    questionableId: Number,
    questionNameTh: String,
    questionApiRoute: String,
    questions: Object
});

const questionInputActive = ref(false);

const questionInput = ref({
    text: '',
    points: 0
});
const inputQuestionImages =ref(null);
const tempImagesFile = reactive([]);
const tempImagesUrl = reactive([]);

const browseInputQuestionImages = () => inputQuestionImages.value.click();

async function addNewQuestion(){
    questionInputActive.value = !questionInputActive.value;
    if(questionInput.value.text.trim().length > 0 || tempImagesFile.length>0){

        const config = { headers: { 'Content-Type': 'multipart/form-data' } };
        let newQstForm = new FormData();
        newQstForm.append('text', questionInput.value.text);
        newQstForm.append('points', questionInput.value.points);

        Array.from(tempImagesFile).forEach((image)=>{
        newQstForm.append('images[]', image);
        });

        let newQResp = await axios.post(`${props.questionApiRoute}/questions`, newQstForm, config);
        if (newQResp.status===200) {
            props.questions.push(newQResp.data.question);
            Swal.fire('บันทึกสำเร็จ','เพิ่มคำถามเสร็จเรียบร้อย.','success' );
        }
        questionInput.value.text='';
        questionInput.value.points=0;
        tempImagesFile.splice(0);
        tempImagesUrl.splice(0);

    }
}

async function deleteQuestion(qID,qIdx){
    await axios.delete(`${props.questionApiRoute}/questions/${qID}`);
    props.questions.splice(qIdx, 1);
}

function onInputImageChange(e){
    Array.from(e.target.files).forEach(image => {
        tempImagesFile.push(image);
        tempImagesUrl.push(URL.createObjectURL(image));
    });
}
function onCancleHandler(e){
    inputQuestionImages.files = [];
    questionInput.value.text = '';
    questionInput.value.points = 0;
    tempImagesFile.splice(0);
    tempImagesUrl.splice(0);
    questionInputActive.value = false;
}

function deleteTempImages(index){
    tempImagesFile.splice(index,1);
    tempImagesUrl.splice(index,1);
}

</script>

<template>
    <div class="w-full">
        <div class="flex items-center justify-center bg-white border-t-4 border-blue-600 rounded-lg overflow-hidden shadow-lg">
            <div class="tabs flex flex-col justify-center py-4">
                <div class="tabs-header px-2 w-full flex flex-col items-center justify-center">
                    <div class="text-xl font-medium">
                        คำถาม/แบบทดสอบ ( {{ props.questions.length }} ) ข้อ
                    </div>
                    <div class="text-base font-normal" v-if="!props.questions.length">
                        (ไม่มีคำถาม/แบบทดสอบ)
                    </div>
                    <div v-else class="w-full">
                        <div v-for="(question,idx) in props.questions" :key="question.id" class="w-full border rounded-md p-1 my-2">
                            <QuestionItem
                                :question="question"
                                :indexNumber="idx"
                                :isCourseOwner="$page.props.isCourseAdmin"
                                @delete-question="deleteQuestion(question.id, idx)"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border-t-4 border-blue-600 rounded-lg overflow-hidden shadow-lg my-4 px-2" v-if="$page.props.isCourseAdmin">
            <!-- <div class="flex flex-col items-between my-2 space-y-2 " v-if="questionInputActive"> -->
            <!-- <div class="flex items-center"> -->
                <!-- <label for="inline-input-label-with-helper-text" class="text-sm font-medium dark:text-white w-[36px]">
                    ข้อ {{ props.question.data.length + 1 }}
                </label> -->
                <!-- <span class="w-[48px]">ข้อ {{ props.questions.length + 1 }}</span> -->
                <!-- <input type="text" v-model="questionInput.text" id="inline-input-label-with-helper-text" class="py-2 border-gray-200 w-full rounded-full text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400" aria-describedby="hs-inline-input-helper-text"> -->
                <!-- <input type="text" v-model="questionInput.text" class="py-2 border-gray-200 w-full rounded-full text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400" > -->
            <!-- </div> -->

            <!-- <div class="flex items-center space-x-4">
                <span>คะแนนเมื่อตอบถูก</span>
                <div class="flex items-center justify-center">
                    <button type="button" @click="decrement" class="flex items-center justify-center mx-[2px] border-2 border-red-400 hover:bg-red-400 focus:ring-blue-500 focus:ring-offset-blue-200 text-red-600 hover:text-white transition ease-in duration-200 text-center text-base font-semibold shadow-md rounded-full" >
                        <i class="pi pi-minus-circle p-[8px] font-bold"></i>
                    </button>
                    <span class="mx-1 border-2 border-blue-400 px-3 py-1 rounded-lg text-blue-500">{{ questionInput.points }}</span>
                    <button type="button" @click="questionInput.points++" class="flex items-center justify-center mx-[2px] border-2 border-green-400 hover:bg-green-400   text-green-600 hover:text-white transition ease-in duration-200 text-center text-base font-semibold shadow-md rounded-full" >
                        <span><i class="pi pi-plus-circle p-[10px] font-bold"></i></span>
                    </button>
                </div>
            </div> -->
            <!-- </div> -->


            <div class="" v-if="$page.props.isCourseAdmin">
                <form class="space-y-2" v-if="questionInputActive">
                    <!-- Component: Rounded base size basic textarea -->
                    <div class="relative mt-4">
                        <textarea id="id-01" type="text" name="id-01" v-model="questionInput.text" placeholder="Write your message" rows="3" class="relative w-full px-4 py-2 text-sm placeholder-transparent transition-all border rounded outline-none focus-visible:outline-none peer border-slate-200 text-slate-500 autofill:bg-white invalid:border-pink-500 invalid:text-pink-500 focus:border-emerald-500 focus:outline-none invalid:focus:border-pink-500 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"></textarea>
                        <label for="id-01" class="cursor-text peer-focus:cursor-default absolute left-2 -top-2 z-[1] px-2 text-xs text-slate-400 transition-all before:absolute before:top-0 before:left-0 before:z-[-1] before:block before:h-full before:w-full before:bg-white before:transition-all peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-sm peer-required:after:text-pink-500 peer-required:after:content-['\00a0*'] peer-invalid:text-pink-500 peer-focus:-top-2 peer-focus:text-xs peer-focus:text-emerald-500 peer-invalid:peer-focus:text-pink-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400 peer-disabled:before:bg-transparent">
                           คำถาม
                        </label>
                    </div>
                    <!-- End Rounded base size basic textarea -->
                    <div v-if="tempImagesUrl.length>0">
                        <div v-for="(image,index) in tempImagesUrl" :key="index" class="">
                            <div class="relative mb-2 max-h-fit overflow-hidden">
                                <img :src="image" class="rounded-lg"/>
                                <button @click.prevent="deleteTempImages(index)" v-if="$page.props.isCourseAdmin"
                                class="absolute top-1 right-1 rounded-full cursor-pointer bg-gray-100 p-[6px]">
                                    <Icon icon="fa-solid:trash-alt" class="w-4 h-4 text-red-500" />
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between h-10 w-full">
                        <div class="flex justify-start">
                            <label for="assignment-point-input" class=" mr-4 mt-1 block text-lg font-medium text-gray-900 dark:text-white">คะแนน</label>
                            <button @click.prevent="questionInput.points<=0? 0: questionInput.points--;" class="border-violet-500 hover:bg-violet-600 active:bg-violet-700 dark:border-violet-400 flex items-center justify-center rounded-l-xl border-2 p-2 text-xl  transition duration-200 hover:cursor-pointer dark:bg-white/5 dark:text-white dark:hover:bg-white/10 dark:active:bg-white/20">
                                <Icon icon="heroicons-solid:minus-sm" class="hover:text-white hover:scale-150 dark:text-white" />
                            </button>
                            <input type="text" name="" disabled v-model="questionInput.points" id="assignment-point-input" class="w-10 border-x-0 border-y-2 border-violet-500">
                            <button @click.prevent="questionInput.points++" class="border-violet-500 hover:bg-violet-600 active:bg-violet-700 dark:border-violet-400 flex items-center justify-center rounded-r-xl border-2 p-2 text-xl  transition duration-200 hover:cursor-pointer dark:bg-white/5 dark:text-white dark:hover:bg-white/10 dark:active:bg-white/20">
                                <Icon icon="heroicons-solid:plus-sm" class=" hover:text-white hover:scale-150 dark:text-white" />
                            </button>
                        </div>
                        <div class=" ml-4" data-title="Insert Photo" >
                            <input type="file" accept="image/*" multiple ref="inputQuestionImages" class="hidden" @change="onInputImageChange">
                            <Icon icon="heroicons:photo" class="w-9 h-9 hover:scale-110 bg-blue-200 hover:bg-blue-300 rounded-full p-2" @click.prevent="browseInputQuestionImages" />
                        </div>
                    </div>
                </form>
            </div>

            <div class="w-full flex justify-center items-center py-4" v-if="$page.props.isCourseAdmin">
                <!-- <button v-if="!questionInputActive" type="button" @click.prevent="addNewQuestion" class="w-[25%] py-2 flex justify-center items-center  bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 focus:ring-offset-blue-200 text-white transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-full "> -->
                <button v-if="!questionInputActive" type="button" @click.prevent="addNewQuestion" class="flex justify-center items-center text-white bg-violet-600 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 p-2 text-center">
                    <i class="pi pi-plus-circle mx-2 text-lg font-semibold"></i>
                    เพิ่มคำถาม
                </button>
                <button  v-if="questionInputActive" type="button" @click.prevent="onCancleHandler" class="px-4 py-1.5 mr-2 flex justify-center items-center border-2 border-red-500 hover:bg-red-300 focus:ring-blue-500 focus:ring-offset-blue-200 text-red-500 hover:text-white transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-full ">
                    <span> <i class="pi pi-times-circle mx-2 font-semibold"></i> </span>
                    ยกเลิก
                </button>
                <button  v-if="questionInputActive && (questionInput.text.trim() !=='')" type="button" @click.prevent="addNewQuestion" class="px-4 py-2 flex justify-center items-center  bg-green-400 hover:bg-green-500 focus:ring-green-400 focus:ring-offset-green-200 text-white transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-full ">
                    <span> <i class="pi pi-save mx-2 font-semibold"></i> </span>
                    บันทึก
                </button>
            </div>
        </div>
    </div>
</template>
