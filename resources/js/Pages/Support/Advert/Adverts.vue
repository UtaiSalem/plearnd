<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import Swal from 'sweetalert2';

import MainLayout from '@/Layouts/MainLayout.vue';

import AdvertiseItemCard from '@/PlearndComponents/widgets/advertises/AdvertiseItemCard.vue';

import LoadingPage from '@/PlearndComponents/accessories/LoadingPage.vue';

const props = defineProps({
    donates: Object,
    adverts: Object,
});

const isCreateAdvertPageLoading = ref(false);

const handleLinkToPage = (href) => {
    isCreateAdvertPageLoading.value = true;
    router.visit(href);
};


</script>

<template>
    <div>
        <MainLayout title="ทุนสนับสนุนการเรียนรู้">
            <template #coverProfileCard>
                <div class="hidden md:flex items-center mx-auto mt-2 mb-4 shadow-lg bg-[url('/storage/images/banner/banner-bg.png')] bg-cover bg-no-repeat rounded-lg">
                    <img class="section-banner-icon " :src="'/storage/images/banner/badges-icon.png'" alt="forums-icon">
                    <p class="text-white font-bold text-2xl">ทุนสนับสนุนการเรียนรู้</p>
                </div>
                <div class="">
                    <!-- <MobileNavbar /> -->
                </div>
            </template>

            <template #leftSideWidget>
                <!-- Donation List -->
                <!-- <DonationListWidget v-if="donates.data.length"
                    :donates
                    @add-new-donate-activity=""
                    @go-to-create-donate="handleLinkToPage('/supports/donates/create')"
                /> -->
            </template>

            <template #rightSidebar>
                <!-- <PendingFriendsWidget :pendingFriends="props.pendingFriends" /> -->
            </template>

            <template #mainContent>

                <LoadingPage v-if="isCreateAdvertPageLoading" />

                <div class="mt-2 md:mt-0 mb-4 flex justify-center bg-white p-2 rounded-lg border-t-4 border-blue-500">
                    <button class="flex items-center justify-center max-w-fit px-4 py-2 my-1 font-medium text-center text-white rounded-md bg-teal-400"
                        @click.prevent="handleLinkToPage('/supports/advertises/create')">
                        <Icon icon="flat-color-icons:donate" class="w-7 h-7" />
                        <span>ให้การสนับสนุนทุนการเรียนรู้</span>
                    </button>
                </div>
                <template v-if="donates.data.length">
                    <div class=" grid md:grid-cols-2 gap-4">
                        <div v-for="(advert, index) in props.adverts.data" :key="index"
                            class="border mb-2 rounded-lg hover:shadow-indigo-300 hover:shadow-md bg-white">
                            <AdvertiseItemCard 
                                :advert="advert"
                                :index="index"
                            />
                        </div>
                    </div>
                </template>  
                <template v-else>
                    <div class="flex justify-center items-center h-96">
                        <p class="text-2xl font-bold text-gray-400">No Donations</p>
                    </div>
                </template>
            </template>

        </MainLayout>
    </div>
</template>
