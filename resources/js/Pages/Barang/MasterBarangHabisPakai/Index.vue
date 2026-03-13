<template>
  <AuthenticatedLayout>
    <Head title="Master Barang Habis Pakai" />
    
    <div class="py-6">
      <div class="w-full">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Master Barang Habis Pakai</h2>
              <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <div class="tooltip" data-tip="Download template Excel">
                  <button @click="downloadTemplate" :disabled="isDownloading" class="btn btn-outline btn-sm sm:btn-md">
                    <div v-if="isDownloading" class="w-20 mr-2">
                      <progress class="progress progress-primary w-full" />
                    </div>
                    <svg v-else class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="hidden sm:inline">{{ isDownloading ? 'Downloading...' : 'Template' }}</span>
                  </button>
                </div>
                <div class="tooltip" data-tip="Import data dari Excel">
                  <button @click="showImportModal = true" class="btn btn-outline btn-sm sm:btn-md">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                    </svg>
                    <span class="hidden sm:inline">Import</span>
                  </button>
                </div>
                <div class="tooltip" data-tip="Export data ke Excel">
                  <button @click="exportData" :disabled="isExporting" class="btn btn-outline btn-sm sm:btn-md">
                    <div v-if="isExporting" class="w-4 h-4 mr-1">
                      <div class="progress progress-xs w-full">
                        <div class="progress-bar bg-primary"></div>
                      </div>
                    </div>
                    <svg v-else class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="hidden sm:inline">{{ isExporting ? 'Exporting...' : 'Export' }}</span>
                  </button>
                </div>
                <div class="tooltip" data-tip="Tambah barang baru">
                  <button @click="showCreateModal = true" class="btn btn-primary btn-sm sm:btn-md">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span class="hidden sm:inline">Tambah Barang</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              <div class="form-control">
                <div class="mb-2">
                  <span class="text-sm font-medium">Cari Barang</span>
                </div>
                <input
                  v-model="search"
                  type="text"
                  placeholder="Nama atau kode barang..."
                  class="input input-bordered w-full max-w-xs"
                />
              </div>
              <div class="form-control">
                <div class="mb-2">
                  <span class="text-sm font-medium">Kategori</span>
                </div>
                <select v-model="filterKategori" class="select select-bordered w-full max-w-xs">
                  <option value="">Semua Kategori</option>
                  <option v-for="kategori in (kategoris || [])" :key="kategori" :value="kategori">{{ kategori }}</option>
                </select>
              </div>
              <div class="form-control">
                <div class="mb-2">
                  <span class="text-sm font-medium">Status</span>
                </div>
                <select v-model="filterStatus" class="select select-bordered w-full max-w-xs">
                  <option value="">Semua Status</option>
                  <option value="aktif">Aktif</option>
                  <option value="tidak_aktif">Tidak Aktif</option>
                  <option value="stok_kosong">Stok Kosong</option>
                  <option value="pending_pengadaan">Pending Pengadaan</option>
                  <option value="pending_permintaan">Pending Permintaan</option>
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
                <select v-model="perPage" class="select select-bordered select-sm w-20">
                  <option v-for="option in perPageOptions" :key="option" :value="option">{{ option }}</option>
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
                        <th class="px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Kode
                        </th>
                        <th class="px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Nama Barang
                        </th>
                        <th class="hidden md:table-cell px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Satuan
                        </th>
                        <th class="hidden lg:table-cell px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Kategori
                        </th>
                        <th class="px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Status
                        </th>
                        <th class="hidden lg:table-cell px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Keterangan
                        </th>
                        <th class="px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Aksi
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="item in sortedBarangs" :key="item.id">
                        <td class="px-2 sm:px-4 py-2 text-sm font-mono">{{ item.kode_barang || '-' }}</td>
                        <td class="px-2 sm:px-4 py-2 text-sm">{{ item.nama_barang || '-' }}</td>
                        <td class="hidden md:table-cell px-4 py-2 text-sm">{{ item.satuan || '-' }}</td>
                        <td class="hidden lg:table-cell px-4 py-2 text-sm">
                          <span class="badge badge-outline">{{ item.kategori || '-' }}</span>
                        </td>
                        <td class="px-2 sm:px-4 py-2 text-sm">
                          <span :class="getStatusBadgeClass(item)" class="badge text-xs">
                            {{ getStatusText(item) }}
                          </span>
                        </td>
                        <td class="hidden lg:table-cell px-4 py-2 text-sm">{{ item.keterangan || '-' }}</td>
                        <td class="px-2 sm:px-4 py-2 text-sm">
                          <div class="flex gap-1 sm:gap-2">
                            <div class="tooltip" data-tip="Edit barang">
                              <button @click="editItem(item)" class="btn btn-xs sm:btn-sm btn-outline">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                              </button>
                            </div>
                            <div class="tooltip" data-tip="Hapus barang">
                              <button 
                                @click="openDeleteModal(item)" 
                                :class="getDeleteButtonClass(item)" 
                                :disabled="!canDeleteItem(item)" 
                                :title="getDeleteTooltip(item)"
                              >
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
                <div v-if="!isLoading && sortedBarangs.length === 0" class="text-center text-gray-400 py-10">
                  Tidak ada data.
                </div>
                <!-- Pagination -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-4">
                  <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500">{{ barangs?.from || 0 }}-{{ barangs?.to || 0 }}</span>
                    <span class="text-xs text-gray-400">/</span>
                    <span class="text-xs font-medium">{{ barangs?.total || 0 }}</span>
                  </div>
                  <div class="flex gap-1">
                    <!-- First Page -->
                    <button 
                      class="btn btn-sm" 
                      :disabled="(barangs?.current_page || 1) <= 1" 
                      @click="goToPage(1)"
                      :class="{ 'btn-disabled': (barangs?.current_page || 1) <= 1 }"
                    >
                      1
                    </button>
                    
                    <!-- Previous Pages -->
                    <button 
                      v-if="(barangs?.current_page || 1) > 3" 
                      class="btn btn-sm btn-disabled"
                      disabled
                    >
                      ...
                    </button>
                    
                    <button 
                      v-if="(barangs?.current_page || 1) > 2" 
                      class="btn btn-sm" 
                      @click="goToPage((barangs?.current_page || 1) - 1)"
                    >
                      {{ (barangs?.current_page || 1) - 1 }}
                    </button>
                    
                    <!-- Current Page -->
                    <button 
                      class="btn btn-sm btn-primary" 
                      disabled
                    >
                      {{ barangs?.current_page || 1 }}
                    </button>
                    
                    <!-- Next Pages -->
                    <button 
                      v-if="(barangs?.current_page || 1) < (barangs?.last_page || 1) - 1" 
                      class="btn btn-sm" 
                      @click="goToPage((barangs?.current_page || 1) + 1)"
                    >
                      {{ (barangs?.current_page || 1) + 1 }}
                    </button>
                    
                    <button 
                      v-if="(barangs?.current_page || 1) < (barangs?.last_page || 1) - 2" 
                      class="btn btn-sm btn-disabled"
                      disabled
                    >
                      ...
                    </button>
                    
                    <!-- Last Page -->
                    <button 
                      v-if="(barangs?.last_page || 1) > 1" 
                      class="btn btn-sm" 
                      :disabled="(barangs?.current_page || 1) >= (barangs?.last_page || 1)" 
                      @click="goToPage(barangs?.last_page || 1)"
                      :class="{ 'btn-disabled': (barangs?.current_page || 1) >= (barangs?.last_page || 1) }"
                    >
                      {{ barangs?.last_page || 1 }}
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
        <h3 class="font-bold text-lg mb-4">{{ editingItem ? 'Edit' : 'Tambah' }} Barang Habis Pakai</h3>
        <form @submit.prevent="openSaveModal(saveItem)">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <div class="form-control">
              <div class="mb-2">
                <span class="text-sm font-medium">Kode Barang *</span>
              </div>
              <input 
                v-model="form.kode_barang" 
                type="text" 
                class="input input-bordered w-full max-w-xs" 
                placeholder="Masukkan kode barang"
                required 
              />
            </div>
            <div class="form-control">
              <div class="mb-2">
                <span class="text-sm font-medium">Nama Barang *</span>
              </div>
              <input 
                v-model="form.nama_barang" 
                type="text" 
                class="input input-bordered w-full max-w-xs" 
                placeholder="Masukkan nama barang"
                required 
              />
            </div>
            <div class="form-control">
              <div class="mb-2">
                <span class="text-sm font-medium">Satuan *</span>
              </div>
              <select v-model="form.satuan" class="select select-bordered w-full max-w-xs" required>
                <option value="">Pilih Satuan</option>
                <option v-for="option in satuanOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
            </div>
            <div class="form-control">
              <div class="mb-2">
                <span class="text-sm font-medium">Kategori *</span>
              </div>
              <select v-model="form.kategori" class="select select-bordered w-full max-w-xs" required>
                <option value="">Pilih Kategori</option>
                <option v-for="option in kategoriOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
            </div>
            <div class="form-control sm:col-span-2">
              <div class="mb-2">
                <span class="text-sm font-medium">Keterangan</span>
              </div>
              <textarea 
                v-model="form.keterangan" 
                class="textarea textarea-bordered w-full max-w-2xl" 
                rows="3"
                placeholder="Masukkan keterangan (opsional)"
              ></textarea>
            </div>
          </div>
          <div class="modal-action flex flex-col sm:flex-row justify-end gap-2 mt-6">
            <button type="button" @click="closeCreateModal" class="btn btn-outline">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Import Modal -->
    <div v-if="showImportModal" class="modal modal-open">
      <div class="modal-box w-11/12 max-w-2xl">
        <h3 class="font-bold text-lg mb-4">Import Data Barang Habis Pakai</h3>
        <form @submit.prevent="importData">
          <div class="space-y-4">
            <div class="alert alert-info">
              <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div>
                <h4 class="font-semibold">Petunjuk Import</h4>
                <ul class="text-sm mt-2 space-y-1">
                  <li>• File harus berformat Excel (.xlsx atau .xls)</li>
                  <li>• Maksimal ukuran file 2MB</li>
                  <li>• Kolom wajib: Kode Barang, Nama Barang, Satuan</li>
                  <li>• Kolom opsional: Kategori, Keterangan</li>
                  <li>• Download template terlebih dahulu untuk format yang benar</li>
                </ul>
              </div>
            </div>
            
            <div class="form-control">
              <div class="mb-2">
                <span class="text-sm font-medium">Pilih File Excel</span>
              </div>
              <input 
                ref="fileInput"
                type="file" 
                accept=".xlsx,.xls"
                @change="handleFileChange"
                class="file-input file-input-bordered w-full max-w-xs" 
                required
              />
              <div class="mt-1">
                <span class="text-xs text-gray-500">Format: .xlsx atau .xls, maksimal 2MB</span>
              </div>
            </div>
          </div>
          
          <div class="modal-action flex flex-col sm:flex-row justify-end gap-2 mt-6">
            <button type="button" @click="closeImportModal" class="btn btn-outline">Batal</button>
            <button type="submit" :disabled="!selectedFile || isImporting" class="btn btn-primary">
              <div v-if="isImporting" class="w-4 h-4 mr-1">
                <div class="progress progress-xs w-full">
                  <div class="progress-bar bg-primary"></div>
                </div>
              </div>
              {{ isImporting ? 'Mengimpor...' : 'Import Data' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div v-if="showDeleteModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">Konfirmasi Hapus</h3>
        <p>Apakah Anda yakin ingin menghapus barang <b>{{ itemToDelete?.nama_barang || 'ini' }}</b>?</p>
        <div class="modal-action">
          <button class="btn btn-outline" @click="closeDeleteModal">Batal</button>
          <button class="btn btn-error" @click="confirmDelete">Hapus</button>
        </div>
      </div>
    </div>

    <!-- Modal Konfirmasi Simpan -->
    <div v-if="showSaveModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">Konfirmasi Simpan</h3>
        <p>{{ editingItem ? 'Apakah Anda yakin ingin menyimpan perubahan data barang?' : 'Apakah Anda yakin ingin menyimpan data barang baru?' }}</p>
        <div class="modal-action">
          <button class="btn btn-outline" @click="closeSaveModal">Batal</button>
          <button class="btn btn-primary" @click="confirmSave">Simpan</button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { useToast } from '@/composables/useToast.js'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  barangs: Object,
  filters: Object,
  kategoris: Array,
  satuans: Array,
  config: Object
})

