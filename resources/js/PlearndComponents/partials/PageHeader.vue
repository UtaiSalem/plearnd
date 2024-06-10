<script setup>
import { Icon } from '@iconify/vue';
import { ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';

import MainLayout from '@/Layouts/MainLayout.vue';
import Navbar from '@/PlearndComponents/Navbar.vue';
import PostViewer from '@/HopeuiComponents/partials/PostViewer.vue';
// import PollViewer from '@/HopeuiComponents/partials/PollViewer.vue';
// const pageData = usePage().props;

const props = defineProps({
    academy: Object,
    profilePath: String,
});

const isDarkMode = ref(false);
const logoInput = ref(null);
const coverInput = ref(null);
const tempLogo = ref(props.profilePath + 'storage/' + props.academy.logo);
const tempCover = ref(props.profilePath + 'storage/' + props.academy.cover);
// const props = defineProps({});
const browseCover = () => { coverInput.value.click() };
const browseLogo = () => { logoInput.value.click() };

function onCoverInputChange(event) {
    tempCover.value = URL.createObjectURL(event.target.files[0]);
}
function onLogoInputChange(event) {
    tempLogo.value = URL.createObjectURL(event.target.files[0]);
}
</script>

<template>
    <div class="w-full h-full bg-white rounded-lg overflow-hidden relative">
        <img :src="tempCover" alt="" class="w-full h-36 sm:h-52 md:h-72 lg:h-70 overflow-hidden">
        <div id="cover" class="absolute top-2 left-2 md:h-28 lg:h-60">
            <input type="file" class="hidden" ref="coverInput" accept="image/*" @change="onCoverInputChange">
            <button class="rounded-full bg-white p-1" @click.prevent="browseCover">
                <Icon icon="heroicons-outline:photograph" />
            </button>
        </div>

        <div class="-mt-8 sm:-mt-10 md:-mt-12 lg:-mt-14 ml-4 flex flex-col md:flex-row justify-center md:justify-start items-center space-x-2">
            <div class="relative w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 lg:w-28 lg:h-28 flex justify-center items-center border-gray-400 border-2 rounded-full overflow-hidden">
                <img class="bg-cover object-center h-full"
                    :src="tempLogo"
                    alt='school logo'>
                <input type="file" class="hidden" accept="image/*" ref="logoInput" @change="onLogoInputChange">
                <button type="button" @click.prevent="browseLogo"
                    class="absolute bottom-0 text-white bg-gray-300 hover:bg-opacity-50 hover:text-gray-600 transition duration-200 rounded-full md:p-1">
                    <Icon icon="heroicons:camera" class="w-4 h-4 text-white" />
                </button>
            </div>
            <div class="flex flex-col justify-center">
                <div class="w-full text-center lg:text-start">
                    <span class="font-semibold text-xl text-[#444444a9] bg-white bg-opacity-70 px-1 rounded-md">{{ academy.name }}</span>
                </div>
                <div class="w-full">
                    <span class="text-base text-gray-500">{{ academy.slogan }}</span>
                </div>
            </div>
        </div>
        <div class="flex justify-center space-x-2 text-gray-500">
            <span class=" text-sm">Member : 2300</span>
            <span class=" text-sm">Instructors : 100</span>
            <span class=" text-sm">Students : 2040</span>
            <span class=" text-sm">Courses : 2040</span>
        </div>

    </div>
</template>

<style>
/* .page-header{
    background-image: url('../storage/images/pages/01-page.png');
} */
</style>