<template>
  <AuthenticatedLayout>
    <Head title="Master Barang Modal" />
    
    <div class="py-6">
      <div class="w-full">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Master Barang Modal</h2>
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
                <div class="tooltip" data-tip="Tambah asset baru">
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
                  placeholder="Nama atau kode asset..."
                  class="input input-bordered w-full"
                />
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Kategori</span>
                </label>
                <select v-model="filterKategori" class="select select-bordered w-full">
                  <option value="">Semua Kategori</option>
                  <option value="Elektronik">Elektronik</option>
                  <option value="Furniture">Furniture</option>
                  <option value="Kendaraan">Kendaraan</option>
                  <option value="Alat Medis">Alat Medis</option>
                  <option value="Lainnya">Lainnya</option>
                </select>
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Status Kondisi</span>
                </label>
                <select v-model="filterKondisi" class="select select-bordered w-full">
                  <option value="">Semua Kondisi</option>
                  <option value="baik">Baik</option>
                  <option value="perbaikan">Perbaikan</option>
                  <option value="rusak_berat">Rusak Berat</option>
                  <option value="tidak_ada_data">Tidak Ada Data</option>
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
                          <span class="hidden sm:inline">Kode</span>
                          <span class="sm:hidden">Kode</span>
                        <span v-if="sortBy === 'kode_barang'">
                          <span v-if="sortDir === 'asc'">▲</span>
                          <span v-else-if="sortDir === 'desc'">▼</span>
                        </span>
                      </th>
                        <th class="px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortColumn('nama_barang')">
                          <span class="hidden sm:inline">Nama Asset</span>
                          <span class="sm:hidden">Nama</span>
                        <span v-if="sortBy === 'nama_barang'">
                          <span v-if="sortDir === 'asc'">▲</span>
                          <span v-else-if="sortDir === 'desc'">▼</span>
                        </span>
                      </th>
                        <th class="hidden md:table-cell px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortColumn('satuan')">
                        Satuan
                        <span v-if="sortBy === 'satuan'">
                          <span v-if="sortDir === 'asc'">▲</span>
                          <span v-else-if="sortDir === 'desc'">▼</span>
                        </span>
                      </th>
                        <th class="px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortColumn('kategori')">
                          <span class="hidden sm:inline">Kategori</span>
                          <span class="sm:hidden">Kat</span>
                        <span v-if="sortBy === 'kategori'">
                          <span v-if="sortDir === 'asc'">▲</span>
                          <span v-else-if="sortDir === 'desc'">▼</span>
                        </span>
                      </th>
                        <th class="hidden md:table-cell px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Status Kondisi
                        </th>
                        <th class="px-2 sm:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="item in tableData" :key="item.id">
                        <td class="px-2 sm:px-4 py-2 text-sm">{{ item.kode_barang }}</td>
                        <td class="px-2 sm:px-4 py-2 text-sm">{{ item.nama_barang }}</td>
                        <td class="hidden md:table-cell px-4 py-2 text-sm">{{ item.satuan }}</td>
                        <td class="px-2 sm:px-4 py-2 text-sm">{{ item.kategori }}</td>
                        <td class="hidden md:table-cell px-4 py-2 text-sm">
                          <div class="flex flex-col gap-1">
                            <!-- Status badges with responsive text -->
                            <div class="flex flex-wrap gap-1">
                              <span v-if="getKondisiCount(item, 'baik') > 0" 
                                    class="badge badge-success text-xs sm:text-sm">
                                <span class="hidden sm:inline">Baik:</span>
                                <span class="sm:hidden">B:</span>
                                {{ getKondisiCount(item, 'baik') }}
                              </span>
                              <span v-if="getKondisiCount(item, 'perbaikan') > 0" 
                                    class="badge badge-warning text-xs sm:text-sm">
                                <span class="hidden sm:inline">Perbaikan:</span>
                                <span class="sm:hidden">P:</span>
                                {{ getKondisiCount(item, 'perbaikan') }}
                              </span>
                              <span v-if="getKondisiCount(item, 'rusak_berat') > 0" 
                                    class="badge badge-error text-xs sm:text-sm">
                                <span class="hidden sm:inline">Rusak Berat:</span>
                                <span class="sm:hidden">RB:</span>
                                {{ getKondisiCount(item, 'rusak_berat') }}
                              </span>
                              <span v-if="getKondisiCount(item, 'baik') + getKondisiCount(item, 'perbaikan') + getKondisiCount(item, 'rusak_berat') === 0" 
                                    class="badge badge-secondary text-xs sm:text-sm">
                                <span class="hidden sm:inline">Tidak Ada Data</span>
                                <span class="sm:hidden">-</span>
                              </span>
                            </div>
                            <!-- Total summary -->
                            <div class="text-xs text-gray-500">
                              <span class="hidden sm:inline">Total:</span>
                              <span class="sm:hidden">T:</span>
                              {{ getTotalDetailCount(item) }} item
                            </div>
                          </div>
                        </td>
                        <td class="px-2 sm:px-4 py-2 text-sm">
                          <div class="flex gap-1 sm:gap-2">
                            <button @click="showDetailModal(item)" class="btn btn-xs sm:btn-sm btn-info" title="Detail Kondisi">
                              <i class="fas fa-list"></i> Detail
                            </button>
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
      <div class="modal-box w-11/12 max-w-2xl">
        <h3 class="font-bold text-lg mb-4">{{ editingItem ? 'Edit' : 'Tambah' }} Asset</h3>
        <form @submit.prevent="openSaveModal(saveItem)">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <div class="form-control">
              <label class="label">
                <span class="label-text">Kode Barang *</span>
              </label>
              <input 
                v-model="form.kode_barang" 
                type="text" 
                class="input input-bordered w-full" 
                :class="{ 'input-error': kodeBarangError }"
                placeholder="Masukkan 12 digit angka"
                maxlength="12"
                @input="validateKodeBarang"
                required 
              />
              <label class="label" v-if="kodeBarangError">
                <span class="label-text-alt text-error">{{ kodeBarangError }}</span>
              </label>
              <label class="label" v-else>
                <span class="label-text-alt">Kode barang harus berupa angka dengan tepat 12 digit</span>
              </label>
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Nama Barang</span>
              </label>
              <input v-model="form.nama_barang" type="text" class="input input-bordered w-full" required />
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Satuan</span>
              </label>
              <input v-model="form.satuan" type="text" class="input input-bordered w-full" required />
            </div>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Kategori</span>
              </label>
              <select v-model="form.kategori" class="select select-bordered w-full" required>
                <option value="">Pilih Kategori</option>
                <option value="Elektronik">Elektronik</option>
                <option value="Furniture">Furniture</option>
                <option value="Kendaraan">Kendaraan</option>
                <option value="Alat Medis">Alat Medis</option>
                <option value="Lainnya">Lainnya</option>
              </select>
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
        <h3 class="font-bold text-lg mb-4">Import Data Barang Modal</h3>
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
        <p>Apakah Anda yakin ingin menghapus asset <b>{{ itemToDelete?.nama_barang }}</b>?</p>
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
        <p>Apakah Anda yakin ingin mengembalikan asset <b>{{ itemToRestore?.nama_barang }}</b>?</p>
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
        <p>Apakah Anda yakin ingin menyimpan data asset ini?</p>
        <div class="modal-action">
          <button class="btn btn-outline" @click="showSaveModal = false">Batal</button>
          <button class="btn btn-primary" @click="confirmSave">Simpan</button>
        </div>
      </div>
    </div>

    <!-- Modal Detail Kondisi Barang -->
    <div v-if="showDetail" class="modal modal-open">
      <div class="modal-box w-11/12 max-w-7xl">
        <div class="flex justify-between items-center mb-4">
          <h3 class="font-bold text-lg">Detail Kondisi Barang: {{ detailItem?.nama_barang }}</h3>
          <div class="text-sm text-gray-500">
            Total: {{ getTotalDetailCount(detailItem) }} item
          </div>
        </div>
        
        <!-- Informasi Master Barang Modal -->
        <div class="bg-base-200 p-4 rounded-lg mb-4">
          <h4 class="font-semibold text-md mb-2">Informasi Master Barang Modal</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
            <div>
              <span class="font-medium">Kode Barang:</span>
              <span class="ml-2 font-mono">{{ detailItem?.kode_barang }}</span>
            </div>
            <div>
              <span class="font-medium">Nama Barang:</span>
              <span class="ml-2">{{ detailItem?.nama_barang }}</span>
            </div>
            <div>
              <span class="font-medium">Satuan:</span>
              <span class="ml-2">{{ detailItem?.satuan }}</span>
            </div>
            <div>
              <span class="font-medium">Kategori:</span>
              <span class="ml-2">{{ detailItem?.kategori }}</span>
            </div>
            <div class="md:col-span-2 lg:col-span-3">
              <span class="font-medium">Keterangan:</span>
              <span class="ml-2">{{ detailItem?.keterangan || '-' }}</span>
            </div>
          </div>
        </div>
        <div class="mb-4">
          <div class="stats shadow">
            <div class="stat">
              <div class="stat-title">Total Item</div>
              <div class="stat-value text-primary">{{ getTotalDetailCount(detailItem) }}</div>
            </div>
            <div class="stat">
              <div class="stat-title">Kondisi Baik</div>
              <div class="stat-value text-success">{{ getKondisiCount(detailItem, 'baik') }}</div>
            </div>
            <div class="stat">
              <div class="stat-title">Perbaikan</div>
              <div class="stat-value text-warning">{{ getKondisiCount(detailItem, 'perbaikan') }}</div>
            </div>
            <div class="stat">
              <div class="stat-title">Rusak Berat</div>
              <div class="stat-value text-error">{{ getKondisiCount(detailItem, 'rusak_berat') }}</div>
            </div>
          </div>
        </div>
        
                <div v-if="detailItem && detailItem.detail_barang_modals && detailItem.detail_barang_modals.length">
          <!-- Detail Pagination Controls -->
          <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600">Tampilkan:</span>
              <select class="select select-bordered select-sm w-20" :value="detailPerPage" @change="e => changeDetailPerPage(Number(e.target.value))">
                <option v-for="size in [5, 10, 25, 50]" :key="size" :value="size">{{ size }}</option>
              </select>
            </div>
            <div class="text-sm text-gray-500">
              Menampilkan {{ detailStartItem }} - {{ detailEndItem }} dari {{ detailItems.length }} data
            </div>
          </div>
          
          <div class="overflow-x-auto max-w-full">
            <table class="table table-zebra w-full table-compact">
              <thead>
                <tr>
                  <th class="w-12">No</th>
                  <th class="w-32">Nomor Seri</th>
                  <th class="w-24">Merk</th>
                  <th class="w-24">Tipe</th>
                  <th class="w-20">Tahun</th>
                  <th class="w-16">Unit</th>
                  <th class="w-24">Ruang</th>
                  <th class="w-32">Puskesmas</th>
                  <th class="w-24">NIE</th>
                  <th class="w-20">Kondisi</th>
                  <th class="min-w-32">Keterangan</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(d, idx) in paginatedDetailItems" :key="d.id">
                  <td class="text-center">{{ detailStartItem + idx }}</td>
                  <td class="font-mono text-xs">{{ d.nomor_seri }}</td>
                  <td class="text-xs truncate" :title="d.merk || '-'">{{ d.merk || '-' }}</td>
                  <td class="text-xs truncate" :title="d.tipe || '-'">{{ d.tipe || '-' }}</td>
                  <td class="text-xs text-center">{{ d.tahun_perolehan || '-' }}</td>
                  <td class="text-xs text-center">{{ detailItem?.satuan || '-' }}</td>
                  <td class="text-xs truncate" :title="getRuangName(d)">{{ getRuangName(d) }}</td>
                  <td class="text-xs truncate" :title="getPuskesmasName(d)">{{ getPuskesmasName(d) }}</td>
                  <td class="font-mono text-xs">{{ d.nie || '-' }}</td>
                  <td class="text-center">
                    <span :class="{
                      'badge badge-success badge-xs': d.kondisi === 'baik',
                      'badge badge-warning badge-xs': d.kondisi === 'perbaikan',
                      'badge badge-error badge-xs': d.kondisi === 'rusak_berat',
                      'badge badge-secondary badge-xs': !['baik','perbaikan','rusak_berat'].includes(d.kondisi)
                    }">
                      {{ getShortKondisiLabel(d.kondisi) }}
                    </span>
                  </td>
                  <td class="text-xs">
                    <div class="tooltip" :data-tip="d.keterangan">
                      <span class="truncate block max-w-32">{{ d.keterangan || '-' }}</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Detail Pagination -->
          <div v-if="detailTotalPages > 1" class="flex justify-between items-center mt-4">
            <div class="text-sm text-gray-500">
              Halaman {{ detailCurrentPage }} dari {{ detailTotalPages }}
            </div>
            <div class="flex gap-2">
              <button 
                class="btn btn-sm" 
                :disabled="detailCurrentPage <= 1" 
                @click="goToDetailPage(detailCurrentPage - 1)"
              >
                Sebelumnya
              </button>
              <button 
                class="btn btn-sm" 
                :disabled="detailCurrentPage >= detailTotalPages" 
                @click="goToDetailPage(detailCurrentPage + 1)"
              >
                Berikutnya
              </button>
            </div>
          </div>
        </div>
        <div v-else class="text-center text-gray-400 py-6">
          <i class="fas fa-box-open text-4xl mb-4"></i>
          <p>Tidak ada data detail barang.</p>
        </div>
        <div class="modal-action mt-6">
          <button class="btn btn-outline" @click="showDetail = false">Tutup</button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
