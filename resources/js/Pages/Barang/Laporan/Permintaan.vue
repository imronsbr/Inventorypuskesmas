<template>
  <AuthenticatedLayout>
    <Head title="Laporan Permintaan Barang" />
    
    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filter Periode -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-semibold mb-4">Filter Laporan</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Periode Dari</span>
                </label>
                <input 
                v-model="filter.dari" 
                type="date"
                  class="input input-bordered"
              />
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Periode Sampai</span>
                </label>
                <input 
                v-model="filter.sampai" 
                type="date"
                  class="input input-bordered"
              />
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Status</span>
                </label>
                <select v-model="filter.status" class="select select-bordered">
                  <option value="">Semua Status</option>
                  <option value="diajukan">Diajukan</option>
                  <option value="disetujui">Disetujui</option>
                  <option value="ditolak">Ditolak</option>
                  <option value="diproses">Diproses</option>
                </select>
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Puskesmas</span>
                </label>
                <select v-model="filter.puskesmas" class="select select-bordered">
                  <option value="">Semua Puskesmas</option>
                  <option v-for="puskesmas in puskesmasList" :key="puskesmas.id" :value="puskesmas.id">
                    {{ puskesmas.nama }}
                  </option>
                </select>
              </div>
            </div>
            <div class="flex gap-2 mt-4">
              <button @click="generateLaporan" class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Generate Laporan
              </button>
              <button @click="exportExcel" class="btn btn-outline">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Excel
              </button>
            </div>
          </div>
        </div>

        <!-- Ringkasan -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Total Permintaan</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ summary.total_permintaan }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Disetujui</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ summary.disetujui }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Menunggu</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ summary.menunggu }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Ditolak</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ summary.ditolak }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Chart -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
              <h3 class="text-lg font-semibold mb-4">Status Permintaan</h3>
              <div class="h-64 flex items-center justify-center">
                <div class="text-center">
                  <svg class="w-32 h-32 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                  </svg>
                  <p class="text-gray-500 mt-2">Chart akan ditampilkan di sini</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
              <h3 class="text-lg font-semibold mb-4">Trend Permintaan</h3>
              <div class="h-64 flex items-center justify-center">
                <div class="text-center">
                  <svg class="w-32 h-32 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                  </svg>
                  <p class="text-gray-500 mt-2">Chart akan ditampilkan di sini</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabel Laporan -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold">Detail Laporan Permintaan</h3>
              <div class="flex gap-2">
                <button @click="printLaporan" class="btn btn-outline btn-sm">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                  </svg>
                  Print
                </button>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr>
                    <th>No. Permintaan</th>
                    <th>Tanggal</th>
                    <th>Pemohon</th>
                    <th>Puskesmas</th>
                    <th>Ruang</th>
                    <th>Jumlah Item</th>
                    <th>Status</th>
                    <th>Prioritas</th>
                    <th>Waktu Proses</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in paginatedData" :key="item.id">
                    <td>{{ item.nomor }}</td>
                    <td>{{ formatDate(item.tanggal_pesanan) }}</td>
                    <td>{{ item.pemohon?.nama }}</td>
                    <td>{{ item.puskesmas?.nama }}</td>
                    <td>{{ item.ruang?.nama }}</td>
                    <td>{{ item.jumlah_item }}</td>
                    <td>
                      <span :class="getStatusClass(item.status)">
                        {{ item.status }}
                      </span>
                    </td>
                    <td>
                      <span :class="getPrioritasClass(item.prioritas)">
                        {{ item.prioritas }}
                      </span>
                    </td>
                    <td>{{ item.waktu_proses }}</td>
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
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Data
const laporan = ref([])
const puskesmasList = ref([])
const currentPage = ref(1)
const perPage = ref(10)

// Filter
const filter = ref({
  dari: '',
  sampai: '',
  status: '',
  puskesmas: ''
})

// Summary
const summary = ref({
  total_permintaan: 0,
  disetujui: 0,
  menunggu: 0,
  ditolak: 0
})

// Computed
const filteredLaporan = computed(() => {
  let filtered = laporan.value

  if (filter.value.status) {
    filtered = filtered.filter(item => item.status === filter.value.status)
  }

  if (filter.value.puskesmas) {
    filtered = filtered.filter(item => item.puskesmas_id == filter.value.puskesmas)
  }

  return filtered
})

const totalPages = computed(() => {
  return Math.ceil(filteredLaporan.value.length / perPage.value)
})

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  const end = start + perPage.value;
  return filteredLaporan.value.slice(start, end);
});

// Methods
const getStatusClass = (status) => {
  const classes = {
    'diajukan': 'badge badge-warning',
    'disetujui': 'badge badge-success',
    'ditolak': 'badge badge-error',
    'diproses': 'badge badge-info'
  }
  return classes[status] || 'badge badge-outline'
}

const getPrioritasClass = (prioritas) => {
  const classes = {
    'tinggi': 'badge badge-error',
    'sedang': 'badge badge-warning',
    'rendah': 'badge badge-success'
  }
  return classes[prioritas] || 'badge badge-outline'
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID')
}

const generateLaporan = () => {
  // Implementasi generate laporan
}

const exportExcel = () => {
  router.visit('/laporan/permintaan/export', { 
    method: 'get',
    data: filter.value 
  })
}

const printLaporan = () => {
  window.print()
}

const updateSummary = () => {
  const data = laporan.value
  summary.value = {
    total_permintaan: data.length,
    disetujui: data.filter(item => item.status === 'disetujui').length,
    menunggu: data.filter(item => item.status === 'diajukan').length,
    ditolak: data.filter(item => item.status === 'ditolak').length
  }
}

// Load data
onMounted(() => {
  // Load puskesmas data
  puskesmasList.value = [
    { id: 1, nama: 'Puskesmas Kecamatan A' },
    { id: 2, nama: 'Puskesmas Kecamatan B' }
  ]

  // Load laporan data
  laporan.value = [
    {
      id: 1,
      nomor: 'PRM001',
      tanggal_pesanan: '2024-01-15',
      pemohon: { nama: 'John Doe' },
      puskesmas: { nama: 'Puskesmas Kecamatan A' },
      ruang: { nama: 'Poli Umum' },
      jumlah_item: 3,
      status: 'disetujui',
      prioritas: 'sedang',
      waktu_proses: '2 hari'
    },
    {
      id: 2,
      nomor: 'PRM002',
      tanggal_pesanan: '2024-01-14',
      pemohon: { nama: 'Jane Smith' },
      puskesmas: { nama: 'Puskesmas Kecamatan B' },
      ruang: { nama: 'Poli Anak' },
      jumlah_item: 2,
      status: 'diajukan',
      prioritas: 'tinggi',
      waktu_proses: '-'
    },
    {
      id: 3,
      nomor: 'PRM003',
      tanggal_pesanan: '2024-01-13',
      pemohon: { nama: 'Bob Johnson' },
      puskesmas: { nama: 'Puskesmas Kecamatan A' },
      ruang: { nama: 'Ruang Admin' },
      jumlah_item: 1,
      status: 'ditolak',
      prioritas: 'rendah',
      waktu_proses: '1 hari'
    }
  ]

  updateSummary()
})
</script> 