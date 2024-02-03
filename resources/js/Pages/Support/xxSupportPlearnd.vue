<script setup lang="ts">
import { ref,reactive, onMounted, computed } from 'vue';
import { Icon } from '@iconify/vue';
import { useObjectUrl } from '@vueuse/core'
import MainLayout from '@/Layouts/MainLayout.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

// import SlipUpload from './SlipUpload.vue';
// import FilePreview  from  './SlipPreview.vue'
// import  createUploader  from  './compositions/file-uploader'
// import useFileList from './compositions/file-list.js';
// const { uploadFiles } = createUploader('YOUR URL HERE')
// const { files, addFiles, removeFile } = useFileList();

const props = defineProps({
    Supporter: Object,
});


const totalMoneySupport = ref(20);
const timeToShowProductMedia = ref(1);
const moneyIndexSelected = ref(0);
const timeIndexSelected = ref(0);
const quantityToShowProductMedia = ref(0);
const tip = ref(0);
const slipImage = ref(null);
const transferDate = ref(new Date().toLocaleDateString());
const transferTime = ref(new Date().toLocaleDateString());
const inputImages = ref(null);
const draging = ref(false);


const generateOptions = (min, max, step) => {
    const options = [];
    for (let i = min; i <= max; i += step) {
        options.push(i);
    }
    return options;
}

const timeToShowProductMediaOptions = ref(generateOptions(1, 15, 1));
const totalMoneySupportOptions = ref(generateOptions(20, 100, 10));

function computeQuantityToShowProductMedia(mIdx=0,tIdx=0){
    totalMoneySupport.value = totalMoneySupportOptions.value[mIdx];
    timeToShowProductMedia.value = timeToShowProductMediaOptions.value[tIdx];
    quantityToShowProductMedia.value = Math.floor(((totalMoneySupport.value)*684)/(timeToShowProductMedia.value*64));
    tip.value = totalMoneySupport.value >= 50 && timeToShowProductMedia.value >= 5 ? quantityToShowProductMedia.value + timeToShowProductMedia.value : quantityToShowProductMedia.value;
    // return Math.floor(((totalMoneySupport.value)*684)/(timeToShowProductMedia.value*6.84));
}

const browseInputImages = () => { inputImages.value.click() };
function onInputImageChange(e) {
    // console.log(e.target.files);
    slipImage.value = {
        file: e.target.files[0],
        url: URL.createObjectURL(e.target.files[0]),
    };
}

function onDropFile(e){
    // let file = e.target.files[0];
    let files = [...e.dataTransfer.items]
        .filter( item => item.kind ==='file' )
        .map(item => item.getAsFile());

    slipImage.value = {
        file: files[0],
        url: useObjectUrl(files[0]),
    };

}

function deleteImage(){ slipImage.value = null;}



const form = reactive({
    supporter: totalMoneySupport.value,
    totalMoney: totalMoneySupport.value,
    duration: timeToShowProductMedia.value,
    viewQuantity: quantityToShowProductMedia.value,
    transferDate: transferDate.value,
    transferTime: transferTime.value,
    // transferSlip: slipImage.value.file,
});

onMounted(() => {computeQuantityToShowProductMedia()})

function onSubmitForm(){
    try {
        // const config = { headers: { 'Content-Type': 'multipart/form-data' } };
        // let newSupportForm = new FormData();
        // newSupportForm.append('data', form , );
        console.log(form);
    } catch (error) {
        console.log(error);
    }
}

</script>

