<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useToast } from '@/composables/useToast.js'

// Data
const items = ref([])
const currentPage = ref(1)
const perPage = ref(10)
const search = ref('')
const filterStatus = ref('')
const sortBy = ref('tanggal_permintaan')
const sortDirection = ref('desc') // 'asc' or 'desc'
const showCreateModal = ref(false)
const showDeleteModal = ref(false)
const showSaveModal = ref(false)
const showApproveModal = ref(false)
const showRejectModal = ref(false)
const editingItem = ref(null)
const itemToDelete = ref(null)
const itemToApprove = ref(null)
const itemToReject = ref(null)

// Form
const form = ref({
  nomor_permintaan: '',
  tanggal_permintaan: '',
  nama_pemohon: '',
  ruang: '',
  keterangan: ''
})

// Computed
const filteredData = computed(() => {
  let data = items.value || [];

  if (search.value) {
    data = data.filter(item => 
      item.nomor_permintaan.toLowerCase().includes(search.value.toLowerCase()) ||
      item.nama_pemohon.toLowerCase().includes(search.value.toLowerCase())
    )
  }

  if (filterStatus.value) {
    data = data.filter(item => item.status === filterStatus.value)
  }

  // Sort
  data.sort((a, b) => {
    const aValue = a[sortBy.value];
    const bValue = b[sortBy.value];

    if (sortBy.value === 'tanggal_permintaan' || sortBy.value === 'tanggal_approval') {
      return sortDirection.value === 'asc' ? new Date(aValue) - new Date(bValue) : new Date(bValue) - new Date(aValue);
    }
    if (sortBy.value === 'jumlah_item') {
      return sortDirection.value === 'asc' ? aValue - bValue : bValue - aValue;
    }
    return sortDirection.value === 'asc' ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
  });

  return data
})

const totalPages = computed(() => Math.ceil(filteredData.value.length / perPage.value))

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  const end = start + perPage.value
  return filteredData.value.slice(start, end)
})

// Methods
const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID')
}

const getStatusClass = (status) => {
  switch (status) {
    case 'pending':
      return 'badge-warning'
    case 'approved':
      return 'badge-success'
    case 'rejected':
      return 'badge-error'
    case 'completed':
      return 'badge-info'
    default:
      return 'badge-outline'
  }
}

const getStatusText = (status) => {
  switch (status) {
    case 'pending':
      return 'Pending'
    case 'approved':
      return 'Disetujui'
    case 'rejected':
      return 'Ditolak'
    case 'completed':
      return 'Selesai'
    default:
      return status
  }
}

const viewDetail = (item) => {
  // Implement view detail functionality
  console.log('View detail:', item)
}

const openApproveModal = (item) => {
  itemToApprove.value = item
  showApproveModal.value = true
}

const closeApproveModal = () => {
  showApproveModal.value = false
  itemToApprove.value = null
}

const confirmApprove = () => {
  if (itemToApprove.value) {
    itemToApprove.value.status = 'approved'
    itemToApprove.value.tanggal_approval = new Date().toISOString().split('T')[0]
    toast.success('Permintaan berhasil disetujui.', 'Berhasil')
    closeApproveModal()
  }
}

const openRejectModal = (item) => {
  itemToReject.value = item
  showRejectModal.value = true
}

const closeRejectModal = () => {
  showRejectModal.value = false
  itemToReject.value = null
}

const confirmReject = () => {
  if (itemToReject.value) {
    itemToReject.value.status = 'rejected'
    itemToReject.value.tanggal_approval = new Date().toISOString().split('T')[0]
    toast.success('Permintaan berhasil ditolak.', 'Berhasil')
    closeRejectModal()
  }
}

const openDeleteModal = (item) => {
  itemToDelete.value = item
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  itemToDelete.value = null
}

const confirmDelete = () => {
  if (itemToDelete.value) {
    items.value = items.value.filter(i => i.id !== itemToDelete.value.id)
    toast.success('Permintaan berhasil dihapus.', 'Berhasil')
    closeDeleteModal()
  }
}

