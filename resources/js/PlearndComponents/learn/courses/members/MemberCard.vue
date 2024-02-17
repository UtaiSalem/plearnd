<script setup>
import { computed } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';

import ProgressBar from 'primevue/progressbar';
import DotsDropdownMenu from '@/PlearndComponents/accessories/DotsDropdownMenu.vue';

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

</script>

<template>
    <li :class="props.member.role === 3 ? 'hidden':''"
    class="relative bg-white shadow-lg p-2 rounded-lg flex flex-wrap justify-between items-center w-full mb-3">
      <div class=" absolute top-0 right-0.5" v-if="$page.props.isCourseAdmin">
        <DotsDropdownMenu @delete-model="emit('request-unmember-course')">
          <template #deleteModel>
            <div>
              <!-- <button @click="requestToBeUnMember" class="flex items-center w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-100 dark:hover:bg-red-800 hover:text-red-700 dark:text-red-100 dark:hover:text-red-200 focus:outline-none focus:ring focus:ring-red-500"> -->
              <!-- <Icon icon="bi:trash" class="w-5 h-5" /> -->
              <span class="ml-2">ลบสมาชิก</span>
              <!-- </button> -->
            </div>
          </template>
        </DotsDropdownMenu>
      </div>
      
      <div class="flex items-center min-w-fit">
        <img class="w-12 h-12 rounded-full" :src="member.user.avatar" :alt="member.user.name">
        <div class="ml-2">
          <p class="text-gray-900  tracking-wide text-sm">{{member.user.name}}</p>
          <div class="flex items-center mt-2 space-x-2">
            <span class="text-slate-600">No.{{ props.member.order_number?? '#' }}</span>
            <span class="text-slate-600">กลุ่ม/ห้อง {{ props.member.group ? props.member.group.name : '-' }}</span>
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