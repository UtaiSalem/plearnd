<template>
    <nav class="bg-[#17073a]">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="/" class="text-white text-lg font-semibold">Social Network</a>
                    <!-- Logo and Name -->
                </div>
                <div class="flex items-center">
                    <div class="mr-4">
                        <input type="text" placeholder="Search"
                            class="px-4 py-2 rounded-lg focus:outline-none focus:shadow-outline text-gray-800" />
                        <!-- Search Box -->
                    </div>
                    <div class="flex items-center mr-6">
                        <a href="/" class="text-white mr-4">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            <!-- Home Icon -->
                        </a>
                        <a href="/" class="text-white mr-4">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            <!-- Messages Icon -->
                        </a>
                        <a href="/" class="text-white mr-4">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 21h6m-3-3v-4m5 4v-4m0 0h-2.586a1 1 0 0 1-.707-.293l-2.414-2.414A1 1 0 0 1 9.586 12H4v7a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-7h-2.586a1 1 0 0 1-.707-.293l-2.414-2.414A1 1 0 0 1 9.586 8H15V5H9.586a1 1 0 0 0-.707.293L6.465 7.707A1 1 0 0 0 6 8.414V12h5.414a1 1 0 0 1 .707.293l2.414 2.414A1 1 0 0 1 
              15.414 16H9v3h5.414a1 1 0 0 0 .707-.293l2.414-2.414A1 1 0 0 0 18 16.586V13h-5.414a1 1 0 0 1-.707-.293l-2.414-2.414A1 1 0 0 1 9.586 10H15V7H9.586a1 1 0 0 0-.707.293L6.465 9.707A1 1 0 0 0 6 10.414V14h6.586a1 1 0 0 1 .707.293l2.414 2.414A1 1 0 0 1 16 17.586V21">
                                </path>
                            </svg>
                            <!-- Notifications Icon -->
                        </a>
                        <a href="/" class="text-white mr-4">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            <!-- Friend Requests Icon -->
                        </a>
                        <a href="/" class="text-white mr-4">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h18v2H3zm0 6h18v2H3zm0 6h18v2H3zm0 6h18v2H3zM3 9h12M3 15h12"></path>
                            </svg>
                            <!-- Score Points Icon -->
                        </a>
                        <button class="text-white mr-4">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5a7 7 0 0 1 7 7a7 7 0 0 1-7 7a7 7 0 0 1-7-7a7 7 0 0 1 7-7zM23 12l-2 2l-2-2">
                                </path>
                            </svg>
                            <!-- Dark Mode Toggler Button -->
                        </button>
                    </div>
                    <div class="flex items-center">
                        <img src="user-avatar.jpg" alt="User Avatar" class="h-8 w-8 rounded-full mr-2" />
                        <!-- User Avatar Image -->
                        <button class="text-white focus:outline-none">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="
              round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"></path>
                            </svg>
                            <!-- Dropdown Menu Indicator -->
                        </button>
                        <div class="ml-2">
                            <!-- Dropdown Menu Content -->
                            <div class="bg-white border rounded-lg mt-2">
                                <a href="/" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
                                <a href="/" class="block px-4 py-2 hover:bg-gray-100">Settings</a>
                                <a href="/" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup></script>

<style lang="scss" scoped></style>
