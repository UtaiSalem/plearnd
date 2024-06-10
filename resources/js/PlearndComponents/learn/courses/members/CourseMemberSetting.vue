<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { Icon } from '@iconify/vue';
import RadialProgress from "vue3-radial-progress";
import MemberAssignmentStatusViewer from '@/PlearndComponents/learn/courses/members/MemberAssignmentStatusViewer.vue';
import MemberQuestionStatusViewer from '@/PlearndComponents/learn/courses/members/MemberQuestionStatusViewer.vue';

const props = defineProps({
    member_info: Object,
});

const isLoading = ref(false);
const errorMessages = ref([]);

const form = ref({
    member_name:    props.member_info.member_name || usePage().props.auth.user.name,
    order_number:   props.member_info.order_number,
    member_code:    props.member_info.member_code,
});

// grade calculator return object of grade and color
const grade = computed(() => {
    // let score = props.member_info.achieved_score;
    let score = props.member_info.achieved_score * 100 / usePage().props.course.data.total_score;
    if (score >= 80) {
        return {    grade: 4,   color: 'text-green-500' };
    } else if (score >= 75) {
        return {    grade: 3.5, color: 'text-blue-500' };
    } else if (score >= 70) {
        return {     grade: 3,  color: 'text-blue-500' };
    } else if (score >= 65) {
        return {    grade: 2.5, color: 'text-blue-500' };
    } else if (score >= 60) {
        return {    grade: 2,   color: 'text-orange-500' };
    } else if (score >= 55) {
        return {    grade: 1.5, color: 'text-orange-500' };
    } else if (score >= 50) {
        return {    grade: 1,   color: 'text-orange-500' };
    } else {
        return {    grade: 0,   color: 'text-red-500' };
    } 
});

//percentage math.floor
const percentage = computed(() => {
    let percent = props.member_info.achieved_score * 100 / usePage().props.course.data.total_score;
    return `${Math.floor(percent)}%`;
});

const gradeStatus = computed(()=>{
    if(!props.member_info.order_number || 
        !props.member_info.achieved_score || 
        !props.member_info.member_code ||
        props.member_info.grade_progress === 5 ||
        (props.member_info.notes_comments && props.member_info.notes_comments.length) ||
        // (props.member_info.achieved_score + props.member_info.bonus_points) < 50 ||
        errorMessages.value.length
    ){
        return false;
    }else{
        return true;
    }
});

async function handleUpdateMemberInfo() {
    try {
        isLoading.value = true;
        let resp = await axios.patch(`/courses/${props.member_info.course_id}/members/${props.member_info.id}/update`, form.value);
        if (resp.data.success) {
            props.member_info.member_name = form.value.member_name;
            props.member_info.order_number = form.value.order_number;
            props.member_info.member_code = form.value.member_code;

            props.member_info.notes_comments = resp.data.notes_comments;
            
            isLoading.value = false;
            Swal.fire({
                icon: 'success',
                title: 'แก้ไขข้อมูลสำเร็จ',
                text: 'แก้ไขข้อมูลเรียบร้อยแล้ว',
                showConfirmButton: false,
                timer: 1000
            });
        }
    } catch (error) {
        console.log(error);
        isLoading.value = false;
    }
}

function setGradeStatus(errMsg){
    errorMessages.value.push(errMsg);
}

onMounted(()=>{
    if (!form.value.order_number) {
        errorMessages.value.push('ยังไม่กรอกเลขที่');
    }else if (!form.value.member_code) {
        errorMessages.value.push('ยังไม่กรอกเลขประจำตัว');
    }
});

</script>

