<script setup>
import { ref, defineProps, defineEmits } from 'vue'
import { Dialog, DialogPanel, DialogTitle } from '@headlessui/vue'

const props = defineProps({
    studentData: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['close', 'update'])

const formData = ref({
    prefix_name: props.studentData.prefix_name || '',
    first_name: props.studentData.first_name || '',
    last_name: props.studentData.last_name || '',
    // ...existing form fields...
})

const isSubmitting = ref(false)

const handleSubmit = async () => {
    isSubmitting.value = true
    try {
        // Add your API call here to update student data
        await updateStudent(formData.value)
        emit('update', formData.value)
        closeModal()
    } catch (error) {
        console.error('Error updating student:', error)
    } finally {
        isSubmitting.value = false
    }
}

const closeModal = () => {
    emit('close')
}

</script>

<template>
    <Dialog as="div" class="relative z-10" @close="closeModal">
        <DialogPanel class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
            <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                แก้ไขข้อมูลนักเรียน
            </DialogTitle>

            <!-- Form Content -->
            <form @submit.prevent="handleSubmit" class="mt-4 space-y-4">
                <!-- Prefix Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">คำนำหน้าชื่อ</label>
                    <input 
                        type="text"
                        v-model="formData.prefix_name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>

                <!-- First Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">ชื่อ</label>
                    <input 
                        type="text"
                        v-model="formData.first_name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>

                <!-- Last Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">นามสกุล</label>
                    <input 
                        type="text"
                        v-model="formData.last_name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>

                <!-- ...existing code for other fields... -->

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end space-x-3">
                    <button
                        type="button"
                        @click="closeModal"
                        class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        ยกเลิก
                    </button>
                    <button
                        type="submit"
                        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                        :disabled="isSubmitting"
                    >
                        {{ isSubmitting ? 'กำลังบันทึก...' : 'บันทึก' }}
                    </button>
                </div>
            </form>
        </DialogPanel>
    </Dialog>
</template>