<script setup>
import { Icon } from '@iconify/vue';
const props = defineProps({
    images: Object,
    edit: Boolean
});

const emit = defineEmits(['update:images']);

function deleteImage(index){
    props.images.splice(index,1);
    emit('update:images');
    // props.images.splice(0);
}

</script>
<template>
    <div class="">
        <!-- <div class="flex flex-wrap columns-2 md:columns-3 lg:columns-4"> -->
        <div class="columns-1">
            <div v-if="images">
                <div v-for="(image,index) in images" :key="image.url" class="">
                    <div class="relative mb-2 max-h-fit overflow-hidden">
                        <!-- <a href=""> -->
                            <!-- <img :src="image.url" class="rounded-lg hover:scale-110 duration-200"/> -->
                        <img :src="image.url" class="rounded-lg"/>
                        <!-- </a> -->
                        <button v-if="edit" @click.prevent="deleteImage(index)"
                        class="absolute top-1 left-1 rounded-full cursor-pointer bg-gray-100 p-2">
                            <!-- <Icon icon="heroicons:x-circle" class="w-6 h-6 text-red-500" /> -->
                            <Icon icon="fa-solid:trash-alt" class="w-5 h-5 text-red-500" />
                        </button>
                    </div>
                </div>
            </div>
            <!-- <div v-else> -->
                <!-- <div v-for="(i) in 4" :key="i" class="">
                    <div class="relative mb-2 max-h-fit overflow-hidden">
                        <a href="">
                            <img :src="images[i].url" class="rounded-lg hover:scale-110 duration-300" />
                        </a>
                        <button class="absolute top-1 left-1 rounded-full ">
                            <Icon icon="heroicons:x-circle" class="w-6 h-6 text-red-500" />
                        </button>
                    </div>
                </div> -->
                <!-- <div class="flex justify-center items-center">  -->
                    <!-- <div class="group relative mb-2 max-h-fit overflow-hidden" :class="`bg-[url(${images[5].url.toString()})]`"> -->
                    <!-- <div class="group relative mb-2 max-h-fit overflow-hidden"> -->
                        <!-- <img :src="images[5].url" alt="" class="rounded-lg hover:scale-110 brightness-50 duration-300"> -->
                        <!-- <a href="" class="absolute top-1/2 left-1/2 text-white text-4xl w-24">+ {{ images.length-5 }}</a> -->
                        <!-- <div class="absolute top-0 left-0 w-full rounded-lg flex flex-col justify-center items-center bg-gray-800 opacity-60 h-full  duration-500"> -->
                            <!-- <a class="absolute top-0 left-0 text-white text-xl hover:scale-125 duration-200" href="#">+{{ images.length-5 }}</a> -->
                        <!-- </div> -->
                    <!-- </div> -->
                <!-- </div> -->
            <!-- </div> -->
        </div>
    </div>
</template>
