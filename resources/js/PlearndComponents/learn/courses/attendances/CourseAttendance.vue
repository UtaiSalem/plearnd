<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

import AttendanceCard from '@/PlearndComponents/learn/courses/attendances/AttendanceCard.vue'
import StaggeredFade from '@/PlearndComponents/accessories/StaggeredFade.vue';

const props = defineProps({
    groups: Object,
});

const authGroupIndex = computed(()=> {
    if (usePage().props.isCourseAdmin) return 0;
    return usePage().props.groups.data.findIndex((group)=> group.id === usePage().props.courseMemberOfAuth.group_id);
});

const activeGroupTab = ref(authGroupIndex.value);
const isLoadingAttendances = ref(false);
const openCreateNewAttendanceForm = ref(false);
const groupAttendances = ref([]);
const attendanceResource = ref(null);

const currentDate = new Date();
const dateString = currentDate.toLocaleDateString('th-TH', { year: 'numeric', month: '2-digit', day: '2-digit' });
const timeString = currentDate.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

const showAttendanceResource = computed(()=> {
    return attendanceResource.value && attendanceResource.value.details.length > 0;
});

const form = ref({
    description:'',
    date: new Date(),
});

const activeGroupMembers = computed(()=> {
    return props.groups[activeGroupTab.value].members;
});

function setActiveGroupTab(tab){
    activeGroupTab.value = tab;
    attendanceResource.value = null;
    getGroupAttendances(usePage().props.course.data.id, props.groups[activeGroupTab.value].id);
    // if(groupAttendances.value.length <= 0){
    //     groupAttendances.value.push({
    //         id: 0,
    //         date: 'ยังไม่มีประวัติการเชคชื่อ',
    //     });
    // }
}

async function handleAttendanceDateSelection(e){
    isLoadingAttendances.value = true;
    attendanceResource.value = null;
    let attendance_id = e.target.value;
    if(attendance_id == 0){
        return;
    }else{
        attendanceResource.value = null;
        let detailsResp = await axios.get(`/attendances/${attendance_id}/details`);
        if(detailsResp.data && detailsResp.data.success){
            attendanceResource.value = detailsResp.data.attendance_resource;
            isLoadingAttendances.value = false;
        }
    }
}

onMounted(async ()=>{
    form.value.date = currentDate;
    getGroupAttendances(usePage().props.course.data.id, props.groups[activeGroupTab.value].id);
});

async function getGroupAttendances(courseId, groupId){
    try {
        isLoadingAttendances.value = true;
        groupAttendances.value = [];
        let resp = await axios.get(`/courses/${courseId}/groups/${groupId}/attendances`);
        if(resp.data && resp.data.success){
            groupAttendances.value = resp.data.attendances;
            isLoadingAttendances.value = false;
        }else{
            isLoadingAttendances.value = false;
        }
    } catch (error) {
        console.log(error);
        isLoadingAttendances.value = false;
    }
}

const handleCreateNewAttendanceFormSubmit = async () => {
    try {
        attendanceResource.value = null;
        isLoadingAttendances.value = true;
        let resp = await axios.post(`/courses/${usePage().props.course.data.id}/groups/${props.groups[activeGroupTab.value].id}/attendances`, form.value);
        if(resp.data && resp.data.success){
            activeGroupMembers.value.forEach( async (member) => {
                await axios.post(`/attendances/${resp.data.attendance.id}/details`, {
                    course_id: usePage().props.course.data.id,
                    group_id: props.groups[activeGroupTab.value].id,
                    course_attendance_id: resp.data.attendance.id,
                    course_member_id: member.id,
                    status: 0,
                    comments: '',
                });
            });

            let attendaceResp = await axios.get(`/attendances/${resp.data.attendance.id}/details`);
            if(attendaceResp.data && attendaceResp.data.success){
                attendanceResource.value = attendaceResp.data.attendance_resource;
            }

            isLoadingAttendances.value = false;
            openCreateNewAttendanceForm.value = false;
        }else{
                console.log(resp.data);
                isLoadingAttendances.value = false;
        }
    } catch (error) {
        console.log(error);
        isLoadingAttendances.value = false;
        openCreateNewAttendanceForm.value = false;
    }
}

const handleCancleCreateNewAttendance = () => {
    form.value.description = '';
    openCreateNewAttendanceForm.value = false;
}

const handleFinishAttendance =  () => {
    attendanceResource.value = null;
}

</script>

