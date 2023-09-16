<script setup>
import { ref,reactive } from 'vue'
import { useObjectUrl } from '@vueuse/core'
// import { shallowRef } from 'vue'
import { usePage } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue'
import { Icon } from '@iconify/vue';
import Swal from 'sweetalert2'

const showCreateNewTopicForm =ref(false);
const inputImages = ref(null);
const topicImages = reactive([]);
const tempImages = reactive([]);

const emit = defineEmits(['add-new-topic']);

const form = ref({
  course_id: usePage().props.course.data.id,
  lesson_id: usePage().props.lesson.data.id,
  title: '',
  content: '',
});

async function submitFormHandler(){
  try {
    const config = { headers: { 'Content-Type': 'multipart/form-data' } };
    let newTopicForm = new FormData();
    newTopicForm.append('course_id', form.value.course_id);
    newTopicForm.append('lesson_id', form.value.lesson_id);
    newTopicForm.append('title', form.value.title);
    newTopicForm.append('content', form.value.content);

    Array.from(tempImages).forEach((image)=>{
      newTopicForm.append('images[]', image.file);
    });

    let topicResp = await axios.post('/topics', newTopicForm, config);
    if (topicResp.status===200) {
      // console.log(topicResp.data);
      usePage().props.topics.data.push(topicResp.data.newTopic); 
      // topicResp.data.images.forEach(el => {
      //       topicImages.push(el);
      // });
      tempImages.splice(0);
      Swal.fire(
        'เรียบร้อย',
        'เพิ่มหัวข้อใหม่เสร็จสมบูรณ์',
        'success'
      )
    }  
    form.value.title = '';
    form.value.content = '';

    showCreateNewTopicForm.value = false;
  } catch (err) {
    console.error(err);
  }
}

const browseInputImages = () => { inputImages.value.click() };
function onInputImageChange(e){
    Array.from(e.target.files).forEach(image => {
        tempImages.push({ file: image, url: useObjectUrl(image)});
    });
}
function onCancleHandler(){
    form.value.title = '';
    form.value.content = '';
    inputImages.files = [];
    tempImages.splice(0);
    showCreateNewTopicForm.value = false;
}

function deleteTempImage(index){ tempImages.splice(index,1); }

</script>
<template>
  <div class="bg-white border-t-4 border-blue-600 rounded-lg overflow-hidden shadow-lg">
    <div class="tabs flex flex-col justify-center pt-4">
      <div class="tabs-header px-4 w-full flex items-center justify-between border-b-2 border-blue-400">
        <div class="text-xl font-medium">
          เพิ่มหัวข้อใหม่ในบทเรียน
        </div>
      </div>
      <div class="tabs-content w-full">
        <div class="pb-4">
          <form @submit.prevent="submitFormHandler" class="m-4 space-y-2" v-if="showCreateNewTopicForm">
            <!-- <InputLabel for="topic_title" value="ชื่อหัวข้อ" /> -->
            <label for="topic_title" class="block text-lg font-medium text-gray-900 dark:text-white">ชื่อหัวข้อ</label>
            <TextInput id="topic_title" v-model="form.title" type="text" class="mt-0 block w-full text-start" required autofocus autocomplete="topic_title" />

            <label for="topic_content" class="block text-lg font-medium text-gray-900 dark:text-white">เนื้อหา</label>
            <textarea id="topic_content" v-model="form.content" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder=""></textarea>

          </form>
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
          <div class=" ml-4" data-title="Insert Photo" v-if="showCreateNewTopicForm" >
              <input type="file" accept="image/*" multiple ref="inputImages" class="hidden" @change="onInputImageChange">
              <Icon icon="heroicons:photo" class="w-9 h-9 hover:scale-110 bg-blue-200 hover:bg-blue-300 rounded-full p-2" @click.prevent="browseInputImages" />
          </div>  
          <div class="pt-2 mt-2  rounded-b flex justify-center items-center">
              <div v-if="showCreateNewTopicForm" class="flex space-x-4">
                  <button @click.prevent="onCancleHandler" class="text-gray-600 bg-red-300 hover:bg-red-400 hover:text-white focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                      ยกเลิก
                  </button>
                  <button type="submit" @click.prevent="submitFormHandler"  class="text-white bg-violet-600 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                      บันทึก
                  </button>
              </div>
              <div v-else >
                  <button type="button" @click.prevent="showCreateNewTopicForm=true" class="text-white bg-violet-600 hover:bg-violet-700 focus:ring-4 focus:ring-violet-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                      เพิ่มหัวข้อใหม่
                  </button>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

