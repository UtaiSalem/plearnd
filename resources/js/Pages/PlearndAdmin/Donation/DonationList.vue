<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import DonateCard from '@/PlearndComponents/earn/donates/DonateCard.vue';

const props = defineProps({
    donates: {
        type: Object,
        required: true,
    },
});

</script>

<template>
    <MainLayout title="Donate List">
        <template #coverProfileCard>
            <div class="flex items-center max-w-full mx-auto mt-2 mb-4 shadow-lg bg-[url('/storage/images/banner/banner-bg.png')] bg-cover bg-no-repeat rounded-lg">
                <img class="section-banner-icon " :src="'/storage/images/banner/badges-icon.png'" alt="forums-icon">
                <p class="text-white font-bold text-4xl">รายการสนับสนุนทุนการเรียนรู้</p>
            </div>
        </template>

        <template #mainContent>
            <div class="py-4">
                <div v-if="props.donates.data.length" class="mx-auto max-w-full flex flex-wrap justify-center gap-2 ">
                    <div v-for="(donate,index) in props.donates.data" :key="index" class="w-full md:w-[48%] xl:w-[30%]">
                        <DonateCard :donate="donate" />
                    </div>
                </div>
                <div v-else class="text-center">
                    <p class="text-gray-500">ไม่มีรายการสนับสนุนทุนการเรียนรู้</p>
                </div>
            </div>
        </template>
    </MainLayout>
</template>