const page = usePage()
const search = ref(props.filters?.search || '')
const filterKategori = ref(props.filters?.kategori || '')
const filterStatus = ref(props.filters?.status || '')
const perPage = ref(props.filters?.perPage || 10)
const showCreateModal = ref(false)
const showDeleteModal = ref(false)
const showSaveModal = ref(false)
const showImportModal = ref(false)
const editingItem = ref(null)
const itemToDelete = ref(null)
const selectedFile = ref(null)
const isImporting = ref(false)
const isDownloading = ref(false)
const isExporting = ref(false)
const isLoading = ref(false)
const saveAction = ref(null)
const fileInput = ref(null)

const form = ref({
  kode_barang: '',
  nama_barang: '',
  satuan: '',
  kategori: '',
  keterangan: ''
})

// Use configuration from backend
const satuanOptions = computed(() => props.config?.satuanOptions || [])
const kategoriOptions = computed(() => props.config?.kategoriOptions || [])
const statusOptions = computed(() => props.config?.statusOptions || [])
const perPageOptions = computed(() => props.config?.perPageOptions || [10, 25, 50])

const badgeClass = (stok) => {
  if (!stok || stok <= 0) return 'badge-error';
  return 'badge-success';
}

const getStatusText = (item) => {
  if (item.deleted_at) {
    return 'Tidak Aktif';
  }
  
  // Check if item has stock
  if (item.stok > 0) {
    return 'Stok Tersedia';
  }
  
  // Check if item has pending pengadaan
  if (item.pending_pengadaan > 0) {
    return 'Pending Pengadaan';
  }
  
  // Check if item has pending permintaan
  if (item.pending_permintaan > 0) {
    return 'Pending Permintaan';
  }
  
  return 'Stok Kosong';
}

