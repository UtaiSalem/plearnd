<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3';
import DotsDropdownMenu from '@/PlearndComponents/accessories/DotsDropdownMenu.vue';
import { Icon } from '@iconify/vue';
import Swal from 'sweetalert2';

const props = defineProps({
    group: Object,
    group_index: Number,
    courseMemberOfAuth: Object,
});

const emit = defineEmits([
    'edit-course-group',
    'delete-course-group',
    'request-tobe-group-member',
    'request-tobe-group-unmember'
]);

const isLoading = ref(false);
const showGroupOptions = ref(false);
const openEditGroupModal = ref(false);

const authIsGroupMember = computed(() => {
    return props.courseMemberOfAuth ? props.courseMemberOfAuth.group_member_status===1:false;
})

const tempCover = ref(props.group.image_url ||'/storage/images/courses/covers/default_cover.jpg');
const coverInput = ref(null);

const browseCover = () => { coverInput.value.click() };
function onCoverInputChange(event){
  form.value.image_url = event.target.files[0];
  tempCover.value = URL.createObjectURL(event.target.files[0]);
}

const form = ref({
    name: props.group.name,
    description: props.group.description,
    image_url: props.group.image_url
});


async function handleGroupMemberRequest(){
    // console.log(`/courses/${usePage().props.course.data.id}/groups/${props.group.id}/members`);
    try {

        if (usePage().props.auth.user.pp < usePage().props.course.data.tuition_fees) {
            Swal.fire(
                'ขออภัย',
                'คุณมีแต้มสะสมไม่เพียงพอที่จะสมัครเข้าร่วมกลุ่มรายวิชานี้ , กรุณาเติมแต้มสะสมก่อน',
                'warning'
            );
            return;
        }

        isLoading.value = true;

        let memberResp = await axios.post(`/courses/${usePage().props.course.data.id}/groups/${props.group.id}/members`);

        if (memberResp.data.success) {
            usePage().props.courseMemberOfAuth  =memberResp.data.courseMemberOfAuth;
            usePage().props.groups.data[props.group_index].members = memberResp.data.group.members;
            props.group.members                 = memberResp.data.group.members;
            memberResp.data.courseMemberOfAuth.course_member_status == 1 ? usePage().props.course.data.isMember=true : usePage().props.course.data.isMember=false;

            Swal.fire(
                'เสร็จสิ้น',
                'ขอเป็นสมาชิกเรียบร้อยแล้ว',
                'success'
                );
        }else{
            Swal.fire(
                'ขออภัย',
                'เกิดข้อผิดพลาดในการขอเข้าร่วมกลุ่ม',
                memberResp.data.msg
            );
        }

        isLoading.value = false;
    } catch (error) {
        console.log(error);
        isLoading.value = false;
    }
}


async function handleUnmemberGroupRequest(){
    try {
        isLoading.value = true;
        // emit('request-tobe-group-unmember');
        let unmemberGroupResp = await axios.delete(`/courses/${usePage().props.course.data.id}/groups/${props.group.id}/members/${props.courseMemberOfAuth.id}`);
        if (unmemberGroupResp.data.success) {
           
            usePage().props.courseMemberOfAuth          = unmemberGroupResp.data.courseMember;
            usePage().props.course.data.status          = unmemberGroupResp.data.courseMember.status;
            usePage().props.course.data.member_status   = unmemberGroupResp.data.courseMember.member_status;
            usePage().props.groups.data[props.group_index].members   = unmemberGroupResp.data.group.members;

            unmemberGroupResp.data.courseMember.course_member_status == 1 ? usePage().props.course.data.isMember=true : usePage().props.course.data.isMember=false;
            // usePage().props.courseMemberOfAuth = null;
            Swal.fire(
                'เสร็จสิ้น',
                'ออกจากสมาชิกกลุ่มเรียบร้อยแล้ว',
                'success'
            );

        }
        showGroupOptions.value = false;
        isLoading.value = false;
    } catch (error) {
        console.log(error);
        isLoading.value = false;
    }
}

