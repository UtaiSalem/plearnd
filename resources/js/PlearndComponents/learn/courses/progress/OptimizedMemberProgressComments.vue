<script setup>
import { ref, watch, nextTick } from "vue";

const props = defineProps({
  member_id: Number,
  course_id: Number,
  notes_comments: String,
});

const emit = defineEmits(['update:notes-comments']);

// Local state to avoid unnecessary re-renders
const localNotesComments = ref(props.notes_comments || '');
const isEditing = ref(false);
const isLoading = ref(false);
const commentsTextarea = ref(null);

// Watch for changes in props
watch(() => props.notes_comments, (newValue) => {
  localNotesComments.value = newValue || '';
});

// Handle edit mode toggle
const toggleEditMode = () => {
  isEditing.value = !isEditing.value;
  
  // Focus on textarea when entering edit mode
  if (isEditing.value) {
    nextTick(() => {
      if (commentsTextarea.value) {
        commentsTextarea.value.focus();
      }
    });
  }
};

// Handle save comments
const saveComments = async () => {
  if (isLoading.value) return;
  
  isLoading.value = true;
  try {
    let resp = await axios.patch(`/courses/${props.course_id}/members/${props.member_id}/notes_comments`, { 
      notes_comments: localNotesComments.value 
    });
    
    if (resp.data && resp.data.success) {
      emit('update:notes-comments', localNotesComments.value);
      isEditing.value = false;
    }
  } catch (error) {
    console.error('Error updating notes comments:', error);
    // Revert to original value on error
    localNotesComments.value = props.notes_comments || '';
  } finally {
    isLoading.value = false;
  }
};

// Handle cancel edit
const cancelEdit = () => {
  localNotesComments.value = props.notes_comments || '';
  isEditing.value = false;
};

// Handle keydown events
const handleKeydown = (event) => {
  if (event.key === 'Escape') {
    cancelEdit();
  } else if (event.key === 'Enter' && event.ctrlKey) {
    saveComments();
  }
};
</script>

<template>
  <div class="relative">
    <!-- Display mode -->
    <div v-if="!isEditing" 
         @click="toggleEditMode" 
         class="px-2 py-1 text-sm text-gray-700 cursor-pointer hover:bg-gray-100 rounded transition-colors"
         :title="localNotesComments || 'คลิกเพื่อเพิ่มหมายเหตุ'">
      {{ localNotesComments || '-' }}
    </div>
    
    <!-- Edit mode -->
    <div v-else class="flex flex-col space-y-2">
      <textarea
        ref="commentsTextarea"
        v-model="localNotesComments"
        @keydown="handleKeydown"
        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
        rows="2"
        placeholder="เพิ่มหมายเหตุ..."
      ></textarea>
      
      <div class="flex justify-end space-x-2">
        <button
          @click="cancelEdit"
          class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors"
        >
          ยกเลิก
        </button>
        
        <button
          @click="saveComments"
          :disabled="isLoading"
          class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center space-x-1"
        >
          <svg v-if="isLoading" class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V4a10 10 0 00-10 10h2zm2 8a8 8 0 018-8h2a10 10 0 00-10-10v2zm8 2a8 8 0 01-8-8h-2a10 10 0 0010 10v-2zm-8-2a8 8 0 01-8 8V20a10 10 0 0010-10h-2z"></path>
          </svg>
          <span>บันทึก</span>
        </button>
      </div>
    </div>
  </div>
</template>