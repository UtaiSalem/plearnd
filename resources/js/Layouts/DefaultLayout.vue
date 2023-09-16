<script setup>
import { Head, Link } from "@inertiajs/vue3";
import FlashSidebarRight from "@/PlearndComponents/partials/FlashSidebarRight.vue";
import FlashSidebarLeft from "@/PlearndComponents/partials/FlashSidebarLeft.vue";
import PlearnNavbar from "@/PlearndComponents/Navbar.vue";

</script>
<template>
    <div class="bg-slate-300 py-1">
      <PlearnNavbar />
      
      <!-- <FlashSidebarLeft class="hidden md:block" /> -->
  
      <div class="flex flex-col justify-center mx-0 md:space-x-1 md:mx-28 gap-4 mt-[72px]">
  
        <!-- Page Heading -->
        <div v-if="$slots.header">
            <slot name="header" />
        </div>
  
        <div class="flex justify-between" style="margin-left: 0px;">
          <!-- Sidebar start-->
          <div class="hidden lg:block w-[23%]">
            <div v-if="$slots.leftSideWidget">
              <slot name="leftSideWidget" />
            </div>
          </div>
          <!-- Sidebar end -->
  
          <!-- content -->
          <div class="container mx-auto w-full sm:w-[80%] lg:w-[50%] ">
            <main class="">
                <slot></slot>                
            </main>
          </div>
          <!-- End content -->
  
          <!-- right sidebar -->
          <div class="hidden lg:block w-[23%]">
            <div v-if="$slots.rightSideWidget">
              <slot name="rightSideWidget" />
            </div>
          </div>
          <!--End right sidebar -->
        </div>
      </div>
  
      <!-- <FlashSidebarRight class="hidden md:block" /> -->
    </div>
  </template>
  
  
  <style>
  @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@400;500&display=swap');
  
  *{
      font-family: 'Rajdhani', 'Kanit', sans-serif;
  }
  </style>
  