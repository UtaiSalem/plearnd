<script setup lang="ts">
import { ref } from 'vue';
import { Icon } from '@iconify/vue';
import axios from 'axios';
const props = defineProps({
    member_id: Number,
    course_id: Number,
    notes_comments: String,
})

const emit = defineEmits(['update:notesComments']);

// const localNotesComments = ref(props.notesComments);
const isLoading = ref(false);
const form = ref({
    notes_comments: props.notes_comments,
})

const handleNotesCommentsInput = () => {
    // console.log(form.value.notes_comments);
    emit('update:notesComments', form.value.notes_comments);
}

async function handleNotesCommentsFormSubmit () {
    try {
        isLoading.value = true;
        const response = await axios.patch(`/courses/${props.course_id}/members/${props.member_id}/notes-comments`, form.value);
        if (response.data &&response.data.success) {
            emit('update:notesComments', form.value.notes_comments);
            console.log(response.data.success);
            console.log(response.data.notes_comments);
            isLoading.value = false;
        }
        
    } catch (error) {
        console.log(error);
        isLoading.value = false;
    }
}
</script>

<template>
    <div>

        <!-- Modal toggle -->
        <button :data-modal-target="`crud-modal-${props.member_id}}-notes-comments`" :data-modal-toggle="`crud-modal-${props.member_id}}-notes-comments`"
            class="block focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            :class="{ 'text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-800': props.notes_comments }"
            type="button">
            <Icon icon="fa-regular:edit" class="w-4 h-4"></Icon>
        </button>

        <!-- Main modal -->
        <div :id="`crud-modal-${props.member_id}}-notes-comments`" tabindex="-1" aria-hidden="true"
            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            บันทึกย่อ
                        </h3>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            :data-modal-toggle="`crud-modal-${props.member_id}}-notes-comments`">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <form class="p-4 md:p-5" @submit.prevent="handleNotesCommentsFormSubmit">
                        <div class="grid gap-4 mb-4 grid-cols-2">
                            <div class="col-span-2">
                                <textarea :id="`notes-comments-form-input-${props.member_id}`" rows="3" v-model="form.notes_comments" @input="handleNotesCommentsInput"
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                ></textarea>
                            </div>
                        </div>
                        <button type="submit" :disabled="isLoading"
                            class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            :data-modal-toggle="`crud-modal-${props.member_id}}-notes-comments`"
                            >
                            <Icon icon="icomoon-free:spinner" class="w-5 h-5 animate-spin" v-if="isLoading" />
                            <Icon icon="fa-regular:save" class="w-4 h-4 mx-1" v-else />
                            บันทึก
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
