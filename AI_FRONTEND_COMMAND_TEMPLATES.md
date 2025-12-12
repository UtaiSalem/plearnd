# AI Frontend Command Templates for Vue.js 3 + Tailwind CSS

## โครงสร้างโปรเจคและเทคโนโลยีที่ใช้

**เทคโนโลยีหลัก:**
- Vue.js 3 (Composition API)
- Tailwind CSS 3.4.17
- Inertia.js
- Laravel Backend
- PrimeVue 3.53.1
- Headless UI
- Pinia 3.0.3

**ไลบรารีเสริม:**
- @iconify/vue สำหรับไอคอน
- vue-sweetalert2 สำหรับการแจ้งเตือน
- @vuepic/vue-datepicker สำหรับปฏิทิน
- html2canvas สำหรับการสกรีนช็อต
- file-saver สำหรับดาวน์โหลดไฟล์

**รูปแบบการออกแบบ:**
- ใช้สีหลัก: indigo, purple, pink สำหรับ gradient
- ใช้สีรอง: blue, green, yellow, orange, red สำหรับ status
- มีการใช้ animation และ transition อย่างหลากหลาย
- ใช้ card-based design กับ shadow และ rounded corners
- ใช้ responsive design สำหรับ mobile และ desktop

---

## 📋 เทมเพลตคำสั่งสำหรับการสร้างหน้าเว็บทั่วไป

### คำสั่งพื้นฐาน:

```
สร้างหน้า Vue.js 3 ใหม่โดยใช้ Composition API และ Tailwind CSS ตามโปรเจคปัจจุบัน

รายละเอียด:
- ใช้ Vue 3 Composition API กับ <script setup>
- ใช้ Tailwind CSS สำหรับ styling ตามโปรเจคปัจจุบัน
- ใช้สีหลัก indigo/purple สำหรับ header และ primary actions
- ใช้ card-based design กับ shadow และ rounded corners
- ใช้ responsive design สำหรับ mobile และ desktop
- เพิ่ม animation และ transitions ที่เหมาะสม
- ใช้ไอคอนจาก @iconify/vue หรือ FontAwesome
- ใช้ gradient backgrounds สำหรับ headers และ important sections
- เพิ่ม hover effects และ interactive elements
- ใช้ loading states และ empty states ที่เหมาะสม

โครงสร้างไฟล์:
- สร้างที่ resources/js/Pages/[path]/[PageName].vue
- ใช้ <template>, <script setup>, และ <style scoped>
- แบ่งเป็น components ถ้าจำเป็น

เนื้อหาหน้าเว็บ:
[ระบุรายละเอียดเนื้อหาที่ต้องการ]
```

---

## 🧩 เทมเพลตคำสั่งสำหรับการสร้างคอมโพเนนต์

### คำสั่งสำหรับคอมโพเนนต์ทั่วไป:

```
สร้าง Vue Component ใหม่โดยใช้ Composition API และ Tailwind CSS

รายละเอียด Component:
- ชื่อ: [ชื่อคอมโพเนนต์]
- วัตถุประสงค์: [วัตถุประสงค์ของคอมโพเนนต์]
- Props: [ระบุ props ที่ต้องการ]
- Emits: [ระบุ events ที่ต้องการ]

การออกแบบ:
- ใช้ Tailwind CSS ตามโปรเจคปัจจุบัน
- ใช้สีหลัก indigo/purple สำหรับ interactive elements
- เพิ่ม hover effects และ transitions
- ใช้ responsive design
- เพิ่ม loading states ถ้าจำเป็น
- ใช้ไอคอนที่เหมาะสม

โครงสร้าง:
- สร้างที่ resources/js/Components/[ComponentName].vue
- ใช้ <script setup> กับ defineProps และ defineEmits
- เพิ่ม TypeScript definitions ถ้าจำเป็น

ฟังก์ชันการทำงาน:
[ระบุฟังก์ชันการทำงานที่ต้องการ]
```

---

## 📊 เทมเพลตคำสั่งสำหรับการสร้างแดชบอร์ด

### คำสั่งสำหรับแดชบอร์ด:

