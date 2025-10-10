<script setup>
import { ref, reactive, computed, onMounted, onBeforeUnmount } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';

const props = defineProps({
    attendance: Object,
    aIndx: Number,
    aDate: String,
    startAt: String,
    finishAt: String,
    lateTime: Number,
    aDescription: String,
    authMemberAttendance: Object,
});

const refAuthMemberAttendanceStatus = ref(props.authMemberAttendance ? props.authMemberAttendance.status : null);

const timeLeft = ref(0);
const isLate = ref(false);
const isFinished = ref(false);
const canRegister = ref(false);
const countdownTimer = ref(null);

const status = computed(()=>{
    switch (refAuthMemberAttendanceStatus.value) {
        case 0:
            return {icon:'heroicons-outline:x-circle', color:'text-red-500'};
        case 1:
            return {icon:'heroicons-outline:check-circle', color:'text-green-500'};
        case 2:
            return {icon:'heroicons-outline:check-circle', color:'text-yellow-500'};
        case 3:
            return {icon:'heroicons-outline:x-circle', color:'text-red-500'};
        default:
            return {icon:'heroicons-outline:x-circle', color:'text-red-500'};
    }
});

const isLoadingMemberAttendanceDetail = ref(false);

const form = reactive({
    course_attendance_id: props.attendance.id,
    course_member_id: usePage().props.courseMemberOfAuth.user_id,
    status: 1,
    comments: null,
});

const load = () => {
    isLoadingMemberAttendanceDetail.value = true;
    storeAttendance();
};

async function storeAttendance() {
    let storeResultResp = await axios.post(`/attendances/${props.attendance.id}/details`, form);
    if (storeResultResp.data && storeResultResp.data.success) {
        refAuthMemberAttendanceStatus.value = storeResultResp.data.attendance_detail.status;
        isLoadingMemberAttendanceDetail.value = false;
    }else{
        console.log(storeResultResp.data);
        isLoadingMemberAttendanceDetail.value = false;
    }
}

const calculateTimeLeft = () => {
    const now = new Date();
    const finish = new Date(props.finishAt);
    
    const diff = finish - now;
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    isLate.value = new Date(now - new Date(props.startAt)).getMinutes() > props.lateTime;
    isFinished.value = now > finish;
    canRegister.value = !isFinished.value && refAuthMemberAttendanceStatus.value === null;
    timeLeft.value = `${hours}:${minutes}:${seconds}`;
};

// Add countdown function
const startCountdown = () => {
    countdownTimer.value = setInterval(() => {
        calculateTimeLeft();
    }, 1000);
};

// Start and cleanup countdown
onMounted(() => {
    startCountdown();
});

onBeforeUnmount(() => {
    if (countdownTimer.value) {
        clearInterval(countdownTimer.value);
    }
});

</script>

<template>
    <tr class="bg-white border-b">
        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ aIndx+1 }}</td>
        <td class="text-sm text-gray-900 font-light px-3 py-4 whitespace-nowrap">{{ new Date(aDate).toLocaleDateString('en-GB') }}</td>
        <td class="text-sm text-gray-900 font-light px-3 py-4 whitespace-nowrap">{{ new Date(startAt).toLocaleString('en-GB') }}</td>
        <td class="text-sm text-gray-900 font-light px-3 py-4 whitespace-nowrap">{{ new Date(finishAt).toLocaleString('en-GB') }}</td>
        <td class="text-sm text-gray-900 font-light px-3 py-4 whitespace-nowrap">{{ aDescription }}</td>
        <td class="text-sm text-gray-900 font-light px-3 py-4 whitespace-nowrap">
            <span v-if="status && !canRegister" class="px-2 inline-flex text-xs leading-5 font-semibold" :class="status.color">
                <Icon :icon="status.icon" width="24" height="24" />
            </span>
            <div v-if="canRegister" class="card flex ">
                <Button 
                    type="button" 
                    :label="'มาเรียน ('+ timeLeft + ')'" 
                    icon="pi pi-check" 
                    :loading="isLoadingMemberAttendanceDetail" 
                    @click="load" 
                    class="p-3 text-gray-200 font-base"
                    :class="isLate ? 'bg-yellow-500': 'bg-green-400'"                    
                />
            </div>
        </td>
    </tr>
</template>
