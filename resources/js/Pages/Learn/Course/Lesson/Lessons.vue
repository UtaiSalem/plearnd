<script setup>
import { ref } from 'vue';
import { usePage } from "@inertiajs/vue3";

import CourseLayout from '@/Layouts/CourseLayout.vue';
import LoadingPage from '@/PlearndComponents/accessories/LoadingPage.vue';
import CourseLessonsList from '@/PlearndComponents/learn/courses/lessons/CourseLessonsList.vue';
import CreateNewLesson from '@/PlearndComponents/learn/courses/lessons/CreateNewLesson.vue';
import Groups from "../Group/Groups.vue";

const props = defineProps({
    course: Object,
    lessons: Object,
    isCourseAdmin: Boolean,
    courseMemberOfAuth: Object,

    groups: Object,
});

const isLoadingPage = ref(false);

</script>

<template>
    <div>
        <CourseLayout 
            :course 
            :isCourseAdmin
            :courseMemberOfAuth
            :activeTab="1"
        >
            <template #courseContent>

                <LoadingPage v-if="isLoadingPage" />

                <div>
                    <div v-if="!props.lessons.data.length"
                        class="p-4 my-4 text-base text-gray-600 bg-white border-t-4 border-blue-500 rounded-lg shadow-lg ">
                        <div class="text-center">
                            <p>ยังไม่มีบทเรียนในรายวิชานี้</p>
                        </div>
                    </div>

                    <CourseLessonsList :lessons="props.lessons.data" />

                    <CreateNewLesson v-if="props.isCourseAdmin"
                        @creating-new-lesson="isLoadingPage = true"
                        @add-new-lesson="(newLesson) => { props.lessons.data.push(newLesson); isLoadingPage = false; }" 
                    />
                </div>
            </template>
        </CourseLayout>
    </div>
</template>
