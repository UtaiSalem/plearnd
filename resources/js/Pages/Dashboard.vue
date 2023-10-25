<script setup>
import { Head } from "@inertiajs/vue3";
import Sidebar from "@/_components/Sidebar.vue";
// import NavbarMain from '@/PlearndComponents/flowbite/NavbarMain.vue';
import Navbar from "@/_components/Navbar.vue";
</script>

<template>
  <div class="h-full">
    <Head title="Teacher Dashboard" />
    <div class="hidden md:flex h-full w-56 flex-col fixed inset-y-0 z-50">
      <Sidebar />
    </div>
    <div>
      <Navbar />
    </div>
    <main>
        <slot></slot>
    </main>
  </div>
</template>
