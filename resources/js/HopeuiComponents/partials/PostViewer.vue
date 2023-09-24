
<script setup>
import { ref } from 'vue';
import PostContentViewer from './PostViewerPartials/PostContentViewer.vue';
import PostReactionViewer from './PostViewerPartials/PostReactionViewer.vue';

const props = defineProps({
    activity: Object,
});

const waiting = ref(false);

const emit = defineEmits(['delete-activity']);

const isPostCreation = ref(props.activity.action == "เขียนโพสต์");

async function handleDeletePost(postId) {
    try {
        waiting.value = true;
        const activityId = props.activity.id;
        const deleteActResp = await axios.delete('/activities/'+ props.activity.id);
        if (deleteActResp.data.success) {
            emit('delete-activity', activityId);
        }
        waiting.value = false;
    } catch (error) {
        console.log(error);
    }
}

</script>

<template>
    <div class="my-4">
        <div class="bg-white shadow-lg rounded-lg  dark:bg-dark-card py-1" v-if="activity">
            <div v-if="!isPostCreation" class="">
                <div  class="px-4 pt-4 flex justify-between">
                    <div>
                        <div class="flex justify-center gap-4">
                            <img :src="activity.action_by.avatar" class="w-12 h-12 p-[3px] rounded-full ring-1 ring-blue-600 dark:ring-gray-500" alt="">
                            <div>
                                <div class="flex items-center gap-4 mb-2">
                                    <h6 class="">
                                        <a :href="'/users' + activity.action_by.id + '/feed'">
                                            {{ activity.action_by.name }}
                                        </a>
                                    </h6>
                                    <small class="text-gray-800 mt-1 dark:text-secondary-600">
                                        {{ activity.action }}
                                    </small>
                                    <small class="text-gray-800 mt-1 dark:text-secondary-600">
                                        {{ activity.action_to }}
                                    </small>
                                </div>
                                <!-- <p class="text-gray-500 text-xs">{{ activity.created_at }}</p> -->
                                <p class="text-gray-500 text-xs">{{ activity.diff_humans_created_at }}</p>
                            </div>
                        </div>
                    </div>
                    <div x-data="dropdown" class="inline-block">
                        <a class="inline-block px-4 py-1 text-center text-gray-400  cursor-pointer" type="button" @click="toggle">
                            <svg width="18" class="inline-block w-5 h-5"  aria-expanded="false"
                                            role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentcolor">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M10 20.6788C10 21.9595 11.0378 23 12.3113 23C13.5868 23 14.6265 21.9595 14.6265 20.6788C14.6265 19.3981 13.5868 18.3576 12.3113 18.3576C11.0378 18.3576 10 19.3981 10 20.6788ZM10 12.0005C10 13.2812 11.0378 14.3217 12.3113 14.3217C13.5868 14.3217 14.6265 13.2812 14.6265 12.0005C14.6265 10.7198 13.5868 9.67929 12.3113 9.67929C11.0378 9.67929 10 10.7198 10 12.0005ZM12.3113 5.64239C11.0378 5.64239 10 4.60192 10 3.3212C10 2.04047 11.0378 1 12.3113 1C13.5868 1 14.6265 2.04047 14.6265 3.3212C14.6265 4.60192 13.5868 5.64239 12.3113 5.64239Z"
                                                fill="#8A92A6" />
                                        </svg>
                        </a>
                        <ul x-show.transition="open"  x-bind="dropdownTransition" class="z-10 py-2  absolute text-left text-secondary-500 bg-white  rounded min-w-36 dark:bg-dark-bg "   style="display: none;">
                            <li><a class=" block text-left pr-[1.5rem] pl-[1rem] py-1 ml-[0.5rem] mr-[0.5rem] rtl:text-right rtl:pl-[1.5rem] rtl:pr-4 whitespace-nowrap hover:text-white hover:bg-primary-500 hover:rounded" href="#">
                            <svg class="icon-18" width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M19.9927 18.9534H14.2984C13.7429 18.9534 13.291 19.4124 13.291 19.9767C13.291 20.5422 13.7429 21.0001 14.2984 21.0001H19.9927C20.5483 21.0001 21.0001 20.5422 21.0001 19.9767C21.0001 19.4124 20.5483 18.9534 19.9927 18.9534Z" fill="currentColor"></path>
                                    <path d="M10.309 6.90385L15.7049 11.2639C15.835 11.3682 15.8573 11.5596 15.7557 11.6929L9.35874 20.0282C8.95662 20.5431 8.36402 20.8344 7.72908 20.8452L4.23696 20.8882C4.05071 20.8903 3.88775 20.7613 3.84542 20.5764L3.05175 17.1258C2.91419 16.4915 3.05175 15.8358 3.45388 15.3306L9.88256 6.95545C9.98627 6.82108 10.1778 6.79743 10.309 6.90385Z" fill="currentColor"></path>
                                    <path opacity="0.4" d="M18.1208 8.66544L17.0806 9.96401C16.9758 10.0962 16.7874 10.1177 16.6573 10.0124C15.3927 8.98901 12.1545 6.36285 11.2561 5.63509C11.1249 5.52759 11.1069 5.33625 11.2127 5.20295L12.2159 3.95706C13.126 2.78534 14.7133 2.67784 15.9938 3.69906L17.4647 4.87078C18.0679 5.34377 18.47 5.96726 18.6076 6.62299C18.7663 7.3443 18.597 8.0527 18.1208 8.66544Z" fill="currentColor"></path>
                                </svg>
                            <span class="ml-2">Edit Post</span>
                            </a></li>
                            <li><a class="block text-left pr-[1.5rem] pl-[1rem] py-1 ml-[0.5rem] mr-[0.5rem] rtl:text-right rtl:pl-[1.5rem] rtl:pr-4 whitespace-nowrap hover:text-white hover:bg-primary-500 hover:rounded" href="#">
                                <svg class="icon-18" width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.7688 8.71387H16.2312C18.5886 8.71387 20.5 10.5831 20.5 12.8885V17.8254C20.5 20.1308 18.5886 22 16.2312 22H7.7688C5.41136 22 3.5 20.1308 3.5 17.8254V12.8885C3.5 10.5831 5.41136 8.71387 7.7688 8.71387ZM11.9949 17.3295C12.4928 17.3295 12.8891 16.9419 12.8891 16.455V14.2489C12.8891 13.772 12.4928 13.3844 11.9949 13.3844C11.5072 13.3844 11.1109 13.772 11.1109 14.2489V16.455C11.1109 16.9419 11.5072 17.3295 11.9949 17.3295Z" fill="currentColor"></path>
                                    <path opacity="0.4" d="M17.523 7.39595V8.86667C17.1673 8.7673 16.7913 8.71761 16.4052 8.71761H15.7447V7.39595C15.7447 5.37868 14.0681 3.73903 12.0053 3.73903C9.94257 3.73903 8.26594 5.36874 8.25578 7.37608V8.71761H7.60545C7.20916 8.71761 6.83319 8.7673 6.47754 8.87661V7.39595C6.4877 4.41476 8.95692 2 11.985 2C15.0537 2 17.523 4.41476 17.523 7.39595Z" fill="currentColor"></path>
                                </svg>
                                <span class="ml-2">Security</span>
                            </a></li>
                            <li><a class="block text-left pr-[1.5rem] pl-[1rem] py-1 ml-[0.5rem] mr-[0.5rem] rtl:text-right rtl:pl-[1.5rem] rtl:pr-4 whitespace-nowrap hover:text-white hover:bg-primary-500 hover:rounded" href="#">
                                <svg class="icon-18" width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M22 12C22 17.524 17.523 22 12 22C6.477 22 2 17.524 2 12C2 6.478 6.477 2 12 2C17.523 2 22 6.478 22 12Z" fill="currentColor"></path>
                                    <path d="M15.5739 15.8145C15.4429 15.8145 15.3109 15.7805 15.1899 15.7095L11.2639 13.3675C11.0379 13.2315 10.8989 12.9865 10.8989 12.7225V7.67554C10.8989 7.26154 11.2349 6.92554 11.6489 6.92554C12.0629 6.92554 12.3989 7.26154 12.3989 7.67554V12.2965L15.9589 14.4195C16.3139 14.6325 16.4309 15.0925 16.2189 15.4485C16.0779 15.6835 15.8289 15.8145 15.5739 15.8145Z" fill="currentColor"></path>
                                </svg>
                                <span class="ml-2">Timer</span>
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div :class="!isPostCreation ? 'm-4 border-[1.5px] border-gray-200 rounded-lg':''" class="p-4">

                <div class="text-gray-700 text-base rounded-lg relative" >
                    <PostContentViewer
                        :post="activity.target_resource"
                        :action_to_id="activity.action_to_id"
                        :actorId="activity.action_by.id"
                        :acting="activity.action"
                        @delete-post="(postId) => handleDeletePost(postId)"
                    />
                    <div v-if="waiting" class="absolute -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                        <svg aria-hidden="true" class="w-8 h-8 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/></svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>

                <!-- <div class="grid grid-cols-2 gap-3">
                    <a href="#" class="col-span-2">
                        <img src="storage/assets/images/newsfeed/03.png" class="rounded-lg w-full" alt="">
                    </a>
                    <a href="#">
                        <img src="storage/assets/images/newsfeed/08.png" class="rounded-lg w-full" alt="">
                    </a>
                    <a href="#">
                        <img src="storage/assets/images/newsfeed/09.png" class="rounded-lg w-full" alt="">
                    </a>
                    <a href="#">
                        <img src="storage/assets/images/newsfeed/10.png" class="rounded-lg w-full" alt="">
                    </a>
                    <a href="#">
                        <img src="storage/assets/images/newsfeed/11.png" class="rounded-lg w-full" alt="">
                    </a>

                    <img src="/storage/assets/images/app/01.png" class="rounded-lg" alt="">
                </div> -->
                <div>
                    <PostReactionViewer
                        :post="activity.target_resource"
                        :model="activity.action_to.toLowerCase()"
                    />
                </div>

            </div>
        </div>
    </div>
</template>

