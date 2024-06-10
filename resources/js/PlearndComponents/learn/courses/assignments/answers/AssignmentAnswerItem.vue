<script setup>

import AssignmentImagesViewer from '../AssignmentImagesViewer.vue';
import DotsDropdownMenu from '@/PlearndComponents/accessories/DotsDropdownMenu.vue';
import PointsManager from '@/PlearndComponents/learn/courses/assignments/answers/PointsManager.vue';

const props = defineProps({
    assignment: Object,
    answer: Object,
    index: Number
});
const emit = defineEmits(['delete-answer','update-answer']);

async function onSetPoints(points){
    try {
        emit('update-answer', points);
    } catch (error) {
        console.log(error);
    }
}
</script>
<template>
    <div v-if="answer.student.id === $page.props.auth.user.id || $page.props.isCourseAdmin"
        class="flex gap-4 w-full justify-start p-2 rounded-lg"
        :class="answer.points >= (assignment.points/2) ? 'bg-green-100': answer.points ? 'bg-red-200': 'bg-gray-200'">
        <img :src="answer.student.avatar"
            class="hidden sm:block w-9 h-9 p-[2px] rounded-full ring-1 ring-blue-600 dark:ring-gray-500" alt="">
        <div class="w-full">
            <div class="flex justify-between">
                <div class="flex items-end space-x-2">
                    <p class="mb-0">{{ answer.student.name }}</p>
                    <span class="text-gray-600 text-sm">{{ answer.created_at }}</span>
                </div>
                <div v-if="$page.props.isCourseAdmin || (answer.student.id === $page.props.auth.user.id)">
                    <DotsDropdownMenu @delete-model="emit('delete-answer')">
                        <template #deleteModel>
                            <div>ลบคำตอบ</div>
                        </template>
                    </DotsDropdownMenu>
                </div>
            </div>
            <p v-if="answer.content"
                class="text-gray-700 text-sm bg-slate-50 border-[1.5px] border-gray-200 p-2 ml-auto rounded-lg my-2">
                {{ answer.content }}
            </p>
            <div>
                <AssignmentImagesViewer :images="answer.images" />
            </div>
            <div v-if="$page.props.isCourseAdmin">
                <PointsManager 
                    :assignment="props.assignment" 
                    :answer="props.answer"

                    @setPoints="(points)=>onSetPoints(points)" 
                />
            </div>
        </div>
    </div>
</template>
