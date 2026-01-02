<script setup>
import { ref, onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import MemberProgressItem from './MemberProgressItem.vue';
import { saveAs } from 'file-saver';
import * as XLSX from 'xlsx';

const props = defineProps({
    groupName: String,
    members: Array,
    isCourseAdmin: Boolean,
});

const page = usePage();

//member-progress-table const
const membersProgressTable = ref(null);
const sheetName = ref(props.groupName);
const sanitizedSheetName = ref(sheetName.value.replace(/[:\\\/?*\[\]]/g, ''));

const gradeProgress = (score) => {
    if (score >= 80) return { grade: 4 };
    else if (score >= 75) return { grade: 3.5 };
    else if (score >= 70) return { grade: 3 };
    else if (score >= 65) return { grade: 2.5 };
    else if (score >= 60) return { grade: 2 };
    else if (score >= 55) return { grade: 1.5 };
    else if (score >= 50) return { grade: 1 };
    else return { grade: 0 };
};

const exportTableToExcel = () => {
    // สร้าง header
    const headers = [
        'เลขที่',
        'รหัส',
        'ชื่อ - สกุล'
    ];
    
    // เพิ่ม header ภาระงาน
    page.props.assignments?.data?.forEach((assignment, index) => {
        headers.push(`@${index + 1}(${assignment.points})`);
    });
    
    // เพิ่ม header แบบทดสอบ
    page.props.quizzes?.data?.forEach((quiz, index) => {
        headers.push(`#${index + 1}(${quiz.total_score})`);
    });
    
    headers.push(
        `คะแนนเก็บ (${page.props.course?.data?.total_score})`,
        'คะแนนพิเศษ',
        'คะแนน (ได้/เต็ม)',
        'คะแนนรวม',
        'เกรดที่ทำได้',
        'เกรดที่แก้ได้',
        'หมายเหตุ'
    );
    
    // สร้าง data rows
    const sortedMembers = [...props.members]
        .sort((a, b) => (a.order_number || 9999) - (b.order_number || 9999));
    
    const rows = sortedMembers.map(member => {
        const row = [
            member.order_number || '',
            member.member_code || '',
            member.member_name || member.user?.name || ''
        ];
        
        // เพิ่มคะแนนภาระงาน
        page.props.assignments?.data?.forEach((assignment) => {
            const answers = assignment.answers?.filter(answer => answer.student?.id === member.user?.id) || [];
            const answer = answers[0];
            row.push(answer?.points ?? '');
        });
        
        // เพิ่มคะแนนแบบทดสอบ - ใช้ course_members_score แทน answers
        page.props.quizzes?.data?.forEach((quiz) => {
            const memberScore = quiz.course_members_score?.find(score => score.user_id === member.user?.id);
            row.push(memberScore?.score ?? '');
        });
        
        const totalMemberScore = (member.achieved_score || 0) + (member.bonus_points || 0);
        const gradePercentage = member.achieved_score 
            ? Math.round(((member.achieved_score + (member.bonus_points || 0)) / page.props.course.data.total_score) * 100)
            : 0;
        
        // ตรวจสอบว่าคำตอบของภาระงานและแบบทดสอบครบถ้วนหรือไม่
        // ตรงตาม logic ใน MemberAssignmentAnswerProgress และ MemberQuizzesAnswerProgress
        let isAllAnswersCompleted = true;
        
        // ตรวจสอบภาระงาน - ตรงกับ MemberAssignmentAnswerProgress.vue
        // เช็คว่ามีคำตอบหรือไม่ (!props.answers.length)
        if (page.props.assignments?.data && page.props.assignments.data.length > 0) {
            for (const assignment of page.props.assignments.data) {
                const answers = assignment.answers?.filter(answer => answer.student?.id === member.user?.id) || [];
                // ถ้าไม่มีคำตอบเลย (answers.length === 0) ถือว่าไม่สมบูรณ์
                if (answers.length === 0) {
                    isAllAnswersCompleted = false;
                    break;
                }
            }
        }
        
        // ตรวจสอบแบบทดสอบ - ตรงกับ MemberQuizzesAnswerProgress.vue
        // เช็คว่ามีคะแนนหรือไม่ (!member_score.value) โดยใช้ course_members_score
        if (isAllAnswersCompleted && page.props.quizzes?.data && page.props.quizzes.data.length > 0) {
            for (const quiz of page.props.quizzes.data) {
                // ใช้ course_members_score แทน answers เหมือนใน MemberQuizzesAnswerProgress
                const memberScore = quiz.course_members_score?.find(score => score.user_id === member.user?.id);
                // ถ้าไม่มีคะแนนเลย (!memberScore) ถือว่าไม่สมบูรณ์
                if (!memberScore) {
                    isAllAnswersCompleted = false;
                    break;
                }
            }
        }
        
        // ตรวจสอบว่าข้อมูลครบถ้วนหรือไม่ (ตาม logic ใน MemberProgressItem.vue)
        // gradeStatus = true หมายความว่าข้อมูลครบถ้วน สามารถคำนวณเกรดได้
        const hasNotesComments = member.notes_comments && member.notes_comments.length;
        const gradeStatus = !(
            !member.order_number || 
            !member.achieved_score || 
            !member.member_code ||
            !isAllAnswersCompleted ||
            member.grade_progress === 5 ||
            hasNotesComments
        );
        
        // ถ้า gradeStatus เป็น false ให้แสดง "ร" ในคอลัมน์ "เกรดที่ทำได้"
        const calculatedGrade = gradeStatus ? gradeProgress(gradePercentage).grade : 'ร';
        
        // สำหรับคอลัมน์ "เกรดที่แก้ได้" ให้แสดง "ร" ถ้า grade_progress === 5
        const editableGrade = member.grade_progress === 5 ? 'ร' : (member.grade_progress || '');
        
        // สำหรับคอลัมน์ "คะแนนรวม" ให้แสดง "ร" ถ้า gradeStatus เป็น false
        const displayGradePercentage = gradeStatus ? gradePercentage : 'ร';
        
        row.push(
            member.achieved_score || '',
            member.bonus_points || 0,
            `${totalMemberScore}/${page.props.course.data.total_score}`,
            displayGradePercentage,
            calculatedGrade,
            editableGrade,
            (member.notes_comments && member.notes_comments.length > 0) 
                ? member.notes_comments.map(nc => nc.note_comment).join('; ') 
                : ''
        );
        
        return row;
    });
    
    // สร้าง worksheet จากข้อมูล
    const data = [headers, ...rows];
    const worksheet = XLSX.utils.aoa_to_sheet(data);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, sanitizedSheetName.value);
    const excelBuffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
    const blob = new Blob([excelBuffer], { type: 'application/octet-stream' });
    saveAs(blob, Date.now()+'_' + sanitizedSheetName.value + '.xlsx');
};
</script>

