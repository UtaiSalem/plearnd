<script setup>
import { ref } from 'vue';
import { Icon } from '@iconify/vue';
import { Link } from '@inertiajs/vue3';

import DotsDropdownMenu from '@/PlearndComponents/accessories/DotsDropdownMenu.vue';
import Swal from 'sweetalert2';
import LoadingPage from '@/PlearndComponents/accessories/LoadingPage.vue';
// import Textarea from 'primevue/textarea';

const props = defineProps({
    lesson: Object,
    isCourseAdmin: Boolean,
});

const isLoadingPage = ref(false);
const isDeletingImage = ref(false);

function handleBackLink() {
    isLoadingPage.value = true;
    // usePage().$inertia.visit('/courses/'+usePage().props.course.data.id);
    window.history.back();
    // route('course.feeds', 1);
}

const handleEditLesson = () => {
    isLoadingPage.value = true;
    window.location.href = '/courses/' + props.lesson.course.id + '/lessons/' + props.lesson.id + '/edit';
}

function onDeleteLesson() {
    Swal.fire({
        title: 'ลบบทเรียน',
        text: "ยืนยันการลบบทเรียนอย่างถาวร",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#7c3aed',
        cancelButtonColor: '#f87171',
        confirmButtonText: 'ยืนยันการลบ'
    }).then(async (result) => {
        if (result.isConfirmed) {
            let delResp = await axios.delete(`/courses/${props.lesson.course.id}/lessons/${props.lesson.id}`);
            if (delResp.status === 200) {
                // props.lessons.splice(index, 1);
                // usePage().$inertia.visit('/courses/'+props.lesson.course.id+'/lessons');
                // window.history.back();
                window.location.href = '/courses/' + props.lesson.course.id + '/lessons';
            }
        }
    })
}

async function deleteImage(index, imgId) {
    Swal.fire({
        title: 'ลบรูปภาพของบทเรียน',
        text: "ยืนยันการลบรูปภาพของบทเรียนอย่างถาวร",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#7c3aed',
        cancelButtonColor: '#f87171',
        confirmButtonText: 'ยืนยันการลบ'
    }).then(async (result) => {
        if (result.isConfirmed) {
            isDeletingImage.value = true;
            let delResp = await axios.delete(`/lessons/${props.lesson.id}/images/${imgId}`);
            if (delResp.status === 200) {
                props.lesson.images.splice(index, 1);
            }
            isDeletingImage.value = false;
        }
    })
}

