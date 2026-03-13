<template>
  <AuthenticatedLayout>
    <Head title="Laporan Stok" />
    
    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filter Periode -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-semibold mb-4">Filter Laporan</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Periode</span>
                </label>
                <input 
                v-model="filter.periode" 
                  type="text" 
                placeholder="Contoh: 2024-01"
                  class="input input-bordered"
              />
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Lokasi</span>
                </label>
                <select v-model="filter.lokasi_id" class="select select-bordered">
                  <option value="">Semua Lokasi</option>
                  <option value="1">Ruang Poli Umum</option>
                  <option value="2">Ruang Poli Gigi</option>
                  <option value="3">Ruang Laboratorium</option>
                  <option value="4">Ruang Farmasi</option>
                  <option value="5">Ruang Administrasi</option>
                </select>
              </div>
              <div class="form-control">
                <label class="label">
                  <span class="label-text">Status</span>
                </label>
                <select v-model="filter.status" class="select select-bordered">
                  <option value="">Semua Status</option>
                  <option value="tersedia">Tersedia</option>
                  <option value="stok_menipis">Stok Menipis</option>
                  <option value="habis">Habis</option>
                </select>
              </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 mt-4">
              <div class="tooltip" data-tip="Generate laporan berdasarkan filter">
                <button @click="generateLaporan" class="btn btn-primary btn-sm sm:btn-md">
                  <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                  <span class="hidden sm:inline">Generate Laporan</span>
                  <span class="sm:hidden">Generate</span>
                </button>
              </div>
              <div class="tooltip" data-tip="Export laporan ke Excel">
                <button @click="exportExcel" class="btn btn-outline btn-sm sm:btn-md">
                  <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <span class="hidden sm:inline">Export Excel</span>
                  <span class="sm:hidden">Export</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Ringkasan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-6 h-6 sm:w-8 sm:h-8 bg-blue-500 rounded-md flex items-center justify-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                  </div>
                </div>
                <div class="ml-3 sm:ml-4">
                  <p class="text-xs sm:text-sm font-medium text-gray-500">Total Barang</p>
                  <p class="text-lg sm:text-2xl font-semibold text-gray-900">{{ summary.total_barang }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-6 h-6 sm:w-8 sm:h-8 bg-green-500 rounded-md flex items-center justify-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                </div>
                <div class="ml-3 sm:ml-4">
                  <p class="text-xs sm:text-sm font-medium text-gray-500">Total Nilai</p>
                  <p class="text-lg sm:text-2xl font-semibold text-gray-900">{{ formatCurrency(summary.total_nilai) }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-6 h-6 sm:w-8 sm:h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                  </div>
                </div>
                <div class="ml-3 sm:ml-4">
                  <p class="text-xs sm:text-sm font-medium text-gray-500">Stok Rendah</p>
                  <p class="text-lg sm:text-2xl font-semibold text-gray-900">{{ summary.stok_rendah }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-6 h-6 sm:w-8 sm:h-8 bg-red-500 rounded-md flex items-center justify-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </div>
                </div>
                <div class="ml-3 sm:ml-4">
                  <p class="text-xs sm:text-sm font-medium text-gray-500">Stok Habis</p>
                  <p class="text-lg sm:text-2xl font-semibold text-gray-900">{{ summary.stok_habis }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
              <h3 class="text-lg font-semibold mb-4">Grafik Nilai Stok per Kategori</h3>
              <BarChart :data="categoryChartData" />
            </div>
          </div>
          
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
              <h3 class="text-lg font-semibold mb-4">Grafik Jumlah Barang per Lokasi</h3>
              <BarChart :data="locationChartData" />
            </div>
          </div>
        </div>

        <!-- Tabel Laporan -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold">Detail Laporan Stok</h3>
              <div class="flex gap-2">
                <div class="tooltip" data-tip="Print laporan stok">
                  <button @click="printLaporan" class="btn btn-outline btn-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print
                  </button>
                </div>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Periode</th>
                    <th>Stok Awal</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Stok Akhir</th>
                    <th>Harga Satuan</th>
                    <th>Nilai Akhir</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in paginatedData" :key="item.id">
                    <td>{{ item.masterBarangHabisPakai?.kode_barang || '-' }}</td>
                    <td>{{ item.masterBarangHabisPakai?.nama_barang || '-' }}</td>
                    <td>
                      <span class="badge badge-outline">{{ item.masterBarangHabisPakai?.kategori || '-' }}</span>
                    </td>
                    <td>{{ item.periode }}</td>
                    <td>{{ item.stok_awal }}</td>
                    <td>{{ item.masuk }}</td>
                    <td>{{ item.keluar }}</td>
                    <td>{{ item.stok_akhir }}</td>
                    <td>{{ formatCurrency(item.harga_satuan) }}</td>
                    <td>{{ formatCurrency(item.nilai_akhir) }}</td>
                    <td>{{ item.lokasi?.nama || '-' }}</td>
                    <td>
                      <span :class="getStatusClass(item.stok_akhir)">
                        {{ getStatusText(item.stok_akhir) }}
                      </span>
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
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import BarChart from '@/Components/Charts/BarChart.vue'

const props = defineProps({
  stoks: {
    type: Array,
    default: () => []
  },
  summary: {
    type: Object,
    default: () => ({
      total_nilai: 0,
      total_barang: 0,
      stok_habis: 0,
      stok_rendah: 0
    })
  },
  chartData: {
    type: Object,
    default: () => ({})
  },
  locationChartData: {
    type: Object,
    default: () => ({})
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

// Filter
const filter = ref({
  periode: props.filters.periode || '',
  lokasi_id: props.filters.lokasi_id || '',
  status: props.filters.status || ''
})

// Computed
const filteredStoks = computed(() => {
  let filtered = props.stoks

  if (filter.value.periode) {
    filtered = filtered.filter(item => item.periode === filter.value.periode)
  }

  if (filter.value.lokasi_id) {
    filtered = filtered.filter(item => item.lokasi_id == filter.value.lokasi_id)
  }

  if (filter.value.status) {
    filtered = filtered.filter(item => item.stok_akhir <= 0)
  }

  return filtered
})

const currentPage = ref(1);
const perPage = ref(10);

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  const end = start + perPage.value;
  return filteredStoks.value.slice(start, end);
});

const totalPages = computed(() => Math.ceil(filteredStoks.value.length / perPage.value));

// Chart data
const categoryChartData = computed(() => {
  const categories = Object.keys(props.chartData)
  const values = Object.values(props.chartData).map(item => item.total_nilai)
  
  return {
    labels: categories,
    datasets: [{
      label: 'Total Nilai (Rp)',
      data: values,
      backgroundColor: [
        '#3B82F6',
        '#10B981',
        '#F59E0B',
        '#EF4444',
        '#8B5CF6',
        '#06B6D4',
        '#84CC16',
        '#F97316'
      ]
    }]
  }
})

const locationChartData = computed(() => {
  const locations = Object.keys(props.locationChartData)
  const values = Object.values(props.locationChartData).map(item => item.total_barang)
  
  return {
    labels: locations,
    datasets: [{
      label: 'Jumlah Barang',
      data: values,
      backgroundColor: [
        '#3B82F6',
        '#10B981',
        '#F59E0B',
        '#EF4444',
        '#8B5CF6'
      ]
    }]
  }
})

// Methods
const getStatusClass = (stok) => {
  if (stok <= 0) return 'badge badge-error'
  if (stok <= 10) return 'badge badge-warning'
  return 'badge badge-success'
}

const getStatusText = (stok) => {
  if (stok <= 0) return 'Habis'
  if (stok <= 10) return 'Stok Menipis'
  return 'Tersedia'
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID')
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR'
  }).format(amount)
}

const generateLaporan = () => {
  router.get('/laporan/stok', filter.value, {
    preserveState: true,
    preserveScroll: true
  })
}

const exportExcel = () => {
  router.visit('/laporan/stok/export', { 
    method: 'get',
    data: filter.value 
  })
}

const printLaporan = () => {
  window.print()
}
</script> 