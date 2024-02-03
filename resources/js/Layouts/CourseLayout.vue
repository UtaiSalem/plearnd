<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head, Link } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';
// import Swal from 'sweetalert2';
import MainLayout from "@/Layouts/MainLayout.vue";
import CourseProfileCover from '@/PlearndComponents/learn/courses/CourseProfileCover.vue';

const props = defineProps({
    course: Object,
    lessons: Object,
    groups: Object,
    isCourseAdmin: Boolean,
    courseMemberOfAuth: Object,
});

const activeTab = ref(1);

const compCover = computed(() => {
    return props.course.data.cover ? '/storage/images/courses/covers/'+props.course.data.cover:'/storage/images/courses/covers/default_cover.jpg';
})

const tempLogo = ref(props.course.data.user.avatar);
const tempCover = ref(compCover);
const tempHeader = ref(props.course.data.name);
const tempSubheader = ref(props.course.data.code);


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
            >
            </CourseProfileCover>
            <div class=" bg-white shadow-xl w-full rounded-lg overflow-hidden mt-4">
                <div class="flex flex-row justify-around">
                    <Link :href="`/courses/${props.course.data.id}`" as="button" type="button" @click.prevent="setActiveTab(5)"
                    class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                    >
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="icon-park-outline:view-grid-detail" class="w-6 md:w-8 h-6 md:h-8"
                                :class="{'text-cyan-500': activeTab === 1}" />
                            <span class="hidden md:block"
                                :class="{'text-cyan-500': activeTab === 1}">บทเรียน</span>
                        </div>
                    </Link>
                    
                    <!-- <Link :href="`/courses/${props.course.data.id}/member`" as="button" type="button" @click.prevent="setActiveTab(2)"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        :class="{'border-b-4 border-cyan-500': activeTab === 1 }">
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="icon-park-outline:view-grid-detail" class="w-6 md:w-8 h-6 md:h-8"
                                :class="{'text-cyan-500': activeTab === 1}" />
                            <span class="hidden md:block"
                                :class="{'text-cyan-500': activeTab === 1}">บทเรียน</span>
                        </div>
                    </Link> -->
                    <!-- <Link :href="`/courses/users/${$page.props.auth.user.id}/member`" -->
                    <!-- <button type="button" @click.prevent="setActiveTab(2)"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        :class="{'border-b-4 border-cyan-500': activeTab===2}">
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="material-symbols:assignment-add-outline" class="w-6 md:w-8 h-6 md:h-8"
                                :class="{'text-cyan-500': activeTab===2}" />
                            <span class="hidden md:block"
                                :class="{'text-cyan-500': activeTab===2}">แบบฝึกหัด</span>
                        </div>
                    </button> -->
                    <!-- <Link :href="`/courses`" -->
                    <!-- <button type="button" @click.prevent="setActiveTab(3)"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        :class="{'border-b-4 border-cyan-500': activeTab === 3}">
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="healthicons:i-exam-qualification-outline" class="w-6 md:w-8 h-6 md:h-8"
                                :class="{'text-cyan-500': activeTab === 3}" />
                            <span class="hidden md:block" :class="{'text-cyan-500': activeTab === 3}">แบบทดสอบ</span>
                        </div>
                    </button> -->

                    <!-- <button type="button" @click.prevent="setActiveTab(4)"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        :class="{'border-b-4 border-cyan-500': activeTab === 4}">
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="ph:users-four" class="w-6 md:w-8 h-6 md:h-8"
                                :class="{'text-cyan-500': activeTab === 4}" />
                            <span class="hidden md:block" :class="{'text-cyan-500': activeTab === 4}">สมาชิก</span>
                        </div>
                    </button> -->

                    <!-- <button type="button" @click.prevent="setActiveTab(5)"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        :class="{'border-b-4 border-cyan-500': activeTab === 5}">
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="heroicons-outline:user-group" class="w-6 md:w-8 h-6 md:h-8"
                                :class="{'text-cyan-500': activeTab === 5}" />
                            <span class="hidden md:block" :class="{'text-cyan-500': activeTab === 5}">กลุ่ม</span>
                        </div>
                    </button> -->

                    <!-- <button v-if="props.isCourseAdmin" type="button" @click.prevent="setActiveTab(6)"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        :class="{'border-b-4 border-cyan-500': activeTab === 6}">
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="lets-icons:group-add-light" class="w-6 md:w-8 h-6 md:h-8"
                                :class="{'text-cyan-500': activeTab === 6}" />
                            <span class="hidden md:block" :class="{'text-cyan-500': activeTab === 6}">คำขอเป็นสมาชิก</span>
                        </div>
                    </button> -->

                    <!-- <button v-if="props.isCourseAdmin" type="button" @click.prevent="setActiveTab(7)"
                        class="tab-item border-b-4 hover:border-gray-400 rounded-none w-full text-center flex-row justify-center "
                        :class="{'border-b-4 border-cyan-500': activeTab === 7}">
                        <div class="flex flex-col items-center py-2 justify-center text-slate-600/80 ">
                            <Icon icon="mdi-light:settings" class="w-6 md:w-8 h-6 md:h-8"
                                :class="{'text-cyan-500': activeTab === 7}" />
                            <span class="hidden md:block" :class="{'text-cyan-500': activeTab === 7}">การตั้งค่า</span>
                        </div>
                    </button> -->

                </div>
            </div>
        </template>

        <template #leftSideWidget>
            <div class="space-y-4">
                <!-- <CourseCardWidget :course="$page.props.course.data"/> -->

                <!-- <CourseGroupProfileWidget /> -->
            </div>
        </template>

        <template #mainContent>
            <div class="mt-4">
                <div v-if="$slots.courseContent">
                    <slot name="courseContent"></slot>
                </div>
            </div>
        </template>

        <template #rightSideWidget>
            <div>
                <!-- <CourseCardWidget :course="$page.props.course.data"/> -->
            </div>
        </template>
    </MainLayout>
</template>
