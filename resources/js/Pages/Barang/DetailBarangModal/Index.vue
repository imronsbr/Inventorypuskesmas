<template>
  <AuthenticatedLayout>
    <Head title="Detail Barang Modal" />
    
    <div class="py-6">
      <div class="w-full">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Detail Barang Modal</h2>
              <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <div class="tooltip" data-tip="Download template Excel">
                  <button @click="downloadTemplate" class="btn btn-outline btn-sm sm:btn-md">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="hidden sm:inline">Template</span>
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
                  <button @click="exportData" class="btn btn-outline btn-sm sm:btn-md">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="hidden sm:inline">Export</span>
                  </button>
                </div>
                <div class="tooltip" data-tip="Tambah asset detail baru">
                  <button @click="showCreateModal = true" class="btn btn-primary btn-sm sm:btn-md">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span class="hidden sm:inline">Tambah Asset</span>
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
                <label class="label">
                  <span class="label-text">Cari Asset</span>
                </label>
                <input
                  v-model="search"
                  type="text"
                  placeholder="Nomor seri, merk, tipe..."
                  class="input input-bordered w-full"
                />
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Kondisi</span>
                </label>
                <select v-model="filterKondisi" class="select select-bordered w-full">
                  <option value="">Semua Kondisi</option>
                  <option value="baik">Baik</option>
                  <option value="perbaikan">Perbaikan</option>
                  <option value="rusak_berat">Rusak Berat</option>
                </select>
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Ruang</span>
                </label>
                <select v-model="filterRuang" class="select select-bordered w-full">
                  <option value="">Semua Ruang</option>
                  <option v-for="ruang in ruangs" :key="ruang.id" :value="ruang.id">
                    {{ ruang.nama }}
                  </option>
                </select>
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Kode Barang</span>
                </label>
                <select v-model="filterKodeBarang" class="select select-bordered w-full">
                  <option value="">Semua Barang</option>
                  <option v-for="barang in masterBarangModals" :key="barang.kode_barang" :value="barang.kode_barang">
                    {{ barang.kode_barang }} - {{ barang.nama_barang }}
                  </option>
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
                        <th class="px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortColumn('kode_barang')">
                          <span class="hidden sm:inline">Kode Barang</span>
                          <span class="sm:hidden">Kode</span>
                        <span v-if="sortBy === 'kode_barang'">
                          <span v-if="sortDir === 'asc'">▲</span>
                          <span v-else-if="sortDir === 'desc'">▼</span>
                        </span>
                      </th>
                        <th class="px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortColumn('nomor_seri')">
                          <span class="hidden sm:inline">Nomor Seri</span>
                          <span class="sm:hidden">Seri</span>
                        <span v-if="sortBy === 'nomor_seri'">
                          <span v-if="sortDir === 'asc'">▲</span>
                          <span v-else-if="sortDir === 'desc'">▼</span>
                        </span>
                      </th>
                        <th class="hidden md:table-cell px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortColumn('merk')">
                        Merk
                        <span v-if="sortBy === 'merk'">
                          <span v-if="sortDir === 'asc'">▲</span>
                          <span v-else-if="sortDir === 'desc'">▼</span>
                        </span>
                      </th>
                        <th class="hidden lg:table-cell px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortColumn('tipe')">
                        Tipe
                        <span v-if="sortBy === 'tipe'">
                          <span v-if="sortDir === 'asc'">▲</span>
                          <span v-else-if="sortDir === 'desc'">▼</span>
                        </span>
                      </th>
                        <th class="px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortColumn('kondisi')">
                          <span class="hidden sm:inline">Kondisi</span>
                          <span class="sm:hidden">Kond</span>
                        <span v-if="sortBy === 'kondisi'">
                          <span v-if="sortDir === 'asc'">▲</span>
                          <span v-else-if="sortDir === 'desc'">▼</span>
                        </span>
                      </th>
                        <th class="hidden md:table-cell px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortColumn('ruang_id')">
                        Ruang
                        <span v-if="sortBy === 'ruang_id'">
                          <span v-if="sortDir === 'asc'">▲</span>
                          <span v-else-if="sortDir === 'desc'">▼</span>
                        </span>
                      </th>
                        <th class="hidden lg:table-cell px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortColumn('harga')">
                        Harga
                        <span v-if="sortBy === 'harga'">
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
                        <td class="px-2 sm:px-4 py-2 text-sm font-mono">{{ item.kode_barang }}</td>
                        <td class="px-2 sm:px-4 py-2 text-sm font-mono">{{ item.nomor_seri || '-' }}</td>
                        <td class="hidden md:table-cell px-4 py-2 text-sm">{{ item.merk || '-' }}</td>
                        <td class="hidden lg:table-cell px-4 py-2 text-sm">{{ item.tipe || '-' }}</td>
                        <td class="px-2 sm:px-4 py-2 text-sm">
                          <span :class="getKondisiBadgeClass(item.kondisi)" class="badge badge-xs sm:badge-sm">
                            {{ getKondisiLabel(item.kondisi) }}
                          </span>
                        </td>
                        <td class="hidden md:table-cell px-4 py-2 text-sm">{{ item.ruang?.nama || '-' }}</td>
                        <td class="hidden lg:table-cell px-4 py-2 text-sm">{{ formatCurrency(item.harga) }}</td>
                        <td class="px-2 sm:px-4 py-2 text-sm">
                          <div class="flex gap-1 sm:gap-2">
                            <div v-if="!item.deleted_at" class="tooltip" data-tip="Edit asset">
                              <button @click="editItem(item)" class="btn btn-xs sm:btn-sm btn-outline">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                              </svg>
                            </button>
                          </div>
                            <div v-if="!item.deleted_at" class="tooltip" data-tip="Hapus asset">
                              <button @click="deleteItem(item)" class="btn btn-xs sm:btn-sm btn-error">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                              </svg>
                            </button>
                          </div>
                            <div v-if="item.deleted_at" class="tooltip" data-tip="Restore asset">
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
                  <div class="flex gap-2">
                    <button class="btn btn-sm" :disabled="currentPage <= 1" @click="goToPage(currentPage - 1)">Sebelumnya</button>
                    <button class="btn btn-sm" :disabled="currentPage >= lastPage" @click="goToPage(currentPage + 1)">Berikutnya</button>
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
      <div class="modal-box w-11/12 max-w-4xl">
        <h3 class="font-bold text-lg mb-4">{{ editingItem ? 'Edit' : 'Tambah' }} Asset Detail</h3>
        <form @submit.prevent="openSaveModal(saveItem)">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <div class="form-control">
              <label class="label">
                <span class="label-text">Kode Barang *</span>
              </label>
              <select v-model="form.kode_barang" class="select select-bordered w-full" required>
                <option value="">Pilih Barang</option>
                <option v-for="barang in masterBarangModals" :key="barang.kode_barang" :value="barang.kode_barang">
                  {{ barang.kode_barang }} - {{ barang.nama_barang }}
                </option>
              </select>
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Nomor Seri</span>
              </label>
              <input v-model="form.nomor_seri" type="text" class="input input-bordered w-full" />
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Merk</span>
              </label>
              <input v-model="form.merk" type="text" class="input input-bordered w-full" />
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Tipe</span>
              </label>
              <input v-model="form.tipe" type="text" class="input input-bordered w-full" />
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Tahun Perolehan</span>
              </label>
              <input v-model="form.tahun_perolehan" type="number" min="1900" :max="new Date().getFullYear() + 1" class="input input-bordered w-full" />
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Ruang</span>
              </label>
              <select v-model="form.ruang_id" class="select select-bordered w-full">
                <option value="">Pilih Ruang</option>
                <option v-for="ruang in ruangs" :key="ruang.id" :value="ruang.id">
                  {{ ruang.nama }} ({{ ruang.puskesmas?.nama }})
                </option>
              </select>
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Kondisi</span>
              </label>
              <select v-model="form.kondisi" class="select select-bordered w-full">
                <option value="">Pilih Kondisi</option>
                <option value="baik">Baik</option>
                <option value="perbaikan">Perbaikan</option>
                <option value="rusak_berat">Rusak Berat</option>
              </select>
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">NIE</span>
              </label>
              <input v-model="form.nie" type="text" class="input input-bordered w-full" />
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Harga</span>
              </label>
              <input v-model="form.harga" type="number" min="0" step="0.01" class="input input-bordered w-full" />
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Kapitalisasi</span>
              </label>
              <input v-model="form.kapitalisasi" type="number" min="0" step="0.01" class="input input-bordered w-full" />
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Jumlah</span>
              </label>
              <input v-model="form.jumlah" type="number" min="1" class="input input-bordered w-full" />
            </div>
            <div class="form-control sm:col-span-2">
              <label class="label">
                <span class="label-text">Keterangan</span>
              </label>
              <textarea v-model="form.keterangan" class="textarea textarea-bordered w-full" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-action flex flex-col sm:flex-row justify-end gap-2 mt-6">
            <button type="button" @click="showCreateModal = false" class="btn btn-outline">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Import Modal -->
    <div v-if="showImportModal" class="modal modal-open">
      <div class="modal-box w-11/12 max-w-2xl">
        <h3 class="font-bold text-lg mb-4">Import Data Detail Barang Modal</h3>
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
                  <li>• Kolom wajib: Kode Barang, Nomor Seri</li>
                  <li>• Kolom opsional: Merk, Tipe, Kondisi, Harga, dll</li>
                  <li>• Download template terlebih dahulu untuk format yang benar</li>
                </ul>
              </div>
            </div>
            
            <div class="form-control">
              <label class="label">
                <span class="label-text">Pilih File Excel</span>
              </label>
              <input 
                ref="fileInput"
                type="file" 
                accept=".xlsx,.xls"
                @change="handleFileChange"
                class="file-input file-input-bordered w-full" 
                required
              />
              <label class="label">
                <span class="label-text-alt">Format: .xlsx atau .xls, maksimal 2MB</span>
              </label>
            </div>
          </div>
          
          <div class="modal-action flex flex-col sm:flex-row justify-end gap-2 mt-6">
            <button type="button" @click="showImportModal = false" class="btn btn-outline">Batal</button>
            <button type="submit" :disabled="!selectedFile || isImporting" class="btn btn-primary">
              <span v-if="isImporting" class="loading loading-spinner loading-sm"></span>
              {{ isImporting ? 'Mengimpor...' : 'Import Data' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Konfirmasi Hapus DaisyUI -->
    <div v-if="showDeleteModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">Konfirmasi Hapus</h3>
        <p>Apakah Anda yakin ingin menghapus asset detail <b>{{ itemToDelete?.nomor_seri || itemToDelete?.kode_barang }}</b>?</p>
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
        <p>Apakah Anda yakin ingin mengembalikan asset detail <b>{{ itemToRestore?.nomor_seri || itemToRestore?.kode_barang }}</b>?</p>
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
        <p>Apakah Anda yakin ingin menyimpan data asset detail ini?</p>
        <div class="modal-action">
          <button class="btn btn-outline" @click="showSaveModal = false">Batal</button>
          <button class="btn btn-primary" @click="confirmSave">Simpan</button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
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
  detailBarangModals: Object,
  masterBarangModals: Array,
  ruangs: Array,
  puskesmas: Array,
  filters: Object,
  config: Object
})

