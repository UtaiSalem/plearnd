<script lang="ts" setup>
import { ref } from "vue";
import { Link } from '@inertiajs/vue3';
const routes = ref([
    {
        icon: "icon",
        label: 'My teaching',
        href:'/dashboard/student/'
    },
    {
        icon: "icon",
        label: 'My learning',
        href:'dashboard/teacher/'
    },
]);
</script>

<template>
    <div>
        <ul>
            <li v-for="route,i in routes" :key="i" class="hover:bg-blue-200 mb-2">
                <Link :href="route.href" class="flex w-full h-[40px] items-center">
                    <div class="w-1/4">{{ route.icon }}</div>
                    <div class="w-3/4">{{ route.label }}</div>
                </Link>
            </li>
        </ul>
    </div>
</template>