<template>
    <div class="w-full mx-auto ">
        <section class="max-w-full" aria-multiselectable="false">
            <ul v-if="$page.props.isCourseAdmin" class="flex flex-wrap items-center bg-white rounded-md shadow-lg overflow-hidden" role="tablist">
                <li v-for="(group, index) in $page.props.groups.data" :key="index" class="w-1/3" role="presentation ">
                    <button @click.prevent="setActiveGroupTab(index)"
                        class="inline-flex items-center justify-center w-full h-12 gap-2 px-6  text-sm tracking-wide transition duration-300 border-b-2 focus-visible:outline-none whitespace-nowrap hover:border-violet-600 focus:border-violet-700 text-violet-500 hover:text-violet-600 focus:text-violet-700 hover:bg-violet-50 focus:bg-violet-50 disabled:cursor-not-allowed disabled:border-slate-500 stroke-violet-500 hover:stroke-violet-600 focus:stroke-violet-700"
                        :class="activeGroupTab === index ? 'border-violet-500 bg-violet-200 font-bold':'font-medium'"
                        id="tab-label-1a" role="tab" aria-setsize="3" aria-posinset="1" tabindex="0"
                        aria-controls="tab-panel-1a" aria-selected="true">
                        <span>{{ group.name }}</span>
                    </button>
                </li>
            </ul>
            <div class="mt-4 w-full">
                <!-- <label for="country" class="block text-sm font-medium leading-6 text-gray-900">Country</label> -->
              <select id="country" name="country" @change="handleAttendanceDateSelection" autocomplete="country-name" class="w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600  sm:text-sm sm:leading-6">
                    <option :value="0" :selected="!groupAttendances.length">{{ dateString + ' ' + timeString }}</option>
                    <option 
                        v-for="(attendance, index) in groupAttendances" 
                        :key="index" 
                        :value="attendance.id"
                    >
                        <!-- :selected="index === groupAttendances.length-1" -->
                        {{ new Date(attendance.date).toLocaleDateString('th-TH', { year: 'numeric', month: '2-digit', day: '2-digit' }) + ' ' + new Date(attendance.date).toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) }}
                    </option>
              </select>

            </div>

            <div v-if="isLoadingAttendances" class="plearnd-card mt-4 ">
                <Icon icon="svg-spinners:blocks-shuffle-3" class="h-6 w-6 mx-auto text-violet-600" />
            </div>
            <div v-if="showAttendanceResource" class="">
                <div class="pt-4" id="tab-panel-1a" aria-hidden="false" role="tabpanel" aria-labelledby="tab-label-1a"
                    tabindex="-1">
                    <staggered-fade :duration="50" tag="ul" class="flex flex-col w-full ">

                        <AttendanceCard 
                            v-for="(detail, index) in attendanceResource.details"
                            :key="index" 
                            :data-index="index"
                            :courseId="attendanceResource.course_id"
                            :groupId="attendanceResource.group_id"
                            :attendanceId="attendanceResource.id"
                            :attendanceDetail="detail"
                        />
                        <div class="flex items-center justify-center">
                            <button v-if="showAttendanceResource" type="button" @click.prevent="handleFinishAttendance" class="text-red-600 bg-white rounded-full shadow-xl">
                                <Icon icon="uiw:circle-close" class="w-10 h-10"></Icon>
                            </button>
                        </div>
                    </staggered-fade>
                </div>
            </div>

            <div v-else class="plearnd-card mt-4">
                <div v-if="openCreateNewAttendanceForm">
                <!-- <div v-if="!showAttendanceResource"> -->
                    <form @submit.prevent="handleCreateNewAttendanceFormSubmit">
                        <div class="space-y-4">
                            <div class="border-b border-gray-900/10 pb-4">
                                <div class=" grid grid-cols-1 gap-4 sm:grid-cols-6">
                                    <div class="sm:col-span-3">
                                        <span :id="`attendance-date-input`"
                                            class="block text-sm font-medium leading-6 text-gray-900">วันที่</span>
                                        <div class="mt-2">
                                            <VueDatePicker id="attendanceDateInput" name="attendanceDateInput"
                                                :format="'dd/MM/yyyy HH:mm'"
                                                v-model="form.date"
                                                :model-value="form.date" placeholder="วันที่"
                                                @update:model-value="handleAttendanceDateSelection" />
                                        </div>
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label for="first-name"
                                            class="block text-sm font-medium leading-6 text-gray-900">คำอธิบาย</label>
                                        <div class="mt-2">
                                            <input type="text" :name="form.description" id="first-name" required :autocomplete="form.description||'_'" v-model="form.description"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="mt-2 flex items-center justify-end gap-x-4">
                            <button type="button" @click.prevent="handleCancleCreateNewAttendance" class="text-sm font-semibold leading-6 text-rose-600 rounded-md bg-rose-200 px-3 py-1.5">ยกเลิก</button>
                            <button type="submit" :disabled="isLoadingAttendances"
                                class="flex justify-center space-x-1 items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                                <Icon v-if="isLoadingAttendances" icon="svg-spinners:270-ring-with-bg" class="h-6 w-6 text-white" />
                                <span v-else> บันทึก </span>
                            </button>
                        </div>
                    </form>
                </div>
                <div v-else class="flex justify-center ">
                    <button @click.prevent="openCreateNewAttendanceForm = true" class="plearnd-btn-success w-36 flex items-center space-x-2">
                        <Icon icon="heroicons-solid:plus" class="h-6 w-6 text-white" />
                        <small>เชคชื่อใหม่</small>
                    </button>
                </div>
            </div>

        </section>
    </div>
</template>
