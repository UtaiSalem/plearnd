<script setup>
import { ref,reactive } from "vue";
import { Icon } from '@iconify/vue';
import { usePage } from '@inertiajs/vue3';
import { useObjectUrl } from '@vueuse/core';
// import TextInput from '@/Components/TextInput.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    assignmentableType: String,
    assignmentableId: Number,
    assignmentApiRoute: String,
    assignmentNameTh: String
});
const emit = defineEmits(['add-new-assignment']);

const inputAsmImages = ref(null);
const tempImages = reactive([]);

const form = reactive({
    title:'',
    points:0
});

const showCreateNewAssignmentForm =ref(false);

const browseInputAsmImages = () => inputAsmImages.value.click();
async function onSubmitFormHandler(e) {
    try {
        const config = { headers: { 'Content-Type': 'multipart/form-data' } };
        let newAsmForm = new FormData();
        newAsmForm.append('title', form.title);
        newAsmForm.append('points', form.points);

        Array.from(tempImages).forEach((image)=>{
        newAsmForm.append('images[]', image.file);
        });

        let asmResp = await axios.post(`${props.assignmentApiRoute}/assignments`, newAsmForm, config);
        if (asmResp.status===200) {
            emit('add-new-assignment', asmResp.data.assignment);
            tempImages.splice(0);
            Swal.fire(
                'เรียบร้อย',
                'เพิ่มแบบฝึกหัดใหม่เสร็จสมบูรณ์',
                'success'
            )
        }
        e.target.files = [];
        form.points = 0;  
        showCreateNewAssignmentForm.value = false;
    } catch (err) {
        console.error(err);
    }
}
function onInputImageChange(e){
    Array.from(e.target.files).forEach(image => {
        tempImages.push({ file: image, url: useObjectUrl(image)});
    });
}
function onCancleHandler(e){
    // e.target.files = [];
    inputAsmImages.files = [];
    form.title = '';
    form.points = 0;
    tempImages.splice(0);
    showCreateNewAssignmentForm.value = false;
}

function deleteTempImage(index){ tempImages.splice(index,1); }


</script>
<template>
    <div class="bg-white border-t-4 border-blue-600 rounded-lg overflow-hidden shadow-lg">
        <!-- <div class="tabs flex flex-col justify-center pt-4">
            <div class="tabs-header px-4 w-full flex items-center justify-between border-b-2 border-blue-400">
                <div class="text-xl font-medium">
                    เพิ่มแบบฝึกหัด/ภาระงาน ใน{{ props.assignmentNameTh }}นี้
                </div>
            </div>
        </div> -->
        
        <div class="tabs-content w-full">
            <div class="">
                <form @submit.prevent="submitFormHandler" class="m-4 space-y-2" v-if="showCreateNewAssignmentForm">
                    <!-- Component: Rounded base size basic textarea -->
                    <div class="relative">
                        <textarea id="id-01" type="text" name="id-01" v-model="form.title" placeholder="Write your message" rows="3" class="relative w-full px-4 py-2 text-sm placeholder-transparent transition-all border rounded outline-none focus-visible:outline-none peer border-slate-200 text-slate-500 autofill:bg-white invalid:border-pink-500 invalid:text-pink-500 focus:border-emerald-500 focus:outline-none invalid:focus:border-pink-500 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"></textarea>
                        <label for="id-01" class="cursor-text peer-focus:cursor-default absolute left-2 -top-2 z-[1] px-2 text-xs text-slate-400 transition-all before:absolute before:top-0 before:left-0 before:z-[-1] before:block before:h-full before:w-full before:bg-white before:transition-all peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-sm peer-required:after:text-pink-500 peer-required:after:content-['\00a0*'] peer-invalid:text-pink-500 peer-focus:-top-2 peer-focus:text-xs peer-focus:text-emerald-500 peer-invalid:peer-focus:text-pink-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400 peer-disabled:before:bg-transparent">
                           คำสั่ง/คำจี้แจง
                        </label>
                    </div>
                    <!-- End Rounded base size basic textarea -->
                    <div class="flex justify-between h-10 w-full">
                        <div class="flex justify-start">
                            <label for="assignment-point-input" class=" mr-4 mt-1 block text-lg font-medium text-gray-900 dark:text-white">คะแนน</label>
                            <button @click.prevent="form.points<=0? 0: form.points--;" class="border-violet-500 hover:bg-violet-600 active:bg-violet-700 dark:border-violet-400 flex items-center justify-center rounded-l-xl border-2 p-2 text-xl  transition duration-200 hover:cursor-pointer dark:bg-white/5 dark:text-white dark:hover:bg-white/10 dark:active:bg-white/20">
                                <Icon icon="heroicons-solid:minus-sm" class="hover:text-white hover:scale-150 dark:text-white" />
                            </button>
                            <input type="text" name="" disabled v-model="form.points" id="assignment-point-input" class="w-10 border-x-0 border-y-2 border-violet-500">
                            <button @click.prevent="form.points++" class="border-violet-500 hover:bg-violet-600 active:bg-violet-700 dark:border-violet-400 flex items-center justify-center rounded-r-xl border-2 p-2 text-xl  transition duration-200 hover:cursor-pointer dark:bg-white/5 dark:text-white dark:hover:bg-white/10 dark:active:bg-white/20">
                                <Icon icon="heroicons-solid:plus-sm" class=" hover:text-white hover:scale-150 dark:text-white" />
                            </button>
                        </div>
                        <div class=" ml-4" data-title="Insert Photo" >
                            <input type="file" accept="image/*" multiple ref="inputAsmImages" class="hidden" @change="onInputImageChange">
                            <Icon icon="heroicons:photo" class="w-9 h-9 hover:scale-110 bg-blue-200 hover:bg-blue-300 rounded-full p-2" @click.prevent="browseInputAsmImages" />
                        </div> 
                    </div>
                </form>
            </div>
            <div class="mx-4 columns-1">
              <div v-if="tempImages" class="">
                  <div v-for="(image,index) in tempImages" :key="image.url" class="">
                      <div class="relative mb-2 max-h-fit overflow-hidden">
                          <img :src="image.url" class="rounded-lg"/>
                          <button v-if="$page.props.isCourseAdmin" @click.prevent="deleteTempImage(index)"
                          class="absolute top-1 right-1 rounded-full cursor-pointer bg-gray-100 p-2">
                              <Icon icon="fa-solid:trash-alt" class="w-5 h-5 text-red-500" />
                          </button>
                      </div>
                  </div>
              </div>
          </div>
        </div>

        <div class="py-2 mt-2  rounded-b flex justify-center items-center">
              <div v-if="showCreateNewAssignmentForm" class="flex space-x-4">
                  <button @click.prevent="onCancleHandler" class="text-gray-600 bg-red-300 hover:bg-red-400 hover:text-white focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                      ยกเลิก
                  </button>
                  <button type="submit" @click.prevent="onSubmitFormHandler"  class="text-white bg-violet-600 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                      บันทึก
                  </button>
              </div>
              <div v-else >
                  <button type="button" @click.prevent="showCreateNewAssignmentForm=true" class="text-white bg-violet-600 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                      เพิ่มแบบฝึกหัด/ภาระงานใหม่
                  </button>
              </div>
          </div>
    </div>
</template>

