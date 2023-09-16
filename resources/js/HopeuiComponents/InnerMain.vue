<template>
<div>
  Inner main
  <slot></slot>
</div>
</template>

<script setup>
import { ref,onMounted } from 'vue'
import { initFlowbite } from 'flowbite'
// import Navbar from '@/PlearnComponents/Navbar.vue'
// import CreatePostVue from '@/HopeuiComponents/widgets/CreatePost.vue';
// import ActivityCardVue from '@/HopeuiComponents/widgets/ActivityCard.vue';
// import ExploreCardVue from '@/HopeuiComponents/widgets/ExploreCard.vue';
// import StoriesCardVue from '@/HopeuiComponents/widgets/StoriesCard.vue';
// import MeassageCardVue from '@/HopeuiComponents/widgets/MessageCard.vue';
// import SuggestedCardVue from '@/HopeuiComponents/widgets/SuggestedCard.vue';
// import CreateNewAcademyCardWidget from '@/Components/widgets/CreateNewSchoolWidget.vue'
// import AINabarVue from '@/ChatGPTComponents/AINabar.vue';
// import AIPostBox from '@/ChatGPTComponents/AIPostBox.vue'
// import QuickPostBox from '@/PlearnComponents/QuickPostBox.vue'

// import ModalShowAllComment from '@/HopeuiComponents/partials/PostViewerPartials/ModalShowAllComment.vue';

onMounted(() => {
    initFlowbite();
})

const isDarkMode = ref(false);

</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Kanit:wght@400;500&display=swap');

*{
    font-family: 'Rajdhani', 'Kanit', sans-serif;
}
</style>