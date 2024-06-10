<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { Icon } from '@iconify/vue';

import MemberAssignmentsAnswerProgress from '@/PlearndComponents/learn/courses/progress/MemberAssignmentAnswerProgress.vue';
import MemberQuizzesAnswerProgress from '@/PlearndComponents/learn/courses/progress/MemberQuizzesAnswerProgress.vue';
import MemberGradeProgress from './MemberGradeProgress.vue';
import MemberProgressComments from './MemberProgressComments.vue';

const props = defineProps({
    member: Object,
});

const isLoading = ref(false);
const isAllAnswersCompleted = ref(true);
const errorMessages = ref([]);

const totalMemberScore = computed(() => {
    return props.member.achieved_score + props.member.bonus_points;
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
        // (props.member.achieved_score + props.member.bonus_points) < 50 ||
        errorMessages.value.length
    ){
        return false;
    }else{
        return true;
    }
});

const bonusPointsForm = ref({
    bonusPoints: props.member.bonus_points ?? 0,
    gradeProgress: gradeProgress(totalMemberScore).grade,
});

const handleBonusPointsInput = async () => {
    isLoading.value = true;
    try {
        let response = await axios.patch(`/courses/${props.member.course_id}/members/${props.member.id}/bonus-points`, {
            bonus_points: bonusPointsForm.value.bonusPoints,
        });

        if (response.data.success) {
            props.member.achieved_score += bonusPointsForm.value.bonusPoints;
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

// function setGradeStatus(){
//     isAllAnswersCompleted.value = false;
// }
function setGradeStatus(errMsg){
    errorMessages.value.push(errMsg);
}

</script>

<template>
    <th scope="row" :class="gradeStatus ? 'bg-green-100 text-green-600' : 'bg-red-50 text-red-600'"
        class="border border-slate-300 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">
        <p class=" min-w-fit">{{ member.order_number }}</p>
    </th>
    <td :class="gradeStatus ? 'bg-green-100 text-green-600' : 'bg-red-50 text-red-600'"
    class="px-2 py-1  border border-slate-300 text-center">
        <p>{{ member.member_code }}</p>
    </td>
    <td :class="gradeStatus ? 'bg-green-100 text-green-600' : 'bg-red-50 text-red-600'"
    class="pl-2 py-1 border border-slate-300 text-start">
        <p class="w-46 min-w-fit">{{ member.user.name }}</p>
    </td>

    <td :class="gradeStatus ? 'bg-green-100 text-green-600' : 'bg-red-50 text-red-600'"
    v-for="(assignment, asmIndx) in $page.props.assignments.data" :key="assignment.id"
        class="px-2 py-1 border border-slate-300 text-center w-8"
    >
        <!-- {{ member.answers.filter((answer)=> answer.assignment_id === assignment.id).length ? member.answers.filter((answer)=> answer.assignment_id === assignment.id)[0].points + '/' + assignment.points : 'ยังไม่ส่งงาน' }} -->
        <MemberAssignmentsAnswerProgress 
            :member_info="member" 
            :assignment="assignment" 
            :answers="assignment.answers.filter((answer) => answer.student.id === props.member.user.id)"
            @handleEmptyPoints="setGradeStatus(' ภาระงานที่ ' + (asmIndx+1) + ' ไม่มีคะแนน')"
        />
    </td>

    <td :class="gradeStatus ? 'bg-green-100 text-green-600' : 'bg-red-50 text-red-600'"
    v-for="(quiz, qzIndx) in $page.props.quizzes.data" :key="quiz.id" 
    class="px-2 py-1 border border-slate-300 text-center w-8"
    
    >
        <MemberQuizzesAnswerProgress 
            :quiz="quiz" 
            :member_id="member.user.id"
            @handleEmptyPoints="setGradeStatus(' แบบทดสอบที่ ' + (qzIndx+1) + ' ไม่มีคะแนน')"
        />
    </td>

    <td :class="gradeStatus ? 'bg-green-100 text-green-600' : 'bg-red-50 text-red-600'"
    class="px-2 py-0 border border-slate-300 text-center">
        {{ member.achieved_score }}
    </td>

    <td :class="gradeStatus ? 'bg-green-100 text-green-600' : 'bg-red-50 text-red-600'"
    class="px-2 py-0 border border-slate-300 text-center">
        <form @submit.prevent="handleBonusPointsInput" class="flex">
            <!-- <input type="number" name="bonusPoints" v-model="bonusPointsForm.bonusPoints" :id="`member-${member.id}-bonus-points`" autocomplete="bonus-points" class="block w-16 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"> -->
            <input type="number" name="bonusPoints" v-model="member.bonus_points" :id="`member-${member.id}-bonus-points`" autocomplete="bonus-points" class="block w-16 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            <button type="submit" class="rounded-md  px-1 py-0 ml-1 text-sm font-base text-green-600 shadow-md  focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                <Icon icon="icomoon-free:spinner" class="w-5 h-5 animate-spin" v-if="isLoading" />
                <Icon icon="fluent:save-24-regular" class="w-5 h-5" v-else />
            </button>
        </form>
    </td>

    <td :class="gradeStatus ? 'bg-green-100 text-green-600' : 'bg-red-50 text-red-600'"
    class="px-2 py-0 border border-slate-300 text-center">
        {{ totalMemberScore }}
    </td>

    <td :class="gradeStatus ? 'bg-green-100 text-green-600' : 'bg-red-50 text-red-600'"
    class="px-2 py-0 border border-slate-300 text-center text-lg font-bold">
        <p class="rounded-md"
            :class="`bg-${gradeProgress(totalMemberScore).color}-200 text-${gradeProgress(totalMemberScore).color}-500`">
            {{  gradeStatus ? gradeProgress(totalMemberScore).grade : 'ร' }}
        </p>
    </td>

    <td class="px-2 py-0 border border-slate-300 text-center">
        <MemberGradeProgress 
            :grade="gradeProgress(totalMemberScore).grade"
            :gradeStatus="gradeStatus"
            :member_id="member.id"
            :course_id="member.course_id"

            v-model:grade-progress="props.member.grade_progress"
        />
    </td>
    <td class="px-2 py-0 border border-slate-300 text-center">
        <MemberProgressComments
            :member_id="props.member.id"
            :course_id="props.member.course_id"
            :notes_comments="props.member.notes_comments"

            v-model:notes-comments="props.member.notes_comments"
        />
    </td>
</template>
