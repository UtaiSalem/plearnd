<script setup>
import { ref } from 'vue';
import { Icon } from '@iconify/vue';

const props = defineProps({
    activity: Object,
});

const refActivity = ref(props.activity);
</script>

<template>
    <div class="my-4">
        <div class="bg-white shadow-lg rounded-lg dark:bg-dark-card p-4 relative" v-if="activity">
            <div class="flex flex-wrap w-full gap-4">
                <div class="flex items-center gap-2 min-w-fit">
                    <div class="flex-shrink-0">
                        <img class="w-12 h-12 p-[3px] rounded-full ring-1 ring-blue-600 dark:ring-gray-500"
                            :src="refActivity.target_resource.advertiser ? refActivity.target_resource.advertiser.avatar : '/storage/plearnd-logo.png'"
                            :alt="refActivity.target_resource.advertiser ? refActivity.target_resource.advertiser.name+'photo':'advertiser-photo'">
                    </div>
                    <h6 class="">{{ refActivity.target_resource.advertiser ? refActivity.target_resource.advertiser.name : 'Unknown Advertiser' }}</h6>
                    <div class="flex justify-between">
                        <div class="flex flex-wrap items-center gap-1">
                            <small class="text-gray-800 dark:text-secondary-600">
                                สนับสนุน
                            </small>
                            <small class="text-gray-800 dark:text-secondary-600">
                                เมื่อ
                            </small>
                            <small class="text-gray-800 dark:text-secondary-600">
                                {{ refActivity.diff_humans_created_at }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-2">
                <div class="bg-green-100 rounded-lg flex items-center justify-center text-base font-bold tracking-tight">
                    <div class="flex items-center justify-center space-x-1">
                        <div class="p-2">
                            <span class="text-[28px] text-yellow-400">
                                {{ refActivity.target_resource.amounts }}
                            </span>
                            <span class="text-[12px] text-green-500">
                                <span class="text-green-500">/เหลือ</span>
                                {{ refActivity.target_resource.remaining_points }}
                                <span class="text-green-500">แต้ม</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>