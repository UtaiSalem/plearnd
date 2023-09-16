<template>
  <div class="bg-slate-200 dark:bg-indigo-950 pt-20" :class="{'dark': isDarkMode}">

    <Navbar :isDarkMode="isDarkMode" @toggle-dark-mode="isDarkMode = !isDarkMode" />
    <!-- <AINabarVue /> -->
    <!-- <FlashSidebarLeft class="hidden md:block" /> -->

    <div class="flex flex-col justify-center mx-0 md:space-x-1 md:mx-28 ">


      <div class="flex justify-between">
        <!-- Sidebar start-->
        <div class="hidden lg:block w-[23%] space-y-4">

            <!-- <ExploreCardVue /> -->

            <!-- <CreateNewAcademyCardWidget /> -->

            <CreateNewCourseWidget />
            
            <!-- <ActivityCardVue /> -->
        </div>
        <!-- Sidebar end -->

        <!-- content -->
        <div class="container mx-auto w-full sm:w-[80%] lg:w-[50%] ">
          <main class=" space-y-4">
                <!-- <StoriesCardVue /> -->

                <CreatePostVue />
              <slot></slot>                
          </main>
        </div>
        <!-- End content -->

        <!-- right sidebar -->
        <div class="hidden lg:block w-[23%] space-y-4">
            <!-- <MeassageCardVue /> -->

            <!-- <SuggestedCardVue /> -->
        </div>
        <!--End right sidebar -->
      </div>
    </div>

    <!-- <FlashSidebarRight class="hidden md:block" /> -->
    <!-- <div id="hs-scroll-inside-body-modal" class="hs-overlay hidden w-full h-full fixed top-0 left-0 z-[60] overflow-x-hidden overflow-y-auto">
            <ModalShowAllComment />
    </div> -->
  </div>
</template>

<script setup>
import { ref,onMounted } from 'vue'
import { initFlowbite } from 'flowbite'
import Navbar from '@/PlearndComponents/Navbar.vue'
import CreatePostVue from '@/HopeuiComponents/widgets/CreatePost.vue';
import ActivityCardVue from '@/HopeuiComponents/widgets/ActivityCard.vue';
// import ExploreCardVue from '@/HopeuiComponents/widgets/ExploreCard.vue';
// import StoriesCardVue from '@/HopeuiComponents/widgets/StoriesCard.vue';
// import MeassageCardVue from '@/HopeuiComponents/widgets/MessageCard.vue';
// import SuggestedCardVue from '@/HopeuiComponents/widgets/SuggestedCard.vue';
import CreateNewAcademyCardWidget from '@/PlearndComponents/widgets/CreateNewSchoolWidget.vue';
import CreateNewCourseWidget from '@/PlearndComponents/widgets/CreateNewCourseWidget.vue';
// import AINabarVue from '@/ChatGPTComponents/AINabar.vue';
// import AIPostBox from '@/ChatGPTComponents/AIPostBox.vue'
// import QuickPostBox from '@/PlearnComponents/QuickPostBox.vue'

// import ModalShowAllComment from '@/HopeuiComponents/partials/PostViewerPartials/ModalShowAllComment.vue';

onMounted(() => {
    initFlowbite();
})

const isDarkMode = ref(false);

</script>
