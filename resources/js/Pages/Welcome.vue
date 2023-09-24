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
    ceo: Object
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

    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-gradient-to-bl from-teal-400 to-blue-500 bg-center dark:bg-gray-900 selection:bg-red-500 selection:text-white">
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-center md:text-right z-10">
            <Link v-if="$page.props.auth.user" :href="route('dashboard')" 
                class="text-sm md:text-lg font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
            Dashboard</Link>

            <template v-else>
                <Link :href="route('login')"
                    class="text-sm md:text-lg font-semibold text-gray-800 hover:text-white dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                เข้าใช้งาน</Link>

                <Link v-if="canRegister" :href="route('register')"
                    class="text-sm md:text-lg ml-4 font-semibold text-gray-800 hover:text-white dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                สมัครสมาชิก</Link>
            </template>
        </div>
        
        <div class="w-full h-full mt-8 bg-gradient-to-bl from-teal-400 to-blue-500 flex flex-col justify-center items-center text-white">

            <p class="text-xs sm:text-2xl ">เรียนบ้าง เล่นบ้าง สร้างรายได้ด้วย</p>
            <h3 class="text-base md:text-4xl ">www.<b class="text-base md:text-6xl">plearnd</b>.com</h3>

            <div class="hidden sm:grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 mt-10 lg:mt-20 justify-between">                
<!--                 
                <div class="bg-transparent border text-center items-center">
                        <div class="text-5xl px-10 py-5">
                            {{ totalDays }}
                        </div>
                        <hr>
                        <p class="px-10 py-5">days</p>
                </div>
                <div class="bg-transparent border text-center">
                    <p class="text-5xl px-10 py-5">{{ hour }}</p>
                    <hr>
                    <p class="px-10 py-5">hours</p>
                </div>
                <div class="bg-transparent border text-center">
                    <p class="text-5xl px-10 py-5 w-[100px]">{{ minute }}</p>
                    <hr>
                    <p class="px-10 py-5">mins</p>
                </div>
                <div class="bg-transparent border text-center">
                    <p class="text-5xl px-10 py-5 w-[100px]">{{ second }}</p>
                    <hr>
                    <p class="px-10 py-5">secs</p>
                </div> -->
            </div>

            <div class="min-w-32 min-h-48 p-3 mb-4 font-medium">
                <div class="w-32 flex-none rounded-t lg:rounded-t-none lg:rounded-l text-center shadow-lg ">
                    <div class="block rounded-t overflow-hidden  text-center ">
                        <div class="bg-blue-500 text-white py-1">
                            <p>
                            {{ months }}
                            </p>
                            <p>
                            {{ years }}
                            </p>
                        </div>
                        <div class="pt-1 border-l border-r border-white bg-white">
                            <span class="text-5xl font-bold leading-tight text-gray-800">
                                {{ todayDate }}
                            </span>
                        </div>
                        <div class="pb-2 border-l border-r border-b rounded-b-lg text-center border-white bg-white -pt-2 -mb-1">
                            <span class="text-md text-gray-800 font-extrabold">
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
                                    <h2 class="text-2xl font-bold">
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
                                    <h2 class="text-2xl font-bold">
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
                                    <h2 class="text-2xl font-bold">
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
                                    <h2 class="text-2xl font-bold">
                                        <span>{{ $page.props.lessonsCount }}</span> 
                                    </h2>
                                </div>
                                <div>Lessons</div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <div class="mt-20 mb-4 max-w-6xl text-center">
                <p class="text-xs sm:text-lg text-red-600">***อยู่ระหว่างการพัฒนาและทดลองใช้งาน***</p>
            </div>
        </div>
    </div>
    <footer class="bg-gray-200 mx-auto w-full max-w-container px-4 sm:px-6 lg:px-8">
        <div class="border-t border-slate-900/5 py-10 text-center">
            <div class="text-center flex flex-wrap">
                <span>www.</span>
                <span class="text-base md:text-4xl text-gray-600">plearnd</span>
                <span>.com</span>
            </div>
            <p class="mt-5 text-center text-sm leading-6 text-slate-700">เล่นบ้าง เรียนบ้าง สร้างรายได้ด้วย เพลิน!!</p>
            <div class="mt-8 flex items-center justify-center space-x-4 text-sm font-semibold leading-6 text-slate-700">
                <img :src="ceo.data.avatar" alt="" class="rounded-full w-24 h-24">
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

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}

@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}</style>
