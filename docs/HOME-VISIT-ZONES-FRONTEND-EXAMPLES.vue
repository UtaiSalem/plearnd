<!-- ตัวอย่างการใช้งาน Zone Dropdown ใน Vue Component -->

<template>
  <div class="zone-selector">
    <!-- Zone Dropdown for Home Visit Form -->
    <div class="form-group">
      <label for="zone_id" class="form-label">
        โซนการเยี่ยมบ้าน
        <span class="text-muted">(ไม่บังคับ)</span>
      </label>
      <select 
        id="zone_id" 
        v-model="form.zone_id" 
        class="form-control"
        :class="{ 'is-invalid': errors.zone_id }"
      >
        <option value="">-- ไม่ระบุโซน --</option>
        <option 
          v-for="zone in zones" 
          :key="zone.id" 
          :value="zone.id"
          :style="{ color: zone.color }"
        >
          {{ zone.zone_name }} ({{ zone.zone_code }})
        </option>
      </select>
      <div v-if="errors.zone_id" class="invalid-feedback">
        {{ errors.zone_id }}
      </div>
    </div>

    <!-- Zone Badge Display (for showing selected zone) -->
    <div v-if="selectedZone" class="zone-badge" :style="zoneStyle">
      <i class="fas fa-map-marker-alt"></i>
      {{ selectedZone.zone_name }}
    </div>
  </div>
</template>

