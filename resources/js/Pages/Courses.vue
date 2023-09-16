 
<script setup>
import { ref } from 'vue';
import { Head } from "@inertiajs/vue3";

import MainLayout from "@/Layouts/MainLayout.vue";
import Navbar from "@/PlearndComponents/Navbar.vue";
import CoursesListCard from "@/PlearndComponents/courses/CoursesListCard.vue";
import PageHeader from '@/PlearndComponents/partials/PageHeader.vue';
import CoursesGrid from '@/PlearndComponents/partials/CoursesGrid.vue'
// import CreateNewSchoolWidget from '@/Components/widgets/CreateNewSchoolWidget.vue';

const props = defineProps({
    allCourses: Object,
    authOwnerCourses: Object,
    authMemberCourses: Object,
});

</script>
<template>
    <!-- <Head title="Courses" /> -->

    <div>

        <MainLayout>
            <template #header>
                <Head title="Courses"></Head>
            </template>

            <template #navbar>
                <div><Navbar /></div>
            </template>

            <!-- <template #leftSideWidget>
                <div>
                    <CreateNewSchoolWidget />
                </div>
            </template> -->

            <template #mainContent>
                <div class="mt-16 text-base text-gray-700 bg-white border-t-4 border-blue-500 rounded-lg p-4 shadow-lg ">
                    Courses รายวิชาทั้งหมด
                </div>
                <div>
                    <CoursesListCard 
                        :courses="props.allCourses.data"                      
                    ></CoursesListCard>
                    <!-- <CoursesGrid :courses="props.allCourses.data" /> -->
                </div>

            </template>

            <!-- <template #rightSideWidget>
                <div>
                    <CreateNewSchoolWidget />
                </div>
            </template> -->

        </MainLayout>

    </div>
</template>