const getStatusBadgeClass = (item) => {
  if (item.deleted_at) {
    return 'badge-error';
  }
  
  // Check if item has stock
  if (item.stok > 0) {
    return 'badge-success';
  }
  
  // Check if item has pending pengadaan
  if (item.pending_pengadaan > 0) {
    return 'badge-warning';
  }
  
  // Check if item has pending permintaan
  if (item.pending_permintaan > 0) {
    return 'badge-info';
  }
  
  return 'badge-secondary';
}

// Watch for filter changes and trigger backend request
watch(filterKategori, (newValue) => {
  router.get('/master-barang-habis-pakai', {
    page: 1,
    perPage: perPage.value,
    search: search.value,
    kategori: newValue,
    status: filterStatus.value
  }, { preserveState: true, preserveScroll: true })
})

watch(search, (newValue) => {
  router.get('/master-barang-habis-pakai', {
    page: 1,
    perPage: perPage.value,
    search: newValue,
    kategori: filterKategori.value,
    status: filterStatus.value
  }, { preserveState: true, preserveScroll: true })
})

watch(filterStatus, (newValue) => {
  router.get('/master-barang-habis-pakai', {
    page: 1,
    perPage: perPage.value,
    search: search.value,
    kategori: filterKategori.value,
    status: newValue
  }, { preserveState: true, preserveScroll: true })
})

