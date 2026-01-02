<script setup>
import { ref, onMounted } from 'vue';
import { useCourseStore } from '@/stores/course';
import { storeToRefs } from 'pinia';
import InfiniteLoading from "v3-infinite-loading";

import CreateCoursePost from "@/PlearndComponents/learn/courses/posts/CreateCoursePost.vue";
import CoursePostViewer from '@/PlearndComponents/learn/courses/posts/CoursePostViewer.vue';
import PostLoadingSkeleton from '@/PlearndComponents/accessories/PostLoadingSkeleton.vue';

const props = defineProps({
    course: Object,
});

const store = useCourseStore();
const { feeds, feedsPagination, isLoading } = storeToRefs(store);
const isLoadingMore = ref(false);

async function fetchMorePosts() {
    if (isLoadingMore.value) return;
    
    if (feedsPagination.value.currentPage < feedsPagination.value.lastPage) {
        isLoadingMore.value = true;
        await store.fetchFeeds(props.course.id, feedsPagination.value.currentPage + 1);
        isLoadingMore.value = false;
    }
}

const handleAddNewActivity = (newActivity) => {
    feeds.value = [newActivity, ...feeds.value];
}

const handleDeletePost = (index) => {
    feeds.value.splice(index, 1);
}

</script>

<template>
    <div>
        <!-- Create Post Section -->
        <CreateCoursePost 
            v-if="store.isCourseAdmin || (store.course && store.course.is_member)" 
            :course_id="course?.id" 
            @add-new-post="handleAddNewActivity"
        />

        <!-- Posts List -->
        <div v-if="feeds && feeds.length > 0">
            <div v-for="(activity, index) in feeds" :key="activity.id">
                <CoursePostViewer 
                    :postKey="`post-${activity.id}`"
                    :postIndex="index"
                    :activity="activity"
                    @delete-post="handleDeletePost(index)"
                />
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading && (!feeds || feeds.length === 0)" class="mt-4">
            <PostLoadingSkeleton />
        </div>

        <!-- Infinite Loading -->
        <InfiniteLoading 
            v-if="feeds && feeds.length > 0" 
            @distance="1" 
            @infinite="fetchMorePosts" 
        /> 
        
        <!-- Empty State -->
        <div v-if="!isLoading && (!feeds || feeds.length === 0)" class="text-center py-8 text-gray-500">
            ยังไม่มีโพสต์ในรายวิชานี้
        </div>
    </div>
</template>