<script>
export default {
  name: 'ZoneSelector',
  
  props: {
    zones: {
      type: Array,
      required: true,
      default: () => []
    },
    modelValue: {
      type: [Number, String, null],
      default: null
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },

  emits: ['update:modelValue'],

  data() {
    return {
      form: {
        zone_id: this.modelValue
      }
    }
  },

  computed: {
    selectedZone() {
      if (!this.form.zone_id) return null;
      return this.zones.find(z => z.id === this.form.zone_id);
    },

    zoneStyle() {
      if (!this.selectedZone) return {};
      return {
        backgroundColor: this.selectedZone.color || '#6B7280',
        color: '#FFFFFF',
        padding: '0.5rem 1rem',
        borderRadius: '0.375rem',
        display: 'inline-block',
        fontSize: '0.875rem',
        fontWeight: '500'
      };
    }
  },

  watch: {
    'form.zone_id'(newValue) {
      this.$emit('update:modelValue', newValue);
    },

    modelValue(newValue) {
      this.form.zone_id = newValue;
    }
  }
}
</script>

<!-- =========================================== -->
<!-- ตัวอย่างการใช้งานใน Parent Component -->
<!-- =========================================== -->

<template>
  <div class="home-visit-form">
    <h2>สร้างบันทึกการเยี่ยมบ้าน</h2>

    <form @submit.prevent="submitHomeVisit">
      <!-- Student Info -->
      <div class="form-group">
        <label>นักเรียน</label>
        <input type="text" :value="student.full_name" disabled class="form-control" />
      </div>

      <!-- Visit Date -->
      <div class="form-group">
        <label>วันที่เยี่ยมบ้าน <span class="text-danger">*</span></label>
        <input 
          type="date" 
          v-model="form.visit_date" 
          class="form-control"
          required
        />
      </div>

      <!-- Visit Time -->
      <div class="form-group">
        <label>เวลาเยี่ยมบ้าน</label>
        <input 
          type="time" 
          v-model="form.visit_time" 
          class="form-control"
        />
      </div>

      <!-- Zone Selection -->
      <ZoneSelector
        v-model="form.zone_id"
        :zones="zones"
        :errors="errors"
      />

      <!-- Observations -->
      <div class="form-group">
        <label>การสังเกตการณ์</label>
        <textarea 
          v-model="form.observations" 
          class="form-control"
          rows="4"
        ></textarea>
      </div>

      <!-- Notes -->
      <div class="form-group">
        <label>บันทึกเพิ่มเติม</label>
        <textarea 
          v-model="form.notes" 
          class="form-control"
          rows="4"
        ></textarea>
      </div>

      <!-- Images -->
      <div class="form-group">
        <label>รูปภาพ</label>
        <input 
          type="file" 
          @change="handleFileUpload" 
          multiple 
          accept="image/*"
          class="form-control"
        />
      </div>

      <!-- Submit Button -->
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> บันทึก
        </button>
        <button type="button" @click="cancel" class="btn btn-secondary">
          ยกเลิก
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import ZoneSelector from './ZoneSelector.vue';

export default {
  name: 'HomeVisitForm',
  
  components: {
    ZoneSelector
  },

  props: {
    student: {
      type: Object,
      required: true
    },
    zones: {
      type: Array,
      default: () => []
    }
  },

  data() {
    return {
      form: useForm({
        visit_date: new Date().toISOString().split('T')[0],
        visit_time: null,
        zone_id: null,
        observations: '',
        notes: '',
        images: []
      }),
      errors: {}
    }
  },

  methods: {
    handleFileUpload(event) {
      this.form.images = Array.from(event.target.files);
    },

    submitHomeVisit() {
      // Using Inertia.js
      this.form.post(route('homevisit.teacher.create.home.visit', this.student.id), {
        onSuccess: () => {
          this.$toast.success('บันทึกการเยี่ยมบ้านเรียบร้อยแล้ว');
        },
        onError: (errors) => {
          this.errors = errors;
          this.$toast.error('เกิดข้อผิดพลาด กรุณาตรวจสอบข้อมูล');
        }
      });
    },

    cancel() {
      this.$inertia.visit(route('homevisit.teacher.dashboard'));
    }
  }
}
</script>

<style scoped>
.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.text-muted {
  color: #6B7280;
  font-size: 0.875rem;
}

.text-danger {
  color: #EF4444;
}

.form-control {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid #D1D5DB;
  border-radius: 0.375rem;
  font-size: 1rem;
}

.form-control:focus {
  outline: none;
  border-color: #3B82F6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-control.is-invalid {
  border-color: #EF4444;
}

.invalid-feedback {
  color: #EF4444;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.zone-badge {
  margin-top: 0.5rem;
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}

.btn {
  padding: 0.625rem 1.25rem;
  border: none;
  border-radius: 0.375rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background-color: #3B82F6;
  color: white;
}

.btn-primary:hover {
  background-color: #2563EB;
}

.btn-secondary {
  background-color: #6B7280;
  color: white;
}

.btn-secondary:hover {
  background-color: #4B5563;
}
</style>

<!-- =========================================== -->
<!-- ตัวอย่างการแสดง Zone ใน Home Visit List -->
<!-- =========================================== -->

<template>
  <div class="home-visit-card">
    <div class="card-header">
      <h3>การเยี่ยมบ้านวันที่ {{ formatDate(visit.visit_date) }}</h3>
      <span 
        v-if="visit.zone" 
        class="zone-badge"
        :style="{ backgroundColor: visit.zone.color }"
      >
        <i class="fas fa-map-marker-alt"></i>
        {{ visit.zone.zone_name }}
      </span>
    </div>

    <div class="card-body">
      <p><strong>ผู้เยี่ยม:</strong> {{ visit.visitor_name }}</p>
      <p><strong>การสังเกตการณ์:</strong> {{ visit.observations }}</p>
      <p><strong>บันทึก:</strong> {{ visit.notes }}</p>
    </div>

    <div class="card-footer">
      <button @click="editVisit(visit.id)" class="btn btn-sm btn-primary">
        แก้ไข
      </button>
      <button @click="deleteVisit(visit.id)" class="btn btn-sm btn-danger">
        ลบ
      </button>
    </div>
  </div>
</template>

<!-- =========================================== -->
<!-- ตัวอย่าง Admin Zone Management -->
<!-- =========================================== -->

<template>
  <div class="zone-management">
    <div class="header">
      <h2>จัดการโซนการเยี่ยมบ้าน</h2>
      <button @click="showCreateModal = true" class="btn btn-primary">
        <i class="fas fa-plus"></i> เพิ่มโซนใหม่
      </button>
    </div>

    <!-- Zone List -->
    <div class="zone-list">
      <div 
        v-for="zone in zones.data" 
        :key="zone.id" 
        class="zone-item"
        :class="{ 'inactive': !zone.is_active }"
      >
        <div class="zone-info">
          <div 
            class="zone-color-indicator" 
            :style="{ backgroundColor: zone.color }"
          ></div>
          <div class="zone-details">
            <h4>{{ zone.zone_name }} ({{ zone.zone_code }})</h4>
            <p>{{ zone.description }}</p>
            <small>จำนวนการเยี่ยมบ้าน: {{ zone.home_visits_count }}</small>
          </div>
        </div>

        <div class="zone-actions">
          <button 
            @click="toggleStatus(zone.id)" 
            class="btn btn-sm"
            :class="zone.is_active ? 'btn-warning' : 'btn-success'"
          >
            {{ zone.is_active ? 'ปิดใช้งาน' : 'เปิดใช้งาน' }}
          </button>
          <button @click="editZone(zone)" class="btn btn-sm btn-primary">
            แก้ไข
          </button>
          <button 
            @click="deleteZone(zone.id)" 
            class="btn btn-sm btn-danger"
            :disabled="zone.home_visits_count > 0"
          >
            ลบ
          </button>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="pagination">
      <!-- Add pagination component here -->
    </div>

    <!-- Create/Edit Modal -->
    <ZoneModal
      v-if="showCreateModal"
      :zone="selectedZone"
      @close="showCreateModal = false"
      @saved="loadZones"
    />
  </div>
</template>

<script>
export default {
  name: 'ZoneManagement',
  
  data() {
    return {
      zones: { data: [] },
      showCreateModal: false,
      selectedZone: null,
      loading: false
    }
  },

  mounted() {
    this.loadZones();
  },

  methods: {
    async loadZones() {
      this.loading = true;
      try {
        const response = await axios.get('/home-visit/admin/zones');
        this.zones = response.data.zones;
      } catch (error) {
        console.error('Error loading zones:', error);
      } finally {
        this.loading = false;
      }
    },

    async toggleStatus(zoneId) {
      try {
        await axios.put(`/home-visit/admin/zones/${zoneId}/toggle-status`);
        this.$toast.success('อัพเดทสถานะเรียบร้อยแล้ว');
        this.loadZones();
      } catch (error) {
        this.$toast.error('เกิดข้อผิดพลาด');
      }
    },

    editZone(zone) {
      this.selectedZone = zone;
      this.showCreateModal = true;
    },

    async deleteZone(zoneId) {
      if (!confirm('คุณแน่ใจหรือไม่ที่จะลบโซนนี้?')) return;

      try {
        await axios.delete(`/home-visit/admin/zones/${zoneId}`);
        this.$toast.success('ลบโซนเรียบร้อยแล้ว');
        this.loadZones();
      } catch (error) {
        this.$toast.error(error.response?.data?.message || 'เกิดข้อผิดพลาด');
      }
    }
  }
}
</script>

<style scoped>
.zone-management {
  padding: 2rem;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.zone-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.zone-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  background: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.zone-item.inactive {
  opacity: 0.6;
}

.zone-info {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.zone-color-indicator {
  width: 4rem;
  height: 4rem;
  border-radius: 0.5rem;
}

.zone-details h4 {
  margin: 0 0 0.5rem 0;
}

.zone-details p {
  margin: 0 0 0.5rem 0;
  color: #6B7280;
}

.zone-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-sm {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
}
</style>
