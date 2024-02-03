<script setup>
import QuestionItem from '@/PlearndComponents/learn/courses/questions/QuestionItem.vue';

const props = defineProps({
    questionableType: String,
    questionableId: Number,
    questionNameTh: String,
    questionApiRoute: String,
    questions: Object
});

const model = defineModel();

const emit = defineEmits([
    'delete-question',
    'update:questions'
]);

async function deleteQuestion(qID,qIdx){
    emit('delete-question',qID,qIdx);
}

</script>

<template>
    <div class="w-full ">
        <div class="tabs flex flex-col justify-center pt-4 w-full">
            <div class="tabs-header px-2 w-full flex flex-col items-center justify-center">
                <div class="text-base font-normal" v-if="!props.questions.length">
                    (ไม่มีคำถาม)
                </div>
                <div v-else-if="model && !$page.props.isCourseAdmin" class="w-full ">
                    <div v-for="(question,idx) in props.questions" :key="question.id" class="w-full border rounded-md p-1 my-2 ">
                        <QuestionItem
                            :question="question"
                            :indexNumber="idx"
                            :isCourseOwner="$page.props.isCourseAdmin"
                            @delete-question="deleteQuestion(question.id, idx)"
                        />
                    </div>
                </div>
                <div v-if="$page.props.isCourseAdmin" class="w-full ">
                    <div v-for="(question,idx) in props.questions" :key="question.id" class="w-full border rounded-md p-1 my-2 ">
                        <QuestionItem
                            :question="question"
                            :indexNumber="idx"
                            :isCourseOwner="$page.props.isCourseAdmin"
                            @delete-question="deleteQuestion(question.id, idx)"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
