<script setup>
import { ref } from 'vue';
import CourseProfileCoverStandalone from '@/PlearndComponents/learn/courses/CourseProfileCoverStandalone.vue';

// Mock data for testing
const mockCourseData = ref({
    id: 1,
    name: 'วิชาคณิตศาสตร์พื้นฐาน',
    code: 'MATH101',
    cover_header: 'วิชาคณิตศาสตร์พื้นฐาน',
    cover_subheader: 'MATH101 - ปริญญาตรี ชั้นปีที่ 1',
    tuition_fees: 500,
    lessons_count: 12,
    enrolled_students: 45,
    member_status: null, // null = not member, '0' = pending, '1' = active
    isMember: false
});

// Mock course member data
const mockCourseMemberOfAuth = ref(null);

// Mock page props (simulating Inertia.js)
if (typeof window !== 'undefined') {
    window.page = {
        props: {
            isCourseAdmin: true, // Test as admin first
            courseGroups: [
                { id: 1, name: 'กลุ่มเรียน A', image: null, members_count: 25 },
                { id: 2, name: 'กลุ่มเรียน B', image: null, members_count: 30 },
                { id: 3, name: 'กลุ่มเรียน C', image: null, members_count: 15 }
            ],
            auth: {
                user: {
                    pp: 1000 // Mock user points
                }
            }
        }
    };
}

// Event handlers for testing
function onCoverImageChange(file) {
    console.log('Cover image changed:', file);
}

function onLogoImageChange(file) {
    console.log('Logo image changed:', file);
}

function onHeaderChange(header) {
    console.log('Header changed:', header);
    mockCourseData.value.cover_header = header;
}

function onSubheaderChange(subheader) {
    console.log('Subheader changed:', subheader);
    mockCourseData.value.cover_subheader = subheader;
}

function onRequestToBeMember(groupId, groupIndex) {
    console.log('Request to be member:', { groupId, groupIndex });
    // Simulate membership request
    mockCourseMemberOfAuth.value = {
        id: 1,
        course_member_status: 0 // pending
    };
    mockCourseData.value.member_status = '0';
    mockCourseData.value.isMember = false;
}

function onRequestToBeUnmember(memberId) {
    console.log('Request to unmember:', memberId);
    // Simulate unmembership
    mockCourseMemberOfAuth.value = null;
    mockCourseData.value.member_status = null;
    mockCourseData.value.isMember = false;
}

// Toggle between admin and student view for testing
function toggleUserRole() {
    if (window.page.props.isCourseAdmin) {
        window.page.props.isCourseAdmin = false;
        // Set as student with no membership
        mockCourseMemberOfAuth.value = null;
        mockCourseData.value.member_status = null;
        mockCourseData.value.isMember = false;
    } else {
        window.page.props.isCourseAdmin = true;
        mockCourseMemberOfAuth.value = null;
        mockCourseData.value.member_status = null;
        mockCourseData.value.isMember = false;
    }
}

// Toggle membership status for testing
function toggleMembershipStatus() {
    if (!mockCourseMemberOfAuth.value) {
        // Create pending membership
        mockCourseMemberOfAuth.value = {
            id: 1,
            course_member_status: 0
        };
        mockCourseData.value.member_status = '0';
        mockCourseData.value.isMember = false;
    } else if (mockCourseData.value.member_status === '0') {
        // Change to active membership
        mockCourseMemberOfAuth.value.course_member_status = 1;
        mockCourseData.value.member_status = '1';
        mockCourseData.value.isMember = true;
    } else {
        // Remove membership
        mockCourseMemberOfAuth.value = null;
        mockCourseData.value.member_status = null;
        mockCourseData.value.isMember = false;
    }
}
</script>

<template>
    <div class="min-h-screen bg-gray-100 p-8">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Course Profile Cover Standalone Test</h1>
            
            <!-- Test Controls -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">Test Controls</h2>
                <div class="flex flex-wrap gap-4">
                    <button 
                        @click="toggleUserRole"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                        Toggle User Role (Current: {{ window.page?.props?.isCourseAdmin ? 'Admin' : 'Student' }})
                    </button>
                    <button 
                        @click="toggleMembershipStatus"
                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                        Toggle Membership (Current: {{ 
                            mockCourseData.member_status === null ? 'Not Member' : 
                            mockCourseData.member_status === '0' ? 'Pending' : 'Active' 
                        }})
                    </button>
                </div>
                
                <div class="mt-4 text-sm text-gray-600">
                    <p><strong>Current Status:</strong></p>
                    <ul class="list-disc list-inside">
                        <li>User Role: {{ window.page?.props?.isCourseAdmin ? 'Admin' : 'Student' }}</li>
                        <li>Membership: {{ 
                            mockCourseData.member_status === null ? 'Not Member' : 
                            mockCourseData.member_status === '0' ? 'Pending Member' : 'Active Member' 
                        }}</li>
                        <li>Course: {{ mockCourseData.name }}</li>
                        <li>Tuition Fees: {{ mockCourseData.tuition_fees }} points</li>
                    </ul>
                </div>
            </div>

            <!-- Component Test -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Component Test</h2>
                <CourseProfileCoverStandalone
                    :cover-image="'/images/default-cover.png'"
                    :logo-image="'/images/default-logo.png'"
                    :cover-header="mockCourseData.cover_header"
                    :cover-subheader="mockCourseData.cover_subheader"
                    model="course"
                    model-table="courses"
                    :modelable-id="mockCourseData.id"
                    modelable-type="course"
                    modelable-route="/courses/1"
                    :model-data="mockCourseData"
                    sub-model-name-th="บทเรียน"
                    :course-member-of-auth="mockCourseMemberOfAuth"
                    @cover-image-change="onCoverImageChange"
                    @logo-image-change="onLogoImageChange"
                    @header-change="onHeaderChange"
                    @subheader-change="onSubheaderChange"
                    @request-tobe-member="onRequestToBeMember"
                    @request-tobe-unmember="onRequestToBeUnmember"
                />
            </div>

            <!-- Instructions -->
            <div class="bg-blue-50 rounded-lg p-6 mt-8">
                <h2 class="text-xl font-semibold mb-4">Testing Instructions</h2>
                <div class="space-y-2 text-gray-700">
                    <p><strong>For Admin Testing:</strong></p>
                    <ul class="list-disc list-inside ml-4">
                        <li>Click the camera icon to test cover image upload</li>
                        <li>Click the camera icon on the logo to test logo upload</li>
                        <li>Click the pencil icon to edit header and subheader</li>
                        <li>Try saving and canceling edits</li>
                    </ul>
                    
                    <p class="mt-4"><strong>For Student Testing:</strong></p>
                    <ul class="list-disc list-inside ml-4">
                        <li>Test membership request buttons</li>
                        <li>Try different membership statuses (pending/active)</li>
                        <li>Test group selection dropdown</li>
                        <li>Test cancel membership functionality</li>
                    </ul>
                    
                    <p class="mt-4"><strong>Notifications:</strong></p>
                    <ul class="list-disc list-inside ml-4">
                        <li>All actions will show console logs</li>
                        <li>Success/error notifications appear in top-right corner</li>
                        <li>Loading states are simulated with delays</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>