<template>
    <div class="w-full mx-auto ">
        <!-- <div class="bg-white rounded-lg border-t-4 border-blue-500 p-4 my-4 font-bold uppercase text-2xl text-gray-700">
            การตั้งค่าสมาชิก
        </div> -->
        <div class="bg-white rounded-lg p-4 text-gray-600 space-y-2 mt-4">
            <h2 class="text-xl font-semibold leading-7 ">ข้อมูลสมาชิก</h2>
        </div>
        <div class="bg-white rounded-lg p-4 text-gray-600 space-y-2 mt-4">
            <div class="grid grid-cols-1 w-full">
                <label :for="`username-${member_info.id}`" class="flex text-sm font-medium leading-6 text-gray-900">
                    <p class="font-semibold">ชื่อ-นามสกุล 
                        <small>(ภาษาไทย ตรงตามบัตรประชาชน)</small>
                    </p>
                </label>
                <div class="mt-0.5">
                    <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 ">
                        <input type="text" v-model="form.member_name" name="username" :id="`username-${member_info.id}`" autocomplete="username"
                            class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6"
                            placeholder="" />
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center w-full space-x-2">
                <div>
                    <label :for="`username-${member_info.id}`" class="flex text-sm font-medium leading-6 text-gray-900">
                        <p class="font-semibold">เลขที่</p>
                    </label>
                    <div class="mt-0.5">
                        <input type="number" :id="`username-${member_info.id}`" v-model="form.order_number" min="1" class="block w-16 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                    </div>
                </div>
                <div>
                    <label :for="`member-code-${member_info.id}`" class="flex text-sm font-medium leading-6 text-gray-900">
                        <p class="font-semibold">เลขประจำตัว</p>
                    </label>
                    <div class="mt-0.5">
                        <input type="number" :id="`member-code-${member_info.id}`" v-model="form.member_code" min="1" class="block w-24 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                    </div>
                </div>
                <div>
                    <label :for="`member-group-${member_info.id}`" class="flex text-sm font-medium leading-6 text-gray-900">
                        <p class="font-semibold">กลุ่ม/ห้อง</p>
                    </label>
                    <div class="mt-0.5">
                        <input type="text" :id="`member-group-${member_info.id}`" disabled v-model="props.member_info.group.name" min="1" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                    </div>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-x-6">
                <!-- <button type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel</button> -->
                <button type="button" @click.prevent="handleUpdateMemberInfo" :disabled="isLoading" 
                    :class="{'cursor-not-allowed opacity-50 bg-slate-700': isLoading}"
                    class="rounded-md bg-indigo-600 flex items-center justify-center w-16 space-x-2 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    <span v-if="isLoading" class="animate-spin px-2"><Icon icon="uiw:loading" class="w-5 h-5"></Icon></span>
                    <span v-else>บันทึก</span> 
                    <!-- <span><Icon icon="svg-spinners:bars-rotate-fade" class="w-6 h-6"></Icon></span> -->
                </button>
            </div>
        </div>
        <div class="bg-white rounded-lg p-4 text-gray-600 space-y-2 mt-4">
            <h2 class="text-xl font-semibold leading-7 text-gray-600">ข้อมูลการเรียน</h2>
        </div>
        <div class="bg-white rounded-lg p-4 text-gray-600 space-y-2 mt-4">
            <h2 class="text-md font-bold leading-7 text-gray-600">แบบฝึกหัด</h2>
            <MemberAssignmentStatusViewer 
                :member_info="props.member_info"
                @handleEmptyPoints="(msg)=>setGradeStatus(msg)"
            />
        </div>
        <div class="bg-white rounded-lg p-4 text-gray-600 space-y-2 mt-4">
            <h2 class="text-md font-bold leading-7 text-gray-600">แบบทดสอบ</h2>
            <MemberQuestionStatusViewer 
                @sendErrorsMsg="(msg)=>setGradeStatus(msg)"
            />
        </div>
        <div class="bg-white rounded-lg p-4 text-gray-600 space-y-2 mt-4">
            <div class="grid grid-cols-1 w-full">
                <label :for="`member-total-score-${member_info.id}`" class="flex text-sm font-medium leading-6 text-gray-600">
                    <p class="font-semibold">คะแนนรวม/เกรด</p>
                </label>
                <div>
                    <ul>
                        <li v-for="(msg, index) in errorMessages" :key="index" class="text-red-600 text-sm flex items-center justify-start space-x-1.5">
                            <span><Icon icon="typcn:warning-outline"></Icon></span>
                            <span>{{ msg }}</span>
                        </li>
                        <li v-if="props.member_info.notes_comments && props.member_info.notes_comments.length > 0" class="text-red-600 text-sm flex items-center justify-start space-x-1.5">
                            <span><Icon icon="typcn:warning-outline"></Icon></span>
                            <span>{{ props.member_info.notes_comments }}</span>
                        </li>
                    </ul>
                </div>
                <div class="mt-0.5 mx-auto">
                    <RadialProgress 
                    :diameter="120"
                    :completed-steps="member_info.achieved_score"
                    :total-steps="$page.props.course.data.total_score">
                        <!-- Your inner content here -->
                        <div class="contents">
                            <span>{{ percentage }}</span>
                            <small v-if="$page.props.course.data.status === 3" class="text-4xl font-bold" :class="grade.color">
                                <!-- {{ grade.grade }}  -->
                                {{ gradeStatus ? grade.grade : 'ร'}} 
                            </small>
                            <small v-else class="text-md text-yellow-400 font-bold">{{ 'รอประมวลผล' }} </small>
                            <small class="text-md font-bold" :class="grade.color">เกรด</small>
                        </div>
                    </RadialProgress>
                </div>
                <div class="mx-auto">
                    <span class="font-bold" :class="grade.color">{{ member_info.achieved_score + " / " + $page.props.course.data.total_score }}</span>
                </div>
            </div>
        </div>

        <!-- <div class="bg-white rounded-lg p-4 text-gray-600 space-y-2 mt-4" v-if="errorMessages.length">
            <div v-for="(msg, index) in errorMessages" :key="index">
                <div class="text-red-600 flex items-center justify-center">
                    <span><iconify-icon icon="typcn:warning-outline"></iconify-icon></span>
                    <span>
                        {{ msg }}
                    </span>
                </div>
            </div>
        </div> -->
    </div>
</template>
