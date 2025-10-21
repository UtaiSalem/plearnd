<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  member_id: Number,
  course_id: Number,
  grade: Number,
  gradeStatus: Boolean,
  grade_progress: Number,
});

const emit = defineEmits(['update:grade-progress']);

// Local state to avoid unnecessary re-renders
const localGradeProgress = ref(props.grade_progress);

// Watch for changes in props
watch(() => props.grade_progress, (newValue) => {
  localGradeProgress.value = newValue;
});

// Handle grade status change
const handleGradeStatusChange = async (event) => {
  const selectedStatus = event.target.value;
  localGradeProgress.value = parseInt(selectedStatus);

  try {
    let resp = await axios.patch(`/courses/${props.course_id}/members/${props.member_id}/grade_progress`, { 
      grade_progress: selectedStatus 
    });
    
    if (resp.data && resp.data.success) {
      emit('update:grade-progress', localGradeProgress.value);
    }
  } catch (error) {
    console.error('Error updating grade progress:', error);
    // Revert to original value on error
    localGradeProgress.value = props.grade_progress;
  }
};
</script>

<template>
    <select
        :id="`select-grade-status-progress-${props.member_id}`"
        v-model="localGradeProgress"
        @change="handleGradeStatusChange"
        class="bg-slate-100 border-2 border-slate-400 text-slate-800 text-sm font-medium rounded-md focus:ring-2 focus:ring-teal-500 focus:border-teal-600 block w-[72px] p-2.5 hover:bg-slate-200 transition-colors shadow-sm">
      <option :value="5">{{ 'ร' }}</option>
      <option :value="grade">{{ grade }}</option>
    </select>
</template>