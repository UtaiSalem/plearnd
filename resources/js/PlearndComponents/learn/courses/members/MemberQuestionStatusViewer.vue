<script setup>
// const props = defineProps({
//     quizzes: Object,
// });
const getBGStatus = (quiz) => {
    // compute member_achieved_score compare with total_score and return bg color
    if (quiz.member_achieved_score >= (quiz.total_score/2)) {
        return 'success';
    } else if (quiz.member_achieved_score > 0) {
        return 'warning';
    } else {
        return 'danger';
    }
};
</script>
<template>
    <div class="card">
        <DataTable :value="$page.props.quizzes.data" tableStyle="min-width: 100%">
            <Column field="title" header="หัวข้อ" class="w-5/6"></Column>
            <Column header="คะแนน" class="w-1/6">
                <template #body="slotProps">
                    <Tag :value="slotProps.data.member_achieved_score + ' / ' + slotProps.data.total_score" :severity="getBGStatus(slotProps.data)" />
                </template>
            </Column>
        </DataTable>
    </div>
</template>

