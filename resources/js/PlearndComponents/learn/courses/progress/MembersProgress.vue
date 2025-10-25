<script setup>
import { ref } from 'vue';
import MemberProgressItem from './MemberProgressItem.vue';
import { saveAs } from 'file-saver';
import * as XLSX from 'xlsx';
// import TableExport from 'tableexport';
const props = defineProps({
    groupName: String,
    members: Array,
    isCourseAdmin: Boolean,
});

//member-progress-table const
const membersProgressTable = ref(null);
const sheetName = ref(props.groupName); // Example sheet name
const sanitizedSheetName = ref(sheetName.value.replace(/[:\\\/?*\[\]]/g, '')); // Remove invalid characters

const exportTableToExcel = () => {
    const table = membersProgressTable.value; // อ้างอิงไปที่ตาราง
    // const rows = table.querySelectorAll('tr'); // Get all rows
    //   // Define the columns to include (e.g., "ชื่อ - สกุล", "คะแนน (ได้/เต็ม)")
    // const columnsToInclude = ['เลขที่', 'รหัส', 'ชื่อ - สกุล', 'คะแนน (ได้/เต็ม)'];

    const worksheet = XLSX.utils.table_to_sheet(table); // แปลงตาราง HTML เป็น worksheet
    const workbook = XLSX.utils.book_new(); // สร้าง workbook ใหม่
    XLSX.utils.book_append_sheet(workbook, worksheet, sanitizedSheetName.value); // เพิ่ม worksheet ลงใน workbook
    const excelBuffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' }); // เขียน workbook เป็น array buffer
    const blob = new Blob([excelBuffer], { type: 'application/octet-stream' }); // สร้าง Blob จาก buffer
    saveAs(blob, Date.now() + '.xlsx'); // บันทึกไฟล์ Excel
};

</script>


<template>
    <div class="relative rounded-xl shadow-lg bg-gradient-to-br from-gray-50 via-blue-50 to-green-50 p-4" style="max-width: 100vw; overflow-x: auto;">
    <table ref="membersProgressTable" class="min-w-[1200px] text-sm text-center text-gray-700 dark:text-gray-300 rounded-lg overflow-hidden" style="width: max-content;">
            <thead class="text-xs font-semibold uppercase bg-gradient-to-r from-blue-200 via-green-100 to-yellow-100 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="w-12 px-2 py-3 border border-slate-300 rounded-tl-lg">
                        เลขที่
                    </th>
                    <th scope="col" class="w-20 px-2 py-3 border border-slate-300">
                        รหัส
                    </th>
                    <th scope="col" class="pl-2 py-3 border border-slate-300 min-w-[260px] max-w-[320px] text-left">
                        ชื่อ - สกุล
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300"
                        v-for="(assignment, index) in $page.props.assignments.data" :key="assignment.id">
                        <span class="bg-blue-100 text-blue-800 rounded px-2 py-1 shadow-sm">@{{ index + 1 }} ({{ assignment.points }})</span>
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300"
                        v-for="(quiz, index) in $page.props.quizzes.data" :key="quiz.id">
                        <span class="bg-purple-100 text-purple-800 rounded px-2 py-1 shadow-sm">#{{ index + 1 }} ({{ quiz.total_score }})</span>
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        <span class="bg-green-100 text-green-800 rounded px-2 py-1 shadow-sm">คะแนนเก็บ ({{ $page.props.course.data.total_score }})</span>
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        <span class="bg-yellow-100 text-yellow-800 rounded px-2 py-1 shadow-sm">คะแนนพิเศษ</span>
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        <span class="bg-green-200 text-green-900 rounded px-2 py-1 shadow-sm">คะแนน (ได้/เต็ม)</span>
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        <span class="bg-blue-200 text-blue-900 rounded px-2 py-1 shadow-sm">คะแนน (%)</span>
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        <span class="bg-indigo-100 text-indigo-800 rounded px-2 py-1 shadow-sm">เกรดที่ทำได้</span>
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        <span class="bg-pink-100 text-pink-800 rounded px-2 py-1 shadow-sm">เกรดที่แก้ได้</span>
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300 rounded-tr-lg">
                        <span class="bg-gray-200 text-gray-800 rounded px-2 py-1 shadow-sm">หมายเหตุ</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="member in members.sort((a, b) => a.order_number - b.order_number).filter(member => member.order_number !== null)"
                    :key="member.id"
                    class="bg-white hover:bg-blue-50 transition-colors border-b dark:bg-gray-800 dark:border-gray-700">
                    <MemberProgressItem :member="member" :isCourseAdmin="isCourseAdmin" />
                </tr>
                <tr v-for="member in members.sort((a, b) => a.order_number - b.order_number).filter(member => member.order_number === null)"
                    :key="member.id"
                    class="bg-gray-50 hover:bg-yellow-50 transition-colors border-b dark:bg-gray-800 dark:border-gray-700">
                    <MemberProgressItem :member="member" :isCourseAdmin="isCourseAdmin" />
                </tr>
            </tbody>
        </table>
        <div class="flex w-full justify-end my-4">
            <button ref="downloadButton" @click.prevent="exportTableToExcel" class="px-4 py-2 bg-gradient-to-r from-blue-500 via-green-400 to-blue-600 text-white rounded-lg shadow hover:scale-105 hover:bg-blue-700 transition-all duration-200">
                ดาวน์โหลด Excel
            </button>
        </div>
    </div>
</template>
