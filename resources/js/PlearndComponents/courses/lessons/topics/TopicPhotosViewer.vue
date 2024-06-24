<script setup>
import { ref } from 'vue';
import { Icon } from '@iconify/vue';
import axios from 'axios';

const props = defineProps({
    topic_id: Number,
    images: Object,
    edit: Boolean
});
const emit = defineEmits(['update:images']);

const isLoading = ref(false);

async function deleteImage(index, id){
    isLoading.value = true;
    // const response = await axios.delete(`/topics/${props.topic_id}/images/${id}`);
    const response = await axios.delete(`/topic_images/${id}`);
    if (response.status === 200) {
        props.images.splice(index,1);
        emit('update:images');
    }
    isLoading.value = false;
    // emit('update:images');
}

</script>
<template>
    <div class="mt-4">
        <!-- <div class="flex flex-wrap columns-2 md:columns-3 lg:columns-4"> -->
        <div class="columns-1">
            <div v-if="images">
                <div v-for="(image,index) in images" :key="image.image_url" class="">
                    <div class="relative mb-2 max-h-fit overflow-hidden">
                        <!-- <a href=""> -->
                            <!-- <img :src="image.url" class="rounded-lg hover:scale-110 duration-200"/> -->
                        <img :src="'../../storage/'+image.image_url" class="rounded-lg"/>
                        <!-- </a> -->
                        <button v-if="edit" @click.prevent="deleteImage(index,image.id)"
                        class="absolute top-1 left-1 rounded-full cursor-pointer bg-gray-100 p-2">
                            <!-- <Icon icon="heroicons:x-circle" class="w-6 h-6 text-red-500" /> -->
                            <Icon icon="fa-solid:trash-alt" class="w-5 h-5 text-red-500" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
