<script setup>
import { ref, onMounted } from 'vue';
import { Head, usePage } from "@inertiajs/vue3";
import Swal from 'sweetalert2';

import MainLayout from "@/Layouts/MainLayout.vue";
import Navbar from '@/PlearndComponents/Navbar.vue';
// import ProfileCover from "@/PlearndComponents/partials/ProfileCover.vue";
import CourseProfileCover from '@/PlearndComponents/courses/CourseProfileCover.vue';
import CourseBasicInFoCard from '@/PlearndComponents/courses/CourseBasicInFoCard.vue';
import CourseLessonsList from '@/PlearndComponents/courses/lessons/CourseLessonsList.vue';
import CreateNewLessonWidget from "@/PlearndComponents/widgets/CreateNewLessonWidget.vue";
import CreateNewAssignmentCard from '@/PlearndComponents/courses/assignments/CreateNewAssignmentCard.vue';
import AssignmentListViewer from '@/PlearndComponents/courses/assignments/AssignmentListViewer.vue';
import QuestionsListViewer from '@/PlearndComponents/courses/questions/QuestionsListViewer.vue';
import axios from 'axios';

const isDarkMode = ref(false);
const isMobile = ref(false);
onMounted(() => {
    if ('ontouchstart' in window) {
            isMobile.value = true;
        }
});

const props = defineProps({
    course: Object,
    lessons: Object,
    assignments: Object,
    questions: Object,
    imagePath: String,
    isCourseAdmin: Boolean
});

const tempLogo = ref(props.course.data.user.avatar || props.imagePath + 'storage/images/logos/default_logo.png');
const tempCover = ref(props.course.data.cover || props.imagePath + 'storage/images/covers/default_cover.png');
const tempHeader = ref(props.course.data.name);
const tempSubheader = ref(props.course.data.code);


function pushNewLesson(data){
    usePage().props.course.data.lessons.push(data);
    // console.log(pageData.props.course.data.lessons.length);
}

async function onCoverImageChange(coverFile) {
    const academyCoverUpdate = new FormData();
    academyCoverUpdate.append('cover', coverFile);
    academyCoverUpdate.append('_method', 'patch');
    await axios.post(`/courses/${props.course.data.academy.data.id}/update`, academyCoverUpdate);
}

async function onLogoImageChange(logoFile) {
    const academyLogoUpdate = new FormData();
    academyLogoUpdate.append('logo', logoFile);
    academyLogoUpdate.append('_method', 'patch');
    await axios.post(`/academies/${props.course.data.academy.data.id}/update`, academyLogoUpdate);
}

async function onHeaderChange(courseName) {
    await axios.patch(`/courses/${props.course.data.id}`, { name:courseName });
}

async function onSubheaderChange(academySlogan) {
    await axios.patch(`/academies/${props.course.data.academy.data.id}/update`, { slogan:academySlogan });
}

async function onRequestToBeAMember(){
    try {
        let memberResp = await axios.post(`/courses/${props.course.data.id}/members`);
        if (memberResp.data.success) {
            props.course.data.isMember=memberResp.data.isMember;
            props.course.data.enrolled_students = memberResp.data.enrolledStudents;
            if (memberResp.data.isMember) {
                Swal.fire(
                    'เสร็จสิ้น',
                    'ขอเป็นสมาชิกเรียบร้อยแล้ว',
                    'success'
                )
            }else{
                Swal.fire(
                    'เสร็จสิ้น',
                    'ออกจากการเป็นสมาชิกเรียบร้อยแล้ว',
                    'success'
                )
            }
        }
    } catch (error) {
        console.log(error);
    }
}

function onAddNewAssignmentHandler(newAsm){
    props.assignments.data.push(newAsm);
}

async function onUpdateCourseHandler(){
    try {
        const config = { headers: { 'Content-Type': 'multipart/form-data' } };
        let courseUpdateForm = new FormData();
        courseUpdateForm.append('name', props.course.data.name);
        courseUpdateForm.append('code', props.course.data.code);
        courseUpdateForm.append('description', props.course.data.description);
        courseUpdateForm.append('category', props.course.data.category);

        // Array.from(tempImagesFile).forEach((image)=>{
        //     courseUpdateForm.append('images[]', image);
        // });

        courseUpdateForm.append('_method', 'put');
      let resultResp = await axios.post(`/courses/${props.course.data.id}`, courseUpdateForm ,config);
      console.log(resultResp.data);
    } catch (error) {
        console.log(error);
    }
}


