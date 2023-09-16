 <script setup>
import { Head } from "@inertiajs/vue3";
// import DefaultLayout from "@/Layouts/DefaultLayout.vue";
// import ProfileCover from "@/Components/partials/ProfileCover.vue";
// import QuickPostBox from '@/Components/widgets/QuickPostBox.vue';
// import ActivitiesFeed from '@/Components/widgets/ActivitiesFeed.vue';
// import CreateNewSchoolWidget from '@/Components/widgets/CreateNewSchoolWidget.vue';
</script>
<template>
    <Head title="User Profile" />
    <DefaultLayout>
        <template #header>
            <!-- <ProfileCover 
                :coverImage="$page.props.userProfile.data.profile.cover_image"
                :profileImage="$page.props.userProfile.data.avatar" 
                :coverName="$page.props.userProfile.data.name"
            /> -->
        </template>

        <template #leftSideWidget>
            <div>
                <!-- <CreateNewSchoolWidget /> -->
            </div>
        </template>

        <!-- <QuickPostBox /> -->

        <!-- <ActivitiesFeed /> -->

        <template #rightSideWidget>
            <div>
                <!-- <CreateNewSchoolWidget /> -->
            </div>
        </template>

    </DefaultLayout>
</template>