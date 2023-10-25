<script lang="ts" setup>
import Logo from "./Logo.vue";
import SidebarRoutes from "./SidebarRoutes.vue";
</script>

<template>
    <div class=" h-full shadow-sm border-r overflow-y-auto flex flex-col bg-white">
       <div class="p-6">
            <Logo />
       </div>
       <div class="p-6">
         <SidebarRoutes />
       </div>
    </div>
</template>
