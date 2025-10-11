<script setup>
import { ref, computed } from 'vue';
// import { useRouter } from 'vue-router';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { Icon } from '@iconify/vue';

import MemberAssignmentsAnswerProgress from '@/PlearndComponents/learn/courses/progress/MemberAssignmentAnswerProgress.vue';
import MemberQuizzesAnswerProgress from '@/PlearndComponents/learn/courses/progress/MemberQuizzesAnswerProgress.vue';
import MemberGradeProgress from './MemberGradeProgress.vue';
import MemberProgressComments from './MemberProgressComments.vue';

const props = defineProps({
    member: Object,
    isCourseAdmin: Boolean,
});

// const router = useRouter();
const isLoading = ref(false);
const isAllAnswersCompleted = ref(true);
const errorMessages = ref([]);
const refBonusPoints = ref(props.member.bonus_points??0);
const isLinkingToMemberSettingPage = ref(false);
const refOrderNumber = ref(props.member.order_number);
const isOrderLoading = ref(false);
const totalMemberScore = computed(() => {
    return props.member.achieved_score + refBonusPoints.value ;
});

const gradeProgress = (score) => {
    if (score >= 80) {
        return { grade: 4, color: 'green' };
    } else if (score >= 75) {
        return { grade: 3.5, color: 'blue' };
    } else if (score >= 70) {
        return { grade: 3, color: 'blue' };
    } else if (score >= 65) {
        return { grade: 2.5, color: 'blue' };
    } else if (score >= 60) {
        return { grade: 2, color: 'orange' };
    } else if (score >= 55) {
        return { grade: 1.5, color: 'orange' };
    } else if (score >= 50) {
        return { grade: 1, color: 'orange' };
    } else {
        return { grade: 0, color: 'red' };
    }
};

const gradeStatus = computed(()=>{
    if(!props.member.order_number || 
        !props.member.achieved_score || 
        !props.member.member_code ||
        !isAllAnswersCompleted.value ||
        props.member.grade_progress === 5 ||
        (props.member.notes_comments && props.member.notes_comments.length) ||
        errorMessages.value.length
    ){
        return false;
    }else{
        return true;
    }
});

const gradePercentage = computed(()=>{
    if(props.member.achieved_score){
        return Math.round(((props.member.achieved_score + refBonusPoints.value) / usePage().props.course.data.total_score) * 100);
    }else{
        return 0;
    }
});

const bonusPointsForm = ref({
    bonusPoints: 0,
    gradeProgress: gradeProgress(totalMemberScore).grade,
});

const orderNumberForm = ref({
    orderNumber: props.member.order_number || 0,
});

const handleBonusPointsInputSubmit = async () => {
    isLoading.value = true;
    try {
        let response = await axios.patch(`/courses/${props.member.course_id}/members/${props.member.id}/bonus-points`, {
            bonus_points: bonusPointsForm.value.bonusPoints,
        });

        if (response.data.success) {
            refBonusPoints.value += bonusPointsForm.value.bonusPoints;
            bonusPointsForm.value.bonusPoints = 0;
            Swal.fire({
                icon: 'success',
                title: 'บันทึกคะแนนโบนัสสำเร็จ',
                showConfirmButton: false,
                timer: 1500,
            });
            isLoading.value = false;
        }
        
    } catch (error) {
        console.log(error);
        isLoading.value = false;
        Swal.fire({
            icon: 'error',
            title: 'บันทึกคะแนนโบนัสไม่สำเร็จ',
            showConfirmButton: false,
            timer: 1500,
        });
    }
    isLoading.value = false;
}

const handleOrderNumberInputSubmit = async () => {
    isOrderLoading.value = true;
    try {
        let response = await axios.patch(`/courses/${props.member.course_id}/members/${props.member.id}/order-number`, {
            order_number: orderNumberForm.value.orderNumber,
        });

        if (response.data.success) {
            refOrderNumber.value = orderNumberForm.value.orderNumber;
            props.member.order_number = orderNumberForm.value.orderNumber;
            Swal.fire({
                icon: 'success',
                title: 'บันทึกเลขที่สำเร็จ',
                showConfirmButton: false,
                timer: 1500,
            });
            isOrderLoading.value = false;
        }
        
    } catch (error) {
        console.log(error);
        isOrderLoading.value = false;
        Swal.fire({
            icon: 'error',
            title: 'บันทึกเลขที่ไม่สำเร็จ',
            showConfirmButton: false,
            timer: 1500,
        });
    }
    isOrderLoading.value = false;
}

function setGradeStatus(errMsg){
    errorMessages.value.push(errMsg);
}

const navigateToMemberSettings = (courseId, memberId) => {
    isLinkingToMemberSettingPage.value = true;
    try {
        // route('course.member.settings.page.show', { course: courseId, course_member: memberId });
        window.location.href = `/courses/${courseId}/members/${memberId}/member-settings`;
    } catch (error) {
        console.log(error);
    }
}

</script>

