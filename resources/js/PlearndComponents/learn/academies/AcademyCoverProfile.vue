<script setup>
import { ref } from 'vue';
import { Link,usePage } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';
import BlueVioletButton from '@/PlearndComponents/accessories/BlueVioletButton.vue';
import CyanSeaButton from '@/PlearndComponents/accessories/CyanSeaButton.vue'


const props = defineProps({
    coverImage: { type: String, default: null },
    logoImage: { type: String, default: null },
    coverHeader: { type: String, default: null },
    coverSubheader: { type: String, default: null },
    model: String,
    modelTable: String,
    modelableId: Number,
    modelableType: String,
    modelableRoute: String,
    modelData: Object,
    // subModelData: Array,
    subModelDataLength: Number,
    subModelNameTh: String,
    isAcademyAdmin: Boolean,
});

const emit = defineEmits([
    'update:coverHeader',
    'update:coverSubheader',
    'cover-image-change', 
    'logo-image-change',
    'header-change',
    'subheader-change',
    'request-tobe-member',
    'request-tobe-unmember',
]);

const logoInput = ref(null);
const coverInput = ref(null);
const tempLogo = ref(props.logoImage);
const tempCover = ref(props.coverImage);
const tempHeader = ref(props.coverHeader);
const tempSubheader = ref(props.coverSubheader);

const inputHeaderEditing = ref(false);
const inputSubheaderEditing = ref(false)
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
function onHeaderSave(){
    emit('header-change', tempHeader.value);
    inputHeaderEditing.value = false;
}
function onSubheaderSave(e){
    e.preventDefault();
    // console.log(tempSubheader);
    emit('subheader-change', tempSubheader.value);
    inputSubheaderEditing.value = false;
}

function requestToBeMember(){
    emit('request-tobe-member');
}