watch(perPage, (newValue) => {
  router.get('/master-barang-habis-pakai', {
    page: 1,
    perPage: newValue,
    search: search.value,
    kategori: filterKategori.value,
    status: filterStatus.value
  }, { preserveState: true, preserveScroll: true })
})

const goToPage = (page) => {
  router.get('/master-barang-habis-pakai', {
    page: page,
    perPage: perPage.value,
    search: search.value,
    kategori: filterKategori.value,
    status: filterStatus.value
  }, { preserveState: true, preserveScroll: true })
}

const editItem = (item) => {
  if (!item || !item.id) {
    toast.error('Item yang akan diedit tidak valid', 'Error')
    return
  }
  
  editingItem.value = item
  form.value = { 
    kode_barang: item.kode_barang || '',
    nama_barang: item.nama_barang || '',
    satuan: item.satuan || '',
    kategori: item.kategori || '',
    keterangan: item.keterangan || ''
  }
  showCreateModal.value = true
}

// Validation methods for delete
const canDeleteItem = (item) => {
  // Check if item has stock
  if (item.stok > 0) return false
  
  // Check if item has pending pengadaan
  if (item.pending_pengadaan > 0) return false
  
  // Check if item has pending permintaan
  if (item.pending_permintaan > 0) return false
  
  return true
}

