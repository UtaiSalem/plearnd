<script setup>
import { ref,reactive } from 'vue';
import { Icon } from '@iconify/vue';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
    title: { type: String, default: null },
    description: { type: String, default:null},
    lesson: Object
});

const lessonImages = reactive(props.lesson.images);
const inputImages = ref(null);
const tempImagesFile = reactive([]);
const tempImagesUrl = reactive([]);
const courseId = usePage().props.course.data.id;
const lessonId = usePage().props.lesson.data.id;
const browseInputImages = () => { inputImages.value.click() };
const isLoading = ref();

const showEditForm = ref(false);

const emit = defineEmits([
    'update:title', 
    'update:description', 
]);

function resizeTextarea(e) {
    emit('update:description', e.target.value)
    e.target.style.height = "auto";
    e.target.style.height = `${e.target.scrollHeight}px`;
}
async function handelOnUpdateLesson(){

    const config = { headers: { 'Content-Type': 'multipart/form-data' } };
    let lessonUpdateForm = new FormData();
    lessonUpdateForm.append('title', props.title);
    lessonUpdateForm.append('description', props.description);

    Array.from(tempImagesFile).forEach((image)=>{
        lessonUpdateForm.append('images[]', image);
    });
    lessonUpdateForm.append('_method', 'put');

    const lessonResp = await axios.post(`/courses/${courseId}/lessons/${lessonId}`, lessonUpdateForm , config); 
    if (lessonResp.status===200) {

        lessonResp.data.images.forEach(el => {
            props.lesson.images.push(el);
        });

        tempImagesFile.splice(0);
        tempImagesUrl.splice(0);
        Swal.fire(
            'เสร็จสมบูรณ์',
            'บันทึการแก้ไขเสร็จเรียบร้อย',
            'success'
        )
    }
    showEditForm.value = false;
}
function onInputChange(e){
    Array.from(e.target.files).forEach(image => {
        tempImagesFile.push(image);
        tempImagesUrl.push(URL.createObjectURL(image));
    });
}
function onCancleHandler(){
    tempImagesFile.splice(0);
    tempImagesUrl.splice(0);
    inputImages.files = [];
    showEditForm.value = false;
}
function deleteTempImage(index){
    tempImagesFile.splice(index,1);
    tempImagesUrl.splice(index,1);
}

async function deleteImage(index, id){
    isLoading.value = true;
    // const response = await axios.delete(`/topics/${props.topic_id}/images/${id}`);
    const response = await axios.delete(`/lessons/${props.lesson.id}/images/${id}`);
    if (response.status === 200) {
        lessonImages.splice(index,1);
    }
    isLoading.value = false;
}

