<script setup>
import { ref, reactive, computed, watch } from 'vue';
import { router, usePage } from "@inertiajs/vue3";
import { Icon } from '@iconify/vue';
import Swal from 'sweetalert2';

import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const props = defineProps({
    course: Object,
    isCourseAdmin: Boolean
});

const emit = defineEmits(['update-course']);

const isOpenCategoryOptions = ref(false);
const isOpenLevelOptions = ref(false);
const courseRange = ref([
    props.course.start_date, 
    props.course.end_date
]);

const courseCategories = ref([
    { name: 'ภาษาไทย' },
    { name: 'คณิตศาสตร์' },
    { name: 'วิทยาศาสตร์' },
    { name: 'สังคมศึกษา ศาสนา และวัฒนธรรม' },
    { name: 'สุขศึกษาและพลศึกษา' },
    { name: 'ศิลปะ' },
    { name: 'การงานอาชีพและเทคโนโลยี' },
    { name: 'ภาษาต่างประเทศ' },
]);
const courseLevelOptions = ref([
    { level: 'ชั้นประถมศึกษาปีที่ 1' },
    { level: 'ชั้นประถมศึกษาปีที่ 2' },
    { level: 'ชั้นประถมศึกษาปีที่ 3' },
    { level: 'ชั้นประถมศึกษาปีที่ 4' },
    { level: 'ชั้นประถมศึกษาปีที่ 5' },
    { level: 'ชั้นประถมศึกษาปีที่ 6' },
    { level: 'ชั้นมัธยมศึกษาปีที่ 1' },
    { level: 'ชั้นมัธยมศึกษาปีที่ 2' },
    { level: 'ชั้นมัธยมศึกษาปีที่ 3' },
    { level: 'ชั้นมัธยมศึกษาปีที่ 4' },
    { level: 'ชั้นมัธยมศึกษาปีที่ 5' },
    { level: 'ชั้นมัธยมศึกษาปีที่ 6' },

]);

const form = ref({
  code: props.course.code,
  name: props.course.name,
  description: props.course.description,
  category: props.course.category,
  level: props.course.level,
  credit_units: props.course.credit_units,
  hours_per_week: props.course.hours_per_week,

  start_date: props.course.start_date,
  end_date: props.course.end_date,
//   start_date: courseRange.value[0] === "" ? null : new Date(courseRange.value[0]),
//   end_date: courseRange.value[1] === "" ? null : new Date(courseRange.value[1]),

  auto_accept_members: props.course.setting.auto_accept_members == 1 ? true : false,
  tuition_fees: props.course.tuition_fees,
  saleable: props.course.saleable == 1 ? true : false,
  price: props.course.price,
  status: props.course.status,

});

const formDelete = ref({
    course_name:''
});

const canDeleteCourse = computed(() => {
    return props.isCourseAdmin && formDelete.value.course_name === props.course.name;
});

function handleDeleteCourse(){
    // for (let index = 0; index < 100; index++) {
    //     const randomNumber = Math.random().toString().slice(2, 10);
    //     console.log(randomNumber);
    // }
    Swal.fire({
        title: 'ลบบทเรียน',
        text: "ยืนยันการลบบทเรียนอย่างถาวร",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#7c3aed',
        cancelButtonColor: '#f87171',
        confirmButtonText: 'ยืนยันการลบ',
    })
    .then( async (result)=> {
        if (result.isConfirmed) {
            let deleteResp = await axios.delete(`/courses/${props.course.id}`);
            if (deleteResp.data && deleteResp.data.success) {
                console.log(deleteResp.data);
                router.route('courses');
            }
        }
    });
}

function handleSelectCategory(category){
  form.value.category = category;
  isOpenCategoryOptions.value = false;
}
function handleSelectLevel(level){
  form.value.level = level;
  isOpenLevelOptions.value = false;
}

function handleDateSelect(modelData){
  courseRange.value = modelData;
//   form.value.start_date = modelData ? new Date(courseRange.value[0]).toDateString() : null;
  form.value.start_date = modelData ? new Date(courseRange.value[0]).toLocaleString() : null;
  form.value.end_date   = modelData ? new Date(courseRange.value[1]).toLocaleString() : null;
//   form.value.start_date = new Date(modelData[0]);
//   form.value.end_date = new Date(modelData[1]);

}

