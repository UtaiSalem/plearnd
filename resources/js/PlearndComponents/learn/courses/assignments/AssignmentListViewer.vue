<script setup>
import AssignmentItem from '@/PlearndComponents/learn/courses/assignments/AssignmentItem.vue';

const props = defineProps({
    assignmentableType: String,
    assignmentableId: Number,
    assignmentNameTh: String,
    assignmentApiRoute: String,
    assignments: Object,
});

function handleUpdateAssignment(updatedAsm, i) {
    props.assignments[i] = updatedAsm;
}

</script>
<template>
    <div class="w-full">
        <div class="flex items-center justify-center bg-white border-t-4 border-blue-600 rounded-lg overflow-hidden shadow-lg">
            <div class="tabs flex flex-col justify-center p-3 sm:p-4 w-full">
                <div class="tabs-header px-2 sm:px-4 w-full flex flex-col items-center justify-center">
                    <div class="text-base sm:text-xl font-medium text-center">
                        แบบฝึกหัด/ภาระงาน ( {{ props.assignments.length }} ) ข้อ
                    </div>
                    <div class="text-sm sm:text-base font-normal text-gray-500" v-if="props.assignments.length<1">
                        (ไม่มีแบบฝึกหัด/ภาระงาน)
                    </div>
                </div>
            </div>
        </div>
        <div v-for="(asm, i) in props.assignments" :key="i" class="my-2">
            <AssignmentItem 
                :asmIndex='i'
                :assignment="asm"
                :assignmentableType="props.assignmentableType"
                :assignmentableId="props.assignmentableId"
                :assignmentNameTh="props.assignmentNameTh"
                :assignmentApiRoute="props.assignmentApiRoute"

                @update-assignment="(updatedAsm) => handleUpdateAssignment(updatedAsm, i)"
            />
        </div>
    </div>
</template>
