<template>
  <AuthenticatedLayout>
    <Head title="Monitoring Alkes" />
    
    <div class="py-6">
      <div class="w-full">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h2 class="text-2xl font-bold text-gray-900">Monitoring Alkes</h2>
              <button @click="showCreateModal = true" class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Alkes
              </button>
            </div>
          </div>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Cari Alkes</span>
                </label>
                <input 
                v-model="search" 
                  type="text" 
                placeholder="Nama atau kode alkes..."
                  class="input input-bordered"
              />
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Kategori</span>
                </label>
                <select v-model="filterKategori" class="select select-bordered">
                  <option value="">Semua Kategori</option>
                  <option value="Diagnostik">Diagnostik</option>
                  <option value="Terapi">Terapi</option>
                  <option value="Monitoring">Monitoring</option>
                  <option value="Laboratorium">Laboratorium</option>
                  <option value="Lainnya">Lainnya</option>
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
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr>
                    <th @click="sortBy = 'kode_barang'; sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'" class="cursor-pointer hover:bg-gray-100">
                      <div class="flex items-center gap-2">
                        Kode
                        <svg v-if="sortBy === 'kode_barang'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </div>
                    </th>
                    <th @click="sortBy = 'nama_barang'; sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'" class="cursor-pointer hover:bg-gray-100">
                      <div class="flex items-center gap-2">
                        Nama Alkes
                        <svg v-if="sortBy === 'nama_barang'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </div>
                    </th>
                    <th @click="sortBy = 'kategori'; sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'" class="cursor-pointer hover:bg-gray-100">
                      <div class="flex items-center gap-2">
                        Kategori
                        <svg v-if="sortBy === 'kategori'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </div>
                    </th>
                    <th @click="sortBy = 'kondisi'; sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'" class="cursor-pointer hover:bg-gray-100">
                      <div class="flex items-center gap-2">
                        Kondisi
                        <svg v-if="sortBy === 'kondisi'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </div>
                    </th>
                    <th @click="sortBy = 'lokasi'; sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'" class="cursor-pointer hover:bg-gray-100">
                      <div class="flex items-center gap-2">
                        Lokasi
                        <svg v-if="sortBy === 'lokasi'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </div>
                    </th>
                    <th @click="sortBy = 'tanggal_maintenance'; sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'" class="cursor-pointer hover:bg-gray-100">
                      <div class="flex items-center gap-2">
                        Maintenance Terakhir
                        <svg v-if="sortBy === 'tanggal_maintenance'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in paginatedData" :key="item.id">
                    <td>{{ item.kode_barang }}</td>
                    <td>{{ item.nama_barang }}</td>
                    <td>
                      <span class="badge badge-outline">{{ item.kategori }}</span>
                    </td>
                    <td>
                      <span class="badge" :class="getConditionClass(item.kondisi)">
                        {{ item.kondisi }}
                      </span>
                    </td>
                    <td>{{ item.lokasi || '-' }}</td>
                    <td>{{ formatDate(item.tanggal_maintenance) }}</td>
                    <td>
                      <span class="badge" :class="getStatusClass(item.status)">
                        {{ item.status }}
                      </span>
                    </td>
                    <td>
                      <div class="flex gap-2">
                        <div class="tooltip" data-tip="Edit alkes">
                          <button @click="editItem(item)" class="btn btn-sm btn-outline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                          </button>
                        </div>
                        <div class="tooltip" data-tip="Maintenance alkes">
                          <button @click="maintenanceItem(item)" class="btn btn-sm btn-warning">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                          </button>
                        </div>
                        <div class="tooltip" data-tip="Hapus alkes">
                          <button @click="openDeleteModal(item)" class="btn btn-sm btn-error">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="text-lg font-semibold mb-4">{{ editingItem ? 'Edit' : 'Tambah' }} Alkes</h3>
        <form @submit.prevent="showSaveModal = true">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
              <label class="label">
                <span class="label-text">Kode Alkes</span>
              </label>
              <input 
              v-model="form.kode_barang" 
                type="text" 
                placeholder="Masukkan kode alkes..." 
                class="input input-bordered" 
              required
            />
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Nama Alkes</span>
              </label>
              <input 
              v-model="form.nama_barang" 
                type="text" 
                placeholder="Masukkan nama alkes..." 
                class="input input-bordered" 
              required
            />
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Kategori</span>
              </label>
              <select v-model="form.kategori" class="select select-bordered" required>
                <option value="">Pilih Kategori</option>
                <option value="Diagnostik">Diagnostik</option>
                <option value="Terapi">Terapi</option>
                <option value="Monitoring">Monitoring</option>
                <option value="Laboratorium">Laboratorium</option>
                <option value="Lainnya">Lainnya</option>
              </select>
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Kondisi</span>
              </label>
              <select v-model="form.kondisi" class="select select-bordered" required>
                <option value="">Pilih Kondisi</option>
                <option value="Baik">Baik</option>
                <option value="Rusak Ringan">Rusak Ringan</option>
                <option value="Rusak Berat">Rusak Berat</option>
              </select>
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Lokasi</span>
              </label>
              <input 
              v-model="form.lokasi" 
                type="text" 
                placeholder="Masukkan lokasi..." 
                class="input input-bordered"
            />
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Tanggal Maintenance</span>
              </label>
              <input 
              v-model="form.tanggal_maintenance" 
              type="date"
                class="input input-bordered"
            />
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Status</span>
              </label>
              <select v-model="form.status" class="select select-bordered" required>
                <option value="">Pilih Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Nonaktif">Nonaktif</option>
                <option value="Maintenance">Maintenance</option>
              </select>
            </div>
            <div class="form-control md:col-span-2">
              <label class="label">
                <span class="label-text">Keterangan</span>
              </label>
              <textarea 
              v-model="form.keterangan" 
                placeholder="Tulis keterangan..." 
                class="textarea textarea-bordered" 
                rows="3"
              ></textarea>
            </div>
          </div>
          <div class="flex justify-end gap-2 mt-6">
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
        <p class="mb-6">Apakah Anda yakin ingin menghapus alkes <strong>{{ itemToDelete?.nama_barang }}</strong>?</p>
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
        <p class="mb-6">{{ editingItem ? 'Apakah Anda yakin ingin menyimpan perubahan data alkes?' : 'Apakah Anda yakin ingin menyimpan data alkes baru?' }}</p>
        <div class="modal-action">
          <button @click="closeSaveModal" class="btn btn-outline">Batal</button>
          <button @click="confirmSave" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

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
const filterKategori = ref('')
const sortBy = ref('nama_barang')
const sortDirection = ref('asc') // 'asc' or 'desc'
const showCreateModal = ref(false)
const showDeleteModal = ref(false)
const showSaveModal = ref(false)
const editingItem = ref(null)
const itemToDelete = ref(null)

