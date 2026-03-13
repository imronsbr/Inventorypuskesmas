<template>
  <AuthenticatedLayout>
    <Head title="Approval Permintaan Barang" />
    
    <div class="py-6">
      <div class="w-full">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h2 class="text-2xl font-bold text-gray-900">Approval Permintaan Barang</h2>
              <div class="tooltip" data-tip="Refresh data permintaan">
                <button @click="refreshData" class="btn btn-outline">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                </button>
              </div>
              <div class="flex gap-2">
                <div class="tooltip" data-tip="Setujui semua item yang dipilih">
                  <button @click="approveSelected" class="btn btn-success" :disabled="selectedItems.length === 0">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Approve Selected
                  </button>
                </div>
                <div class="tooltip" data-tip="Tolak semua item yang dipilih">
                  <button @click="rejectSelected" class="btn btn-error" :disabled="selectedItems.length === 0">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Reject Selected
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Cari Permintaan</span>
                </label>
                <input 
                v-model="search" 
                  type="text" 
                placeholder="Nomor atau pemohon..."
                  class="input input-bordered"
              />
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Status</span>
                </label>
                <select v-model="filterStatus" class="select select-bordered">
                  <option value="">Semua Status</option>
                  <option value="Pending">Pending</option>
                  <option value="Approved">Approved</option>
                  <option value="Rejected">Rejected</option>
                </select>
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Tanggal</span>
                </label>
                <input 
                v-model="filterDate" 
                type="date"
                  class="input input-bordered"
              />
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
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr>
                    <th>
                      <input type="checkbox" v-model="selectAll" @change="toggleSelectAll" class="checkbox" />
                    </th>
                    <th>No. Permintaan</th>
                    <th>Tanggal</th>
                    <th>Pemohon</th>
                    <th>Ruang</th>
                    <th>Jumlah Item</th>
                    <th>Status</th>
                    <th>Alasan</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in paginatedData" :key="item.id">
                    <td>
                      <input type="checkbox" v-model="selectedItems" :value="item.id" class="checkbox" />
                    </td>
                    <td>{{ item.nomor_permintaan }}</td>
                    <td>{{ formatDate(item.tanggal_permintaan) }}</td>
                    <td>{{ item.pemohon }}</td>
                    <td>{{ item.ruang }}</td>
                    <td>{{ item.jumlah_item }}</td>
                    <td>
                      <span :class="getStatusBadgeClass(item.status)" class="badge">
                        {{ item.status }}
                      </span>
                    </td>
                    <td>
                      <span class="truncate max-w-xs block" :title="item.alasan">
                        {{ item.alasan }}
                      </span>
                    </td>
                    <td>
                      <div class="flex gap-2">
                        <div class="tooltip" data-tip="Lihat detail">
                          <button @click="viewDetail(item)" class="btn btn-sm btn-info">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                          </button>
                        </div>
                        <div v-if="item.status === 'Pending'" class="tooltip" data-tip="Setujui permintaan">
                          <button @click="approveItem(item)" class="btn btn-sm btn-success">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                          </button>
                        </div>
                        <div v-if="item.status === 'Pending'" class="tooltip" data-tip="Tolak permintaan">
                          <button @click="rejectItem(item)" class="btn btn-sm btn-error">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
            <div class="flex justify-center mt-6">
              <div class="join">
                <button class="join-item btn" :disabled="currentPage === 1" @click="currentPage--">«</button>
                <button class="join-item btn" v-for="page in totalPages" :key="page" 
                        :class="{ 'btn-active': page === currentPage }" @click="currentPage = page">
                  {{ page }}
                </button>
                <button class="join-item btn" :disabled="currentPage === totalPages" @click="currentPage++">»</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <dialog :class="{ 'modal modal-open': showDetailModal }" @click.self="showDetailModal = false">
      <div class="modal-box">
        <h3 class="text-lg font-semibold mb-4">Detail Permintaan</h3>
        <div v-if="selectedItem" class="space-y-4">
          <!-- Header Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="label">
                <span class="label-text font-semibold">Nomor Permintaan</span>
              </label>
              <p>{{ selectedItem.nomor_permintaan }}</p>
            </div>
            <div>
              <label class="label">
                <span class="label-text font-semibold">Tanggal</span>
              </label>
              <p>{{ formatDate(selectedItem.tanggal_permintaan) }}</p>
            </div>
            <div>
              <label class="label">
                <span class="label-text font-semibold">Pemohon</span>
              </label>
              <p>{{ selectedItem.pemohon }}</p>
            </div>
            <div>
              <label class="label">
                <span class="label-text font-semibold">Ruang</span>
              </label>
              <p>{{ selectedItem.ruang }}</p>
            </div>
            <div>
              <label class="label">
                <span class="label-text font-semibold">Status</span>
              </label>
              <span :class="getStatusBadgeClass(selectedItem.status)" class="badge">
                {{ selectedItem.status }}
              </span>
            </div>
            <div>
              <label class="label">
                <span class="label-text font-semibold">Jumlah Item</span>
              </label>
              <p>{{ selectedItem.jumlah_item }}</p>
            </div>
          </div>

          <!-- Alasan -->
          <div>
            <label class="label">
              <span class="label-text font-semibold">Alasan Permintaan</span>
            </label>
            <p class="bg-gray-50 p-3 rounded">{{ selectedItem.alasan }}</p>
          </div>

          <!-- Daftar Barang -->
          <div>
            <label class="label">
              <span class="label-text font-semibold">Daftar Barang</span>
            </label>
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Keterangan</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in selectedItem.items" :key="item.id">
                    <td>{{ item.nama_barang }}</td>
                    <td>{{ item.jumlah }}</td>
                    <td>{{ item.satuan }}</td>
                    <td>{{ item.keterangan || '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Action Buttons -->
          <div v-if="selectedItem.status === 'Pending'" class="flex justify-end gap-2 pt-4">
            <div class="tooltip" data-tip="Setujui permintaan ini">
              <button @click="approveItem(selectedItem)" class="btn btn-success">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Approve
              </button>
            </div>
            <div class="tooltip" data-tip="Tolak permintaan ini">
              <button @click="rejectItem(selectedItem)" class="btn btn-error">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Reject
              </button>
            </div>
          </div>
        </div>
      </div>
    </dialog>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Data
const items = ref([])
const currentPage = ref(1)
const perPage = ref(10)
const search = ref('')
const filterStatus = ref('')
const filterDate = ref('')
const showDetailModal = ref(false)
const selectedItem = ref(null)
const selectedItems = ref([])
const selectAll = ref(false)

// Computed
const filteredData = computed(() => {
  let data = items.value || [];

  if (search.value) {
    data = data.filter(item => 
      item.nomor_permintaan.toLowerCase().includes(search.value.toLowerCase()) ||
      item.pemohon.toLowerCase().includes(search.value.toLowerCase())
    )
  }

  if (filterStatus.value) {
    data = data.filter(item => item.status === filterStatus.value)
  }

  if (filterDate.value) {
    data = data.filter(item => item.tanggal_permintaan === filterDate.value)
  }

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
  return new Date(date).toLocaleDateString('id-ID')
}

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'Pending':
      return 'badge-warning'
    case 'Approved':
      return 'badge-success'
    case 'Rejected':
      return 'badge-error'
    default:
      return 'badge-outline'
  }
}

const viewDetail = (item) => {
  selectedItem.value = item
  showDetailModal.value = true
}

const approveItem = (item) => {
  if (confirm(`Apakah Anda yakin ingin menyetujui permintaan ${item.nomor_permintaan}?`)) {
    item.status = 'Approved'
    selectedItems.value = selectedItems.value.filter(id => id !== item.id)
  }
}

const rejectItem = (item) => {
  const reason = prompt('Alasan penolakan:')
  if (reason !== null) {
    item.status = 'Rejected'
    item.alasan_penolakan = reason
    selectedItems.value = selectedItems.value.filter(id => id !== item.id)
  }
}

const approveSelected = () => {
  if (confirm(`Apakah Anda yakin ingin menyetujui ${selectedItems.value.length} permintaan?`)) {
    items.value.forEach(item => {
      if (selectedItems.value.includes(item.id)) {
        item.status = 'Approved'
      }
    })
    selectedItems.value = []
  }
}

const rejectSelected = () => {
  const reason = prompt('Alasan penolakan untuk semua item yang dipilih:')
  if (reason !== null) {
    items.value.forEach(item => {
      if (selectedItems.value.includes(item.id)) {
        item.status = 'Rejected'
        item.alasan_penolakan = reason
      }
    })
    selectedItems.value = []
  }
}

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedItems.value = paginatedData.value.map(item => item.id)
  } else {
    selectedItems.value = []
  }
}