const search = ref(props.filters?.search || '')
const filterKondisi = ref(props.filters?.kondisi || '')
const filterRuang = ref(props.filters?.ruang_id || '')
const filterKodeBarang = ref(props.filters?.kode_barang || '')
const sortBy = ref(props.filters?.sortBy || 'kode_barang')
const sortDir = ref(props.filters?.sortDir || 'asc')
const showCreateModal = ref(false)
const editingItem = ref(null)
const showImportModal = ref(false)
const fileInput = ref(null)
const selectedFile = ref(null)
const isImporting = ref(false)
const isLoading = ref(false)

// Tambahkan state untuk modal konfirmasi hapus
const showDeleteModal = ref(false)
const itemToDelete = ref(null)

// Tambahkan state untuk modal konfirmasi restore
const showRestoreModal = ref(false)
const itemToRestore = ref(null)

// Tambahkan state untuk modal konfirmasi simpan
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
  kode_barang: '',
  nomor_seri: '',
  merk: '',
  tipe: '',
  tahun_perolehan: '',
  ruang_id: '',
  kondisi: '',
  nie: '',
  keterangan: '',
  harga: '',
  kapitalisasi: '',
  jumlah: 1
})

// Computed properties untuk data table
const tableData = computed(() => props.detailBarangModals?.data || [])
const currentPage = computed(() => props.detailBarangModals?.current_page || 1)
const lastPage = computed(() => props.detailBarangModals?.last_page || 1)
const perPage = computed(() => props.detailBarangModals?.per_page || 10)
const total = computed(() => props.detailBarangModals?.total || 0)

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
watch(filterKondisi, (newValue) => {
  router.get('/detail-barang-modal', buildQueryParams({
    search: search.value,
    kondisi: newValue,
    ruang_id: filterRuang.value,
    kode_barang: filterKodeBarang.value
  }), { preserveState: true, preserveScroll: true })
})

