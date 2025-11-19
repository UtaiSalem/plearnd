import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

export function useVisitReports(allVisits, zones) {
  const showFilters = ref(false)
  const showDetailModal = ref(false)
  const selectedVisit = ref(null)
  const isExporting = ref(false)
  const currentPage = ref(1)
  const perPage = ref(25)
  
  const filters = ref({
    startDate: '',
    endDate: '',
    status: '',
    zoneId: '',
    studentName: ''
  })

  // Computed - Filtered Visits
  const filteredVisits = computed(() => {
    let result = [...allVisits.value]

    if (filters.value.startDate) {
      result = result.filter(visit => 
        new Date(visit.visit_date) >= new Date(filters.value.startDate)
      )
    }
    if (filters.value.endDate) {
      result = result.filter(visit => 
        new Date(visit.visit_date) <= new Date(filters.value.endDate)
      )
    }
    if (filters.value.status) {
      result = result.filter(visit => visit.visit_status === filters.value.status)
    }
    if (filters.value.zoneId) {
      result = result.filter(visit => visit.zone_id == filters.value.zoneId)
    }
    if (filters.value.studentName) {
      const searchTerm = filters.value.studentName.toLowerCase()
      result = result.filter(visit => {
        const student = visit.student
        if (!student) return false
        const fullName = `${student.first_name_th || student.first_name || ''} ${student.last_name_th || student.last_name || ''}`.toLowerCase()
        return fullName.includes(searchTerm)
      })
    }

    return result.sort((a, b) => new Date(b.visit_date) - new Date(a.visit_date))
  })

  const totalPages = computed(() => {
    return Math.ceil(filteredVisits.value.length / perPage.value)
  })

  const paginatedVisits = computed(() => {
    const start = (currentPage.value - 1) * perPage.value
    const end = start + perPage.value
    return filteredVisits.value.slice(start, end)
  })

  // Methods
  const toggleFilters = () => {
    showFilters.value = !showFilters.value
  }

  const clearFilters = () => {
    filters.value = {
      startDate: '',
      endDate: '',
      status: '',
      zoneId: '',
      studentName: ''
    }
    currentPage.value = 1
  }

  const previousPage = () => {
    if (currentPage.value > 1) {
      currentPage.value--
    }
  }

  const nextPage = () => {
    if (currentPage.value < totalPages.value) {
      currentPage.value++
    }
  }

  const viewVisitDetails = (visit) => {
    selectedVisit.value = visit
    showDetailModal.value = true
  }

  const closeDetailModal = () => {
    showDetailModal.value = false
    selectedVisit.value = null
  }

  const exportToExcel = async () => {
    isExporting.value = true
    try {
      const response = await axios.post('/api/home-visit/admin/visits/export/excel', {
        filters: filters.value,
        visits: filteredVisits.value.map(v => v.id)
      }, {
        responseType: 'blob'
      })
      
      const url = window.URL.createObjectURL(new Blob([response.data]))
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', `home-visits-${new Date().toISOString().split('T')[0]}.xlsx`)
      document.body.appendChild(link)
      link.click()
      link.remove()
    } catch (error) {
      console.error('Export failed:', error)
      alert('เกิดข้อผิดพลาดในการส่งออก: ' + (error.response?.data?.message || error.message))
    } finally {
      isExporting.value = false
    }
  }

  const getStudentFullName = (student) => {
    if (!student) return 'ไม่ระบุ'
    return `${student.first_name_th || student.first_name || ''} ${student.last_name_th || student.last_name || ''}`.trim() || 'ไม่ระบุชื่อ'
  }

  const getStudentInitial = (student) => {
    if (!student) return 'N'
    const firstName = student.first_name_th || student.first_name || ''
    return firstName.charAt(0).toUpperCase() || 'N'
  }

  return {
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
    getStudentInitial
  }
}
