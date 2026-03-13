<template>
  <AuthenticatedLayout>
    <Head title="Master Obat" />
    
    <div class="py-6">
      <div class="w-full">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Master Obat</h2>
              <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <div class="tooltip" data-tip="Tambah obat baru">
                  <button @click="showCreateModal = true" class="btn btn-primary btn-sm sm:btn-md">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span class="hidden sm:inline">Tambah Obat</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <div class="form-control">
                <div class="mb-2">
                  <span class="text-sm font-medium">Cari Obat</span>
                </div>
                <input
                  v-model="search"
                  type="text"
                  placeholder="Nama, kode, atau komposisi obat..."
                  class="input input-bordered w-full max-w-xs"
                />
              </div>
              <div class="form-control">
                <div class="mb-2">
                  <span class="text-sm font-medium">Kategori</span>
                </div>
                <select v-model="filterKategori" class="select select-bordered w-full max-w-xs">
                  <option value="">Semua Kategori</option>
                  <option v-for="kategori in kategoris" :key="kategori" :value="kategori">{{ kategori }}</option>
                </select>
              </div>
              <div class="form-control">
                <div class="mb-2">
                  <span class="text-sm font-medium">Bentuk</span>
                </div>
                <select v-model="filterBentuk" class="select select-bordered w-full max-w-xs">
                  <option value="">Semua Bentuk</option>
                  <option v-for="bentuk in bentuks" :key="bentuk" :value="bentuk">{{ bentuk }}</option>
                </select>
              </div>
              <div class="form-control">
                <div class="mb-2">
                  <span class="text-sm font-medium">Status Kondisi</span>
                </div>
                <select v-model="filterKondisi" class="select select-bordered w-full max-w-xs">
                  <option value="">Semua Kondisi</option>
                  <option value="tersedia">Tersedia</option>
                  <option value="stok_menipis">Stok Menipis</option>
                  <option value="habis">Habis</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
            <!-- Page Size Selector - Top Right -->
            <div class="flex justify-end mb-4">
              <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Tampilkan:</span>
                <select id="pageSize" class="select select-bordered select-sm w-20" :value="perPage" @change="e => changePerPage(Number(e.target.value))">
                  <option v-for="size in [10, 25, 50, 100]" :key="size" :value="size">{{ size }}</option>
                </select>
              </div>
            </div>
            
            <div class="overflow-x-auto">
              <div v-if="isLoading" class="text-center py-10">
                <div class="loading loading-spinner loading-lg"></div>
                <p class="mt-2 text-gray-500">Memuat data...</p>
              </div>
              
              <!-- Table content -->
              <div v-else>
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortColumn('kode_obat')">
                          <span class="hidden sm:inline">Kode</span>
                          <span class="sm:hidden">Kode</span>
                        <span v-if="sortBy === 'kode_obat'">
                          <span v-if="sortDir === 'asc'">▲</span>
                          <span v-else-if="sortDir === 'desc'">▼</span>
                        </span>
                      </th>
                        <th class="px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortColumn('nama_obat')">
                          <span class="hidden sm:inline">Nama Obat</span>
                          <span class="sm:hidden">Nama</span>
                        <span v-if="sortBy === 'nama_obat'">
                          <span v-if="sortDir === 'asc'">▲</span>
                          <span v-else-if="sortDir === 'desc'">▼</span>
                        </span>
                      </th>
                        <th class="hidden md:table-cell px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortColumn('kategori')">
                        Kategori
                        <span v-if="sortBy === 'kategori'">
                          <span v-if="sortDir === 'asc'">▲</span>
                          <span v-else-if="sortDir === 'desc'">▼</span>
                        </span>
                      </th>
                        <th class="hidden lg:table-cell px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortColumn('bentuk')">
                        Bentuk
                        <span v-if="sortBy === 'bentuk'">
                          <span v-if="sortDir === 'asc'">▲</span>
                          <span v-else-if="sortDir === 'desc'">▼</span>
                        </span>
                      </th>
                        <th class="px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortColumn('stok')">
                          <span class="hidden sm:inline">Stok</span>
                          <span class="sm:hidden">Stok</span>
                        <span v-if="sortBy === 'stok'">
                          <span v-if="sortDir === 'asc'">▲</span>
                          <span v-else-if="sortDir === 'desc'">▼</span>
                        </span>
                      </th>
                        <th class="hidden md:table-cell px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Status Kondisi
                        </th>
                        <th class="hidden lg:table-cell px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortColumn('harga_satuan')">
                        Harga
                        <span v-if="sortBy === 'harga_satuan'">
                          <span v-if="sortDir === 'asc'">▲</span>
                          <span v-else-if="sortDir === 'desc'">▼</span>
                        </span>
                      </th>
                        <th class="px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="item in tableData" :key="item.id">
                        <td class="px-2 sm:px-4 py-2 text-sm font-mono">{{ item.kode_obat }}</td>
                        <td class="px-2 sm:px-4 py-2 text-sm">{{ item.nama_obat }}</td>
                        <td class="hidden md:table-cell px-4 py-2 text-sm">
                          <span class="badge badge-outline">{{ item.kategori }}</span>
                        </td>
                        <td class="hidden lg:table-cell px-4 py-2 text-sm">{{ item.bentuk }}</td>
                        <td class="px-2 sm:px-4 py-2 text-sm text-center">{{ item.stok || 0 }}</td>
                        <td class="hidden md:table-cell px-4 py-2 text-sm">
                          <span :class="getStatusKondisiBadge(item)" class="badge text-xs">
                            {{ getStatusKondisiLabel(item) }}
                          </span>
                        </td>
                        <td class="hidden lg:table-cell px-4 py-2 text-sm font-mono">Rp {{ formatNumber(item.harga_satuan || 0) }}</td>
                        <td class="px-2 sm:px-4 py-2 text-sm">
                          <div class="flex gap-1 sm:gap-2">
                            <div v-if="!item.deleted_at" class="tooltip" data-tip="Edit obat">
                              <button @click="editItem(item)" class="btn btn-xs sm:btn-sm btn-outline">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                              </svg>
                            </button>
                          </div>
                            <div v-if="!item.deleted_at" class="tooltip" data-tip="Hapus obat">
                              <button @click="deleteItem(item)" class="btn btn-xs sm:btn-sm btn-error">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                              </svg>
                            </button>
                          </div>
                            <div v-if="item.deleted_at" class="tooltip" data-tip="Restore obat">
                              <button @click="restoreItem(item)" class="btn btn-xs sm:btn-sm btn-success">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                              </button>
                            </div>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
                </div>
                <div v-if="!isLoading && tableData.length === 0" class="text-center text-gray-400 py-10">
                  Tidak ada data.
                </div>
                <!-- Pagination -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-4">
                  <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500">{{ (currentPage - 1) * perPage + 1 }}-{{ Math.min(currentPage * perPage, total) }}</span>
                    <span class="text-xs text-gray-400">/</span>
                    <span class="text-xs font-medium">{{ total }}</span>
                  </div>
                  <div class="flex gap-1">
                    <!-- First Page -->
                    <button 
                      class="btn btn-sm" 
                      :disabled="currentPage <= 1" 
                      @click="goToPage(1)"
                      :class="{ 'btn-disabled': currentPage <= 1 }"
                    >
                      1
                    </button>
                    
                    <!-- Previous Pages -->
                    <button 
                      v-if="currentPage > 3" 
                      class="btn btn-sm btn-disabled"
                      disabled
                    >
                      ...
                    </button>
                    
                    <button 
                      v-if="currentPage > 2" 
                      class="btn btn-sm" 
                      @click="goToPage(currentPage - 1)"
                    >
                      {{ currentPage - 1 }}
                    </button>
                    
                    <!-- Current Page -->
                    <button 
                      class="btn btn-sm btn-primary" 
                      disabled
                    >
                      {{ currentPage }}
                    </button>
                    
                    <!-- Next Pages -->
                    <button 
                      v-if="currentPage < lastPage - 1" 
                      class="btn btn-sm" 
                      @click="goToPage(currentPage + 1)"
                    >
                      {{ currentPage + 1 }}
                    </button>
                    
                    <button 
                      v-if="currentPage < lastPage - 2" 
                      class="btn btn-sm btn-disabled"
                      disabled
                    >
                      ...
                    </button>
                    
                    <!-- Last Page -->
                    <button 
                      v-if="lastPage > 1" 
                      class="btn btn-sm" 
                      :disabled="currentPage >= lastPage" 
                      @click="goToPage(lastPage)"
                      :class="{ 'btn-disabled': currentPage >= lastPage }"
                    >
                      {{ lastPage }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal" class="modal modal-open">
      <div class="modal-box w-11/12 max-w-2xl">
        <h3 class="font-bold text-lg mb-4">{{ editingItem ? 'Edit' : 'Tambah' }} Obat</h3>
        <form @submit.prevent="openSaveModal(saveItem)">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <div class="form-control">
              <div class="mb-2">
                <span class="text-sm font-medium">Kode Obat *</span>
              </div>
              <input 
                v-model="form.kode_obat" 
                type="text" 
                class="input input-bordered w-full max-w-xs" 
                placeholder="Masukkan kode obat"
                required 
              />
            </div>
            <div class="form-control">
              <div class="mb-2">
                <span class="text-sm font-medium">Nama Obat</span>
              </div>
              <input v-model="form.nama_obat" type="text" class="input input-bordered w-full max-w-xs" required />
            </div>
            <div class="form-control">
              <div class="mb-2">
                <span class="text-sm font-medium">Kategori</span>
              </div>
              <select v-model="form.kategori" class="select select-bordered w-full max-w-xs" required>
                <option value="">Pilih Kategori</option>
                <option v-for="kategori in kategoris" :key="kategori" :value="kategori">{{ kategori }}</option>
              </select>
            </div>
            <div class="form-control">
              <div class="mb-2">
                <span class="text-sm font-medium">Bentuk</span>
              </div>
              <select v-model="form.bentuk" class="select select-bordered w-full max-w-xs" required>
                <option value="">Pilih Bentuk</option>
                <option v-for="bentuk in bentuks" :key="bentuk" :value="bentuk">{{ bentuk }}</option>
              </select>
            </div>
            <div class="form-control">
              <div class="mb-2">
                <span class="text-sm font-medium">Satuan</span>
              </div>
              <input v-model="form.satuan" type="text" class="input input-bordered w-full max-w-xs" required />
            </div>
            <div class="form-control">
              <div class="mb-2">
                <span class="text-sm font-medium">Stok</span>
              </div>
              <input v-model="form.stok" type="number" min="0" class="input input-bordered w-full max-w-xs" />
            </div>
            <div class="form-control">
              <div class="mb-2">
                <span class="text-sm font-medium">Harga Satuan</span>
              </div>
              <input v-model="form.harga_satuan" type="number" min="0" step="100" class="input input-bordered w-full max-w-xs" required />
            </div>
            <div class="form-control sm:col-span-2">
              <div class="mb-2">
                <span class="text-sm font-medium">Komposisi</span>
              </div>
              <textarea v-model="form.komposisi" class="textarea textarea-bordered w-full max-w-2xl" rows="3"></textarea>
            </div>
            <div class="form-control sm:col-span-2">
              <div class="mb-2">
                <span class="text-sm font-medium">Keterangan</span>
              </div>
              <textarea v-model="form.keterangan" class="textarea textarea-bordered w-full max-w-2xl" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-action flex flex-col sm:flex-row justify-end gap-2 mt-6">
            <button type="button" @click="showCreateModal = false" class="btn btn-outline">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Konfirmasi Hapus DaisyUI -->
    <div v-if="showDeleteModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">Konfirmasi Hapus</h3>
        <p>Apakah Anda yakin ingin menghapus obat <b>{{ itemToDelete?.nama_obat }}</b>?</p>
        <div class="modal-action">
          <button class="btn btn-outline" @click="showDeleteModal = false">Batal</button>
          <button class="btn btn-error" @click="confirmDelete">Hapus</button>
        </div>
      </div>
    </div>

    <!-- Modal Konfirmasi Restore DaisyUI -->
    <div v-if="showRestoreModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">Konfirmasi Restore</h3>
        <p>Apakah Anda yakin ingin mengembalikan obat <b>{{ itemToRestore?.nama_obat }}</b>?</p>
        <div class="modal-action">
          <button class="btn btn-outline" @click="showRestoreModal = false">Batal</button>
          <button class="btn btn-success" @click="confirmRestore">Restore</button>
        </div>
      </div>
    </div>

    <!-- Modal Konfirmasi Simpan DaisyUI -->
    <div v-if="showSaveModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">Konfirmasi Simpan</h3>
        <p>Apakah Anda yakin ingin menyimpan data obat ini?</p>
        <div class="modal-action">
          <button class="btn btn-outline" @click="showSaveModal = false">Batal</button>
          <button class="btn btn-primary" @click="confirmSave">Simpan</button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useToast } from '@/composables/useToast.js'