```
สร้างหน้า Dashboard โดยใช้ Vue.js 3 + Tailwind CSS ตามโปรเจคปัจจุบัน

โครงสร้างหน้า:
- Header พร้อม gradient background (indigo -> purple -> pink)
- Statistics cards แสดงข้อมูลสำคัญ
- Charts หรือ data visualizations
- Recent activities หรือ timeline
- Quick actions หรือ shortcuts

Statistics Cards:
- ใช้ grid layout (responsive)
- แต่ละ card มีไอคอน ตัวเลข และ label
- ใช้สีที่แตกต่างกัน: blue, green, yellow, orange, red
- เพิ่ม hover effects และ subtle animations
- ใช้ rounded corners และ shadows

Data Visualization:
- ใช้ charts หรือ graphs ที่เหมาะสมกับข้อมูล
- ใช้สีที่สอดคล้องกับ theme
- เพิ่ม loading states และ error states
- ใช้ responsive design

Recent Activities:
- ใช้ timeline layout หรือ card list
- แสดง timestamp, user, action
- เพิ่ม status indicators
- ใช้ hover effects และ transitions

Technical Requirements:
- ใช้ Composition API กับ <script setup>
- ใช้ Tailwind CSS ตามโปรเจค
- เพิ่ม animations และ transitions
- ใช้ icons จาก @iconify/vue
- ใช้ gradient backgrounds สำหรับ headers
- ใช้ responsive design สำหรับทุกขนาดหน้าจอ

ข้อมูลที่ต้องแสดง:
[ระบุข้อมูลที่ต้องแสดงในแดชบอร์ด]
```

---

## 📝 เทมเพลตคำสั่งสำหรับการสร้างฟอร์ม

### คำสั่งสำหรับฟอร์ม:

```
สร้างฟอร์ม Vue.js 3 โดยใช้ Composition API และ Tailwind CSS

โครงสร้างฟอร์ม:
- ใช้ <form> กับ proper validation
- แบ่ง sections ด้วย card layouts
- ใช้ responsive grid สำหรับ form fields
- เพิ่ม loading states สำหรับ submit

Form Fields:
- Text inputs: ใช้ rounded-lg, focus:ring-indigo-500
- Select dropdowns: ใช้ Tailwind styling
- Date pickers: ใช้ @vuepic/vue-datepicker
- Textareas: ใช้ proper sizing และ resize
- Checkboxes/Radio: ใช้ custom styling
- File uploads: ใช้ drag & drop interface

Validation:
- ใช้ reactive validation states
- แสดง error messages ใต้ fields
- ใช้ color coding: red for errors, green for success
- เพิ่ม visual feedback สำหรับ touched fields

Actions:
- Primary button: ใช้ bg-indigo-600 hover:bg-indigo-700
- Secondary button: ใช้ gray styling
- Cancel button: ใช้ subtle styling
- เพิ่ม loading states สำหรับ buttons
- ใช้ proper spacing และ alignment

Styling:
- ใช้ consistent spacing กับ Tailwind
- เพิ่ม hover effects และ transitions
- ใช้ icons สำหรับ field labels ถ้าจำเป็น
- ใช้ responsive design

ฟังก์ชันการทำงาน:
[ระบุฟังก์ชันการทำงานของฟอร์ม]
```

---

## 📋 เทมเพลตคำสั่งสำหรับการสร้างตารางข้อมูล

### คำสั่งสำหรับตาราง:

```
สร้างตารางข้อมูล Vue.js 3 โดยใช้ Composition API และ Tailwind CSS

โครงสร้างตาราง:
- ใช้ responsive table design
- เพิ่ม header พร้อม gradient background
- ใช้ striped rows หรือ hover effects
- เพิ่ม sorting และ filtering functionality

Table Features:
- Pagination: ใช้ component-based design
- Search: ใช้ real-time filtering
- Sort: ใช้ clickable headers พร้อม icons
- Filters: ใช้ dropdowns หรือ date pickers
- Actions: ใชง action buttons ใน last column

Column Design:
- Headers: ใช้ bg-gray-50 หรือ gradient
- Cells: ใช้ proper padding และ alignment
- Status: ใช้ colored badges
- Actions: ใชง icon buttons พร้อม hover effects
- Data: ใช้ proper formatting สำหรับ dates, numbers

Responsive Design:
- Mobile: ใช้ card-based layout
- Tablet: ใช้ horizontal scroll
- Desktop: ใช้ full table view
- เพิ่ม breakpoints ที่เหมาะสม

Empty States:
- แสดง message เมื่อไม่มีข้อมูล
- ใช้ icons และ proper messaging
- เพิ่ม call-to-action ถ้าจำเป็น

Loading States:
- ใช้ skeleton loaders
- เพิ่ม shimmer effects
- ใช้ proper loading indicators

ข้อมูลที่ต้องแสดง:
[ระบุโครงสร้างข้อมูลและคอลัมน์ที่ต้องการ]
```

---

## 📑 เทมเพลตคำสั่งสำหรับการสร้างหน้าแสดงรายการ (List View)

### คำสั่งสำหรับ List View:

