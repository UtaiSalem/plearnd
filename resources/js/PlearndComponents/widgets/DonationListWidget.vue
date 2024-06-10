<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import Swal from 'sweetalert2';

import DonateItemCard from '@/PlearndComponents/widgets/donates/DonateItemCard.vue';

const props = defineProps({
    donates: Object,
});

const emit = defineEmits(['add-new-donate-activity']);

const isCreateDonatePageLoading = ref(false);
const isGetDonateLoading = ref(false);
const isGetDonateSuccess = ref(false);
const timeLeft = ref();
const donateRecieved = ref(0);

const currentIndex = ref(0);
const currentDonateId = ref();
const donateResponse = ref();
const recieveDonateActivity = ref();

const showDonorProgress = computed(()=>{
    return timeLeft.value >= 0 && isGetDonateLoading.value;
});

const handleLinkToCreateDonate = () => {
    isCreateDonatePageLoading.value = true;
    window.location.href = "/supports/donates/create";
};

const handleGetDonate = (donateId, idx) => {
    isGetDonateLoading.value = true;
    timeLeft.value = 5000;
    donateRecieved.value = 0;
    currentIndex.value = idx;
    currentDonateId.value = donateId; 
    try {

        getDonate(donateId);

        countdown();

    } catch (error) {
        console.log(error);
    }
};

const getDonate = async (id) => {
    try {
        let getDonateResponse = await axios.get(`/donates/${id}/get-donate`);

        if (getDonateResponse.data.success) {
            donateResponse.value = getDonateResponse.data.donate;
            recieveDonateActivity.value = getDonateResponse.data.activity;            
        }
        else{
            alertGetDonateError();
        }
    } catch (error) {
        console.log(error);
        alertGetDonateError();
    }
};

const wait = (ms) => new Promise(resolve => setTimeout(resolve, ms));

const countdown = () => {
    timeLeft.value -= 18.51;
    if (timeLeft.value > 18.51) {
        donateRecieved.value += 1;
        wait(18.5).then(countdown);
    } else {
        isGetDonateLoading.value = false;
        handleGetDonateSuccess();
    }    
};    

const handleGetDonateSuccess = () => {
    // if (donateResponse.value){
        if (usePage().props.auth.user) { usePage().props.auth.user.pp += 270 };

        props.donates.data[currentIndex.value].remaining_points = donateResponse.value.remaining_points;

        if (props.donates.data[currentIndex.value].remaining_points < 270) {
            props.donates.data.splice(currentIndex.value, 1);
        }
        emit('add-new-donate-activity', recieveDonateActivity.value);
        recieveDonateActivity.value = null;
        alertGetDonateSuccess();
    // }
};

const alertGetDonateSuccess = () => {
    Swal.fire({
        title: 'รับการสนับสนุนสำเร็จ',
        text: 'คุณได้รับการสนับสนุนเรียบร้อยแล้ว' + ' 270' + ' แต้ม',
        icon: 'success',
        showConfirmButton: false,
        timer: 1200
    });
};

const alertGetDonateError = () => {
    Swal.fire({
        title: 'เกิดข้อผิดพลาด',
        text: 'ไม่สามารถรับการสนับสนุนได้',
        icon: 'error',
        showConfirmButton: false,
        timer: 1200
    });
};

</script>
<template>
    <div class="bg-white p-1 rounded-lg shadow-lg border-t-4 border-blue-500">
        <p class="text-lg font-bold mb-2 p-2">รายการทุนสนับสนุน</p>
        <hr class="mb-2">
        <div v-for="(donate, index) in props.donates.data" :key="index"
            class="border mb-2 rounded-lg hover:shadow-indigo-300 hover:shadow-md bg-indigo-50/30">
            <DonateItemCard 
                :donate 
                @getDonateRequest="handleGetDonate(donate.id, index)" 
            />
        </div>
        <hr class="my-2">

        <div v-if="props.donates.data[currentIndex].donor" :class="{'hidden' : !showDonorProgress}" class="fixed top-0 left-0 z-20 flex items-center justify-center w-full h-full modal bg-gray-900/75 modal-overlay">
        <!-- <div class="fixed top-0 left-0 z-20 flex items-center justify-center w-full h-full modal bg-gray-900/75 modal-overlay"> -->
            <div class="relative w-[40rem] h-[22rem] z-30 bg-white rounded-lg mx-auto overflow-hidden">
                <figure class="flex items-center p-2 mb-4 bg-gray-300 rounded-md">
                    <div class="flex-shrink-0">
                        <img class="w-40 h-40 rounded-full border-4 border-yellow-400"
                            :src="props.donates.data[currentIndex].donor.avatar"
                            :alt="props.donates.data[currentIndex].donor.name + 'photo'">
                    </div>
                    <div class="w-full ps-3">
                        <div class="flex flex-col mb-1 text-[40px] text-gray-700 dark:text-gray-400">
                            <span class="font-bold text-blue-500">{{ props.donates.data[currentIndex].donor.name }}</span>
                            <span class="font-semibold">{{ props.donates.data[currentIndex].donor.personal_code }}</span>
                        </div>
                    </div>
                </figure>
                <ProgressBar mode="indeterminate" style="height: 16px"></ProgressBar>
                <div class="w-full flex justify-center">
                    <p class="mt-4 text-[20px]">ให้การสนับสนุน แก่คุณ</p>
                </div>
                <div class="absolute top-[75%] left-[14rem] text-[60px] text-yellow-400/80 ">{{ donateRecieved}} แต้ม</div>
            </div>
        </div>

        <div v-else :class="{'hidden' : !showDonorProgress}" class="fixed top-0 left-0 z-20 flex items-center justify-center w-full h-full modal bg-gray-900/75 modal-overlay">
            <div class="relative w-[40rem] h-[39rem] z-30 bg-white rounded-lg mx-auto  overflow-hidden">
                <img :src="'/storage/images/supports/donates/plearnd_poster.png'" alt="" srcset="" class="w-full">
                <ProgressBar mode="indeterminate" style="height: 16px"></ProgressBar>
                <div class="absolute top-[30%] left-[14rem] text-[60px] text-yellow-300/80 ">{{ donateRecieved}} แต้ม</div>
            </div>
        </div>

        <div class="hidden fixed top-0 left-0 z-20 items-center justify-center w-full h-full modal bg-gray-900/75 modal-overlay">
        <img :src="'/storage/images/supports/donates/plearnd_poster.png'" alt="" srcset="" class="w-full">
        </div>
        
    </div>
</template>