// Watch for search changes and trigger backend request
watch(search, (newValue) => {
  router.get('/detail-barang-modal', buildQueryParams({
    search: newValue,
    kondisi: filterKondisi.value,
    ruang_id: filterRuang.value,
    kode_barang: filterKodeBarang.value
  }), { preserveState: true, preserveScroll: true })
})

// Watch for ruang filter changes and trigger backend request
watch(filterRuang, (newValue) => {
  router.get('/detail-barang-modal', buildQueryParams({
    search: search.value,
    kondisi: filterKondisi.value,
    ruang_id: newValue,
    kode_barang: filterKodeBarang.value
  }), { preserveState: true, preserveScroll: true })
})

// Watch for kode barang filter changes and trigger backend request
watch(filterKodeBarang, (newValue) => {
  router.get('/detail-barang-modal', buildQueryParams({
    search: search.value,
    kondisi: filterKondisi.value,
    ruang_id: filterRuang.value,
    kode_barang: newValue
  }), { preserveState: true, preserveScroll: true })
})

// Pagination functions
const goToPage = (page) => {
  router.get('/detail-barang-modal', buildQueryParams({
    page: page,
    search: search.value,
    kondisi: filterKondisi.value,
    ruang_id: filterRuang.value,
    kode_barang: filterKodeBarang.value
  }), { preserveState: true, preserveScroll: true })
}

