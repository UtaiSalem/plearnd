# CourseProfileCover Standalone Component Documentation

## Overview

This document describes the refactoring of the `CourseProfileCover.vue` component to work independently without Pineer/Backend integration. The standalone version allows for complete testing and development of all component functionality before connecting to the backend.

## Files Created

### 1. `CourseProfileCoverStandalone.vue`
- **Location**: `resources/js/PlearndComponents/learn/courses/CourseProfileCoverStandalone.vue`
- **Purpose**: Standalone version of the original component with local state management
- **Key Features**:
  - Complete UI functionality without backend dependencies
  - Mock data and local state management
  - Simulated API operations with loading states
  - Built-in notification system

### 2. `CourseProfileCoverTest.vue`
- **Location**: `resources/js/Pages/Test/CourseProfileCoverTest.vue`
- **Purpose**: Test page to demonstrate and validate standalone functionality
- **Key Features**:
  - Interactive testing controls
  - Role switching (Admin/Student)
  - Membership status toggling
  - Real-time status display

## Key Changes and Refactoring

### 1. State Management Replacement

**Original (Pinia Store)**:
```javascript
const courseProfileStore = useCourseProfileStore();
const showOptionGroups = computed(() => courseProfileStore.getShowOptionGroups());
const isUpdatingCover = computed(() => courseProfileStore.isLoading(`update_cover_${courseId.value}`));
```

**Standalone (Local State)**:
```javascript
// Local UI States
const showOptionGroups = ref(false);
const loadingStates = ref({
    update_cover: false,
    update_logo: false,
    // ... other loading states
});

// Local computed properties
const isUpdatingCover = computed(() => loadingStates.value.update_cover);
```

### 2. Mock Data Implementation

**Course Groups**:
```javascript
const mockCourseGroups = ref([
    { id: 1, name: 'กลุ่มเรียน A', image: null, members_count: 25 },
    { id: 2, name: 'กลุ่มเรียน B', image: null, members_count: 30 },
    { id: 3, name: 'กลุ่มเรียน C', image: null, members_count: 15 }
]);
```

**Fallback to window.page props**:
```javascript
const courseGroups = computed(() => {
    if (typeof window !== 'undefined' && window.page?.props?.courseGroups) {
        return window.page.props.courseGroups;
    }
    return mockCourseGroups.value;
});
```

### 3. Mock API Operations

**Image Upload Simulation**:
```javascript
async function onCoverInputChange(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Create temporary preview URL
    const tempUrl = URL.createObjectURL(file);
    tempImages.value[courseId.value]['cover'] = tempUrl;
    
    try {
        loadingStates.value.update_cover = true;
        // Simulate API delay
        await new Promise(resolve => setTimeout(resolve, 1500));
        
        // Mock success
        showNotification('สำเร็จ', 'อัปเดตรูปปกเรียบร้อยแล้ว', 'success');
        // Cleanup...
    } catch (error) {
        // Error handling...
    } finally {
        loadingStates.value.update_cover = false;
    }
}
```

**Membership Request Simulation**:
```javascript
async function onRequestToBeMember(groupId, groupIndx) {
    if (isRequestingMember.value) return;
    
    try {
        loadingStates.value.request_member = true;
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        // Mock points validation
        const mockUserPoints = 1000;
        const mockTuitionFees = props.modelData?.tuition_fees || 500;
        
        if (mockUserPoints < mockTuitionFees) {
            showNotification('ขออภัย', 'คุณมีแต้มสะสมไม่เพียงพอ...', 'warning');
            return;
        }
        
        // Mock success
        showNotification('เสร็จสิ้น', 'ขอเป็นสมาชิกเรียบร้อยแล้ว', 'success');
    } catch (error) {
        // Error handling...
    } finally {
        loadingStates.value.request_member = false;
    }
}
```

### 4. Notification System

