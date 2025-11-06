# Course Page — Version 2

เอกสารฉบับนี้สรุป **Component Map**, **Pinia Store (state shape, actions, getters)**, **Data Flow**, และแผนการพัฒนา/ย้ายข้อมูลสำหรับการสร้างหน้าคอร์สเวอร์ชันใหม่ (SPA) โดยจะแยกหน้ากลุ่มเรียน (Groups) และหน้าสมาชิก (Members) ออกจากกันอย่างชัดเจน

---

## วัตถุประสงค์สั้น ๆ
- แยกเวอร์ชันใหม่ (v2) ออกจากระบบเดิมเพื่อไม่กระทบระบบที่พัฒนาแล้ว
- ทำให้เป็น SPA (Vue 3 + Vite) โดยใช้ **Pinia** เป็น store กลาง
- ปรับปรุง performance, scalability, และ UX (ไม่ต้อง reload หน้า)
- รองรับ concurrent user จำนวนมาก และผู้ดูแลหลายระดับ

---

## สถาปัตยกรรมภาพรวม
- Frontend: Vue 3 (Composition API) + Vite
- State: Pinia (store แยกตาม domain)
- Router: Vue Router (dynamic nested routes สำหรับแต่ละรายวิชา)
- Styling: TailwindCSS
- HTTP: Axios (API layer) + interceptor สำหรับ token
- Realtime (optional): WebSocket / Pusher สำหรับประกาศ/เช็กชื่อแบบสด

---

## โฟลเดอร์ตัวอย่าง (src/)
```
src/
 ├─ layouts/
 │   ├─ MainLayout.vue
 │   └─ CourseLayout.vue
 ├─ pages/
 │   └─ Course/
 │       ├─ Dashboard.vue
 │       ├─ Announcement.vue
 │       ├─ Attendance.vue
 │       ├─ Lessons.vue
 │       ├─ Assignments.vue
 │       ├─ Exams.vue
 │       ├─ Groups.vue
 │       ├─ Members.vue
 │       ├─ Settings.vue
 │       └─ Summary.vue
 ├─ components/
 │   ├─ course/
 │   │   ├─ CourseHeader.vue
 │   │   ├─ CourseSidebar.vue
 │   │   ├─ Pagination.vue
 │   │   ├─ GroupTable.vue
 │   │   ├─ GroupFormModal.vue
 │   │   ├─ MemberList.vue
 │   │   └─ MemberInviteModal.vue
 ├─ store/
 │   ├─ useCourseStore.js
 │   ├─ useGroupStore.js
 │   ├─ useMemberStore.js
 │   ├─ useLessonStore.js
 │   ├─ useAssignmentStore.js
 │   ├─ useExamStore.js
 │   └─ useSummaryStore.js
 ├─ api/
 │   ├─ courseApi.js
 │   ├─ groupApi.js
 │   └─ memberApi.js
 └─ router/
     └─ courseRoutes.js
```

---

## Routes (ตัวอย่าง)
```js
{
  path: '/course/:courseId',
  component: CourseLayout,
  children: [
    { path: '', name: 'Course.Dashboard', component: Dashboard },
    { path: 'announcements', name: 'Course.Announcements', component: Announcement },
    { path: 'attendance', name: 'Course.Attendance', component: Attendance },
    { path: 'lessons', name: 'Course.Lessons', component: Lessons },
    { path: 'assignments', name: 'Course.Assignments', component: Assignments },
    { path: 'exams', name: 'Course.Exams', component: Exams },
    { path: 'groups', name: 'Course.Groups', component: Groups },
    { path: 'members', name: 'Course.Members', component: Members },
    { path: 'settings', name: 'Course.Settings', component: Settings },
    { path: 'summary', name: 'Course.Summary', component: Summary }
  ]
}
```

---

# Component Map — รายการ Component หลัก และความสัมพันธ์

component ใหม่อยู่ภายใต้โครงสร้าง

@resources/js/Pages/Learnd/Course/v2/...

### Layouts
- **MainLayout.vue** — global header, global sidebar, auth status, notifications
- **CourseLayout.vue** — course-specific header (CourseHeader), left CourseSidebar, RouterView สำหรับหน้าต่าง ๆ ของรายวิชา

