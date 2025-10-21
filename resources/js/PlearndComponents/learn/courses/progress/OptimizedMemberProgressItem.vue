<script setup>
import { ref, computed, watch } from "vue";
import { Icon } from "@iconify/vue";

import OptimizedMemberAssignmentAnswerProgress from "./OptimizedMemberAssignmentAnswerProgress.vue";
import OptimizedMemberQuizAnswerProgress from "./OptimizedMemberQuizAnswerProgress.vue";
import OptimizedMemberGradeProgress from "./OptimizedMemberGradeProgress.vue";
import OptimizedMemberProgressComments from "./OptimizedMemberProgressComments.vue";

const props = defineProps({
  member: Object,
  isCourseAdmin: Boolean,
  assignments: Array,
  quizzes: Array,
  course: Object,
});

const emit = defineEmits(['member-updated']);

// State management
const isLoading = ref(false);
const isOrderLoading = ref(false);
const errorMessages = ref([]);

// Local reactive data to avoid unnecessary re-renders
const localBonusPoints = ref(props.member.bonus_points ?? 0);
const localOrderNumber = ref(props.member.order_number);
const localGradeProgress = ref(props.member.grade_progress);
const localNotesComments = ref(props.member.notes_comments);

// Computed properties
const totalMemberScore = computed(() => {
  return props.member.achieved_score + localBonusPoints.value;
});

const gradeProgress = (score) => {
  if (score >= 80) {
    return { grade: 4, color: "teal" };
  } else if (score >= 75) {
    return { grade: 3.5, color: "cyan" };
  } else if (score >= 70) {
    return { grade: 3, color: "sky" };
  } else if (score >= 65) {
    return { grade: 2.5, color: "indigo" };
  } else if (score >= 60) {
    return { grade: 2, color: "amber" };
  } else if (score >= 55) {
    return { grade: 1.5, color: "orange" };
  } else if (score >= 50) {
    return { grade: 1, color: "stone" };
  } else {
    return { grade: 0, color: "slate" };
  }
};

const gradeStatus = computed(() => {
  if (
    !props.member.order_number ||
    !props.member.achieved_score ||
    !props.member.member_code ||
    props.member.grade_progress === 5 ||
    (props.member.notes_comments && props.member.notes_comments.length) ||
    errorMessages.value.length
  ) {
    return false;
  } else {
    return true;
  }
});

const gradePercentage = computed(() => {
  if (props.member.achieved_score) {
    return Math.round(
      ((props.member.achieved_score + localBonusPoints.value) /
        props.course.total_score) *
        100
    );
  } else {
    return 0;
  }
});

// Form data
const bonusPointsForm = ref({
  bonusPoints: 0,
});

const orderNumberForm = ref({
  orderNumber: props.member.order_number || 0,
});

// Methods
const handleBonusPointsInputSubmit = async () => {
  if (isLoading.value || bonusPointsForm.value.bonusPoints === 0) return;
  
  isLoading.value = true;
  try {
    const response = await axios.patch(
      `/courses/${props.member.course_id}/members/${props.member.id}/bonus-points`,
      {
        bonus_points: bonusPointsForm.value.bonusPoints,
      }
    );

    if (response.data.success) {
      localBonusPoints.value += bonusPointsForm.value.bonusPoints;
      bonusPointsForm.value.bonusPoints = 0;
      
      // Emit update to parent
      emit('member-updated', props.member.id, {
        bonus_points: localBonusPoints.value
      });
    }
  } catch (error) {
    console.error('Error updating bonus points:', error);
  } finally {
    isLoading.value = false;
  }
};

const handleOrderNumberInputSubmit = async () => {
  if (isOrderLoading.value || orderNumberForm.value.orderNumber === localOrderNumber.value) return;
  
  isOrderLoading.value = true;
  try {
    const response = await axios.patch(
      `/courses/${props.member.course_id}/members/${props.member.id}/order-number`,
      {
        order_number: orderNumberForm.value.orderNumber,
      }
    );

    if (response.data.success) {
      localOrderNumber.value = orderNumberForm.value.orderNumber;
      
      // Emit update to parent
      emit('member-updated', props.member.id, {
        order_number: localOrderNumber.value
      });
    }
  } catch (error) {
    console.error('Error updating order number:', error);
  } finally {
    isOrderLoading.value = false;
  }
};

