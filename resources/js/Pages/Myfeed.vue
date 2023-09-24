<script setup>
import { ref } from 'vue';
import { Head,usePage } from '@inertiajs/vue3';
import InfiniteLoading from "v3-infinite-loading";
import Swal from 'sweetalert2';

import MainLayout from '@/Layouts/MainLayout.vue';
import Navbar from '@/PlearndComponents/Navbar.vue';
import PostViewer from '@/HopeuiComponents/partials/PostViewer.vue';
import PostLoading from '@/PlearndComponents/accessories/PostLoadingSkeleton.vue';
// import QuickPostBox from '@/PlearndComponents/QuickPostBox.vue';
import CreatePost from '@/HopeuiComponents/widgets/CreatePost.vue';

const props = defineProps({
    user: Object,
    activities: Object
});

// console.log(props.activities.data);

const loading = ref(false);
const currentPage = ref(props.activities.meta.current_page||1);
const lastPage = ref(props.activities.meta.last_page||1);


const getMoreActivities = async () => {
    try {
        loading.value = true;

        if (currentPage.value < lastPage.value) {
            currentPage.value++;
            const actResp = await axios.get('/users/'+ props.user.id + '/activities?page=' + currentPage.value);
            if (actResp.data.success) {
                actResp.data.activities.forEach(activity => {
                    props.activities.data.push(activity)
                });
            }
        }

        loading.value = false;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
};

function handleDeleteActivity(actId) {
    props.activities.data.splice(actId, 1);
    usePage().props.auth.user.pp--;
    Swal.fire(
        'สำเร็จ',
        'ลบโพสต์เสร็จสมบูรณ์',
        'success'
    );
}

</script>
<template>
    <div class="">
        <MainLayout>
            <template #header>
                <div>
                    <Head title="My feed" />
                </div>
            </template>
            <template #navbar>
                <div>
                    <Navbar></Navbar>
                </div>
            </template>

            <template #mainContent>
                <div class="mt-[75px]">

                    <!-- <CreatePost /> -->

                    <div v-for="(activity,index) in props.activities.data" :key="index">
                        <div v-if="activity.action_to === 'Post'">
                            <PostViewer
                                :activity="activity"
                                @delete-activity="()=> handleDeleteActivity(index)"
                            />
                        </div>
                        <div v-else-if="activity.action_to === 'Poll'">
                            <!-- <PollViewer :activity="activity" /> -->
                        </div>
                    </div>
                    <div v-if="loading">
                        <PostLoading class="my-4" v-for="(item,index) in 2" :key="index" />
                    </div>

                    <InfiniteLoading @infinite="getMoreActivities()" />
                </div>
            </template>

        </MainLayout>
    </div>
</template>

