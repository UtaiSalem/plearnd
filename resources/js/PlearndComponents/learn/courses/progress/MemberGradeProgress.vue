
<script setup>
import { ref } from "vue";

const props = defineProps({
  member_id: Number,
  grade: Number,
  gradeStatus: Number,
});
const gradeStatus = ref(props.gradeStatus);

function handleGradeStatusChange(event) {
  const selectedStatus = event.target.value;
  gradeStatus.value = selectedStatus;
  console.log(gradeStatus.value);
}

</script>

<template>
    <select :id="`select-grade-status-progress-${props.member_id}`" @change="handleGradeStatusChange"
      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-[72px] p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
      <option :value="grade" :selected="gradeStatus<=4">{{ grade }}</option>
      <option :value="5" :selected="gradeStatus>4">{{ 'ร' }}</option>
    </select>
</template>