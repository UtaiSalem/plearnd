<script setup>
import { ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3'
import MainLayout from "@/Layouts/MainLayout.vue";
import Navbar from '@/PlearndComponents/Navbar.vue';
import xCourseProfileCover from '@/PlearndComponents/courses/xCourseProfileCover.vue';
import LessonBasicInfoCard from '@/PlearndComponents/courses/lessons/LessonBasicInfoCard.vue';
import LessonTopicsList from '@/PlearndComponents/courses/lessons/topics/LessonTopicsList.vue';
import CreateNewTopicCard from '@/PlearndComponents/courses/lessons/topics/CreateNewTopicCard.vue';
import CreateNewAssignmentCard from '@/PlearndComponents/courses/assignments/CreateNewAssignmentCard.vue';
import AssignmentListViewer from '@/PlearndComponents/courses/assignments/AssignmentListViewer.vue';
import QuestionsListViewer from '@/PlearndComponents/courses/questions/QuestionsListViewer.vue';

// const topics = usePage().topics;
const props = defineProps({
    isCourseAdmin: Boolean,
    academy: Object,
    course: Object,
    lesson: Object,
    topics: Object,
    assignments: Object,
    questions: Object,
    imagePath: String,
});

const isDarkMode = ref(false);
const tempLogo = ref(props.course.data.user.avatar || props.imagePath + 'storage/images/logos/default_logo.png');
const tempCover = ref(props.imagePath + 'storage/images/covers/default_cover.png');
const tempHeader = ref(props.course.data.name);
const tempSubheader = ref(props.course.data.code);

function onAddNewAssignmentHandler(newAsm){
    props.assignments.data.push(newAsm);
}
</script>
<template>
    <div>
        <MainLayout>           
            <template #header>
                <Head title="Create new lesson"></Head>
            </template>

            <template #navbar>
                <Navbar :isDarkMode="isDarkMode" @toggle-dark-mode="isDarkMode = !isDarkMode" />
            </template>

            <template #coverProfileCard>
                <xCourseProfileCover 
                    :logo="tempLogo"  
                    :cover="tempCover"  
                    :name="tempHeader"
                    :code="tempSubheader"
                />
            </template>

            <template #leftSideWidget>
                <div>
                    <!-- <ActivityCardVue /> -->
                </div>
            </template>

            <template #mainContent>
                <div class="w-full space-y-4">
                    <LessonBasicInfoCard 
                        v-model:title="props.lesson.data.title"
                        v-model:description="props.lesson.data.description"
                        :lesson="props.lesson.data"
                    />

                    <LessonTopicsList :topics="props.topics.data" />

                    <CreateNewTopicCard />

                    <AssignmentListViewer
                        :assignmentableType="'lessons'"
                        :assignmentableId="props.lesson.data.id"
                        :assignmentNameTh="'บทเรียน'"
                        :assignmentApiRoute="`/lessons/${props.lesson.data.id}`"
                        v-model:assignments="props.assignments.data"
                    />

                    <CreateNewAssignmentCard v-if="$page.props.isCourseAdmin"
                        :assignmentableType="'lessons'"
                        :assignmentableId="props.lesson.data.id"
                        :assignmentNameTh="'บทเรียน'"
                        :assignmentApiRoute="`/lessons/${props.lesson.data.id}`"
                        @add-new-assignment="(newAsm) => { onAddNewAssignmentHandler(newAsm) }"
                    />

                    <QuestionsListViewer 
                        :questionableType="'lessons'"
                        :questionableId="props.lesson.data.id"
                        :questionNameTh="'หัวข้อ'"
                        :questionApiRoute="`/lessons/${props.lesson.data.id}`"
                        v-model:questions="props.questions.data"
                    />

                </div>
            </template>

            <template #rightSideWidget>
                <div>
                    <!-- <MeassageCardVue /> -->
                </div>
            </template>
        </MainLayout>       
    </div>
</template>

