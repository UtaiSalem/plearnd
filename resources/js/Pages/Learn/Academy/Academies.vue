<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import AcademyCard from '@/PlearndComponents/learn/academies/AcademyCard.vue';

const props = defineProps({
    authOwnerAcademies: Object,
    authMemberAcademies: Object,
    allAcademies: Object,

});

</script>

<template>
    <div>
        <MainLayout title="Academies">
            <template #coverProfileCard>
                <div :style="`background-image: url(${'/storage/images/banner/banner-bg.png'});`"
                    class="flex items-center max-w-7xl mx-auto jusb mt-2 mb-4 shadow-lg bg-image bg-cover bg-no-repeat">
                    <img class="section-banner-icon" :src="'/storage/images/banner/forums-icon.png'" alt="forums-icon">
                    <p class="text-white font-bold text-4xl">แหล่งเรียนรู้</p>
                </div>
            </template>
            <template #leftSideWidget>
                <div>
                    
                </div>
            </template>
            <template #mainContent>
                <div class="section-header my-4 p-4 bg-white rounded-xl shadow-lg">
                    <div class="flex justify-between">               
                        <h2 class="font-bold text-2xl text-gray-700 font-mali">แหล่งเรียนรู้ของฉัน {{ ' ' + props.authOwnerAcademies.data.length || 'ยังไม่มีแหล่งเรียนรู้' }}</h2>
                    </div>
                </div>

                <div class="flex flex-wrap justify-between gap-4 " >
                    <div v-for="(academy, index) in props.authOwnerAcademies.data" :key="index" class="w-full sm:w-[48%]"> 
                        <AcademyCard :academy="academy" />
                    </div>
                </div>

                <div class="section-header my-4 p-4 bg-white rounded-xl shadow-lg">
                    <div class="section-header-info">               
                        <h2 class="font-bold text-2xl text-gray-700 font-mali">แหล่งเรียนรู้ที่ฉันเป็นสมาชิก {{ ' ' + props.authMemberAcademies.data.length || 'ยังไม่มีแหล่งเรียนรู้ที่เป็นสมาชิก' }}</h2>
                    </div>
                </div>

                <div class="flex flex-wrap justify-between gap-4 " >
                    <div v-for="(academy, index) in props.authMemberAcademies.data" :key="index" class="w-full sm:w-[48%]"> 
                        <AcademyCard :academy="academy" />
                    </div>
                </div>

                <div class="section-header my-4 p-4 bg-white rounded-xl shadow-lg">
                    <div class="section-header-info">               
                        <h2 class="font-bold text-2xl text-gray-700 font-mali">แหล่งเรียนรู้ทั้งหมด {{ ' ' + props.allAcademies.data.length || 'ยังไม่มีแหล่งเรียนรู้' }}</h2>
                    </div>
                </div>

                <div class="flex flex-wrap justify-between gap-4" >
                    <div v-for="(academy, index) in props.allAcademies.data" :key="index" class="w-full sm:w-[48%]"> 
                        <AcademyCard :academy="academy" />
                    </div>
                </div>
                
            </template>
        </MainLayout>
    </div>
</template>