const refreshData = () => {
  // In a real application, you would fetch new data from the server
  // For now, we'll just re-render the current data
  console.log('Refreshing data...')
}

// Load sample data
onMounted(() => {
  items.value = [
    {
      id: 1,
      nomor_permintaan: 'PB-20241201-001',
      tanggal_permintaan: '2024-12-01',
      pemohon: 'Dr. Sarah Johnson',
      ruang: 'Ruang Poli Umum',
      jumlah_item: 3,
      status: 'Pending',
      alasan: 'Kebutuhan rutin untuk pelayanan pasien',
      items: [
        { id: 1, nama_barang: 'Kertas A4', jumlah: 5, satuan: 'Rim', keterangan: '' },
        { id: 2, nama_barang: 'Tinta Printer HP', jumlah: 2, satuan: 'Botol', keterangan: 'Warna hitam' },
        { id: 3, nama_barang: 'Pulpen', jumlah: 20, satuan: 'Pcs', keterangan: '' }
      ]
    },
    {
      id: 2,
      nomor_permintaan: 'PB-20241201-002',
      tanggal_permintaan: '2024-12-01',
      pemohon: 'Suster Maria',
      ruang: 'Laboratorium',
      jumlah_item: 2,
      status: 'Approved',
      alasan: 'Penggantian alat yang rusak',
      items: [
        { id: 1, nama_barang: 'Sabun Cuci', jumlah: 3, satuan: 'Botol', keterangan: '' },
        { id: 2, nama_barang: 'Tissue', jumlah: 10, satuan: 'Roll', keterangan: 'Untuk laboratorium' }
      ]
    },
    {
      id: 3,
      nomor_permintaan: 'PB-20241130-003',
      tanggal_permintaan: '2024-11-30',
      pemohon: 'Budi Santoso',
      ruang: 'Ruang Administrasi',
      jumlah_item: 1,
      status: 'Rejected',
      alasan: 'Kebutuhan administrasi',
      items: [
        { id: 1, nama_barang: 'Staples', jumlah: 5, satuan: 'Box', keterangan: 'Untuk dokumen' }
      ]
    }
  ]
})
</script> 