<script setup>
import { ref } from 'vue';
import { Icon } from '@iconify/vue';

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
    subModelData: Object,
    subModelNameTh: String,
    groups: Object,
    courseMemberOfAuth: Object,
});

const emit = defineEmits([
    'update:coverHeader',
    'update:coverSubheader',
    'cover-image-change', 
    'logo-image-change',
    'header-change',
    'subheader-change',
    'request-tobe-member',
    'request-tobe-unmember'
]);

const logoInput = ref(null);
const coverInput = ref(null);
const tempLogo = ref(props.logoImage);
const tempCover = ref(props.coverImage);
const tempHeader = ref(props.coverHeader);
const tempSubheader = ref(props.coverSubheader);

const showOptionGroups = ref(false);
const showAcceptMemberOption = ref(false);
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
    emit('header-change', props.coverHeader);
    inputHeaderEditing.value = false;
}
function onSubheaderSave(e){
    e.preventDefault();
    emit('subheader-change', props.coverSubheader);
    inputSubheaderEditing.value = false;
}

function onRequestToBeMember(groupId, groupIndx){
    emit('request-tobe-member', groupId, groupIndx);
    showOptionGroups.value = false;
}

function onRequestToBeUnMember(){
    emit('request-tobe-unmember', props.courseMemberOfAuth.id);
    showAcceptMemberOption.value = false;
}