const toast = useToast()
const page = usePage()

onMounted(() => {
  // Check for flash messages on mount
  if (page.props.flash?.success) {
    toast.success(page.props.flash.success, 'Berhasil')
  }
  if (page.props.flash?.error) {
    toast.error(page.props.flash.error, 'Gagal')
  }
})

const props = defineProps({
  obats: Object,
  filters: Object,
  kategoris: Array,
  bentuks: Array
})

const search = ref(props.filters?.search || '')
const filterKategori = ref(props.filters?.kategori || '')
const filterBentuk = ref(props.filters?.bentuk || '')
const filterKondisi = ref(props.filters?.kondisi || '')
const sortBy = ref(props.filters?.sortBy || 'nama_obat')
const sortDir = ref(props.filters?.sortDir || 'asc')
const showCreateModal = ref(false)
const editingItem = ref(null)
const isLoading = ref(false)

// Modal states
const showDeleteModal = ref(false)
const itemToDelete = ref(null)
const showRestoreModal = ref(false)
const itemToRestore = ref(null)
const showSaveModal = ref(false)
const saveAction = ref(null)

function openSaveModal(action) {
  saveAction.value = action
  showSaveModal.value = true
}

function confirmSave() {
  if (saveAction.value) {
    saveAction.value()
    showSaveModal.value = false
    saveAction.value = null
  }
}