**Built-in Notification System**:
```javascript
const showNotification = (title, message, type = 'info') => {
    console.log(`[${type.toUpperCase()}] ${title}: ${message}`);
    
    if (typeof document !== 'undefined') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="font-semibold">${title}</div>
            <div class="text-sm">${message}</div>
        `;
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }
};
```

## Functionality Testing

### 1. Admin Functions
- ✅ Cover image upload with preview
- ✅ Logo image upload with preview
- ✅ Header editing (inline)
- ✅ Subheader editing (inline)
- ✅ Save/Cancel operations
- ✅ Loading states during operations

### 2. Student Functions
- ✅ Membership request buttons
- ✅ Group selection dropdown
- ✅ Points validation simulation
- ✅ Pending membership status
- ✅ Active membership status
- ✅ Cancel membership functionality
- ✅ Confirmation dialogs

### 3. UI/UX Features
- ✅ Responsive design (mobile/desktop)
- ✅ Dropdown animations
- ✅ Loading spinners
- ✅ Error handling
- ✅ Success notifications
- ✅ Click outside to close dropdowns
- ✅ Escape key to close dropdowns

## Testing Instructions

### 1. Setup
1. Navigate to the test page: `/test/course-profile-cover`
2. Open browser developer tools to see console logs
3. Ensure you have test images ready for upload testing

### 2. Admin Testing
1. Click "Toggle User Role" to ensure you're in Admin mode
2. Test cover image upload:
   - Click the camera icon in the top-left corner
   - Select an image file
   - Verify preview appears
   - Wait for success notification
3. Test logo upload:
   - Click the camera icon on the course logo
   - Select an image file
   - Verify preview appears
   - Wait for success notification
4. Test header editing:
   - Click the pencil icon next to the course name
   - Edit the text in the input field
   - Click save (green checkmark) or cancel (red X)
   - Verify success notification or cancellation

### 3. Student Testing
1. Click "Toggle User Role" to switch to Student mode
2. Test membership request:
   - Click "ขอเป็นสมาชิก" button
   - If groups exist, select a group from dropdown
   - Wait for success notification
3. Test membership statuses:
   - Use "Toggle Membership Status" button
   - Test pending status (รอการตอบรับ)
   - Test active status (ออกจากสมาชิก)
   - Test cancel membership functionality

## Integration with Pineer (Future Steps)

### 1. Preparation
The standalone component is designed to make integration with Pineer straightforward:

**Current Mock Functions**:
```javascript
// Mock implementation
async function onCoverInputChange(event) {
    // Mock API call
    await new Promise(resolve => setTimeout(resolve, 1500));
    showNotification('สำเร็จ', 'อัปเดตรูปปกเรียบร้อยแล้ว', 'success');
}
```

**Future Pineer Integration**:
```javascript
// Real implementation with Pineer
async function onCoverInputChange(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    try {
        loadingStates.value.update_cover = true;
        
        // Replace with actual Pineer API call
        const result = await pineerAPI.uploadCourseCover(courseId.value, file);
        
        // Handle success
        showNotification('สำเร็จ', 'อัปเดตรูปปกเรียบร้อยแล้ว', 'success');
        
        // Update state with real data
        // ... update component state
        
    } catch (error) {
        showNotification('ข้อผิดพลาด', error.message, 'error');
    } finally {
        loadingStates.value.update_cover = false;
    }
}
```

### 2. Integration Strategy

1. **Replace Mock Functions**: Gradually replace mock functions with actual Pineer API calls
2. **Maintain Local State**: Keep local state management for UI states
3. **Error Handling**: Enhance error handling with real API error responses
4. **Data Synchronization**: Implement proper data synchronization between local state and backend
5. **Testing**: Use the standalone version for testing during integration

### 3. Benefits of This Approach

1. **Parallel Development**: Frontend can be developed and tested independently
2. **Faster Iteration**: No backend dependency during UI development
3. **Better Testing**: All edge cases can be tested with mock data
4. **Smoother Integration**: Clear separation between UI logic and API logic
5. **Reduced Risk**: UI is proven to work before backend integration

## Migration Path

### Phase 1: Standalone Development ✅
- [x] Create standalone component
- [x] Implement all UI functionality
- [x] Add comprehensive testing
- [x] Document all features

### Phase 2: Pineer Integration (Future)
- [ ] Replace mock API calls with Pineer integration
- [ ] Implement real error handling
- [ ] Add data synchronization
- [ ] Test with real backend
- [ ] Deploy integrated version

### Phase 3: Optimization (Future)
- [ ] Performance optimization
- [ ] Enhanced error handling
- [ ] Additional features
- [ ] Production deployment

## Conclusion

The standalone `CourseProfileCoverStandalone.vue` component successfully addresses the requirement to develop and test all functionality within the component directly before connecting to Pineer. This approach ensures:

1. **Complete Functionality**: All features work independently
2. **Proper Testing**: Comprehensive testing without backend dependencies
3. **Clear Integration Path**: Well-defined strategy for future Pineer integration
4. **Maintainable Code**: Clean separation between UI logic and API logic

The component is now ready for thorough testing and can be easily integrated with Pineer when the backend is ready.