### Page Components (Course)
- **Dashboard.vue** — สรุปภาพรวม (announcements, upcoming assignments, quick stats)
- **Announcement.vue** — จัดการประกาศ, create/edit, rich text, pinned
- **Attendance.vue** — เช็กชื่อ (per group), ปุ่มเช็กอัตโนมัติ, รายงานการขาด
- **Lessons.vue** — list of lessons, เรียงลำดับ, drag-drop reorder (optional)
- **Assignments.vue** — สร้าง/แก้ไข assignment, ส่งงาน, ตรวจงาน
- **Exams.vue** — สร้าง/แก้ไข/จัดการข้อสอบ, ตั้งเวลา, คะแนน
- **Groups.vue** — (สำคัญ) จัดการกลุ่มเรียน: สร้างกลุ่ม, แก้ไข, เพิ่ม/ย้ายสมาชิก, มอบหมาย group leader
- **Members.vue** — รายการสมาชิก: role (student, teacher, TA), ค้นหา, เชิญ, แบน/ให้สิทธิ์
- **Settings.vue** — การตั้งค่ารายวิชา (visibility, enrollment, roles)
- **Summary.vue** — ตารางสรุปคะแนน, filters, export (CSV/PDF)

### Reusable Components
- **CourseHeader.vue** — breadcrumb, course actions, quick stats
- **CourseSidebar.vue** — navigation (dynamic), group switcher (ถ้ามีหลายกลุ่ม)
- **Pagination.vue** — general
- **Modal components** — GroupFormModal, MemberInviteModal, ConfirmDialog
- **Tables/Lists** — GroupTable, MemberList, LessonCard, AssignmentList
- **Charts** — SummaryChart (ใช้ Recharts/Chart.js ถ้าจำเป็น)

---

# Pinia Store Design
แยก store ตาม domain เพื่อความชัดเจนและยืดหยุ่น

## useCourseStore (หลัก)
**State**
```js
{
  courseId: null,
  courseMeta: {}, // title, code, term, instructors
  loading: false,
  error: null
}
```
**Actions**
- `init(courseId)` — ดึง meta เบื้องต้น (ประกาศ, counts)
- `updateCourse(payload)`
- `fetchQuickStats()`

**Getters**
- `instructors`, `isTeacher(userId)`

---

## useGroupStore
**State**
```js
{
  groups: [], // [{id,name,capacity,membersCount,memberIds,meta}]
  activeGroupId: null,
  loading: false,
  pagination: { page:1, perPage:20, total:0 }
}
```
**Actions**
- `fetchGroups(courseId, params)` — paginated
- `createGroup(courseId, payload)`
- `updateGroup(groupId, payload)`
- `deleteGroup(groupId)`
- `addMemberToGroup(groupId, memberId)`
- `removeMemberFromGroup(groupId, memberId)`
- `setActiveGroup(groupId)`
- `moveMember(groupFrom, groupTo, memberId)`

**Getters**
- `activeGroup`, `groupById(id)`, `groupsForSelect`

**Notes**
- เมื่อแก้ไข group → update store แล้วทำ optimistic UI (ถ้า API รองรับ)
- หากสมาชิกจำนวนมาก ให้ใช้ server-side pagination + search

---

## useMemberStore
**State**
```js
{
  members: [], // [{id, name, role, email, avatar, status}]
  loading: false,
  filters: { role: '', q: '' },
  pagination: { page:1, perPage:50, total:0 }
}
```
**Actions**
- `fetchMembers(courseId, params)`
- `inviteMember(courseId, payload)`
- `updateMemberRole(memberId, role)`
- `removeMember(memberId)`
- `bulkImport(membersArray)`

**Getters**
- `memberById(id)`, `students`, `instructors`, `tas`

**Notes**
- หน้า Members จะใช้ `fetchMembers` แบบ lazy load และ search-as-you-type (debounced)
- หากระบบมี LDAP/SIS integration ให้แยก action สำหรับ sync

---

## useLessonStore / useAssignmentStore / useExamStore (สรุป)
- State รูปแบบคล้ายกัน: items[], loading, pagination, activeItem
- Actions: fetchList, fetchOne, create, update, delete, publish/unpublish
- Getters: itemById, publishedItems

---

## useSummaryStore
**State**
```js
{
  summaries: {}, // keyed by studentId or groupId
  loading: false
}
```
**Actions**
- `fetchCourseSummary(courseId)`
- `fetchStudentSummary(courseId, studentId)`
- `exportCSV(filters)`

---