```
สร้างหน้า List View โดยใช้ Vue.js 3 + Tailwind CSS ตามโปรเจคปัจจุบัน

โครงสร้างหน้า:
- Header พร้อม gradient background และ actions
- Filters section พร้อม multiple filter options
- List items ใน card หรือ row format
- Pagination หรือ infinite scroll

List Items:
- ใช้ card-based design กับ shadows
- เพิ่ม hover effects และ transitions
- แสดง key information: title, status, date, etc.
- เพิ่ม action buttons หรือ menu
- ใช้ status indicators พร้อม colors

Filters:
- Search bar พร้อม real-time filtering
- Dropdown filters สำหรับ categories
- Date range pickers สำหรับ date filtering
- Status filters พร้อม colored badges
- Clear filters button

Layout Options:
- Grid view: สำหรับ visual items
- List view: สำหรับ detailed information
- Compact view: สำหรับ mobile
- Toggle between views

Interactions:
- Click to view details
- Hover to show actions
- Swipe for mobile actions
- Keyboard navigation

Empty States:
- แสดง illustration หรือ icons
- เพิ่ม descriptive messages
- ใช้ call-to-action buttons

Loading States:
- ใช้ skeleton cards
- เพิ่ม shimmer effects
- ใชง progressive loading

ข้อมูลที่ต้องแสดง:
[ระบุประเภทข้อมูลและรูปแบบที่ต้องการ]
```

---

## 🎯 คำสั่งรวมที่ครอบคลุมทุกกรณี

### คำสั่งสำหรับการสร้างหน้าเว็บที่สมบูรณ์:

```
สร้างหน้า Vue.js 3 ที่สมบูรณ์โดยใช้ Composition API และ Tailwind CSS ตามมาตรฐานโปรเจคปัจจุบัน

**เทคโนโลยีที่ต้องใช้:**
- Vue.js 3 กับ Composition API และ <script setup>
- Tailwind CSS 3.4.17 ตาม config ปัจจุบัน
- Inertia.js สำหรับ routing
- PrimeVue components ถ้าจำเป็น
- @iconify/vue สำหรับ icons
- Pinia สำหรับ state management

**การออกแบบ UI/UX:**
- ใช้ gradient backgrounds (indigo -> purple -> pink) สำหรับ headers
- ใช้ card-based design กับ rounded-lg และ shadows
- ใช้สีหลัก: indigo, purple, pink สำหรับ primary elements
- ใช้สีรอง: blue, green, yellow, orange, red สำหรับ status
- เพิ่ม animations และ transitions ที่ smooth
- ใช้ hover effects และ micro-interactions
- ใช้ responsive design สำหรับทุกขนาดหน้าจอ

**โครงสร้างหน้าเว็บ:**
1. Header Section:
   - Gradient background พร้อม title และ actions
   - Breadcrumb navigation ถ้าจำเป็น
   - User menu หรือ profile section

2. Content Area:
   - Filters section พร้อม multiple filter options
   - Main content ตามประเภทหน้า (dashboard, form, table, list)
   - Loading states และ empty states
   - Pagination หรือ infinite scroll

3. Interactive Elements:
   - Buttons พร้อม proper hover states
   - Forms พร้อม validation
   - Modals สำหรับ details หรือ confirmations
   - Tooltips สำหรับ additional information

**ประเภทหน้าเว็บ:**
[เลือกประเภทที่ต้องการ]
- Dashboard: พร้อม statistics cards และ charts
- Form: พร้อม validation และ multi-step
- Table: พร้อม sorting, filtering, และ pagination
- List View: พร้อม cards และ grid/list toggle
- Detail Page: พร้อม tabbed content และ related items

**คุณสมบัติเฉพาะ:**
- Real-time updates ถ้าจำเป็น
- Export functionality (Excel, PDF)
- Print-friendly styles
- Accessibility features (ARIA labels, keyboard navigation)
- Performance optimizations (lazy loading, virtual scrolling)

**การจัดการข้อมูล:**
- API integration พร้อม error handling
- Data formatting สำหรับ dates, numbers, currency
- Search functionality พร้อม debouncing
- Caching strategy ถ้าจำเป็น

**รายละเอียดเฉพาะของหน้า:**
[ระบุรายละเอียดเฉพาะของหน้าที่ต้องการสร้าง]

**โครงสร้างไฟล์:**
- Main page: resources/js/Pages/[path]/[PageName].vue
- Components: resources/js/Components/[ComponentName].vue
- Composables: resources/js/composables/[useFunction].js
- Types: resources/js/types/[Type].ts ถ้าจำเป็น

**ข้อกำหนดเพิ่มเติม:**
- ใช้ TypeScript definitions ถ้าจำเป็น
- เพิ่ม unit tests ถ้าจำเป็น
- ใช้ ESLint ตาม config ปัจจุบัน
- เพิ่ม comments สำหรับ complex logic
```

