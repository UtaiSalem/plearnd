<script setup>
/**
 * GenericListSkeleton - Reusable loading skeleton
 * 
 * Usage:
 * <GenericListSkeleton />                      - Default 5 rows with avatar
 * <GenericListSkeleton :rows="3" />            - 3 rows
 * <GenericListSkeleton :showAvatar="false" />  - No avatar
 * <GenericListSkeleton :showThumbnail="true" />- With thumbnail (card style)
 */
const props = defineProps({
    rows: { type: Number, default: 5 },
    showAvatar: { type: Boolean, default: true },
    showThumbnail: { type: Boolean, default: false },
});
</script>

<template>
    <div class="space-y-3">
        <div v-for="i in rows" :key="i" class="bg-white rounded-lg shadow p-4 animate-pulse">
            <!-- Card with Thumbnail -->
            <div v-if="showThumbnail" class="flex gap-4">
                <div class="w-24 h-24 bg-gray-200 rounded-lg flex-shrink-0"></div>
                <div class="flex-1 space-y-3">
                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-3 bg-gray-200 rounded w-full"></div>
                    <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                </div>
            </div>
            
            <!-- List Item -->
            <div v-else class="flex items-center gap-4">
                <div v-if="showAvatar" class="w-12 h-12 bg-gray-200 rounded-full flex-shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                </div>
            </div>
        </div>
    </div>
</template>
