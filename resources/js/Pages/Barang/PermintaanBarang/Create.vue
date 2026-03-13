<template>
  <AuthenticatedLayout>
    <Head title="Buat Permintaan Barang" />
    
    <div class="py-6">
      <div class="w-full">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h2 class="text-2xl font-bold text-gray-900">Buat Permintaan Barang</h2>
              <button @click="savePermintaan" class="btn btn-primary" :disabled="isSubmitting">
                <svg v-if="isSubmitting" class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ isSubmitting ? 'Menyimpan...' : 'Simpan Permintaan' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="savePermintaan">
              <!-- Informasi Permintaan -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="form-control w-full">
                  <label class="label">
                    <span class="label-text">Nomor Permintaan</span>
                    <span class="label-text-alt">Opsional</span>
                  </label>
                  <input v-model="form.nomor_permintaan" type="text" class="input input-bordered w-full" readonly />
                </div>
                <div class="form-control w-full">
                  <label class="label">
                    <span class="label-text">Tanggal Permintaan</span>
                  </label>
                  <input v-model="form.tanggal_permintaan" type="date" class="input input-bordered w-full" required />
                </div>
                <div class="form-control w-full">
                  <label class="label">
                    <span class="label-text">Pemohon</span>
                  </label>
                  <input v-model="form.pemohon" type="text" class="input input-bordered w-full" required />
                </div>
                <div class="form-control w-full">
                  <label class="label">
                    <span class="label-text">Ruang/Unit</span>
                  </label>
                  <select v-model="form.ruang_id" class="select select-bordered w-full" required>
                    <option value="">Pilih Ruang</option>
                    <option v-for="ruang in ruangs" :key="ruang.id" :value="ruang.id">
                      {{ ruang.nama_ruang }}
                    </option>
                  </select>
                </div>
                <div class="form-control w-full md:col-span-2">
                  <label class="label">
                    <span class="label-text">Alasan Permintaan</span>
                  </label>
                  <textarea v-model="form.alasan" rows="3" class="textarea textarea-bordered w-full" required></textarea>
                </div>
              </div>

              <!-- Daftar Barang -->
              <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-lg font-semibold">Daftar Barang</h3>
                  <button type="button" @click="addItem" class="btn btn-outline btn-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Barang
                  </button>
                </div>

                <div class="overflow-x-auto">
                  <table class="table table-zebra w-full">
                    <thead>
                      <tr>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Satuan</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item, index) in form.items" :key="index">
                        <td>
                          <select v-model="item.barang_id" class="select select-bordered w-full" required>
                            <option value="">Pilih Barang</option>
                            <option v-for="barang in barangs" :key="barang.id" :value="barang.id">
                              {{ barang.nama_barang }} ({{ barang.kode_barang }})
                            </option>
                          </select>
                        </td>
                        <td>
                          <input v-model.number="item.jumlah" type="number" class="input input-bordered w-20" min="1" required />
                        </td>
                        <td>
                          <span v-if="getBarangById(item.barang_id)">
                            {{ getBarangById(item.barang_id).satuan }}
                          </span>
                        </td>
                        <td>
                          <input v-model="item.keterangan" type="text" class="input input-bordered" placeholder="Keterangan khusus" />
                        </td>
                        <td>
                          <button type="button" @click="removeItem(index)" class="btn btn-sm btn-error">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div v-if="form.items.length === 0" class="text-center py-8 text-gray-500">
                  <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                  </svg>
                  <p>Belum ada barang yang ditambahkan</p>
                  <button type="button" @click="addItem" class="btn btn-outline mt-2">Tambah Barang Pertama</button>
                </div>
              </div>

              <!-- Preview -->
              <div v-if="form.items.length > 0" class="bg-gray-50 p-4 rounded-lg mb-6">
                <h4 class="font-semibold mb-3">Ringkasan Permintaan</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                  <div>
                    <span class="font-medium">Total Item:</span> {{ form.items.length }}
                  </div>
                  <div>
                    <span class="font-medium">Total Jumlah:</span> {{ totalJumlah }}
                  </div>
                  <div>
                    <span class="font-medium">Status:</span> 
                    <span class="badge badge-info">Draft</span>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex justify-end gap-2 mt-6">
                <div class="tooltip" data-tip="Batal dan kembali ke daftar">
                  <button type="button" @click="$inertia.visit('/barang/permintaan')" class="btn btn-outline">Batal</button>
                </div>
                <div class="tooltip" data-tip="Simpan permintaan barang">
                  <button type="submit" :disabled="isSubmitting" class="btn btn-primary">
                    <span v-if="isSubmitting" class="loading loading-spinner loading-sm"></span>
                    {{ isSubmitting ? 'Menyimpan...' : 'Simpan Permintaan' }}
                  </button>
                </div>
              </div>
            </form>
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
import { useToast } from 'vue-toastification'

