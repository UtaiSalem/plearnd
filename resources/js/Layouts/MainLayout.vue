<script setup>
    import { ref, onMounted } from 'vue'
    import { Link, Head, usePage, router } from '@inertiajs/vue3'
    import { initFlowbite } from 'flowbite'
    import { Icon } from '@iconify/vue';
    import SVGJoinGroup from '@/Components/SVGIcons/Join Group Icon.svg';

    defineProps({
        title: String,
    });

    onMounted(() => {
        initFlowbite();
    })


    const authUser  = ref(usePage().props.auth.user);
    const isOpenSettingMenu = ref(false);
    const isSidebarOpen = ref(false);

    const toggleSettingMenu = () => isOpenSettingMenu.value = !isOpenSettingMenu.value;

    const logout = () => {
        router.post(route('logout'));
    };
</script>
<template>
    <div class=" bg-slate-300 min-h-screen">
        <!-- Page Heading -->
        <Head>
            <title>{{ title }}</title>
            <link rel="icon" type="image/*" :href="'/storage/images/favicon.ico'" />
        </Head>

        <!-- Navbar -->
        <header class="fixed top-0 h-[56px] shadow-md px-2 mx-auto w-full flex bg-[#34465d] justify-between z-30">
            <!-- logo && Title -->
            <div class="flex items-center md:justify-between w-52">
                <!-- <img src="https://vasterra.com/blog/wp-content/uploads/2021/08/Tailwind-img.png" alt="" class="flex w-10 h-10" /> -->
                <Icon icon="logos:pm2-icon" class="flex w-10 h-10 text-indigo-200 -rotate-90 bg-slate-200 rounded-md p-0.5" />
                <!-- <p class="flex justify-center items-center font-extrabold text-[44px]  w-10 h-10 text-cyan-600 bg-indigo-200 rounded-md">P</p> -->
                <Link :href="route('dashboard')" class="hidden md:flex justify-between items-center space-x-3 ">
                    <span class="text-2xl font-semibold text-white uppercase">Plearnd</span>
                </Link>
                <div>
                    <button type="button" title="Plearnd-Hamburger-Menu" class="text-gray-400 mt-1" @click.prevent="isSidebarOpen = !isSidebarOpen">
                        <Icon icon="solar:hamburger-menu-bold" class="w-8 h-8" />
                    </button>
                </div>
            </div>

            <div class="hidden md:flex items-center justify-center w-1/3">

            </div>

            <div class="hidden md:flex items-center justify-between space-x-3">

            </div>

            <div class="hidden xs:flex items-center text-white space-x-1">
                <div class="plearn-navbar-icon min-w-fit">
                    <img :src="'/storage/images/badge/completedq.png'" alt="completedq-b" class="w-8 h-8">
                    <span>{{ authUser.pp.toLocaleString() }}</span><span class="ml-1 hidden md:flex">แต้ม</span>
                </div>
                <div class="plearn-navbar-icon min-w-fit">
                    <img :src="'/storage/images/badge/goldc.png'" alt="completedq-b" class="w-8 h-8">
                    <span>{{ authUser.wallet.toLocaleString() }}</span> <span class="ml-1 hidden md:flex">บาท</span>
                </div>
            </div>


            <div class="flex items-center justify-end space-x-2 md:w-1/2 lg:w-1/4 sm:w-24 min-w-fit">

                <div class="hidden md:flex font-semibold dark:text-white text-white">
                    <div>{{ authUser.name }}</div>
                </div>
                <div class="relative avatar online">
                    <button @click="isProfileOpen = !isProfileOpen" class=" hidden sm:block rounded-full border-2 border-white">
                        <img class="object-cover w-8 h-8 rounded-full" :src="authUser.profile_photo_url"
                            :alt="authUser.name" />
                    </button>
                    <!-- <span class="bottom-3 left-7 absolute w-2.5 h-2.5 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"></span> -->
                </div>

                <div class="flex items-center justify-center text-white">
                    <!-- Dropdown toggle button -->
                    <button @click.prevent="toggleSettingMenu" title="Plearnd-Setting-Menu"
                        class="relative z-10 block  border border-transparent rounded-full text-white focus:border-blue-500 focus:ring-opacity-40 dark:focus:ring-opacity-40 focus:ring-blue-300 dark:focus:ring-blue-400 focus:ring dark:bg-gray-800 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                            <path fill-rule="evenodd"
                                d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.02.12-.115.26-.297.348a7.493 7.493 0 00-.986.57c-.166.115-.334.126-.45.083L6.3 5.508a1.875 1.875 0 00-2.282.819l-.922 1.597a1.875 1.875 0 00.432 2.385l.84.692c.095.078.17.229.154.43a7.598 7.598 0 000 1.139c.015.2-.059.352-.153.43l-.841.692a1.875 1.875 0 00-.432 2.385l.922 1.597a1.875 1.875 0 002.282.818l1.019-.382c.115-.043.283-.031.45.082.312.214.641.405.985.57.182.088.277.228.297.35l.178 1.071c.151.904.933 1.567 1.85 1.567h1.844c.916 0 1.699-.663 1.85-1.567l.178-1.072c.02-.12.114-.26.297-.349.344-.165.673-.356.985-.57.167-.114.335-.125.45-.082l1.02.382a1.875 1.875 0 002.28-.819l.923-1.597a1.875 1.875 0 00-.432-2.385l-.84-.692c-.095-.078-.17-.229-.154-.43a7.614 7.614 0 000-1.139c-.016-.2.059-.352.153-.43l.84-.692c.708-.582.891-1.59.433-2.385l-.922-1.597a1.875 1.875 0 00-2.282-.818l-1.02.382c-.114.043-.282.031-.449-.083a7.49 7.49 0 00-.985-.57c-.183-.087-.277-.227-.297-.348l-.179-1.072a1.875 1.875 0 00-1.85-1.567h-1.843zM12 15.75a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <!-- Dropdown menu -->
                    <div v-if="isOpenSettingMenu"
                        class="absolute right-4 top-12 z-20 w-56 pt-2 mt-2 overflow-hidden bg-white rounded-md shadow-xl dark:bg-gray-800 transition-all duration-800">
                        <a href="#"
                            class="flex items-center p-3 -mt-2 text-sm text-gray-600 transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                            <img class="flex-shrink-0 object-cover mx-1 rounded-full w-9 h-9"
                                :src="authUser.profile_photo_url" :alt="authUser.name" />
                            <div class="mx-1">
                                <h1 class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    {{ authUser.name }}
                                </h1>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ authUser.email}}</p>
                            </div>
                        </a>

                        <hr class="border-gray-200 dark:border-gray-700" />

                        <Link href="/user/profile"
                            class="flex items-center p-3 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                            <svg class="w-5 h-5 mx-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7 8C7 5.23858 9.23858 3 12 3C14.7614 3 17 5.23858 17 8C17 10.7614 14.7614 13 12 13C9.23858 13 7 10.7614 7 8ZM12 11C13.6569 11 15 9.65685 15 8C15 6.34315 13.6569 5 12 5C10.3431 5 9 6.34315 9 8C9 9.65685 10.3431 11 12 11Z"
                                    fill="currentColor"></path>
                                <path
                                    d="M6.34315 16.3431C4.84285 17.8434 4 19.8783 4 22H6C6 20.4087 6.63214 18.8826 7.75736 17.7574C8.88258 16.6321 10.4087 16 12 16C13.5913 16 15.1174 16.6321 16.2426 17.7574C17.3679 18.8826 18 20.4087 18 22H20C20 19.8783 19.1571 17.8434 17.6569 16.3431C16.1566 14.8429 14.1217 14 12 14C9.87827 14 7.84344 14.8429 6.34315 16.3431Z"
                                    fill="currentColor"></path>
                            </svg>
                            <span class="mx-1"> ข้อมูลส่วนตัว </span>
                        </Link>

                        <Link href="/dashboard"
                            class="flex items-center p-3 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                            <svg class="w-5 h-5 mx-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7 8C7 5.23858 9.23858 3 12 3C14.7614 3 17 5.23858 17 8C17 10.7614 14.7614 13 12 13C9.23858 13 7 10.7614 7 8ZM12 11C13.6569 11 15 9.65685 15 8C15 6.34315 13.6569 5 12 5C10.3431 5 9 6.34315 9 8C9 9.65685 10.3431 11 12 11Z"
                                    fill="currentColor"></path>
                                <path
                                    d="M6.34315 16.3431C4.84285 17.8434 4 19.8783 4 22H6C6 20.4087 6.63214 18.8826 7.75736 17.7574C8.88258 16.6321 10.4087 16 12 16C13.5913 16 15.1174 16.6321 16.2426 17.7574C17.3679 18.8826 18 20.4087 18 22H20C20 19.8783 19.1571 17.8434 17.6569 16.3431C16.1566 14.8429 14.1217 14 12 14C9.87827 14 7.84344 14.8429 6.34315 16.3431Z"
                                    fill="currentColor"></path>
                            </svg>
                            <span class="mx-1"> แผงควบคุม </span>
                        </Link>

                        <Link href="/supports/advertise"
                            class="flex items-center p-3 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                            <Icon icon="solar:card-send-broken" class="w-5 h-5 mx-1" />
                            <span class="mx-1">ประชาสัมพันธ์สินค้า </span>
                        </Link>
                        
                        <Link href="/supports/loan"
                            class="flex items-center p-3 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                            <Icon icon="solar:card-send-broken" class="w-5 h-5 mx-1" />
                            <span class="mx-1">สนับสนุนทุนการศึกษา </span>
                        </Link>
                        <Link href="/forgot-password" v-if="$page.props.auth.user.id ===1"
                            class="flex items-center p-3 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                            <Icon icon="solar:card-send-broken" class="w-5 h-5 mx-1" />
                            <span class="mx-1">รีเซ็ตรหัสผ่าน </span>
                        </Link>
                        <!--
                        <a href="#"
                            class="flex items-center p-3 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                            <svg class="w-5 h-5 mx-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M13.8199 22H10.1799C9.71003 22 9.30347 21.673 9.20292 21.214L8.79592 19.33C8.25297 19.0921 7.73814 18.7946 7.26092 18.443L5.42392 19.028C4.97592 19.1709 4.48891 18.9823 4.25392 18.575L2.42992 15.424C2.19751 15.0165 2.27758 14.5025 2.62292 14.185L4.04792 12.885C3.98312 12.2961 3.98312 11.7019 4.04792 11.113L2.62292 9.816C2.27707 9.49837 2.19697 8.98372 2.42992 8.576L4.24992 5.423C4.48491 5.0157 4.97192 4.82714 5.41992 4.97L7.25692 5.555C7.50098 5.37416 7.75505 5.20722 8.01792 5.055C8.27026 4.91269 8.52995 4.78385 8.79592 4.669L9.20392 2.787C9.30399 2.32797 9.71011 2.00049 10.1799 2H13.8199C14.2897 2.00049 14.6958 2.32797 14.7959 2.787L15.2079 4.67C15.4887 4.79352 15.7622 4.93308 16.0269 5.088C16.2739 5.23081 16.5126 5.38739 16.7419 5.557L18.5799 4.972C19.0276 4.82967 19.514 5.01816 19.7489 5.425L21.5689 8.578C21.8013 8.98548 21.7213 9.49951 21.3759 9.817L19.9509 11.117C20.0157 11.7059 20.0157 12.3001 19.9509 12.889L21.3759 14.189C21.7213 14.5065 21.8013 15.0205 21.5689 15.428L19.7489 18.581C19.514 18.9878 19.0276 19.1763 18.5799 19.034L16.7419 18.449C16.5093 18.6203 16.2677 18.7789 16.0179 18.924C15.7557 19.0759 15.4853 19.2131 15.2079 19.335L14.7959 21.214C14.6954 21.6726 14.2894 21.9996 13.8199 22ZM7.61992 16.229L8.43992 16.829C8.62477 16.9652 8.81743 17.0904 9.01692 17.204C9.20462 17.3127 9.39788 17.4115 9.59592 17.5L10.5289 17.909L10.9859 20H13.0159L13.4729 17.908L14.4059 17.499C14.8132 17.3194 15.1998 17.0961 15.5589 16.833L16.3799 16.233L18.4209 16.883L19.4359 15.125L17.8529 13.682L17.9649 12.67C18.0141 12.2274 18.0141 11.7806 17.9649 11.338L17.8529 10.326L19.4369 8.88L18.4209 7.121L16.3799 7.771L15.5589 7.171C15.1997 6.90671 14.8132 6.68175 14.4059 6.5L13.4729 6.091L13.0159 4H10.9859L10.5269 6.092L9.59592 6.5C9.39772 6.58704 9.20444 6.68486 9.01692 6.793C8.81866 6.90633 8.62701 7.03086 8.44292 7.166L7.62192 7.766L5.58192 7.116L4.56492 8.88L6.14792 10.321L6.03592 11.334C5.98672 11.7766 5.98672 12.2234 6.03592 12.666L6.14792 13.678L4.56492 15.121L5.57992 16.879L7.61992 16.229ZM11.9959 16C9.78678 16 7.99592 14.2091 7.99592 12C7.99592 9.79086 9.78678 8 11.9959 8C14.2051 8 15.9959 9.79086 15.9959 12C15.9932 14.208 14.2039 15.9972 11.9959 16ZM11.9959 10C10.9033 10.0011 10.0138 10.8788 9.99815 11.9713C9.98249 13.0638 10.8465 13.9667 11.9386 13.9991C13.0307 14.0315 13.9468 13.1815 13.9959 12.09V12.49V12C13.9959 10.8954 13.1005 10 11.9959 10Z"
                                fill="currentColor"></path>
                            </svg>

                            <span class="mx-1"> Settings </span>
                        </a> -->

                        <hr class="border-gray-200 dark:border-gray-700" />
                        <div class="flex items-center justify-center p-2 ">
                            <button @click.prevent="logout"
                                class="flex w-full justify-center items-center p-2 space-x-2 rounded-md font-mali font-bold text-indigo-500 hover:bg-indigo-200">
                                <span>
                                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </span>
                                <span>ออกจากระบบ</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </header>

        <!-- left Sidbar -->
        <div class="fixed flex flex-col top-[56px] left-0   bg-white dark:bg-gray-900 h-full transition-all duration-300 shadow-lg z-20"
            :class="isSidebarOpen? 'w-52':'hidden sm:flex sm:flex-col w-16'">
            <div class="overflow-y-auto overflow-x-hidden flex flex-col justify-between flex-grow">
                <ul class="flex flex-col p-1 space-y-1">
                    <li>
                        <Link href="/dashboard" class="plearnd-sidebar-link "
                            :class="{'justify-center': !isSidebarOpen}">
                        <span>
                            <svg class="w-6 h-6 opacity-80" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px"
                                viewBox="0 0 20 20" enable-background="new 0 0 20 20" xml:space="preserve">
                                <path fill="#4f46e5" d="M16,0H4C1.791,0,0,1.791,0,4v8c0,2.209,1.791,4,4,4h12c2.209,0,4-1.791,4-4V4C20,1.791,18.209,0,16,0z
	                                 M18,12c0,1.103-0.897,2-2,2H4c-1.103,0-2-0.897-2-2V4c0-1.103,0.897-2,2-2h12c1.103,0,2,0.897,2,2V12z" />
                                <g>
                                    <g>
                                        <path fill-rule="evenodd" clip-rule="evenodd" fill="#4f46e5"
                                            d="M0,10v2h20v-2H0z M5,20h10v-2H5V20z" />
                                    </g>
                                </g>
                            </svg>
                        </span>
                        <span class="font-mali font-semibold" :class="{ 'hidden': !isSidebarOpen }">แผงจัดการ</span>
                        </Link>
                    </li>
                    <li>
                       <Link href="/academies" class="plearnd-sidebar-link " :class="{'justify-center': !isSidebarOpen}">
                       <span>
                           <svg class="w-6 h-6 opacity-80" xmlns="http://www.w3.org/2000/svg"
                               xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px"
                               viewBox="0 0 20 20" enable-background="new 0 0 20 20" xml:space="preserve">
                               <path fill-rule="evenodd" clip-rule="evenodd" fill="#4f46e5" d="M16,20h-1v-2h1c1.103,0,2-0.897,2-2v-1h2v1
                                            C20,18.209,18.209,20,16,20z M18,11h2v2h-2V11z M18,7h2v2h-2V7z M18,4c0-1.103-0.897-2-2-2h-1V0h1c2.209,0,4,1.791,4,4v1h-2V4z
                                            M8.75,9h2.5C12.767,9,14,10.346,14,12s-1.233,3-2.75,3H11v1H9v-1H7c-0.553,0-1-0.447-1-1s0.447-1,1-1h2h2h0.25
                                            c0.406,0,0.75-0.458,0.75-1s-0.344-1-0.75-1h-2.5C7.233,11,6,9.654,6,8s1.233-3,2.75-3H9V4h2v1h1.5c0.553,0,1,0.448,1,1
                                            s-0.447,1-1,1H11H9H8.75C8.344,7,8,7.458,8,8S8.344,9,8.75,9z M11,0h2v2h-2V0z M7,0h2v2H7V0z M0,16v-1h2v1c0,1.103,0.896,2,2,2h1v2
                                            H4C1.791,20,0,18.209,0,16z M2,4v1H0V4c0-2.209,1.791-4,4-4h1v2H4C2.896,2,2,2.897,2,4z M2,7v2H0V7H2z M2,13H0v-2h2V13z M9,20H7v-2
                                            h2V20z M13,20h-2v-2h2V20z" />
                           </svg>
                       </span>
                       <span class="font-mali font-semibold"
                           :class="{ 'hidden': !isSidebarOpen }">โรงเรียนของฉัน</span>
                       </Link>
                   </li>
                    <!-- <li>
                       <Link href="/academies" class="plearnd-sidebar-link " :class="{'justify-center': !isSidebarOpen}">
                        <SVGJoinGroup  />
                       <span class="font-mali font-semibold"
                           :class="{ 'hidden': !isSidebarOpen }">โรงเรียนของฉัน</span>
                       </Link>
                   </li> -->

                   <li v-if="$page.props.auth.user.pp > 610000 && $page.url !=='/academy/create'">
                       <Link href="/academies/create" class="plearnd-sidebar-link "
                           :class="{'justify-center': !isSidebarOpen}">
                       <span>
                           <!-- <Icon icon="fluent:form-new-28-regular" class="w-6 h-6 text-indigo-600" /> -->
                           <svg class="w-6 h-6 opacity-80" xmlns="http://www.w3.org/2000/svg"
                               xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px"
                               viewBox="0 0 20 20" enable-background="new 0 0 20 20" xml:space="preserve">
                               <path fill-rule="evenodd" clip-rule="evenodd" fill="#4f46e5"
                                   d="M19.968,4.031C19.941,5.666,19.579,11,15.99,11h-0.125
                                        c-0.426,2.357-2.111,2.999-5.858,2.999c-3.748,0-5.434-0.642-5.859-2.999H4.009c-3.588,0-3.951-5.333-3.977-6.969L0,2h2.028h1.98
                                        h0.015V0H15.99v2l0,0h1.981H20L19.968,4.031z M4.009,3.999L1.994,4c0,0,0.112,4.999,2.015,4.999V3.999z M13.993,2H6.02v7.6
                                        c0,2.385,0.741,2.399,3.987,2.399c3.245,0,3.986-0.014,3.986-2.399V2z M17.972,3.999H15.99v5C17.893,8.999,18.006,4,18.006,4
                                        L17.972,3.999z M11.005,15.999H13c2.206,0,3.993,1.789,3.993,4h-1.989h-0.006c0-1.104-0.896-2.001-1.998-2.001h-1.995H9.009H7.013
                                        c-1.102,0-1.996,0.896-1.996,2.001H4.996H3.02c0-2.211,1.788-4,3.993-4h1.996v-2.001h0.998h0.998V15.999z" />
                           </svg>
                       </span>
                       <span class="font-mali font-semibold"
                           :class="{ 'hidden': !isSidebarOpen }">เปิดโรงเรียนใหม่</span>
                       </Link>
                   </li>
                   <li>
                       <Link href="/courses" class="plearnd-sidebar-link "
                           :class="{'justify-center': !isSidebarOpen}">
                       <span>
                           <!-- <Icon icon="fluent:form-new-28-regular" class="w-6 h-6 text-indigo-600" /> -->
                           <Icon icon="mingcute:profile-line" class="w-6 h-6 opacity-80" />
                       </span>
                       <span class="font-mali font-semibold"
                           :class="{ 'hidden': !isSidebarOpen }">รวมรายวิชา</span>
                       </Link>
                   </li>
                </ul>
            </div>
            <div class="mb-14 p-3 flex items-center justify-center border-t">
                <!-- Sidebar footer -->
                <button @click.prevent="logout"
                    class="flex items-center justify-center w-full px-4 py-1.5 space-x-2 text-base text-indigo-600 uppercase bg-indigo-200 hover:bg-indigo-400  rounded-md focus:outline-none focus:ring">

                    <img class="object-cover w-8 h-8 rounded-full" :class="isSidebarOpen? 'block':'hidden'"
                        :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name" />

                    <div class="flex items-center">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span :class="isSidebarOpen? 'block':'hidden'"> Logout </span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex flex-col justify-between mx-4 pt-16 sm:ml-[80px] rounded-lg">

            <div v-if="$slots.coverProfileCard" class="" >
                <slot name="coverProfileCard"></slot>
            </div>

            <div class="flex justify-center md:justify-between gap-4" >

                <!-- Sidebar start-->
                <div class="hidden md:block lg:w-[35%] md:w-[40%]">
                    <div v-if="$slots.leftSideWidget">
                        <slot name="leftSideWidget" />
                    </div>
                </div>
                <!-- Sidebar end -->

                <!-- content -->
                <div class="w-full mb-4">
                    <main>
                        <slot name="mainContent"></slot>
                    </main>
                </div>
                <!-- End content -->

                <!-- right sidebar -->
                <div class="hidden lg:block w-[35%]">
                    <div v-if="$slots.rightSideWidget">
                        <slot name="rightSideWidget" />
                    </div>
                </div>
                <!--End right sidebar -->
            </div>
        </div>

    </div>
</template>