---

## 📚 เอกสารคำแนะนำการใช้คำสั่ง

### วิธีการใช้คำสั่ง:

1. **เลือกเทมเพลตที่เหมาะสม:**
   - ใช้เทมเพลตทั่วไปสำหรับหน้าธรรมดา
   - ใช้เทมเพลตเฉพาะสำหรับคอมโพเนนต์พิเศษ
   - ใช้คำสั่งรวมสำหรับหน้าที่ซับซ้อน

2. **ปรับแต่งคำสั่ง:**
   - แทนที่ [ข้อความในวงเล็บ] ด้วยข้อมูลจริง
   - เพิ่มรายละเอียดเฉพาะที่ต้องการ
   - ระบุข้อมูลที่ต้องการแสดงให้ชัดเจน

3. **ตรวจสอบความสอดคล้อง:**
   - ตรวจสอบว่าข้อมูลตรงกับโครงสร้างโปรเจค
   - ตรวจสอบว่าใช้ libraries ที่มีอยู่แล้ว
   - ตรวจสอบว่าตรงกับมาตรฐานการออกแบบ

4. **การปรับแต่งหลังการสร้าง:**
   - ตรวจสอบ responsive design
   - ทดสอบ functionality ทั้งหมด
   - ปรับแต่ง performance ถ้าจำเป็น

### เคล็ดลับเพิ่มเติม:

- **ใช้ชื่อที่สื่อความหมาย:** ตั้งชื่อ components และ variables ให้ชัดเจน
- **เพิ่ม error handling:** จัดการกับ error states อย่างเหมาะสม
- **ทดสอบ accessibility:** ตรวจสอบว่าใช้งานได้ดีกับ screen readers
- ** optimize performance:** ใช้ lazy loading สำหรับข้อมูลจำนวนมาก
- **ใช้ consistent styling:** ทำตามมาตรฐานการออกแบบของโปรเจค

### ตัวอย่างการใช้งานจริง:

```
สร้างหน้า Student Management โดยใช้ Vue.js 3 + Tailwind CSS

รายละเอียด:
- แสดงรายชื่อนักเรียนในรูปแบบตาราง
- มีฟังก์ชันค้นหา, กรอง, และแบ่งหน้า
- สามารถเพิ่ม, แก้ไข, และลบข้อมูลนักเรียน
- แสดงข้อมูล: รหัส, ชื่อ-นามสกุล, ชั้นเรียน, email, เบอร์โทร, สถานะ

ฟอร์ม:
- ใช้ validation สำหรับ required fields
- ใช้ date picker สำหรับวันเกิด
- ใช้ dropdown สำหรับชั้นเรียนและสถานะ

ตาราง:
- ใช้ sorting สำหรับคอลัมน์ชื่อและวันที่
- ใช้ colored badges สำหรับสถานะ
- ใช้ action buttons ในคอลัมน์สุดท้าย
```

---

## 🎨 มาตรฐานการออกแบบของโปรเจค

### สีที่ใช้:
- **Primary:** indigo (500, 600, 700)
- **Secondary:** purple (500, 600, 700)
- **Success:** green (500, 600, 700)
- **Warning:** yellow (500, 600, 700)
- **Danger:** red (500, 600, 700)
- **Info:** blue (500, 600, 700)

### Gradients:
- **Header:** from-indigo-600 via-purple-600 to-pink-500
- **Cards:** from-gray-50 to-indigo-50/30
- **Buttons:** ใช้ solid colors พร้อม hover effects

### Spacing:
- **Container:** max-w-7xl mx-auto px-4 sm:px-6 lg:px-8
- **Cards:** p-4 หรือ p-6
- **Sections:** space-y-4 หรือ space-y-6
- **Grid:** gap-4 หรือ gap-6

### Typography:
- **Headers:** text-xl, text-2xl, text-3xl กับ font-bold
- **Body:** text-sm, text-base กับ font-medium
- **Labels:** text-xs กับ font-medium

### Animations:
- **Transitions:** transition-all duration-200/300
- **Hover:** hover:scale-105, hover:shadow-lg
- **Loading:** animate-pulse, animate-spin
- **Entrance:** animate-fade-in, animate-slide-in

### Components:
- **Buttons:** px-4 py-2 rounded-lg font-medium
- **Inputs:** rounded-lg border-gray-300 focus:ring-indigo-500
- **Cards:** bg-white shadow rounded-lg
- **Badges:** px-2.5 py-0.5 rounded-full text-xs

---

เอกสารนี้สร้างขึ้นเพื่อให้ AI สามารถสร้างหน้าเว็บที่สวยงามและสอดคล้องกับโปรเจคปัจจุบันได้อย่างมีประสิทธิภาพ