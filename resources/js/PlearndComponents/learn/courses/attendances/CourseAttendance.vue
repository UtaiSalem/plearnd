<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';
import Swal from 'sweetalert2';

import '@vuepic/vue-datepicker/dist/main.css'

import StaggeredFade from '@/PlearndComponents/accessories/StaggeredFade.vue';
import MembersAttendanceDetails from './MembersAttendanceDetails.vue';
import CreateNewCourseAttendanceForm from './CreateNewCourseAttendanceForm.vue';
import ShowCourseAttendanceForm from './ShowCourseAttendanceForm.vue';


const props = defineProps({
    groups: Object,
});

const activeGroupTab = ref(usePage().props.courseMemberOfAuth ? usePage().props.courseMemberOfAuth.last_accessed_group_tab : 0);
const isLoadingAttendances = ref(false);
const openCreateNewAttendanceForm = ref(false);
const groupAttendances = ref([]);
const attendanceResource = ref(null);

const isShowCurrentAttendance = ref(false);
const currentAttendance = ref(null);
const currentAttendanceIndex = ref(null);

const showAttendanceResource = computed(()=> {
    return !isLoadingAttendances.value;
});

const activeGroupMembers = computed(()=> {
    return props.groups[activeGroupTab.value].members.sort((a,b) => a.order_number - b.order_number);
});

async function setActiveGroupTab(tab){
    activeGroupTab.value = tab;
    attendanceResource.value = null;
    getGroupAttendances(usePage().props.course.data.id, props.groups[activeGroupTab.value].id);

    if (usePage().props.courseMemberOfAuth && (tab < props.groups.length)) {
         let resp = await axios.post(`/courses/${usePage().props.course.data.id}/members/${usePage().props.courseMemberOfAuth.id}/set-active-group-tab`, {group_tab: activeGroupTab.value});
         if (resp.data.success) {
             usePage().props.courseMemberOfAuth.last_accessed_group_tab = tab;
         }
    }
}

onMounted(async ()=>{
    getGroupAttendances(usePage().props.course.data.id, props.groups[activeGroupTab.value].id);
});

async function getGroupAttendances(courseId, groupId){
    try {
        isLoadingAttendances.value = true;
        groupAttendances.value = [];
        let resp = await axios.get(`/courses/${courseId}/groups/${groupId}/attendances`, {
            params: {
                isCourseAdmin: usePage().props.isCourseAdmin,
            }
        });
        
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

const handleAddNewAttendance = async (atdn) => {
    attendanceResource.value = null;
    groupAttendances.value.push(atdn);          
    handleCancleCreateNewAttendance();
}

const handleCancleCreateNewAttendance = () => {
    openCreateNewAttendanceForm.value = false;
}

const handleUpdateAttendance = async (atdn) => {
    groupAttendances.value[currentAttendanceIndex.value] = atdn;
    handleCloseAttendance();
}

const handleDeleteAttendance = async (aIndx) => {
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่ที่จะลบการบันทึกนี้?',
        text: "คุณจะไม่สามารถเรียกดูการบันทึกนี้ได้อีกต่อไป!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ยืนยัน',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const response = axios.delete(`/attendances/${groupAttendances.value[aIndx].id}`);
            return response;
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                if (result.value.data.success) {
                    groupAttendances.value.splice(aIndx, 1);
                    Swal.fire(
                        'ลบข้อมูลสำเร็จ!',
                        'บันทึกการมาเรียนถูกลบออกเรียบร้อยแล้ว',
                        'success'
                    )
                } else {
                    Swal.fire(
                        'เกิดข้อผิดพลาด!',
                        'เกิดข้อผิดพลาดในการลบการบันทึกนี้ กรุณาลองใหม่อีกครั้ง',
                        'error'
                    )
                }
            } catch (error) {
                console.log(error);
            } finally {
                isLoadingAttendances.value = false;
                handleCloseAttendance();
                handleCancleCreateNewAttendance();
            }
        }
    });              
}

const handleShowAttendance = (aIndx) => {
    currentAttendance.value = groupAttendances.value[aIndx];
    // console.log(currentAttendance.value);
    currentAttendanceIndex.value = aIndx;
    isShowCurrentAttendance.value = true;
}

const handleCloseAttendance = () => {
    isShowCurrentAttendance.value = false;
    currentAttendance.value = null;
}

