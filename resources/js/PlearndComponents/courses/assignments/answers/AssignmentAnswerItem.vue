<script setup>
import { ref,computed } from 'vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import AssignmentImagesViewer from '../AssignmentImagesViewer.vue';
import DotsDropdownMenu from '@/PlearndComponents/accessories/DotsDropdownMenu.vue';
// import { format } from 'date-fns';
const props = defineProps({
    answer: Object
});
const emit = defineEmits(['delete-answer']);

// const refanswer = ref(props.answer);
// const authUser = usePage().props.auth.user;
// const isLoading = ref(false);
// const isNotanswerOwner = computed(()=> props.answer.user.id !== authUser.id);
// const handleLikesanswer = async () => {
//     isLoading.value = true;
//     await axios.post(`/post_answers/${props.answer.id}/like`);    
//     refanswer.value.isLikedByAuth = ! refanswer.value.isLikedByAuth;
//     if(refanswer.value.isLikedByAuth){
//         refanswer.value.likes++;
//         authUser.pp--;
//         refanswer.value.user.point++;
//     }else{
//         refanswer.value.likes--;  
//     }    
//     isLoading.value = false;   
// };

// const handleDislikesanswer = async () => {
//     isLoading.value = true;
//     await axios.post(`/post_answers/${props.answer.id}/dislike`);
//     refanswer.value.isDislikedByAuth = ! refanswer.value.isDislikedByAuth;
//     if(refanswer.value.isDislikedByAuth){
//         refanswer.value.dislikes++;
//         authUser.pp--;
//     }
//     else{
//         refanswer.value.dislikes--;   
//     }    
//     isLoading.value = false;  
// };

const onDeleteAsmAnswer = () => emit('delete-answer');

</script>
<template>
    <div class="">
        <div class="flex gap-4 w-full justify-start">
            <img :src="props.answer.student.avatar"
                class="w-9 h-9 p-[2px] rounded-full ring-1 ring-blue-600 dark:ring-gray-500" alt="">
            <div class="w-full">
                <div class="flex justify-between">
                    <div class="flex items-end space-x-2">
                        <p class="mb-0">{{ props.answer.student.name }}</p>
                        <span class="text-gray-600 text-sm">{{ props.answer.created_at }}</span>
                    </div>
                    <div v-if="$page.props.isCourseAdmin">
                        <DotsDropdownMenu @delete-model="(index) => onDeleteAsmAnswer" />
                    </div>
                </div>
                <p v-if="props.answer.content" class="text-gray-700 text-sm bg-slate-50 border-[1.5px] border-gray-200 p-2 ml-auto rounded-lg my-2">
                    {{ props.answer.content }}
                </p>
                <div>
                    <AssignmentImagesViewer :images="props.answer.images" />
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <!-- <button @click.prevent="handleLikesanswer" :disabled="refanswer.isDislikedByAuth"
                        class="flex items-center space-x-1  px-2 py-1 rounded-md text-green-500 ">
                        <div class="flex justify-around items-center space-x-2 hover:scale-125">
                            <span v-if="refanswer.isLikedByAuth" >
                                <Icon icon="icon-park-solid:like" class="w-5 h-5 " />
                            </span>
                            <span v-else>
                                <Icon icon="icon-park-outline:like" class="w-5 h-5"  />
                            </span>
                        </div>
                        <span>{{ refanswer.likes }}</span>
                    </button> -->
                    <!-- <button @click.prevent="handleDislikesanswer" :disabled="refanswer.isLikedByAuth"
                        class="flex space-x-1 px-2 py-1 rounded-md text-red-500">
                        <Icon :icon="`heroicons-${ refanswer.isDislikedByAuth ? 'solid':'outline'}:thumb-down`" class="w-5 h-5 hover:scale-125" />
                        <span>{{ refanswer.dislikes }}</span>
                    </button> -->
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