// Hapus import Modal
// import Modal from '@/Components/Modal.vue'
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
  barangs: Object,
  filters: Object,
  kategoris: Array,
  ruangs: Array
})

const search = ref(props.filters?.search || '')
const filterKategori = ref(props.filters?.kategori || '')
const filterKondisi = ref(props.filters?.kondisi || '')
const sortBy = ref(props.filters?.sortBy || 'nama_barang')
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

// Inertia loading event
// onMounted(() => {
//   if (page.props.flash?.success) {
//     toast.success(page.props.flash.success, 'Berhasil')
//   }
//   if (page.props.flash?.error) {
//     toast.error(page.props.flash.error, 'Gagal')
//   }
//   const start = () => { isLoading.value = true }
//   const end = () => { isLoading.value = false }
//   window.addEventListener('inertia:start', start)
//   window.addEventListener('inertia:finish', end)
//   // Clean up
//   onUnmounted(() => {
//     window.removeEventListener('inertia:start', start)
//     window.removeEventListener('inertia:finish', end)
//   })
// })

const form = ref({
  kode_barang: '',
  nama_barang: '',
  satuan: '',
  kategori: '',
  keterangan: ''
})

const kodeBarangError = ref('')

const validateKodeBarang = () => {
  const kode = form.value.kode_barang
  if (!kode) {
    kodeBarangError.value = ''
    return
  }
  
  // Hanya boleh angka
  if (!/^\d+$/.test(kode)) {
    kodeBarangError.value = 'Kode barang hanya boleh berisi angka'
    return
  }
  
  // Harus tepat 12 digit
  if (kode.length !== 12) {
    kodeBarangError.value = 'Kode barang harus tepat 12 digit'
    return
  }
  
  kodeBarangError.value = ''
}

