<script setup>
import { ref } from 'vue';
import { Link,router } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';

const props = defineProps({
    courses: Object,
});


const isLoading = ref(false);


</script>
<template>
    <div>
        <ul class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <li v-for="(course,i) in props.courses" :key="course.id" class="mb-4">
                <Link :href="'/courses/'+course.id">
                    <div class="max-w-2xl sm:max-w-sm md:max-w-sm lg:max-w-sm xl:max-w-sm sm:mx-auto md:mx-auto lg:mx-auto xl:mx-auto bg-white shadow-xl rounded-lg text-gray-900 hover:scale-105">
                        <div class="rounded-t-lg h-24 overflow-hidden">
                            <img class="object-cover object-top w-full" src='https://images.unsplash.com/photo-1549880338-65ddcdfd017b?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=400&fit=max&ixid=eyJhcHBfaWQiOjE0NTg5fQ' alt='Mountain'>
                        </div>
                        <div class="mx-auto w-24 h-24 -mt-16 border-4 border-white rounded-full overflow-hidden">
                            <img class="object-cover object-center h-24" :src="course.user.avatar" alt='Woman looking front'>
                        </div>
                        <div class="text-center mt-2">
                            <h2 class="text-violet-950 font-semibold">{{ course.code }}</h2>
                            <p class="text-violet-950 text-base font-bold">{{ course.name }}</p>
                            <p class="text-violet-950 text-sm font-bold">ผู้สอน:: {{ course.user.name }}</p>
                        </div>
                        <ul class="py-2 mt-1 text-violet-800 flex items-center justify-around">
                            <!-- <li class="flex flex-col items-center justify-around">
                                <svg class="w-4 fill-current text-blue-900" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path
                                        d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                </svg>
                                <div>2k</div>
                            </li> -->
                            <li class="flex flex-col items-center justify-between">
                                <svg class="w-4 fill-current text-blue-900" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path
                                        d="M7 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0 1c2.15 0 4.2.4 6.1 1.09L12 16h-1.25L10 20H4l-.75-4H2L.9 10.09A17.93 17.93 0 0 1 7 9zm8.31.17c1.32.18 2.59.48 3.8.92L18 16h-1.25L16 20h-3.96l.37-2h1.25l1.65-8.83zM13 0a4 4 0 1 1-1.33 7.76 5.96 5.96 0 0 0 0-7.52C12.1.1 12.53 0 13 0z" />
                                </svg>
                                <div>{{ course.enrolled_students }}</div>
                            </li>
                            <li class="flex flex-col items-center justify-around">
                                <svg class="w-4 fill-current text-blue-900" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path
                                        d="M9 12H1v6a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-6h-8v2H9v-2zm0-1H0V5c0-1.1.9-2 2-2h4V2a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1h4a2 2 0 0 1 2 2v6h-9V9H9v2zm3-8V2H8v1h4z" />
                                </svg>
                                <div>{{ course.lessons_count }}</div>
                            </li>
                        </ul>
                        <!-- <div class="p-2 border-t mx-4 mt-1" v-if="!course.isMember" >
                            <button 
                                class=" flex items-center text-sm max-w-fit mx-auto rounded-full bg-violet-700 hover:shadow-lg text-white px-4 py-1">
                                <span>
                                    เข้าร่วม
                                </span>
                            </button>
                        </div> -->
                        <!-- <div class="p-2 border-t mx-4 mt-1" v-if="course.isMember" > -->
                            <!-- <Link :href="route('courses.show', course.id)" class="text-sm max-w-fit block mx-auto rounded-full bg-violet-700 hover:shadow-lg text-white px-4 py-1">เข้าเรียน</Link> -->
                            <!-- <Link :href="'/courses/'+ course.id" class="text-sm max-w-fit block mx-auto rounded-full bg-violet-700 hover:shadow-lg text-white px-4 py-1">เข้าเรียน</Link> -->
                        <!-- </div> -->
                    </div>
                </Link>
            </li>
        </ul>
    </div>
</template>