// Form
const form = ref({
  kode_barang: '',
  nama_barang: '',
  kategori: '',
  kondisi: '',
  lokasi: '',
  tanggal_maintenance: '',
  status: '',
  keterangan: ''
})

// Computed
const filteredData = computed(() => {
  let data = items.value || [];

  if (search.value) {
    data = data.filter(item => 
      item.nama_barang.toLowerCase().includes(search.value.toLowerCase()) ||
      item.kode_barang.toLowerCase().includes(search.value.toLowerCase())
    )
  }

  if (filterKategori.value) {
    data = data.filter(item => item.kategori === filterKategori.value)
  }

  // Sort
  data.sort((a, b) => {
    const aValue = a[sortBy.value];
    const bValue = b[sortBy.value];

    if (sortBy.value === 'tanggal_maintenance') {
      return sortDirection.value === 'asc' ? new Date(aValue) - new Date(bValue) : new Date(bValue) - new Date(aValue);
    }
    return sortDirection.value === 'asc' ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
  });

  return data
})

const totalPages = computed(() => Math.ceil(filteredData.value.length / perPage.value));

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  const end = start + perPage.value;
  return filteredData.value.slice(start, end);
});

// Methods
const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID')
}

const getConditionClass = (kondisi) => {
  switch (kondisi) {
    case 'Baik':
      return 'badge-success'
    case 'Rusak Ringan':
      return 'badge-warning'
    case 'Rusak Berat':
      return 'badge-error'
    default:
      return 'badge-outline'
  }
}

const getStatusClass = (status) => {
  switch (status) {
    case 'Aktif':
      return 'badge-success'
    case 'Nonaktif':
      return 'badge-error'
    case 'Maintenance':
      return 'badge-warning'
    default:
      return 'badge-outline'
  }
}

const editItem = (item) => {
  editingItem.value = item
  form.value = { ...item }
  showCreateModal.value = true
}

const maintenanceItem = (item) => {
  editingItem.value = item
  form.value = { ...item }
  form.value.status = 'Maintenance'
  showCreateModal.value = true
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
    toast.success('Data alkes berhasil dihapus.', 'Berhasil')
    closeDeleteModal()
  }
}

const closeCreateModal = () => {
  showCreateModal.value = false
  editingItem.value = null
  form.value = {
    kode_barang: '',
    nama_barang: '',
    kategori: '',
    kondisi: '',
    lokasi: '',
    tanggal_maintenance: '',
    status: '',
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
      ...form.value
    })
  }
  
  toast.success(editingItem.value ? 'Data alkes berhasil diperbarui.' : 'Data alkes berhasil disimpan.', 'Berhasil')
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
      kode_barang: 'ALK001',
      nama_barang: 'Tensimeter Digital',
      kategori: 'Diagnostik',
      kondisi: 'Baik',
      lokasi: 'Ruang Poli Umum',
      tanggal_maintenance: '2024-01-15',
      status: 'Aktif',
      keterangan: 'Alat untuk mengukur tekanan darah'
    },
    {
      id: 2,
      kode_barang: 'ALK002',
      nama_barang: 'Stetoskop',
      kategori: 'Diagnostik',
      kondisi: 'Baik',
      lokasi: 'Ruang Poli Umum',
      tanggal_maintenance: '2024-01-10',
      status: 'Aktif',
      keterangan: 'Alat untuk mendengarkan suara jantung'
    },
    {
      id: 3,
      kode_barang: 'ALK003',
      nama_barang: 'Nebulizer',
      kategori: 'Terapi',
      kondisi: 'Rusak Ringan',
      lokasi: 'Ruang Terapi',
      tanggal_maintenance: '2024-01-20',
      status: 'Maintenance',
      keterangan: 'Alat untuk terapi inhalasi'
    }
  ]
})
</script> 