const getDeleteButtonClass = (item) => {
  return canDeleteItem(item) ? 'btn btn-sm btn-error' : 'btn btn-sm btn-disabled'
}

const getDeleteTooltip = (item) => {
  const reasons = []
  
  if (item.stok > 0) {
    reasons.push(`${item.stok} stok aktif`)
  }
  
  if (item.pending_pengadaan > 0) {
    reasons.push(`${item.pending_pengadaan} pengadaan pending`)
  }
  
  if (item.pending_permintaan > 0) {
    reasons.push(`${item.pending_permintaan} permintaan pending`)
  }
  
  if (reasons.length > 0) {
    return `Tidak dapat dihapus: ${reasons.join(', ')}`
  }
  
  return 'Hapus barang'
}

const openDeleteModal = (item) => {
  if (!item || !item.id) {
    toast.error('Item yang akan dihapus tidak valid', 'Error')
    return
  }
  
  // Check if item can be deleted
  if (!canDeleteItem(item)) {
    const reasons = []
    
    if (item.stok > 0) {
      reasons.push(`${item.stok} stok aktif`)
    }
    
    if (item.pending_pengadaan > 0) {
      reasons.push(`${item.pending_pengadaan} pengadaan pending`)
    }
    
    if (item.pending_permintaan > 0) {
      reasons.push(`${item.pending_permintaan} permintaan pending`)
    }
    
    const message = `Barang tidak dapat dihapus: ${reasons.join(', ')}`
    toast.error(message, 'Error')
    return
  }
  
  itemToDelete.value = item
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  itemToDelete.value = null
}

const confirmDelete = () => {
  if (!itemToDelete.value) {
    toast.error('Item yang akan dihapus tidak ditemukan', 'Error')
    closeDeleteModal()
    return
  }
  
  router.delete(`/master-barang-habis-pakai/${itemToDelete.value.id}`, {
    onSuccess: () => {
      toast.success('Data barang berhasil dihapus.', 'Berhasil')
      closeDeleteModal()
    },
    onError: (errors) => {
      toast.error('Gagal menghapus data barang.', 'Error')
      closeDeleteModal()
    }
  })
}

const closeCreateModal = () => {
    showCreateModal.value = false
    editingItem.value = null
    form.value = {
      kode_barang: '',
      nama_barang: '',
      satuan: '',
      kategori: '',
      keterangan: ''
  }
}

const closeSaveModal = () => {
  showSaveModal.value = false
  saveAction.value = null
}

const closeImportModal = () => {
  showImportModal.value = false
  selectedFile.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const openSaveModal = (action) => {
  saveAction.value = action
  showSaveModal.value = true
}

const confirmSave = () => {
  if (saveAction.value) {
    saveAction.value()
    showSaveModal.value = false
    saveAction.value = null
  }
}

const saveItem = () => {
  // Validate required fields
  if (!form.value.kode_barang || !form.value.nama_barang || !form.value.satuan || !form.value.kategori) {
    toast.error('Semua field wajib diisi', 'Error')
    return
  }
  
  if (editingItem.value) {
    router.put(`/master-barang-habis-pakai/${editingItem.value.id}`, form.value, {
      onSuccess: () => {
        toast.success('Data barang berhasil diperbarui.', 'Berhasil')
        closeCreateModal()
      },
      onError: (errors) => {
        toast.error('Gagal memperbarui data barang.', 'Error')
      }
    })
  } else {
    router.post('/master-barang-habis-pakai', form.value, {
      onSuccess: () => {
        toast.success('Data barang berhasil disimpan.', 'Berhasil')
        closeCreateModal()
      },
      onError: (errors) => {
        toast.error('Gagal menyimpan data barang.', 'Error')
      }
    })
  }
}

// Download template function
const downloadTemplate = async () => {
  try {
    isDownloading.value = true
    await router.get('/master-barang-habis-pakai/download-template', {}, {
      onSuccess: () => {
        toast.success('Template berhasil didownload.', 'Berhasil')
      },
      onError: (errors) => {
        toast.error('Gagal download template.', 'Error')
      }
    })
  } catch (error) {
    toast.error('Gagal download template.', 'Error')
  } finally {
    isDownloading.value = false
  }
}

// Export data function
const exportData = async () => {
  try {
    isExporting.value = true
    await router.get('/master-barang-habis-pakai/export', {}, {
      onSuccess: () => {
        toast.success('Data berhasil diexport.', 'Berhasil')
      },
      onError: (errors) => {
        toast.error('Gagal export data.', 'Error')
      }
    })
  } catch (error) {
    toast.error('Gagal export data.', 'Error')
  } finally {
    isExporting.value = false
  }
}

// Import data function
async function importData() {
  if (!selectedFile.value) {
    toast.error('Pilih file terlebih dahulu', 'Error')
    return
  }

  isImporting.value = true
  
  try {
    const formData = new FormData()
    formData.append('file', selectedFile.value)
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     document.querySelector('meta[name="_token"]')?.getAttribute('content')
    
    const response = await fetch('/master-barang-habis-pakai/import', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
      body: formData
    })
    
    const result = await response.json()
    
    if (result.success) {
      toast.success(result.message, 'Berhasil')
      closeImportModal()
      router.reload()
    } else {
      toast.error(result.message || 'Gagal mengimpor data', 'Gagal')
    }
  } catch (error) {
    console.error('Import error:', error)
    toast.error('Terjadi kesalahan saat mengimpor data', 'Error')
  } finally {
    isImporting.value = false
  }
}