</script>
<template>
    <div class="relative w-full rounded-lg bg-cover bg-white ">
        <div class="">
            <img :src="tempCover" alt="" class="w-full h-36 sm:h-52 md:h-72 lg:h-70 rounded-t-lg">
            <div id="cover-input-label" class="absolute top-2 left-2 md:h-28 lg:h-60" v-if="$page.props.isCourseAdmin">
                <input type="file" class="hidden" ref="coverInput" accept="image/*" @change="onCoverInputChange">
                <button type="button" @click.prevent="browseCover"
                    class="rounded-full bg-white bg-opacity-60 p-1 text-gray-600">
                    <Icon icon="heroicons-outline:photograph" class="w-6 h-6" />
                </button>
            </div>
            <div id="cover-tuition-fees" class="absolute top-0 right-0 md:h-28 lg:h-60">
                <p class=" bg-yellow-300/75 flex items-center space-x-1 text-white font-semibold py-1 px-3 rounded-tr-lg rounded-bl-lg">
                    <Icon icon="ri:bit-coin-line" class="w-6 h-6 " />
                    <span class="text-lg">
                        {{ props.modelData.tuition_fees }} 
                    </span>
                </p>
            </div>

        </div>
        <div class="flex flex-col sm:flex-row items-center justify-center sm:justify-between w-full px-4 -mt-[52px] sm:-mt[-54-px] md:-mt-[64px] lg:-mt-[84px]">
            <div class="flex flex-col sm:flex-row items-center w-[69%]">
                <!-- <div class="relative max-w-fit w-[88px] h-[88px] sm:w-28 sm:h-28 md:w-32 md:h-32 lg:w-40 lg:h-40 flex justify-center items-center border-gray-400 border-2 rounded-full overflow-hidden"> -->
                <div class="relative min-w-fit flex justify-center items-center border-white border-[4px] rounded-full overflow-hidden ">
                    <img class="bg-cover object-center w-[88px] h-[88px] sm:w-28 sm:h-28 md:w-32 md:h-32 lg:w-40 lg:h-40" :src="tempLogo" alt='school logo'>
                    <input type="file" class="hidden" accept="image/*" ref="logoInput" @change="onLogoInputChange" v-if="$page.props.isCourseAdmin" >
                    <button type="button" @click.prevent="browseLogo" v-if="$page.props.isCourseAdmin"
                        class="absolute bottom-0 bg-white bg-opacity-60 text-gray-600 border-[1px] border-gray-300 transition duration-200 rounded-full md:p-1">
                        <Icon icon="heroicons:camera" class="w-4 h-4 " />
                    </button>
                </div>
                <div class="sm:ml-4 space-y-4 text-center md:text-start w-full">
                    <div class="flex relative justify-center sm:justify-start w-full ">
                        <div class="relative " :class="{'w-full':inputHeaderEditing}">
                            <input type="text" name="" :value="coverHeader" @input="$emit('update:coverHeader', $event.target.value)" v-if="inputHeaderEditing"
                                    id="coverHeaderNameInput"
                                    class="bg-white bg-opacity-60 !w-full rounded-md bg-transparent text-center sm:text-start font-semibold text-base sm:text-xl focus:outline-0"
                            >
                            <p v-else class="bg-slate-300 bg-opacity-80 font-semibold text-base sm:text-xl max-w-fit p-2 rounded-md">{{ coverHeader }}</p>
                            <!-- <div class=""> -->
                                <div v-if="inputHeaderEditing" class="absolute top-0 -right-6 flex flex-col-reverse ">
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
                                    <button  @click="inputHeaderEditing = true" class=" " v-if=" $page.props.isCourseAdmin">
                                        <Icon icon="heroicons:pencil-square"
                                            class="w-6 h-6 bg-white bg-opacity-60 rounded-full text-gray-700 p-1" />
                                    </button>
                                </div>
                            <!-- </div> -->
                        </div>
                    </div>
                    <div class="flex justify-center sm:justify-start">
                        <div class="relative" :class="{'w-full': inputSubheaderEditing}">
                            <textarea rows="1"
                            v-if="inputSubheaderEditing" 
                            :value="coverSubheader"
                            @input="emit('update:coverSubheader', $event.target.value)"
                            class="rounded-lg  text-center sm:text-start font-normal text-lg w-full"
                            ></textarea>
                            <p v-else-if="coverSubheader" class="min-h-fit bg-slate-300 bg-opacity-80 font-normal text-lg p-2 mx-4 sm:mx-0 rounded-md text-center sm:text-start">{{ coverSubheader }}</p>
                            <p v-else-if="$page.props.isCourseAdmin" class="min-h-fit bg-slate-300 bg-opacity-80 font-normal text-lg p-2 mx-4 sm:mx-0 rounded-md text-center sm:text-start">{{ 'แก้ไขรหัสวิชา' }}</p>
                            <div class="absolute top-0 -right-6">
                            <!-- <div class="absolute top-0 -right-1 sm:-right-6"> -->
                                <div v-if="inputSubheaderEditing" class="flex flex-col-reverse">
                                    <button  @click="onSubheaderSave" class=" ">
                                        <Icon icon="fluent:save-24-regular"
                                            class="w-6 h-6 bg-green-400 bg-opacity-60 rounded-full text-gray-700 p-1" />
                                    </button>
                                    <button  @click="inputSubheaderEditing = false" class="">
                                        <Icon icon="heroicons-outline:x"
                                            class="w-6 h-6 bg-red-400 bg-opacity-60 rounded-full text-gray-700 p-1 " />
                                    </button>
                                </div>
                                <div v-else class="">
                                    <button  @click="inputSubheaderEditing = true" class="" v-if="$page.props.isCourseAdmin">
                                        <Icon icon="heroicons:pencil-square"
                                            class="w-6 h-6 bg-gray-200 bg-opacity-60 rounded-full text-gray-700 p-1" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div  v-if="!$page.props.isCourseAdmin" class="hidden md:block space-x-2 mx-4 w-[31%] justify-end">
                <!-- <button v-if="props.modelData.member_status==='0'" class="plearnd-btn-warning">
                    <span class="flex items-center justify-center " @click.prevent="requestToBeUnMember()">
                        - <Icon icon="majesticons:door-exit-line" class="ml-2" /> 
                        <span class="text-sm font-normal mx-2">
                            รอการตอบรับ
                        </span>
                    </span>
                </button> -->
                <!-- <div v-if="props.courseMemberOfAuth" class="group relative cursor-pointer py-2 w-56 ml-auto">
                    <div v-if="props.modelData.member_status==='0'" class=" bg-yellow-500 rounded-md flex justify-center w-full">
                        <button class="flex items-center justify-between w-full menu-hover my-0 py-2 text-base font-medium text-white mx-2" 
                            @click.prevent="()=>{ showAcceptMemberOption = !showAcceptMemberOption }">
                            รอการตอบรับ
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="h-6 w-6 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </div>
                    <div v-if="props.modelData.member_status==='0'" 
                        :class="showAcceptMemberOption ? 'visible ': 'invisible'"
                        class="absolute z-[80] flex w-full flex-col py-1 text-gray-800 shadow-xl ">
                        <button @click.prevent="onRequestToBeUnMember"
                            class="py-2 border-b bg-red-200 rounded-md font-semibold text-red-500 hover:text-red-600 ">
                            ยกเลิกคำขอ
                        </button>                       
                    </div>
                </div> -->
                <!-- Component: Basic dropdown menu -->

                <!-- End Basic dropdown menu-->
                <!-- <button v-if="props.modelData.member_status==='1'" @click.prevent="onRequestToBeUnMember" class="w-full justify-items-end bg-yellow-300 p-2 rounded-md text-gray-700 hover:text-red-800">
                    <span class="flex items-center justify-center ">
                        <Icon icon="majesticons:door-exit-line" class="ml-2" /> 
                        <span class="text-sm font-normal mx-2">
                            ออกจากสมาชิก
                        </span>
                    </span>
                </button> -->
                <!-- <BlueVioletButton v-if="props.modelData.member_status===null" @click.prevent="onRequestToBeMember()">
                    ขอเข้าร่วมสมาชิก
                </BlueVioletButton> -->
                <!-- <div v-if="!props.courseMemberOfAuth" class="group relative cursor-pointer py-2 w-56 ml-auto">

                    <button v-if="props.groups.length==0" 
                        @click.prevent="onRequestToBeMember(0)"
                        class="py-2 text-base font-medium text-white bg-cyan-500 w-full rounded-lg">
                        ขอเป็นสมาชิก
                    </button>

                    <div v-if="props.groups.length>0" class=" bg-cyan-500 rounded-md flex justify-center w-full">
                        <button class="flex items-center justify-between w-full menu-hover my-0 py-2 text-base font-medium text-white mx-2" 
                            @click.prevent="()=>{ showOptionGroups = !showOptionGroups }">
                            ขอเป็นสมาชิก
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="h-6 w-6 text-white transition duration-300" :class="{'rotate-180':showOptionGroups}">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </div>

                    <div v-if="props.groups.length>0" 
                        :class="showOptionGroups ? 'visible transition-all duration-300': 'invisible'"
                        class="absolute z-[80] flex w-full flex-col py-1 text-gray-800 shadow-xl">
                        <button 
                            v-for="(group, index) in props.groups" :key="index"
                            @click.prevent="onRequestToBeMember(group.id, index)"
                            class="py-2 border-b bg-white font-semibold text-gray-600 hover:text-black">
                            {{ group.name }}
                        </button>                       
                    </div>

                </div> -->
            </div>
        </div>

        <div class="my-3 md:flex justify-center gap-4 md:!gap-14">
            <div class="flex justify-center space-x-4 md:space-x-10">
                <div class="flex flex-col items-center justify-center">
                    <h3 class="text-bluePrimary text-2xl font-bold">{{ props.modelData.lessons_count }}</h3>
                    <p class="text-lightSecondary text-sm font-normal">{{ props.subModelNameTh }}</p>
                </div>
                <div class="flex flex-col items-center justify-center" v-if="props.modelData.enrolled_students">
                    <h3 class="text-bluePrimary text-2xl font-bold">{{ props.modelData.enrolled_students }}</h3>
                    <p class="text-lightSecondary text-sm font-normal">ผู้เรียน</p>
                </div>

            </div>
            <!-- <div  v-if="!$page.props.isCourseAdmin" class="justify-center md:hidden space-x-4 m-2">
                <div v-if="props.courseMemberOfAuth" class="group relative cursor-pointer py-2 w-56 mx-auto">
                    <div v-if="props.modelData.member_status==='0'" class=" bg-yellow-500 rounded-md flex justify-center w-full">
                        <button class="flex items-center justify-between w-full menu-hover my-0 py-2 text-base font-medium text-white mx-2" 
                            @click.prevent="()=>{ showAcceptMemberOption = !showAcceptMemberOption }">
                            รอการตอบรับ
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="h-6 w-6 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </div>
                    <div v-if="props.modelData.member_status==='0'" 
                        :class="showAcceptMemberOption ? 'visible ': 'invisible'"
                        class="absolute z-[80] flex w-full flex-col py-1 text-gray-800 shadow-xl ">
                        <button @click.prevent="onRequestToBeUnMember"
                            class="py-2 border-b bg-red-200 rounded-md font-semibold text-red-500 hover:text-red-600 ">
                            ยกเลิกคำขอ
                        </button>                       
                    </div>
                </div>
                <CyanSeaButton v-else-if="props.modelData.member_status==='1'" @click.prevent="requestToBeUnMember()">
                    <span class="flex items-center justify-center ">
                        - <Icon icon="majesticons:door-exit-line" class="ml-2" /> 
                        <span class="text-sm font-normal mx-2">
                            ออกจากสมาชิก
                        </span>
                    </span>
                </CyanSeaButton>

                <div v-if="!props.courseMemberOfAuth" class="group relative cursor-pointer py-2 w-56 mx-auto">
                    <div v-if="props.groups.length>0" class=" bg-cyan-500 rounded-md flex justify-center w-full">
                        <button class="flex items-center justify-between w-full menu-hover my-0 py-2 text-base font-medium text-white mx-2" 
                            @click.prevent="()=>{ showOptionGroups = !showOptionGroups }">
                            ขอเป็นสมาชิก
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                                stroke-width="1.5" stroke="currentColor" 
                                class="h-6 w-6 text-white"
                                :class="{'rotate-180':showOptionGroups}"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </div>
                    <div v-if="props.groups.length>0" 
                        :class="showOptionGroups ? 'visible ': 'invisible'"
                        class="absolute z-[80] flex w-full flex-col py-1 text-gray-800 shadow-xl ">
                        <button 
                            v-for="(group, index) in props.groups" :key="index"
                            @click.prevent="onRequestToBeMember(group.id)"
                            class="rounded-md p-2 border-b bg-white font-semibold text-gray-600 hover:bg-blue-200 hover:text-blue-600 ">
                            {{ group.name }}
                        </button>                       
                    </div>
                </div>
            </div> -->
        </div>
    </div>
</template>
