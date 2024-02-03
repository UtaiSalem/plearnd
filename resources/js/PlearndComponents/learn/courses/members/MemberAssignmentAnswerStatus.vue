<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    member_info: Object,
    assignment: Object,
    answers: Object,
});

const isLoadingMemberAssignmentsAnswer = ref(false);

const memberAnswers = computed(()=>{
    return props.answers.filter((answer)=> answer.student.id === props.member_info.user.id);
});

//computed member_achieved_score and compare with total_score and return bg color


</script>

<template>
    <div>
        <div v-if="memberAnswers.length">
            <div v-for="(answer) in memberAnswers" :key="answer.id">
                <div v-if="answer.points" 
                    class=" text-white py-1 px-2 max-w-fit rounded-md text-xs font-semibold"
                    :class="answer.points >= assignment.points/2 ? 'bg-green-400' : answer.points > 0 ? 'bg-orange-400' : 'bg-red-500'"
                >
                {{ answer.points + ' / ' + assignment.points }}
                </div>
                <div v-else class="bg-slate-500 text-white py-1 px-2 rounded-md text-xs flex flex-col justify-center items-center">
                    <span>{{ 'รอตรวจ' }}</span>
                    <span>/{{ assignment.points }}</span>
                </div>
            </div>
        </div>
        <div v-else>
            <span class="bg-red-500 text-white py-1 px-2 rounded-full text-xs">
                ยังไม่ส่งงาน
            </span>
        </div>
    </div>
</template>

<style lang="scss" scoped>

</style>