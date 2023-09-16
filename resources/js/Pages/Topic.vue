<script setup>
import { ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3'
import MainLayout from "@/Layouts/MainLayout.vue";
import Navbar from '@/PlearndComponents/Navbar.vue';
import xCourseProfileCover from '@/PlearndComponents/courses/xCourseProfileCover.vue';
import TopicBasicInfoCard from '@/PlearndComponents/courses/lessons/topics/TopicBasicInfoCard.vue';
import CreateNewAssignmentCard from '@/PlearndComponents/courses/assignments/CreateNewAssignmentCard.vue'
import AssignmentListViewer from '@/PlearndComponents/courses/assignments/AssignmentListViewer.vue'
// import TopicsListFeed from '@/PlearndComponents/widgets/TopicsListFeed.vue';
import QuestionsListViewer from '@/PlearndComponents/courses/questions/QuestionsListViewer.vue';

const props = defineProps({
    isCourseAdmin: Boolean,
    academy: Object,
    course: Object,
    lesson: Object,
    topic: Object,
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
    usePage().props.assignments.data.push(newAsm);
}

</script>
<template>
    <div>
        <MainLayout>           
            <template #header>
                <Head title="Lesson's topic"></Head>
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

                    <TopicBasicInfoCard 
                        v-model:title="props.topic.data.title"
                        v-model:content="props.topic.data.content"
                        v-model:images="props.topic.data.images"                       
                    />

                    <AssignmentListViewer
                        :assignmentableType="'topics'"
                        :assignmentableId="props.topic.data.id"
                        :assignmentNameTh="'หัวข้อ'"
                        :assignmentApiRoute="`/topics/${props.topic.data.id}`"
                        v-model:assignments="props.assignments.data"
                    />

                    <CreateNewAssignmentCard v-if="$page.props.isCourseAdmin"
                        :assignmentableType="'topics'"
                        :assignmentableId="props.topic.data.id"
                        :assignmentNameTh="'หัวข้อ'"
                        :assignmentApiRoute="`/topics/${props.topic.data.id}`"
                        @add-new-assignment="(newAsm) => { onAddNewAssignmentHandler(newAsm) }"
                    />
                    <!-- <LessonTopicsList /> -->

                    <!-- <CreateNewTopicCard /> -->

                    <!-- <TopicsListFeed /> -->
                    <QuestionsListViewer 
                        :questionableType="'topics'"
                        :questionableId="props.topic.data.id"
                        :questionNameTh="'หัวข้อ'"
                        :questionApiRoute="`/topics/${props.topic.data.id}`"
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

