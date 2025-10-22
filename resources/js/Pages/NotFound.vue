<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

onMounted(() => {
    // Redirect to newsfeed after 3 seconds
    setTimeout(() => {
        router.push('/newsfeed-v2');
    }, 3000);
});
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-gray-800 mb-4">404</h1>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">หน้านี้ไม่พบ</h2>
            <p class="text-gray-600 mb-8">ขออภัย หน้าที่คุณกำลังมองหาไม่พบ</p>
            <div class="space-x-4">
                <button 
                    @click="router.push('/newsfeed-v2')" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    กลับไปหน้าหลัก
                </button>
                <p class="text-sm text-gray-500 mt-4">
                    จะนำทางไปยังหน้าหลักอัตโนมัติในอีก 3 วินาที
                </p>
            </div>
        </div>
    </div>
</template>