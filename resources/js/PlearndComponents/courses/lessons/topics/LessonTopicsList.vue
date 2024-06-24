<script setup>
import { reactive, ref } from "vue";
import { router,Link } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';
import TextInput from '@/Components/TextInput.vue'
import TopicListImagesViewer from './TopicListImagesViewer.vue'
import DotsDropdownMenu from "@/PlearndComponents/accessories/DotsDropdownMenu.vue";
import Swal from 'sweetalert2'

const props = defineProps({
    topics: Object,
});

const form = reactive({
    action: "Created post",
    body: "New post on " + new Date().toString(),
});

function handleSubmit() {
    router.post("/newsfeed", form);
}

function handleDeleteTopic(index,id){
    Swal.fire({
            title: 'แน่ใจหรือไม่?',
            text: "ที่จะลบหัวข้อนี้อย่างถาวร!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'ยกเลิก',
            confirmButtonText: 'ใช่, ยืนยัน!'
            }).then(async (result) => {
            if (result.isConfirmed) {
                const delResp = await axios.delete(`/topics/${id}`);
                console.log(delResp.data);
                props.topics.splice(index,1);
                Swal.fire('ลบเสร็จสมบูรณ์!','ลบหัวข้อบทเรียนแล้ว.','success' )
            }
        })
}
</script>
<template>
    <div class="" >
        <div class="bg-white border-t-4 border-blue-600 rounded-lg overflow-hidden shadow-lg my-4">
            <p class="m-4 text-xl font-semibold">
                หัวข้อของบทเรียน <span v-if="props.topics.length<1" class="text-sm font-thin"> (ยังไม่มีหัวข้อของบทเรียน)</span>
            </p>
        </div>
        <div class="rounded-lg">
            <div v-for="(topic,index) in props.topics" :key="topic.id"  class="tabs flex flex-col justify-center mt-4 rounded-lg bg-white shadow-lg text-gray-700 overflow-hidden">
                <div class="relative">
                    <div class="absolute top-1 right-4">
                        <!-- <Icon icon="heroicons-solid:dots-horizontal" /> -->
                        <DotsDropdownMenu :index="index" @delete-model="(idx)=> handleDeleteTopic(idx, topic.id)"/>
                    </div>
                    <div :id="`bar-with-underline-1${topic.id}`" role="tabpanel" aria-labelledby="bar-with-underline-item-1">
                        <div class="m-4">
                            <form class="bg-white rounded-lg" action="" method="post">
                                <label for="topic_title" class="block text-lg font-medium text-gray-900 dark:text-white">หัวข้อที่ {{ index + 1 }}</label>
                                <TextInput id="topic_title" v-model="topic.title" type="text" class="mt-0 mb-2 block w-full text-start" required autofocus autocomplete="topic_title" />

                                <label for="topic_content" class="block text-lg font-medium text-gray-900 dark:text-white">เนื้อหา</label>
                                <textarea id="topic_content" v-model="topic.content" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder=""></textarea>

                                <div class="mt-4">

                                </div>
                            </form>
                            <div v-if="topic.images.length>0">
                                <TopicListImagesViewer 
                                    :topic_id="topic.id" 
                                    :edit="false" 
                                    :images="topic.images" 
                                />
                            </div>
                            <div v-if="topic.assignments">
                                Topic Assignment {{ topic.assignments.length }}
                            </div>
                            <div class="w-full grid justify-end">
                                <Link :href="`/topics/${topic.id}`" class="underline">ดูรายละเอียด</Link>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
  
  