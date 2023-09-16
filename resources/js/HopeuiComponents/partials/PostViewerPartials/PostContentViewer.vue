<script setup>
import { computed } from "vue";

const props = defineProps({
    post: Object,
    action_to_id: String,
    actorId: Number,
    acting: String
});

const showPostHeader = computed( () => props.acting !== 'เขียนโพสต์' && props.actorId !== props.post.author.id );

</script>

<style lang="scss" scoped>

</style>

<template>
    <div>
        <div class="flex  gap-4">
            <img :src="post.author.avatar" class="w-12 h-12 p-[3px] rounded-full ring-1 ring-blue-600 dark:ring-gray-500" alt="">
            <div>
                <div class="flex items-center gap-4 mb-2">
                    <h6 class="">{{ post.author.name }}</h6>
                    <small class="text-gray-800 mt-1 dark:text-secondary-600">
                        {{ 'เขียนโพสต์' }}
                    </small>
                    <small class="text-gray-800 mt-1 dark:text-secondary-600">
                        เมื่อ
                    </small>
                    <small class="text-gray-800 mt-1 dark:text-secondary-600">
                        {{ post.diff_humans_created_at }}
                    </small>
                    <!-- <p class="text-gray-500 text-xs">{{ post.diff_humans_created_at }}</p> -->
                </div>
            </div>
        </div>
        <div class="bg-gray-100 mt-2 p-2 rounded-lg">
            {{ post.content }}
        </div>
    </div>
</template>