function onCancleEditCourseGroup(){
    openEditGroupModal.value = false;
    form.value.name = props.group.name;
    form.value.description = props.group.description;
    form.value.image_url = props.group.image_url;
}

//Submit update group
async function handleFormSubmit(){
    try {
        let updateResp = await axios.patch(`/courses/${usePage().props.course.data.id}/groups/${props.group.id}`, form.value);
        if (updateResp.data && updateResp.data.success) {
            props.group.name = form.value.name;
            props.group.description = form.value.description;
            props.group.image_url = form.value.image_url;
            emit('edit-course-group');
            openEditGroupModal.value = false;
        }
    } catch (error) {
        console.log(error);
    }
}

</script>

<template>
    <div
        class="sm:max-w-sm md:max-w-sm lg:max-w-sm xl:max-w-sm sm:mx-auto md:mx-auto lg:mx-auto xl:mx-auto  bg-white shadow-xl rounded-lg text-gray-900 overflow-t-hidden">

        <div class="relative rounded-t-lg h-32 ">
            <!-- <img class="object-cover object-top w-full" :src="props.group.image_url ? '/storage/images/courses/groups/'+props.group.image_url :'/storage/images/courses/covers/default_cover.jpg'" alt='Mountain'> -->
            <img class="object-cover object-top w-full rounded-t-lg h-40" :src="props.group.image_url ? '/storage/images/courses/groups/'+props.group.image_url :'/storage/images/courses/covers/default_cover.jpg'" alt='Mountain'>
            <div class="absolute top-2 right-2 text-end" v-if="$page.props.isCourseAdmin">
                <!-- <DotsDropdownMenu @delete-model="onDeleteCourseGroup(props.group.id, i)" > -->
                <DotsDropdownMenu
                    @edit-model="openEditGroupModal=true" 
                    @delete-model="emit('delete-course-group')" 
                >
                    <template #editModel><div>แก้ไขกลุ่ม</div></template>
                    <template #deleteModel><div>ลบกลุ่ม</div></template>
                </DotsDropdownMenu>
            </div>
        </div>
        <!-- <div class="mx-auto w-32 h-32 relative -mt-16 border-4 border-white rounded-full overflow-hidden">
                <img class="object-cover object-center h-32" src='https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=400&fit=max&ixid=eyJhcHBfaWQiOjE0NTg5fQ' alt='Woman looking front'>
            </div> -->
        <div class="text-center pt-3 mt-7">
            <h2 class="font-semibold">{{ props.group.name }}</h2>
            <p class="text-gray-500">{{ props.group.description }}</p>
        </div>

        <ul class="py-4 mt-2 text-gray-700 flex items-center justify-around">
            <!-- <li class="flex flex-col items-center justify-around">
            <svg class="w-4 fill-current text-blue-900" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path
                    d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
            </svg>
            <div>2k</div>
        </li> -->
            <li class="flex flex-col items-center justify-between">
                <svg class="w-4 fill-current text-blue-900" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path
                        d="M7 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0 1c2.15 0 4.2.4 6.1 1.09L12 16h-1.25L10 20H4l-.75-4H2L.9 10.09A17.93 17.93 0 0 1 7 9zm8.31.17c1.32.18 2.59.48 3.8.92L18 16h-1.25L16 20h-3.96l.37-2h1.25l1.65-8.83zM13 0a4 4 0 1 1-1.33 7.76 5.96 5.96 0 0 0 0-7.52C12.1.1 12.53 0 13 0z" />
                </svg>
                <div>{{ $page.props.groups.data[props.group_index].members.length }}</div>
                <p>สมาชิก</p>
            </li>
            <!-- <li class="flex flex-col items-center justify-around">
            <svg class="w-4 fill-current text-blue-900" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path
                    d="M9 12H1v6a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-6h-8v2H9v-2zm0-1H0V5c0-1.1.9-2 2-2h4V2a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1h4a2 2 0 0 1 2 2v6h-9V9H9v2zm3-8V2H8v1h4z" />
            </svg>
            <div>15</div>
        </li> -->
        </ul>

        <div v-if="!$page.props.isCourseAdmin" class="p-2 border-t mt-2">
            <div v-if="authIsGroupMember && props.courseMemberOfAuth.group_id === props.group.id" class="group relative cursor-pointer w-56 mx-auto">
                <button @click.prevent="showGroupOptions = !showGroupOptions" :disabled="isLoading"
                    class="flex mx-auto rounded-full bg-green-500 hover:shadow-lg font-base text-white px-4 py-2">
                    <Icon icon="uiw:loading" v-if="isLoading" class="animate-spin h-5 w-5 mx-2"/>
                    เป็นสมาชิกแล้ว
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-6 w-6 mx-2 text-white transition-transform duration-300"  :class="{'rotate-180':showGroupOptions}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
                <div v-if="showGroupOptions"
                    class="absolute z-[50] flex w-full mx-auto flex-col py-1 text-gray-800 shadow-xl ">
                    <button @click.prevent="handleUnmemberGroupRequest" :disabled="isLoading"
                        class="py-2 border-b bg-red-200 rounded-md font-semibold text-red-500 hover:text-red-600 ">
                        ออกจากกลุ่ม
                    </button>                       
                </div>

                <!-- <div v-if="props.courseMemberOfAuth.status==='1'" 
                    :class="showGroupOptions ? 'visible ': 'invisible'"
                    class="absolute z-[80] flex w-3/4 mx-auto flex-col py-1 text-gray-800 shadow-xl ">
                    <button @click.prevent="onRequestToBeUnMember"
                        class="py-2 border-b bg-red-200 rounded-md font-semibold text-red-500 hover:text-red-600 ">
                        ออกจากสมาชิก
                    </button>                       
                </div> -->
            </div>
            <button v-else-if="props.courseMemberOfAuth && props.courseMemberOfAuth.group_member_status===0 && props.courseMemberOfAuth.group_id === props.group.id" :disabled="isLoading"
                class="block mx-auto rounded-full bg-yellow-500 hover:shadow-lg font-base text-white px-4 py-2">
                รอการตอบรับ
            </button>
            <div  v-else>
                <button
                    :disabled="isLoading"
                    @click.prevent="handleGroupMemberRequest"
                    class="flex mx-auto rounded-full bg-blue-600 hover:shadow-lg font-semibold text-white px-4 py-2">
                    <Icon icon="uiw:loading" v-if="isLoading" class="animate-spin h-5 w-5 mr-2"/>
                    ขอเข้าร่วมกลุ่ม
                </button>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="$page.props.isCourseAdmin && openEditGroupModal" class="fixed top-0 left-0 z-20 flex items-center justify-center w-screen h-screen bg-slate-800/80 backdrop-blur-lg" aria-labelledby="header-4a content-4a" aria-modal="true" tabindex="-1" role="dialog">
            <div class="flex max-h-[90vh] max-w-md flex-col gap-2 overflow-hidden rounded-lg bg-white px-4 py-3 text-slate-500 shadow-xl shadow-slate-700/10" id="modal" role="document">
                <!-- Modal header -->
                <header id="header-4a" class="flex items-center">
                    <h3 class="flex-1 text-lg font-medium text-slate-700">แก้ไขกลุ่ม</h3>
                    <button @click.prevent="onCancleEditCourseGroup" class="inline-flex items-center justify-center h-10 gap-2 px-5 text-sm font-medium tracking-wide transition duration-300 rounded-full focus-visible:outline-none justify-self-center whitespace-nowrap bg-violet-100 text-violet-500 hover:bg-violet-200 hover:text-violet-600 focus:bg-violet-200 focus:text-violet-700 disabled:cursor-not-allowed disabled:text-violet-300 disabled:shadow-none disabled:hover:bg-transparent" aria-label="close dialog">
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
                                <button  type="button" @click.prevent="onCancleEditCourseGroup" class="inline-flex items-center justify-center w-full h-10 gap-2 px-5 text-sm font-medium tracking-wide text-white transition duration-300 rounded whitespace-nowrap bg-red-400 hover:bg-red-500 focus:bg-red-600 focus-visible:outline-none disabled:cursor-not-allowed disabled:border-yellow-300 disabled:bg-yellow-300 disabled:shadow-none">
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