const kategoriOptions = [
  { value: 'Elektronik', label: 'Elektronik' },
  { value: 'Furniture', label: 'Furniture' },
  { value: 'Kendaraan', label: 'Kendaraan' },
  { value: 'Alat Medis', label: 'Alat Medis' },
  { value: 'Lainnya', label: 'Lainnya' }
]

const rawData = computed(() => props.barangs?.data || [])

const filteredData = computed(() => {
  let data = rawData.value
  if (search.value) {
    data = data.filter(item =>
      item.nama_barang.toLowerCase().includes(search.value.toLowerCase()) ||
      item.kode_barang.toLowerCase().includes(search.value.toLowerCase())
    )
  }
  if (filterKategori.value) {
    data = data.filter(item => item.kategori === filterKategori.value)
  }
  return data
})





// Computed properties untuk data table
const tableData = computed(() => props.barangs?.data || [])
const currentPage = computed(() => props.barangs?.current_page || 1)
const lastPage = computed(() => props.barangs?.last_page || 1)
const perPage = computed(() => props.barangs?.per_page || 10)
const total = computed(() => props.barangs?.total || 0)

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
  router.get('/master-barang-modal', buildQueryParams({
    search: search.value,
    kategori: newValue,
    kondisi: filterKondisi.value
  }), { preserveState: true, preserveScroll: true })
})