const closeCreateModal = () => {
  showCreateModal.value = false
  editingItem.value = null
  form.value = {
    nomor_permintaan: '',
    tanggal_permintaan: '',
    nama_pemohon: '',
    ruang: '',
    keterangan: ''
  }
}

const closeSaveModal = () => {
  showSaveModal.value = false
}

const confirmSave = () => {
  if (editingItem.value) {
    // Update existing item
    const index = items.value.findIndex(i => i.id === editingItem.value.id)
    items.value[index] = { ...form.value }
  } else {
    // Add new item
    items.value.push({
      id: Date.now(),
      ...form.value,
      status: 'pending',
      jumlah_item: 0,
      tanggal_approval: null
    })
  }
  
  toast.success(editingItem.value ? 'Permintaan berhasil diperbarui.' : 'Permintaan berhasil dibuat.', 'Berhasil')
  closeCreateModal()
  closeSaveModal()
}

const toast = useToast()

// Inertia event listeners for toastification
onMounted(() => {
  // Listen for Inertia success events
  router.on('success', (event) => {
    if (event.detail.page.props.flash?.success) {
      toast.success(event.detail.page.props.flash.success, 'Berhasil')
    }
  })

  // Listen for Inertia error events
  router.on('error', (event) => {
    if (event.detail.page.props.flash?.error) {
      toast.error(event.detail.page.props.flash.error, 'Gagal')
    }
  })

  // Load sample data
  items.value = [
    {
      id: 1,
      nomor_permintaan: 'PRM001',
      tanggal_permintaan: '2024-01-15',
      nama_pemohon: 'Dr. Sarah',
      ruang: 'Ruang Poli Umum',
      jumlah_item: 5,
      status: 'approved',
      tanggal_approval: '2024-01-16',
      keterangan: 'Permintaan untuk stok ATK'
    },
    {
      id: 2,
      nomor_permintaan: 'PRM002',
      tanggal_permintaan: '2024-01-17',
      nama_pemohon: 'Dr. Ahmad',
      ruang: 'Ruang Laboratorium',
      jumlah_item: 3,
      status: 'pending',
      tanggal_approval: null,
      keterangan: 'Permintaan untuk alat laboratorium'
    },
    {
      id: 3,
      nomor_permintaan: 'PRM003',
      tanggal_permintaan: '2024-01-18',
      nama_pemohon: 'Dr. Maria',
      ruang: 'Ruang Farmasi',
      jumlah_item: 8,
      status: 'completed',
      tanggal_approval: '2024-01-19',
      keterangan: 'Permintaan untuk obat-obatan'
    }
  ]
})
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Permintaan Barang" />
    
    <div class="py-6">
      <div class="w-full">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Permintaan Barang</h2>
              <div class="tooltip" data-tip="Buat permintaan barang baru">
                <button @click="showCreateModal = true" class="btn btn-primary btn-sm sm:btn-md">
                  <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  <span class="hidden sm:inline">Buat Permintaan</span>
                  <span class="sm:hidden">Buat</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Cari Permintaan</span>
                </label>
                <input 
                v-model="search" 
                  type="text" 
                placeholder="Nomor atau nama pemohon..."
                  class="input input-bordered"
              />
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Status</span>
                </label>
                <select v-model="filterStatus" class="select select-bordered">
                  <option value="">Semua Status</option>
                  <option value="pending">Pending</option>
                  <option value="approved">Disetujui</option>
                  <option value="rejected">Ditolak</option>
                  <option value="completed">Selesai</option>
                </select>
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Per Halaman</span>
                </label>
                <select v-model="perPage" class="select select-bordered">
                  <option value="10">10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr>
                    <th @click="sortBy = 'nomor_permintaan'; sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'" class="cursor-pointer hover:bg-gray-100">
                      <div class="flex items-center gap-2">
                        <span class="hidden sm:inline">Nomor</span>
                        <span class="sm:hidden">No</span>
                        <svg v-if="sortBy === 'nomor_permintaan'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </div>
                    </th>
                    <th @click="sortBy = 'tanggal_permintaan'; sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'" class="cursor-pointer hover:bg-gray-100">
                      <div class="flex items-center gap-2">
                        <span class="hidden sm:inline">Tanggal</span>
                        <span class="sm:hidden">Tgl</span>
                        <svg v-if="sortBy === 'tanggal_permintaan'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </div>
                    </th>
                    <th @click="sortBy = 'nama_pemohon'; sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'" class="cursor-pointer hover:bg-gray-100">
                      <div class="flex items-center gap-2">
                        <span class="hidden sm:inline">Pemohon</span>
                        <span class="sm:hidden">Pem</span>
                        <svg v-if="sortBy === 'nama_pemohon'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </div>
                    </th>
                    <th class="hidden md:table-cell cursor-pointer hover:bg-gray-100" @click="sortBy = 'ruang'; sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'">
                      <div class="flex items-center gap-2">
                        Ruang
                        <svg v-if="sortBy === 'ruang'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </div>
                    </th>
                    <th @click="sortBy = 'jumlah_item'; sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'" class="cursor-pointer hover:bg-gray-100">
                      <div class="flex items-center gap-2">
                        <span class="hidden sm:inline">Jumlah Item</span>
                        <span class="sm:hidden">Item</span>
                        <svg v-if="sortBy === 'jumlah_item'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </div>
                    </th>
                    <th @click="sortBy = 'status'; sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'" class="cursor-pointer hover:bg-gray-100">
                      <div class="flex items-center gap-2">
                        Status
                        <svg v-if="sortBy === 'status'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </div>
                    </th>
                    <th class="hidden lg:table-cell cursor-pointer hover:bg-gray-100" @click="sortBy = 'tanggal_approval'; sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'">
                      <div class="flex items-center gap-2">
                        Tanggal Approval
                        <svg v-if="sortBy === 'tanggal_approval'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </div>
                    </th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in paginatedData" :key="item.id">
                    <td class="text-sm">{{ item.nomor_permintaan }}</td>
                    <td class="text-sm">{{ formatDate(item.tanggal_permintaan) }}</td>
                    <td class="text-sm">{{ item.nama_pemohon }}</td>
                    <td class="hidden md:table-cell text-sm">{{ item.ruang }}</td>
                    <td class="text-sm">{{ item.jumlah_item }} item</td>
                    <td>
                      <span class="badge" :class="getStatusClass(item.status)">
                        {{ getStatusText(item.status) }}
                      </span>
                    </td>
                    <td class="hidden lg:table-cell text-sm">{{ item.tanggal_approval ? formatDate(item.tanggal_approval) : '-' }}</td>
                    <td>
                      <div class="flex gap-1 sm:gap-2">
                        <div class="tooltip" data-tip="Lihat detail">
                          <button @click="viewDetail(item)" class="btn btn-xs sm:btn-sm btn-info">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                          </button>
                        </div>
                        <div v-if="item.status === 'pending'" class="tooltip" data-tip="Setujui permintaan">
                          <button @click="openApproveModal(item)" class="btn btn-xs sm:btn-sm btn-success">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                          </button>
                        </div>
                        <div v-if="item.status === 'pending'" class="tooltip" data-tip="Tolak permintaan">
                          <button @click="openRejectModal(item)" class="btn btn-xs sm:btn-sm btn-error">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                          </button>
                        </div>
                        <div class="tooltip" data-tip="Hapus permintaan">
                          <button @click="openDeleteModal(item)" class="btn btn-xs sm:btn-sm btn-error">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                          </button>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 mt-6">
              <div class="text-sm text-center sm:text-left">
                Menampilkan {{ (currentPage - 1) * perPage + 1 }} - {{ Math.min(currentPage * perPage, paginatedData.length) }} dari {{ paginatedData.length }} data
              </div>
              <div class="join">
                <button class="join-item btn btn-sm" :disabled="currentPage === 1" @click="currentPage--">«</button>
                <button class="join-item btn btn-sm" v-for="page in totalPages" :key="page" 
                        :class="{ 'btn-active': page === currentPage }" @click="currentPage = page">
                  {{ page }}
                </button>
                <button class="join-item btn btn-sm" :disabled="currentPage === totalPages" @click="currentPage++">»</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Modal -->
    <div v-if="showCreateModal" class="modal modal-open">
      <div class="modal-box w-11/12 max-w-2xl">
        <h3 class="text-lg font-semibold mb-4">Buat Permintaan Barang</h3>
        <form @submit.prevent="showSaveModal = true">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text">Nomor Permintaan</span>
              </label>
              <input v-model="form.nomor_permintaan" type="text" class="input input-bordered w-full" required />
            </div>
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text">Tanggal Permintaan</span>
              </label>
              <input v-model="form.tanggal_permintaan" type="date" class="input input-bordered w-full" required />
            </div>
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text">Nama Pemohon</span>
              </label>
              <input v-model="form.nama_pemohon" type="text" class="input input-bordered w-full" required />
            </div>
            <div class="form-control w-full">
              <label class="label">
                <span class="label-text">Ruang</span>
              </label>
              <select v-model="form.ruang" class="select select-bordered w-full" required>
                <option value="">Pilih Ruang</option>
                <option value="Ruang Poli Umum">Ruang Poli Umum</option>
                <option value="Ruang Poli Gigi">Ruang Poli Gigi</option>
                <option value="Ruang Laboratorium">Ruang Laboratorium</option>
                <option value="Ruang Farmasi">Ruang Farmasi</option>
                <option value="Ruang Administrasi">Ruang Administrasi</option>
              </select>
            </div>
            <div class="form-control w-full sm:col-span-2">
              <label class="label">
                <span class="label-text">Keterangan</span>
                <span class="label-text-alt">Opsional</span>
              </label>
              <textarea v-model="form.keterangan" rows="3" class="textarea textarea-bordered w-full"></textarea>
            </div>
          </div>
          <div class="modal-action flex flex-col sm:flex-row justify-end gap-2 mt-6">
            <button type="button" @click="closeCreateModal" class="btn btn-outline">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="text-lg font-semibold mb-4">Konfirmasi Hapus</h3>
        <p class="mb-6">Apakah Anda yakin ingin menghapus permintaan <strong>{{ itemToDelete?.nomor_permintaan }}</strong>?</p>
        <div class="modal-action">
          <button @click="closeDeleteModal" class="btn btn-outline">Batal</button>
          <button @click="confirmDelete" class="btn btn-error">Hapus</button>
        </div>
      </div>
    </div>

    <!-- Save Confirmation Modal -->
    <div v-if="showSaveModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="text-lg font-semibold mb-4">Konfirmasi Simpan</h3>
        <p class="mb-6">{{ editingItem ? 'Apakah Anda yakin ingin menyimpan perubahan permintaan?' : 'Apakah Anda yakin ingin menyimpan permintaan baru?' }}</p>
        <div class="modal-action">
          <button @click="closeSaveModal" class="btn btn-outline">Batal</button>
          <button @click="confirmSave" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </div>

    <!-- Approve Confirmation Modal -->
    <div v-if="showApproveModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="text-lg font-semibold mb-4">Konfirmasi Setujui</h3>
        <p class="mb-6">Apakah Anda yakin ingin menyetujui permintaan <strong>{{ itemToApprove?.nomor_permintaan }}</strong>?</p>
        <div class="modal-action">
          <button @click="closeApproveModal" class="btn btn-outline">Batal</button>
          <button @click="confirmApprove" class="btn btn-success">Setujui</button>
        </div>
      </div>
    </div>

    <!-- Reject Confirmation Modal -->
    <div v-if="showRejectModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="text-lg font-semibold mb-4">Konfirmasi Tolak</h3>
        <p class="mb-6">Apakah Anda yakin ingin menolak permintaan <strong>{{ itemToReject?.nomor_permintaan }}</strong>?</p>
        <div class="modal-action">
          <button @click="closeRejectModal" class="btn btn-outline">Batal</button>
          <button @click="confirmReject" class="btn btn-error">Tolak</button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template> 