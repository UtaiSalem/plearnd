<script setup>
// import { ref, computed, onMounted } from 'vue';
import MemberAssignmentsAnswerStatus from '@/PlearndComponents/learn/courses/members/MemberAssignmentAnswerStatus.vue';

const props = defineProps({
    member_info: Object,
});

</script>

<template>
<div class="shadow-lg rounded-lg overflow-hidden">
    <table class="w-full table-fixed">
        <thead>
            <tr class="bg-gray-100">
                <th class="w-1/12 py-2 px-4 text-left text-gray-600 font-bold uppercase">ที่</th>
                <th class="w-full py-2 px-4 text-left text-gray-600 font-bold uppercase">หัวข้อ</th>
                <th class="w-8/12 xs:w-4/12 py-2 px-4 text-left text-gray-600 font-bold uppercase">คะแนน</th>
            </tr>
        </thead>
        <tbody class="bg-white">
            <tr v-for="(assignment, index) in $page.props.assignments.data" :key="assignment.id">
                <td class="py-4 px-4 border-b border-gray-200">{{ index+1 }}</td>
                <td class="py-4 px-4 border-b border-gray-200 text-sm">{{ assignment.title }}</td>
                <td class="py-4 px-4 border-b border-gray-200">
                    <!-- <span class="bg-green-500 text-white py-0.5 px-2 rounded-full text-xs"> -->
                        <MemberAssignmentsAnswerStatus
                            :member_info="props.member_info"
                            :assignment="assignment"  
                            :answers="assignment.answers" 
                        />
                    <!-- </span> -->
                </td>
            </tr>
            <!-- Add more rows here -->
        </tbody>
    </table>
</div>
</template>