<template>
    <td scope="row" :class="gradeStatus ? 'bg-green-200 text-green-800' : 'bg-red-50 text-red-600'"
        class="border border-slate-300 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">
        <div v-if="!isCourseAdmin" class="w-12 min-w-fit">
            {{ member.order_number }}
        </div>
        <form v-else @submit.prevent="handleOrderNumberInputSubmit" class="flex items-center justify-center">
            <input type="number" name="orderNumber" v-model="orderNumberForm.orderNumber" :id="`member-${member.id}-order-number`" autocomplete="order-number" class="block w-16 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            <button type="submit" class="rounded-md  px-1 py-0 ml-1 text-sm font-base text-green-800 shadow-md  focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-800">
                <Icon icon="icomoon-free:spinner" class="w-5 h-5 animate-spin" v-if="isOrderLoading" />
                <Icon icon="fluent:save-24-regular" class="w-5 h-5" v-else />
            </button>
        </form>
    </td>
    <td :class="gradeStatus ? 'bg-green-200 text-green-800' : 'bg-red-50 text-red-600'"
        class="px-1 py-1  border border-slate-300 text-center">
        <p>{{ member.member_code }}</p>
    </td>
    <td :class="gradeStatus ? 'bg-green-200 text-green-800' : 'bg-red-50 text-red-600'"
        class="pl-2 py-1 border border-slate-300 text-start w-48 ">
        {{ member.member_name?? member.user.name }}
    </td>

    <td :class="gradeStatus ? 'bg-green-200 text-green-800' : 'bg-red-50 text-red-600'"
        v-for="(assignment, asmIndx) in $page.props.assignments.data" :key="assignment.id"
        class="px-2 py-1 border border-slate-300 text-center w-8"
    >
        <MemberAssignmentsAnswerProgress 
            :member_info="member" 
            :assignment="assignment" 
            :answers="assignment.answers.filter((answer) => answer.student.id === props.member.user.id)"

            @handleEmptyPoints="setGradeStatus(' ภาระงานที่ ' + (asmIndx+1) + ' ไม่มีคะแนน')"
        />
    </td>

    <td :class="gradeStatus ? 'bg-green-200 text-green-800' : 'bg-red-50 text-red-600'"
        v-for="(quiz, qzIndx) in $page.props.quizzes.data" :key="quiz.id" 
        class="px-2 py-1 border border-slate-300 text-center w-8">
        <MemberQuizzesAnswerProgress 
            :quiz="quiz" 
            :member_id="member.user.id"
            @handleEmptyPoints="setGradeStatus(' แบบทดสอบที่ ' + (qzIndx+1) + ' ไม่มีคะแนน')"
        />
    </td>

    <td :class="gradeStatus ? 'bg-green-200 text-green-800' : 'bg-red-50 text-red-600'"
        class="px-2 py-0 border border-slate-300 text-center">
        {{ member.achieved_score }}
    </td>

    <td :class="gradeStatus ? 'bg-green-200 text-green-800' : 'bg-red-50 text-red-600'"
        class="px-2 py-0 border border-slate-300 text-center">
        <form @submit.prevent="handleBonusPointsInputSubmit" class="flex items-center justify-center">
            <span class="mr-2">{{ refBonusPoints }}</span>
            <input type="number" name="bonusPoints" v-model="bonusPointsForm.bonusPoints" :id="`member-${member.id}-bonus-points`" autocomplete="bonus-points" class="block w-16 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            <button type="submit" class="rounded-md  px-1 py-0 ml-1 text-sm font-base text-green-800 shadow-md  focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-800">
                <Icon icon="icomoon-free:spinner" class="w-5 h-5 animate-spin" v-if="isLoading" />
                <Icon icon="fluent:save-24-regular" class="w-5 h-5" v-else />
            </button>
        </form>
    </td>

    <td :class="gradeStatus ? 'bg-green-200 text-green-800' : 'bg-red-50 text-red-600'"
        class="px-2 py-0 border border-slate-300 text-center">
        {{ totalMemberScore + '/' + $page.props.course.data.total_score }}
    </td>

    <td :class="gradeStatus ? 'bg-green-200 text-green-800' : 'bg-red-50 text-red-600'"
        class="px-2 py-0 border border-slate-300 text-center">
        {{ gradePercentage }}
    </td>

    <td :class="gradeStatus ? 'bg-green-200 text-green-800' : 'bg-red-50 text-red-600'"
        class="px-2 py-0 border border-slate-300 text-center text-lg font-bold">
        <span class="rounded-md px-2" v-if="gradeStatus"
            :class="`bg-${gradeProgress(gradePercentage).color}-300 text-${gradeProgress(gradePercentage).color}-800`">
            {{  gradeProgress(gradePercentage).grade }}
        </span>
        <span class="rounded-md px-2" v-else
            :class="`bg-${gradeProgress(gradePercentage).color}-300 text-${gradeProgress(gradePercentage).color}-800`">
            ร
        </span>
    </td>

    <td class="p-1 flex justify-center">
        <MemberGradeProgress 
            :grade="gradeProgress(totalMemberScore).grade"
            :gradeStatus="gradeStatus"
            :member_id="member.id"
            :course_id="member.course_id"

            v-model:grade-progress="props.member.grade_progress"
        />
    </td>
    <td class="p-1 border">
        <MemberProgressComments
            :member_id="props.member.id"
            :course_id="props.member.course_id"
            :notes_comments="props.member.notes_comments"

            v-model:notes-comments="props.member.notes_comments"
        />
    </td>

    <div v-if="isLinkingToMemberSettingPage" class="fixed top-0 left-0 z-20 flex items-center justify-center w-full h-full modal">
        <div class="absolute w-full h-full bg-gray-900 opacity-75 modal-overlay"></div>
        <div class="flex items-center justify-center h-64">
            <Icon icon="svg-spinners:bars-rotate-fade" class="z-30 w-32 h-32 text-white" />
        </div>
    </div>
</template>