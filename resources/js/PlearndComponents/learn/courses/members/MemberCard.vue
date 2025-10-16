<script setup>
import { computed } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';

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

const courseTotalScore = computed(() => {
  return usePage().props.course.data.total_score;
});

const canManage = computed(() => (props.member.user.id === usePage().props.auth.user.id) || usePage().props.isCourseAdmin);

const percentage = computed(() => {
  return props.member.achieved_score ? ((props.member.achieved_score/courseTotalScore.value) * 100).toFixed(2) : 0;
});

</script>

<template>
    <li :class="props.member.role === 3 ? 'hidden':''"
    class="relative bg-white shadow-lg p-2 rounded-lg flex flex-wrap justify-between items-center w-full mb-3">
      <div class=" absolute top-0 right-0.5" v-if="$page.props.isCourseAdmin">
        <DotsDropdownMenu @delete-model="emit('request-unmember-course')">
          <template #deleteModel>
            <div>
              <span class="ml-2">ลบสมาชิก</span>
            </div>
          </template>
        </DotsDropdownMenu>
      </div>
      
      <div class="flex items-center min-w-fit">
          <img class="w-12 h-12 rounded-full" :src="member.user.avatar" :alt="member.user.name">
          <div class="ml-2">
              <p class="text-gray-900  tracking-wide text-sm">{{ member.member_name ?? member.user.name}}</p>
              <div class="flex items-center mt-2 space-x-2 text-sm">
                  <span class="text-slate-700 flex"><Icon icon="fluent:number-symbol-square-20-regular" width="20" height="20" /> {{ ' ' +props.member.order_number?? '#' }}</span>
                  <span class="text-slate-700 flex"><Icon icon="heroicons:user-group" width="20" height="20" class="mr-1.5" /> {{ props.member.group ? props.member.group.name : '-' }}</span>
              </div>
          </div>
      </div>
      <div class="flex flex-col items-center ml-2 mt-2 w-full sm:w-1/2">
        <div class="w-full bg-gray-200 rounded-full dark:bg-gray-700">
          <div 
            class="bg-blue-600 text-xs font-medium text-blue-100 text-center py-0.5 leading-none rounded-full" 
            :style="percentage > 0 ? `width: ${percentage}%`: `width: ${percentage}`"> {{ percentage }}%
          </div>
        </div>
      </div>
    </li>
</template>
