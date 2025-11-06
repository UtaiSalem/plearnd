<script setup>
    import { ref, onMounted } from 'vue';
    import { Link, Head, usePage, router } from '@inertiajs/vue3';
    import { initFlowbite } from 'flowbite';
    import { Icon } from '@iconify/vue';
    // import SVGJoinGroup from '@/Components/SVGIcons/Join Group Icon.svg';

    import Dropdown from '@/Components/Dropdown.vue';
    import DropdownLink from '@/Components/DropdownLink.vue';
    import LoadingPage from '@/PlearndComponents/accessories/LoadingPage.vue';
    import MobileNavbar from '@/PlearndComponents/accessories/MobileNavbar.vue';

    defineProps({
        title: String,
    });
    
    const authUser  = ref(usePage().props.auth.user);
    const isOpenSettingMenu = ref(false);
    const isSidebarOpen = ref(false);
    const isLoadingPage = ref(false);
    const currentColumn = ref(2);

    const isDarkMode = ref(false);

    const navigation = [
        // { name: 'แผงจัดการ',     href: '/dashboard', },
        // { name: 'แหล่งเรียนรู้',     href: '/academies', icon: 'teenyicons:school-outline'},
        { name: 'กระดานข่าว',        href: '/newsfeed',              icon: 'fluent:feed-24-regular',},
        { name: 'รายวิชา',           href: '/courses',               icon: 'fluent-mdl2:publish-course'},
        { name: 'สะสมแต้ม',          href: '/donates',               icon: 'mdi:hand-coin-outline'},
        { name: 'ดูสินค้า',            href: '/supports',              icon: 'eos-icons:product-subscriptions-outlined'},

    ];


    onMounted(() => {
        initFlowbite();
        // checkScreenWidth();
    });

    //a function to get screen width and set isSidebarOpen value if the screen width is lg or md
    const checkScreenWidth = () => {
        if (window.innerWidth > 1024) {
            isSidebarOpen.value = true;
        }
    };

    const handleLinkToPage = (href) => {
        isLoadingPage.value = true;
        router.visit(href);
    };

    const toggleSettingMenu = () => isOpenSettingMenu.value = !isOpenSettingMenu.value;  

    const logout = () => {
        router.post(route('logout'));
    };

    // set current column value
    const setCurrentColumn = (column) => {
        currentColumn.value = column;
        console.log(currentColumn.value);
    };


