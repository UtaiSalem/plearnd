<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import {
  Listbox,
  ListboxLabel,
  ListboxButton,
  ListboxOptions,
  ListboxOption,
} from '@headlessui/vue'
// import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import AttendanceCard from '@/PlearndComponents/learn/courses/attendances/AttendanceCard.vue'
import MemberCard from '@/PlearndComponents/learn/courses/members/MemberCard.vue'
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
const isOpenModal = ref(false)
const showGroupMembers = ref(true);
const showCreateNewAttendanceButton = ref(true);
const attendanceDate = ref(new Date().toLocaleDateString('th-TH', { year: 'numeric', month: '2-digit', day: '2-digit' }));
const currentDate = new Date();
const dateString = currentDate.toLocaleDateString('th-TH', { year: 'numeric', month: '2-digit', day: '2-digit' });
const timeString = currentDate.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

const groupAttendances = ref([]);
const attendanceGroupMembers = ref([]);
const attendanceGroupDetails = ref([]);
const attendanceResource = ref(null);
const newAttendanceDetail = ref({
    // course_id: usePage().props.course.data.id,
    // group_id: props.groups[activeGroupTab.value].id,
    // course_attendance_id: props.attendance.id,
    // course_member_id: props.member.id,
    status: null,
    comments: null,
});

const showAttendanceResource = computed(()=> {
    return attendanceResource.value && attendanceResource.value.details.length > 0;
});
const openCreateNewAttendanceForm = ref(false);
// const selectedAttendance = ref(groupAttendances.value[0] || null);
const selectedAttendance = ref(attendanceDate);

const form = ref({
    description:'',
    date: new Date(),
});

const activeGroupMembers = computed(()=> {
    return props.groups[activeGroupTab.value].members;
});

function setActiveGroupTab(tab){
    activeGroupTab.value = tab;
    attendanceGroupDetails.value = [];
    getGroupAttendances(usePage().props.course.data.id, props.groups[activeGroupTab.value].id);
    // if(groupAttendances.value.length <= 0){
    //     groupAttendances.value.push({
    //         id: 0,
    //         date: 'ยังไม่มีประวัติการเชคชื่อ',
    //     });
    // }
}

async function handleAttendanceDateSelection(e){
    let attendance_id = e.target.value;
    if(attendance_id == 0){
        return;
    }else{
        attendanceGroupDetails.value = [];
        attendanceResource.value = null;
        let detailsResp = await axios.get(`/attendances/${attendance_id}/details`);
        if(detailsResp.data && detailsResp.data.success){
            attendanceResource.value = detailsResp.data.attendace_resource;
            // isOpenModal.value = true;
            // showGroupMembers.value = true;
            // showAttendanceResource.value = true;
        }
    }

    // console.log(attendance_id);
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
            // if(groupAttendances.value.length <1){
                // groupAttendances.value.push({
                //     id: 0,
                //     date: currentDate,
                // });
                // selectedAttendance.value = {
                //     date: 'ยังไม่มีประวัติการเชคชื่อ',
                // }
            // }else{
            //     selectedAttendance.value = groupAttendances.value[groupAttendances.value.length-1].date;
            // }
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
        attendanceGroupMembers.value = [];
        newAttendance.value = {};
        isLoadingAttendances.value = true;
        let resp = await axios.post(`/courses/${usePage().props.course.data.id}/groups/${props.groups[activeGroupTab.value].id}/attendances`, form.value);
        if(resp.data && resp.data.success){
            newAttendance.value = resp.data.attendance;
            isLoadingAttendances.value = false;
            isOpenModal.value = true;
            // attendanceGroupMembers.value = resp.data.attendace_member_details;
            openCreateNewAttendanceForm.value = false;
        }else{
                console.log(resp.data);
        }
    } catch (error) {
        console.log(error);
        isLoadingAttendances.value = false;
        isOpenModal.value = false;
        openCreateNewAttendanceForm.value = false;
    }
}

const handleCancleCreateNewAttendance = () => {
    form.value.description = '';
    openCreateNewAttendanceForm.value = false;
    showGroupMembers.value = false;
    isOpenModal.value = false;
    attendanceGroupMembers.value = [];
}

