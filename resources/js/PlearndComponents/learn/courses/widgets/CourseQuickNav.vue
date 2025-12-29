<script setup>
import { ref, computed } from 'vue';
import { Icon } from '@iconify/vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    isCourseAdmin: Boolean,
    courseMemberOfAuth: Object,
    activeTab: Number,
});

const isLoading = ref(false);

const navigateTo = (path) => {
    isLoading.value = true;
    router.visit(`/courses/${props.course.data.id}/${path}`);
};

const menuItems = computed(() => {
    const items = [
        { id: 12, label: 'ข้อมูลรายวิชา', icon: 'heroicons:information-circle', path: 'basic-info', show: true },
        { id: 11, label: 'กระดานข่าว', icon: 'codicon:feedback', path: 'feeds', show: true },
        { id: 1, label: 'บทเรียน', icon: 'icon-park-outline:view-grid-detail', path: 'lessons', show: true },
        { id: 2, label: 'ภาระงาน/แบบฝึกหัด', icon: 'material-symbols:assignment-add-outline', path: 'assignments', show: true },
        { id: 3, label: 'แบบทดสอบ', icon: 'healthicons:i-exam-qualification-outline', path: 'quizzes', show: props.courseMemberOfAuth || props.isCourseAdmin },
        { id: 7, label: 'เข้าเรียน', icon: 'tabler:calendar-user', path: 'attendances', show: props.courseMemberOfAuth || props.isCourseAdmin },
        { id: 5, label: 'กลุ่มเรียน', icon: 'heroicons-outline:user-group', path: 'groups', show: true },
        { id: 4, label: 'สมาชิก', icon: 'ph:users-four', path: 'members', show: props.courseMemberOfAuth !== null },
        { id: 10, label: 'ผลการเรียน', icon: 'mdi:graph-box-plus-outline', path: 'progress', show: props.isCourseAdmin },
        { id: 8, label: 'ตั้งค่ารายวิชา', icon: 'mdi-light:settings', path: 'settings', show: props.isCourseAdmin },
    ];
    return items.filter(item => item.show);
});
</script>

<template>
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden sticky top-20">
        <!-- Header -->
        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 px-4 py-3">
            <h3 class="text-white font-semibold flex items-center gap-2">
                <Icon icon="mdi:navigation-variant" class="w-5 h-5" />
                เมนูลัด
            </h3>
        </div>
        
        <!-- Menu Items -->
        <nav class="p-2">
            <ul class="space-y-1">
                <li v-for="item in menuItems" :key="item.id">
                    <button
                        @click="navigateTo(item.path)"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 text-left"
                        :class="activeTab === item.id 
                            ? 'bg-cyan-50 text-cyan-700 border-l-4 border-cyan-500' 
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                    >
                        <Icon :icon="item.icon" class="w-5 h-5 flex-shrink-0" />
                        <span class="text-sm font-medium">{{ item.label }}</span>
                    </button>
                </li>
            </ul>
        </nav>

        <!-- Loading Overlay -->
        <div v-if="isLoading" class="absolute inset-0 bg-white/80 flex items-center justify-center">
            <Icon icon="svg-spinners:bars-rotate-fade" class="w-8 h-8 text-cyan-500" />
        </div>
    </div>
</template>