const handleGradeProgressUpdate = async (newGradeProgress) => {
  if (localGradeProgress.value === newGradeProgress) return;
  
  try {
    const response = await axios.patch(
      `/courses/${props.member.course_id}/members/${props.member.id}/grade_progress`,
      {
        grade_progress: newGradeProgress,
      }
    );

    if (response.data.success) {
      localGradeProgress.value = newGradeProgress;
      
      // Emit update to parent
      emit('member-updated', props.member.id, {
        grade_progress: localGradeProgress.value
      });
    }
  } catch (error) {
    console.error('Error updating grade progress:', error);
  }
};

const handleNotesCommentsUpdate = async (newNotesComments) => {
  if (localNotesComments.value === newNotesComments) return;
  
  try {
    const response = await axios.patch(
      `/courses/${props.member.course_id}/members/${props.member.id}/notes_comments`,
      {
        notes_comments: newNotesComments,
      }
    );

    if (response.data.success) {
      localNotesComments.value = newNotesComments;
      
      // Emit update to parent
      emit('member-updated', props.member.id, {
        notes_comments: localNotesComments.value
      });
    }
  } catch (error) {
    console.error('Error updating notes comments:', error);
  }
};

const setGradeStatus = (errMsg) => {
  errorMessages.value.push(errMsg);
};

// Watch for changes in props.member and update local state
watch(() => props.member.bonus_points, (newValue) => {
  localBonusPoints.value = newValue ?? 0;
});

watch(() => props.member.order_number, (newValue) => {
  localOrderNumber.value = newValue;
});

watch(() => props.member.grade_progress, (newValue) => {
  localGradeProgress.value = newValue;
});

watch(() => props.member.notes_comments, (newValue) => {
  localNotesComments.value = newValue;
});
</script>

