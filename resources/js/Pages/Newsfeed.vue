<template>
    <div class="">
        <Head title="Newsfeed" />

        <div v-for="(activity,index) in $page.props.activities.data" :key="index" >
            <div v-if="activity.action_to === 'Post'">
                <PostViewer :activity="activity" />
            </div>
            <div v-else-if="activity.action_to === 'Poll'">
                <!-- <PollViewer :activity="activity" /> -->
            </div>
        </div>
        
        <!-- <div>{{ $page.props.activity }}</div> -->
    </div>
</template>
<script setup>
import { Head } from '@inertiajs/vue3';
import HopeuiLayout from '@/Layouts/HopeuiLayout.vue';
import PostViewer from '@/HopeuiComponents/partials/PostViewer.vue'
// import PollViewer from '@/HopeuiComponents/partials/PollViewer.vue'

defineOptions({
    layout: HopeuiLayout
})
</script>

