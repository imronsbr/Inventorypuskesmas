<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Pengadaan Barang
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold">Daftar Pengadaan Barang</h3>
              <Link
                :href="route('pengadaan-barang.create')"
                class="btn btn-primary btn-sm"
              >
                <i class="fas fa-plus mr-2"></i>
                Tambah Pengadaan
              </Link>
            </div>

            <!-- Filters -->
            <div class="card bg-base-100 shadow-sm mb-6">
              <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                  <div class="form-control">
                    <label class="label">
                      <span class="label-text">Status</span>
                    </label>
                    <select v-model="filters.status" class="select select-bordered w-full">
                      <option value="">Semua Status</option>
                      <option value="draft">Draft</option>
                      <option value="submitted">Submitted</option>
                      <option value="approved_penjab">Approved Penjab</option>
                      <option value="approved_pptk">Approved PPTK</option>
                      <option value="approved_perencana">Approved Perencana</option>
                      <option value="approved_kepala_puskesmas">Approved Kepala Puskesmas</option>
                      <option value="approved_kepala_tata_usaha">Approved Kepala Tata Usaha</option>
                      <option value="approved">Approved</option>
                      <option value="rejected">Rejected</option>
                      <option value="completed">Completed</option>
                    </select>
                  </div>

                  <div class="form-control">
                    <label class="label">
                      <span class="label-text">Jenis Barang</span>
                    </label>
                    <select v-model="filters.jenis_barang" class="select select-bordered w-full">
                      <option value="">Semua Jenis</option>
                      <option value="habis_pakai">Habis Pakai</option>
                      <option value="modal">Modal</option>
                      <option value="obat">Obat</option>
                      <option value="alkes">Alkes</option>
                    </select>
                  </div>

                  <div class="form-control">
                    <label class="label">
                      <span class="label-text">Cari</span>
                    </label>
                    <input
                      v-model="filters.search"
                      type="text"
                      placeholder="Nomor pengadaan..."
                      class="input input-bordered w-full"
                    />
                  </div>

                  <div class="form-control">
                    <label class="label">
                      <span class="label-text">Urutkan</span>
                    </label>
                    <select v-model="filters.sortBy" class="select select-bordered w-full">
                      <option value="created_at">Tanggal Dibuat</option>
                      <option value="nomor_pengadaan">Nomor Pengadaan</option>
                      <option value="status">Status</option>
                      <option value="jenis_barang">Jenis Barang</option>
                    </select>
                  </div>
                </div>

                <div class="flex justify-between items-center mt-4">
                  <button @click="clearFilters" class="btn btn-outline btn-sm">
                    Reset Filter
                  </button>
                  <div class="flex items-center gap-2">
                    <button @click="toggleSortDir" class="btn btn-ghost btn-sm">
                      <i :class="filters.sortDir === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
              <table class="table table-zebra w-full">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nomor Pengadaan</th>
                    <th>Jenis Barang</th>
                    <th>Status</th>
                    <th>Dibuat Oleh</th>
                    <th>Tanggal Dibuat</th>
                    <th>Total Item</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(pengadaan, index) in pengadaanBarangs.data" :key="pengadaan.id">
                    <td>{{ (pengadaanBarangs.current_page - 1) * pengadaanBarangs.per_page + index + 1 }}</td>
                    <td>
                      <Link
                        :href="route('pengadaan-barang.show', pengadaan.id)"
                        class="link link-primary font-medium"
                      >
                        {{ pengadaan.nomor_pengadaan }}
                      </Link>
                    </td>
                    <td>
                      <span class="badge badge-outline">
                        {{ getJenisBarangLabel(pengadaan.jenis_barang) }}
                      </span>
                    </td>
                    <td>
                      <span :class="`badge ${getStatusBadge(pengadaan.status)}`">
                        {{ getStatusLabel(pengadaan.status) }}
                      </span>
                    </td>
                    <td>{{ pengadaan.created_by?.name || '-' }}</td>
                    <td>{{ formatDate(pengadaan.created_at) }}</td>
                    <td>{{ pengadaan.detail_pengadaan_barangs?.length || 0 }} item</td>
                    <td>
                      <div class="flex gap-2">
                        <Link
                          :href="route('pengadaan-barang.show', pengadaan.id)"
                          class="btn btn-ghost btn-xs"
                          title="Detail"
                        >
                          <i class="fas fa-eye"></i>
                        </Link>
                        
                        <button
                          v-if="canApprove(pengadaan)"
                          @click="approvePengadaan(pengadaan.id)"
                          class="btn btn-success btn-xs"
                          title="Setujui"
                        >
                          <i class="fas fa-check"></i>
                        </button>
                        
                        <button
                          v-if="canReject(pengadaan)"
                          @click="showRejectModal(pengadaan)"
                          class="btn btn-error btn-xs"
                          title="Tolak"
                        >
                          <i class="fas fa-times"></i>
                        </button>
                        
                        <button
                          v-if="canDelete(pengadaan)"
                          @click="deletePengadaan(pengadaan.id)"
                          class="btn btn-error btn-xs"
                          title="Hapus"
                        >
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-center mt-6">
              <Pagination :links="pengadaanBarangs.links" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reject Modal -->
    <div v-if="showReject" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">Tolak Pengadaan Barang</h3>
        <div class="form-control">
          <label class="label">
            <span class="label-text">Alasan Penolakan</span>
          </label>
          <textarea
            v-model="rejectReason"
            class="textarea textarea-bordered"
            rows="3"
            placeholder="Masukkan alasan penolakan..."
          ></textarea>
        </div>
        <div class="modal-action">
          <button @click="closeRejectModal" class="btn btn-ghost">Batal</button>
          <button @click="confirmReject" class="btn btn-error">Tolak</button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { useToast } from '@/composables/useToast'

