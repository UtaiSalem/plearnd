<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';

import InfiniteLoading from "v3-infinite-loading";
import CommentForm from '@/PlearndComponents/learn/courses/posts/comments/CommentForm.vue';
import CoursePostCommentItem from '@/PlearndComponents/learn/courses/posts/comments/CoursePostCommentItem.vue';
import PostLoadingSkeleton from '@/PlearndComponents/accessories/PostLoadingSkeleton.vue';


const props = defineProps({
    comments_count: Number,
    comments: Object,
    postId: Number,
    post: Object,
    isPostOwner: Boolean
});
const emit = defineEmits(['new-comment-added']);

const initComments = ref(props.comments);
const commentsCount = ref(props.comments_count);
const postComments = ref(null);
const showAllComments = computed(()=> props.comments_count > 3);

const isLoading = ref(false);
const isLoadingCoursePostComments = ref(false);
const showCommentsModal = ref(false);
const currentPage = ref();
const lastPage = ref();
const nextPageUrl = ref();

const handleAddNewComment = (newComment) => {
    initComments.value.unshift(newComment);
    commentsCount.value++;
    emit('new-comment-added');
    // console.log(newComment.content);
}

const showCommentsModalHandler = async () => {
    try {
        showCommentsModal.value = true;
        isLoading.value = true;
        let commentsResponse = await axios.get(`/courses/${props.post.course_id}/posts/${props.postId}/comments`);
        // console.log(commentsResponse.data.post_comments);
        currentPage.value = commentsResponse.data.post_comments.current_page;
        lastPage.value = commentsResponse.data.post_comments.last_page;
        nextPageUrl.value = commentsResponse.data.post_comments.next_page_url;
        postComments.value = commentsResponse.data.comments;

        isLoading.value = false;
    } catch (error) {
        isLoading.value = false;
        showCommentsModal.value = false;
        console.log(error);
    }
}

const clearCommentModal = () => {
    showCommentsModal.value = false;
    postComments.value = null;
};

const handleDeleteComment = async (idx) => {
    initComments.value.splice(idx, 1);
    commentsCount.value--;

};
const handleDeletePostComment = async (idx) => {
    postComments.value.splice(idx, 1);
    commentsCount.value--;

};

const fetchMorePostComments = async () => {
    try {
        // currentPage.value++;
        if (currentPage.value < lastPage.value) {
            isLoadingCoursePostComments.value = true;
            // let commentsResp = await axios.get(`/courses/${props.post.course_id}/posts/${props.postId}/comments?page=${currentPage.value}`);
            let commentsResp = await axios.get(nextPageUrl.value);
            if (commentsResp.data.success) {
                commentsResp.data.comments.forEach(comment => {
                    postComments.value.push(comment);
                });
                // postComments.value = [...postComments.value, ...commentsResp.data.comments];
                // postComments.value = postComments.value.concat(commentsResp.data.comments);

                currentPage.value = commentsResp.data.post_comments.current_page;
                nextPageUrl.value = commentsResp.data.post_comments.next_page_url;

            }
            isLoadingCoursePostComments.value = false;
        }

    } catch (error) {
        console.log(error);
        isLoadingCoursePostComments.value = false;
    }
};

</script>

<template>
    <div>
        <div>
            <CommentForm 
                :postId="postId"
                :post_url="post.post_url"

                @add-new-comment="(newCom) => handleAddNewComment(newCom)"
                class=" mb-4"
            />

            <CoursePostCommentItem v-for="(comment, index) in initComments" :key="comment.id" :comment="comment" :isPostOwner @delete-post-comment="handleDeleteComment(index)" />

            <div v-if="showAllComments" class="flex flex-col w-full">
                <!-- <AcademyPostCommentItem :comment="comment" v-for="comment in initComments.slice(-3)" :key="comment.id" /> -->
                <button @click.prevent="showCommentsModalHandler" type="button" class="ml-auto mr-2 text-sm">
                    ...ดูความคิดเห็นเพิ่มเติม...
                </button>
            </div>
        </div>

        <div v-if="showCommentsModal">
            <div class="flex fixed top-0 left-0 right-0 z-[50] p-2 w-full h-full bg-gray-800 bg-opacity-80 justify-center items-start py-16">
                <!-- <div v-if="isLoading" class="flex w-full max-w-xl  bg-white text-violet-600 rounded-lg justify-center items-center  py-10">
                    <Icon icon="eos-icons:bubble-loading" class="w-10 h-10 mx-5" />Loading...
                </div> -->
                <div class="w-full max-w-xl bg-white rounded-lg mx-2">
                    <div class="relative shadow dark:bg-gray-700 rounded-lg flex flex-col max-h-[90vh]">
                        <div class="flex justify-between items-center">
                            <button type="button" @click="clearCommentModal" class="absolute top-3 right-2.5 text-red-500  hover:bg-red-300 hover:text-red-600 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                <Icon icon="heroicons:x-mark-20-solid" class="w-6 h-6 hover:scale-125" />
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <div class="mt-14 p-2 overflow-y-auto border-t-2 border-blue-400">
                            <PostLoadingSkeleton v-if="isLoading" />
                            <div class=" ">
                                <ul>
                                    <li v-for="(postComment, index) in postComments" :key="postComment.id" class="">
                                        <CoursePostCommentItem :comment="postComment" :isPostOwner @delete-post-comment="handleDeletePostComment(index)" />
                                    </li>
                                </ul>
                                <PostLoadingSkeleton v-if="isLoadingCoursePostComments" />
                            </div>
                            <InfiniteLoading @distance="1" @infinite="fetchMorePostComments" /> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