const form = ref({
  kode_obat: '',
  nama_obat: '',
  kategori: '',
  bentuk: '',
  satuan: '',
  stok: 0,
  harga_satuan: 0,
  komposisi: '',
  keterangan: ''
})

// Computed properties untuk data table
const tableData = computed(() => props.obats?.data || [])
const currentPage = computed(() => props.obats?.current_page || 1)
const lastPage = computed(() => props.obats?.last_page || 1)
const perPage = computed(() => props.obats?.per_page || 10)
const total = computed(() => props.obats?.total || 0)

// Helper function to build query parameters
function buildQueryParams(additionalParams = {}) {
  const params = {
    page: 1,
    perPage: perPage.value,
    sortBy: sortBy.value,
    sortDir: sortDir.value,
    ...additionalParams
  }
  
  // Only include non-empty values
  const filteredParams = {}
  Object.keys(params).forEach(key => {
    if (params[key] !== '' && params[key] !== null && params[key] !== undefined) {
      filteredParams[key] = params[key]
    }
  })
  
  return filteredParams
}

// Watch for filter changes and trigger backend request
watch(filterKategori, (newValue) => {
  router.get('/master-obat', buildQueryParams({
    search: search.value,
    kategori: newValue,
    bentuk: filterBentuk.value,
    kondisi: filterKondisi.value
  }), { preserveState: true, preserveScroll: true })
})

