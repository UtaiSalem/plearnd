<template>
    <div class="p-4 bg-white rounded-lg shadow">
      <div class="flex items-start">
        <img :src="user.avatar" alt="User Avatar" class="h-10 w-10 rounded-full mr-4" />
        <textarea class="flex-grow resize-none focus:outline-none" rows="3" placeholder="Write your post..."></textarea>
      </div>
      <div class="flex justify-end mt-4">
        <button class="px-4 py-2 bg-blue-500 text-white rounded-lg">Post</button>
      </div>
    </div>
  </template>
  
  <script setup>
    import { reactive } from "vue";

    const user = reactive({
        avatar: '/storage/assets/images/user/01.jpg', // Replace with the actual path to the user avatar
      });

  </script>
  