// Watch for search changes and trigger backend request
watch(search, (newValue) => {
  router.get('/master-barang-modal', buildQueryParams({
    search: newValue,
    kategori: filterKategori.value,
    kondisi: filterKondisi.value
  }), { preserveState: true, preserveScroll: true })
})

// Watch for kondisi filter changes and trigger backend request
watch(filterKondisi, (newValue) => {
  router.get('/master-barang-modal', buildQueryParams({
    search: search.value,
    kategori: filterKategori.value,
    kondisi: newValue
  }), { preserveState: true, preserveScroll: true })
})

// Pagination functions
const goToPage = (page) => {
  router.get('/master-barang-modal', buildQueryParams({
    page: page,
    search: search.value,
    kategori: filterKategori.value,
    kondisi: filterKondisi.value
  }), { preserveState: true, preserveScroll: true })
}

const changePerPage = (newPerPage) => {
  router.get('/master-barang-modal', buildQueryParams({
    perPage: newPerPage,
    search: search.value,
    kategori: filterKategori.value,
    kondisi: filterKondisi.value
  }), { preserveState: true, preserveScroll: true })
}

const sortColumn = (column) => {
  const newSortDir = sortBy.value === column && sortDir.value === 'asc' ? 'desc' : 'asc'
  sortBy.value = column
  sortDir.value = newSortDir
  
  router.get('/master-barang-modal', buildQueryParams({
    page: currentPage.value,
    search: search.value,
    kategori: filterKategori.value,
    kondisi: filterKondisi.value
  }), { preserveState: true, preserveScroll: true })
}



