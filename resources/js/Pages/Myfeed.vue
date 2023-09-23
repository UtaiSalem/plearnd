<script setup>
import { ref, reactive, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import InfiniteLoading from "v3-infinite-loading";
// import "v3-infinite-loading/lib/style.css";

import MainLayout from '@/Layouts/DefaultLayout.vue';
import PostViewer from '@/HopeuiComponents/partials/PostViewer.vue'
// import PollViewer from '@/HopeuiComponents/partials/PollViewer.vue'
import PostLoading from '@/PlearndComponents/accessories/PostLoadingSkeleton.vue'
import axios from 'axios';

defineOptions({
    layout: MainLayout
})

const props = defineProps({
    myActivities: Object
});

const newActivities = reactive([]);
const loading = ref(false);
const page = ref(1);

const getMoreActivities = async () => {
    try {
        loading.value = true;
        const actResp = await axios.get('/activities?page='+ page.value++);
        if (actResp.data.success) {
            actResp.data.activities.forEach(activity => {
                props.activities.data.push(activity)
            });
        }
        loading.value = false;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }    
};

</script>
<template>
    <div class="">
        <Head title="Newsfeed" />

        <div v-if="loading">
            <PostLoading class="my-4" v-for="(item,index) in 2" :key="index" />
        </div>
        <InfiniteLoading @distance="2" @infinite="getMoreActivities()" />          
        <!-- <div>{{ $page.props.activity }}</div> -->
    </div>
</template>

