<script setup>
import { usePage } from "@inertiajs/vue3";

import MemberProgressItem from './MemberProgressItem.vue';
defineProps({
    members: Array,
});


</script>

<template>
    <div class="relative overflow-x-auto rounded-lg">
        <table class="w-screen text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                <tr class="text-center">
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        เลขที่
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        รหัส
                    </th>
                    <th scope="col" class="pl-2 py-3 border border-slate-300 w-40">
                        ชื่อ - สกุล
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300"
                        v-for="(assignment, index) in $page.props.assignments.data" :key="assignment.id">
                        {{ '#' + (index + 1) }}
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300"
                        v-for="(quiz, index) in $page.props.quizzes.data" :key="quiz.id">
                        {{ '@' + (index + 1) }}
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        คะแนนสอบ
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        คะแนนพิเศษ
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        คะแนนรวม
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        เกรด
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        สถานะ
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        หมายเหตุ
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="member in members.sort(function (a, b) { return a.order_number - b.order_number }).filter((member)=> member.order_number !== null)"
                    :key="member.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <MemberProgressItem :member="member" />
                </tr>
                <tr v-for="member in members.sort(function (a, b) { return a.order_number - b.order_number }).filter((member)=> member.order_number === null)"
                    :key="member.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <MemberProgressItem :member="member" />
                </tr>
            </tbody>
        </table>
    </div>
</template>
