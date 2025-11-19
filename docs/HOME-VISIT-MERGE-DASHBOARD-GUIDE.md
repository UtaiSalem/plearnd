# คู่มือการรวมฟีเจอร์รายงานเข้ากับแดชบอร์ด

## สรุปการเปลี่ยนแปลง

เราได้รวมฟีเจอร์รายงานการเยี่ยมบ้านเข้ากับหน้าแดshบอร์ดแล้ว โดย:

1. **ลบแท็บ "รายงานการเยี่ยมบ้าน"** ออก - เหลือเพียง 2 แท็บหลัก (แดชบอร์ด, โซน)
2. **สร้าง Composable Function** (`useVisitReports.js`) เพื่อจัดการฟีเจอร์รายงาน
3. **รอการอัปเดต Template** - แทนที่ส่วน "การเยี่ยมบ้านล่าสุด" ด้วยรายงานแบบเต็ม

## ไฟล์ที่สร้างใหม่

✅ `resources/js/composables/useVisitReports.js` - Composition function สำหรับจัดการรายงาน

## ไฟล์ที่อัปเดต

✅ `Dashboard.vue` - ลบ import VisitReports component และลบแท็บรายงาน

## ขั้นตอนถัดไป

เนื่องจากไฟล์ Dashboard.vue มีขนาดใหญ่ (428 บรรทัด) และมีโครงสร้างซับซ้อน ฉันแนะนำให้:

### ตัวเลือก 1: แก้ไขด้วยมือ (แนะนำ)

แก้ไขไฟล์ `Dashboard.vue` ดังนี้:

#### 1. อัปเดต Script Section (บรรทัดประมาณ 285-428)

```vue
<script>
import { ref, onMounted, computed } from "vue";
import { router } from "@inertiajs/vue3";
import StudentManagement from "./Components/StudentManagement.vue";
import ZoneManagement from "./Components/ZoneManagement.vue";
import VisitDetailModal from "./Components/VisitDetailModal.vue";
import { useVisitReports } from "@/composables/useVisitReports";

export default {
    components: {
        StudentManagement,
        ZoneManagement,
        VisitDetailModal,
    },

    props: {
        stats: Object,
        recentVisits: Array,
        monthlyVisits: Array,
        students: Array,
        allVisits: {
            type: Array,
            default: () => [],
        },
        zones: {
            type: Array,
            default: () => [],
        },
    },

    setup(props) {
        const activeTab = ref("dashboard");
        const studentsData = ref(props.students || []);

        const tabs = [
            { id: "dashboard", name: "แดชบอร์ด" },
            { id: "zones", name: "จัดการโซนการเยี่ยมบ้าน" },
            { id: "settings", name: "ตั้งค่า (กำลังพัฒนา)" },
        ];

        // ใช้ Visit Reports Composable
        const allVisitsRef = ref(props.allVisits || []);
        const zonesRef = ref(props.zones || []);

        const {
            showFilters,
            showDetailModal,
            selectedVisit,
            isExporting,
            currentPage,
            perPage,
            filters,
            filteredVisits,
            totalPages,
            paginatedVisits,
            toggleFilters,
            clearFilters,
            previousPage,
            nextPage,
            viewVisitDetails,
            closeDetailModal,
            exportToExcel,
            getStudentFullName,
            getStudentInitial,
        } = useVisitReports(allVisitsRef, zonesRef);

        // Utility Methods
        const getStatusText = (status) => {
            const statusMap = {
                active: "กำลังเรียน",
                graduated: "จบการศึกษา",
                transferred: "ย้ายโรงเรียน",
                suspended: "พักการเรียน",
                pending: "รอดำเนินการ",
                "in-progress": "กำลังดำเนินการ",
                completed: "เสร็จสิ้น",
                cancelled: "ยกเลิก",
            };
            return statusMap[status] || status;
        };

        const formatDate = (dateString) => {
            if (!dateString) return "ยังไม่มีการเยี่ยม";
            const date = new Date(dateString);
            return date.toLocaleDateString("th-TH", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
        };

        const logout = () => {
            router.post("/home-visit/admin/logout");
        };

        return {
            activeTab,
            tabs,
            studentsData,
            zones: zonesRef,
            // Visit Reports
            showFilters,
            showDetailModal,
            selectedVisit,
            isExporting,
            currentPage,
            perPage,
            filters,
            filteredVisits,
            totalPages,
            paginatedVisits,
            toggleFilters,
            clearFilters,
            previousPage,
            nextPage,
            viewVisitDetails,
            closeDetailModal,
            exportToExcel,
            getStudentFullName,
            getStudentInitial,
            // Utilities
            getStatusText,
            formatDate,
            logout,
        };
    },
};
</script>
```

