<script setup>
import { onMounted,ref } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import InfiniteLoading from "v3-infinite-loading";

import Swal from 'sweetalert2';

import CreatePost from '@/PlearndComponents/widgets/CreatePost.vue';
import NewsFeedPostsViewer from '@/PlearndComponents/play/posts/NewsFeedPostsViewer.vue';
import AcademyPostViewer from '@/PlearndComponents/learn/academies/posts/AcademyPostViewer.vue';
import CoursePostViewer from '@/PlearndComponents/learn/courses/posts/CoursePostViewer.vue';
import ActivitiesLoadingSkeleton from '@/PlearndComponents/accessories/AcademiesLoadingSkeleton.vue';
import PeopleYouMayKnowWidget from '@/PlearndComponents/widgets/PeopleYouMayKnowWidget.vue';
import PendingFriendsWidget from '@/PlearndComponents/widgets/friends/PendingFriendsWidget.vue';
import DonationListWidget from '@/PlearndComponents/widgets/DonationListWidget.vue';
import DonationsViewer from '@/PlearndComponents/earn/donates/DonationsViewer.vue';
import DonateRecipientViewer from '@/PlearndComponents/earn/donates/DonateRecipientViewer.vue';
import LoadingPage from '@/PlearndComponents/accessories/LoadingPage.vue';

const props = defineProps({
    activities: Object,
    peopleMayKnow: Object,
    pendingFriends: Object,
    donates: Object,    
});

const isLoadingActivities = ref(false);
const currentPage = ref(1);
const lastPage = ref(props.activities.meta.last_page);
const newsfeedActivities = ref(props.activities.data);

const isLoading = ref(false);

const getActivities = async () => {
    try {
        isLoadingActivities.value = true;
        currentPage.value++;
        if (currentPage.value <= lastPage.value) {
            const response = await axios.get(`/api/newsfeed/activities?page=${currentPage.value}`);
            if (response.data.success) {
                response.data.activities.forEach(activity => {
                    newsfeedActivities.value.push(activity);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด! กรุณาลองใหม่อีกครั้ง',
                    text: response.data.message,
                });
            }
        }
        isLoadingActivities.value = false;
    } catch (error) {
        console.log(error);
        isLoadingActivities.value = false;
    }
};

const handleAddNewDonateActivity = (activity) => {
    newsfeedActivities.value.unshift(activity);
};

</script>

<template>
    <div class="">
        <MainLayout title="Newsfeed">
            <template #coverProfileCard>
                <div class="flex items-center mx-auto mt-2 mb-4 shadow-lg bg-[url('/storage/images/banner/banner-bg.png')] bg-cover bg-no-repeat rounded-lg">
                    <img class="section-banner-icon " :src="'/storage/images/banner/forums-icon.png'" alt="forums-icon">
                    <p class="text-white font-bold text-4xl">กระดานข่าว</p>
                </div>
            </template>

            <template #leftSideWidget>
                <!-- Donation List -->
                <DonationListWidget v-if="donates.data.length"
                    :donates
                    @add-new-donate-activity="(newActivity)=> newsfeedActivities.unshift(newActivity)"
                />
            </template>

            <template #rightSideWidget >
                <!-- User who is maybe friend -->
                <PeopleYouMayKnowWidget :peopleMayKnow />
                <div class="my-4"></div>
                <PendingFriendsWidget :pendingFriends v-if="props.pendingFriends.data.length"/>
            </template>

            <template #mainContent>
                <div>
                
                    <LoadingPage v-if="isLoading" />

                    <CreatePost @add-new-post-activity="(newActivity)=> newsfeedActivities.unshift(newActivity)"/>

                    <div>
                        <div v-for="(activity,index) in newsfeedActivities" :key="index" >
                            
                            <div v-if="activity.action_to === 'Post'">
                                <NewsFeedPostsViewer 
                                    :activity 
                                    @delete-post="newsfeedActivities.splice(index,1)" 
                                />
                            </div>

                            <div v-else-if="activity.action_to === 'AcademyPost'">
                                <AcademyPostViewer :activity @delete-post="newsfeedActivities.splice(index,1)" />
                            </div>

                            <div v-else-if="activity.action_to === 'CoursePost'">
                                <CoursePostViewer :activity="activity" @delete-post="newsfeedActivities.splice(index, 1)" />
                            </div>

                            <div v-else-if="activity.action_to === 'Donate'">
                                <DonationsViewer :activity="activity" />
                            </div>

                            <div v-else-if="activity.action_to === 'DonateRecipient'">
                                <DonateRecipientViewer :activity="activity" />
                            </div>

                            <div v-else-if="activity.action_to === 'Poll'">
                                'Poll Viewer'
                            </div>
                        </div>

                        <div v-if="isLoadingActivities" class="mt-4">
                            <ActivitiesLoadingSkeleton />
                        </div>

                    </div>
                    <InfiniteLoading @distance="1" @infinite="getActivities" v-if="activities.meta.total>0" />
                </div>
            </template>
        </MainLayout>
    </div>
</template>
