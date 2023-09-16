<template>
    <div>
        <!-- :class="[ 
            isLikedByAuth ? 'text-white bg-'+ reactionTheme + ' border-' + reactionTheme  : ' text-gray-700 hover:border-' + reactionTheme + ' hover:text-'+ reactionTheme +' ',
            !isLikedByAuth && isLoading ? ' text-'+ reactionTheme : ''
        ]" -->
        <button  @click.prevent="handleSubmit" :disabled="isLoading || isDislikedByAuth" 
            :class="[refIsLikedByAuth ? 'text-green-500' : 'text-gray-500', !isDislikedByAuth ? 'hover:scale-110 cursor-pointer':'']"
            class=" flex justify-center items-center space-x-1 text-sm rounded-full p-2 ">
            <div v-if="isLoading">
                <Icon icon="eos-icons:bubble-loading" class="w-6 h-6 "/>
            </div>
            <div v-else class="flex justify-around items-center space-x-2">
                <span v-if="refIsLikedByAuth" >
                    <Icon icon="icon-park-solid:like" class="w-7 h-7" />
                </span>
                <span v-else>
                    <Icon icon="icon-park-outline:like" class="w-7 h-7"  />
                </span>
                <!-- <Icon v-else icon="heroicons-outline:thumb-up" :class="'w-6 h-6 ' + 'text' + reactionTheme"  /> -->
            </div>
            <!-- <span v-if="isLikedByAuth">เลิกถูกใจ</span>
            <span v-else>ถูกใจ</span> -->
        </button>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import axios from 'axios';
    // import { useForm } from "@inertiajs/vue3";
    import { Icon } from '@iconify/vue';

    const props = defineProps({
        desModelId: { type: Number, default: 0},
        isLikedByAuth: { type: Boolean, default: false },
        isDislikedByAuth: { type: Boolean, default: false },
    });
    
    const emit = defineEmits(['user-like-post', 'user-unlike-post']);
    const refIsLikedByAuth = ref(props.isLikedByAuth);
    
    const isLoading = ref(false);
    // const reactionForm = useForm({});
    const handleSubmit = async () => {
        isLoading.value = true;
        // reactionForm.post(`/posts/${props.desModelId}/like`,
        //     { 
        //         preserveScroll: true, 
        //         onSuccess: () => {
        //             refIsLikedByAuth.value = !refIsLikedByAuth.value;

        //             if(refIsLikedByAuth.value){
        //                 emit('user-unlike-post');
        //             }else{
        //                 emit('user-like-post');
        //             }
        //             isLoading.value = false; 
        //         } 
        //     });
        
        axios.post(`/posts/${props.desModelId}/like`);
        
        refIsLikedByAuth.value = !refIsLikedByAuth.value;
        if(refIsLikedByAuth.value){
            emit('user-like-post');
        }else{
            emit('user-unlike-post');
        }
        
        isLoading.value = false;             


    };
</script>
