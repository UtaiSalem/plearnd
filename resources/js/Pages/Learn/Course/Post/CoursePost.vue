<script setup>
import { ref, reactive, onMounted } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import SingleCoursePostViewer from '@/PlearndComponents/learn/courses/posts/SingleCoursePostViewer.vue';
import PostSkeleton from '@/Components/PostSkeleton.vue';

const props = defineProps({
    'activity': Object,
});

const isLoadingPost = ref(true);

const getPost = async () => {
    const response = await axios.get(`/posts/${props.post_id}/getCoursePostActivity`);

    activity.value = response.data.activity;
    isLoadingPost.value = false;
};

</script>

<template>
    <div>
        <MainLayout  title="Post/โพสต์">
            <template #mainContent>
                <div v-if="isLoadingPost && !activity">
                    <PostSkeleton />
                </div>
                <div v-else>
                    <SingleCoursePostViewer :activity="props.activity.data" />
                </div>
            </template>
        </MainLayout>
    </div>
</template>
