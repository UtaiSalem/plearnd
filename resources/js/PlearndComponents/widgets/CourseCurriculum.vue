<template>
    <div class="hs-accordion-group shadow-md bg-white p-5 rounded-xl divide-y-2">
        <div class="hs-accordion active" id="hs-basic-with-title-and-arrow-stretched-heading-one">
            <button class="hs-accordion-toggle hs-accordion-active:text-blue-600 group py-3 inline-flex items-center justify-between gap-x-3 w-full font-semibold text-left text-gray-800 transition hover:text-gray-500 dark:hs-accordion-active:text-blue-500 dark:text-gray-200 dark:hover:text-gray-400" aria-controls="hs-basic-with-title-and-arrow-stretched-collapse-one">
                {{ lesson.title }}
            <svg class="hs-accordion-active:hidden hs-accordion-active:text-blue-600 hs-accordion-active:group-hover:text-blue-600 block w-3 h-3 text-gray-600 group-hover:text-gray-500 dark:text-gray-400" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M2 5L8.16086 10.6869C8.35239 10.8637 8.64761 10.8637 8.83914 10.6869L15 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <svg class="hs-accordion-active:block hs-accordion-active:text-blue-600 hs-accordion-active:group-hover:text-blue-600 hidden w-3 h-3 text-gray-600 group-hover:text-gray-500 dark:text-gray-400" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M2 11L8.16086 5.31305C8.35239 5.13625 8.64761 5.13625 8.83914 5.31305L15 11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            </button>
            <div id="hs-basic-with-title-and-arrow-stretched-collapse-one" class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300" aria-labelledby="hs-basic-with-title-and-arrow-stretched-heading-one">
            <p class="text-gray-800 dark:text-gray-200">
                {{ lesson.description }}
            </p>
            <Link :href="`/lesson/${lesson.id}`">อ่านเนื้อหาบทเรียน</Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps(['lesson'])

</script>