</script>
<template>
    <div class="">

        <LoadingPage v-if="isLoadingPage" />

        <button @click="handleBackLink"
            class="inline-flex items-center border bg-white border-indigo-300 px-3 py-1.5 rounded-md text-indigo-500 hover:bg-indigo-50">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18">
                </path>
            </svg>
            <span class="ml-1 text-lg font-bold">Back</span>
        </button>

        <div class="relative overflow-hidden shadow-lg  my-4 bg-white rounded-lg ">
            <div class="p-2">

                <div class="border-b">
                    <div class="absolute top-1 right-2 " v-if="lesson.status == 1 || $page.props.isCourseAdmin">
                        <DotsDropdownMenu @delete-model="onDeleteLesson" @edit-model="handleEditLesson">
                            <template #editModel>
                                <div>
                                    แก้ไข
                                </div>
                            </template>
                            <template #deleteModel>
                                <div>
                                    ลบบทเรียน
                                </div>
                            </template>
                        </DotsDropdownMenu>
                    </div>
                    <div class="flex  m-0.5">
                        <Link href="#">
                        <img :src="lesson.creater.avatar"
                            class="w-12 h-12 p-[3px] rounded-full ring-1 ring-blue-600 dark:ring-gray-500" </Link>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">
                                <a href="#" class="hover:underline">{{ lesson.creater.name }}</a>
                            </p>
                            <div class="flex space-x-1 text-sm text-gray-500">
                                <time datetime="2020-03-16">{{ lesson.created_at }}{{ ' @' +
                                    lesson.created_at_for_humans
                                    }}</time>
                            </div>
                            <div class="flex space-x-1 text-sm text-indigo-500">
                                <span>{{ 'เวลาอ่าน ' + lesson.min_read + ' นาที' }}</span>
                            </div>

                        </div>
                    </div>
                </div>

                <div class=" mt-4">
                    <p>หัวข้อ/เรื่อง</p>
                </div>
                <div class="p-3 mt-2 bg-gray-100 rounded-md">
                    {{ lesson.title }}
                </div>

                <div v-if="lesson.description" class=" mt-4">
                    <p>คำอธิบาย</p>
                </div>
                <div v-if="lesson.description" class="p-3 border-2 border-gray-200 rounded-md ">
                    <p class="">
                        {{ lesson.description }}
                    </p>
                </div>

                <div class="mt-4">
                    <p>เนื้อหา</p>
                </div>
                <div class="p-3  border-2 border-gray-200 rounded-md ">
                    <p class="" v-if="lesson.content">
                        {{ lesson.content }}
                    </p>
                </div>

                <div v-if="lesson.images.length > 0" class="mt-4">
                    <p>รูปภาพ</p>
                </div>

                <div v-if="lesson.images.length > 0" class="mt-2 columns-1">
                    <div v-for="(image, index) in lesson.images" :key="image.id" class="">
                        <div class="relative mb-2 overflow-hidden max-h-fit">
                            <img :src="image.full_url" class="rounded-lg border-2" />
                            <button @click.prevent="deleteImage(index, image.id)" v-if="isCourseAdmin" title="ลบรูปภาพ"
                                class="absolute top-1 right-1 w-8 h-8 flex items-center justify-center rounded-full cursor-pointer bg-gray-100 p-[6px]">
                                <Icon icon="fa-solid:trash-alt" class="w-4 h-4 text-red-500" />
                            </button>
                            <div v-if="isDeletingImage"
                                class=" absolute m-auto left-0 right-0 top-0 bottom-0 w-full h-full z-10 bg-gray-600/75 rounded-lg flex items-center justify-center">
                                <Icon icon="svg-spinners:ring-resize" class=" w-20 h-20 text-white " />
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="lesson.youtube_url" class="mt-4">
                    <p>วิดีโอ</p>
                </div>
                <div v-if="lesson.youtube_url" class=" border-2 border-gray-200 rounded-lg ">
                    <vue-plyr>
                        <div data-plyr-provider="youtube" :data-plyr-embed-id="lesson.youtube_url"></div>
                    </vue-plyr>
                </div>
            </div>
            <div class="px-6 py-3 flex flex-row items-center justify-between bg-indigo-200">

                <span href="#" class="py-1 text-xs font-regular text-gray-900 mr-1 flex flex-row items-center">
                    <svg height="13px" width="13px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512"
                        style="enable-background:new 0 0 512 512;" xml:space="preserve">
                        <g>
                            <g>
                                <path
                                    d="M256,0C114.837,0,0,114.837,0,256s114.837,256,256,256s256-114.837,256-256S397.163,0,256,0z M277.333,256 c0,11.797-9.536,21.333-21.333,21.333h-85.333c-11.797,0-21.333-9.536-21.333-21.333s9.536-21.333,21.333-21.333h64v-128 c0-11.797,9.536-21.333,21.333-21.333s21.333,9.536,21.333,21.333V256z">
                                </path>
                            </g>
                        </g>
                    </svg>
                    <span class="ml-1">{{ lesson.point_tuition_fee > 0 ? 'ค่าอ่าน ' + lesson.point_tuition_fee + ' แต้ม'
            :
            'ฟรี'
                        }}</span>
                </span>

                <span href="#" class="py-1 text-xs font-regular text-gray-900 mr-1 flex flex-row items-center">
                    <svg class="h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                        </path>
                    </svg>
                    <span class="ml-1">{{ lesson.view_count }} Views</span>
                </span>

                <span href="#" class="py-1 text-xs font-regular text-gray-900 mr-1 flex flex-row items-center">
                    <svg class="h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                        </path>
                    </svg>
                    <span class="ml-1">{{ lesson.like_count }} Likes</span>
                </span>
                <span href="#" class="py-1 text-xs font-regular text-gray-900 mr-1 flex flex-row items-center">
                    <svg class="h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                        </path>
                    </svg>
                    <span class="ml-1">{{ lesson.comment_count }} Comments</span>
                </span>
            </div>
        </div>
    </div>
</template>
