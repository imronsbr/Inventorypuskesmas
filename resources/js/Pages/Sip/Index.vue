<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Daftar SIP Saya
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Daftar Surat Izin Praktik (SIP)</h3>
                                <p class="text-sm text-gray-600 mt-1">Kelola data SIP Anda</p>
                            </div>
                            <div class="tooltip" data-tip="Tambah SIP baru">
                                <Link :href="route('sip.create')" class="btn btn-primary">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah SIP
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Search -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Cari Nomor SIP</span>
                                </label>
                                <input
                                    type="text"
                                    class="input input-bordered"
                                    v-model="filters.search"
                                    placeholder="Nomor SIP..."
                                    @input="debounceSearch"
                                />
                            </div>

                            <!-- Status Filter -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Status</span>
                                </label>
                                <select class="select select-bordered" v-model="filters.status">
                                    <option value="">Semua Status</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="tidak_aktif">Tidak Aktif</option>
                                </select>
                            </div>

                            <!-- Sort -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Urutkan</span>
                                </label>
                                <select class="select select-bordered" v-model="filters.sortBy">
                                    <option value="tanggal_berakhir">Tanggal Berakhir</option>
                                    <option value="tanggal_terbit">Tanggal Terbit</option>
                                    <option value="nomor_sip">Nomor SIP</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4">
                            <button @click="applyFilters" class="btn btn-primary btn-sm">
                                Terapkan Filter
                            </button>
                            <button @click="clearFilters" class="btn btn-ghost btn-sm">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th>Nomor SIP</th>
                                        <th>Tempat Praktik</th>
                                        <th>Tanggal Terbit</th>
                                        <th>Tanggal Berakhir</th>
                                        <th>Status</th>
                                        <th>File</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="sip in sips.data" :key="sip.id">
                                        <td>
                                            <div class="font-medium">{{ sip.nomor_sip }}</div>
                                        </td>
                                        <td>{{ sip.tempat_praktik }}</td>
                                        <td>{{ formatDate(sip.tanggal_terbit) }}</td>
                                        <td>{{ formatDate(sip.tanggal_berakhir) }}</td>
                                        <td>
                                            <span class="badge" :class="getStatusBadgeClass(sip.tanggal_berakhir)">
                                                {{ getStatusLabel(sip.tanggal_berakhir) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div v-if="sip.file_path" class="flex gap-2">
                                                <div class="tooltip" data-tip="Download file">
                                                    <Link :href="route('sip.download', sip.id)" class="btn btn-sm btn-info">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </Link>
                                                </div>
                                            </div>
                                            <span v-else class="text-gray-500 text-sm">Tidak ada file</span>
                                        </td>
                                        <td>
                                            <div class="flex gap-1">
                                                <div class="tooltip" data-tip="Lihat detail">
                                                    <Link :href="route('sip.show', sip.id)" class="btn btn-sm btn-info">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </Link>
                                                </div>
                                                <div class="tooltip" data-tip="Edit SIP">
                                                    <Link :href="route('sip.edit', sip.id)" class="btn btn-sm btn-warning">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </Link>
                                                </div>
                                                <div class="tooltip" data-tip="Hapus SIP">
                                                    <button @click="confirmDelete(sip)" class="btn btn-sm btn-error">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            <Pagination :links="sips.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

const props = defineProps({
    sips: Object,
    pegawai: Object,
    filters: Object,
})

const filters = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
    sortBy: props.filters.sortBy || 'tanggal_berakhir',
    sortDir: props.filters.sortDir || 'desc',
})

const debounceSearch = () => {
    clearTimeout(window.searchTimeout)
    window.searchTimeout = setTimeout(() => {
        applyFilters()
    }, 500)
}

const applyFilters = () => {
    router.get(route('sip.index'), filters, {
        preserveState: true,
        preserveScroll: true,
    })
}

const clearFilters = () => {
    Object.assign(filters, {
        search: '',
        status: '',
        sortBy: 'tanggal_berakhir',
        sortDir: 'desc',
    })
    applyFilters()
}

const confirmDelete = (sip) => {
    if (confirm(`Apakah Anda yakin ingin menghapus SIP "${sip.nomor_sip}"?`)) {
        router.delete(route('sip.destroy', sip.id), {
            onSuccess: () => {
                toast.success('Data SIP berhasil dihapus!')
            },
            onError: () => {
                toast.error('Gagal menghapus data SIP!')
            }
        })
    }
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const getStatusLabel = (tanggalBerakhir) => {
    const today = new Date()
    const berakhir = new Date(tanggalBerakhir)
    return berakhir >= today ? 'Aktif' : 'Tidak Aktif'
}

const getStatusBadgeClass = (tanggalBerakhir) => {
    const today = new Date()
    const berakhir = new Date(tanggalBerakhir)
    return berakhir >= today ? 'badge-success' : 'badge-error'
}
</script> 