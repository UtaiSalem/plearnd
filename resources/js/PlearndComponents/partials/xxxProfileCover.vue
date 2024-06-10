<script setup>
import { ref,computed } from 'vue';
import { Link } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';

const props = defineProps({
    coverImage : { type: String, default: null },
    logoImage : { type: String, default: null },
    coverHeader : { type: String, default: null },
    coverSubheader : { type: String, default: null },
});

const emit = defineEmits(['cover-image-change', 'logo-image-change']);

const logoInput = ref(null);
const coverInput = ref(null);
const tempLogo = ref(props.logoImage);
const tempCover = ref(props.coverImage);
const tempHeader = ref(props.coverHeader);
const tempSubheader = ref(props.coverSubheader);

const inputHeaderEditing = ref(false);
const inputSubheaderWidth = computed(()=> tempSubheader.value.length );
const browseCover = () => { coverInput.value.click() };
const browseLogo = () => { logoInput.value.click() };

function onCoverInputChange(event) {
    tempCover.value = URL.createObjectURL(event.target.files[0]);
    emit('cover-image-change', event.target.files[0]);
}
function onLogoInputChange(event) {
    tempLogo.value = URL.createObjectURL(event.target.files[0]);
    emit('logo-image-change', event.target.files[0]);
}
</script>
<template>
    <div>
        <div class="dark:!bg-navy-800 shadow-lg rounded-xl overflow-hidden relative mx-auto flex  flex-col items-center bg-white  dark:text-white dark:shadow-none"
        >
            <div class="relative w-full justify-center rounded-t-lg bg-cover bg-white overflow-hidden ">
                <img :src="tempCover" alt="" class="w-full h-36 sm:h-52 md:h-72 lg:h-70">
                <div id="cover" class="absolute top-2 left-2 md:h-28 lg:h-60">
                    <input type="file" class="hidden" ref="coverInput" accept="image/*" @change="onCoverInputChange" >
                    <button type="button" @click.prevent="browseCover" class="rounded-full bg-white bg-opacity-60 p-1 text-gray-600">
                        <Icon icon="heroicons-outline:photograph" class="w-6 h-6" />
                    </button>
                </div>

                <div class="w-full flex ">
                    <div class="w-full mx-4 flex justify-center sm:justify-between -mt-12 sm:-mt-14 md:-mt-16 lg:-mt-20 items-center">
                        <div class=" bg-green-300 flex flex-col sm:flex-row items-center justify-center sm:justify-start w-full">
                            <div class="bg-purple-400 relative max-w-fit w-[88px] h-[88px] sm:w-28 sm:h-28 md:w-32 md:h-32 lg:w-40 lg:h-40 flex justify-center items-center border-gray-400 border-2 rounded-full overflow-hidden">
                                <img class="bg-cover object-center h-full" :src="tempLogo" alt='school logo'>
                                <input type="file" class="hidden" accept="image/*" ref="logoInput" @change="onLogoInputChange" >
                                <button type="button" @click.prevent="browseLogo" class="absolute bottom-0 bg-white bg-opacity-60 text-gray-600 border-[1px] border-gray-300 transition duration-200 rounded-full md:p-1">
                                    <Icon icon="heroicons:camera" class="w-4 h-4 " />
                                </button>
                            </div>
                            <div class="bg-blue-300 flex flex-col justify-center w-full sm:w-[80%] ">
                                <div class="bg-red-400 mb-4 max-w-fit flex flex-wrap justify-center sm:justify-start overflow-x-auto space-x-2 relative">
                                    <input type="text" name="" v-model="tempHeader" v-if="inputHeaderEditing"
                                        id="coverHeaderNameInput"
                                        class="bg-white bg-opacity-60 rounded-md bg-transparent border-none text-center sm:text-start font-semibold text-2xl"

                                    >
                                    <p v-else class="bg-white bg-opacity-60 font-semibold text-2xl max-w-fit p-2 rounded-md">{{ tempHeader }}</p>
                                    <div class="absolute top-0 right-0">
                                        <button v-if="inputHeaderEditing" @click="inputHeaderEditing = false">
                                            <Icon icon="fluent:save-24-regular" class="w-6 h-6 bg-white bg-opacity-60 rounded-full text-gray-700 p-1" />
                                        </button>
                                        <button v-else @click="inputHeaderEditing = true">
                                            <Icon icon="heroicons:pencil-square" class="w-6 h-6 bg-white bg-opacity-60 rounded-full text-gray-700 p-1" />
                                        </button>
                                    </div>
                                </div>
                                <div class="">
                                    <input type="text" v-model="tempSubheader"
                                        class="rounded-md bg-transparent border-none text-center sm:text-start"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="space-x-2 hidden md:flex min-w-fit mx-6">
                            <button class="bg-[#8581fa] py-2 px-4 rounded-lg text-white shadow-lg "> + เข้าร่วม</button>
                            <button class="bg-[#23d2e2] py-2 px-4 rounded-lg text-white shadow-lg "> + Message</button>
                        </div>
                    </div>
                </div>

                <!-- <div class="-mt-12 md:-mt-14 lg:-mt-[72px] w-full flex flex-col md:flex-row md:justify-start md:ml-4 items-center space-x-2">
                    <div class="relative w-24 h-24 md:w-28 md:h-28 lg:w-36 lg:h-36 flex justify-center items-center border-gray-400 border-2 rounded-full overflow-hidden">
                        <img class="bg-cover object-center h-full" :src="tempLogo" alt='school logo'>
                        <input type="file" class="hidden" accept="image/*" ref="logoInput" @change="onLogoInputChange" >
                        <button type="button" @click.prevent="browseLogo" class="absolute bottom-0 bg-white bg-opacity-60 text-gray-600 border-[1px] border-gray-300 transition duration-200 rounded-full md:p-1">
                            <Icon icon="heroicons:camera" class="w-4 h-4 " />
                        </button>
                    </div>
                    <div class="flex flex-col justify-center md:space-y-4 ">
                        <div class=" text-center md:text-start">
                            <span class="font-semibold text-xl text-[#272727e5] bg-white bg-opacity-80 px-2 rounded-md">{{ coverHeader }}</span>
                            <input type="text" name="" v-model="tempHeader" 
                                id="coverHeaderNameInput"
                                :class="`w-[${inputWidth}rem]`"
                                class="rounded-md bg-transparent border-none"    
                            >
                            <span><button>edit</button></span>
                        </div>
                        <div class="w-full">
                            <span class="text-base text-gray-700">{{ coverSubheader }}</span>
                        </div>
                    </div>
                </div> -->

                <!-- <div class="absolute hidden md:block bottom-[30px] lg:bottom-10 right-0 space-x-4 m-2">
                    <button class="bg-[#8581fa] border-2 border-gray-200 py-2 px-4 rounded-lg text-white shadow-lg"> + เข้าร่วม</button>
                    <button class="bg-[#23d2e2] py-2 px-4 rounded-lg text-white shadow-lg"> + Message</button>
                </div> -->
            </div>
            <!-- <div class="mt-4 flex flex-col items-center">
                <p class="text-bluePrimary text-xl font-bold">
                    {{ coverName }}
                </p>
                <p class="text-lightSecondary text-base font-normal">
                    Product Manager
                </p>
            </div> -->
            <div class="mt-3 mb-3 md:flex gap-4 md:!gap-14">
                <div class="flex justify-center space-x-4 md:space-x-10">
                    <div class="flex flex-col items-center justify-center">
                        <h3 class="text-bluePrimary text-2xl font-bold">17</h3>
                        <p class="text-lightSecondary text-sm font-normal">Posts</p>
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        <h3 class="text-bluePrimary text-2xl font-bold">9.7K</h3>
                        <p class="text-lightSecondary text-sm font-normal">
                            Followers
                        </p>
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        <h3 class="text-bluePrimary text-2xl font-bold">434</h3>
                        <p class="text-lightSecondary text-sm font-normal">
                            Following
                        </p>
                    </div>
                </div>
                <div class="md:hidden space-x-4 m-2">
                    <button class="bg-[#615dfa] py-2 px-4 rounded-lg text-white shadow-lg"> + Add friend</button>
                    <!-- <button class="bg-[#23d2e2] py-2 px-4 rounded-lg text-white shadow-lg"> + Message</button> -->
                </div>
            </div>
        </div>
    </div>
</template>
