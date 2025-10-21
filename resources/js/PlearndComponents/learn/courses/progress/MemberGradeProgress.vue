<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  member_id: Number,
  course_id: Number,
  grade: Number,
  gradeStatus: Boolean,
});

const emit = defineEmits(['update:gradeProgress']);

// กำหนดค่าเริ่มต้นให้ localGradeStatus
const localGradeStatus = ref(props.gradeStatus ? props.grade : 5);

// ตรวจสอบการเปลี่ยนแปลงของ props.gradeStatus
watch(() => props.gradeStatus, (newStatus) => {
  localGradeStatus.value = newStatus ? props.grade : 5;
});

async function handleGradeStatusChange(event) {
  const selectedStatus = event.target.value;
  localGradeStatus.value = parseInt(selectedStatus);
  console.log(localGradeStatus.value);

  try {
    let resp = await axios.patch(`/courses/${props.course_id}/members/${props.member_id}/grade_progress`, { grade_progress: selectedStatus });
    if (resp.data && resp.data.success) {
      console.log(resp.data);
      emit('update:gradeProgress', localGradeStatus.value);
    }
  } catch (error) {
    console.log(error);
  }
}
</script>

<template>
    <select
        :id="`select-grade-status-progress-${props.member_id}`"
        v-model="localGradeStatus"
        @change="handleGradeStatusChange"
        class="bg-slate-100 border-2 border-slate-400 text-slate-800 text-sm font-medium rounded-md focus:ring-2 focus:ring-teal-500 focus:border-teal-600 block w-[72px] p-2.5 hover:bg-slate-200 transition-colors shadow-sm">
      <option :value="5">{{ 'ร' }}</option>
      <option :value="grade">{{ grade }}</option>
    </select>
</template>
