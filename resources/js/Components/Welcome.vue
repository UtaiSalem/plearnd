<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <div>
        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
            <!-- <ApplicationLogo class="block h-12 w-auto" /> -->
            <!-- <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3"> -->
            <!-- </div> -->
            <div class=" flex items-center space-x-4">
                <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                <div class="font-medium text-xl text-gray-800 dark:text-gray-200">
                    {{ $page.props.auth.user.name }}
                </div>
                <div class="font-medium text-md text-gray-500">
                    {{ $page.props.auth.user.email }}
                </div>
            </div>

            <h1 class="mt-8 text-2xl font-medium text-gray-900">
                Welcome to plearn!
            </h1>

        </div>
    </div>
</template>
