<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
// import AssignmentAnswerItem from './AssignmentAnswerItem.vue';
import DotsDropdownMenu from '@/PlearndComponents/accessories/DotsDropdownMenu.vue';
import AssignmentImagesViewer from '../AssignmentImagesViewer.vue';
import PointsManager from '@/PlearndComponents/learn/courses/assignments/answers/PointsManager.vue';

import Swal from 'sweetalert2';
import { Icon } from '@iconify/vue';

const props =  defineProps({
    assignment: Object,
    answers: Object,
});


async function onSetPoints(answerId, points,i){
    try {
        const updateResp = await axios.post(`/assignments/${props.assignment.id}/answers/${answerId}/set-points`, 
                            {
                                points      :   points,
                                course_id   :   usePage().props.course.data.id,   
                            });
        if (updateResp.data.success) {
            props.answers[i].points = points;
            Swal.fire('สำเร็จ!','บันทึกคะแนนเรียบร้อยแล้ว.','success' );
        }
    } catch (error) {
        console.log(error);
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
            await axios.delete(`/assignments/${props.assignment.id}/answers/${answerId}`, 
                {
                    data: {
                        course_id   :   usePage().props.course.data.id,   
                    }
                }
            );
            props.answers.splice(index, 1);
            Swal.fire('ลบเสร็จสิ้น!','ลบคำตอบเรียบร้อยแล้ว.','success' )
        }
    });
};

</script>
<template>
    <div>
        <!-- <div>
            <ul class="flex flex-wrap  items-center " role="tablist">
                <li v-for="(group, index) in $page.props.groups.data" :key="index" class="w-1/3" role="presentation ">
                    <button @click.prevent="setActiveGroupTab(index)"
                        class="inline-flex items-center justify-center w-full h-12 gap-2 px-6 mb-2 text-sm tracking-wide transition duration-300 border-b-2 rounded-t focus-visible:outline-none whitespace-nowrap hover:border-violet-600 focus:border-violet-700 text-violet-500 hover:text-violet-600 focus:text-violet-700 hover:bg-violet-50 focus:bg-violet-50 disabled:cursor-not-allowed disabled:border-slate-500 stroke-violet-500 hover:stroke-violet-600 focus:stroke-violet-700"
                        :class="activeGroupTab === index ? 'border-violet-500 bg-violet-200 font-bold':'font-medium'"
                        id="tab-label-1a" role="tab" aria-setsize="3" aria-posinset="1" tabindex="0"
                        aria-controls="tab-panel-1a" aria-selected="true">
                        <span>{{ group.name + ' (' + group.members.length +')' }}</span>
                    </button>
                </li>
                <li>
                    <button @click.prevent="setActiveGroupTab($page.props.groups.data.length)"
                        class="inline-flex items-center justify-center w-full h-12 gap-2 px-6 mb-2 text-sm tracking-wide transition duration-300 border-b-2 rounded-t focus-visible:outline-none whitespace-nowrap hover:border-violet-600 focus:border-violet-700 text-violet-500 hover:text-violet-600 focus:text-violet-700 hover:bg-violet-50 focus:bg-violet-50 disabled:cursor-not-allowed disabled:border-slate-500 stroke-violet-500 hover:stroke-violet-600 focus:stroke-violet-700"
                        :class="activeGroupTab === $page.props.groups.data.length ? 'border-violet-500 bg-violet-200 font-bold':'font-medium'"
                        id="tab-label-1a" role="tab" aria-setsize="3" aria-posinset="1" tabindex="0"
                        aria-controls="tab-panel-1a" aria-selected="true">
                        <span>{{ 'ไม่มีกลุ่ม' }}</span>
                    </button>
                </li>
            </ul>
        </div> -->
        <div v-for="(answer,i) in props.answers" :key="answer.id" 
            class="m-2"
        >
            <div v-if="answer.student.id === $page.props.auth.user.id || $page.props.isCourseAdmin"
                class="flex gap-4 w-full justify-start p-2 rounded-lg"
                :class="answer.points >= (assignment.points/2) ? 'bg-green-100': answer.points ? 'bg-red-200': 'bg-gray-200'"
            >
                <img :src="answer.student.avatar"
                    class="hidden sm:block w-9 h-9 p-[2px] rounded-full ring-1 ring-blue-600 dark:ring-gray-500" alt="">
                <div class="w-full">
                    <div class="flex justify-between">
                        <div class="flex items-end space-x-2">
                            <p class="mb-0">{{ answer.student.name }}</p>
                            <span class="text-gray-600 text-sm">{{ answer.created_at }}</span>
                        </div>
                        <div v-if="$page.props.isCourseAdmin || (answer.student.id === $page.props.auth.user.id)">
                            <DotsDropdownMenu 
                                @delete-model="onDeleteAsmAnswer(answer.id, i)" 
                            >
                                <template #deleteModel><div>ลบคำตอบ</div></template>
                            </DotsDropdownMenu>
                        </div>
                    </div>
                    <p v-if="answer.content" class="text-gray-700 text-sm bg-slate-50 border-[1.5px] border-gray-200 p-2 ml-auto rounded-lg my-2">
                        {{ answer.content }}
                    </p>
                    <div>
                        <AssignmentImagesViewer :images="answer.images" />
                    </div>
                    <div v-if="$page.props.isCourseAdmin">
                        <PointsManager 
                            :assignment="props.assignment"
                            :answer="answer" 
                            @setPoints="(points)=>onSetPoints(answer.id, points, i)" 
                        />
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