</script>
<template>
    <!-- <div class="min-h-screen bg-slate-300"> -->
    <div class="min-h-screen mx-auto bg-slate-200 dark:bg-indigo-950" :class="{'dark': isDarkMode}">

        <LoadingPage v-if="isLoadingPage" />
        
        <!-- Page Heading -->
        <Head>
            <title>{{ title }}</title>
            <!-- <link rel="icon" type="image/*" :href="'/storage/images/favicon.ico'" /> -->
        </Head>

        <!-- Navbar -->
        <header class="fixed top-0 h-[56px] sm:shadow-md pl-2 flex justify-between w-full items-center z-30 bg-[#34465d]">
            <!-- logo && Title -->
            <div class="flex items-center md:justify-between min-w-fit">
                <Link :href="route('welcome')"> 
                    <img :src="'/storage/images/plearnd-logo.png'" alt="" class="flex w-12 h-12" />
                </Link>
                <Link :href="route('welcome')" class="items-center justify-between hidden space-x-3 md:flex ">
                    <span class="px-2 text-2xl font-semibold text-white rounded-lg plearnd-brand bg-violet-500">Plearnd</span>
                </Link>
                <!-- <button type="button" title="Plearnd-Hamburger-Menu" class="text-gray-400 " @click.prevent="isSidebarOpen = !isSidebarOpen">
                    <Icon icon="solar:hamburger-menu-bold" class="w-9 h-9" />
                </button> -->
            </div>

            <!-- Navigation Menus -->
            <div class="items-center justify-center hidden w-1/3 md:flex">
                <div class="flex space-x-1">
                    <!-- <Link v-for="navbarMenu in navigation" :key="navbarMenu.name" :href="navbarMenu.href"
                        :class="[$page.url.startsWith(navbarMenu.href) ? 'bg-violet-500 text-white' : 'text-gray-300 hover:bg-violet-700 hover:text-white', 'flex items-center justify-center rounded-md px-3 py-2 text-sm font-medium']"
                        :aria-current="navbarMenu.current ? 'page' : undefined">
                        <Icon :icon="navbarMenu.icon" class="w-6 h-6 mr-2" />
                        <span class="hidden lg:block">
                            {{ navbarMenu.name }}
                        </span>
                    </Link> -->
                    <button v-for="navbarMenu in navigation" :key="navbarMenu.name" @click.prevent="handleLinkToPage(navbarMenu.href)"
                        :class="[$page.url.startsWith(navbarMenu.href) ? 'bg-violet-500 text-white' : 'text-gray-300 hover:bg-violet-700 hover:text-white', 'flex items-center justify-center rounded-md px-3 py-2 text-sm font-medium']"
                        :aria-current="navbarMenu.current ? 'page' : undefined">
                        <Icon :icon="navbarMenu.icon" class="w-5 h-5 " />
                        <span class="hidden xl:block">
                            {{ navbarMenu.name }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- User wallet -->
            <div class="items-center space-x-1 text-white flex">
                <div class="plearn-navbar-icon min-w-fit">
                    <Link :href="route('donates.list')" class="flex items-center">
                        <img :src="'/storage/images/badge/completedq.png'" alt="completedq-b" class="w-8 h-8">
                        <span>{{ authUser.pp.toLocaleString() }}</span><span class="hidden ml-1 md:flex">แต้ม</span>
                    </Link>
                </div>
                <div class="plearn-navbar-icon min-w-fit">
                    <img :src="'/storage/images/badge/goldc.png'" alt="completedq-b" class="w-8 h-8">
                    <span>{{ authUser.wallet.toLocaleString() }}</span> <span class="hidden ml-1 md:flex">บาท</span>
                </div>
            </div>

            <!-- Setting dropdown menus -->
            <div class="flex items-center justify-end space-x-2 md:w-1/2 lg:w-1/4 sm:w-24 min-w-fit">
                <div class="hidden font-semibold text-white lg:flex dark:text-white">
                    <div>
                        <Link :href="'#'" >{{ authUser.name }}</Link>
                    </div>
                </div>
                <div class="relative avatar online">
                    <Link :href="'#'" class="hidden border-2 border-white rounded-full sm:block">
                        <img class="object-cover w-8 h-8 rounded-full" :src="authUser.profile_photo_url" :alt="authUser.name" />
                    </Link>
                    <!-- <span class="bottom-3 left-7 absolute w-2.5 h-2.5 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"></span> -->
                </div>

                <Dropdown align="right" width="56">
                    <template #trigger>
                        <button v-if="$page.props.jetstream.managesProfilePhotos"
                            class="flex overflow-hidden text-sm transition border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300">
                            <!-- <img class="object-cover w-8 h-8 rounded-full" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name"> -->
                            <div class="inline-flex items-center p-1 text-sm font-medium leading-4 text-gray-200 transition duration-150 ease-in-out border border-transparent rounded-md dark:text-gray-400 dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700">
                                <!-- {{ $page.props.auth.user.name }} -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-6 h-6">
                                    <path fill-rule="evenodd"
                                        d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.02.12-.115.26-.297.348a7.493 7.493 0 00-.986.57c-.166.115-.334.126-.45.083L6.3 5.508a1.875 1.875 0 00-2.282.819l-.922 1.597a1.875 1.875 0 00.432 2.385l.84.692c.095.078.17.229.154.43a7.598 7.598 0 000 1.139c.015.2-.059.352-.153.43l-.841.692a1.875 1.875 0 00-.432 2.385l.922 1.597a1.875 1.875 0 002.282.818l1.019-.382c.115-.043.283-.031.45.082.312.214.641.405.985.57.182.088.277.228.297.35l.178 1.071c.151.904.933 1.567 1.85 1.567h1.844c.916 0 1.699-.663 1.85-1.567l.178-1.072c.02-.12.114-.26.297-.349.344-.165.673-.356.985-.57.167-.114.335-.125.45-.082l1.02.382a1.875 1.875 0 002.28-.819l.923-1.597a1.875 1.875 0 00-.432-2.385l-.84-.692c-.095-.078-.17-.229-.154-.43a7.614 7.614 0 000-1.139c-.016-.2.059-.352.153-.43l.84-.692c.708-.582.891-1.59.433-2.385l-.922-1.597a1.875 1.875 0 00-2.282-.818l-1.02.382c-.114.043-.282.031-.449-.083a7.49 7.49 0 00-.985-.57c-.183-.087-.277-.227-.297-.348l-.179-1.072a1.875 1.875 0 00-1.85-1.567h-1.843zM12 15.75a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </template>

                    <template #content>
                        <div class="overflow-hidden w-60">
                            <DropdownLink href="#">
                                <div
                                    class="flex items-center justify-between text-sm text-gray-600 transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <img class="object-cover w-12 h-12 mx-1 rounded-full"
                                        :src="authUser.profile_photo_url" :alt="authUser.name" />
                                    <div class="mx-1">
                                        <h1 class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                            {{ authUser.name }}
                                        </h1>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ authUser.email}}</p>
                                    </div>
                                </div>
                            </DropdownLink>

                            <hr class="border-gray-200 dark:border-gray-700" />

                            <DropdownLink :href="route('profile.show')">
                                <div
                                    class="flex items-center p-1 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <svg class="w-5 h-5 mx-1" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M7 8C7 5.23858 9.23858 3 12 3C14.7614 3 17 5.23858 17 8C17 10.7614 14.7614 13 12 13C9.23858 13 7 10.7614 7 8ZM12 11C13.6569 11 15 9.65685 15 8C15 6.34315 13.6569 5 12 5C10.3431 5 9 6.34315 9 8C9 9.65685 10.3431 11 12 11Z"
                                            fill="currentColor"></path>
                                        <path
                                            d="M6.34315 16.3431C4.84285 17.8434 4 19.8783 4 22H6C6 20.4087 6.63214 18.8826 7.75736 17.7574C8.88258 16.6321 10.4087 16 12 16C13.5913 16 15.1174 16.6321 16.2426 17.7574C17.3679 18.8826 18 20.4087 18 22H20C20 19.8783 19.1571 17.8434 17.6569 16.3431C16.1566 14.8429 14.1217 14 12 14C9.87827 14 7.84344 14.8429 6.34315 16.3431Z"
                                            fill="currentColor"></path>
                                    </svg>
                                    <span class="mx-1"> ข้อมูลส่วนตัว </span>
                                </div>
                            </DropdownLink>

                            <!-- <DropdownLink :href="route('dashboard')">
                                <div
                                    class="flex items-center p-1 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <svg class="w-5 h-5 mx-1" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M7 8C7 5.23858 9.23858 3 12 3C14.7614 3 17 5.23858 17 8C17 10.7614 14.7614 13 12 13C9.23858 13 7 10.7614 7 8ZM12 11C13.6569 11 15 9.65685 15 8C15 6.34315 13.6569 5 12 5C10.3431 5 9 6.34315 9 8C9 9.65685 10.3431 11 12 11Z"
                                            fill="currentColor"></path>
                                        <path
                                            d="M6.34315 16.3431C4.84285 17.8434 4 19.8783 4 22H6C6 20.4087 6.63214 18.8826 7.75736 17.7574C8.88258 16.6321 10.4087 16 12 16C13.5913 16 15.1174 16.6321 16.2426 17.7574C17.3679 18.8826 18 20.4087 18 22H20C20 19.8783 19.1571 17.8434 17.6569 16.3431C16.1566 14.8429 14.1217 14 12 14C9.87827 14 7.84344 14.8429 6.34315 16.3431Z"
                                            fill="currentColor"></path>
                                    </svg>
                                    <span class="mx-1"> แผงควบคุม </span>
                                </div>
                            </DropdownLink> -->

                            <DropdownLink :href="route('newsfeed')">
                                <div
                                    class="flex items-center p-1 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <Icon icon="solar:card-send-broken" class="w-5 h-5 mx-1" />
                                    <span class="mx-1"> กระดานข่าว </span>
                                </div>
                            </DropdownLink>

                            <DropdownLink href="/courses">
                                <div
                                    class="flex items-center p-1 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <Icon icon="solar:card-send-broken" class="w-5 h-5 mx-1" />
                                    <span class="mx-1"> รวมรายวิชา </span>
                                </div>
                            </DropdownLink>

                            <!-- <DropdownLink href="/academies">
                                <div
                                    class="flex items-center p-1 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <Icon icon="solar:card-send-broken" class="w-5 h-5 mx-1" />
                                    <span class="mx-1"> รวมแหล่งเรียนรู้ </span>
                                </div>
                            </DropdownLink> -->

                            <DropdownLink href="/supports/advertises/create">
                                <div
                                    class="flex items-center p-1 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <Icon icon="solar:card-send-broken" class="w-5 h-5 mx-1" />
                                    <span class="mx-1">ประชาสัมพันธ์สินค้า </span>
                                </div>
                            </DropdownLink>

                            <DropdownLink href="/supports/donates/create">
                                <div
                                    class="flex items-center p-1 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <Icon icon="solar:card-send-broken" class="w-5 h-5 mx-1" />
                                    <span class="mx-1">สนับสนุนทุนการศึกษา </span>
                                </div>
                            </DropdownLink>

                            <!-- <DropdownLink href="/supports/loan">
                                <div
                                    class="flex items-center p-1 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <Icon icon="solar:card-send-broken" class="w-5 h-5 mx-1" />
                                    <span class="mx-1">สนับสนุนทุนการศึกษา </span>
                                </div>
                            </DropdownLink> -->

                            <DropdownLink href="/forgot-password" v-if="$page.props.auth.user.is_plearnd_admin">
                                <div
                                    class="flex items-center p-1 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <Icon icon="solar:card-send-broken" class="w-5 h-5 mx-1" />
                                    <span class="mx-1">รีเซ็ตรหัสผ่าน </span>
                                </div>
                            </DropdownLink>

                            <DropdownLink href="/plearnd-admin/supports/advertises" v-if="$page.props.auth.user.is_plearnd_admin">
                                <div
                                    class="flex items-center p-1 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <Icon icon="solar:card-send-broken" class="w-5 h-5 mx-1" />
                                    <span class="mx-1">อนุมัติโฆษณา </span>
                                </div>
                            </DropdownLink>

                            <DropdownLink href="/plearnd-admin/supports/donates" v-if="$page.props.auth.user.is_plearnd_admin">
                                <div
                                    class="flex items-center p-1 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <Icon icon="solar:card-send-broken" class="w-5 h-5 mx-1" />
                                    <span class="mx-1">อนุมัติทุนสนับสนุนเว็บ </span>
                                </div>
                            </DropdownLink>

                            <hr class="border-t border-gray-200 dark:border-gray-600" />

                            <div class="flex items-center justify-center p-2 ">
                                <button @click.prevent="logout"
                                    class="flex items-center justify-center w-full p-2 space-x-2 font-bold text-indigo-500 rounded-md font-prompt hover:bg-indigo-200">
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
                    </template>
                </Dropdown>
            </div>

            <!-- <div>
                
                <button type="button" title="Plearnd-dark-mode-Setting" class="mt-1 text-gray-200" @click.prevent="isDarkMode = !isDarkMode">
                    <Icon icon="line-md:moon-loop" class="w-8 h-8" v-if="!isDarkMode" />
                    <Icon icon="line-md:sun-rising-loop" class="w-8 h-8" v-if="isDarkMode" />
                </button> 
               
            </div> -->

            <!-- 
            <div class="items-center justify-between hidden space-x-3 md:flex">

            </div> 
            -->

        </header>

        <MobileNavbar 
            :navigation="navigation"
            @go-to-page="(url)=>handleLinkToPage(url)"
            @set-current-active-column="(column)=> setCurrentColumn(column)"
        />

        <!-- left Sidbar -->
        <!-- <div class="fixed flex flex-col top-[56px] bg-white dark:bg-gray-900 h-full transition-all duration-300 shadow-lg z-10"
            :class="isSidebarOpen? 'w-52':'hidden sm:flex sm:flex-col w-16'"> -->
            <!-- <div class="flex flex-col justify-between flex-grow overflow-x-hidden overflow-y-auto"> -->
                <!-- <ul class="flex flex-col p-1 space-y-1"> -->
                    <!-- <li>
                        <Link href="/dashboard" class="plearnd-sidebar-link"
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
                            <span class="font-semibold font-prompt" :class="{ 'hidden': !isSidebarOpen }">แผงจัดการ</span>
                        </Link>
                    </li> -->
                    <!-- <li>
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
                       <span class="font-semibold font-prompt"
                           :class="{ 'hidden': !isSidebarOpen }">แหล่งเรียนรู้</span>
                       </Link>
                   </li> -->
                    <!-- <li>
                       <Link href="/academies" class="plearnd-sidebar-link " :class="{'justify-center': !isSidebarOpen}">
                        <SVGJoinGroup  />
                       <span class="font-semibold font-prompt"
                           :class="{ 'hidden': !isSidebarOpen }">โรงเรียนของฉัน</span>
                       </Link>
                   </li> -->

                   <!-- <li v-if="$page.props.auth.user.pp > 610000 && $page.url !=='/academy/create'">
                       <Link href="/academies/create" class="plearnd-sidebar-link "
                           :class="{'justify-center': !isSidebarOpen}">
                       <span>
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
                       <span class="font-semibold font-prompt"
                           :class="{ 'hidden': !isSidebarOpen }">เปิดแหล่งเรียนรู้ใหม่</span>
                       </Link>
                   </li> -->
                   
                   <!-- <li>
                       <button @click.prevent="handleLinkToPage('/courses')" class="plearnd-sidebar-link "
                           :class="{'justify-center': !isSidebarOpen}">
                       <span>
                           <Icon icon="mingcute:profile-line" class="w-6 h-6 opacity-80" />
                       </span>
                       <span class="font-semibold font-prompt"
                           :class="{ 'hidden': !isSidebarOpen }">รวมรายวิชา</span>
                       </button>
                   </li> -->
                   
                <!-- </ul> -->
            <!-- </div> -->
            <!-- <div class="flex items-center justify-center p-3 border-t mb-14">
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
            </div> -->
        <!-- </div> -->

        <!-- Main content -->
        <div class="flex flex-col justify-between sm:mx-4 md:pt-16 rounded-lg">

            <div v-if="$slots.coverProfileCard" class="w-full" >
                <slot name="coverProfileCard"></slot>
            </div>

            <div class="flex justify-center gap-4 md:justify-between" >

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
<style>
@import url('https://fonts.googleapis.com/css2?family=Audiowide&display=swap');

.plearnd-brand {
                font-family: 'Audiowide', sans-serif;
                font-size: 1.5rem;
                font-weight: 400;
                font-style: normal;
            }
</style>