</script>

<template>
    <Head title="รายวิชา" />

    <MainLayout>
        <template #header>
                <Head title="รายวิชา"></Head>
        </template>
        <template #navbar>
            <Navbar :isDarkMode="isDarkMode" @toggle-dark-mode="isDarkMode = !isDarkMode" />
        </template>
        <template #coverProfileCard>
            <CourseProfileCover
                    :coverImage="tempCover"
                    :logoImage="tempLogo"
                    v-model:cover-header="tempHeader"
                    v-model:cover-subheader="tempSubheader"

                    :model="'Course'"
                    :modelTable="'courses'"
                    :modelableId="props.course.data.id"
                    :modelableType="'app/models/Course'"
                    :modelableRoute="`/courses/${props.course.data.id}`"
                    :modelData="props.course.data"
                    :subModelData="props.lessons.data"
                    :subModelNameTh="'บทเรียน'"

                    @cover-image-change="(data) => { onCoverImageChange(data); }"
                    @logo-image-change="(data) => { onLogoImageChange(data); }"
                    @header-change="(data)=>{ onHeaderChange(data) }"
                    @subheader-change="(data)=>{ onSubheaderChange(data) }"
                    @request-tobe-member="onRequestToBeAMember()"
            ></CourseProfileCover>
            <!-- <CourseProfileCover
                :cover="tempCover"
                :logo="tempLogo"
                :name="tempHeader"
                :code="tempSubheader"
            /> -->
        </template>

        <template #leftSideWidget>
            <div class="space-y-4">
                <!-- <CourseCardWidget :course="$page.props.course.data"/>

                <CourseGroupProfileWidget /> -->
            </div>
        </template>

        <template #mainContent>
            <!-- <QuickPostBox /> -->
            <CourseBasicInFoCard
                v-model:name="tempHeader"
                v-model:code="tempSubheader"
                v-model:description="props.course.data.description"
                v-model:category="props.course.data.category"
                @save-update="onUpdateCourseHandler()"
            />
                <!-- v-model:image="toRefProps.course.data.cover" -->
            <div class=" space-y-4">
                <div  v-if="props.lessons.data.length<0" class="text-base text-gray-600 bg-white border-t-4 border-blue-500 rounded-lg p-4 my-4 shadow-lg ">
                   <div  class="text-center">
                        <p>ยังไม่มีบทเรียนในรายวิชานี้</p>
                   </div>
                </div>

                <CourseLessonsList
                    :lessons="props.lessons.data"
                />

                <!-- <div class="xcard">
                    <div v-for="(lesson,idx) in $page.props.lessons.data" :key="idx" class="flex flex-col py-4">
                        <Accordion :activeIndex="0">
                            <AccordionTab :header="lesson.title">
                                <p>{{ lesson.description }}</p>

                                <p> {{ lesson.topics }} </p>
                            </AccordionTab>
                        </Accordion>
                        <CourseCurriculum :lesson="lesson" />
                    </div>
                </div> -->

                <!-- <LessonAccordian /> -->

                <div v-if="props.isCourseAdmin" class="text-base text-gray-600 bg-white border-t-4 border-blue-500 rounded-lg p-4 my-4 shadow-lg ">
                    <CreateNewLessonWidget />
                </div>

                <AssignmentListViewer
                    :assignmentableType="'courses'"
                    :assignmentableId="props.course.data.id"
                    :assignmentNameTh="'รายวิชา'"
                    :assignmentApiRoute="`/courses/${props.course.data.id}`"
                    v-model:assignments="props.assignments.data"
                />

                <CreateNewAssignmentCard v-if="$page.props.isCourseAdmin"
                    :assignmentableType="'courses'"
                    :assignmentableId="props.course.data.id"
                    :assignmentNameTh="'รายวิชา'"
                    :assignmentApiRoute="`/courses/${props.course.data.id}`"
                    @add-new-assignment="(newAsm) => { onAddNewAssignmentHandler(newAsm) }"
                />

                <QuestionsListViewer
                        :questionableType="'courses'"
                        :questionableId="props.course.data.id"
                        :questionNameTh="'รายวิชา'"
                        :questionApiRoute="`/courses/${props.course.data.id}`"
                        v-model:questions="props.questions.data"
                />

            </div>
            <!-- <ActivitiesFeed /> -->
        </template>

        <template #rightSideWidget>
            <div>
                <!-- <CourseCardWidget :course="$page.props.course.data"/> -->
            </div>
        </template>
    </MainLayout>
</template>
