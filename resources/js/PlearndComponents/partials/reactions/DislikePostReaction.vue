<template>
    <div>
        <button type="submit" @click.prevent="handleSubmit" :disabled="isLoading || isLikedByAuth" 
            :class="[refIsDislikedByAuth ? 'text-red-400' : 'text-gray-500', !isLikedByAuth ? 'hover:scale-110 cursor-pointer': '' ]"
            class=" flex justify-center items-center space-x-1 text-sm rounded-full p-2 ">
            <div v-if="isLoading">
                <Icon icon="eos-icons:bubble-loading" class="w-6 h-6"/>
            </div>
            <div v-else class="flex justify-around items-center space-x-2 ">
                <span v-if="refIsDislikedByAuth">
                    <Icon icon="heroicons-solid:thumb-down" class="w-7 h-7" />
                </span>
                <span v-else>
                    <Icon icon="heroicons-outline:thumb-down" class="w-7 h-7"  />
                </span>
            </div>
        </button>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import axios from 'axios';
    import { Icon } from '@iconify/vue';

    const props = defineProps({
        desModelId: { type: Number, default: 0},
        isLikedByAuth: { type: Boolean, default: false },
        isDislikedByAuth: { type: Boolean, default: false },
    });

    const emit = defineEmits(['user-dislike-post', 'user-undislike-post']);
    const refIsDislikedByAuth = ref(props.isDislikedByAuth);
    
    const isLoading = ref(false);
    const handleSubmit = () => {
        isLoading.value = true;

        axios.post(`/posts/${props.desModelId}/dislike`);
        
        refIsDislikedByAuth.value = !refIsDislikedByAuth.value;

        if(refIsDislikedByAuth.value){
            emit('user-dislike-post');
        }else{
            emit('user-undislike-post');
        }
        
        isLoading.value = false;        
    };
</script>
