<script setup>
import { usePage } from '@inertiajs/vue3';

// const props = defineProps({
//     course: {
//         type: Object
//     },
// });
const emit = defineEmits(['course-data-change']);
const course = usePage().props.course;

</script>
<template>
    <div class="bg-white rounded-lg ">
        <div class="header">
            <h3 class="text-xl font-semibold px-2 py-3 border-b-2 border-blue-400">
                ข้อมูลทั่วไปเกี่ยวกับรายวิชา
            </h3>
        </div>
        <form action="">
            <div class="p-3">
                <div class="flex flex-col space-y-3">
                    <div class="">
                        <label for="product-name" class="text-sm font-medium text-gray-900 block mb-2">ชื่อวิชา</label>
                        <input type="text" name="product-name"  id="product-name" v-model="course.name" @change="emit('course-data-change', course)" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" placeholder="Apple Imac 27”" required="">
                    </div>
                    <div class="">
                        <label for="product-name" class="text-sm font-medium text-gray-900 block mb-2">รหัสวิชา</label>
                        <input type="text" name="product-name" id="product-name" v-model="course.code" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" placeholder="Apple Imac 27”" required="">
                    </div>
                    <div class="">
                        <label for="product-details" class="text-sm font-medium text-gray-900 block mb-2">คำอธิบายรายวิชา</label>
                        <textarea id="product-details" rows="4" v-model="course.description" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-4" placeholder="Details"></textarea>
                    </div>
                </div>
            </div>
            <div class="p-4 border-t border-gray-200 rounded-b flex justify-end">
                <button class="text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="submit">Save all</button>
            </div>
        </form>
    </div>
</template>