watch(filterBentuk, (newValue) => {
  router.get('/master-obat', buildQueryParams({
    search: search.value,
    kategori: filterKategori.value,
    bentuk: newValue,
    kondisi: filterKondisi.value
  }), { preserveState: true, preserveScroll: true })
})

// Watch for search changes and trigger backend request
watch(search, (newValue) => {
  router.get('/master-obat', buildQueryParams({
    search: newValue,
    kategori: filterKategori.value,
    bentuk: filterBentuk.value,
    kondisi: filterKondisi.value
  }), { preserveState: true, preserveScroll: true })
})

// Watch for kondisi filter changes and trigger backend request
watch(filterKondisi, (newValue) => {
  router.get('/master-obat', buildQueryParams({
    search: search.value,
    kategori: filterKategori.value,
    bentuk: filterBentuk.value,
    kondisi: newValue
  }), { preserveState: true, preserveScroll: true })
})

// Pagination functions
const goToPage = (page) => {
  router.get('/master-obat', buildQueryParams({
    page: page,
    search: search.value,
    kategori: filterKategori.value,
    bentuk: filterBentuk.value,
    kondisi: filterKondisi.value
  }), { preserveState: true, preserveScroll: true })
}

const changePerPage = (newPerPage) => {
  router.get('/master-obat', buildQueryParams({
    perPage: newPerPage,
    search: search.value,
    kategori: filterKategori.value,
    bentuk: filterBentuk.value,
    kondisi: filterKondisi.value
  }), { preserveState: true, preserveScroll: true })
}

