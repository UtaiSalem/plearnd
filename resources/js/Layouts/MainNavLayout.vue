<template>
  <div class="h-screen bg-gray-300">
    <div id="MainNav" class="bg-white shadow-xl fixed h-14 z-50 flex items-center justify-between w-full border-b">
      <div id="NavStart" class="flex items-center justify-start w-[280px] p-1">
        <Link href="/" class="pl-3 min-w-[55px]">PL</Link>
        <div class="flex items-center justify-center p-1 ml-2 bg-gray-200 h-[40px] rounded-full">
          <!-- <Magnify class="p-[6px]" size="22" fillColor="#64676b" /> -->
          <input type="text" name="" id="" class="hidden lg:block bg-gray-200 border-none p-0 ring-0 focus:ring-0"
            placeholder="Search facebook" />
        </div>
      </div>

      <div id="NavCenter" class="hidden lg:flex items-center ml-5 justify-center w-8/12 max-w-[600px]">
        <Link href="/" class="w-full">
        <div :class="$page.url === '/' ? 'mt-1.5' : ''"
          class="flex items-center justify-center h-12 p-1 mt-1.5 hover:bg-gray-300 w-full rounded-lg cursor-pointer">
          <div>
            <!-- <Home v-if="$page.url === '/p'" class="mx-auto" size="27" fillColor="#1a73e3" /> -->
            <!-- <HomeOutLine v-else class="mx-auto" size="32" fillColor="#64676b" /> -->
          </div>
        </div>
        <div v-if="$page.url === '/p'" class="border-b-4 border-b-blue-400 rounded-md"></div>
        </Link>
        <button
          class="flex items-center justify-center h-12 p-1 mt-1.5 hover:bg-gray-300 w-full rounded-lg cursor-pointer">
          <!-- <TelevisionPlay class="mx-auto" size="32" fillColor="#64676b" /> -->
        </button>
        <button
          class="flex items-center justify-center h-12 p-1 mt-1.5 hover:bg-gray-300 w-full rounded-lg cursor-pointer">
          <!-- <StorefrontOutline class="mx-auto" size="32" fillColor="#64676b" /> -->
        </button>
        <button
          class="flex items-center justify-center h-12 p-1 mt-1.5 hover:bg-gray-300 w-full rounded-lg cursor-pointer">
          <span class="border-2 rounded-full border-gray-500 p-1">
            <!-- <AccountGroup class="mx-auto" size="24 " fillColor="#64676b" /> -->
          </span>
        </button>
        <button
          class="flex items-center justify-center h-12 p-1 mt-1.5 hover:bg-gray-300 w-full rounded-lg cursor-pointer">
          <!-- <AccountCircleOutline class="mx-auto" size="32" fillColor="#64676b" /> -->
        </button>
      </div>

      <div id="NavEnd" class="flex items-center justify-end w-2/12 mr-4">
        <button>
          <!-- <Dotsgrid /> -->xxxx
        </button>
      </div>
    </div>

    <main class="relative top-[56px] bg-red-200">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

</script>

<style lang="scss" scoped></style>
