<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head, router, usePage, Link } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';
import Swal from 'sweetalert2';

import MainLayout from "@/Layouts/MainLayout.vue";
import StaggeredFade from '@/PlearndComponents/accessories/StaggeredFade.vue';
import GroupedMemberList from '@/PlearndComponents/learn/courses/members/GroupedMemberList.vue'
import NonGroupedMemberList from '@/PlearndComponents/learn/courses/members/NonGroupedMemberList.vue'
import CourseProfileCover from '@/PlearndComponents/learn/courses/CourseProfileCover.vue';
import CourseLessonsList from '@/PlearndComponents/learn/courses/lessons/CourseLessonsList.vue';
import CreateNewLesson from '@/PlearndComponents/learn/courses/lessons/CreateNewLesson.vue';
import CreateNewAssignmentCard from '@/PlearndComponents/learn/courses/assignments/CreateNewAssignmentCard.vue';
import AssignmentListViewer from '@/PlearndComponents/learn/courses/assignments/AssignmentListViewer.vue'; 
import QuizzesListViewer from '@/PlearndComponents/learn/courses/quizzes/QuizzesListViewer.vue';  
import CreateNewCourseQuize from '@/PlearndComponents/learn/courses/quizzes/CreateNewCourseQuize.vue';  
import CourseGroupList from '@/PlearndComponents/learn/courses/groups/CourseGroupList.vue';
import CourseAttendance from '@/PlearndComponents/learn/courses/attendances/CourseAttendance.vue';
import CourseSettings from '@/PlearndComponents/learn/courses/CourseSettings.vue';
import CourseMemberSetting from '@/PlearndComponents/learn/courses/members/CourseMemberSetting.vue';

const props = defineProps({
    course: Object,
    lessons: Object,
    assignments: Object,
    quizzes: Object,
    groups: Object,
    members: Object,
    isCourseAdmin: Boolean,
    courseMemberOfAuth: Object,
});


const isDarkMode = ref(false);
const isMobile = ref(false);
const activeTab = ref(1);
const activeGroupTab = ref(0);


const nonGroupedMembers = computed(() => {
    return props.members.data.filter((member) => !member.group);
});

const unAcceptedMembers = computed(() => {
    return props.members.data.filter((member) => member.status == 0);
});

const unAcceptedCourseMembers = computed(() => {
    return props.members.data.filter((member) => member.course_member_status == 0);
});

const compCover = computed(() => {
    return props.course.data.cover ? '/storage/images/courses/covers/'+props.course.data.cover:'/storage/images/courses/covers/default_cover.jpg';
})

onMounted(() => { 
    if ('ontouchstart' in window) { isMobile.value = true; };
    // if (window.matchMedia('(prefers-color-scheme: dark)').matches) { isDarkMode.value = true; };
    activeTab.value = props.courseMemberOfAuth.last_accessed_tab;
});

const tempLogo = ref(props.course.data.user.avatar);
const tempCover = ref(compCover);
const tempHeader = ref(props.course.data.name);
const tempSubheader = ref(props.course.data.code);

const setActiveTab = async (tab) => {
    activeTab.value = tab;
    await axios.post(`/courses/${props.course.data.id}/members/${props.courseMemberOfAuth.id}/set-active-tab`, {tab: tab});
};
// const setActiveGroupTab = computed(()=> props.groups.data.find());

function pushNewLesson(data){
    usePage().props.course.data.lessons.push(data);
}

async function onCoverImageChange(coverFile) {
    const config = { headers: { 'content-type': 'multipart/form-data' } };
    const courseCoverUpdate = new FormData();
    courseCoverUpdate.append('cover', coverFile);
    courseCoverUpdate.append('_method', 'patch');
    const resp = await axios.post(`/courses/${props.course.data.id}`, courseCoverUpdate, config );
    usePage().props.course.data.cover = resp.data.course.cover;
}

async function onHeaderChange(courseName) {
    let resp = await axios.patch(`/courses/${props.course.data.id}`, { name: courseName });
    usePage().props.course.data.name = resp.data.course.name;
}

async function onSubheaderChange(courseCode) {
    let resp = await axios.patch(`/courses/${props.course.data.id}`, { code: courseCode });
    usePage().props.course.data.code = resp.data.course.code;
}