// function handleSubmitForm(){
    // console.log(courseData.start_date);
    // console.log(courseData.end_date);
    // emit('update-course', form.value);
// }

async function handleSubmitForm(){

try {
    const config = { headers: { 'Content-Type': 'multipart/form-data' } };
    let courseUpdateForm = new FormData();
    courseUpdateForm.append('name', form.value.name);
    courseUpdateForm.append('code', form.value.code);
    courseUpdateForm.append('description', form.value.description);
    courseUpdateForm.append('category', form.value.category);
    courseUpdateForm.append('level', form.value.level);   
    courseUpdateForm.append('credit_units', form.value.credit_units);
    courseUpdateForm.append('hours_per_week', form.value.hours_per_week);
    courseUpdateForm.append('start_date', form.value.start_date);
    courseUpdateForm.append('end_date', form.value.end_date);
    courseUpdateForm.append('auto_accept_members', form.value.auto_accept_members);
    courseUpdateForm.append('tuition_fees', form.value.tuition_fees);
    courseUpdateForm.append('saleable', form.value.saleable);
    courseUpdateForm.append('price', form.value.price);
    courseUpdateForm.append('status', form.value.status);
    
    courseUpdateForm.append('_method', 'put');
    let resultResp = await axios.post(`/courses/${props.course.id}`, courseUpdateForm ,config);

    if (resultResp.data && resultResp.data.success) {
        // console.log(resultResp.data);
        router.reload({ only: ['course']});
        Swal.fire({
            title: 'บันทึกข้อมูลสำเร็จ',
            text: "แก้ไขข้อมูลรายวิชาเรียบร้อยแล้ว",
            icon: 'success',
            showConfirmButton: false,
            timer: 1200
        });
    }
} catch (error) {
    console.log(error);
}

// console.log(usePage().props.course.data);
}
</script>