<template>
    <div>
        <MainLayout title="Support Plearnd.com">
            <template #coverProfileCard>
                <div class="flex items-center max-w-7xl mx-auto jusb mt-2 mb-4 shadow-lg bg-[url('/storage/images/banner/banner-bg.png')] bg-cover bg-no-repeat rounded-lg overflow-hidden">
                    <img class="" :src="'/storage/images/banner/badges-icon.png'" alt="forums-icon">
                    <p class="text-white font-bold text-4xl">สนับสนุน เว็บไซต์ เพลินด์</p>
                </div>
            </template>
            <template #mainContent>
                <div>
                    <div class="plearnd-card">
                        <!-- Payment Information -->
                        <div>
                            <h2 class="text-xl font-semibold text-gray-700 dark:text-white mb-2">
                                ข้อมูลการสนับสนุน
                            </h2>

                            <h3 class="text-lg font-semibold text-gray-600 dark:text-white mb-2">
                                ผู้สนับสนุน
                            </h3>
                            <div class="max-w-sm space-y-2 sm:py-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-2">
                                <img class="block mx-auto h-14 rounded-full sm:mx-0 sm:shrink-0" :src="Supporter.data.avatar" :alt="Supporter.data.name">
                                <div class="text-center space-y-2 sm:text-left">
                                    <div class="space-y-0.5">
                                        <p class="text-xl text-black font-semibold">
                                            {{ Supporter.data.name }}
                                        </p>
                                        <!-- <p class="text-slate-500 font-medium">
                                            Product Engineer
                                        </p> -->
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 mt-4">
                                <div>
                                    <label for="money-quantity" class="block text-gray-700 dark:text-white mb-1">
                                        จำนวนเงิน
                                    </label>
                                    <!-- <input type="number" min="20" id="card_number" v-model="totalMoneySupport"  @input="computeQuantityToShowProductMedia"
                                        class="w-full text-center rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none"> -->
                                        <select id="money-quantity" v-model="moneyIndexSelected" @change.prevent="computeQuantityToShowProductMedia(moneyIndexSelected, timeIndexSelected)"
                                        class="w-full text-center rounded-lg border border-gray-400 py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none">

                                        <option v-for="(moneyOption, midx) in totalMoneySupportOptions" :key="midx" :value="midx" :selected="moneyOption.value==20">
                                            {{ moneyOption }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-4 mt-4">
                                <div>
                                    <label for="time-to-show-Product-media-dropdown" class="block text-gray-700 dark:text-white mb-1 w-3/4">ระยะเวลาแสดงผลสื่อประชาสัมพันธ์สินค้า(วินาที/ครั้ง)</label>
                                    <select id="time-to-show-Product-media-dropdown" v-model="timeIndexSelected" @change.prevent="computeQuantityToShowProductMedia(0, timeIndexSelected)"
                                        class="w-full text-center rounded-lg border border-gray-400 py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none">
                                        <!-- <option value="5" selected>5</option> -->
                                        <option v-for="(timeOption, tidx) in timeToShowProductMediaOptions" :key="tidx" :value="tidx" :selected="timeOption.value==1">
                                            {{ timeOption }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-4 mt-4">
                                <div>
                                    <p class="block text-gray-700 dark:text-white mb-1">จำนวนที่จะแสดงผลสื่อประชาสัมพันธ์สินค้า</p>
                                    <p  class="w-full rounded-lg border border-gray-400 bg-gray-300 text-center py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none">
                                        <!-- {{ quantityToShowProductMedia }}  (+{{ timeToShowProductMedia }} ) -->
                                        {{ tip }}
                                    </p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-4 mt-4">
                                <div v-if="slipImage">
                                    <div class="relative mb-2 max-h-fit overflow-hidden">
                                        <img :src="slipImage.url" class="rounded-lg"/>
                                        <button @click.prevent="deleteImage"
                                        class="absolute top-1 left-1 rounded-full cursor-pointer bg-gray-100 p-2">
                                            <Icon icon="fa-solid:trash-alt" class="w-5 h-5 text-red-500" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-4 mt-4" v-if="!slipImage">
                                <div id="dropzone"
                                    @dragover.prevent="draging = true"
                                    @dragleave.prevent="draging = false"
                                    @drop.prevent="onDropFile"
                                    :class="draging ? 'bg-blue-200 border-blue-400': 'bg-gray-200 border-gray-400'"
                                    class="w-full relative border-2 border-dashed rounded-lg p-2"
                                >
                                    <!-- <input type="file" class="absolute inset-0 w-full h-full opacity-0 z-50" /> -->
                                    <div class="text-center">
                                        <img class="mx-auto h-8 w-8 opacity-70"
                                            src="https://www.svgrepo.com/show/357902/image-upload.svg" alt="">

                                        <h3 class="mt-2 text-sm font-medium text-gray-900">
                                            <p class="relative">
                                                <span>Drag and drop</span>
                                                <button class="plearnd-btn-primary w-36 mx-2" @click.prevent="browseInputImages"> or browse</button>
                                                <span>to upload</span>
                                            </p>
                                            <input id="file-upload" type="file" accept="image/*" ref="inputImages" class="hidden"  @change.prevent="onInputImageChange">
                                        </h3>
                                        <p class="mt-1 text-xs text-gray-500">
                                            PNG, JPG, GIF up to 10MB
                                        </p>
                                    </div>
                                    <!-- <img v-if="slipImage" :src="slipImage.url" class="mt-4 mx-auto max-h-40 hidden" id="preview"> -->
                                </div>
                                <!-- <SlipUpload class="drop-area"
                                    @files-dropped="addFiles"
                                    #default="{ dropZoneActive }"
                                >
                                    <label for="file-input">
                                        <span v-if="dropZoneActive">
                                            <span>Drop Them Here</span>
                                            <span class="smaller">to add them</span>
                                        </span>
                                        <span v-else>
                                            <span>Drag Your Files Here</span>
                                            <span class="smaller">
                                                or <strong><em>click here</em></strong> to select files
                                            </span>
                                        </span>
                                        <input type="file" id="file-input" @change="onInputChange" class="" />
                                    </label>
                                    <ul class="image-list" v-show="files.length">
                                        <FilePreview  v-for="file  of  files" :key="file.id" :file="file"  tag="li" @remove="removeFile" />
                                    </ul>
                                </SlipUpload> -->
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <p class="block text-gray-700 dark:text-white mb-1">
                                    วัน/เวลา ที่ทำรายการ
                                </p>
                            </div>
                            <div class="grid grid-cols-1 gap-4 ">
                                <div class="-mx-3 flex flex-wrap">
                                    <div class="w-full px-3 sm:w-1/2">
                                        <div class="mb-5">
                                            <!-- <label for="date" class=" block text-base font-medium text-[#07074D]">
                                                วันที่
                                            </label> -->
                                            <!-- <input type="date" v-model="transferDate"  id="date"
                                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" /> -->

                                            <VueDatePicker v-model="transferDate" :format="'dd-mm-yyyy'" :enable-time-picker="false"></VueDatePicker>
                                        </div>
                                    </div>
                                    <div class="w-full px-3 sm:w-1/2">
                                        <div class="mb-5">
                                            <!-- <label for="time" class=" block text-base font-medium text-[#07074D]">
                                                เวลา
                                            </label> -->
                                            <!-- <input type="time" v-model="transferTime" id="time"
                                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                            -->

                                            <VueDatePicker v-model="transferTime" time-picker />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-4 ">
                                <p class="block text-red-500 text-sm dark:text-white mb-1">
                                    กรุณาตรวจสอบวันที่และเวลาให้ตรงกับใบสลิปโอนเงิน (หากเกิดข้อผิดพลาดจะไม่สามารถคืนเงินได้)
                                </p>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button @click.prevent="onSubmitForm"
                                class="bg-teal-500 text-white px-4 py-2 rounded-lg hover:bg-teal-700 dark:bg-teal-600 dark:text-white dark:hover:bg-teal-900"
                            >
                                บันทึกข้อมูล
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </MainLayout>
    </div>
</template>

