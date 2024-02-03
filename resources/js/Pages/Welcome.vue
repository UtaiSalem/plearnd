<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,

    usersCount: Number,
    coursesCount: Number,
    lessonsCount: Number,
    postsCount: Number,
    ceo: Object,
    visitorCounter: Number,


    appUrl: String,
});

// const secondCounter = ref(864000);
const currentTimes = ref(null);
const seconds = ref(0);
const minutes = ref(0);
const hours = ref(0);
const daysNameTh = ref('');
const todayDate = ref(0);
const months = ref(0);
const years = ref(0);

// const totalDays = ref(0);
// const bgUrl = ref('http://plearnd.test/storage/landing/ceo.jpg');

onMounted(()=>{
    getDayOfWeek();
    setInterval(function(){
        let datetime = new Date();
        currentTimes.value = datetime.toLocaleTimeString();
        seconds.value = datetime.getSeconds();
        minutes.value = datetime.getMinutes();
        hours.value = datetime.getHours();


        // secondCounter.value++;
        // totalDays.value = Math.floor(secondCounter.value/86400);
    }, 1000);
});

const getDayOfWeek = () => {
    const today = new Date();
    // const weekDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const weekDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
    const todayMonth = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
    daysNameTh.value = weekDays[today.getDay()];
    months.value = todayMonth[today.getMonth()];
    years.value = today.getFullYear();
    todayDate.value = today.getDate();
};

</script>

