<template>
    <div class="ml-[2px]">
        <div class="flex gap-4 w-full justify-start">
            <img :src="comment.user.avatar"
                class="w-9 h-9 p-[2px] rounded-full ring-1 ring-blue-600 dark:ring-gray-500" alt="">
            <div class="w-full">
                <div class="flex space-x-2 items-end">
                    <h6 class="mb-0">{{ comment.user.name }}</h6>
                    <span class="text-gray-600 text-sm">{{ comment.create_at }}</span>
                </div>
                <p class="text-gray-700 text-sm bg-slate-50 border-[1.5px] border-gray-200 p-1 ml-auto rounded-lg my-2">
                    {{ comment.content }}
                </p>
                <div class="flex items-center gap-4 text-xs" v-if="isNotCommentOwner">
                    <button @click.prevent="handleLikesComment" :disabled="refComment.isDislikedByAuth"
                        class="flex items-center space-x-1  px-2 py-1 rounded-md text-green-500 ">
                        <div class="flex justify-around items-center space-x-2 hover:scale-125">
                            <span v-if="refComment.isLikedByAuth" >
                                <Icon icon="icon-park-solid:like" class="w-5 h-5 " />
                            </span>
                            <span v-else>
                                <Icon icon="icon-park-outline:like" class="w-5 h-5"  />
                            </span>
                            <!-- <Icon v-else icon="heroicons-outline:thumb-up" :class="'w-6 h-6 ' + 'text' + reactionTheme"  /> -->
                        </div>
                        <span>{{ refComment.likes }}</span>
                    </button>
                    <button @click.prevent="handleDislikesComment" :disabled="refComment.isLikedByAuth"
                        class="flex space-x-1 px-2 py-1 rounded-md text-red-500">
                        <Icon :icon="`heroicons-${ refComment.isDislikedByAuth ? 'solid':'outline'}:thumb-down`" class="w-5 h-5 hover:scale-125" />
                        <span>{{ refComment.dislikes }}</span>
                    </button>
                    <!-- <button 
                        class="text-primary-500 flex space-x-1 hover:bg-blue-300 hover:scale-110 px-2 py-1 rounded-md">
                        <Icon icon="heroicons-outline:reply" class="w-5 h-5 text-gray-600" />
                        <span class="">ตอบกลับ</span>
                    </button> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref,computed } from 'vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
const props = defineProps({
    comment: Object
});

const refComment = ref(props.comment);
const authUser = usePage().props.auth.user;
const isLoading = ref(false);
const isNotCommentOwner = computed(()=> props.comment.user.id !== authUser.id);
const handleLikesComment = async () => {
    isLoading.value = true;
    await axios.post(`/post_comments/${props.comment.id}/like`);    
    refComment.value.isLikedByAuth = ! refComment.value.isLikedByAuth;
    if(refComment.value.isLikedByAuth){
        refComment.value.likes++;
        authUser.pp--;
        refComment.value.user.point++;
    }else{
        refComment.value.likes--;  
    }    
    isLoading.value = false;   
};

const handleDislikesComment = async () => {
    isLoading.value = true;
    await axios.post(`/post_comments/${props.comment.id}/dislike`);
    refComment.value.isDislikedByAuth = ! refComment.value.isDislikedByAuth;
    if(refComment.value.isDislikedByAuth){
        refComment.value.dislikes++;
        authUser.pp--;
    }
    else{
        refComment.value.dislikes--;   
    }    
    isLoading.value = false;  
};
</script>
