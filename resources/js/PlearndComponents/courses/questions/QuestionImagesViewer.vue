<script setup>
import { ref } from 'vue';
import { Icon } from '@iconify/vue';
import axios from 'axios';

const props = defineProps({
    model_id: Number,
    images: Object,
    edit: Boolean
});
const emit = defineEmits(['update:images']);

const isLoading = ref(false);

async function deleteImage(index, id){
    isLoading.value = true;
    // const response = await axios.delete(`/topics/${props.model_id}/images/${id}`);
    const response = await axios.delete(`/asmimages/${id}`);
    if (response.status === 200) {
        props.images.splice(index,1);
    }
    isLoading.value = false;
    // emit('update:images');
}

</script>
<template>
    <div class="">
        <!-- <div class="flex flex-wrap columns-2 md:columns-3 lg:columns-4"> -->
        <div class="columns-1">
            <div v-if="images && images.length<5">
                <div v-for="(image,index) in images" :key="image.image_url" class="">
                    <div class="relative max-h-fit overflow-hidden">
                        <!-- <a href=""> -->
                            <!-- <img :src="image.url" class="rounded-lg hover:scale-110 duration-200"/> -->
                        <img :src="'../../storage/'+image.image_url" class="rounded-lg"/>
                        <!-- </a> -->
                        <button v-if="edit" @click.prevent="deleteImage(index,image.id)"
                        class="absolute top-1 right-1 rounded-full cursor-pointer bg-gray-100 p-[6px]">
                            <!-- <Icon icon="heroicons:x-circle" class="w-6 h-6 text-red-500" /> -->
                            <Icon icon="fa-solid:trash-alt" class="w-4 h-4 text-red-500" />
                        </button>
                    </div>
                </div>
            </div>
            <div v-else class="columns-2">
                <div v-for="(i) in 4" :key="i" class="">
                    <div class="relative max-h-fit overflow-hidden">
                        <a href="">
                            <img :src="'../../storage/'+images[i].image_url" class="rounded-lg hover:scale-110 duration-300" />
                        </a>
                        <button class="absolute top-1 left-1 rounded-full ">
                            <Icon icon="heroicons:x-circle" class="w-6 h-6 text-red-500" />
                        </button>
                    </div>
                </div>
                <div class="flex justify-center items-center"> 
                    <!-- <div class="group relative mb-2 max-h-fit overflow-hidden"> -->
                    <div class="relative overflow-hidden">
                        <img :src="'../../storage/'+images[5].image_url" alt="" class="rounded-lg hover:scale-110 brightness-50 duration-300">
                        <a href="" class="absolute top-[calc(50%-20px)] left-[calc(50%-24px)] text-white text-3xl w-24">+ {{ images.length-5 }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
