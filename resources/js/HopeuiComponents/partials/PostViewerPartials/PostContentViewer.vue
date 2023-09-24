<script setup>
import DotsDropdownMenu from "@/PlearndComponents/accessories/DotsDropdownMenu.vue";
import { computed } from "vue";

const props = defineProps({
    post: Object,
    action_to_id: String,
    actorId: Number,
    acting: String
});

const emit = defineEmits(['delete-post']);

const showPostHeader = computed( () => props.acting !== 'เขียนโพสต์' && props.actorId !== props.post.author.id );

</script>

<template>
    <div>
        <div class="flex  gap-4 relative">
            <div class="absolute -top-2 -right-2" v-if="post.author.id === $page.props.auth.user.id">
                <DotsDropdownMenu
                    @delete-model="emit('delete-post', props.post.id);"
                >
                    <template #delete-description>
                        <div>
                            ลบโพสต์
                        </div>
                    </template>
                </DotsDropdownMenu>
            </div>
            <img :src="post.author.avatar" class="w-12 h-12 p-[3px] rounded-full ring-1 ring-blue-600 dark:ring-gray-500" alt="">
            <div>
                <div class="flex items-center gap-4 mb-2">
                    <a :href="'/users/' + post.author.id + '/feed'">
                        {{ post.author.name }}
                    </a>
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

