<script setup>
import { ref,reactive } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import Swal from 'sweetalert2';
import { useObjectUrl } from '@vueuse/core';

import TextInput from '@/Components/TextInput.vue';
import CreateNewButton from '@/PlearndComponents/accessories/CreateNewButton.vue'
import BlueVioletButton from '@/PlearndComponents/accessories/BlueVioletButton.vue';

const pageData = usePage().props;
const openCreateNewLessonModal = ref(false);
const lessonsLength = ref(pageData.lessons.data.length + 1);

// const inputLesonImages = ref(null);
const tempImages = reactive([]);
const isDragActive = ref(false);

const emit = defineEmits(['add-new-lesson']);

const form = ref({
  userID: pageData.auth.user.id,
  courseID: pageData.course.data.id,
  title: '',
  description: '',
  content: '',
  youtube_url: '',
  status: 1
});

// const browseInputLessonImages = () => inputLessonImages.value.click();
// function onInputImageChange(e){
//     Array.from(e.target.files).forEach(image => {
//         tempImages.push({ file: image, url: useObjectUrl(image)});
//     });
// }

async function handleCreateLessonFormSubmit(){
  try {
      if (form.value.title.trim().length>0 || form.value.description.trim().length>0) {

      const config = { headers: { 'Content-Type': 'multipart/form-data' } };
      let newLessonForm = new FormData();
      newLessonForm.append('courseId', form.value.courseID);
      newLessonForm.append('title', form.value.title);
      newLessonForm.append('description', form.value.description);
      newLessonForm.append('content', form.value.content);
      // newLessonForm.append('video_url', form.value.video_url);
      newLessonForm.append('youtube_url', form.value.youtube_url);
      newLessonForm.append('status', form.value.status);

      Array.from(tempImages).forEach((image)=>{
        newLessonForm.append('images[]', image.file);
      });

      let newLessonResp = await axios.post(`/api/courses/${pageData.course.data.id}/lessons`, newLessonForm, config);

      if(newLessonResp.data.success){

        emit('add-new-lesson', newLessonResp.data.newLesson);

        form.value.title       = '';
        form.value.description = '';
        form.value.content     = '';
        form.value.youtube_url = '';
        form.value.status      = 1;
        tempImages.splice(0);
        Swal.fire(
            'เสร็จสมบูรณ์',
            'เพิ่มบทเรียนใหม่เสร็จเรียบร้อย',
            'success'
        )
      }
    }
  } catch (error) {
    console.log(error);
    Swal.fire(
        'ล้มเหลว',
        'เกิดข้อผิดพลาด, <br />' + 'กรุณาลองใหม่อีกครั้ง',
        'error'
    )
  }
  openCreateNewLessonModal.value = false;
}

const toggleActive = () => isDragActive.value = !isDragActive.value;
const deleteTempImage = (index) => tempImages.splice(index,1);

function handleDrop(e){
  isDragActive.value = false;
  Array.from(e.dataTransfer.files).forEach(image => {
    tempImages.push({ file: image, url: useObjectUrl(image)});
  });
  // console.log(e.dataTransfer.files);
}

function handleChange(e){
  isDragActive.value = false;
  Array.from(e.target.files).forEach(image => {
    tempImages.push({ file: image, url: useObjectUrl(image)});
    // tempImages.push({ file: image, url: URL.createObjectURL(image)});
  });
}

</script>