const changePerPage = (newPerPage) => {
  router.get('/detail-barang-modal', buildQueryParams({
    perPage: newPerPage,
    search: search.value,
    kondisi: filterKondisi.value,
    ruang_id: filterRuang.value,
    kode_barang: filterKodeBarang.value
  }), { preserveState: true, preserveScroll: true })
}

const sortColumn = (column) => {
  const newSortDir = sortBy.value === column && sortDir.value === 'asc' ? 'desc' : 'asc'
  sortBy.value = column
  sortDir.value = newSortDir
  
  router.get('/detail-barang-modal', buildQueryParams({
    page: currentPage.value,
    search: search.value,
    kondisi: filterKondisi.value,
    ruang_id: filterRuang.value,
    kode_barang: filterKodeBarang.value
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
    router.delete(`/detail-barang-modal/${itemToDelete.value.id}`, {
      onSuccess: () => {
        toast.success('Asset detail berhasil dihapus', 'Berhasil')
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
    router.patch(`/detail-barang-modal/${itemToRestore.value.id}/restore`, {
      onSuccess: () => {
        toast.success('Asset detail berhasil direstore', 'Berhasil')
      },
      onError: (err) => {
        toast.error('Gagal merestore asset detail', 'Error')
      }
    })
    showRestoreModal.value = false
    itemToRestore.value = null
  }
}

function saveItem() {
  if (editingItem.value) {
    router.put(`/detail-barang-modal/${editingItem.value.id}`, form.value)
  } else {
    router.post('/detail-barang-modal', form.value)
  }
  showCreateModal.value = false
  editingItem.value = null
  form.value = {
    kode_barang: '',
    nomor_seri: '',
    merk: '',
    tipe: '',
    tahun_perolehan: '',
    ruang_id: '',
    kondisi: '',
    nie: '',
    keterangan: '',
    harga: '',
    kapitalisasi: '',
    jumlah: 1
  }
}

async function downloadTemplate() {
  try {
    // Download template file
    const response = await fetch('/detail-barang-modal/template', {
      method: 'GET',
      headers: {
        'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      }
    })
    
    if (!response.ok) {
      const errorText = await response.text()
      console.error('Response error:', response.status, errorText)
      throw new Error(`Gagal mengunduh template: ${response.status}`)
    }
    
    const blob = await response.blob()
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = 'template_detail_barang_modal.xlsx'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
    
    // Show success notification
    toast.success('Template berhasil didownload', 'Berhasil')
  } catch (error) {
    console.error('Download error:', error)
    toast.error('Gagal mengunduh template', 'Error')
  }
}

async function exportData() {
  try {
    // Export data to Excel
    const response = await fetch('/detail-barang-modal/export')
    
    if (!response.ok) {
      throw new Error('Gagal mengexport data')
    }
    
    const blob = await response.blob()
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = 'detail_barang_modal_' + new Date().toISOString().slice(0, 10) + '.xlsx'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
    
    // Show success notification
    toast.success('Data berhasil diexport', 'Berhasil')
  } catch (error) {
    console.error('Export error:', error)
    toast.error('Gagal mengexport data', 'Error')
  }
}

async function importData() {
  if (!selectedFile.value) {
    toast.error('Pilih file terlebih dahulu', 'Error')
    return
  }

  isImporting.value = true
  
  try {
    const formData = new FormData()
    formData.append('file', selectedFile.value)
    
    // Get CSRF token from meta tag or use a fallback
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     document.querySelector('meta[name="_token"]')?.getAttribute('content')
    
    const response = await fetch('/detail-barang-modal/import', {
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
      showImportModal.value = false
      selectedFile.value = null
      // Refresh the page to show imported data
      router.reload()
    } else {
      toast.error(result.message, 'Gagal')
    }
  } catch (error) {
    console.error('Import error:', error)
    toast.error('Terjadi kesalahan saat mengimpor data', 'Error')
  } finally {
    isImporting.value = false
  }
}

function handleFileChange(event) {
  selectedFile.value = event.target.files[0]
}

// Helper functions
function getKondisiLabel(kondisi) {
  switch (kondisi) {
    case 'baik': return 'Baik'
    case 'perbaikan': return 'Perbaikan'
    case 'rusak_berat': return 'Rusak Berat'
    default: return 'Tidak Diketahui'
  }
}

function getKondisiBadgeClass(kondisi) {
  switch (kondisi) {
    case 'baik': return 'badge-success'
    case 'perbaikan': return 'badge-warning'
    case 'rusak_berat': return 'badge-error'
    default: return 'badge-secondary'
  }
}

function formatCurrency(amount) {
  if (!amount) return '-'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR'
  }).format(amount)
}
</script> 