const sortColumn = (column) => {
  const newSortDir = sortBy.value === column && sortDir.value === 'asc' ? 'desc' : 'asc'
  sortBy.value = column
  sortDir.value = newSortDir
  
  router.get('/master-obat', buildQueryParams({
    page: currentPage.value,
    search: search.value,
    kategori: filterKategori.value,
    bentuk: filterBentuk.value,
    kondisi: filterKondisi.value
  }), { preserveState: true, preserveScroll: true })
}

function editItem(item) {
  editingItem.value = item
  form.value = { ...item }
  showCreateModal.value = true
}

function deleteItem(item) {
  itemToDelete.value = item
  showDeleteModal.value = true
}

function confirmDelete() {
  if (itemToDelete.value) {
    router.delete(`/master-obat/${itemToDelete.value.id}`, {
      onSuccess: () => {
        toast.success('Obat berhasil dihapus', 'Berhasil')
      }
    })
    showDeleteModal.value = false
    itemToDelete.value = null
  }
}

function restoreItem(item) {
  itemToRestore.value = item
  showRestoreModal.value = true
}

function confirmRestore() {
  if (itemToRestore.value) {
    router.patch(`/master-obat/${itemToRestore.value.id}/restore`, {
      onSuccess: () => {
        toast.success('Obat berhasil direstore', 'Berhasil')
      },
      onError: (err) => {
        toast.error('Gagal merestore obat', 'Error')
      }
    })
    showRestoreModal.value = false
    itemToRestore.value = null
  }
}

function saveItem() {
  if (editingItem.value) {
    router.put(`/master-obat/${editingItem.value.id}`, form.value)
  } else {
    router.post('/master-obat', form.value)
  }
  showCreateModal.value = false
  editingItem.value = null
  form.value = {
    kode_obat: '',
    nama_obat: '',
    kategori: '',
    bentuk: '',
    satuan: '',
    stok: 0,
    harga_satuan: 0,
    komposisi: '',
    keterangan: ''
  }
}

// Helper functions for status kondisi
function getStatusKondisi(item) {
  const stok = item.stok || 0
  if (stok === 0) {
    return 'habis'
  } else if (stok <= 10) {
    return 'stok_menipis'
  } else {
    return 'tersedia'
  }
}

function getStatusKondisiLabel(item) {
  const status = getStatusKondisi(item)
  const labels = {
    'tersedia': 'Tersedia',
    'stok_menipis': 'Stok Menipis',
    'habis': 'Habis'
  }
  return labels[status] || 'Tidak Diketahui'
}

function getStatusKondisiBadge(item) {
  const status = getStatusKondisi(item)
  const badges = {
    'tersedia': 'badge-success',
    'stok_menipis': 'badge-warning',
    'habis': 'badge-error'
  }
  return badges[status] || 'badge-secondary'
}

function formatNumber(number) {
  return new Intl.NumberFormat('id-ID').format(number)
}
</script> 