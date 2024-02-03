<script setup>
import {  usePage } from "@inertiajs/vue3";

import CourseLayout from '@/Layouts/CourseLayout.vue';
import CourseLessonsList from '@/PlearndComponents/learn/courses/lessons/CourseLessonsList.vue';
import CreateNewLesson from '@/PlearndComponents/learn/courses/lessons/CreateNewLesson.vue';

const props = defineProps({
    course: Object,
    lessons: Object,
    lesson: Object,
    groups: Object,
    isCourseAdmin: Boolean,
});

function pushNewLesson(data){
    usePage().props.course.data.lessons.push(data);
}

</script>

<template>
    <div>
     <CourseLayout
        :course="props.course"
        :lessons="props.lessons"
        :groups="props.groups"
        :isCourseAdmin="props.isCourseAdmin"
     >
        <template #courseContent>
            <div>
                <div v-if="!props.lessons.data.length"  class="text-base text-gray-600 bg-white border-t-4 border-blue-500 rounded-lg p-4 my-4 shadow-lg ">
                    <div class="text-center">
                        <p>ยังไม่มีบทเรียนในรายวิชานี้</p>
                    </div>
                </div>

                <CourseLessonsList :lessons="props.lessons.data"  />
                
                <CreateNewLesson v-if="props.isCourseAdmin" 
                    @add-new-lesson="(newLesson) => props.lessons.data.push(newLesson)" 
                />
            </div>
        </template>
     </CourseLayout>
    </div>
</template>