function requestToBeUnMember(){
    emit('request-tobe-unmember');
}
</script>
<template>
    <div>
        <div class="relative w-full rounded-lg bg-cover bg-white overflow-hidden ">
            <img :src="tempCover" alt="" class="w-full h-36 sm:h-52 md:h-72 lg:h-70">
            <div id="cover-change-button" class="absolute top-2 left-2 md:h-28 lg:h-60" v-if="isAcademyAdmin">
                <input type="file" class="hidden" ref="coverInput" accept="image/*" @change="onCoverInputChange">
                <button type="button" @click.prevent="browseCover"
                    class="rounded-full bg-white bg-opacity-60 p-1 text-gray-600">
                    <Icon icon="heroicons-outline:photograph" class="w-6 h-6" />
                </button>
            </div>
            <div id="academy-edit-button" class="absolute top-2 right-2 md:h-28 lg:h-60" v-if="isAcademyAdmin">
                <button type="button" 
                    class="rounded-full bg-white bg-opacity-60 p-1 text-gray-600">
                    <Icon icon="tabler:edit" class="w-6 h-6" />
                </button>
            </div>
            <div class="flex flex-col sm:flex-row items-center justify-center sm:justify-between w-full px-4 -mt-[52px] sm:-mt-[58px] md:-mt-[68px] lg:-mt-[84px]">
                <div class="flex flex-col sm:flex-row items-center w-full ">
                    <div class="relative min-w-fit flex justify-center items-center border-gray-400 border-4 rounded-full overflow-hidden ">
                        <img class="bg-cover object-center w-[88px] h-[88px] sm:w-28 sm:h-28 md:w-32 md:h-32 lg:w-40 lg:h-40" :src="tempLogo" alt='school logo'>
                        <input type="file" class="hidden" accept="image/*" ref="logoInput" @change="onLogoInputChange">
                        <button v-if="isAcademyAdmin" type="button" @click.prevent="browseLogo"
                            class="absolute bottom-0 w-[18px] md:w-[26px] text-center border-[1px] bg-gray-200 bg-opacity-50 transition duration-200 rounded-full md:p-1">
                            <Icon icon="heroicons:camera" class="w-4 h-4 text-gray-600 text-center" />
                        </button>
                    </div>
                    <div class="sm:ml-4 space-y-4 text-center md:text-start w-full">
                        <div class="flex relative justify-center sm:justify-start w-full ">
                            <div class="relative mr-6" :class="{'w-full':inputHeaderEditing}">
                                <input type="text" name="" :value="coverHeader" @input="$emit('update:coverHeader', $event.target.value)" v-if="inputHeaderEditing"
                                        id="coverHeaderNameInput"
                                        class="bg-white bg-opacity-60 !w-full rounded-md bg-transparent text-center sm:text-start font-semibold text-base sm:text-xl focus:outline-0"
                                >
                                <p v-else class="bg-slate-300 bg-opacity-80 font-semibold text-base sm:text-xl max-w-fit p-2 rounded-md">{{ coverHeader }}</p>
                                <div v-if="isAcademyAdmin" class="">
                                    <div v-if="inputHeaderEditing" class="absolute top-0 -right-6 flex flex-col">
                                        <button  @click.prevent="onHeaderSave" class="">
                                            <Icon icon="fluent:save-24-regular"
                                                class="w-6 h-6 bg-green-400 bg-opacity-60 rounded-full text-gray-700 p-1 border border-white" />
                                        </button>
                                        <button  @click="inputHeaderEditing = false" class=" ">
                                            <Icon icon="heroicons-outline:x"
                                                class="w-6 h-6 bg-red-400 bg-opacity-60 rounded-full text-gray-700 p-1 border border-white" />
                                        </button>
                                    </div>
                                    <div v-else class="absolute -top-4 -right-2 sm:-right-6 sm:top-0">
                                        <button  @click="inputHeaderEditing = true" class=" ">
                                            <Icon icon="heroicons:pencil-square"
                                                class="w-6 h-6 bg-white bg-opacity-60 rounded-full text-gray-700 p-1" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center sm:justify-start">
                            <div class="relative mr-6" :class="{'w-full': inputSubheaderEditing}">
                                <textarea v-if="inputSubheaderEditing" :value="coverSubheader" @input="emit('update:coverSubheader', $event.target.value)"
                                class="rounded-lg w-full text-center sm:text-start font-normal text-lg "
                                ></textarea>
                                <p v-else class="min-h-fit bg-slate-300 bg-opacity-80 font-normal text-lg p-2 mx-4 sm:mx-0 rounded-md text-center sm:text-start">{{ coverSubheader }}</p>
                                <div v-if="isAcademyAdmin" class="absolute top-0 -right-6 ">
                                    <div v-if="inputSubheaderEditing" class="flex flex-col">
                                        <button  @click="onSubheaderSave" class=" ">
                                            <Icon icon="fluent:save-24-regular"
                                                class="w-6 h-6 bg-green-400 bg-opacity-60 rounded-full text-gray-700 p-1" />
                                        </button>
                                        <button  @click="inputSubheaderEditing = false" class="">
                                            <Icon icon="heroicons-outline:x"
                                                class="w-6 h-6 bg-red-400 bg-opacity-60 rounded-full text-gray-700 p-1 " />
                                        </button>
                                    </div>
                                    <div v-else>
                                        <button  @click="inputSubheaderEditing = true" class=" ">
                                            <Icon icon="heroicons:pencil-square"
                                                class="w-6 h-6 bg-gray-200 bg-opacity-60 rounded-full text-gray-700 p-1" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="!props.isAcademyAdmin" class="space-x-2 hidden md:flex  mx-4 w-[31%] justify-end">
                    <button v-if="props.modelData.memberStatus==0" class="plearnd-btn-secondary">
                        <span class="flex items-center justify-center " @click.prevent="requestToBeUnMember()">
                            - <Icon icon="majesticons:door-exit-line" class="ml-2" /> 
                            <span class="text-sm font-normal mx-2">
                                รอการตอบรับ
                            </span>
                        </span>
                    </button>
                    <CyanSeaButton v-else-if="props.modelData.memberStatus==1" @click.prevent="requestToBeUnMember()">
                        <span class="flex items-center justify-center ">
                            - <Icon icon="majesticons:door-exit-line" class="ml-2" /> 
                            <span class="text-sm font-normal mx-2">
                                ออกจากสมาชิก
                            </span>
                        </span>
                    </CyanSeaButton>
                    <BlueVioletButton v-else @click.prevent="requestToBeMember()">
                        ขอเข้าร่วมสมาชิก
                    </BlueVioletButton>
                    <!-- <CyanSeaButton>+ message</CyanSeaButton> -->
                </div>
            </div>
            <div class="my-3 md:flex justify-center gap-4 md:!gap-14">
                <div class="flex justify-center space-x-4 md:space-x-10">
                    <div class="flex flex-col items-center justify-center">
                        <h3 class="text-bluePrimary text-2xl font-bold">{{ props.subModelDataLength }}</h3>
                        <p class="text-lightSecondary text-sm font-normal">{{ props.subModelNameTh }}</p>
                    </div>
                    <div class="flex flex-col items-center justify-center" v-if="props.modelData.total_students">
                        <h3 class="text-bluePrimary text-2xl font-bold">{{ props.modelData.total_students }}</h3>
                        <p class="text-lightSecondary text-sm font-normal">สมาชิก</p>
                    </div>
                    <div class="flex flex-col items-center justify-center" v-if="props.modelData.enrolled_students">
                        <h3 class="text-bluePrimary text-2xl font-bold">{{ props.modelData.enrolled_students }}</h3>
                        <p class="text-lightSecondary text-sm font-normal">ผู้เรียน</p>
                    </div>
                    <!-- <div class="flex flex-col items-center justify-center">
                        <h3 class="text-bluePrimary text-2xl font-bold">434</h3>
                        <p class="text-lightSecondary text-sm font-normal">
                            Following
                        </p>
                    </div> -->
                </div>
                <div v-if="!props.isAcademyAdmin" class="flex justify-center md:hidden space-x-4 m-2">
                    <BlueVioletButton @click.prevent="requestToBeMember()" v-if="!props.modelData.memberStatus">
                        <span >
                            + ขอเข้าร่วมสมาชิก
                        </span>
                    </BlueVioletButton>
                    <CyanSeaButton @click.prevent="requestToBeUnMember" v-if="props.modelData.memberStatus==2">
                        <span   class="flex items-center justify-center space-x-3">
                            - <Icon icon="majesticons:door-exit-line" class="ml-2" />
                            <span class="text-sm font-normal mx-2">
                                ออกจากสมาชิก
                            </span>
                        </span>
                    </CyanSeaButton>
                    <!-- <CyanSeaButton>+ message</CyanSeaButton> -->
                </div>
            </div>
    </div>
</div>
</template>
