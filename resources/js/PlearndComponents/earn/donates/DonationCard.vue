<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    donate: Object,
    isProcessing: Boolean,
});

const emit = defineEmits(['getDonateRequest']);

</script>

<template>
  
  <div class="bg-white shadow-xl rounded-lg text-gray-900">
    <div class="rounded-t-lg h-32 overflow-hidden">
        <img class="object-cover object-top w-full" :src="donate.donor ? donate.donor.cover_image : '/storage/images/banner/banner-profile-stats.jpg'" alt='Mountain'>
    </div>
    <div class="m-auto w-32 h-32 relative -mt-16 border-4 border-white rounded-full overflow-hidden bg-white">
        <img class="object-cover object-center w-full h-full" :src="donate.donor ? donate.donor.avatar : '/storage/plearnd-logo.png'" alt='Woman looking front'>
    </div>
    <div class="text-center mt-2">
        <h2 class="font-semibold">{{ donate.donor ? donate.donor.name : 'ไม่ประสงค์ออกนาม' }}</h2>
        <!-- <p class="text-gray-500">Freelance Web Designer</p> -->
    </div>

    <div class="m-2 p-2 bg-gray-200 rounded-lg">
            <!-- <p class="py-1 text-sm">ให้การสนับสนุน</p> -->
        <p class="w-full text-base font-bold tracking-tight text-blue-500">
            <span>
                {{ donate.amounts }} บาท
            </span>
            <div class="w-full flex justify-end space-x-1">
                <span class="text-[16px] text-yellow-600">{{ donate.remaining_points }}</span>
                <span class="text-[10px]"> points</span>
            </div>
        </p>

    </div>
    <hr class="mx-2" />
    
    <div class="flex gap-2 px-2 my-2">
        <button
            class="flex-1 rounded-full bg-blue-600 hover:bg-blue-800 text-sm text-white font-semibold dark:text-white antialiased px-4 py-2">
            เพิ่มเพื่อน
        </button>
        <Link v-if="donate.donor && donate.donor.no_of_ref < 5" :href="donate.donor.referal_link" as="button"
            class="flex-1 items-center justify-center rounded-full bg-teal-500 hover:bg-teal-600 text-sm text-white font-semibold dark:text-white antialiased px-4 py-2">
            สมัครต่อ
        </Link>
    </div>

    <hr class="mx-2" />

    <div class="flex flex-col gap-2 m-2" v-if="donate.remaining_points > 0">
        <button v-if="donate.status === 0" disabled
            class="w-full py-2 mt-1 font-medium text-center text-white bg-orange-400 rounded-md">
            รออนุมัต
        </button>
        <button v-if="donate.status === 1 && $page.props.auth.user" :disabled="isProcessing"
            class="w-full py-2 mt-1 font-medium text-center text-white rounded-md bg-sky-400"
            @click.prevent="emit('getDonateRequest')">
            รับการสนับสนุน
        </button>
    </div>
</div>

</template>
