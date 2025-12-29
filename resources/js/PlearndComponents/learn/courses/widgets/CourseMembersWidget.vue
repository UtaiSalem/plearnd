<script setup>
import { ref, computed } from 'vue';
import { Icon } from '@iconify/vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    members: Array,
    isCourseAdmin: Boolean,
});

const isExpanded = ref(true);
const isLoading = ref(false);

// Get recent members (limit to 8)
const recentMembers = computed(() => {
    if (!props.members || props.members.length === 0) return [];
    return props.members.slice(0, 8);
});

const totalMembers = computed(() => {
    return props.course?.data?.enrolled_students || props.members?.length || 0;
});

const navigateToMembers = () => {
    isLoading.value = true;
    router.visit(`/courses/${props.course.data.id}/members`);
};

const navigateToMember = (userId) => {
    isLoading.value = true;
    router.visit(`/users/${userId}`);
};

const getAvatarUrl = (member) => {
    return member.user?.avatar || member.avatar || '/images/default-avatar.png';
};

const getMemberName = (member) => {
    if (member.user) {
        return member.user.first_name || member.user.name;
    }
    return member.first_name || member.name || 'สมาชิก';
};
</script>

<template>
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Header -->
        <button 
            @click="isExpanded = !isExpanded"
            class="w-full bg-gradient-to-r from-violet-500 to-purple-600 px-4 py-3 flex items-center justify-between"
        >
            <h3 class="text-white font-semibold flex items-center gap-2">
                <Icon icon="mdi:account-group" class="w-5 h-5" />
                สมาชิก
                <span class="ml-1 px-2 py-0.5 bg-white/20 rounded-full text-xs">
                    {{ totalMembers }}
                </span>
            </h3>
            <Icon 
                :icon="isExpanded ? 'mdi:chevron-up' : 'mdi:chevron-down'" 
                class="w-5 h-5 text-white transition-transform duration-200"
            />
        </button>
        
        <!-- Members Grid -->
        <div v-show="isExpanded" class="p-4">
            <div v-if="recentMembers.length > 0" class="grid grid-cols-4 gap-3">
                <button
                    v-for="member in recentMembers"
                    :key="member.id"
                    @click="navigateToMember(member.user_id || member.id)"
                    class="flex flex-col items-center gap-1 p-2 rounded-lg hover:bg-gray-100 transition-colors"
                    :title="getMemberName(member)"
                >
                    <img 
                        :src="getAvatarUrl(member)" 
                        :alt="getMemberName(member)"
                        class="w-10 h-10 rounded-full object-cover border-2 border-violet-200"
                        @error="$event.target.src = '/images/default-avatar.png'"
                    />
                    <span class="text-xs text-gray-600 truncate w-full text-center">
                        {{ getMemberName(member) }}
                    </span>
                </button>
            </div>
            
            <div v-else class="py-4 text-center text-gray-500">
                <Icon icon="mdi:account-off" class="w-10 h-10 mx-auto mb-2 text-gray-300" />
                <p class="text-sm">ยังไม่มีสมาชิก</p>
            </div>

            <!-- View All Button -->
            <button
                @click="navigateToMembers"
                class="w-full mt-3 px-4 py-2 text-center text-sm font-medium text-violet-600 hover:bg-violet-50 rounded-lg transition-colors border border-violet-200"
            >
                ดูสมาชิกทั้งหมด
                <Icon icon="mdi:arrow-right" class="w-4 h-4 inline ml-1" />
            </button>
        </div>

        <!-- Loading Overlay -->
        <div v-if="isLoading" class="absolute inset-0 bg-white/80 flex items-center justify-center">
            <Icon icon="svg-spinners:bars-rotate-fade" class="w-8 h-8 text-violet-500" />
        </div>
    </div>
</template>
