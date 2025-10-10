<script setup>
import { computed } from 'vue';
import { Icon } from '@iconify/vue';

const props = defineProps({
    status: Number,
});

const attendanceStatus = computed(()=>{
    switch (props.status) {
        case 0:
            return {icon:'heroicons-outline:x-circle', color:'text-red-500'};
        case 1:
        return {icon:'heroicons-outline:check-circle', color:'text-green-500'};
        case 2:
            return {icon:'heroicons-outline:check-circle', color:'text-yellow-500'};
        case 3:
            return {icon:'heroicons-outline:x-circle', color:'text-red-500'};
        default:
            return {icon:null, color:null};
    }
});

</script>

<template>
        <span class="px-2 inline-flex text-xs leading-5 font-semibold" :class="attendanceStatus.color">
            <Icon :icon="attendanceStatus.icon" width="24" height="24" />
        </span>
</template>