const toast = useToast()

// Data
const isSubmitting = ref(false)
const barangs = ref([])
const ruangs = ref([])

// Form
const form = ref({
  nomor_permintaan: '',
  tanggal_permintaan: new Date().toISOString().split('T')[0],
  pemohon: '',
  ruang_id: '',
  alasan: '',
  items: []
})

// Computed
const totalJumlah = computed(() => {
  return form.value.items.reduce((total, item) => total + (item.jumlah || 0), 0)
})

// Methods
const generateNomorPermintaan = () => {
  const date = new Date()
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0')
  return `PB-${year}${month}${day}-${random}`
}

const getBarangById = (id) => {
  return barangs.value.find(barang => barang.id == id)
}

const addItem = () => {
  form.value.items.push({
    barang_id: '',
    jumlah: 1,
    keterangan: ''
  })
}

const removeItem = (index) => {
  form.value.items.splice(index, 1)
}

const resetForm = () => {
  form.value = {
    nomor_permintaan: generateNomorPermintaan(),
    tanggal_permintaan: new Date().toISOString().split('T')[0],
    pemohon: '',
    ruang_id: '',
    alasan: '',
    items: []
  }
}

const savePermintaan = async () => {
  if (form.value.items.length === 0) {
    toast.warning('Harap tambahkan minimal satu barang', 'Peringatan')
    return
  }

  // Validate form
  if (!form.value.pemohon || !form.value.ruang_id || !form.value.alasan) {
    toast.warning('Harap lengkapi semua field yang diperlukan', 'Peringatan')
    return
  }

  // Validate items
  for (let i = 0; i < form.value.items.length; i++) {
    const item = form.value.items[i]
    if (!item.barang_id || !item.jumlah || item.jumlah < 1) {
      toast.warning(`Harap lengkapi data barang pada baris ${i + 1}`, 'Peringatan')
      return
    }
  }

  isSubmitting.value = true

  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Show success message
    toast.success('Permintaan barang berhasil disimpan!', 'Berhasil')
    
    // Redirect to permintaan list
    router.visit('/permintaan-barang')
  } catch (error) {
    console.error('Error saving permintaan:', error)
    toast.error('Terjadi kesalahan saat menyimpan permintaan', 'Gagal')
  } finally {
    isSubmitting.value = false
  }
}

// Load data
onMounted(() => {
  // Generate nomor permintaan
  form.value.nomor_permintaan = generateNomorPermintaan()

  // Load sample data
  barangs.value = [
    {
      id: 1,
      kode_barang: 'HPK001',
      nama_barang: 'Kertas A4',
      satuan: 'Rim'
    },
    {
      id: 2,
      kode_barang: 'HPK002',
      nama_barang: 'Tinta Printer HP',
      satuan: 'Botol'
    },
    {
      id: 3,
      kode_barang: 'HPK003',
      nama_barang: 'Sabun Cuci',
      satuan: 'Botol'
    },
    {
      id: 4,
      kode_barang: 'HPK004',
      nama_barang: 'Pulpen',
      satuan: 'Pcs'
    },
    {
      id: 5,
      kode_barang: 'HPK005',
      nama_barang: 'Staples',
      satuan: 'Box'
    },
    {
      id: 6,
      kode_barang: 'HPK006',
      nama_barang: 'Kertas HVS',
      satuan: 'Rim'
    },
    {
      id: 7,
      kode_barang: 'HPK007',
      nama_barang: 'Tissue',
      satuan: 'Roll'
    },
    {
      id: 8,
      kode_barang: 'HPK008',
      nama_barang: 'Sapu',
      satuan: 'Pcs'
    }
  ]

  ruangs.value = [
    { id: 1, nama_ruang: 'Ruang Administrasi' },
    { id: 2, nama_ruang: 'Ruang Poli Umum' },
    { id: 3, nama_ruang: 'Ruang Poli Gigi' },
    { id: 4, nama_ruang: 'Laboratorium' },
    { id: 5, nama_ruang: 'Ruang Farmasi' },
    { id: 6, nama_ruang: 'Ruang Gizi' },
    { id: 7, nama_ruang: 'Ruang KIA' },
    { id: 8, nama_ruang: 'Ruang Imunisasi' }
  ]
})
</script> 