<template>
    <div class="w-full mx-auto ">
        <div class="plearnd-card my-4 font-bold uppercase text-2xl bg-blue-500 text-white">
            การตั้งค่ารายวิชา
        </div>

        <div class="bg-white rounded-lg border-t-4 border-blue-500 px-4 pb-4 mb-4">
            <form @submit.prevent="handleSubmitForm" class="space-y-2" id="create-new-course-form">
                <div class="grid grid-cols-1 gap-4 my-2">
              <h3 class="text-lg font-semibold text-gray-600 dark:text-white ">
                รหัสวิชา
              </h3>
              <TextInput id="course_code_input_form" type="text" class="w-full text-start -mt-3 " v-model="form.code" />
            </div>
            <div class="grid grid-cols-1 gap-4 my-2">
              <h3 class="text-lg font-semibold text-gray-600 dark:text-white ">
                ชื่อวิชา
              </h3>
              <textarea id="course_name_input_form" v-model="form.name" rows="1"
                class="-mt-2 w-full items-center py-4 text-sm text-gray-800 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="ชื่อรายวิชา" required></textarea>
            </div>
                <div class="grid grid-cols-1 gap-4 my-2">
                    <InputLabel for="course_description_input_form" value="คำอธิบายรายวิชา" />
                    <textarea id="course_description_input_form" v-model="form.description" rows="8"
                        class="block p-2.5 -mt-2 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="คำอธิบายรายวิชา"></textarea>

                </div>

                <div class="grid grid-cols-1 gap-2 my-2">
                    <InputLabel for="course_category_input_form" value="กลุ่มสาระการเรียนรู้" />
                    <div class="flex justify-between items-center relative">

                        <input id="course_category_input_form" type="text"
                            class="text-start w-full rounded-l-lg mr-[1px] border-1 border-gray-300"
                            v-model="form.category" @input.prevent="()=>isOpenCategoryOptions=false" />

                        <button type="button" @click.prevent="()=>{ isOpenCategoryOptions = !isOpenCategoryOptions }"
                            class="bg-blue-600 w-8 rounded-r-lg h-full flex justify-center items-center text-white">
                            <Icon icon="heroicons-solid:chevron-down" class="w-9 h-9" />
                        </button>

                        <div v-if="isOpenCategoryOptions"
                            class="absolute right-0 top-12 z-20 w-full pt-2 overflow-hidden bg-white rounded-md shadow-xl dark:bg-gray-800 transition-all duration-800">
                            <div v-for="(category) in courseCategories" :key="category.name">
                                <button @click.prevent="handleSelectCategory(category.name)"
                                    class="flex items-center w-full p-3 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <Icon icon="solar:card-send-broken" class="w-5 h-5 mx-1" />
                                    <span class="mx-1">{{ category.name }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-2 my-2">
                    <InputLabel for="course_level_input_form" value="ระดับการเรียนรู้" />
                    <div class="flex justify-between items-center relative">

                        <input id="course_level_input_form" type="text"
                            class="text-start w-full rounded-l-lg mr-[1px] border-1 border-gray-300"
                            v-model="form.level" @input.prevent="()=>isOpenLevelOptions=false" />

                        <button type="button" @click.prevent="()=>{ isOpenLevelOptions = !isOpenLevelOptions }"
                            class="bg-blue-600 w-8 rounded-r-lg h-full flex justify-center items-center text-white">
                            <Icon icon="heroicons-solid:chevron-down" class="w-9 h-9" />
                        </button>

                        <div v-if="isOpenLevelOptions"
                            class="absolute right-0 top-12 z-20 w-full h-60 pt-2 overflow-y-scroll bg-white rounded-md shadow-xl dark:bg-gray-800 transition-all duration-800">
                            <div v-for="(option) in courseLevelOptions" :key="option.level">
                                <button @click.prevent="handleSelectLevel(option.level)"
                                    class="flex items-center w-full p-3 text-sm text-gray-600 capitalize transition-colors duration-300 transform dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <Icon icon="solar:card-send-broken" class="w-5 h-5 mx-1" />
                                    <span class="mx-1">{{ option.level }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-2">

                    <div class="relative sm:my-3">
                        <input id="course-credit_unit-input" type="number" min="0" v-model="form.credit_units"
                            placeholder="0"
                            class="relative w-full h-12 px-4 pl-14 placeholder-transparent transition-all border rounded-lg outline-none focus-visible:outline-none peer border-slate-300 text-slate-500 autofill:bg-white invalid:border-pink-500 invalid:text-pink-500 focus:border-violet-500 focus:outline-none invalid:focus:border-pink-500 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400" />
                        <label for="course-credit_unit-input"
                            class="cursor-text peer-focus:cursor-default peer-autofill:-top-2 absolute left-2 -top-2 z-[1] px-2 text-xs text-slate-400 transition-all before:absolute before:top-0 before:left-0 before:z-[-1] before:block before:h-full before:w-full before:bg-white before:transition-all peer-placeholder-shown:top-4 peer-placeholder-shown:left-12 peer-placeholder-shown:text-sm peer-required:after:text-pink-500 peer-required:after:content-['\00a0*'] peer-invalid:text-pink-500 peer-focus:-top-2 peer-focus:left-2 peer-focus:text-xs peer-focus:text-violet-500 peer-invalid:peer-focus:text-pink-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400 peer-disabled:before:bg-transparent">
                            หน่วยกิต
                        </label>
                        <Icon icon="fluent:number-symbol-square-24-regular"
                            class="absolute w-7 h-7 top-3 left-4 text-violet-600 peer-disabled:cursor-not-allowed" />
                    </div>

                    <div class="relative sm:my-3">
                        <input id="course-hpw-input" type="number" min="0" v-model="form.hours_per_week" placeholder="0"
                            class="relative w-full h-12 px-4 pl-12 placeholder-transparent transition-all border rounded-lg outline-none focus-visible:outline-none peer border-slate-300 text-slate-500 autofill:bg-white invalid:border-pink-500 invalid:text-pink-500 focus:border-violet-500 focus:outline-none invalid:focus:border-pink-500 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400" />
                        <label for="course-hpw-input"
                            class="cursor-text peer-focus:cursor-default peer-autofill:-top-2 absolute left-4 -top-2 z-[1] px-2 text-xs text-slate-400 transition-all before:absolute before:top-0 before:left-0 before:z-[-1] before:block before:h-full before:w-full before:bg-white before:transition-all peer-placeholder-shown:top-4 peer-placeholder-shown:left-10 peer-placeholder-shown:text-sm peer-required:after:text-pink-500 peer-required:after:content-['\00a0*'] peer-invalid:text-pink-500 peer-focus:-top-2 peer-focus:left-2 peer-focus:text-xs peer-focus:text-violet-500 peer-invalid:peer-focus:text-pink-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400 peer-disabled:before:bg-transparent">
                            คาบ / สัปดาห์
                        </label>
                        <Icon icon="bi:calendar4-week"
                            class="absolute w-5 h-5 top-4 left-4 text-violet-600 peer-disabled:cursor-not-allowed" />
                    </div>

                </div>

                <div class="grid grid-cols-1 gap-4">
                    <InputLabel for="course_description_input_form" value="ระยะเวลา" />
                    <div class="flex items-center space-x-2 -mt-2">
                        <div class="w-full ">
                            <div class="">
                                <VueDatePicker :id="'slip-date-input'" name="courseStartDateInput" v-model="courseRange"
                                    :format="'dd/MM/yyyy'" 
                                    :enable-time-picker="false" 
                                    range 
                                    multi-calendars
                                    :model-value="courseRange" 
                                    @update:model-value="handleDateSelect"
                                    placeholder="วันเริ่มต้น - วันสิ้นสุด">
                                </VueDatePicker>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 my-2">
                    <div class="relative flex flex-wrap items-center mt-2 mb-2">
                        <input type="checkbox" id="autoAcceptCourseMemberInput" v-model="form.auto_accept_members"
                            :checked="form.auto_accept_members"
                            class="w-4 h-4 transition-colors bg-white border-2 rounded appearance-none cursor-pointer focus-visible:outline-none peer border-slate-500 checked:border-violet-500 checked:bg-violet-500 checked:hover:border-violet-600 checked:hover:bg-violet-600 focus:outline-none checked:focus:border-violet-700 checked:focus:bg-violet-700 disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-50" />
                        <label
                            class="pl-2 cursor-pointer text-slate-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400"
                            for="autoAcceptCourseMemberInput">
                            ตอบรับคำขอเป็นสมาชิกอัตโนมัติ
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-2 mb-1">

                    <div class="relative mb-4">
                        <input id="course-fees-input" type="number" v-model="form.tuition_fees" placeholder="0"
                            class="relative w-full h-12 px-4 pl-12 placeholder-transparent transition-all border rounded outline-none focus-visible:outline-none peer border-slate-300 text-slate-500 autofill:bg-white invalid:border-pink-500 invalid:text-pink-500 focus:border-violet-500 focus:outline-none invalid:focus:border-pink-500 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400" />
                        <label for="course-fees-input"
                            class="cursor-text peer-focus:cursor-default peer-autofill:-top-2 absolute left-2 -top-2 z-[1] px-2 text-xs text-slate-400 transition-all before:absolute before:top-0 before:left-0 before:z-[-1] before:block before:h-full before:w-full before:bg-white before:transition-all peer-placeholder-shown:top-3 peer-placeholder-shown:left-10 peer-placeholder-shown:text-sm peer-required:after:text-pink-500 peer-required:after:content-['\00a0*'] peer-invalid:text-pink-500 peer-focus:-top-2 peer-focus:left-2 peer-focus:text-xs peer-focus:text-violet-500 peer-invalid:peer-focus:text-pink-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400 peer-disabled:before:bg-transparent">
                            ค่าธรรมเนียมสมัครสมาชิก
                        </label>
                        <Icon icon="noto:coin"
                            class="absolute w-6 h-6 top-3 left-4 stroke-slate-400 peer-disabled:cursor-not-allowed" />
                        <small
                            class="absolute flex justify-between w-full mb-4 py-1 text-xs transition text-red-400 peer-invalid:text-pink-500">
                            <span>คะแนนที่ต้องใช้เมื่อผู้อื่นต้องการเข้าร่วมสมาชิก</span>

                        </small>
                    </div>

                </div>

                <hr class="bg-white border-white" />

                <div class="relative flex items-center mt-6 mb-2">
                    <input
                        class="w-4 h-4 mt-6 transition-colors bg-white border-2 rounded appearance-none cursor-pointer focus-visible:outline-none peer border-slate-500 checked:border-violet-500 checked:bg-violet-500 checked:hover:border-violet-600 checked:hover:bg-violet-600 focus:outline-none checked:focus:border-violet-700 checked:focus:bg-violet-700 disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-50"
                        type="checkbox" id="course-saling-input" v-model="form.saleable" :checked="form.saleable"
                        placeholder="0" />
                    <label
                        class="pl-2 mt-6 cursor-pointer text-slate-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400"
                        for="course-saling-input">
                        <p class="flex">
                            <span>ขาย </span>
                            <span class="hidden md:block">(เมื่อมีการขอซื้อ)</span>
                        </p>
                    </label>

                    <div class="relative ml-2 md:ml-4" v-if="form.saleable">
                        <input id="course-price-input" type="number" v-model="form.price" placeholder=""
                            class="relative w-full h-12 px-4 pl-12 placeholder-transparent transition-all border rounded outline-none focus-visible:outline-none peer border-slate-200 text-slate-500 autofill:bg-white invalid:border-pink-500 invalid:text-pink-500 focus:border-violet-500 focus:outline-none invalid:focus:border-pink-500 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400" />
                        <label for="course-price-input"
                            class="cursor-text peer-focus:cursor-default peer-autofill:-top-2 absolute left-2 -top-2 z-[1] px-2 text-xs text-slate-400 transition-all before:absolute before:top-0 before:left-0 before:z-[-1] before:block before:h-full before:w-full before:bg-white before:transition-all peer-placeholder-shown:top-3 peer-placeholder-shown:left-10 peer-placeholder-shown:text-sm peer-required:after:text-pink-500 peer-required:after:content-['\00a0*'] peer-invalid:text-pink-500 peer-focus:-top-2 peer-focus:left-2 peer-focus:text-xs peer-focus:text-violet-500 peer-invalid:peer-focus:text-pink-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400 peer-disabled:before:bg-transparent">
                            ราคา
                        </label>
                        <Icon icon="noto:money-bag"
                            class="absolute w-6 h-6 top-3 left-4 stroke-slate-400 peer-disabled:cursor-not-allowed" />
                    </div>

                    <p v-if="form.saleable" class="ml-2 mt-6">บาท</p>
                </div>

                <div class="flex space-x-2">
                    <!-- <p class="text-sm">ปิด</p>
                    <input type="checkbox" v-model="form.status" class="peer sr-only opacity-0"
                        id="input-course-status-toggle" />
                    <label for="input-course-status-toggle"
                        class="relative flex h-6 w-11 cursor-pointer items-center rounded-full bg-gray-400 px-0.5 outline-gray-400 transition-colors before:h-5 before:w-5 before:rounded-full before:bg-white before:shadow before:transition-transform before:duration-300 peer-checked:bg-green-500 peer-checked:before:translate-x-full peer-focus-visible:outline peer-focus-visible:outline-offset-2 peer-focus-visible:outline-gray-400 peer-checked:peer-focus-visible:outline-green-500">
                        <span class="sr-only">Enable</span>
                    </label>
                    <p class="text-sm">เปิด (การเรียนการสอน)</p> -->
                    <fieldset class="flex gap-6 mt-2">
                        <legend class="mb-2 text-slate-500">สถานะรายวิชา:</legend>
                        <div class="relative flex items-center">
                            <input
                                class="w-4 h-4 transition-colors bg-white border-2 rounded-full appearance-none cursor-pointer peer border-slate-500 checked:border-emerald-500 checked:bg-emerald-500 checked:hover:border-emerald-600 checked:hover:bg-emerald-600 focus:outline-none checked:focus:border-emerald-700 checked:focus:bg-emerald-700 focus-visible:outline-none disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-50"
                                type="radio" :value="1" v-model="form.status" :id="`course-${props.course.id}-status-active`" :name="form.status" />
                            <label
                                class="pl-2 cursor-pointer text-slate-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400"
                                :for="`course-${props.course.id}-status-active`">
                                เปิดการเรียน
                            </label>
                        </div>
                        <div class="relative flex items-center">
                            <input
                                class="w-4 h-4 transition-colors bg-white border-2 rounded-full appearance-none cursor-pointer peer border-slate-500 checked:border-emerald-500 checked:bg-emerald-500 checked:hover:border-emerald-600 checked:hover:bg-emerald-600 focus:outline-none checked:focus:border-emerald-700 checked:focus:bg-emerald-700 focus-visible:outline-none disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-50"
                                type="radio" :value="2" v-model="form.status" :id="`course-${props.course.id}-status-draft`" :name="form.status" />
                            <label
                                class="pl-2 cursor-pointer text-slate-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400"
                                :for="`course-${props.course.id}-status-draft`">
                                ร่าง
                            </label>
                        </div>
                        <div class="relative flex items-center">
                            <input
                                class="w-4 h-4 transition-colors bg-white border-2 rounded-full appearance-none cursor-pointer peer border-slate-500 checked:border-emerald-500 checked:bg-emerald-500 checked:hover:border-emerald-600 checked:hover:bg-emerald-600 focus:outline-none checked:focus:border-emerald-700 checked:focus:bg-emerald-700 focus-visible:outline-none disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-50"
                                type="radio" :value="3" v-model="form.status" :id="`course-${props.course.id}-status-completed`" :name="form.status" />
                            <label
                                class="pl-2 cursor-pointer text-slate-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400"
                                :for="`course-${props.course.id}-status-completed`">
                                การเรียนเสร็จสิ้น(เนื้อหาครบสมบูรณ์)
                            </label>
                        </div>
                        <!-- <div class="relative flex items-center">
                            <input
                                class="w-4 h-4 transition-colors bg-white border-2 rounded-full appearance-none cursor-pointer peer border-slate-500 checked:border-emerald-500 checked:bg-emerald-500 checked:hover:border-emerald-600 checked:hover:bg-emerald-600 focus:outline-none checked:focus:border-emerald-700 checked:focus:bg-emerald-700 focus-visible:outline-none disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-50"
                                type="radio" :value="4" v-model="form.status" :id="`course-${props.course.id}-status-closed`" :name="form.status" />
                            <label
                                class="pl-2 cursor-pointer text-slate-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400"
                                :for="`course-${props.course.id}-status-closed`">
                                ปิดการเรียน
                            </label>
                        </div> -->
                    </fieldset>
                </div>


                <div class="grid grid-cols-1 gap-4">
                    <div class="flex items-center justify-around mt-4">
                        <button type="button"
                            class="px-6 py-2 text-white bg-red-500 hover:bg-red-600 hover:text-white rounded">
                            ยกเลิก
                        </button>

                        <button type="submit"
                            class="px-6 py-2 text-white border bg-blue-500 hover:bg-blue-600  rounded-lg">
                            บันทึก
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="bg-white rounded-lg border-t-4 border-blue-500 p-4 mb-4">
            <div class="mb-2">
                <p class="font-bold text-red-600 text-lg">ลบรายวิชา</p>
            </div>
            <div>
                <p class="text-red-400">ข้อมูลทั้งหมดของรายวิชานี้จะถูกลบอย่างถาวร</p>
            </div>
            <div>
                <p class="text-red-400">กรุณาป้อนชื่อรายวิชานี้</p>
                <TextInput id="course_name_input_form" type="text" class="w-full text-start mt-2"
                    v-model="formDelete.course_name" />
                <button v-if="canDeleteCourse" @click.prevent="handleDeleteCourse"
                    class="w-full bg-red-500 p-2 text-white rounded-lg mt-2">ลบรายวิชา</button>
            </div>
        </div>
    </div>
</template>
