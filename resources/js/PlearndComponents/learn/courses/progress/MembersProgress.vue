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
                        v-for="(assignment, index) in $page.props.assignments.data" :key="assignment.id">
                        {{ '@' + (index + 1) + '(' + assignment.points +')'  }}
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300"
                        v-for="(quiz, index) in $page.props.quizzes.data" :key="quiz.id">
                        {{ '#' + (index + 1) + '(' + quiz.total_score +')' }}
                    </th>
                    <th scope="col" class="px-2 py-3 border border-slate-300">
                        {{ 'คะแนนเก็บ (' + $page.props.course.data.total_score +')' }}
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
            <button ref="downloadButton" @click.prevent="exportTableToExcel" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                ดาวน์โหลด Excel
            </button>
        </div>
    </div>
</template>