const preDeleteAttendance = () => {
    handleDeleteAttendance(currentAttendanceIndex.value);
    handleCloseAttendance();
    handleCancleCreateNewAttendance();
}

</script>

<template>
    <div class="w-full mx-auto ">
        <section class="max-w-full" aria-multiselectable="false">
            <ul v-if="$page.props.isCourseAdmin" class="flex flex-wrap items-center bg-white rounded-md shadow-lg overflow-hidden p-2 " role="tablist">
                <li v-for="(group, index) in $page.props.groups.data" :key="index" class="w-1/2 md:w-1/3 lg:w-1/4" role="presentation ">
                    <button @click.prevent="setActiveGroupTab(index)"
                        class="inline-flex items-center justify-center w-full h-12 px-2 rounded-t-md text-sm tracking-wide transition duration-300 border-b-2 focus-visible:outline-none whitespace-nowrap hover:border-violet-600 focus:border-violet-700 text-violet-500 hover:text-violet-600 focus:text-violet-700 hover:bg-violet-50 focus:bg-violet-50 disabled:cursor-not-allowed disabled:border-slate-500 stroke-violet-500 hover:stroke-violet-600 focus:stroke-violet-700"
                        :class="activeGroupTab === index ? 'border-violet-500 bg-violet-200 font-bold':'font-medium'"
                        id="tab-label-1a" role="tab" aria-setsize="3" aria-posinset="1" tabindex="0"
                        aria-controls="tab-panel-1a" aria-selected="true">
                        <span>{{ group.name + ' (' + group.members.length + ')' }}</span>
                    </button>
                </li>
            </ul>

            <div v-if="isLoadingAttendances" class="plearnd-card mt-4 ">
                <Icon icon="svg-spinners:blocks-shuffle-3" class="h-6 w-6 mx-auto text-violet-600" />
            </div>

            <div v-if="showAttendanceResource" class="">
                <div class="pt-4" id="tab-panel-1a" aria-hidden="false" role="tabpanel" aria-labelledby="tab-label-1a"
                    tabindex="-1">
                    <staggered-fade :duration="50" tag="ul" class="flex flex-col w-full ">
                        <div class="bg-white rounded-md p-2">
                            <div class="relative overflow-x-auto rounded-lg">
                                <table class="w-full divide-gray-200 overflow-x-auto">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                                        <tr class="text-center">
                                            <th scope="col" class=" px-1 py-3 border border-slate-300">
                                                สมาชิก
                                            </th>

                                            <th scope="col" v-for="(attendance, index) in groupAttendances" :key="attendance.id"
                                                class="px-1 py-3 border border-slate-300">
                                                <button @click.prevent="handleShowAttendance(index)" class="flex justify-center items-center mx-auto text-sm text-green-600">
                                                    #{{ index + 1 }}<Icon icon="hugeicons:view" width="20" height="20" class="ml-1" />
                                                </button>
                                            </th>

                                            <th v-if="!openCreateNewAttendanceForm" scope="col" class=" px-1 py-3 border bg-white">
                                                <button @click.prevent="openCreateNewAttendanceForm = true" class="flex justify-center items-center mx-auto text-green-600">
                                                    <Icon icon="icon-park-outline:add-one" width="20" height="20" class="" />
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody class="bg-white ">
                                        <MembersAttendanceDetails 
                                            v-for="(member) in activeGroupMembers" :key="member.id"
                                            :member="member"
                                            :groupAttendances="groupAttendances"
                                        />
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </staggered-fade>
                </div>
            </div>

        </section>

        <section  v-if="openCreateNewAttendanceForm">
            <CreateNewCourseAttendanceForm
                :groupId="props.groups[activeGroupTab].id" 
                @close-form="openCreateNewAttendanceForm = false" 
                @cancle-create-new-attendance="handleCancleCreateNewAttendance"
                @add-new-attendance="(atdn)=> handleAddNewAttendance(atdn)"
            />
        </section>

        <section  v-if="isShowCurrentAttendance">
            <ShowCourseAttendanceForm 
                :groupId="props.groups[activeGroupTab].id"
                :attendance="currentAttendance"
                @close-form="isShowCurrentAttendance = false"
                @delete-attendance="preDeleteAttendance"
                @updat-attendance="(respAtd)=>handleUpdateAttendance(respAtd)"
            />
        </section>
    </div>
</template>
