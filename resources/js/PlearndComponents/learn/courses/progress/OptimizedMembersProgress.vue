<script setup>
import { ref, computed, shallowRef } from 'vue';
import { usePage } from "@inertiajs/vue3";
import OptimizedMemberProgressItem from './OptimizedMemberProgressItem.vue';

const props = defineProps({
    groupName: String,
    members: Array,
    isCourseAdmin: Boolean,
    assignments: Array,
    quizzes: Array,
    course: Object,
});

// Optimized member sorting with computed properties
const sortedMembersWithOrder = computed(() => {
    return props.members
        .filter(member => member.order_number !== null)
        .sort((a, b) => a.order_number - b.order_number);
});

const sortedMembersWithoutOrder = computed(() => {
    return props.members
        .filter(member => member.order_number === null)
        .sort((a, b) => (a.order_number || 0) - (b.order_number || 0));
});

// Table reference for export
const membersProgressTable = ref(null);

// Event handler for member updates
const handleMemberUpdated = (memberId, updates) => {
    const memberIndex = props.members.findIndex(m => m.id === memberId);
    if (memberIndex !== -1) {
        // Update the member in the local array
        props.members[memberIndex] = { ...props.members[memberIndex], ...updates };
    }
};
</script>

<template>
    <div class="relative overflow-x-auto rounded-lg">
        <table ref="membersProgressTable" class="w-screen text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                <tr class="text-center">
                    <th scope="col" class="w-12 px-1 py-3 border border-slate-300">
                        เลขที่
                    </th>
                    <th scope="col" class="w-20 px-1 py-3 border border-slate-300">
                        รหัส
                    </th>
                    <th scope="col" class="pl-2 py-3 border border-slate-300 min-w-48">
                        ชื่อ - สกุล
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300"
                        v-for="(assignment, index) in assignments" :key="assignment.id">
                        {{ '@' + (index + 1) + '(' + assignment.points +')'  }}
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300"
                        v-for="(quiz, index) in quizzes" :key="quiz.id">
                        {{ '#' + (index + 1) + '(' + quiz.total_score +')' }}
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        {{ 'คะแนนเก็บ (' + course.total_score +')' }}
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        คะแนนพิเศษ
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        คะแนน (ได้/เต็ม)
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        คะแนน (%)
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        เกรดที่ทำได้
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        เกรดที่แก้ได้
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        หมายเหตุ
                    </th>
                </tr>
            </thead>
            <tbody>
                <!-- Members with order number -->
                <tr v-for="member in sortedMembersWithOrder"
                    :key="member.id" 
                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <OptimizedMemberProgressItem 
                        :member="member" 
                        :isCourseAdmin="isCourseAdmin"
                        :assignments="assignments"
                        :quizzes="quizzes"
                        :course="course"
                        @member-updated="handleMemberUpdated"
                    />
                </tr>
                
                <!-- Members without order number -->
                <tr v-for="member in sortedMembersWithoutOrder"
                    :key="'no-order-' + member.id" 
                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <OptimizedMemberProgressItem 
                        :member="member" 
                        :isCourseAdmin="isCourseAdmin"
                        :assignments="assignments"
                        :quizzes="quizzes"
                        :course="course"
                        @member-updated="handleMemberUpdated"
                    />
                </tr>
            </tbody>
        </table>
    </div>
</template>