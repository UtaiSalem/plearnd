<script setup>
import { ref, reactive} from 'vue'
import { Icon } from '@iconify/vue';
import Swal from 'sweetalert2';

// import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    questionApiRoute: String,
});

const emit = defineEmits(['add-new-question']);

const showCreateNewQuestionForm = ref(false);
const questionInputActive = ref(false);

const questionInput = ref({
    text: '',
    points: 5
});

const isLoading = ref(false);
const inputQuestionImages =ref(null);
const tempImagesFile = reactive([]);
const tempImagesUrl = reactive([]);

const browseInputQuestionImages = () => inputQuestionImages.value.click();

async function addNewQuestion(){
    isLoading.value = true;
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
            emit('add-new-question', newQResp.data.question);
        }
        questionInput.value.text='';
        questionInput.value.points=0;
        tempImagesFile.splice(0);
        tempImagesUrl.splice(0);
        showCreateNewQuestionForm.value = false;
    }
    isLoading.value = false;
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
    showCreateNewQuestionForm.value = false;
}

function deleteTempImages(index){
    tempImagesFile.splice(index,1);
    tempImagesUrl.splice(index,1);
}

</script>

<template>
    <div class="w-full ">
        <!-- <hr class="border-b-2 border-blue-500" /> -->
        <div class="bg-white overflow-hidden mx-4 " v-if="$page.props.isCourseAdmin">

            <div class="" v-if="$page.props.isCourseAdmin">
                <form class="space-y-2" v-if="showCreateNewQuestionForm">
                    <!-- Component: Rounded base size basic textarea -->
                    <div class="relative mt-4">
                        <textarea id="id-01" type="text" name="id-01" v-model="questionInput.text"
                            placeholder="Write your message" rows="3"
                            class="relative w-full px-4 py-2 text-sm placeholder-transparent transition-all border rounded outline-none focus-visible:outline-none peer border-slate-200 text-slate-500 autofill:bg-white invalid:border-pink-500 invalid:text-pink-500 focus:border-emerald-500 focus:outline-none invalid:focus:border-pink-500 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"></textarea>
                        <label for="id-01"
                            class="cursor-text peer-focus:cursor-default absolute left-2 -top-2 z-[1] px-2 text-xs text-slate-400 transition-all before:absolute before:top-0 before:left-0 before:z-[-1] before:block before:h-full before:w-full before:bg-white before:transition-all peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-sm peer-required:after:text-pink-500 peer-required:after:content-['\00a0*'] peer-invalid:text-pink-500 peer-focus:-top-2 peer-focus:text-xs peer-focus:text-emerald-500 peer-invalid:peer-focus:text-pink-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400 peer-disabled:before:bg-transparent">
                            คำถาม
                        </label>
                    </div>
                    <!-- End Rounded base size basic textarea -->
                    <div v-if="tempImagesUrl.length>0">
                        <div v-for="(image,index) in tempImagesUrl" :key="index" class="">
                            <div class="relative mb-2 max-h-fit overflow-hidden">
                                <img :src="image" class="rounded-lg" />
                                <button @click.prevent="deleteTempImages(index)"
                                    v-if="$page.props.isCourseAdmin"
                                    class="absolute top-1 right-1 rounded-full cursor-pointer bg-gray-100 p-[6px]">
                                    <Icon icon="fa-solid:trash-alt" class="w-4 h-4 text-red-500" />
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between h-10 w-full">
                        <div class="flex justify-start items-center">
                            <label for="assignment-point-input"
                                class=" mr-4 mt-1 block text-lg font-medium text-gray-900 dark:text-white">คะแนน</label>
                            <button @click.prevent="questionInput.points<=0? 0: questionInput.points--;"
                                class="border-violet-500 hover:bg-violet-600 active:bg-violet-700 dark:border-violet-400 flex items-center justify-center rounded-l-xl border-2 p-2 text-xl  transition duration-200 hover:cursor-pointer dark:bg-white/5 dark:text-white dark:hover:bg-white/10 dark:active:bg-white/20">
                                <Icon icon="heroicons-solid:minus-sm"
                                    class="hover:text-white hover:scale-150 dark:text-white" />
                            </button>
                            <input type="number" min="0" name="" v-model="questionInput.points"
                                id="assignment-point-input"
                                class="w-20 h-10 text-center border-x-0 border-y-2 border-violet-500">
                            <button @click.prevent="questionInput.points++"
                                class="border-violet-500 hover:bg-violet-600 active:bg-violet-700 dark:border-violet-400 flex items-center justify-center rounded-r-xl border-2 p-2 text-xl  transition duration-200 hover:cursor-pointer dark:bg-white/5 dark:text-white dark:hover:bg-white/10 dark:active:bg-white/20">
                                <Icon icon="heroicons-solid:plus-sm"
                                    class=" hover:text-white hover:scale-150 dark:text-white" />
                            </button>
                        </div>
                        <div class=" ml-4" data-title="Insert Photo">
                            <input type="file" accept="image/*" multiple ref="inputQuestionImages"
                                class="hidden" @change="onInputImageChange">
                            <Icon icon="heroicons:photo"
                                class="w-9 h-9 hover:scale-110 bg-blue-200 hover:bg-blue-300 rounded-full p-2"
                                @click.prevent="browseInputQuestionImages" />
                        </div>
                    </div>
                </form>
            </div>

            <div class="w-full md:w-1/2 md:mx-auto flex justify-center items-center py-4 "
                v-if="$page.props.isCourseAdmin">
                <button v-if="!showCreateNewQuestionForm" type="button" @click.prevent="showCreateNewQuestionForm = true"
                    class="flex justify-center items-center text-white bg-green-500 hover:bg-green-600 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 p-2 text-center">
                    <!-- <i class="pi pi-plus-circle mx-2 text-lg font-semibold"></i> -->
                    เพิ่มคำถาม
                </button>
                <button v-if="showCreateNewQuestionForm" type="button" @click.prevent="onCancleHandler"
                    class="px-4 py-1.5 mr-2 flex justify-center items-center border-2 border-red-500 hover:bg-red-300 focus:ring-blue-500 focus:ring-offset-blue-200 text-red-500 hover:text-white transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-full ">
                    <!-- <span> <i class="pi pi-times-circle mx-2 font-semibold"></i> </span> -->
                    ยกเลิก
                </button>
                <button v-if="showCreateNewQuestionForm && (questionInput.text.trim() !=='')" type="button"
                    @click.prevent="addNewQuestion"
                    class="px-4 py-2 flex justify-center items-center  bg-green-400 hover:bg-green-500 focus:ring-green-400 focus:ring-offset-green-200 text-white transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-full ">
                    <!-- <span> <i class="pi pi-save mx-2 font-semibold"></i> </span> -->
                    <svg v-if="isLoading" width="20" height="20" fill="currentColor" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                            </path>
                        </svg>
                    บันทึก
                </button>
            </div>

        </div>
    </div>
</template>