<template>
    <Head title="Welcome" />

    <div
    class="relative flex justify-center items-center min-h-screen bg-[url('/storage/landing/joanna-kosinska-education-unsplash.png')] bg-cover bg-no-repeat dark:bg-gray-900 selection:bg-red-500 selection:text-white">

        <div class="w-full h-full mt-8 flex flex-col justify-center items-center">
            <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-center md:text-right z-10">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                    class="text-sm md:text-lg font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                Dashboard</Link>

                <template v-else>
                    <Link :href="route('login')"
                        class="bg-gray-200 bg-opacity-50 p-2 rounded-md text-sm md:text-lg font-semibold font-mali text-gray-800 hover:text-white hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                    เข้าใช้งาน</Link>

                    <Link v-if="canRegister" :href="route('register')"
                        class="bg-gray-200 bg-opacity-50 p-2 rounded-md text-sm md:text-lg ml-4 font-semibold font-mali text-gray-800 hover:text-white hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                    สมัครสมาชิก</Link>
                </template>
            </div>

            <p class="text-xs sm:text-2xl sm:mt-6 font-mali text-white ">เรียนบ้าง เล่นบ้าง สร้างรายได้ด้วย</p>
            <h3 class="text-base md:text-4xl text-white ">www.<b class="text-base md:text-6xl ">plearnd</b>.com</h3>


            <div class="min-w-32 min-h-48 p-3 mb-4 font-medium">
                <div class="w-32 flex-none rounded-t lg:rounded-t-none lg:rounded-l text-center shadow-lg ">
                    <div class="block rounded-t overflow-hidden  text-center font-mali">
                        <div class="bg-blue-500 py-1">
                            <p class="font-mali text-white ">
                                {{ months }}
                            </p>
                            <p class="font-mali text-white ">
                                {{ years }}
                            </p>
                        </div>
                        <div class="pt-1 border-l border-r border-white bg-white">
                            <span class="text-5xl font-bold leading-tight text-blue-500">
                                {{ todayDate }}
                            </span>
                        </div>
                        <div class="pb-2 border-l border-r border-b rounded-b-lg text-center border-white bg-white -pt-2 -mb-1">
                            <span class="text-md text-blue-500 font-extrabold">
                                {{ daysNameTh }}
                            </span>
                        </div>
                        <div class="mt-2 py-1 border-l border-r border-b rounded-lg text-center border-white bg-white">
                            <span class="text-lg leading-normal text-gray-800">
                                {{  currentTimes }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <section class="text-gray-700 body-font">
                <div class="container px-5 py-12 mx-auto">
                    <div class="flex flex-wrap -m-4 text-center text-blue-500">

                        <div class="px-4 py-2 md:w-1/4 sm:w-1/2 w-full">
                            <div class="w-full rounded-lg bg-white p-4 aspect ">
                                <div class=" flex justify-center">
                                    <Icon icon="la:users" class="text-blue-500 w-10 h-10" />
                                </div>
                                <div class="my-2">
                                    <h2 class="text-2xl font-bold text-blue-500">
                                        <span>{{ $page.props.usersCount }}</span>
                                    </h2>
                                </div>
                                <div>Users</div>
                            </div>
                        </div>
                        <div class="px-4 py-2 md:w-1/4 sm:w-1/2 w-full">
                            <div class="w-full rounded-lg bg-white p-4 aspect">
                                <div class=" flex justify-center">
                                    <Icon icon="icon-park-outline:comments" class=" text-blue-500 w-10 h-10" />
                                </div>
                                <div class="my-2">
                                    <h2 class="text-2xl font-bold text-blue-500">
                                        <span>{{ $page.props.postsCount }}</span>
                                    </h2>
                                </div>
                                <div>Posts</div>
                            </div>
                        </div>
                        <div class="px-4 py-2 md:w-1/4 sm:w-1/2 w-full">
                            <div class="w-full rounded-lg bg-white p-4 aspect">
                                <div class=" flex justify-center">
                                    <Icon icon="uil:notebooks" class="text-blue-500 w-10 h-10"/>
                                </div>
                                <div class="my-2">
                                    <h2 class="text-2xl font-bold text-blue-500">
                                        <span>{{ $page.props.coursesCount }}</span>
                                    </h2>
                                </div>
                                <div>Courses</div>
                            </div>
                        </div>
                        <div class="px-4 py-2 md:w-1/4 sm:w-1/2 w-full">
                            <div class="w-full rounded-lg bg-white p-4 aspect">
                                <div class=" flex justify-center">
                                    <Icon icon="material-symbols:library-books-outline" class="text-blue-500 w-10 h-10" />
                                </div>
                                <div class="my-2">
                                    <h2 class="text-2xl font-bold text-blue-500">
                                        <span>{{ $page.props.lessonsCount }}</span>
                                    </h2>
                                </div>
                                <div>Lessons</div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
            
            <section class="container flex w-full justify-center">
                <div class="w-full sm:w-40 flex justify-center items-center rounded-lg bg-white m-5">
                    <div class="p-3 space-y-2">
                        <div class="text-center">
                            <Icon icon="mdi:home-group" class="text-blue-500 w-10 h-10 mx-auto" />
                        </div>
                        <div class="my-2 text-center">
                            <h2 class="text-2xl font-bold text-blue-500">
                                <span>{{ $page.props.visitorCounter }}</span>
                            </h2>
                        </div>
                        <div class="text-center text-blue-500">ผู้เข้าชม</div>
                        <div class="text-center text-sm text-red-500">ตั้งแต่ {{ new Date('1/1/2024').toLocaleDateString() }}</div>
                    </div>
                </div>
            </section>

            <div class="mt-20 mb-4 max-w-6xl text-center">
                <p class="text-md sm:text-lg text-white font-mali">***อยู่ระหว่างการพัฒนาและทดลองใช้งาน***</p>
            </div>
        </div>
    </div>
    <footer class="bg-gray-200 mx-auto w-full max-w-container px-4 sm:px-6 lg:px-8">
        <div class="border-t border-slate-900/5 py-10 text-center">
            <!-- <div class="flex flex-wrap justify-center items-start">
                <span>www.</span>
                <span class="text-base md:text-4xl text-gray-600">plearnd</span>
                <span>.com</span>
            </div> -->
            <h3 class="text-gray-700 ">
                <span>www.</span>
                <b class=" md:text-4xl">plearnd</b>
                <span>.com</span>
            </h3>
            <p class="mt-5 text-center text-sm leading-6 text-gray-800 font-mali">เล่นบ้าง เรียนบ้าง สร้างรายได้ด้วย เพลิน!!</p>
            <div class="mt-8 flex items-center justify-center space-x-4 text-sm font-semibold leading-6 text-slate-700">
                <img :src="'/storage/landing/ceo.jpg'" alt="" class="rounded-full w-24 h-24">
            </div>
            <div class="mt-2 ml-16 flex flex-col items-center justify-center space-x-4 text-sm font-semibold leading-6 text-slate-700">
                <div class="w-48 flex justify-start">
                    <div class="flex items-center justify-start space-x-2">
                        <p><Icon icon="devicon:facebook" class="w-6 h-6" /></p>
                        <p>Utai Salem</p>
                    </div>
                </div>
            </div>
            <div class="mt-2 ml-16 flex flex-col items-center justify-center space-x-4 text-sm font-semibold leading-6 text-slate-700">
                <div class="w-48 flex justify-start">
                    <div class="flex items-center justify-start space-x-2">
                        <p><Icon icon="logos:messenger" class="w-6 h-6" /></p>
                        <p>Bhupha MustaFa</p>
                    </div>
                </div>
            </div>
            <div class="mt-2 ml-16 flex flex-col items-center justify-center space-x-4 text-sm font-semibold leading-6 text-slate-700">
                <div class="w-48 flex justify-start">
                    <div class="flex items-center justify-start space-x-2">
                        <p><Icon icon="uil:line" class="text-green-600 w-6 h-6" /></p>
                        <p>babobhupha</p>
                    </div>
                </div>
            </div>
            <!-- <div class="mt-2 ml-16 flex flex-col items-center justify-center space-x-4 text-sm font-semibold leading-6 text-slate-700">
                <div class="w-48 flex justify-start">
                    <div class="flex items-center justify-start space-x-2">
                        <p><Icon icon="bi:telephone" class=" w-6 h-6" /></p>
                        <p>087-2880070</p>
                    </div>
                </div>
            </div> -->
        </div>
    </footer>
</template>

