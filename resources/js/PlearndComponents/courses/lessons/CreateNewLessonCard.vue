<template>
    <div class="w-full bg-white rounded-lg">
        <form >
            <div v-if="showCreateNewLessonForm" class="text-base  px-2 py-3 border-b-2 border-blue-400">
                เพิ่มบทเรียนในรายวิชา
            </div>
            <div v-if="showCreateNewLessonForm">
                <div class="p-3">
                    <div class="flex flex-col space-y-3">
                        <!-- Component: Simple Success Alert -->
                        <div v-if="showSuccess" class="w-full px-4 py-3 text-sm border rounded border-emerald-100 bg-emerald-50 text-emerald-500" role="alert">
                            <p>Success! You have installed Tailwind CSS</p>
                        </div>
                        <!-- End Simple Success Alert -->
                        <div class="">
                            <label for="product-name" class="text-sm font-medium text-gray-900 block mb-1">ชื่อบทเรียน</label>
                            <input type="text" v-model="lessonForm.title" required
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-violet-600 focus:border-violet-600 block w-full p-2.5">
                        </div>
                        <div class="">
                            <label for="product-name" class="text-sm font-medium text-gray-900 block mb-1">คำอธิบาย</label>    
                            <textarea v-model="lessonForm.description"
                                class="bg-gray-50 overflow-hidden border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-violet-600 focus:border-violet-600 block w-full p-4"
                                required
                            ></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t-2 border-gray-200 rounded-b flex justify-center items-center">
                <div v-if="showCreateNewLessonForm" class="flex space-x-4">
                    <button @click.prevent="showCreateNewLessonForm=false" class="text-gray-600 bg-red-300 hover:bg-red-400 hover:text-white focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        ยกเลิก
                    </button>
                    <button type="button" @click.prevent="onCreateNewLessonHandler" class="text-white bg-violet-600 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        บันทึก
                    </button>
                </div>
                <div v-else >
                    <button type="button" @click.prevent="showCreateNewLessonForm=true" class="text-white bg-violet-600 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        เพิ่มบทเรียนใหม่
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';
// import { useTimeout } from '@vueuse/core';

// const { ready, start } = useTimeout(1800, { controls: true })
const emit = defineEmits(['add-new-lesson']);

const showCreateNewLessonForm = ref(false);
const showSuccess = ref(false);
const lessonForm = ref({
    title: 'บทที่ '+ usePage().props.lessons.length ,
    description: 'คำอธิบายรายวิชา'
});
async function onCreateNewLessonHandler(){
    try {
        let lessonResp = await axios.post(`/courses/${usePage().props.course.id}/lessons`, lessonForm.value);
        if (lessonResp.data.success) {
            showSuccess.value =true;
            setTimeout(() => {
                lessonForm.value.title = '';
                lessonForm.value.description = '';               
                showSuccess.value = false;
                usePage().props.lessons.push(lessonResp.data.newLesson);
                showCreateNewLessonForm.value = false;
            }, 2000);
        }
    } catch (error) {
        console.log(error);
    }
}
</script>

<style lang="scss" scoped>

</style>