async function onRequestToBeAMember(grpId,grpIdx){
    try {
        let memberResp = await axios.post(`/courses/${props.course.data.id}/members`, {group_id: grpId});
        if (memberResp.data.success) {
            
            if (memberResp.data.newCourseMember.course_member_status == 1){
                props.course.data.isMember=true;
                props.course.data.enrolled_students++;
                props.groups.data[grpIdx].members.push(memberResp.data.newCourseMember);
            }
            
            usePage().props.courseMemberOfAuth = memberResp.data.newCourseMember;
            // usePage().props.course.data.course_member_status = memberResp.data.newCourseMember.course_member_status;
            
            Swal.fire(
                'เสร็จสิ้น',
                'ขอเป็นสมาชิกเรียบร้อยแล้ว',
                'success'
            );
        }
    } catch (error) {
        console.log(error);
    }
}

async function onRequestToBeUnMember(courseMemberId, memberIndx=null){
    try {
        let unmemberResp = await axios.delete(`/courses/${props.course.data.id}/members/${courseMemberId}`);
        if (unmemberResp.data && unmemberResp.data.success) {
            usePage().props.courseMemberOfAuth = null;
            usePage().props.course.data.member_status = null;
            usePage().props.course.data.isMember = false;
            Swal.fire(
                'สำเร็จ',
                'ยกเลิกขอเป็นสมาชิกเรียบร้อยแล้ว',
                'success'
            )
        }
        if (memberIndx) {
            props.groups.members.data.splice(memberIndx,1);
        }
    } catch (error) {
        console.log(error);
    }
}

function onAddNewAssignmentHandler(newAsm){
    props.assignments.data.push(newAsm);
}

function handleAddNewQuiz(newQuiz){
    props.quizzes.data.push(newQuiz);
}

async function onUpdateCourseHandler(courseData){
    try {
        const config = { headers: { 'Content-Type': 'multipart/form-data' } };
        let courseUpdateForm = new FormData();
        courseUpdateForm.append('name', courseData.name);
        courseUpdateForm.append('code', courseData.code);
        courseUpdateForm.append('description', courseData.description);
        courseUpdateForm.append('category', courseData.category);
        courseUpdateForm.append('level', courseData.level);   
        courseUpdateForm.append('credit_units', courseData.credit_units);
        courseUpdateForm.append('hours_per_week', courseData.hours_per_week);
        courseUpdateForm.append('start_date', courseData.start_date);
        courseUpdateForm.append('end_date', courseData.end_date);
        courseUpdateForm.append('auto_accept_members', courseData.auto_accept_members);
        courseUpdateForm.append('tuition_fees', courseData.tuition_fees);
        courseUpdateForm.append('saleable', courseData.saleable);
        courseUpdateForm.append('price', courseData.price);
        courseUpdateForm.append('status', courseData.status);
        
        courseData.cover ? courseUpdateForm.append('cover', courseData.cover): null;
        courseUpdateForm.append('_method', 'put');
        let resultResp = await axios.put(`/courses/${props.course.data.id}`, courseUpdateForm ,config);

        if (resultResp.data && resultResp.data.success) {
            // console.log(resultResp.data);
            router.reload({ only: ['course']});
        }
    } catch (error) {
        console.log(error);
    }

    // console.log(usePage().props.course.data);
}

</script>