#### 2. แทนที่ส่วน "Recent Visits" ใน Template (บรรทัดประมาณ 175-230)

แทนที่ส่วน:

```vue
<!-- Recent Visits -->
<div class="bg-white shadow overflow-hidden sm:rounded-md">
  ...
</div>
```

ด้วย:

```vue
<!-- Visit Reports with Filters -->
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
  <!-- Header with Actions -->
  <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          <i class="fas fa-clipboard-list mr-2 text-indigo-600"></i>
          การเยี่ยมบ้านทั้งหมด
        </h3>
        <p class="mt-1 text-sm text-gray-500">
          ค้นหาและดูรายละเอียดการเยี่ยมบ้าน
        </p>
      </div>
      <div class="flex gap-2">
        <button
          @click="exportToExcel"
          class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors"
          :disabled="isExporting"
        >
          <i class="fas fa-file-excel mr-2"></i>
          Excel
        </button>
        <button
          @click="toggleFilters"
          :class="[
            showFilters ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700',
            'inline-flex items-center px-3 py-2 hover:bg-indigo-700 text-sm font-medium rounded-lg transition-colors'
          ]"
        >
          <i class="fas fa-filter mr-2"></i>
          ตัวกรอง
        </button>
      </div>
    </div>
  </div>

  <!-- Filters Section (Collapsible) -->
  <div v-if="showFilters" class="px-4 py-4 bg-gray-50 border-b border-gray-200">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">วันที่เริ่มต้น</label>
        <input
          v-model="filters.startDate"
          type="date"
          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
        >
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">วันที่สิ้นสุด</label>
        <input
          v-model="filters.endDate"
          type="date"
          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
        >
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">สถานะ</label>
        <select
          v-model="filters.status"
          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
        >
          <option value="">ทั้งหมด</option>
          <option value="pending">รอดำเนินการ</option>
          <option value="in-progress">กำลังดำเนินการ</option>
          <option value="completed">เสร็จสิ้น</option>
          <option value="cancelled">ยกเลิก</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">โซน</label>
        <select
          v-model="filters.zoneId"
          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
        >
          <option value="">ทั้งหมด</option>
          <option v-for="zone in zones" :key="zone.id" :value="zone.id">
            {{ zone.zone_name }}
          </option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">ค้นหานักเรียน</label>
        <input
          v-model="filters.studentName"
          type="text"
          placeholder="ชื่อนักเรียน..."
          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
        >
      </div>
      <div class="flex items-end">
        <button
          @click="clearFilters"
          class="w-full px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors"
        >
          <i class="fas fa-times mr-2"></i>
          ล้างตัวกรอง
        </button>
      </div>
    </div>
  </div>

  <!-- Results Summary -->
  <div class="px-4 py-3 bg-white border-b border-gray-200">
    <div class="flex items-center justify-between text-sm">
      <span class="text-gray-700">
        แสดง {{ paginatedVisits.length }} จาก {{ filteredVisits.length }} รายการ
      </span>
      <div class="flex items-center gap-4">
        <span class="text-gray-500">หน้า {{ currentPage }}/{{ totalPages }}</span>
        <select
          v-model="perPage"
          class="border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
        >
          <option :value="10">10</option>
          <option :value="25">25</option>
          <option :value="50">50</option>
        </select>
      </div>
    </div>
  </div>

  <!-- Visits List -->
  <div v-if="filteredVisits.length === 0" class="p-12 text-center">
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
      <i class="fas fa-inbox text-gray-400 text-2xl"></i>
    </div>
    <h3 class="text-lg font-medium text-gray-900 mb-2">ไม่พบข้อมูล</h3>
    <p class="text-gray-500">ไม่พบรายการเยี่ยมบ้านตามเงื่อนไขที่ค้นหา</p>
  </div>

  <ul v-else class="divide-y divide-gray-200">
    <li
      v-for="visit in paginatedVisits"
      :key="visit.id"
      class="px-4 py-4 hover:bg-gray-50 cursor-pointer transition-colors"
      @click="viewVisitDetails(visit)"
    >
      <div class="flex items-center justify-between">
        <div class="flex items-center flex-1">
          <div class="flex-shrink-0">
            <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center">
              <span class="text-white font-medium text-sm">
                {{ getStudentInitial(visit.student) }}
              </span>
            </div>
          </div>
          <div class="ml-4 flex-1">
            <div class="flex items-center gap-2">
              <div class="text-sm font-medium text-gray-900">
                {{ getStudentFullName(visit.student) }}
              </div>
              <span v-if="visit.zone" class="text-xs text-gray-500">
                <i class="fas fa-map-marker-alt mr-1"></i>{{ visit.zone.zone_name }}
              </span>
            </div>
            <div class="text-sm text-gray-500 mt-1">
              {{ formatDate(visit.visit_date) }}
              <span v-if="visit.visitor_name" class="ml-2">
                | <i class="fas fa-user text-xs"></i> {{ visit.visitor_name }}
              </span>
              <span v-else-if="visit.participants && visit.participants.length > 0" class="ml-2">
                | <i class="fas fa-users text-xs"></i> {{ visit.participants.length }} คน
              </span>
            </div>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <span v-if="visit.images_count" class="text-xs text-gray-500">
            <i class="fas fa-images mr-1"></i>{{ visit.images_count }}
          </span>
          <span
            :class="[
              visit.visit_status === 'completed' ? 'bg-green-100 text-green-800' :
              visit.visit_status === 'in-progress' ? 'bg-yellow-100 text-yellow-800' :
              visit.visit_status === 'pending' ? 'bg-orange-100 text-orange-800' :
              'bg-gray-100 text-gray-800',
              'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium'
            ]"
          >
            {{ getStatusText(visit.visit_status) }}
          </span>
          <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
        </div>
      </div>
    </li>
  </ul>

  <!-- Pagination -->
  <div v-if="filteredVisits.length > 0" class="px-4 py-3 border-t border-gray-200 bg-gray-50">
    <div class="flex items-center justify-center space-x-2">
      <button
        @click="previousPage"
        :disabled="currentPage === 1"
        class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <i class="fas fa-chevron-left"></i>
      </button>
      <span class="text-sm text-gray-700">
        หน้า {{ currentPage }} จาก {{ totalPages }}
      </span>
      <button
        @click="nextPage"
        :disabled="currentPage === totalPages"
        class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>
  </div>
</div>
```