<template>
  <td
    scope="row"
    :class="
      gradeStatus ? 'bg-slate-100 text-slate-800 border-slate-300' : 'bg-red-100 text-red-700 border-red-300'
    "
    class="border font-medium text-gray-900 whitespace-nowrap dark:text-white text-center"
  >
    <div v-if="!isCourseAdmin" class="w-12 min-w-fit">
      {{ localOrderNumber }}
    </div>
    <form
      v-else
      @submit.prevent="handleOrderNumberInputSubmit"
      class="flex items-center justify-center"
    >
      <input
        type="number"
        name="orderNumber"
        v-model="orderNumberForm.orderNumber"
        :id="`member-${member.id}-order-number`"
        autocomplete="order-number"
        class="block w-16 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
      />
      <button
        type="submit"
        class="rounded-md px-1 py-0 ml-1 text-sm font-base text-teal-700 shadow-sm hover:bg-teal-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-600 transition-colors"
      >
        <Icon
          icon="icomoon-free:spinner"
          class="w-5 h-5 animate-spin"
          v-if="isOrderLoading"
        />
        <Icon icon="fluent:save-24-regular" class="w-5 h-5" v-else />
      </button>
    </form>
  </td>
  <td
    :class="
      gradeStatus ? 'bg-slate-100 text-slate-800 border-slate-300' : 'bg-red-100 text-red-700 border-red-300'
    "
    class="px-1 py-1 border text-center"
  >
    <p>{{ member.member_code }}</p>
  </td>
  <td
    :class="
      gradeStatus ? 'bg-slate-100 text-slate-800 border-slate-300' : 'bg-red-100 text-red-700 border-red-300'
    "
    class="pl-2 py-1 border text-start w-48"
  >
    {{ member.member_name ?? member.user.name }}
  </td>

  <!-- Assignment answers -->
  <td
    :class="
      gradeStatus ? 'bg-slate-100 text-slate-800 border-slate-300' : 'bg-red-100 text-red-700 border-red-300'
    "
    v-for="(assignment, asmIndx) in assignments"
    :key="assignment.id"
    class="px-2 py-1 border text-center w-8"
  >
    <OptimizedMemberAssignmentAnswerProgress
      :member_id="member.id"
      :user_id="member.user.id"
      :assignment="assignment"
      :answers="member.assignment_answers"
      @handleEmptyPoints="
        setGradeStatus('ภาระงานที่ ' + (asmIndx + 1) + ' ไม่มีคะแนน')
      "
    />
  </td>

  <!-- Quiz results -->
  <td
    :class="
      gradeStatus ? 'bg-slate-100 text-slate-800 border-slate-300' : 'bg-red-100 text-red-700 border-red-300'
    "
    v-for="(quiz, qzIndx) in quizzes"
    :key="quiz.id"
    class="px-2 py-1 border text-center w-8"
  >
    <OptimizedMemberQuizAnswerProgress
      :member_id="member.id"
      :user_id="member.user.id"
      :quiz="quiz"
      :results="member.quiz_results"
      @handleEmptyPoints="
        setGradeStatus('แบบทดสอบที่ ' + (qzIndx + 1) + ' ไม่มีคะแนน')
      "
    />
  </td>

  <td
    :class="
      gradeStatus ? 'bg-slate-100 text-slate-800 border-slate-300' : 'bg-red-100 text-red-700 border-red-300'
    "
    class="px-2 py-0 border text-center"
  >
    {{ member.achieved_score }}
  </td>

  <td
    :class="
      gradeStatus ? 'bg-slate-100 text-slate-800 border-slate-300' : 'bg-red-100 text-red-700 border-red-300'
    "
    class="px-2 py-0 border text-center"
  >
    <form
      @submit.prevent="handleBonusPointsInputSubmit"
      class="flex items-center justify-center"
    >
      <span class="mr-2">{{ localBonusPoints }}</span>
      <input
        type="number"
        name="bonusPoints"
        v-model="bonusPointsForm.bonusPoints"
        :id="`member-${member.id}-bonus-points`"
        autocomplete="bonus-points"
        class="block w-16 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
      />
      <button
        type="submit"
        class="rounded-md px-1 py-0 ml-1 text-sm font-base text-teal-700 shadow-sm hover:bg-teal-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-600 transition-colors"
      >
        <Icon
          icon="icomoon-free:spinner"
          class="w-5 h-5 animate-spin"
          v-if="isLoading"
        />
        <Icon icon="fluent:save-24-regular" class="w-5 h-5" v-else />
      </button>
    </form>
  </td>

  <td
    :class="
      gradeStatus ? 'bg-slate-100 text-slate-800 border-slate-300' : 'bg-red-100 text-red-700 border-red-300'
    "
    class="px-2 py-0 border text-center"
  >
    {{ totalMemberScore + "/" + course.total_score }}
  </td>

  <td
    :class="
      gradeStatus ? 'bg-slate-100 text-slate-800 border-slate-300' : 'bg-red-100 text-red-700 border-red-300'
    "
    class="px-2 py-0 border text-center"
  >
    {{ gradePercentage }}
  </td>

  <td
    :class="
      gradeStatus ? 'bg-slate-100 text-slate-800 border-slate-300' : 'bg-red-100 text-red-700 border-red-300'
    "
    class="px-2 py-0 border text-center text-lg font-bold"
  >
    <span
      :class="`rounded-md px-2 bg-${gradeProgress(gradePercentage).color}-200 text-${gradeProgress(gradePercentage).color}-800 border border-${gradeProgress(gradePercentage).color}-300`"
      v-if="gradeStatus"
    >
      {{ gradeProgress(gradePercentage).grade }}
    </span>
    <span
      class="rounded-md px-2 bg-slate-200 text-slate-800 border border-slate-300"
      v-else
    >
      ร
    </span>
  </td>

  <td class="p-1 flex justify-center">
    <OptimizedMemberGradeProgress
      :grade="gradeProgress(gradePercentage).grade"
      :gradeStatus="gradeStatus"
      :member_id="member.id"
      :course_id="member.course_id"
      :grade_progress="localGradeProgress"
      @update:grade-progress="handleGradeProgressUpdate"
    />
  </td>
  
  <td class="p-1 border">
    <OptimizedMemberProgressComments
      :member_id="member.id"
      :course_id="member.course_id"
      :notes_comments="localNotesComments"
      @update:notes-comments="handleNotesCommentsUpdate"
    />
  </td>
</template>