# Data Flow (ลำดับเหตุการณ์ตัวอย่าง)
1. ผู้ใช้เปิด `/course/123` → `CourseLayout` เรียก `useCourseStore.init(123)`
2. `init` ดึง `courseMeta` และ `quickStats` แล้ว update store
3. Sidebar แสดงเมนูและ group switcher (ใช้ `useGroupStore.fetchGroups` ถ้ายังไม่โหลด)
4. ผู้ใช้คลิก `Groups` route → `Groups.vue` เรียก `useGroupStore.fetchGroups({page:1})`
5. ถ้าผู้ใช้คลิก 'เพิ่มสมาชิก' → modal เรียก `useMemberStore.inviteMember` แล้วเมื่อตอบกลับสำเร็จ ให้เรียก `useMemberStore.fetchMembers` และ `useGroupStore.fetchGroups` เพื่อซิงค์จำนวนสมาชิก

---

# UI / UX Considerations
- **Layout ชุดเดียว** (CourseLayout) เพื่อรักษา persistent state (เช่น sidebar opened, active group)
- **Skeleton loaders** แทน spinner บางจุด (UX ที่ดีกว่าเมื่อโหลดรายการ)
- **Debounced search** สำหรับ member list และ announcements
- **Optimistic UI** สำหรับการ add/remove member ภายใต้เงื่อนไขว่า backend รองรับ
- **Accessibility**: keyboard navigation, aria labels, focus management ใน modal

---

# Performance & Scalability
- **Pagination / Cursor-based pagination** สำหรับ members และ group members
- **Lazy load / code-splitting** แต่ละ route (dynamic import)
- **Cache strategy**: Pinia + short-lived cache + revalidate (e.g., stale-while-revalidate)
- **Websocket notifications** สำหรับประกาศใหม่ และการอัปเดตเช็กชื่อแบบ real-time
- **Bulk API endpoints** สำหรับการ invite/import สมาชิกจำนวนมาก
- **Worker / background processing** ในฝั่ง server สำหรับงานหนัก (เช่น export PDF/CSV)

---

# Security & Roles
- Role-based permission checksทั้ง frontend (UI hide) และ backend (authoritative)
- Actions ที่มีผลต่อข้อมูลจำนวนมาก ต้องมี confirmation
- Rate-limiting endpoints ที่เกี่ยวกับ search / invite เพื่อป้องกัน abuse

---

# Migration / Parallel Deployment Strategy
1. สร้าง namespace route `/v2/course/:courseId` เพื่อทดสอบโดยไม่กระทบ v1
2. สร้าง feature flag (per-course) ให้สลับไปใช้ v2 ได้ทีละรายวิชา
3. เริ่ม migrate data non-destructively: groups/members sync endpoints (read from v1 until switch)
4. เมื่อทดสอบครบ, สลับ feature flag เป็น default และ archive v1

---

# Testing Checklist
- Unit tests for store actions & getters
- Integration tests for API layer (mocked responses)
- E2E tests (Cypress / Playwright) สำหรับ flows: invite member, create group, move member
- Load testing (k6 / JMeter) สำหรับ endpoints: fetchMembers, fetchGroups

---

# API Contract (ตัวอย่าง endpoints)
```
GET /api/courses/:courseId
GET /api/courses/:courseId/groups?page=1&perPage=20
POST /api/courses/:courseId/groups
PUT /api/groups/:groupId
DELETE /api/groups/:groupId
POST /api/courses/:courseId/members/invite
GET /api/courses/:courseId/members?page=1&perPage=50&q=
PUT /api/members/:memberId/role
DELETE /api/members/:memberId
GET /api/courses/:courseId/summary
```

---

# Component Implementation Notes (Groups & Members)
### Groups.vue
- Table แสดง `{name, capacity, membersCount, actions}`
- Action: edit (open GroupFormModal), delete (ConfirmDialog), view members (open panel)
- When open members panel: lazy load `GET /groups/:id/members` (server side pagination)

### Members.vue
- Search bar + filters by role
- Bulk actions (invite CSV, change role, remove)
- Invite modal supports single invite (email) หรือ batch upload (CSV)
- Each member row shows status (invited, active, suspended), role dropdown, more actions

---

# Observability & Monitoring
- Frontend error tracking: Sentry (capture exceptions, performance)
- Metrics: page load, route change time, store action durations
- Backend logs: audit log for member changes (who, when, what)

---

# Roadmap (minimal viable v2)
**Sprint 1 (2 hours)**
- Bootstrapping project, layouts, routing, basic course store, CourseLayout
- Implement Groups page (list, create, edit, delete)
- Implement Members page (list, search, invite single)

**Sprint 2 (2 hours)**
- Integrate group-member actions (add/remove/move)
- Implement lazy loading, pagination, skeletons
- Implement role change & bulk invite (CSV)

**Sprint 3 (2 hours)**
- Lessons/Assignments/Exams basic CRUD
- Summary page (basic aggregation)
- Testing, load testing, feature-flag rollout

---



