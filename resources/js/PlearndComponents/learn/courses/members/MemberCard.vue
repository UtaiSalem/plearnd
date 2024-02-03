<script setup>
import { computed } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';

import ProgressBar from 'primevue/progressbar';

const props = defineProps({
    member: {
      type: Object,
      default: () => ({})
    },
    dataIndex: {
      type: Number,
    },
});

const emit = defineEmits(['request-unmember-course']);

const canManage = computed(() => (props.member.user.id === usePage().props.auth.user.id) || usePage().props.isCourseAdmin);
const percentage = computed(() => {
  if (props.member.achieved_score) {
    return props.member.achieved_score;
  }else{
    return 0;
  }
});

// Grade calculator
// const grade = computed(() => {
//       // Assuming a simple grading scale for this example
//       if (props.member.grade_progress >= 90) {
//         return 'A';
//       } else if (props.member.grade_progress >= 80) {
//         return 'B';
//       } else if (props.member.grade_progress >= 70) {
//         return 'C';
//       } else if (props.member.grade_progress >= 60) {
//         return 'D';
//       } else {
//         return 'F';
//       }
// });


</script>

<template>
    <li class="bg-white shadow-lg p-2 rounded-lg flex flex-wrap justify-between items-center w-full mb-3">
      <div class="flex items-center min-w-fit">
        <img class="w-12 h-12 rounded-full" :src="member.user.avatar" :alt="member.user.name">
        <div class="ml-2">
          <p class="text-gray-900  tracking-wide text-sm">{{member.user.name}}</p>
          <div class="flex items-center mt-2">
            <span class="text-slate-600">No.# {{ props.member.order_number }}</span>
            <!-- <select class="block text-white bg-green-500 font-medium transition duration-75 rounded-lg h-6 w-12 mx-2 " >
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
            </select> -->
          </div>
        </div>
      </div>
      <!-- <div v-if="canManage" class="flex items-center ml-2 mt-2 w-full sm:w-1/2">
        <div class="w-full bg-gray-200 rounded-full dark:bg-gray-700">
          <div 
            class="bg-blue-600 text-xs font-medium text-blue-100 text-center py-0.5 leading-none rounded-full" 
            :style="`width: ${percentage}`"> {{ props.member.grade_progress }}%
          </div>
        </div>
        <div class="ml-2">
          <p class="text-gray-900  tracking-wide text-sm">{{  grade }}</p>
        </div>
      </div> -->
      <div v-if="canManage" class="flex flex-col items-center ml-2 mt-2 w-full sm:w-1/2">
        <!-- <div class="w-full flex justify-between mb-1">
          <span class="text-sm font-normal text-blue-700 dark:text-white"></span>
          <span class="text-sm font-normal text-blue-700 dark:text-white">{{ $page.props.course.data.total_score }}</span>
        </div> -->
        <div class="w-full bg-gray-200 rounded-full dark:bg-gray-700">
          <div 
            class="bg-blue-600 text-xs font-medium text-blue-100 text-center py-0.5 leading-none rounded-full" 
            :style="percentage > 0? `width: ${percentage}%`: `width: ${percentage}`"> {{ percentage }}%
          </div>
        </div>
      </div>
    </li>
</template>