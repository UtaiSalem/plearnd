<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Navbar from '@/PlearndComponents/partials/Navbar.vue';

</script>

<template>
    <div>
        <MainLayout>
            <template #header>
                <div>
                    <Head title="Support"></Head>
                </div>
            </template>
            <template #navbar>
                <div>
                    <Navbar />
                </div>
            </template>

            <template #mainContent>
                <div class="mt-16">
                    <div class="max-w-2xl px-8 py-6 mx-auto my-8 bg-white border rounded-lg">
                        <h2 class="mb-4 text-2xl font-medium">สื่อสารการตลาด</h2>
                        <form>
                            <div class="mb-4">
                                <label for="product_name"
                                    class="block mb-2 font-medium text-gray-700">ชื่อสินค้า</label>
                                <input type="text" id="product_name" name="product_name"
                                    class="w-full p-2 border border-gray-400 rounded-lg focus:outline-none focus:border-blue-400"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label for="click_count"
                                    class="block mb-2 font-medium text-gray-700">จำนวนการเผยแพร่</label>
                                <input type="number" id="click_count" name="click_count"
                                    class="w-full p-2 border border-gray-400 rounded-lg focus:outline-none focus:border-blue-400"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label for="pp_per_click"
                                    class="block mb-2 font-medium text-gray-700">อัตราแต้มต่อการเผยแพร่</label>
                                <input type="number" id="pp_per_click" name="pp_per_click"
                                    class="w-full p-2 border border-gray-400 rounded-lg focus:outline-none focus:border-blue-400"
                                    required>
                            </div>

                            <div class="pt-4 mb-6">
                                <div class="mb-8">
                                    <input type="file" name="file" id="file" class="sr-only" />
                                    <label for="file"
                                        class="relative flex min-h-[200px] items-center justify-center rounded-md border border-dashed border-[#e0e0e0] p-12 text-center">
                                        <div>
                                            <span class="mb-2 block text-xl font-semibold text-[#07074D]">
                                                Drop files here
                                            </span>
                                            <span class="mb-2 block text-base font-medium text-[#6B7280]">
                                                Or
                                            </span>
                                            <span
                                                class="inline-flex rounded border border-[#e0e0e0] py-2 px-7 text-base font-medium text-[#07074D]">
                                                Browse
                                            </span>
                                        </div>
                                    </label>
                                </div>

                                <!-- <div class="rounded-md bg-[#F5F7FB] py-4 px-8">
                                        <div class="flex items-center justify-between">
                                            <span class="truncate pr-3 text-base font-medium text-[#07074D]">
                                                banner-design.png
                                            </span>
                                            <button class="text-[#07074D]">
                                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M0.279337 0.279338C0.651787 -0.0931121 1.25565 -0.0931121 1.6281 0.279338L9.72066 8.3719C10.0931 8.74435 10.0931 9.34821 9.72066 9.72066C9.34821 10.0931 8.74435 10.0931 8.3719 9.72066L0.279337 1.6281C-0.0931125 1.25565 -0.0931125 0.651788 0.279337 0.279338Z"
                                                        fill="currentColor" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M0.279337 9.72066C-0.0931125 9.34821 -0.0931125 8.74435 0.279337 8.3719L8.3719 0.279338C8.74435 -0.0931127 9.34821 -0.0931123 9.72066 0.279338C10.0931 0.651787 10.0931 1.25565 9.72066 1.6281L1.6281 9.72066C1.25565 10.0931 0.651787 10.0931 0.279337 9.72066Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="relative mt-5 h-[6px] w-full rounded-lg bg-[#E2E5EF]">
                                            <div class="absolute left-0 right-0 h-full w-[75%] rounded-lg bg-[#6A64F1]"></div>
                                        </div>
                                    </div> -->
                            </div>

                            <div>
                                <button type="submit"
                                    class="hover:shadow-form w-full rounded-md bg-[#6A64F1] py-3 px-8 text-center text-base font-semibold text-white outline-none">
                                    Send File
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </template>

        </MainLayout>
    </div>
</template>