</script>
<template>
    <div class="bg-white rounded-lg pb-4">
        <div class="header">
            <h3 class="text-xl font-semibold px-2 py-3 border-b-2 border-blue-400">
                ข้อมูลทั่วไปเกี่ยวกับบทเรียน
            </h3>
        </div>
        <form action="" @submit.prevent="emit('save-update')" >
            <div class="p-3">
                <div class="flex flex-col space-y-3">
                    <div class="">
                        <label for="lesson-name-input" class="text-sm font-medium text-gray-900 block mb-1">ชื่อบทเรียน</label>
                        <input type="text" id="lesson-name-input" :value="props.title" @input="emit('update:title', $event.target.value)" :disabled="!showEditForm"
                            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-violet-600 focus:border-violet-600 block w-full p-2.5" required>
                    </div>
                    <div class="">
                        <label for="lesson-description-input" class="text-sm font-medium text-gray-900 block mb-1">คำอธิบายบทเรียน</label>    
                        <textarea id="lesson-description-input"
                            :value="props.description" @input="resizeTextarea" :disabled="!showEditForm"
                            class="bg-gray-50 overflow-y-hidden border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-violet-600 focus:border-violet-600 block w-full p-4"
                        ></textarea>
                    </div>

                </div>
            </div>
            <div class="">
                <!-- <TopicPhotosViewer v-model:images="topic.images" :edit="$page.props.isCourseAdmin" :topic_id="topic.id"/> -->
                <div class="columns-1 mx-3">
                    <div v-if="lessonImages">
                        <div v-for="(image,index) in lessonImages" :key="image.image_url" class="">
                            <div class="relative mb-2 max-h-fit overflow-hidden">
                                <img :src="'../../storage/'+image.image_url" class="rounded-lg"/>
                                <button v-if="$page.props.isCourseAdmin  && showEditForm" @click.prevent="deleteImage(index,image.id)"
                                class="absolute top-1 right-1 rounded-full cursor-pointer bg-gray-100 p-2">
                                    <Icon icon="fa-solid:trash-alt" class="w-5 h-5 text-red-500" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <TopicPhotosViewer v-model:images="topic.images" :edit="$page.props.isCourseAdmin" :topic_id="topic.id"/> -->

                <!-- <TopicImagesAddNewImage v-model:images="tempImages" :edit="$page.props.isCourseAdmin" /> -->
                <div class="mx-4">
                    <div class="columns-1">
                        <div v-if="tempImagesUrl">
                            <div v-for="(image,index) in tempImagesUrl" :key="image.url" class="">
                                <div class="relative mb-2 max-h-fit overflow-hidden">
                                    <!-- <img :src="image.url" class="rounded-lg"/> -->
                                    <img :src="image" class="rounded-lg"/>
                                    <button v-if="$page.props.isCourseAdmin && showEditForm" @click.prevent="deleteTempImage(index)"
                                    class="absolute top-1 right-1 rounded-full cursor-pointer bg-gray-100 p-2">
                                        <Icon icon="fa-solid:trash-alt" class="w-5 h-5 text-red-500" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <TopicImagesAddNewImage v-model:images="tempImages" :edit="$page.props.isCourseAdmin" /> -->
            </div>
            <div class="mx-4">
                <div class="quick-post-footer flex justify-between items-center">
                    <div class="quick-post-footer-actions flex">
                        <div class="quick-post-footer-action text-tooltip-tft-medium mx-1" data-title="Insert Photo" v-if="showEditForm">
                            <input type="file" accept="image/*" multiple ref="inputImages" class="hidden" @change="onInputChange">
                            <Icon icon="heroicons:photo" class="w-9 h-9 hover:scale-110 bg-blue-200 hover:bg-blue-300 rounded-full p-2" @click.prevent="browseInputImages" />
                        </div>                                            
                    </div>

                    <div class="  rounded-b flex justify-center items-center" v-if="$page.props.isCourseAdmin">
                        <div v-if="showEditForm" class="flex space-x-4">
                            <button @click.prevent="onCancleHandler" class="text-gray-600 bg-red-300 hover:bg-red-400 hover:text-white focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                ยกเลิก
                            </button>
                            <button type="button" @click.prevent="handelOnUpdateLesson"  class="text-white bg-green-500 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                บันทึก
                            </button>
                        </div>
                        <div v-else-if="$page.props.isCourseAdmin" >
                            <button type="button" @click.prevent="showEditForm=true" class="text-white bg-violet-600 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                <span class="flex justify-center items-center space-x-2">
                                    <Icon icon="heroicons:pencil-square" class="w-5 h-5 mx-1" />แก้ไข
                                </span>
                            </button>
                        </div>
                    </div>

                </div>

            </div>

            <!-- <div class="rounded-b flex justify-center items-center" v-if="$page.props.isCourseAdmin">
                <div v-if="showEditForm" class="flex space-x-4">
                    <button @click.prevent="showEditForm=false" class="text-gray-600 bg-red-300 hover:bg-red-400 hover:text-white focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        ยกเลิก
                    </button>
                    <button type="button" @click.prevent="onSaveUpdatedLesson"  class="text-white bg-green-500 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        บันทึก
                    </button>
                </div>
                <div v-else >
                    <button type="button" @click.prevent="showEditForm=true" class="text-white bg-violet-600 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        <span class="flex justify-center items-center space-x-2">
                            <Icon icon="heroicons:pencil-square" class="w-5 h-5 mx-1" />แก้ไข
                        </span>
                    </button>
                </div>
            </div> -->
        </form>
    </div>
</template>
