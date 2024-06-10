<script setup>
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import AssignmentAnswerItem from './AssignmentAnswerItem.vue';

const props =  defineProps({
    assignment: Object,
    answers: Object,
});

async function onSetPoints(points,i){
    try {
        props.answers[i].points = points;
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
        <div v-for="(answer,i) in props.answers" :key="answer.id" class="m-2" >
            <AssignmentAnswerItem 
                :assignment="props.assignment" 
                :answer="answer" 
                :index="i"

                @update-answer="(points)=>onSetPoints(points, i)"

                @delete-answer="onDeleteAsmAnswer(answer.id, i)"
            />
        </div>
    </div>
</template>


