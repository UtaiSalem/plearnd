<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import Swal from 'sweetalert2';

import CourseGroupItem from '@/PlearndComponents/learn/courses/groups/CourseGroupItem.vue';

const props = defineProps({
    course: Object,
    groups: Object,
    courseMemberOfAuth: Object
});

const computedGroup = computed(()=> usePage().props.groups.data);

const emit = defineEmits([
    'add-new-group', 
    'delete-group',
]);

// watch(()=>usePage().props.courseMemberOfAuth, () => {
//     router.reload({ only: ['groups']});
//     console.log('reload groups');
//     router.reload({ only: ['members']});
//     console.log('reload members');
// })

const tempCover = ref('/storage/images/courses/covers/default_cover.jpg');
const coverInput = ref(null);

const browseCover = () => { coverInput.value.click() };
function onCoverInputChange(event){
  form.value.cover = event.target.files[0];
  tempCover.value = URL.createObjectURL(event.target.files[0]);
}

const defaultForm = ref({
    name: '',
    description: '',
    cover: null
});

const form = ref({
    name: '',
    description: '',
    cover: null
});

const openCreateNewGroupModal = ref(false);

//Create new course's group
async function handleFormSubmit(){
    try {
            if (form.value.name.trim().length>0) {

            const config = { headers: { 'Content-Type': 'multipart/form-data' } };
            let newCourseGroup = new FormData();
            newCourseGroup.append('name', form.value.name);
            newCourseGroup.append('description', form.value.description);
            newCourseGroup.append('cover', form.value.cover ?? null);
            const cgResp = await axios.post(`/courses/${props.course.id}/groups`, newCourseGroup, config);

            if (cgResp.data && cgResp.data.success) {
                emit('add-new-group', cgResp.data.newGroup);
                openCreateNewGroupModal.value = false;
                Swal.fire('สำเร็จ','เพิ่มกลุ่มเรียบร้อยแล้ว.','success');
                form.value = defaultForm.value;
            }
        }
    } catch (error) {
        Swal.fire('ล้มเหลว','เกิดข้อผิดพลาด. <br />'+ error + "กรุณาลองใหม่อีกครั้ง",'error' );
        console.log(error);
    }
}

function onCancleCreateNewCourseGroup(){
    form.value = defaultForm.value;
    openCreateNewGroupModal.value = false;
}

function onDeleteCourseGroup(idx){
    Swal.fire({
            title: 'แน่ใจหรือไม่?',
            text: "ที่จะลบกลุ่มนี้อย่างถาวร!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'ยกเลิก',
            confirmButtonText: 'ใช่, ยืนยัน!'
        })
        .then(async (result) => {
            if (result.isConfirmed) {
                let delResp = await axios.delete(`/courses/${props.course.id}/groups/${props.groups[idx].id}`);
                if (delResp.data && delResp.data.success) {
                    // props.groups.splice(idx, 1);
                    emit('delete-group', idx);
                    Swal.fire('การลบสมบูรณ์!','ลบกลุ่มเรียบร้อยแล้ว.','success');                
                }
            }
            // console.log(`/courses/${props.course.id}/groups/${props.groups[idx].id}`);
        }
    );
}

function onEditCourseGroup(idx){
    // try {
    //     let updateResp = await axios.delete(`/courses/${props.course.id}/groups/${props.groups[idx].id}`);
    //     if (updateResp.data && updateResp.data.success) {
            Swal.fire('การแก้ไขสมบูรณ์!','แก้ไขกลุ่ม '+ props.groups[idx].name + ' เรียบร้อยแล้ว.','success');                
    //     }
    // } catch (error) {
    //     console.log(error);
    // }
}

