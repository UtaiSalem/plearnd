<script setup>
import { ref, computed, reactive } from 'vue'
import { Icon } from '@iconify/vue';
import { usePage } from '@inertiajs/vue3';
import AssignmentImagesViewer from '../AssignmentImagesViewer.vue';
import DotsDropdownMenu from '@/PlearndComponents/accessories/DotsDropdownMenu.vue';
import PointsManager from '@/PlearndComponents/learn/courses/assignments/answers/PointsManager.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    assignment: Object,
    answer: Object,
    index: Number,
    isDeleting: Boolean,
});
const emit = defineEmits(['delete-answer','update-answer', 'edit-answer']);

const isLated = computed(()=> {
    return props.answer.created_at > props.assignment.end_date
});

const isEditing = ref(false);
const isSaving = ref(false);
const editForm = reactive({
    content: '',
    existing_images: [],
    course_id: usePage().props.course.data.id,
});
const tempNewImages = reactive([]);
const tempNewImagesUrl = reactive([]);
const asmAnsImagesInput = ref(null);

const canSubmit = computed(() => {
    return editForm.content.trim().length > 0 || 
           editForm.existing_images.length > 0 || 
           tempNewImages.length > 0;
});

function startEdit() {
    isEditing.value = true;
    editForm.content = props.answer.content || '';
    editForm.existing_images = props.answer.images.map(img => img.id);
    tempNewImages.splice(0);
    tempNewImagesUrl.splice(0);
}

function cancelEdit() {
    isEditing.value = false;
    editForm.content = '';
    editForm.existing_images = [];
    tempNewImages.splice(0);
    tempNewImagesUrl.splice(0);
}

function removeExistingImage(imageId) {
    const index = editForm.existing_images.indexOf(imageId);
    if (index > -1) {
        editForm.existing_images.splice(index, 1);
    }
}

const browseAsmAnsImages = () => asmAnsImagesInput.value.click();

const onAsmAnsImagesInputChange = (e) => { 
    Array.from(e.target.files).forEach((image)=> {
        tempNewImages.push(image);
        tempNewImagesUrl.push(URL.createObjectURL(image));
    });
};

function onDeleteTempImagesHandler(index) {
    tempNewImages.splice(index, 1);
    tempNewImagesUrl.splice(index, 1);
}

async function submitEdit() {
    if (!canSubmit.value) return;
    
    isSaving.value = true;
    try {
        const config = { headers: { 'Content-Type': 'multipart/form-data' } };
        let formData = new FormData();
        formData.append('content', editForm.content);
        formData.append('course_id', editForm.course_id);
        formData.append('_method', 'PUT');
        
        // Add existing images that should be kept
        editForm.existing_images.forEach((imageId) => {
            formData.append('existing_images[]', imageId);
        });
        
        // Add new images
        tempNewImages.forEach((image) => {
            formData.append('images[]', image);
        });
        
        const response = await axios.post(
            `/assignments/${props.assignment.id}/answers/${props.answer.id}`,
            formData,
            config
        );
        
        if (response && response.data.success) {
            Swal.fire(
                'แก้ไขสำเร็จ',
                'แก้ไขคำตอบเสร็จสมบูรณ์',
                'success'
            );
            
            // Update local data
            Object.assign(props.answer, response.data.answer);
            emit('edit-answer', response.data.answer);
            
            cancelEdit();
        }
    } catch (error) {
        console.error(error);
        Swal.fire(
            'เกิดข้อผิดพลาด',
            'ไม่สามารถแก้ไขคำตอบได้',
            'error'
        );
    } finally {
        isSaving.value = false;
    }
}

async function onSetPoints(points){
    try {
        emit('update-answer', points);
    } catch (error) {
        console.log(error);
    }
}