// Handle file change
function handleFileChange(event) {
  selectedFile.value = event.target.files[0]
  
  // Validate file size (2MB limit)
  if (selectedFile.value && selectedFile.value.size > 2 * 1024 * 1024) {
    toast.error('Ukuran file terlalu besar. Maksimal 2MB.', 'Error')
    event.target.value = ''
    selectedFile.value = null
    return
  }
  
  // Validate file type
  const allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel']
  if (selectedFile.value && !allowedTypes.includes(selectedFile.value.type)) {
    toast.error('Format file tidak didukung. Gunakan file Excel (.xlsx atau .xls).', 'Error')
    event.target.value = ''
    selectedFile.value = null
    return
  }
}

const toast = useToast()

const getVisiblePages = () => {
  const currentPage = props.barangs?.current_page || 1;
  const lastPage = props.barangs?.last_page || 1;
  const pages = [];

  if (lastPage <= 7) {
    // Jika total halaman <= 7, tampilkan semua
    for (let i = 1; i <= lastPage; i++) {
      pages.push(i);
  }
  } else {
    // Jika total halaman > 7, gunakan ellipsis
    if (currentPage <= 4) {
      // Halaman awal: 1 2 3 4 5 ... last
      for (let i = 1; i <= 5; i++) {
        pages.push(i);
      }
      pages.push('...');
      pages.push(lastPage);
    } else if (currentPage >= lastPage - 3) {
      // Halaman akhir: 1 ... last-4 last-3 last-2 last-1 last
      pages.push(1);
      pages.push('...');
      for (let i = lastPage - 4; i <= lastPage; i++) {
        pages.push(i);
      }
    } else {
      // Halaman tengah: 1 ... current-1 current current+1 ... last
      pages.push(1);
      pages.push('...');
      pages.push(currentPage - 1);
      pages.push(currentPage);
      pages.push(currentPage + 1);
      pages.push('...');
      pages.push(lastPage);
    }
  }

  return pages;
};

const sortedBarangs = computed(() => {
  // Jika data belum ada, kembalikan array kosong
  if (!props.barangs?.data) return [];
  
  // Filter data yang valid dan sort aman berdasarkan nama_barang
  return [...props.barangs.data]
    .filter(item => item && typeof item === 'object') // Pastikan item valid
    .sort((a, b) => {
      const namaA = (a?.nama_barang || '').toString();
      const namaB = (b?.nama_barang || '').toString();
      return namaA.localeCompare(namaB);
    });
});

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
  
  // Show initial flash messages if any
  if (page.props.flash?.success) {
    toast.success(page.props.flash.success, 'Berhasil')
  }
  if (page.props.flash?.error) {
    toast.error(page.props.flash.error, 'Gagal')
  }
})
</script> 