const props = defineProps({
  pengadaanBarangs: Object,
  filters: Object,
})

const { showToast } = useToast()

const filters = ref({
  status: props.filters.status || '',
  jenis_barang: props.filters.jenis_barang || '',
  search: props.filters.search || '',
  sortBy: props.filters.sortBy || 'created_at',
  sortDir: props.filters.sortDir || 'desc',
})

const showReject = ref(false)
const rejectReason = ref('')
const selectedPengadaan = ref(null)

// Watch filters and update URL
watch(filters, (newFilters) => {
  router.get(route('pengadaan-barang.index'), newFilters, {
    preserveState: true,
    preserveScroll: true,
  })
}, { deep: true })

const clearFilters = () => {
  filters.value = {
    status: '',
    jenis_barang: '',
    search: '',
    sortBy: 'created_at',
    sortDir: 'desc',
  }
}

const toggleSortDir = () => {
  filters.value.sortDir = filters.value.sortDir === 'asc' ? 'desc' : 'asc'
}

const getJenisBarangLabel = (jenis) => {
  const labels = {
    habis_pakai: 'Habis Pakai',
    modal: 'Modal',
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
    month: 'short',
    day: 'numeric',
  })
}

const canApprove = (pengadaan) => {
  // Check if current user can approve based on status
  const currentUser = window.auth?.user
  if (!currentUser) return false
  
  // This would need to be implemented based on user roles and current approval status
  return ['submitted', 'approved_penjab', 'approved_pptk', 'approved_perencana', 'approved_kepala_puskesmas'].includes(pengadaan.status)
}

const canReject = (pengadaan) => {
  return ['submitted', 'approved_penjab', 'approved_pptk', 'approved_perencana', 'approved_kepala_puskesmas'].includes(pengadaan.status)
}

const canDelete = (pengadaan) => {
  return pengadaan.status === 'draft'
}

const approvePengadaan = (id) => {
  router.patch(route('pengadaan-barang.approve', id), {}, {
    onSuccess: () => {
      showToast('Pengadaan barang berhasil disetujui!', 'success')
    },
    onError: (errors) => {
      showToast('Gagal menyetujui pengadaan barang!', 'error')
    }
  })
}

const showRejectModal = (pengadaan) => {
  selectedPengadaan.value = pengadaan
  showReject.value = true
}

const closeRejectModal = () => {
  showReject.value = false
  rejectReason.value = ''
  selectedPengadaan.value = null
}

const confirmReject = () => {
  if (!rejectReason.value.trim()) {
    showToast('Alasan penolakan harus diisi!', 'error')
    return
  }

  router.patch(route('pengadaan-barang.reject', selectedPengadaan.value.id), {
    alasan_reject: rejectReason.value
  }, {
    onSuccess: () => {
      showToast('Pengadaan barang berhasil ditolak!', 'success')
      closeRejectModal()
    },
    onError: (errors) => {
      showToast('Gagal menolak pengadaan barang!', 'error')
    }
  })
}

const deletePengadaan = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus pengadaan barang ini?')) {
    router.delete(route('pengadaan-barang.destroy', id), {
      onSuccess: () => {
        showToast('Pengadaan barang berhasil dihapus!', 'success')
      },
      onError: (errors) => {
        showToast('Gagal menghapus pengadaan barang!', 'error')
      }
    })
  }
}
</script> 