</script>
<template>
    <div v-if="answer.student.id === $page.props.auth.user.id || $page.props.isCourseAdmin"
        class="relative w-full rounded-xl transition-all duration-300 shadow-sm hover:shadow-md overflow-hidden"
        :class="[
            answer.points >= (assignment.points/2) 
                ? 'bg-gradient-to-br from-green-50 to-emerald-100 border border-green-200' 
                : answer.points 
                    ? 'bg-gradient-to-br from-red-50 to-rose-100 border border-red-200' 
                    : 'bg-gradient-to-br from-slate-50 to-gray-100 border border-gray-200'
        ]">
        <div class="flex gap-4 p-4">
            <!-- Avatar -->
            <div class="flex-shrink-0 hidden sm:block">
                <div class="relative">
                    <img :src="answer.student.avatar"
                        class="w-12 h-12 rounded-full object-cover ring-2 ring-white shadow-md" 
                        alt="">
                    <div v-if="answer.points >= (assignment.points/2)" 
                         class="absolute -bottom-1 -right-1 bg-green-500 rounded-full p-0.5">
                        <Icon icon="mdi:check" class="w-3 h-3 text-white" />
                    </div>
                </div>
            </div>

            <div class="flex-1 min-w-0">
                <!-- Header -->
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <h4 class="font-semibold text-gray-800">
                            {{ answer.member_name ?? answer.student.name }}
                        </h4>
                        <div class="flex items-center gap-1.5 text-xs">
                            <Icon icon="mdi:clock-outline" class="w-3.5 h-3.5" 
                                  :class="isLated ? 'text-red-500' : 'text-gray-400'" />
                            <span :class="isLated ? 'text-red-600 font-medium' : 'text-gray-500'">
                                {{ answer.created_at }}
                            </span>
                            <span v-if="isLated" class="px-1.5 py-0.5 bg-red-100 text-red-600 rounded text-xs font-medium">
                                ส่งช้า
                            </span>
                        </div>
                        <span v-if="answer.created_at !== answer.updated_at" 
                              class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 text-blue-600 rounded-full text-xs font-medium">
                            <Icon icon="mdi:pencil" class="w-3 h-3" />
                            แก้ไขแล้ว
                        </span>
                    </div>
                    <!-- Score Badge & Menu -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div v-if="answer.points !== null && answer.points !== undefined">
                            <div class="flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold shadow-sm"
                                 :class="answer.points >= (assignment.points/2) 
                                    ? 'bg-green-500 text-white' 
                                    : answer.points > 0 
                                        ? 'bg-orange-500 text-white' 
                                        : 'bg-gray-400 text-white'">
                                <Icon icon="mdi:star" class="w-3.5 h-3.5" />
                                <span>{{ answer.points }}/{{ assignment.points }}</span>
                            </div>
                        </div>
                        <DotsDropdownMenu 
                            v-if="($page.props.isCourseAdmin || (answer.student.id === $page.props.auth.user.id)) && !isEditing" 
                            @delete-model="emit('delete-answer')"
                            @edit-model="startEdit"
                        >
                            <template #editModel v-if="answer.student.id === $page.props.auth.user.id">
                                <span>แก้ไขคำตอบ</span>
                            </template>
                            <template #deleteModel>
                                <span>ลบคำตอบ</span>
                            </template>
                        </DotsDropdownMenu>
                    </div>
                </div>
            
                <!-- View Mode -->
                <div v-if="!isEditing">
                    <div v-if="answer.content" class="mb-3">
                        <div class="text-gray-700 text-sm bg-white/70 backdrop-blur-sm p-4 rounded-xl shadow-inner whitespace-pre-wrap leading-relaxed">
                            {{ answer.content }}
                        </div>
                    </div>
                    <div>
                        <AssignmentImagesViewer :images="answer.images" />
                    </div>
                </div>
        
                <!-- Edit Mode -->
                <div v-else class="mt-2 bg-white/80 backdrop-blur-sm rounded-xl p-4 border border-violet-200 shadow-sm">
                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-violet-100">
                        <Icon icon="mdi:pencil-box" class="w-5 h-5 text-violet-500" />
                        <span class="font-medium text-violet-700">แก้ไขคำตอบ</span>
                    </div>
                    <div class="relative w-full">
                        <Textarea 
                            :id="`edit-assignment-answer-content${answer.id}`"
                            type="text" 
                            rows="3" 
                            autoResize 
                            v-model="editForm.content" 
                            placeholder="แก้ไขคำตอบ..." 
                            class="w-full px-4 py-2 text-sm transition-all border rounded-lg outline-none focus-visible:outline-none border-slate-300 text-slate-700 bg-white focus:border-violet-500 focus:outline-none disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400"
                        />
                    </div>
                
                    <!-- Existing Images with delete option -->
                    <div v-if="answer.images && answer.images.length > 0" class="mt-3">
                        <p class="text-xs text-gray-600 mb-2 flex items-center gap-1">
                            <Icon icon="mdi:image-multiple" class="w-4 h-4" />
                            รูปภาพปัจจุบัน ({{ editForm.existing_images.length }}/{{ answer.images.length }})
                        </p>
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                            <div v-for="image in answer.images" :key="image.id" 
                                 class="relative group aspect-square rounded-xl overflow-hidden shadow-sm border-2 transition-all duration-200"
                                 :class="editForm.existing_images.includes(image.id) ? 'border-violet-300 hover:border-violet-400' : 'border-red-300'">
                                <img :src="`/storage/images/courses/assignments/answers/${image.filename}`" 
                                     class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105"
                                     :class="!editForm.existing_images.includes(image.id) && 'grayscale opacity-40'" />
                            
                                <!-- Overlay for images to be deleted -->
                                <div v-if="!editForm.existing_images.includes(image.id)" 
                                     class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center gap-1">
                                    <Icon icon="mdi:trash-can" class="w-6 h-6 text-white/80" />
                                    <span class="text-white text-xs font-medium">จะถูกลบ</span>
                                </div>
                            
                                <!-- Delete button -->
                                <button v-if="editForm.existing_images.includes(image.id)"
                                        @click.prevent="removeExistingImage(image.id)"
                                        class="absolute top-1.5 right-1.5 rounded-full cursor-pointer bg-red-500/90 p-1 w-6 h-6 flex items-center justify-center hover:bg-red-600 transition opacity-0 group-hover:opacity-100 shadow-md">
                                    <Icon icon="mdi:close" class="w-3.5 h-3.5 text-white" />
                                </button>
                            
                                <!-- Restore button -->
                                <button v-else
                                        @click.prevent="editForm.existing_images.push(image.id)"
                                        class="absolute top-1.5 right-1.5 rounded-full cursor-pointer bg-green-500/90 p-1 w-6 h-6 flex items-center justify-center hover:bg-green-600 transition shadow-md">
                                    <Icon icon="mdi:undo" class="w-3.5 h-3.5 text-white" />
                                </button>
                            </div>
                        </div>
                    </div>
                
                    <!-- New Images Preview -->
                    <div v-if="tempNewImagesUrl.length > 0" class="mt-3">
                        <p class="text-xs text-gray-600 mb-2 flex items-center gap-1">
                            <Icon icon="mdi:image-plus" class="w-4 h-4 text-green-600" />
                            <span class="text-green-600">รูปภาพใหม่ ({{ tempNewImagesUrl.length }})</span>
                        </p>
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                            <div v-for="(image, index) in tempNewImagesUrl" :key="index" 
                                 class="relative group aspect-square rounded-xl overflow-hidden shadow-sm border-2 border-green-300 hover:border-green-400 transition-all duration-200">
                                <img :src="image" class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105" />
                                <button @click.prevent="onDeleteTempImagesHandler(index)"
                                    class="absolute top-1.5 right-1.5 rounded-full cursor-pointer bg-red-500/90 p-1 w-6 h-6 flex items-center justify-center hover:bg-red-600 transition opacity-0 group-hover:opacity-100 shadow-md">
                                    <Icon icon="mdi:close" class="w-3.5 h-3.5 text-white" />
                                </button>
                                <!-- New badge -->
                                <div class="absolute bottom-1.5 left-1.5 bg-green-500/90 text-white text-xs px-1.5 py-0.5 rounded-md font-medium shadow-sm">
                                    ใหม่
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between mt-3">
                        <div class="flex gap-2">
                            <input type="file" accept="image/*" multiple ref="asmAnsImagesInput" class="hidden" @change="onAsmAnsImagesInputChange">
                            <button @click.prevent="browseAsmAnsImages" 
                                class="flex items-center gap-1.5 px-3 py-2 rounded-lg border-2 border-dashed border-violet-300 text-violet-600 hover:bg-violet-50 hover:border-violet-400 transition-all text-sm font-medium">
                                <Icon icon="mdi:image-plus" class="w-5 h-5" />
                                <span>เพิ่มรูปภาพ</span>
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <button @click="cancelEdit" 
                                :disabled="isSaving"
                                class="flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition-all text-sm font-medium disabled:opacity-50">
                                <Icon icon="mdi:close" class="w-4 h-4" />
                                <span>ยกเลิก</span>
                            </button>
                            <button @click="submitEdit" 
                                :disabled="!canSubmit || isSaving"
                                class="flex items-center gap-1.5 px-5 py-2 rounded-lg text-white transition-all text-sm font-medium shadow-md disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none"
                                :class="canSubmit && !isSaving ? 'bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 hover:shadow-lg' : 'bg-gray-400'">
                                <Icon v-if="isSaving" icon="mdi:loading" class="animate-spin w-4 h-4" />
                                <Icon v-else icon="mdi:check" class="w-4 h-4" />
                                <span>{{ isSaving ? 'กำลังบันทึก...' : 'บันทึกการแก้ไข' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            
                <!-- Points Manager for Admin -->
                <div v-if="$page.props.isCourseAdmin && !isEditing" class="mt-3 pt-3 border-t border-gray-200/50">
                    <PointsManager 
                        :assignment="props.assignment" 
                        :answer="props.answer"
                        @setPoints="(points)=>onSetPoints(points)" 
                    />
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <Teleport to="body">
            <div v-if="isDeleting || isSaving" 
                 class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50">
                <div class="bg-white rounded-2xl p-8 shadow-2xl flex flex-col items-center gap-4">
                    <div class="relative">
                        <div class="w-16 h-16 border-4 border-violet-200 rounded-full animate-pulse"></div>
                        <Icon icon="mdi:loading" class="absolute inset-0 m-auto animate-spin w-10 h-10 text-violet-500" />
                    </div>
                    <p class="text-lg font-medium text-gray-700">{{ isSaving ? 'กำลังบันทึก...' : 'กำลังลบ...' }}</p>
                    <p class="text-sm text-gray-500">กรุณารอสักครู่</p>
                </div>
            </div>
        </Teleport>
    </div>
</template>
