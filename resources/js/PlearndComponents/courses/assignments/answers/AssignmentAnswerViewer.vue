<script setup>
// import AssignmentAnswerItem from './AssignmentAnswerItem.vue';
import DotsDropdownMenu from '@/PlearndComponents/accessories/DotsDropdownMenu.vue';
import AssignmentImagesViewer from '../AssignmentImagesViewer.vue';
import CyanSeaButton from '@/PlearndComponents/accessories/CyanSeaButton.vue';
import Swal from 'sweetalert2';
import { Icon } from '@iconify/vue';

const props =  defineProps({
    assignment: Object,
    answers: Object,
});

async function onSetPoints(answerId, points,i){
    try {
        const updateResp = await axios.patch(`/assignments/${props.assignment.id}/answers/${answerId}`, {points:points});
        if (updateResp.data.success) {
            props.answers[i].points = points;
            Swal.fire('เสร็จสิ้น!','บันทึกคะแนนเรียบร้อยแล้ว.','success' );
        }
    } catch (error) {
        
    }
}

function onDeleteAsmAnswer(answerId,index) {
    Swal.fire({
        title: 'แน่ใจหรือไม่?',
        text: "ที่จะลบคำตอบอย่างถาวร!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'ยกเลิก',
        confirmButtonText: 'ใช่, ยืนยัน!'
        }).then(async (result) => {
        if (result.isConfirmed) {
            await axios.delete(`/assignments/${props.assignment.id}/answers/${answerId}`);
            props.answers.splice(index, 1);
            Swal.fire('ลบเสร็จสิ้น!','ลบคำตอบเรียบร้อยแล้ว.','success' )
        }
    });
    // props.answers.splice(index, 1);
};

</script>
<template>
    <div>
        <div v-for="(answer,i) in answers" :key="answer.id" class="mx-4 my-1" >
            <div class="flex gap-4 w-full justify-start" v-if="answer.student.id === $page.props.auth.user.id || $page.props.isCourseAdmin">
                <img :src="answer.student.avatar"
                    class="w-9 h-9 p-[2px] rounded-full ring-1 ring-blue-600 dark:ring-gray-500" alt="">
                <div class="w-full">
                    <div class="flex justify-between">
                        <div class="flex items-end space-x-2">
                            <p class="mb-0">{{ answer.student.name }}</p>
                            <span class="text-gray-600 text-sm">{{ answer.created_at }}</span>
                        </div>
                        <div v-if="$page.props.isCourseAdmin || (answer.student.id === $page.props.auth.user.id)">
                            <DotsDropdownMenu @delete-model="onDeleteAsmAnswer(answer.id, i)" >
                                <template #delete-description>
                                    <div>
                                        ลบคำตอบ
                                    </div>
                                </template>
                            </DotsDropdownMenu>
                        </div>
                    </div>
                    <p v-if="answer.content" class="text-gray-700 text-sm bg-slate-50 border-[1.5px] border-gray-200 p-2 ml-auto rounded-lg my-2">
                        {{ answer.content }}
                    </p>
                    <div>
                        <AssignmentImagesViewer :images="answer.images" />
                    </div>
                    <div class="flex justify-between items-center space-x-4" v-if="$page.props.isCourseAdmin">
                        <input id="minmax-range" type="range" min="0" :max="assignment.points" v-model="answer.points" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
                        <CyanSeaButton @click.prevent="onSetPoints(answer.id, answer.points,i)"> 
                            <!-- <Icon icon="fa6-solid:ranking-star" class="w-6 h-6" /> -->
                            {{ answer.points }}
                        </CyanSeaButton>
                        <p>คะแนน</p>
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
    </div>
</template>