<template>
    <!-- <div v-if="openCreateNewLessonModal" class="fixed inset-0 w-full h-screen flex items-center justify-center z-30 bg-gray-600 bg-opacity-75"> -->

    <div v-if="openCreateNewLessonModal" class="w-full bg-white rounded-xl shadow-xl z-40">
      <div class="flex w-full items-center justify-between border-b-4 border-blue-500 mt-4 px-4 py-2">
        <div class="text-2xl">สร้างบทเรียนใหม่</div>
      </div>
      <div class="m-4">
      
        <h5 class="text-base font-semibold text-gray-500 dark:text-white ">
            ชื่อวิชา
        </h5>
        <TextInput id="course_name" type="text" disabled class="w-full p-2 mb-2 bg-gray-200" v-model="$page.props.course.data.name"/>
        <h5 class="text-base font-semibold text-gray-500 dark:text-white ">
            รหัสวิชา
        </h5>
        <TextInput id="course_code" type="text" disabled class="w-full p-2 mb-2 bg-gray-200" v-model="$page.props.course.data.code"/>

        <form @submit.prevent="handleCreateLessonFormSubmit" class="my-4 pb-4 space-y-2" enctype="multipart/form-data">
          <h5 class="text-base font-semibold text-gray-500 dark:text-white ">
              ชื่อบทเรียน
          </h5>
          <TextInput id="lesson_name" type="text" class="mt-1 block w-full text-center" v-model="form.title" required />

          <label for="lesson_description" class="block text-sm font-semibold text-gray-600 dark:text-white">คำอธิบาย</label>
          <textarea v-model="form.description" id="lesson_description" rows="4"
            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            :placeholder="form.description"></textarea>

          <label for="lesson_content" class="block text-sm font-semibold text-gray-600 dark:text-white">เนื้อหา-รายละเอียด</label>
          <textarea v-model="form.content" id="lesson_content" rows="10"
            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            :placeholder="form.content"></textarea>

          <div class="">
            <h5 class="text-sm font-semibold text-gray-500 dark:text-white ">
                รูปภาพ
            </h5>
            <div v-if="tempImages" class="">
              <div v-for="(image,index) in tempImages" :key="image.url" class="">
                <div class="relative mb-2 max-h-fit overflow-hidden">
                  <img :src="image.url" class="rounded-lg" />
                  <button v-if="$page.props.isCourseAdmin" @click.prevent="deleteTempImage(index)"
                    class="absolute top-1 right-2 flex items-center justify-around w-8 rounded-full cursor-pointer bg-gray-200 p-2">
                    <Icon icon="fa-solid:trash-alt" class=" w-4 h-4 text-red-500" />
                  </button>
                </div>
              </div>
            </div>
            
            <div id="dropzone" class="w-full relative border-2 border-gray-300 border-dashed rounded-lg p-6"
              :class="{ 'bg-blue-100 border-blue-300 dark:bg-green-700': isDragActive }"
              @dragenter.prevent="toggleActive" 
              @dragleave.prevent="toggleActive" 
              @dragover.prevent=""
              @drop.prevent="handleDrop">

              <div class="text-center">
                <!-- <img class="mx-auto h-12 w-12" src="https://www.svgrepo.com/show/357902/image-upload.svg" alt=""> -->
                <Icon icon="uil:image-upload" class="mx-auto h-12 w-12 text-gray-600" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">
                  <label for="files-upload" class="relative cursor-pointer">
                    <span>Drag and drop</span>
                    <span class="text-indigo-600"> or browse</span>
                    <span> to upload</span>
                  </label>
                  <input id="files-upload" name="lesson_images" @change="handleChange" accept="image/*" type="file" :max-file-size="300000" multiple class="sr-only">
                </h3>
                <p class="mt-1 text-xs text-gray-500">
                  PNG, JPG, GIF up to 4MB
                </p>
              </div>

            </div>
          </div>

          <!-- <div>
            <label class="block text-base font-semibold text-text-gray-700">
              ลิงค์วีดิโอ
            </label>
            <input type="text" v-model="form.video_url" id="">
          </div> -->
          <!-- <div> -->
            <label for="lesson_youtube_link" class="block  text-sm font-semibold text-gray-600 dark:text-white">ลิงค์ยูทูป</label>
            <TextInput id="lesson_youtube_link" type="text" v-model="form.youtube_url" class="mt-1 block w-full" />
            <!-- <TextInput id="lesson_name" type="text" class="mt-1 block w-full text-center" v-model="form.title" required
            autocomplete="lesson_name" /> -->
          <!-- </div> -->

          <!-- Component: Secondary checkboxes -->
          <div class="flex items-center gap-4">
            <h4 class="text-base font-semibold text-gray-500 dark:text-white ">รูปภาพ</h4>
            <div class="relative flex items-center">
              <input class="w-4 h-4 transition-colors bg-white border-2 rounded-full appearance-none cursor-pointer focus-visible:outline-none peer border-slate-500 checked:border-violet-500 checked:bg-violet-200 checked:hover:border-violet-600 checked:hover:bg-violet-300 focus:outline-none checked:focus:border-violet-700 checked:focus:bg-violet-400 disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-50" type="radio" :value="1" id="huey2" v-model="form.status"  />
              <label class="pl-2 cursor-pointer text-slate-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400" for="huey2">
                เผยแพร่
              </label>
              <svg class="absolute left-0 w-4 h-4 transition-all duration-300 scale-50 opacity-0 pointer-events-none fill-violet-500 peer-checked:scale-100 peer-checked:opacity-100 peer-hover:fill-violet-600 peer-focus:fill-violet-700 peer-disabled:cursor-not-allowed" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-labelledby="title-01 description-01" role="graphics-symbol">
                <circle cx="8" cy="8" r="4" />
              </svg>
            </div>
            <div class="relative flex items-center">
              <input class="w-4 h-4 transition-colors bg-white border-2 rounded-full appearance-none cursor-pointer focus-visible:outline-none peer border-slate-500 checked:border-violet-500 checked:bg-violet-200 checked:hover:border-violet-600 checked:hover:bg-violet-300 focus:outline-none checked:focus:border-violet-700 checked:focus:bg-violet-400 disabled:cursor-not-allowed disabled:border-slate-100 disabled:bg-slate-50" type="radio" :value="0" id="dewey2" v-model="form.status" />
              <label class="pl-2 cursor-pointer text-slate-500 peer-disabled:cursor-not-allowed peer-disabled:text-slate-400" for="dewey2">
                ร่าง
              </label>
              <svg class="absolute left-0 w-4 h-4 transition-all duration-300 scale-50 opacity-0 pointer-events-none fill-violet-500 peer-checked:scale-100 peer-checked:opacity-100 peer-hover:fill-violet-600 peer-focus:fill-violet-700 peer-disabled:cursor-not-allowed" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-labelledby="title-02 description-02" role="graphics-symbol">
                <circle cx="8" cy="8" r="4" />
              </svg>
            </div>
          </div>
          <!-- End Secondary checkboxes -->


          <div class="flex items-center w-full">
            <label for="point_tuition_fees_input" class="block text-base font-semibold text-gray-700 w-[50%]">
              แต้มค่าธรรมเนียมการเรียนรู้
            </label>
            <input type="number" id="point_tuition_fees_input" aria-describedby="helper-text-explanation"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-[50%] p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
              placeholder="0">

          </div>

          <div class="flex items-center justify-end space-x-4 ">
            <div class="">
              <button @click.prevent="openCreateNewLessonModal = false"
                class=" px-6 py-2 text-primary bg-gray-300 hover:bg-gray-400 hover:text-white hover:scale-105  rounded-lg">
                ยกเลิก
              </button>
            </div>
            <div>
              <BlueVioletButton type="submit" class="hover:scale-105">บันทึก</BlueVioletButton>
            </div>
          </div>
        </form>
      </div>
    </div>


    <div class="plearnd-card w-full" v-if="!openCreateNewLessonModal">
      <div class="flex justify-center">
          <CreateNewButton  @click.prevent="() => openCreateNewLessonModal=true">เพิ่มบทเรียนใหม่</CreateNewButton>
      </div>
    </div>

</template>
