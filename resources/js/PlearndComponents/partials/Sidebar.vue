<template>
  <div class="sm:block duration-300 mt-16"
  :class="[isSidebarMini ? 'hidden w-20': 'w-52']"
  >
    <div class="w-full h-16  flex px-4 items-center justify-between mb-8 relative">
      <Link href="/" class="font-semibold text-3xl text-blue-400 flex duration-300" :class="{'hidden': isSidebarMini}">
        <!--Logo start-->
        <svg width="30" class="text-primary-500 dark:text-white brand-logo mr-3" viewBox="0 0 30 30" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <rect x="-0.757324" y="19.2427" width="28" height="4" rx="2" transform="rotate(-45 -0.757324 19.2427)"
            fill="currentColor"></rect>
          <rect x="7.72803" y="27.728" width="28" height="4" rx="2" transform="rotate(-45 7.72803 27.728)"
            fill="currentColor"></rect>
          <rect x="10.5366" y="16.3945" width="16" height="4" rx="2" transform="rotate(45 10.5366 16.3945)"
            fill="currentColor"></rect>
          <rect x="10.5562" y="-0.556152" width="28" height="4" rx="2" transform="rotate(45 10.5562 -0.556152)"
            fill="currentColor"></rect>
        </svg>
        <!--logo End-->
        <span class="hidden sm:block" :class="{'w-16 ': isSidebarMini}">Plearn</span>
      </Link>
      <button @click="$emit('toggleSidebar')"
      class="bg-indigo-500 rounded-full p-[6px] text-white font-bold absolute -right-3"
      :class="{'right-[24px]': isSidebarMini}"
      >
        <i class="icon">
            <svg width="20" viewBox="0 0 24 24" class="icon-20 rtl:rotate-180" :class="{'rotate-180': isSidebarMini}" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </i>
      </button>
    </div>

    <div class="mb-4 px-4 space-y-2">
      <p class="pl-4 text-sm font-semibold mb-1" :class="{'hidden': isSidebarMini}">MAIN</p>
      <div
        class="w-full flex items-center text-blue-400 h-10 pl-3 bg-indigo-500-200 hover:bg-indigo-500 hover:text-white rounded-lg cursor-pointer">
        <svg class="h-6 w-6 fill-current mr-2" viewBox="0 0 20 20">
          <path
            d="M18.121,9.88l-7.832-7.836c-0.155-0.158-0.428-0.155-0.584,0L1.842,9.913c-0.262,0.263-0.073,0.705,0.292,0.705h2.069v7.042c0,0.227,0.187,0.414,0.414,0.414h3.725c0.228,0,0.414-0.188,0.414-0.414v-3.313h2.483v3.313c0,0.227,0.187,0.414,0.413,0.414h3.726c0.229,0,0.414-0.188,0.414-0.414v-7.042h2.068h0.004C18.331,10.617,18.389,10.146,18.121,9.88 M14.963,17.245h-2.896v-3.313c0-0.229-0.186-0.415-0.414-0.415H8.342c-0.228,0-0.414,0.187-0.414,0.415v3.313H5.032v-6.628h9.931V17.245z M3.133,9.79l6.864-6.868l6.867,6.868H3.133z">
          </path>
        </svg>
        <span class="text-gray-700 hover:text-white" :class="{'hidden': isSidebarMini}">Dashboard</span>
      </div>
      <div class="w-full flex items-center text-blue-400 h-10 pl-3 hover:bg-indigo-500 hover:text-white rounded-lg cursor-pointer">
        <svg class="h-6 w-6 fill-current mr-2" viewBox="0 0 20 20">
          <path
            d="M17.431,2.156h-3.715c-0.228,0-0.413,0.186-0.413,0.413v6.973h-2.89V6.687c0-0.229-0.186-0.413-0.413-0.413H6.285c-0.228,0-0.413,0.184-0.413,0.413v6.388H2.569c-0.227,0-0.413,0.187-0.413,0.413v3.942c0,0.228,0.186,0.413,0.413,0.413h14.862c0.228,0,0.413-0.186,0.413-0.413V2.569C17.844,2.342,17.658,2.156,17.431,2.156 M5.872,17.019h-2.89v-3.117h2.89V17.019zM9.587,17.019h-2.89V7.1h2.89V17.019z M13.303,17.019h-2.89v-6.651h2.89V17.019z M17.019,17.019h-2.891V2.982h2.891V17.019z">
          </path>
        </svg>
        <span class="text-gray-700 hover:text-white" :class="{'hidden': isSidebarMini}">Analytics</span>
      </div>
      <!-- <div class="w-full flex items-center text-blue-400 h-10 pl-3 hover:bg-indigo-500 hover:text-white rounded-lg cursor-pointer">
        <svg class="h-6 w-6 fill-current mr-2" viewBox="0 0 20 20">
          <path
            d="M17.283,5.549h-5.26V4.335c0-0.222-0.183-0.404-0.404-0.404H8.381c-0.222,0-0.404,0.182-0.404,0.404v1.214h-5.26c-0.223,0-0.405,0.182-0.405,0.405v9.71c0,0.223,0.182,0.405,0.405,0.405h14.566c0.223,0,0.404-0.183,0.404-0.405v-9.71C17.688,5.731,17.506,5.549,17.283,5.549 M8.786,4.74h2.428v0.809H8.786V4.74z M16.879,15.26H3.122v-4.046h5.665v1.201c0,0.223,0.182,0.404,0.405,0.404h1.618c0.222,0,0.405-0.182,0.405-0.404v-1.201h5.665V15.26z M9.595,9.583h0.81v2.428h-0.81V9.583zM16.879,10.405h-5.665V9.19c0-0.222-0.183-0.405-0.405-0.405H9.191c-0.223,0-0.405,0.183-0.405,0.405v1.215H3.122V6.358h13.757V10.405z">
          </path>
        </svg>
        <span class="text-gray-700 hover:text-white" :class="{'hidden': isSidebarMini}">Inventory</span>
      </div> -->
      <!-- <div class="w-full flex items-center text-blue-400 h-10 pl-3 hover:bg-indigo-500 hover:text-white rounded-lg cursor-pointer">
        <svg class="h-6 w-6 fill-current mr-2" viewBox="0 0 20 20">
          <path
            d="M18.303,4.742l-1.454-1.455c-0.171-0.171-0.475-0.171-0.646,0l-3.061,3.064H2.019c-0.251,0-0.457,0.205-0.457,0.456v9.578c0,0.251,0.206,0.456,0.457,0.456h13.683c0.252,0,0.457-0.205,0.457-0.456V7.533l2.144-2.146C18.481,5.208,18.483,4.917,18.303,4.742 M15.258,15.929H2.476V7.263h9.754L9.695,9.792c-0.057,0.057-0.101,0.13-0.119,0.212L9.18,11.36h-3.98c-0.251,0-0.457,0.205-0.457,0.456c0,0.253,0.205,0.456,0.457,0.456h4.336c0.023,0,0.899,0.02,1.498-0.127c0.312-0.077,0.55-0.137,0.55-0.137c0.08-0.018,0.155-0.059,0.212-0.118l3.463-3.443V15.929z M11.241,11.156l-1.078,0.267l0.267-1.076l6.097-6.091l0.808,0.808L11.241,11.156z">
          </path>
        </svg>
        <span class="text-gray-700 hover:text-white" :class="{'hidden': isSidebarMini}">Enquiries</span>
      </div> -->
      <!-- <div class="w-full flex items-center text-blue-400 h-10 pl-3 hover:bg-indigo-500 hover:text-white rounded-lg cursor-pointer">
        <svg class="h-6 w-6 fill-current mr-2" viewBox="0 0 20 20">
          <path
            d="M16.557,4.467h-1.64v-0.82c0-0.225-0.183-0.41-0.409-0.41c-0.226,0-0.41,0.185-0.41,0.41v0.82H5.901v-0.82c0-0.225-0.185-0.41-0.41-0.41c-0.226,0-0.41,0.185-0.41,0.41v0.82H3.442c-0.904,0-1.64,0.735-1.64,1.639v9.017c0,0.904,0.736,1.64,1.64,1.64h13.114c0.904,0,1.64-0.735,1.64-1.64V6.106C18.196,5.203,17.461,4.467,16.557,4.467 M17.377,15.123c0,0.453-0.366,0.819-0.82,0.819H3.442c-0.453,0-0.82-0.366-0.82-0.819V8.976h14.754V15.123z M17.377,8.156H2.623V6.106c0-0.453,0.367-0.82,0.82-0.82h1.639v1.23c0,0.225,0.184,0.41,0.41,0.41c0.225,0,0.41-0.185,0.41-0.41v-1.23h8.196v1.23c0,0.225,0.185,0.41,0.41,0.41c0.227,0,0.409-0.185,0.409-0.41v-1.23h1.64c0.454,0,0.82,0.367,0.82,0.82V8.156z">
          </path>
        </svg>
        <span class="text-gray-700 hover:text-white" :class="{'hidden': isSidebarMini}">Calender</span>
      </div> -->
    </div>


  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from "@inertiajs/vue3";


defineProps({
  isSidebarMini: Boolean
})

// const emit = defineEmits(['toggleSidebar'])
</script>
