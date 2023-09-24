<script setup>
import { computed, ref, reactive, onMounted } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import InfiniteLoading from "v3-infinite-loading";
// import "v3-infinite-loading/lib/style.css";

import HopeuiLayout from '@/Layouts/HopeuiLayout.vue';
import PostViewer from '@/HopeuiComponents/partials/PostViewer.vue'
// import PollViewer from '@/HopeuiComponents/partials/PollViewer.vue'
import PostLoading from '@/PlearndComponents/accessories/PostLoadingSkeleton.vue'

import Swal from 'sweetalert2';

defineOptions({
    layout: HopeuiLayout
})

const props = defineProps({
    activities: Object
});

const loading = ref(false);
const currentPage = ref(props.activities.meta.current_page||1);
const lastPage = ref(props.activities.meta.last_page||1);


const getMoreActivities = async () => {
    try {
        loading.value = true;

        if (currentPage.value < lastPage.value) {
            currentPage.value++;
            const actResp = await axios.get('/activities?page='+ currentPage.value);
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
        <Head title="Newsfeed" />

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
        <InfiniteLoading @distance="1" @infinite="getMoreActivities()" />
        <!-- <div>{{ $page.props.activity }}</div> -->
    </div>
</template>