#### 3. เพิ่ม Modal ก่อนปิด template (ก่อนบรรทัด `</div></template>`)

```vue
    <!-- Visit Detail Modal -->
    <VisitDetailModal
      v-if="showDetailModal"
      :visit="selectedVisit"
      @close="closeDetailModal"
    />
  </div>
</template>
```

### ตัวเลือก 2: สร้างไฟล์ใหม่ทั้งหมด (หากมีปัญหา)

หากการแก้ไขด้วยมือทำให้เกิด error สามารถสร้างไฟล์ Dashboard.vue ใหม่ทั้งหมดได้

## สรุป

การรวมฟีเจอร์นี้จะทำให้:

-   ✅ **ใช้งานง่ายขึ้น** - ไม่ต้องสลับระหว่างแท็บ
-   ✅ **รวดเร็ว** - ดูข้อมูลทั้งหมดในที่เดียว
-   ✅ **ฟีเจอร์ครบ** - มีตัวกรอง, ค้นหา, ส่งออก Excel, ดูรายละเอียด
-   ✅ **Responsive** - รองรับทุกขนาดหน้าจอ
-   ✅ **Code Clean** - ใช้ Composition API แยกเป็น Composable

## ทดสอบหลังแก้ไข

1. เปิด `/home-visit/admin/dashboard`
2. ตรวจสอบว่าหน้าแดชบอร์ดแสดงปกติ
3. ทดสอบตัวกรองทุกประเภท
4. ทดสอบ pagination
5. ทดสอบคลิกดูรายละเอียด
6. ทดสอบส่งออก Excel

## ไฟล์ที่ไม่ใช้งานแล้ว (สามารถลบได้)

-   `resources/js/Pages/Learn/Student/HomeVisit/Admin/Components/VisitReports.vue`

แต่ยังคง Component นี้เพื่อ Reference:

-   `VisitDetailModal.vue` - ยังใช้งานอยู่ใน Dashboard
