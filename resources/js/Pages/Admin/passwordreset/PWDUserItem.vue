<script lang="ts" setup>
import { computed, ref } from 'vue';
import { Icon } from '@iconify/vue';
import axios from 'axios';
import Swal from 'sweetalert2';
const props = defineProps({
    user: Object
});

const userPP = ref(props.user.pp);
const showExchangeForm = computed( () => userPP.value<=3400 );
const moneyQnt = ref(null);

async function handleResetButtonClicked() {
    try {
        if (userPP.value<3400) {
            Swal.fire('แต้มสะสมไม่เพียงพอ','ต้องใช้แต้ม 3400 แต้ม กรุณาเพิ่มแต้มสะสม.','warning' );
            // showExchangeForm.value = true;
        }else{
            let resetResp = await axios.post('/forgot-password/reset/' + props.user.id);
            if(resetResp.data.success){
                userPP.value -= 3400;
                Swal.fire("เสร็จสมบูรณ์" , resetResp.data.message, 'success' );
            }else{
                Swal.fire("เกิดข้อผิดพลาด", resetResp.data.message, 'warning' );
            }
        }
    } catch (error) {
        console.log(error);
    }
}

async function handleExchangeButtonClick() {
    if (moneyQnt.value && moneyQnt.value>0) {
        let excResp = await axios.post('/forgot-password/exchange/' + props.user.id, { money: moneyQnt.value });
        if (excResp.data.success) {
            userPP.value += moneyQnt.value*680;
            moneyQnt.value=null;
            Swal.fire("เสร็จสมบูรณ์" , excResp.data.message, 'success' );
        }else{
            Swal.fire("เกิดข้อผิดพลาด", excResp.data.message, 'warning' );
        }
    }else{
        Swal.fire("เกิดข้อผิดพลาด", 'จำนวนเงินน้อยเกินไป', 'warning' );
    }
}

</script>

<template>
<div
    class="max-w-2xl sm:max-w-sm md:max-w-sm lg:max-w-sm xl:max-w-sm sm:mx-auto md:mx-auto lg:mx-auto xl:mx-auto  bg-white shadow-xl rounded-lg text-gray-900">
    <div class="rounded-t-lg h-32 overflow-hidden">
        <img class="object-cover object-top w-full" src='https://images.unsplash.com/photo-1549880338-65ddcdfd017b?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=400&fit=max&ixid=eyJhcHBfaWQiOjE0NTg5fQ' alt='Mountain'>
    </div>
    <div class="mx-auto w-24 h-24 relative -mt-12 border-4 border-white rounded-full overflow-hidden">
        <img class="object-cover object-center h-24"
        :src="user.profile_photo_url || 'https://cdn.pixabay.com/photo/2016/08/08/09/17/avatar-1577909_1280.png'" alt='Woman looking front'>
    </div>
    <div class="text-center mt-2">
        <h2 class="font-semibold">{{ props.user.name }}</h2>
        <p class="text-gray-500">{{ props.user.email }}</p>
    </div>
    <ul class="py-2 mt-2 text-gray-700 flex items-center justify-around">
        <li class="flex flex-col items-center justify-between">
            <Icon icon="la:id-card-solid" />
            <div>{{ props.user.id }}</div>
        </li>
        <li class="flex flex-col items-center justify-between">
            <Icon icon="noto:coin" />
            <div>{{ userPP }}</div>
        </li>
    </ul>
    <div class="p-4 border-t mx-8 mt-2" v-if="!showExchangeForm">
        <button @click.prevent="handleResetButtonClicked" class="w-full text-xs block mx-auto rounded-full bg-blue-600 hover:bg-red-600 hover:scale-105 hover:shadow-lg font-light text-white py-2">
            รีเซ็ตรหัสผ่าน
        </button>
    </div>
    <div class="p-4 border-t mx-2 mt-2" v-if="showExchangeForm">
        <div class="flex">
            <input type="number" v-model="moneyQnt" placeholder="จำนวนเงิน x 680" class="w-full rounded-l border-2 border-sky-500 focus:outline-none focus:border-sky-500 focus:border-r-0">
            <button type="submit" :disabled="moneyQnt<=0" @click.prevent="handleExchangeButtonClick"
                class="bg-sky-500 text-white rounded-r px-2 md:px-3 py-0 md:py-1 flex items-center justify-center">
                <Icon icon="circum:coin-insert" class="w-8 h-8" />
                <span>เติม</span>
            </button>
        </div>
    </div>
</div>

</template>
