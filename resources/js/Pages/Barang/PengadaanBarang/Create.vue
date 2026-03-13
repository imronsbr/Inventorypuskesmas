<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Buat Pengadaan Barang
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <form @submit.prevent="submitForm">
              <!-- Jenis Barang -->
              <div class="form-control mb-6">
                <label class="label">
                  <span class="label-text font-semibold">Jenis Barang *</span>
                </label>
                <select v-model="form.jenis_barang" class="select select-bordered w-full" required>
                  <option value="">Pilih Jenis Barang</option>
                  <option value="habis_pakai">Barang Habis Pakai</option>
                  <option value="modal">Barang Modal/Asset</option>
                  <option value="obat">Obat</option>
                  <option value="alkes">Alkes</option>
                </select>
                <div v-if="errors.jenis_barang" class="text-error text-sm mt-1">
                  {{ errors.jenis_barang }}
                </div>
              </div>

              <!-- Keterangan -->
              <div class="form-control mb-6">
                <label class="label">
                  <span class="label-text font-semibold">Keterangan</span>
                </label>
                <textarea
                  v-model="form.keterangan"
                  class="textarea textarea-bordered"
                  rows="3"
                  placeholder="Masukkan keterangan pengadaan..."
                ></textarea>
                <div v-if="errors.keterangan" class="text-error text-sm mt-1">
                  {{ errors.keterangan }}
                </div>
              </div>

              <!-- Items Section -->
              <div class="card bg-base-100 shadow-sm mb-6">
                <div class="card-body">
                  <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Item Barang</h3>
                    <button
                      type="button"
                      @click="addItem"
                      class="btn btn-primary btn-sm"
                    >
                      <i class="fas fa-plus mr-2"></i>
                      Tambah Item
                    </button>
                  </div>

                  <div v-if="form.items.length === 0" class="text-center py-8 text-gray-500">
                    <i class="fas fa-box-open text-4xl mb-4"></i>
                    <p>Belum ada item yang ditambahkan</p>
                  </div>

                  <div v-else class="space-y-4">
                    <div
                      v-for="(item, index) in form.items"
                      :key="index"
                      class="card bg-base-200 shadow-sm"
                    >
                      <div class="card-body">
                        <div class="flex justify-between items-start mb-4">
                          <h4 class="font-semibold">Item #{{ index + 1 }}</h4>
                          <button
                            type="button"
                            @click="removeItem(index)"
                            class="btn btn-error btn-xs"
                          >
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                          <!-- Barang Selection -->
                          <div class="form-control">
                            <label class="label">
                              <span class="label-text">Barang *</span>
                            </label>
                            <select
                              v-model="item.barang_id"
                              class="select select-bordered w-full"
                              required
                            >
                              <option value="">Pilih Barang</option>
                              <option
                                v-for="barang in getBarangOptions()"
                                :key="barang.id"
                                :value="barang.id"
                              >
                                {{ barang.nama }}
                              </option>
                            </select>
                          </div>

                          <!-- Jumlah -->
                          <div class="form-control">
                            <label class="label">
                              <span class="label-text">Jumlah *</span>
                            </label>
                            <input
                              v-model.number="item.jumlah"
                              type="number"
                              min="1"
                              class="input input-bordered w-full"
                              required
                            />
                          </div>

                          <!-- Harga Satuan -->
                          <div class="form-control">
                            <label class="label">
                              <span class="label-text">Harga Satuan *</span>
                            </label>
                            <input
                              v-model.number="item.harga_satuan"
                              type="number"
                              min="0"
                              step="0.01"
                              class="input input-bordered w-full"
                              required
                            />
                          </div>

                          <!-- Spesifikasi -->
                          <div class="form-control">
                            <label class="label">
                              <span class="label-text">Spesifikasi</span>
                            </label>
                            <input
                              v-model="item.spesifikasi"
                              type="text"
                              class="input input-bordered w-full"
                              placeholder="Spesifikasi barang..."
                            />
                          </div>

                          <!-- Keterangan Item -->
                          <div class="form-control">
                            <label class="label">
                              <span class="label-text">Keterangan</span>
                            </label>
                            <input
                              v-model="item.keterangan"
                              type="text"
                              class="input input-bordered w-full"
                              placeholder="Keterangan item..."
                            />
                          </div>

                          <!-- Total Harga -->
                          <div class="form-control">
                            <label class="label">
                              <span class="label-text">Total Harga</span>
                            </label>
                            <input
                              :value="formatCurrency(getItemTotal(item))"
                              type="text"
                              class="input input-bordered w-full bg-base-300"
                              readonly
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Total Summary -->
                  <div v-if="form.items.length > 0" class="card bg-primary text-primary-content mt-4">
                    <div class="card-body">
                      <div class="flex justify-between items-center">
                        <span class="font-semibold">Total Anggaran:</span>
                        <span class="text-xl font-bold">{{ formatCurrency(getTotalAnggaran()) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex justify-end gap-4">
                <Link
                  :href="route('pengadaan-barang.index')"
                  class="btn btn-outline"
                >
                  Batal
                </Link>
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="isSubmitting"
                >
                  <span v-if="isSubmitting" class="loading loading-spinner loading-sm"></span>
                  {{ isSubmitting ? 'Menyimpan...' : 'Simpan Pengadaan' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useToast } from '@/composables/useToast'

const props = defineProps({
  errors: Object,
  barangHabisPakai: Array,
  barangModal: Array,
  obat: Array,
  alkes: Array,
})

const { showToast } = useToast()

const form = useForm({
  jenis_barang: '',
  keterangan: '',
  items: [],
})

const isSubmitting = ref(false)

const getBarangOptions = () => {
  switch (form.jenis_barang) {
    case 'habis_pakai':
      return props.barangHabisPakai
    case 'modal':
      return props.barangModal
    case 'obat':
      return props.obat
    case 'alkes':
      return props.alkes
    default:
      return []
  }
}

const addItem = () => {
  form.items.push({
    barang_type: form.jenis_barang,
    barang_id: '',
    jumlah: 1,
    harga_satuan: 0,
    spesifikasi: '',
    keterangan: '',
  })
}

const removeItem = (index) => {
  form.items.splice(index, 1)
}

const getItemTotal = (item) => {
  return (item.jumlah || 0) * (item.harga_satuan || 0)
}

const getTotalAnggaran = () => {
  return form.items.reduce((total, item) => total + getItemTotal(item), 0)
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
  }).format(amount)
}

const submitForm = () => {
  if (form.items.length === 0) {
    showToast('Minimal harus ada 1 item barang!', 'error')
    return
  }

  isSubmitting.value = true

  // Set barang_type for each item
  form.items.forEach(item => {
    item.barang_type = form.jenis_barang
  })

  router.post(route('pengadaan-barang.store'), form.data(), {
    onSuccess: () => {
      showToast('Pengadaan barang berhasil dibuat!', 'success')
    },
    onError: (errors) => {
      showToast('Gagal membuat pengadaan barang!', 'error')
    },
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}

// Watch jenis_barang changes to reset items
watch(() => form.jenis_barang, () => {
  form.items = []
})
</script> 