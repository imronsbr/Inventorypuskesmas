<template>
  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Detail Pengadaan Barang
        </h2>
        <Link
          :href="route('pengadaan-barang.index')"
          class="btn btn-outline btn-sm"
        >
          <i class="fas fa-arrow-left mr-2"></i>
          Kembali
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Info -->
        <div class="card bg-base-100 shadow-sm mb-6">
          <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h3 class="text-lg font-semibold mb-4">Informasi Pengadaan</h3>
                <div class="space-y-3">
                  <div class="flex justify-between">
                    <span class="font-medium">Nomor Pengadaan:</span>
                    <span class="font-bold text-primary">{{ pengadaanBarang.nomor_pengadaan }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="font-medium">Jenis Barang:</span>
                    <span class="badge badge-outline">{{ getJenisBarangLabel(pengadaanBarang.jenis_barang) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="font-medium">Status:</span>
                    <span :class="`badge ${getStatusBadge(pengadaanBarang.status)}`">
                      {{ getStatusLabel(pengadaanBarang.status) }}
                    </span>
                  </div>
                  <div class="flex justify-between">
                    <span class="font-medium">Dibuat Oleh:</span>
                    <span>{{ pengadaanBarang.created_by?.name || '-' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="font-medium">Tanggal Dibuat:</span>
                    <span>{{ formatDate(pengadaanBarang.created_at) }}</span>
                  </div>
                </div>
              </div>

              <div>
                <h3 class="text-lg font-semibold mb-4">Keterangan</h3>
                <p class="text-gray-600">{{ pengadaanBarang.keterangan || 'Tidak ada keterangan' }}</p>
                
                <div class="mt-4">
                  <h4 class="font-semibold mb-2">Total Anggaran:</h4>
                  <div class="text-2xl font-bold text-primary">
                    {{ formatCurrency(getTotalAnggaran()) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Approval Actions -->
        <div v-if="canApprove || canReject" class="card bg-base-100 shadow-sm mb-6">
          <div class="card-body">
            <h3 class="text-lg font-semibold mb-4">Aksi Approval</h3>
            <div class="flex gap-4">
              <button
                v-if="canApprove"
                @click="approvePengadaan"
                class="btn btn-success"
                :disabled="isProcessing"
              >
                <i class="fas fa-check mr-2"></i>
                Setujui Pengadaan
              </button>
              
              <button
                v-if="canReject"
                @click="showRejectModal = true"
                class="btn btn-error"
                :disabled="isProcessing"
              >
                <i class="fas fa-times mr-2"></i>
                Tolak Pengadaan
              </button>
            </div>
          </div>
        </div>

        <!-- Items List -->
        <div class="card bg-base-100 shadow-sm">
          <div class="card-body">
            <h3 class="text-lg font-semibold mb-4">Detail Item Barang</h3>
            
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Total Harga</th>
                    <th>Spesifikasi</th>
                    <th>Keterangan</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in pengadaanBarang.detail_pengadaan_barangs" :key="item.id">
                    <td>{{ index + 1 }}</td>
                    <td>
                      <div class="font-medium">{{ item.barang?.nama || 'Barang tidak ditemukan' }}</div>
                      <div class="text-sm text-gray-500">{{ item.barang_type }}</div>
                    </td>
                    <td>{{ item.jumlah }}</td>
                    <td>{{ formatCurrency(item.harga_satuan) }}</td>
                    <td class="font-bold">{{ formatCurrency(item.jumlah * item.harga_satuan) }}</td>
                    <td>{{ item.spesifikasi || '-' }}</td>
                    <td>{{ item.keterangan || '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Reject Modal -->
        <div v-if="showRejectModal" class="modal modal-open">
          <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Tolak Pengadaan Barang</h3>
            <div class="form-control">
              <label class="label">
                <span class="label-text">Alasan Penolakan *</span>
              </label>
              <textarea
                v-model="rejectReason"
                class="textarea textarea-bordered"
                rows="3"
                placeholder="Masukkan alasan penolakan..."
                required
              ></textarea>
            </div>
            <div class="modal-action">
              <button @click="closeRejectModal" class="btn btn-ghost">Batal</button>
              <button @click="confirmReject" class="btn btn-error" :disabled="!rejectReason.trim()">
                Tolak
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useToast } from '@/composables/useToast'

const props = defineProps({
  pengadaanBarang: Object,
})

const { showToast } = useToast()

const showRejectModal = ref(false)
const rejectReason = ref('')
const isProcessing = ref(false)

const getJenisBarangLabel = (jenis) => {
  const labels = {
    habis_pakai: 'Barang Habis Pakai',
    modal: 'Barang Modal/Asset',
    obat: 'Obat',
    alkes: 'Alkes',
  }
  return labels[jenis] || jenis
}

const getStatusLabel = (status) => {
  const labels = {
    draft: 'Draft',
    submitted: 'Submitted',
    approved_penjab: 'Approved Penjab',
    approved_pptk: 'Approved PPTK',
    approved_perencana: 'Approved Perencana',
    approved_kepala_puskesmas: 'Approved Kepala Puskesmas',
    approved_kepala_tata_usaha: 'Approved Kepala Tata Usaha',
    approved: 'Approved',
    rejected: 'Rejected',
    completed: 'Completed',
  }
  return labels[status] || status
}

const getStatusBadge = (status) => {
  const badges = {
    draft: 'badge-secondary',
    submitted: 'badge-info',
    approved_penjab: 'badge-warning',
    approved_pptk: 'badge-warning',
    approved_perencana: 'badge-warning',
    approved_kepala_puskesmas: 'badge-warning',
    approved_kepala_tata_usaha: 'badge-success',
    approved: 'badge-success',
    rejected: 'badge-error',
    completed: 'badge-success',
  }
  return badges[status] || 'badge-secondary'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
  }).format(amount)
}

const getTotalAnggaran = () => {
  return props.pengadaanBarang.detail_pengadaan_barangs?.reduce((total, item) => {
    return total + (item.jumlah * item.harga_satuan)
  }, 0) || 0
}

const canApprove = computed(() => {
  const currentUser = window.auth?.user
  if (!currentUser) return false
  
  // Check if current user can approve based on status
  return ['submitted', 'approved_penjab', 'approved_pptk', 'approved_perencana', 'approved_kepala_puskesmas'].includes(props.pengadaanBarang.status)
})

const canReject = computed(() => {
  return ['submitted', 'approved_penjab', 'approved_pptk', 'approved_perencana', 'approved_kepala_puskesmas'].includes(props.pengadaanBarang.status)
})

const approvePengadaan = () => {
  isProcessing.value = true
  
  router.patch(route('pengadaan-barang.approve', props.pengadaanBarang.id), {}, {
    onSuccess: () => {
      showToast('Pengadaan barang berhasil disetujui!', 'success')
    },
    onError: (errors) => {
      showToast('Gagal menyetujui pengadaan barang!', 'error')
    },
    onFinish: () => {
      isProcessing.value = false
    }
  })
}

const closeRejectModal = () => {
  showRejectModal.value = false
  rejectReason.value = ''
}

const confirmReject = () => {
  if (!rejectReason.value.trim()) {
    showToast('Alasan penolakan harus diisi!', 'error')
    return
  }

  isProcessing.value = true

  router.patch(route('pengadaan-barang.reject', props.pengadaanBarang.id), {
    alasan_reject: rejectReason.value
  }, {
    onSuccess: () => {
      showToast('Pengadaan barang berhasil ditolak!', 'success')
      closeRejectModal()
    },
    onError: (errors) => {
      showToast('Gagal menolak pengadaan barang!', 'error')
    },
    onFinish: () => {
      isProcessing.value = false
    }
  })
}
</script> 