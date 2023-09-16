<script setup>
import { reactive, ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';
import TextInput from '@/Components/TextInput.vue'
// import TopicPhotosViewer from "./TopicPhotosViewer.vue";
// import TopicImagesAddNewImage from "./TopicImagesAddNewImage.vue";
import Swal from 'sweetalert2'

const props = defineProps({
    title: String,
    content: String,
    images: Object
});

const emit = defineEmits([
    'update:title',
    'update:content',
    'update:images',
]);

const inputImages = ref(null);
// const tempImages = reactive([]);
const tempImagesFile = reactive([]);
const tempImagesUrl = reactive([]);
const topic = ref(usePage().props.topic.data);
const topicImages = ref(props.images);
const showEditTopicForm = ref(false);
const isLoading = ref(false);

const browseInputImages = () => { inputImages.value.click() };

function onInputChange(e){
    Array.from(e.target.files).forEach(image => {
        tempImagesFile.push(image);
        tempImagesUrl.push(URL.createObjectURL(image));
        // tempImages.push({
        //     file: image,
        //     url: URL.createObjectURL(image),
        // });
    });
    // console.log(e.target.value);
}
function onCancleHandler(){
    tempImagesFile.splice(0);
    tempImagesUrl.splice(0);
    // for (let i=0; i<=tempImages.length;i++) {
    //     tempImages.splice(i,1);
    // }
    showEditTopicForm.value = false;
}

async function handleSubmit() {
    const config = { headers: { 'Content-Type': 'multipart/form-data' } };
    let topicUpdateForm = new FormData();
    topicUpdateForm.append('title', topic.value.title);
    topicUpdateForm.append('content', topic.value.content);

    // for (let image of tempImages) {
    //     topicUpdateForm.append('images[]', image.file);
    // }

    Array.from(tempImagesFile).forEach((image)=>{
        topicUpdateForm.append('images[]', image);
    });

    topicUpdateForm.append('_method', 'put');
    const topicResp = await axios.post("/topics/"+ topic.value.id, topicUpdateForm, config);
    if (topicResp.status===200) {
        // usePage().props.topic.data = topicResp.data.topic.data;
        topicResp.data.images.forEach(el => {
            topicImages.value.push(el);
            emit('update:images', topicImages.value);
        });
        console.log( topicResp.data.images);
        tempImagesFile.splice(0);
        tempImagesUrl.splice(0);
        Swal.fire(
        'เสร็จสมบูรณ์',
        'บันทึการแก้ไขเสร็จเรียบร้อย',
        'success'
    )
    }
    showEditTopicForm.value = false;
}

async function deleteImage(index, id){
    isLoading.value = true;
    // const response = await axios.delete(`/topics/${props.topic_id}/images/${id}`);
    const response = await axios.delete(`/topic_images/${id}`);
    if (response.status === 200) {
        topic.value.images.splice(index,1);
        emit('update:images', topic.value.images);
    }
    isLoading.value = false;
}

function deleteTempImage(index){
    tempImagesFile.splice(index,1);
    tempImagesUrl.splice(index,1);
}
</script>

<template>
    <div class="" >
        <Link :href="`/lessons/${$page.props.lesson.data.id}`">
            <div  class="bg-white border-t-4 border-blue-600 rounded-lg overflow-hidden shadow-lg my-4">
                <div class="m-4 text-xl font-semibold">บทเรียน</div>
                <div class="m-4 text-md font-base">{{ $page.props.lesson.data.title }}</div>
                <div class="m-4 text-sm font-normal">{{ $page.props.lesson.data.description }}</div>
            </div>
        </Link>
        <div class="rounded-lg">
            <div class="tabs flex flex-col justify-center mt-4 rounded-lg bg-white shadow-lg text-gray-700 overflow-hidden">
                <div class="mt-3">
                    <div :id="`bar-with-underline-1${topic.id}`" role="tabpanel" aria-labelledby="bar-with-underline-item-1">
                        <div class="m-4">
                            <form class="bg-white rounded-lg" action="" method="post" enctype="multipart/form-data">

                                <label for="topic_title" class="block text-lg font-medium text-gray-900 dark:text-white">ชื่อหัวข้อ</label>
                                <TextInput id="topic_title" type="text" :value="props.title" @input="emit('update:title', $event.target.value)" :disabled="!showEditTopicForm" class="mt-0 mb-2 block w-full text-start" required autofocus autocomplete="topic_title" />

                                <label for="topic_content" class="block text-lg font-medium text-gray-900 dark:text-white">เนื้อหา</label>
                                <textarea id="topic_content" :value="props.content" @input="emit('update:content', $event.target.value)" :disabled="!showEditTopicForm" rows="10" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder=""></textarea>

                                <div class="mt-4">
                                    <!-- <TopicPhotosViewer v-model:images="topic.images" :edit="$page.props.isCourseAdmin" :topic_id="topic.id"/> -->
                                    <div class="columns-1">
                                        <div v-if="topicImages">
                                            <div v-for="(image,index) in topicImages" :key="image.image_url" class="">
                                                <div class="relative mb-2 max-h-fit overflow-hidden">
                                                    <img :src="'../../storage/'+image.image_url" class="rounded-lg"/>
                                                    <button v-if="$page.props.isCourseAdmin  && showEditTopicForm" @click.prevent="deleteImage(index,image.id)"
                                                    class="absolute top-1 right-1 rounded-full cursor-pointer bg-gray-100 p-2">
                                                        <Icon icon="fa-solid:trash-alt" class="w-5 h-5 text-red-500" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <TopicPhotosViewer v-model:images="topic.images" :edit="$page.props.isCourseAdmin" :topic_id="topic.id"/> -->

                                    <!-- <TopicImagesAddNewImage v-model:images="tempImages" :edit="$page.props.isCourseAdmin" /> -->
                                    <div class="">
                                        <div class="columns-1">
                                            <div v-if="tempImagesUrl">
                                                <div v-for="(image,index) in tempImagesUrl" :key="image.url" class="">
                                                    <div class="relative mb-2 max-h-fit overflow-hidden">
                                                        <!-- <img :src="image.url" class="rounded-lg"/> -->
                                                        <img :src="image" class="rounded-lg"/>
                                                        <button v-if="$page.props.isCourseAdmin && showEditTopicForm" @click.prevent="deleteTempImage(index)"
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
                                
                                <div class="mt-2">
                                    <div class="quick-post-footer flex justify-between items-center">
                                        <div class="quick-post-footer-actions flex">
                                            <div class="quick-post-footer-action text-tooltip-tft-medium mx-1" data-title="Insert Photo" v-if="showEditTopicForm">
                                                <input type="file" accept="image/*" multiple ref="inputImages" class="hidden" @change="onInputChange">
                                                <Icon icon="heroicons:photo" class="w-9 h-9 hover:scale-110 bg-blue-200 hover:bg-blue-300 rounded-full p-2" @click.prevent="browseInputImages" />
                                            </div>                                            
                                        </div>

                                        <div class="  rounded-b flex justify-center items-center">
                                            <div v-if="showEditTopicForm" class="flex space-x-4">
                                                <button @click.prevent="onCancleHandler" class="text-gray-600 bg-red-300 hover:bg-red-400 hover:text-white focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                                    ยกเลิก
                                                </button>
                                                <button type="button" @click.prevent="handleSubmit()"  class="text-white bg-green-500 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                                    บันทึก
                                                </button>
                                            </div>
                                            <div v-else-if="$page.props.isCourseAdmin" >
                                                <button type="button" @click.prevent="showEditTopicForm=true" class="text-white bg-violet-600 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                                    <span class="flex justify-center items-center space-x-2">
                                                        <Icon icon="heroicons:pencil-square" class="w-5 h-5 mx-1" />แก้ไข
                                                    </span>
                                                </button>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
  
  