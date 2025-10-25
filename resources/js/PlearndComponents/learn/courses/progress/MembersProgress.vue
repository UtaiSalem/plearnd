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
    <div class="relative overflow-x-auto rounded-lg">
        <table ref="membersProgressTable" class="w-screen text-sm text-left rtl:text-right text-gray-700">
            <thead class="text-xs font-bold text-gray-800">
                <tr class="text-center">
                    <th scope="col" class="w-12 px-1 py-3 border border-slate-300 bg-gradient-to-r from-gray-100 to-gray-200 shadow-sm">
                        เลขที่
                    </th>
                    <th scope="col" class="w-20 px-1 py-3 border border-slate-300 bg-gradient-to-r from-gray-100 to-gray-200 shadow-sm">
                        รหัส
                    </th>
                    <th scope="col" class="pl-2 py-3 border border-slate-300 min-w-48 bg-gradient-to-r from-gray-100 to-gray-200 shadow-sm">
                        ชื่อ - สกุล
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300 bg-gradient-to-r from-purple-100 to-purple-200 shadow-sm"
                        v-for="(assignment, index) in $page.props.assignments.data" :key="assignment.id">
                        {{ '@' + (index + 1) + '(' + assignment.points +')'  }}
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300 bg-gradient-to-r from-cyan-100 to-cyan-200 shadow-sm"
                        v-for="(quiz, index) in $page.props.quizzes.data" :key="quiz.id">
                        {{ '#' + (index + 1) + '(' + quiz.total_score +')' }}
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300 bg-gradient-to-r from-yellow-100 to-yellow-200 shadow-sm">
                        {{ 'คะแนนเก็บ (' + $page.props.course.data.total_score +')' }}
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300 bg-gradient-to-r from-green-100 to-green-200 shadow-sm">
                        คะแนนพิเศษ
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300 bg-gradient-to-r from-indigo-100 to-indigo-200 shadow-sm">
                        คะแนน (ได้/เต็ม)
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300 bg-gradient-to-r from-pink-100 to-pink-200 shadow-sm">
                        คะแนน (%)
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300 bg-gradient-to-r from-emerald-100 to-emerald-200 shadow-sm">
                        เกรดที่ทำได้
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300 bg-gradient-to-r from-gray-100 to-gray-200 shadow-sm">
                        เกรดที่แก้ได้
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300 bg-gradient-to-r from-gray-100 to-gray-200 shadow-sm">
                        หมายเหตุ
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="member in members.sort(function (a, b) { return a.order_number - b.order_number }).filter((member)=> member.order_number !== null)"
                    :key="member.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <MemberProgressItem :member="member" :isCourseAdmin="isCourseAdmin" />
                </tr>
                <tr v-for="member in members.sort(function (a, b) { return a.order_number - b.order_number }).filter((member)=> member.order_number === null)"
                    :key="member.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <MemberProgressItem :member="member" :isCourseAdmin="isCourseAdmin" />
                </tr>
            </tbody>
        </table>
        <div class="flex w-full justify-end my-4"> <!-- เพิ่ม div นี้เพื่อจัดตำแหน่งปุ่มไปทางขวา -->
            <button ref="downloadButton" @click.prevent="exportTableToExcel" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded shadow-md hover:from-blue-600 hover:to-blue-700 transition-all duration-200">
                ดาวน์โหลด Excel
            </button>
        </div>
    </div>
</template>