<template>
    <div>
        <div class="relative overflow-auto rounded-lg shadow-md max-h-[80vh] border border-gray-200">
            <table ref="membersProgressTable" class="w-full text-sm text-left rtl:text-right text-gray-700 bg-white">
                <thead class="text-xs font-bold text-gray-800 bg-gray-50 sticky top-0 z-20 shadow-sm">
                    <tr class="text-center">
                        <th scope="col" class="w-16 px-2 py-3 border border-slate-300 bg-gray-100 sticky left-0 z-30 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                            เลขที่
                        </th>
                        <th scope="col" class="w-20 px-2 py-3 border border-slate-300 bg-gray-100 sticky left-16 z-20">
                            รหัส
                        </th>
                        <th scope="col" class="min-w-[200px] px-2 py-3 border border-slate-300 bg-gray-100 z-10">
                            ชื่อ - สกุล
                        </th>
                        <th scope="col" class="w-16 min-w-[4rem] px-2 py-3 border border-slate-300 bg-purple-50"
                            v-for="(assignment, index) in page.props.assignments?.data" :key="assignment.id">
                            {{ '@' + (index + 1) + '(' + assignment.points +')'  }}
                        </th>
                        <th scope="col" class="w-16 min-w-[4rem] px-2 py-3 border border-slate-300 bg-cyan-50"
                            v-for="(quiz, index) in page.props.quizzes?.data" :key="quiz.id">
                            {{ '#' + (index + 1) + '(' + quiz.total_score +')' }}
                        </th>
                        <th scope="col" class="min-w-[100px] px-2 py-3 border border-slate-300 bg-yellow-50">
                            {{ 'คะแนนเก็บ (' + page.props.course?.data?.total_score +')' }}
                        </th>
                        <th scope="col" class="min-w-[80px] px-2 py-3 border border-slate-300 bg-green-50">
                            คะแนนพิเศษ
                        </th>
                        <th scope="col" class="min-w-[100px] px-2 py-3 border border-slate-300 bg-indigo-50">
                            คะแนน (ได้/เต็ม)
                        </th>
                        <th scope="col" class="min-w-[80px] px-2 py-3 border border-slate-300 bg-pink-50">
                            คะแนนรวม
                        </th>
                        <th scope="col" class="min-w-[80px] px-2 py-3 border border-slate-300 bg-emerald-50">
                            เกรดที่ทำได้
                        </th>
                        <th scope="col" class="min-w-[80px] px-2 py-3 border border-slate-300 bg-gray-50">
                            เกรดที่แก้ได้
                        </th>
                        <th scope="col" class="min-w-[150px] px-2 py-3 border border-slate-300 bg-gray-50">
                            หมายเหตุ
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="member in members.sort(function (a, b) { return a.order_number - b.order_number }).filter((member)=> member.order_number !== null)"
                        :key="member.id" class="bg-white hover:bg-gray-50 transition-colors">
                        <MemberProgressItem :member="member" :isCourseAdmin="isCourseAdmin" />
                    </tr>
                    <tr v-for="member in members.sort(function (a, b) { return a.order_number - b.order_number }).filter((member)=> member.order_number === null)"
                        :key="member.id" class="bg-white hover:bg-gray-50 transition-colors">
                        <MemberProgressItem :member="member" :isCourseAdmin="isCourseAdmin" />
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="flex w-full justify-end mt-4">
            <button ref="downloadButton" @click.prevent="exportTableToExcel" class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl shadow-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 font-bold text-sm">
                <Icon icon="file-icons:microsoft-excel" class="w-5 h-5" />
                <span>ดาวน์โหลด Excel</span>
            </button>
        </div>
    </div>
</template>
