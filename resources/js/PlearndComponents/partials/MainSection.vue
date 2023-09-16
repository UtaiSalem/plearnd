<template>
  <main class="">
    <div class="h-full w-full">

      <MainContent>
        <slot></slot>
        
      </MainContent>
        
    </div>
  </main>
</template>

<script setup>
import MainContent from '@/Components/partials/MainContent.vue';
</script>
