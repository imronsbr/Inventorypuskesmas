<template>
  <AuthenticatedLayout>
    <Head title="Stok Barang Habis Pakai" />
    
    <div class="py-6">
      <div class="w-full">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h2 class="text-2xl font-bold text-gray-900">Stok Barang Habis Pakai</h2>
              <button @click="showCreateModal = true" class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Stok
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
                  <span class="label-text">Cari Barang</span>
                </label>
                <input 
                v-model="search" 
                  type="text" 
                placeholder="Nama atau kode barang..."
                  class="input input-bordered"
              />
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Kategori</span>
                </label>
                <select v-model="filterKategori" class="select select-bordered">
                  <option value="">Semua Kategori</option>
                  <option value="ATK">ATK</option>
                  <option value="ART">ART</option>
                  <option value="Alat Makan">Alat Makan</option>
                  <option value="Alat Laboratorium">Alat Laboratorium</option>
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
                        Nama Barang
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
                    <th @click="sortBy = 'stok_tersedia'; sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'" class="cursor-pointer hover:bg-gray-100">
                      <div class="flex items-center gap-2">
                        Stok Tersedia
                        <svg v-if="sortBy === 'stok_tersedia'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </div>
                    </th>
                    <th @click="sortBy = 'stok_minimum'; sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'" class="cursor-pointer hover:bg-gray-100">
                      <div class="flex items-center gap-2">
                        Stok Minimum
                        <svg v-if="sortBy === 'stok_minimum'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <th @click="sortBy = 'lokasi'; sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'" class="cursor-pointer hover:bg-gray-100">
                      <div class="flex items-center gap-2">
                        Lokasi
                        <svg v-if="sortBy === 'lokasi'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <td>{{ item.stok_tersedia }} {{ item.satuan }}</td>
                    <td>{{ item.stok_minimum }} {{ item.satuan }}</td>
                    <td>
                      <span class="badge" :class="getStatusClass(item.stok_tersedia, item.stok_minimum)">
                        {{ getStatusText(item.stok_tersedia, item.stok_minimum) }}
                      </span>
                    </td>
                    <td>{{ item.lokasi || '-' }}</td>
                    <td>
                      <div class="flex gap-2">
                        <div class="tooltip" data-tip="Edit stok">
                          <button @click="editItem(item)" class="btn btn-sm btn-outline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                          </button>
                        </div>
                        <div class="tooltip" data-tip="Tambah stok">
                          <button @click="addStock(item)" class="btn btn-sm btn-success">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                          </button>
                        </div>
                        <div class="tooltip" data-tip="Hapus stok">
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
        <h3 class="text-lg font-semibold mb-4">{{ editingItem ? 'Edit' : 'Tambah' }} Stok</h3>
        <form @submit.prevent="showSaveModal = true">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
              <label class="label">
                <span class="label-text">Barang</span>
              </label>
              <select v-model="form.barang_id" class="select select-bordered" required>
                <option value="">Pilih Barang</option>
                <option v-for="barang in barangList" :key="barang.id" :value="barang.id">
                  {{ barang.nama_barang }}
                </option>
              </select>
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Stok Tersedia</span>
              </label>
              <input 
              v-model="form.stok_tersedia" 
              type="number"
                placeholder="0" 
                class="input input-bordered" 
              required
            />
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Stok Minimum</span>
              </label>
              <input 
              v-model="form.stok_minimum" 
              type="number"
                placeholder="0" 
                class="input input-bordered" 
              required
            />
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
        <p class="mb-6">Apakah Anda yakin ingin menghapus stok barang <strong>{{ itemToDelete?.nama_barang }}</strong>?</p>
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
        <p class="mb-6">{{ editingItem ? 'Apakah Anda yakin ingin menyimpan perubahan data stok?' : 'Apakah Anda yakin ingin menyimpan data stok baru?' }}</p>
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
const barangList = ref([])

// Form
const form = ref({
  barang_id: '',
  stok_tersedia: '',
  stok_minimum: '',
  lokasi: '',
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

    if (sortBy.value === 'stok_tersedia' || sortBy.value === 'stok_minimum') {
      return sortDirection.value === 'asc' ? aValue - bValue : bValue - aValue;
    }
    return sortDirection.value === 'asc' ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
  });

  return data
})

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  const end = start + perPage.value;
  return filteredData.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(filteredData.value.length / perPage.value)
})

// Methods
const getStatusClass = (stokTersedia, stokMinimum) => {
  if (stokTersedia <= stokMinimum) {
    return 'badge-warning'
  } else if (stokTersedia === 0) {
    return 'badge-error'
  } else {
    return 'badge-success'
  }
}

const getStatusText = (stokTersedia, stokMinimum) => {
  if (stokTersedia <= stokMinimum) {
    return 'Stok Menipis'
  } else if (stokTersedia === 0) {
    return 'Habis'
  } else {
    return 'Tersedia'
  }
}

const editItem = (item) => {
  editingItem.value = item
  form.value = { ...item }
  showCreateModal.value = true
}

const addStock = (item) => {
  editingItem.value = item
  form.value = {
    barang_id: item.id,
    stok_tersedia: 0,
    stok_minimum: item.stok_minimum,
    lokasi: item.lokasi,
    keterangan: ''
  }
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
    toast.success('Data stok berhasil dihapus.', 'Berhasil')
    closeDeleteModal()
  }
}

const closeCreateModal = () => {
  showCreateModal.value = false
  editingItem.value = null
  form.value = {
    barang_id: '',
    stok_tersedia: '',
    stok_minimum: '',
    lokasi: '',
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
  
  toast.success(editingItem.value ? 'Data stok berhasil diperbarui.' : 'Data stok berhasil disimpan.', 'Berhasil')
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
      kode_barang: 'HPK001',
      nama_barang: 'Kertas A4',
      kategori: 'ATK',
      stok_tersedia: 50,
      stok_minimum: 20,
      satuan: 'Rim',
      lokasi: 'Gudang ATK',
      status: 'Tersedia'
    },
    {
      id: 2,
      kode_barang: 'HPK002',
      nama_barang: 'Tinta Printer HP',
      kategori: 'ATK',
      stok_tersedia: 5,
      stok_minimum: 10,
      satuan: 'Botol',
      lokasi: 'Gudang ATK',
      status: 'Stok Menipis'
    },
    {
      id: 3,
      kode_barang: 'HPK003',
      nama_barang: 'Sabun Cuci',
      kategori: 'ART',
      stok_tersedia: 0,
      stok_minimum: 15,
      satuan: 'Botol',
      lokasi: 'Gudang ART',
      status: 'Habis'
    }
  ]
  
  barangList.value = [
    { id: 1, nama_barang: 'Kertas A4' },
    { id: 2, nama_barang: 'Tinta Printer HP' },
    { id: 3, nama_barang: 'Sabun Cuci' }
  ]
})
</script> 