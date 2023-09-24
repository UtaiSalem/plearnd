<script setup>
    import { ref, onMounted } from 'vue'
    import { initFlowbite } from 'flowbite'

    // import CreatePostVue from '@/HopeuiComponents/widgets/CreatePost.vue';
    // import ActivityCardVue from '@/HopeuiComponents/widgets/ActivityCard.vue';
    // import ExploreCardVue from '@/HopeuiComponents/widgets/ExploreCard.vue';
    // import StoriesCardVue from '@/HopeuiComponents/widgets/StoriesCard.vue';
    import MeassageCardVue from '@/HopeuiComponents/widgets/MessageCard.vue';
    import SuggestedCardVue from '@/HopeuiComponents/widgets/SuggestedCard.vue';
    // import CreateNewAcademyCardWidget from '@/Components/widgets/CreateNewSchoolWidget.vue'
    // import AINabarVue from '@/ChatGPTComponents/AINabar.vue';
    // import AIPostBox from '@/ChatGPTComponents/AIPostBox.vue'
    // import QuickPostBox from '@/PlearnComponents/QuickPostBox.vue'

    // import ModalShowAllComment from '@/HopeuiComponents/partials/PostViewerPartials/ModalShowAllComment.vue';

    const isDarkMode = ref(false);
    onMounted(() => {
        initFlowbite();
    })


</script>
<template>
    <div class=" bg-slate-200 h-full">
        <!-- Page Heading -->
        <div v-if="$slots.header">
            <slot name="header" />
        </div>
        <div v-if="$slots.navbar">
            <slot name="navbar"></slot>
        </div>

        <div class="flex flex-col justify-between mx-4 rounded-lg">
            <div v-if="$slots.coverProfileCard" class="" :class="{'mt-[76px]': $slots.navbar}">
                <slot name="coverProfileCard"></slot>
            </div>

            <div class="flex justify-center md:justify-between my-4">

                <!-- Sidebar start-->
                <div class="hidden md:block w-[23%]">
                    <div v-if="$slots.leftSideWidget">
                        <slot name="leftSideWidget" />
                    </div>
                </div>
                <!-- Sidebar end -->

                <!-- content -->
                <div class="w-full md:w-[50%]">
                    <main>

                        <slot name="mainContent"></slot>
                    </main>
                </div>
                <!-- End content -->

                <!-- right sidebar -->
                <div class="hidden md:block w-[23%]">
                    <div v-if="$slots.rightSideWidget">
                        <slot name="rightSideWidget" />
                    </div>
                </div>
                <!--End right sidebar -->
            </div>
        </div>

    </div>
</template>