async function requestTobeGroupMember(grpId, indx){

    try {   
        let memberResp = await axios.post(`/courses/${props.course.id}/groups/${grpId}/members`);
        if (memberResp.data.success) {

            usePage().props.courseMemberOfAuth=memberResp.data.courseMemberOfAuth;
            usePage().props.groups.data[indx].members = memberResp.data.group.members;
            memberResp.data.courseMemberOfAuth.course_member_status == 1 ? usePage().props.course.data.isMember=true : usePage().props.course.data.isMember=false;

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

async function requestTobeUnMemberGroup(grpId, indx){
    
    try {
        let unmemberGroupResp = await axios.delete(`/courses/${props.course.id}/groups/${grpId}/members/${props.courseMemberOfAuth.id}`);
        if (unmemberGroupResp.data.success) {
           
            usePage().props.courseMemberOfAuth          = unmemberGroupResp.data.courseMember;
            usePage().props.course.data.status          = unmemberGroupResp.data.courseMember.status;
            usePage().props.course.data.member_status   = unmemberGroupResp.data.courseMember.member_status;
            usePage().props.groups.data[indx].members   = unmemberGroupResp.data.group.members;
            unmemberGroupResp.data.courseMember.course_member_status == 1 ? usePage().props.course.data.isMember=true : usePage().props.course.data.isMember=false;
            
            Swal.fire(
                'เสร็จสิ้น',
                'ออกจากสมาชิกกลุ่มเรียบร้อยแล้ว',
                'success'
            );

        }
    } catch (error) {
        console.log(error);
    }

}

</script>

<template>
    <div class="flex flex-col space-y-4">

        <div class="flex flex-wrap  justify-between gap-4">
            <div v-for="(group, index) in computedGroup" :key="index" class="w-full sm:w-[48%]">
                <CourseGroupItem class=""
                    :group="group"
                    :courseMemberOfAuth="props.courseMemberOfAuth"
                    :group_index="index"
                    
                    @delete-course-group="()=> onDeleteCourseGroup(index)"
                    @edit-course-group="()=> onEditCourseGroup(index)"
                    @request-tobe-group-member="()=> requestTobeGroupMember(group.id, index)"
                    @request-tobe-group-unmember="()=> requestTobeUnMemberGroup(group.id, index)"
                />
            </div>
        </div>

        <div v-if="$page.props.isCourseAdmin" class=" bg-white rounded-lg shadow-lg p-4 text-center">
            <button @click.prevent="openCreateNewGroupModal = true" class=" block mx-auto rounded-md bg-violet-600 hover:shadow-lg font-semibold text-white px-6 py-2">
                <span>เพิ่มกลุ่มใหม่</span>
            </button>
        </div>

        <!-- Create new group modal form  -->
        <!-- Modal -->
        <div v-if="openCreateNewGroupModal" class="fixed top-0 left-0 z-20 flex items-center justify-center w-screen h-screen bg-slate-800/80 backdrop-blur-lg" aria-labelledby="header-4a content-4a" aria-modal="true" tabindex="-1" role="dialog">
            <div class="flex max-h-[90vh] max-w-md flex-col gap-2 overflow-hidden rounded-lg bg-white p-4 text-slate-500 shadow-xl shadow-slate-700/10" id="modal" role="document">
                <!-- Modal header -->
                <header id="header-4a" class="flex items-center">
                    <h3 class="flex-1 text-lg font-medium text-slate-700">เพิ่มกลุ่มใหม่</h3>
                    <button @click.prevent="onCancleCreateNewCourseGroup" class="inline-flex items-center justify-center h-10 gap-2 px-5 text-sm font-medium tracking-wide transition duration-300 rounded-full focus-visible:outline-none justify-self-center whitespace-nowrap text-violet-500 hover:bg-violet-100 hover:text-violet-600 focus:bg-violet-200 focus:text-violet-700 disabled:cursor-not-allowed disabled:text-violet-300 disabled:shadow-none disabled:hover:bg-transparent" aria-label="close dialog">
                        <span class="relative only:-mx-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" role="graphics-symbol" aria-labelledby="title-79 desc-79">
                            <title id="title-79">Icon title</title>
                            <desc id="desc-79">A more detailed description of the icon</desc>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        </span>
                    </button>
                </header>
                <!-- Modal body -->
                <div id="content-4a" class="flex-1">
                    <form id="create-new-course-group-form" class="flex flex-col space-y-4"
                        @submit.prevent="handleFormSubmit">

                        <div class="relative w-72">
                            <img :src="tempCover" class="rounded-tl-lg rounded-tr-lg" />
                            <div class="absolute bottom-2 right-2 flex flex-col">
                                <input type="file" class="hidden" accept="image/*" ref="coverInput" @change="onCoverInputChange" />
                                <button type="button" @click.prevent="browseCover"
                                    class="text-white bg-violet-400/70 hover:bg-white hover:bg-opacity-50 hover:text-gray-600 transition duration-200 rounded-full p-2">
                                    <Icon icon="heroicons:camera" class="w-6 h-6" />
                                </button>
                            </div>
                        </div>

                        <div class="relative my-2">
                            <input id="id-l12" type="text" v-model="form.name" placeholder="ชื่อกลุ่ม"
                                class="relative w-full h-12 px-4 pl-12 placeholder-transparent transition-all border-b outline-none focus-visible:outline-none peer border-slate-200 text-slate-500 autofill:bg-white invalid:border-pink-500 invalid:text-pink-500 focus:border-violet-500 focus:outline-none invalid:focus:border-pink-500 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400" />
                            <label for="id-l12"
                                class="cursor-text peer-focus:cursor-default peer-autofill:-top-2 absolute left-2 -top-2 z-[1] px-2 text-xs text-slate-400 transition-all before:absolute before:top-0 before:left-0 before:z-[-1] before:block before:h-full before:w-full before:bg-white before:transition-all peer-placeholder-shown:top-3 peer-placeholder-shown:left-10 peer-placeholder-shown:text-base peer-required:after:text-pink-500 peer-required:after:content-['\00a0*'] peer-invalid:text-pink-500 peer-focus:-top-2 peer-focus:left-2 peer-focus:text-xs peer-focus:text-violet-500 peer-invalid:peer-focus:text-pink-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400 peer-disabled:before:bg-transparent">
                                ชื่อกลุ่ม
                            </label>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="absolute w-6 h-6 top-3 left-4 stroke-slate-400 peer-disabled:cursor-not-allowed"
                                fill="none" aria-hidden="true" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.5" aria-labelledby="title-5 description-5" role="graphics-symbol">
                                <title id="title-5">Check mark icon</title>
                                <desc id="description-5">Icon description here</desc>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>

                        </div>
                        <div class="relative">
                            <textarea id="id-b02" type="text" v-model="form.description" rows="2"
                                placeholder="Write your message"
                                class="relative w-full px-4 py-2 text-sm placeholder-transparent transition-all border-b outline-none focus-visible:outline-none peer border-slate-200 text-slate-500 autofill:bg-white invalid:border-pink-500 invalid:text-pink-500 focus:border-violet-500 focus:outline-none invalid:focus:border-pink-500 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"></textarea>
                            <label for="id-b02"
                                class="cursor-text peer-focus:cursor-default absolute left-2 -top-2 z-[1] px-2 text-xs text-slate-400 transition-all before:absolute before:top-0 before:left-0 before:z-[-1] before:block before:h-full before:w-full before:bg-white before:transition-all peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-sm peer-required:after:text-pink-500 peer-required:after:content-['\00a0*'] peer-invalid:text-pink-500 peer-focus:-top-2 peer-focus:text-xs peer-focus:text-violet-500 peer-invalid:peer-focus:text-pink-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400 peer-disabled:before:bg-transparent">
                                คำอธิบาย (ถ้ามี)
                            </label>
                        </div>

                        <div class="text-right my-6">

                            <div class="flex justify-center gap-2">
                                <button  type="button" @click.prevent="onCancleCreateNewCourseGroup" class="inline-flex items-center justify-center w-full h-10 gap-2 px-5 text-sm font-medium tracking-wide text-white transition duration-300 rounded whitespace-nowrap bg-red-400 hover:bg-red-500 focus:bg-red-600 focus-visible:outline-none disabled:cursor-not-allowed disabled:border-yellow-300 disabled:bg-yellow-300 disabled:shadow-none">
                                    <span>ยกเลิก</span>
                                </button>
                                <button  type="submit" class="inline-flex items-center justify-center w-full h-10 gap-2 px-5 text-sm font-medium tracking-wide text-white transition duration-300 rounded whitespace-nowrap bg-violet-500 hover:bg-violet-600 focus:bg-violet-700 focus-visible:outline-none disabled:cursor-not-allowed disabled:border-violet-300 disabled:bg-violet-300 disabled:shadow-none">
                                    <span>ตกลง</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Modal with form -->
    </div>

</template>