const handleFinishAttendance =  () => {
    isOpenModal.value = false;
    showGroupMembers.value = false;
    attendanceGroupMembers.value = [];
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
            <!-- <div class="mt-4">
                <Listbox v-model="selectedAttendance">
                    <div class="relative ">
                        <ListboxButton
                            class="relative w-full cursor-default rounded-lg bg-white py-2 pl-3 pr-10 text-left shadow-md focus:outline-none focus-visible:border-indigo-500 focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-orange-300 sm:text-sm">
                            <span class="block truncate">{{ dateString + ' ' + timeString }}</span>
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                                <Icon icon="heroicons:chevron-up-down-solid" class="h-6 w-6 text-gray-400" aria-hidden="true" />
                            </span>
                        </ListboxButton>

                        <transition leave-active-class="transition duration-100 ease-in"
                            leave-from-class="opacity-100" leave-to-class="opacity-0">
                            <ListboxOptions
                                class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm">
                                <ListboxOption v-slot="{ active, selected }" v-for="attendance in groupAttendances" :key="attendance.id"
                                    :value="attendance" as="template">
                                    <li :class="[active ? 'bg-amber-100 text-amber-900' : 'text-gray-900',
                                            'relative cursor-default select-none py-2 pl-10 pr-4']">
                                        <span :class="[selected ? 'font-medium' : 'font-normal','block truncate',]">
                                            {{ attendance.date }}
                                        </span>
                                        <span v-if="selected"
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 text-emerald-600">
                                            <Icon icon="heroicons:document-check-solid" class="h-6 w-6"
                                                aria-hidden="true" />
                                        </span>
                                    </li>
                                </ListboxOption>
                            </ListboxOptions>
                        </transition>
                    </div>
                </Listbox>
            </div> -->

            <!-- <div class="bg-white rounded-lg shadow-lg mt-4 ">
                <div class="flex ">
                    <VueDatePicker 
                        id="attendance-date-input"
                        name="attendanceDateInput"
                        class="rounded-r-none" 
                        :format="'dd/MM/yyyy H:m:ss'" 
                        :model-value="attendanceDate" 
                        placeholder="วันที่"
                        @update:model-value="handleAttendanceDateSelection"
                    >
                    </VueDatePicker>
                    <Menu as="div" class="">
                        <MenuButton class="flex items-center w-8 justify-center rounded-r-lg bg-teal-400 h-full text-sm font-semibold ring-gray-300 hover:bg-teal-500">
                            <Icon icon="heroicons-solid:chevron-down" class="h-8 w-8 text-white" aria-hidden="true" />
                        </MenuButton>
                        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                            <MenuItems class="absolute right-4 z-10  w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                <div class="py-1">
                                    <MenuItem v-if="!groupAttendances || groupAttendances.length <= 0">
                                        <p>ยังไม่มีประวัติการเชคชื่อ</p>
                                    </MenuItem>
                                    <MenuItem v-else v-for="(item, index) in groupAttendances" :key="index">
                                        <p>attendance</p>
                                    </MenuItem>
                                </div>
                            </MenuItems>
                        </transition>
                    </Menu>
                </div>
            </div> -->
            <!-- <div v-if="showAttendanceResource" class="mt-4"> -->
            <div v-if="attendanceResource" class="">
                <div class="pt-4" id="tab-panel-1a" aria-hidden="false" role="tabpanel" aria-labelledby="tab-label-1a"
                    tabindex="-1">
                    <staggered-fade :duration="50" tag="ul" class="flex flex-col w-full ">
                        <!-- <AttendanceCard 
                            v-for="(detail, index) in attendanceGroupDetails"
                            :key="index" 
                            :data-index="index"
                            :attendance="detail.attendance"
                            :member="member"
                        /> -->
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

            <div v-if="!showAttendanceResource" class="plearnd-card mt-4">
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
                            <button type="submit"
                                class="flex justify-center space-x-1 items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                                <Icon v-if="isLoadingAttendances" icon="svg-spinners:270-ring-with-bg" class="h-6 w-6 text-white" />
                                <span v-else> บันทึก </span>
                            </button>
                        </div>
                    </form>
                </div>
                <div v-else class="flex justify-center">
                    <button @click.prevent="openCreateNewAttendanceForm = true" class="plearnd-btn-success w-36 flex items-center space-x-2">
                        <Icon icon="heroicons-solid:plus" class="h-6 w-6 text-white" />
                        <small>เชคชื่อใหม่</small>
                    </button>
                </div>
            </div>

            <div class="">
                <TransitionRoot as="template" :show="isOpenModal">
                    <Dialog as="div" class="relative z-10 " @close="isOpenModal = false">
                        <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0"
                            enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100"
                            leave-to="opacity-0">
                            <div class="fixed inset-0 bg-gray-600 bg-opacity-80 transition-opacity" />
                        </TransitionChild>

                        <div class="fixed mt-14 inset-0 z-10 w-screen overflow-y-auto">
                            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                <TransitionChild as="template" enter="ease-out duration-300"
                                    enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                                    leave-from="opacity-100 translate-y-0 sm:scale-100"
                                    leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                                    <DialogPanel
                                        class="relative transform overflow-hidden rounded-lg text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                                        <div class=""
                                            id="tab-panel-1a"
                                            aria-hidden="false" role="tabpanel" aria-labelledby="tab-label-1a"
                                            tabindex="-1">
                                            <!-- <AttendanceCard 
                                                v-for="(detail, index) in attendanceGroupMembers"
                                                :key="index" 
                                                :data-index="index" 
                                                :attendanceDetail="detail"
                                            /> -->
                                            <!-- <AttendanceCard v-for="(member, index) in activeGroupMembers" :key="index"
                                                :data-index="index"
                                                :attendance="newAttendanceDetail"
                                                :member="member"
                                            /> -->

                                            <div class="flex justify-center rounded-lg">
                                                <button @click.prevent="handleFinishAttendance" class="plearnd-btn-success w-full flex items-center justify-center space-x-2">
                                                    <Icon icon="mdi:account-multiple-success" class="h-6 w-6 text-white" />
                                                    <small>เสร็จสิ้น</small>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                            <button type="button"
                                                class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto"
                                                @click="isOpenModal = false">Deactivate</button>
                                            <button type="button"
                                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                                                @click="isOpenModal = false" ref="cancelButtonRef">Cancel</button>
                                        </div> -->
                                    </DialogPanel>
                                </TransitionChild>
                            </div>
                        </div>
                    </Dialog>
                </TransitionRoot>
            </div>

        </section>
    </div>
</template>
