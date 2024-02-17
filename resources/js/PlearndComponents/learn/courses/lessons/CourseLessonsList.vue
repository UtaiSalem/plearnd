<script setup>
import { usePage,Link } from '@inertiajs/vue3';

import DotsDropdownMenu from '@/PlearndComponents/accessories/DotsDropdownMenu.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    lessons: Object,
});

const emit = defineEmits(['create-new-lesson']);

function onDeleteLesson(lessonId, index){
    Swal.fire({
        title: 'ลบบทเรียน',
        text: "ยืนยันการลบบทเรียนอย่างถาวร",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#7c3aed',
        cancelButtonColor: '#f87171',
        confirmButtonText: 'ยืนยันการลบ'
    }).then( async (result) => {
        if (result.isConfirmed) {
            let delResp = await axios.delete(`/lessons/${lessonId}`);
            if (delResp.status===200) {
                props.lessons.splice(index, 1);
            }
        }
    })
}


</script>
<template>
    <div class="">
        <div class=" border-t-[3px] border-blue-500 p-3 my-4 text-xl shadow-lg bg-white rounded-lg flex justify-between items-center">
            <span>
                บทเรียนในรายวิชา {{ props.lessons.length }} บทเรียน
            </span>
            <span v-if="usePage().props.isCourseAdmin">
                <!-- <CreateNewLesson @add-new-lesson="(newLesson)=> props.lessons.push(newLesson)" /> -->
            </span>
        </div>

        <div v-for="(lesson, index) in props.lessons" :key="lesson.id" >
            <div class="relative p-3 my-4 bg-white rounded-lg " v-if="lesson.status ==1 || $page.props.isCourseAdmin">
                <div class="absolute top-1 right-4 " v-if="$page.props.isCourseAdmin">
                    <dots-dropdown-menu @delete-model="onDeleteLesson(lesson.id, index)">
                        <template #delete-description>
                            <div>
                                ลบบทเรียน
                            </div>
                        </template>
                    </dots-dropdown-menu>
                </div>
                <div class="p-3 m-2 mt-6 bg-gray-100 rounded-md">
                    <Link :href="`/courses/${lesson.course.id}/lessons/${lesson.id}`" class=" underline">
                        {{ lesson.title }}
                    </Link>
                </div>
                <div class="p-3 m-2 border-2 border-gray-200 rounded-md ">
                    <p class="" v-if="lesson.description">
                        {{ lesson.description.length > 100 ? lesson.description.substring(0, 100) + '...' : lesson.description }}
                    </p>
                </div>
                <div class="p-3 m-2 border-2 border-gray-200 rounded-md ">
                    <p class="" v-if="lesson.description">
                        {{ lesson.content.length > 150 ? lesson.content.substring(0, 150) + '...' : lesson.description }}
                    </p>
                </div>
                <div v-if="lesson.images.length>1" class="relative overflow-hidden ">
                    <img :src="'/storage/images/courses/lessons/'+ lesson.images[1].image_url" alt="" class="rounded-lg brightness-50">
                    <a href="" class="absolute top-[50%] left-[50%] text-white text-3xl w-24">+ {{ lesson.images.length-1 }}</a>
                </div>
                <div class="flex justify-end">
                    <Link :href="`/courses/${lesson.course.id}/lessons/${lesson.id}`" class="mr-4 underline">
                        ดูรายละเอียด>>
                    </Link>
                </div>

            </div>
        </div>
    </div>
</template>