<template>
    <Head title="รายวิชา" />

    <MainLayout :title="props.course.data.name">

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
                    :groups="props.groups.data"
                    :courseMemberOfAuth="props.courseMemberOfAuth"

                    @cover-image-change="(data) => { onCoverImageChange(data); }"
                    @logo-image-change="(data) => { onLogoImageChange(data); }"
                    @header-change="(data)=>{ onHeaderChange(data) }"
                    @subheader-change="(data)=> { onSubheaderChange(data) }"
                    @request-tobe-member="(groupId,groupIndex)=> { onRequestToBeAMember(groupId,groupIndex) }"
                    @request-tobe-unmember="(crsMbrId)=> { onRequestToBeUnMember(crsMbrId) }"
            ></CourseProfileCover>
            <div class=" bg-white shadow-xl w-full rounded-lg overflow-hidden mt-4">
                <div class="flex flex-row justify-around">
                    <!-- <Link :href="`/courses/users/${$page.props.auth.user.id}`" as="button" type="button" @click.prevent="setActiveTab(5)" -->
                    <button type="button" @click.prevent="setActiveTab(1)"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        :class="{'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 1 }">
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="icon-park-outline:view-grid-detail" class="w-6 md:w-8 h-6 md:h-8"
                                :class="{'text-cyan-500': activeTab === 1}" />
                            <span class="hidden md:block"
                                :class="{'text-cyan-500': activeTab === 1}">บทเรียน</span>
                        </div>
                    </button>
                    <!-- <Link :href="`/courses/users/${$page.props.auth.user.id}/member`" -->
                    <button type="button" @click.prevent="setActiveTab(2)"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        :class="{'border-b-4 border-cyan-500 bg-cyan-100': activeTab===2}">
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="material-symbols:assignment-add-outline" class="w-6 md:w-8 h-6 md:h-8"
                                :class="{'text-cyan-500': activeTab===2}" />
                            <span class="hidden md:block"
                                :class="{'text-cyan-500': activeTab===2}">แบบฝึกหัด</span>
                        </div>
                    </button>
                    <!-- <Link :href="`/courses`" -->
                    <button type="button" @click.prevent="setActiveTab(3)"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        :class="{'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 3}">
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="healthicons:i-exam-qualification-outline" class="w-6 md:w-8 h-6 md:h-8"
                                :class="{'text-cyan-500': activeTab === 3}" />
                            <span class="hidden md:block" :class="{'text-cyan-500': activeTab === 3}">แบบทดสอบ</span>
                        </div>
                    </button>

                    <button type="button" @click.prevent="setActiveTab(4)"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        :class="{'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 4}">
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="ph:users-four" class="w-6 md:w-8 h-6 md:h-8"
                                :class="{'text-cyan-500': activeTab === 4}" />
                            <span class="hidden md:block" :class="{'text-cyan-500': activeTab === 4}">สมาชิก</span>
                        </div>
                    </button>

                    <button type="button" @click.prevent="setActiveTab(5)"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        :class="{'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 5}">
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="heroicons-outline:user-group" class="w-6 md:w-8 h-6 md:h-8"
                                :class="{'text-cyan-500': activeTab === 5}" />
                            <span class="hidden md:block" :class="{'text-cyan-500': activeTab === 5}">กลุ่ม</span>
                        </div>
                    </button>

                    <button v-if="props.isCourseAdmin" type="button" @click.prevent="setActiveTab(6)"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        :class="{'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 6}">
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="lets-icons:group-add-light" class="w-6 md:w-8 h-6 md:h-8"
                                :class="{'text-cyan-500': activeTab === 6}" />
                            <span class="hidden md:block" :class="{'text-cyan-500': activeTab === 6}">คำขอเป็นสมาชิก</span>
                        </div>
                    </button>

                    <button v-if="props.isCourseAdmin" type="button" @click.prevent="setActiveTab(7)"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        :class="{'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 7}">
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="tabler:calendar-user" class="w-6 md:w-8 h-6 md:h-8"
                                :class="{'text-cyan-500': activeTab === 7}" />
                            <span class="hidden md:block" :class="{'text-cyan-500': activeTab === 7}">การเข้าเรียน</span>
                        </div>
                    </button>

                    <!-- <Link :href="`/courses/${$page.props.course.data.id}/members/${props.courseMemberOfAuth.id}/progress`"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        :class="{'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 8}">
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="mdi-light:settings" class="w-6 md:w-8 h-6 md:h-8"
                                :class="{'text-cyan-500': activeTab === 8}" />
                            <span class="hidden md:block" :class="{'text-cyan-500': activeTab === 8}">ผลการเรียน</span>
                        </div>
                    </Link> -->

                    <button type="button" @click.prevent="setActiveTab(8)"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        :class="{'border-b-4 border-cyan-500 bg-cyan-100': activeTab === 8}">
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="mdi-light:settings" class="w-6 md:w-8 h-6 md:h-8"
                                :class="{'text-cyan-500': activeTab === 8}" />
                            <span class="hidden md:block" :class="{'text-cyan-500': activeTab === 8}">การตั้งค่า</span>
                        </div>
                    </button>
                    
                    <!-- <Link :href="`/course/${$page.props.course.data.id}/attendaces`"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        >
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="mdi:graph-box-outline" class="w-6 md:w-8 h-6 md:h-8"
                                 />
                            <span class="hidden md:block">ผลการเรียน</span>
                        </div>
                    </Link> -->

                    <!-- <Link :href="route('course.member.show',[props.course.data.id, courseMemberOfAuth.id])" 
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        >
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="mdi:graph-box-outline" class="w-6 md:w-8 h-6 md:h-8"
                                 />
                            <span class="hidden md:block">ผลการเรียน</span>
                        </div>
                    </Link> -->
                    
                </div>
            </div>
        </template>

        <template #leftSideWidget>
            <div class="space-y-4">
                <!-- <CourseCardWidget :course="$page.props.course.data"/>

                <CourseGroupProfileWidget /> -->
            </div>
        </template>

        <template #mainContent>

            <div v-if="activeTab===1" class="mt-4">
                <div v-if="!props.lessons.data.length"  class="text-base text-gray-600 bg-white border-t-4 border-blue-500 rounded-lg p-4 my-4 shadow-lg ">
                    <div  class="text-center">
                            <p>ยังไม่มีบทเรียนในรายวิชานี้</p>
                    </div>
                </div>

                <CourseLessonsList :lessons="props.lessons.data"  />
                
                <CreateNewLesson @add-new-lesson="(newLesson) => props.lessons.data.push(newLesson)" v-if="props.isCourseAdmin" />

            </div>
            <div v-if="activeTab===2" class=" mt-4">
                <AssignmentListViewer
                    :assignmentableType="'courses'"
                    :assignmentableId="props.course.data.id"
                    :assignmentNameTh="'รายวิชา'"
                    :assignmentApiRoute="`/courses/${props.course.data.id}`"
                    v-model:assignments="props.assignments.data"
                />
                <CreateNewAssignmentCard v-if="props.isCourseAdmin"
                    :assignmentableType="'courses'"
                    :assignmentableId="props.course.data.id"
                    :assignmentNameTh="'รายวิชา'"
                    :assignmentApiRoute="`/courses/${props.course.data.id}`"
                    @add-new-assignment="(newAsm) => props.assignments.data.push(newAsm)"
                />
            </div>
            <div v-if="activeTab===3" class=" mt-4">
                <QuizzesListViewer 
                    :quizzes="props.quizzes.data"
                    :quizzesApiRoute="`/courses/${props.course.data.id}`"
                />
                <CreateNewCourseQuize 
                    :course="props.course.data"
                    @add-new-quiz="(newQuiz) => handleAddNewQuiz(newQuiz)"
                />
                
            </div>

            <div v-if="activeTab===4" class=" mt-4">
                <staggered-fade :duration="50" tag="ul" class="flex flex-col w-full ">
                    <GroupedMemberList 
                        :groups="props.groups.data"
                    />
                    <!-- <GroupedMemberList 
                        :groups="usePage().props.groups.data.find((group)=> group.id === usePage().props.courseMemberOfAuth.group_id)"
                    /> -->
                </staggered-fade>
            </div>

            <div v-if="activeTab===5" class=" mt-4">
                <CourseGroupList 
                    :course="props.course.data" 
                    :groups='props.groups.data' 
                    :courseMemberOfAuth="props.courseMemberOfAuth"

                    @add-new-group="(group)=> props.groups.data.push(group)"
                    @delete-group="(index) => props.groups.data.splice(index,1)"
                />

            </div>

            <div v-if="activeTab===6 && props.isCourseAdmin" class=" mt-4">
                <staggered-fade :duration="50" tag="ul" class="flex flex-col w-full ">
                    <NonGroupedMemberList
                        :members="unAcceptedMembers"
                        :groups="props.groups.data"
                    />
                </staggered-fade>
            </div>

            <div v-if="activeTab===7 && props.isCourseAdmin" class=" mt-4">
                <CourseAttendance 
                    :groups="props.groups.data"
                />
            </div>

            <div v-if="activeTab===8" class=" mt-4">
                <div v-if="props.isCourseAdmin">
                    <CourseSettings 
                        :course="props.course.data" 
                        :isCourseAdmin="props.isCourseAdmin"

                        @update-course="(formData)=> onUpdateCourseHandler(formData)"
                    />
                </div>
                <div v-else>
                    <!-- <CourseMemberSetting 
                        :member_info="props.courseMemberOfAuth"
                    /> -->
                    <CourseMemberSetting 
                        :member_info="usePage().props.members.data.find((member)=> member.id === usePage().props.courseMemberOfAuth.id)"
                    />
                </div>
            </div>

        </template>

        <!-- <template #rightSideWidget>
            <div>
                <CourseCardWidget :course="$page.props.course.data"/>
            </div>
        </template> -->
    </MainLayout>
</template>
