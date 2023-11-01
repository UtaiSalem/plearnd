<script setup>
import { computed, ref } from 'vue';
import axios from 'axios';

import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Navbar from '@/PlearndComponents/Navbar.vue';
import PWDUserItem from './passwordreset/PWDUserItem.vue'

const searchEmail = ref('');
const userResult = ref([]);
const userResultCount = ref(null);

async function handleSearchFormSubmit() {
    try {
        const userResp = await axios.post('/forgot-password/getuser', { email: searchEmail.value.trim().toString() });
        if (userResp.data) {
            userResult.value = userResp.data.user;
            userResultCount.value = userResp.data.user.length;
        }
    } catch (error) {
        console.log(error);
    }
}

</script>

<template>
    <div>
        <MainLayout>
            <template #header>
                <div>
                    <Head title="Support"></Head>
                </div>
            </template>
            <template #navbar>
                <div>
                    <Navbar />
                </div>
            </template>

            <template #mainContent>
                <div class="mt-16">
                    <div class="bg-white border rounded-lg px-4 py-3 mx-auto my-4 max-w-full">
                        <h2 class="text-2xl font-medium mb-4">รีเซ็ตรหัสผ่าน</h2>
                        <!-- <form @submit.prevent="handleSearchFormSubmit"> -->
                            <!-- <div>
                                <div class="mb-4">
                                    <label for="product_name" class="block text-gray-700 font-medium mb-2">อีเมลผู้ใช้</label>
                                    <input type="text" id="product_name" v-model="searchEmail"
                                        class="border border-gray-400 p-2 w-full rounded-lg focus:outline-none focus:border-blue-400"
                                        required>
                                </div>

                                <div>
                                    <button type="submit"
                                        class="hover:shadow-form w-full rounded-md bg-[#6A64F1] py-3 px-8 text-center text-base font-semibold text-white outline-none">
                                        ค้นหา
                                    </button>
                                </div>

                            </div> -->

                            <div class="flex items-center justify-center ">
                                <div class="w-full">
                                    <!-- <form class="sm:flex sm:items-center" @submit.prevent="handleSearchFormSubmit"> -->
                                    <form class="sm:flex sm:items-center">
                                        <input id="q" v-model="searchEmail" @input="handleSearchFormSubmit" class="inline w-full sm:w-3/4 rounded-md border border-gray-400 bg-white py-2 pl-3 pr-3 leading-5 placeholder-gray-500 focus:border-indigo-500 focus:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:text-sm" placeholder="อีเมลผู้ใช้" type="search" autofocus="" >
                                        <button type="button" @click.prevent="() => { searchEmail=''; userResult = []; userResultCount=null;}" class="mt-3 inline-flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">ล้างข้อมูล</button>
                                    </form>
                                </div>
                            </div>


                        <!-- </form> -->
                    </div>

                    <div v-if="userResultCount>0" >
                        <div class="max-w-screen-xl mx-auto ">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-2 gap-4">
                                <div v-for="item in userResult" :key="item.id" >
                                    <PWDUserItem :user="item"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="bg-white border rounded-lg px-4 py-3 mx-auto my-4 max-w-full">ไม่มีข้อมูล</div>
                </div>

            </template>

        </MainLayout>
    </div>
</template>