function editItem(item) {
  editingItem.value = item
  form.value = { ...item }
  kodeBarangError.value = ''
  showCreateModal.value = true
}

function deleteItem(item) {
  itemToDelete.value = item
  showDeleteModal.value = true
}

function confirmDelete() {
  if (itemToDelete.value) {
    router.delete(`/master-barang-modal/${itemToDelete.value.id}`, {
      onSuccess: () => {
        toast.success('Asset berhasil dihapus', 'Berhasil')
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
    router.patch(`/master-barang-modal/${itemToRestore.value.id}/restore`, {
      onSuccess: () => {
        toast.success('Asset berhasil direstore', 'Berhasil')
      },
      onError: (err) => {
        toast.error('Gagal merestore asset', 'Error')
      }
    })
    showRestoreModal.value = false
    itemToRestore.value = null
  }
}

function saveItem() {
  // Validasi kode barang sebelum submit
  validateKodeBarang()
  
  if (kodeBarangError.value) {
    toast.error('Mohon perbaiki kode barang terlebih dahulu', 'Validasi Gagal')
    return
  }
  
  if (editingItem.value) {
    router.put(`/master-barang-modal/${editingItem.value.id}`, form.value)
  } else {
    router.post('/master-barang-modal', form.value)
  }
  showCreateModal.value = false
  editingItem.value = null
  form.value = {
    kode_barang: '',
    nama_barang: '',
    satuan: '',
    kategori: '',
    keterangan: ''
  }
  kodeBarangError.value = ''
}

async function downloadTemplate() {
  try {
    // Download template file
    const response = await fetch('/master-barang-modal/template', {
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
    link.download = 'template_master_barang_modal.xlsx'
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
    const response = await fetch('/master-barang-modal/export')
    
    if (!response.ok) {
      throw new Error('Gagal mengexport data')
    }
    
    const blob = await response.blob()
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = 'master_barang_modal_' + new Date().toISOString().slice(0, 10) + '.xlsx'
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
    
    const response = await fetch('/master-barang-modal/import', {
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

// Helper functions for status kondisi
function getStatusKondisi(item) {
  const details = item.detail_barang_modals || []
  
  if (details.length === 0) {
    return 'tidak_ada_data'
  }

  const totalItems = details.length
  const baikCount = details.filter(d => d.kondisi === 'baik').length
  const perbaikanCount = details.filter(d => d.kondisi === 'perbaikan').length
  const rusakBeratCount = details.filter(d => d.kondisi === 'rusak_berat').length

  // Jika semua item dalam kondisi baik
  if (baikCount === totalItems) {
    return 'baik'
  }

  // Jika ada item rusak berat, status rusak berat
  if (rusakBeratCount > 0) {
    return 'rusak_berat'
  }

  // Jika ada item perbaikan, status perbaikan
  if (perbaikanCount > 0) {
    return 'perbaikan'
  }

  // Default ke baik jika tidak ada kondisi lain
  return 'baik'
}

function getStatusKondisiLabel(item) {
  const status = getStatusKondisi(item)
  const labels = {
    'baik': 'Baik',
    'perbaikan': 'Perbaikan',
    'rusak_berat': 'Rusak Berat',
    'tidak_ada_data': 'Tidak Ada Data'
  }
  return labels[status] || 'Tidak Diketahui'
}

function getStatusKondisiBadge(item) {
  const status = getStatusKondisi(item)
  const badges = {
    'baik': 'badge-success',
    'perbaikan': 'badge-warning',
    'rusak_berat': 'badge-error',
    'tidak_ada_data': 'badge-secondary'
  }
  return badges[status] || 'badge-secondary'
}

function getStatusKondisiSummary(item) {
  const details = item.detail_barang_modals || []
  
  if (details.length === 0) {
    return 'Tidak ada data detail barang'
  }

  const totalItems = details.length
  const baikCount = details.filter(d => d.kondisi === 'baik').length
  const perbaikanCount = details.filter(d => d.kondisi === 'perbaikan').length
  const rusakBeratCount = details.filter(d => d.kondisi === 'rusak_berat').length

  const summary = []
  if (baikCount > 0) {
    summary.push(`Baik: ${baikCount}`)
  }
  if (perbaikanCount > 0) {
    summary.push(`Perbaikan: ${perbaikanCount}`)
  }
  if (rusakBeratCount > 0) {
    summary.push(`Rusak Berat: ${rusakBeratCount}`)
  }

  return summary.join(', ') + ` (Total: ${totalItems})`
}

const showDetail = ref(false)
const detailItem = ref(null)
const detailCurrentPage = ref(1)
const detailPerPage = ref(10)

function showDetailModal(item) {
  detailItem.value = item
  detailCurrentPage.value = 1 // Reset to first page
  showDetail.value = true
}

// Computed properties for detail pagination
const detailItems = computed(() => {
  if (!detailItem.value?.detail_barang_modals) return []
  return detailItem.value.detail_barang_modals
})

const paginatedDetailItems = computed(() => {
  const start = (detailCurrentPage.value - 1) * detailPerPage.value
  const end = start + detailPerPage.value
  return detailItems.value.slice(start, end)
})

const detailTotalPages = computed(() => {
  return Math.ceil(detailItems.value.length / detailPerPage.value)
})

const detailStartItem = computed(() => {
  return (detailCurrentPage.value - 1) * detailPerPage.value + 1
})

const detailEndItem = computed(() => {
  return Math.min(detailCurrentPage.value * detailPerPage.value, detailItems.value.length)
})

// Detail pagination functions
function goToDetailPage(page) {
  detailCurrentPage.value = page
}

function changeDetailPerPage(newPerPage) {
  detailPerPage.value = newPerPage
  detailCurrentPage.value = 1
}

function getKondisiCount(item, kondisi) {
  if (!item.detail_barang_modals) return 0
  return item.detail_barang_modals.filter(d => d.kondisi === kondisi).length
}

function getTotalDetailCount(item) {
  if (!item.detail_barang_modals) return 0;
  return item.detail_barang_modals.length;
}

function getKondisiLabel(kondisi) {
  return kondisi === 'baik' ? 'Baik' : kondisi === 'perbaikan' ? 'Perbaikan' : kondisi === 'rusak_berat' ? 'Rusak Berat' : 'Tidak Diketahui'
}

function getShortKondisiLabel(kondisi) {
  return kondisi === 'baik' ? 'B' : kondisi === 'perbaikan' ? 'P' : kondisi === 'rusak_berat' ? 'RB' : '-'
}

function getShortKeterangan(keterangan) {
  if (!keterangan || typeof keterangan !== 'string') return '-';
  if (keterangan.length <= 20) {
    return keterangan;
  }
  return keterangan.substring(0, 20) + '...';
}

function getShortMerk(merk) {
  if (!merk || typeof merk !== 'string') return '-';
  if (merk.length <= 12) {
    return merk;
  }
  return merk.substring(0, 12) + '...';
}

function getShortTipe(tipe) {
  if (!tipe || typeof tipe !== 'string') return '-';
  if (tipe.length <= 12) {
    return tipe;
  }
  return tipe.substring(0, 12) + '...';
}



// Helper function to get ruang name
function getRuangName(detail) {
  if (!detail) return '-';
  if (detail.lokasi) {
    return detail.lokasi.nama || '-';
  }
  return '-';
}

// Helper function to get short ruang name for responsive display
function getShortRuangName(detail) {
  if (!detail) return '-';
  if (detail.lokasi) {
    const nama = detail.lokasi.nama || '-';
    if (typeof nama !== 'string') return '-';
    if (nama.length <= 12) return nama;
    return nama.substring(0, 12) + '...';
  }
  return '-';
}

// Helper function to get short unit name for responsive display
function getShortUnit(unit) {
  if (!unit || typeof unit !== 'string') return '-';
  if (unit.length <= 8) return unit;
  return unit.substring(0, 8) + '...';
}

// Helper function to get puskesmas name
function getPuskesmasName(detail) {
  if (!detail) return '-';
  if (detail.lokasi && detail.lokasi.puskesmas) {
    return detail.lokasi.puskesmas.nama || '-';
  }
  return '-';
}

// Helper function to get list of puskesmas for a master barang
function getPuskesmasList(item) {
  if (!item.detail_barang_modals) return [];
  
  const puskesmasMap = new Map();
  
  item.detail_barang_modals.forEach(detail => {
    if (detail.lokasi && detail.lokasi.puskesmas) {
      const puskesmasId = detail.lokasi.puskesmas.id;
      const puskesmasNama = detail.lokasi.puskesmas.nama;
      
      if (puskesmasMap.has(puskesmasId)) {
        puskesmasMap.get(puskesmasId).count++;
      } else {
        puskesmasMap.set(puskesmasId, {
          id: puskesmasId,
          nama: puskesmasNama,
          count: 1
        });
      }
    }
  });
  
  return Array.from(puskesmasMap.values());
}

// Helper function to get short puskesmas name for responsive display
function getShortPuskesmasName(nama) {
  if (!nama || typeof nama !== 'string') return '-';
  let shortName = nama.replace(/^Puskesmas\s+/i, '');
  if (shortName.length > 15) {
    const words = shortName.split(' ');
    shortName = words[0];
    if (shortName.length > 12) {
      shortName = shortName.substring(0, 12) + '...';
    }
  }
  return shortName;
}
</script>