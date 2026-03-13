<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Daftar Pegawai
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Cari</span>
                                </label>
                                <input
                                    type="text"
                                    class="input input-bordered"
                                    v-model="filters.search"
                                    placeholder="Nama atau NIP..."
                                    @input="debounceSearch"
                                />
                            </div>

                            <!-- Jabatan Filter -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Jabatan</span>
                                </label>
                                <select class="select select-bordered" v-model="filters.jabatan_id">
                                    <option value="">Semua Jabatan</option>
                                    <option v-for="jabatan in jabatans" :key="jabatan.id" :value="jabatan.id">
                                        {{ jabatan.nama }}
                                    </option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Status</span>
                                </label>
                                <select class="select select-bordered" v-model="filters.status_pegawai">
                                    <option value="">Semua Status</option>
                                    <option v-for="option in config.statusPegawaiOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                            </div>

                            <!-- Per Page -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Per Halaman</span>
                                </label>
                                <select class="select select-bordered" v-model="filters.perPage">
                                    <option v-for="option in config.perPageOptions" :key="option" :value="option">
                                        {{ option }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mt-4">
                            <div class="flex gap-2">
                                <button @click="applyFilters" class="btn btn-primary btn-sm">
                                    Terapkan Filter
                                </button>
                                <button @click="clearFilters" class="btn btn-ghost btn-sm">
                                    Reset
                                </button>
                            </div>

                            <div class="tooltip" data-tip="Tambah pegawai baru">
                                <Link :href="route('pegawai.create')" class="btn btn-success btn-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Pegawai
                                </Link>
                            </div>
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
                                        <th class="cursor-pointer" @click="sort('nama')">
                                            <div class="flex items-center gap-1">
                                                Nama
                                                <svg v-if="filters.sortBy === 'nama'" class="w-4 h-4" :class="filters.sortDir === 'asc' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                </svg>
                                            </div>
                                        </th>
                                        <th class="cursor-pointer" @click="sort('nip')">
                                            <div class="flex items-center gap-1">
                                                NIP
                                                <svg v-if="filters.sortBy === 'nip'" class="w-4 h-4" :class="filters.sortDir === 'asc' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                </svg>
                                            </div>
                                        </th>
                                        <th>Jabatan</th>
                                        <th>Status</th>
                                        <th>Unit Kerja</th>
                                        <th>No. HP</th>
                                        <th>Email</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="pegawai in pegawais.data" :key="pegawai.id">
                                        <!-- Nama - Inline Editable -->
                                        <td>
                                            <div v-if="editingId !== pegawai.id">
                                                <span class="cursor-pointer hover:text-blue-600" @click="startEdit(pegawai.id, 'nama', pegawai.nama)">
                                                    {{ pegawai.nama }}
                                                </span>
                                            </div>
                                            <div v-else class="flex items-center gap-1">
                                                <input
                                                    type="text"
                                                    class="input input-bordered input-sm w-full"
                                                    v-model="editingData.nama"
                                                    @keyup.enter="saveEdit"
                                                    @keyup.esc="cancelEdit"
                                                    ref="editInput"
                                                />
                                            </div>
                                        </td>

                                        <!-- NIP - Inline Editable -->
                                        <td>
                                            <div v-if="editingId !== pegawai.id">
                                                <span class="cursor-pointer hover:text-blue-600" @click="startEdit(pegawai.id, 'nip', pegawai.nip)">
                                                    {{ pegawai.nip || '-' }}
                                                </span>
                                            </div>
                                            <div v-else>
                                                <input
                                                    type="text"
                                                    class="input input-bordered input-sm w-full"
                                                    v-model="editingData.nip"
                                                    @keyup.enter="saveEdit"
                                                    @keyup.esc="cancelEdit"
                                                />
                                            </div>
                                        </td>

                                        <!-- Jabatan -->
                                        <td>
                                            <div v-if="editingId !== pegawai.id">
                                                <span class="cursor-pointer hover:text-blue-600" @click="startEdit(pegawai.id, 'jabatan', pegawai.jabatans)">
                                                    {{ getJabatanNames(pegawai.jabatans) }}
                                                </span>
                                            </div>
                                            <div v-else>
                                                <select class="select select-bordered select-sm w-full" v-model="editingData.jabatan_ids" multiple>
                                                    <option v-for="jabatan in jabatans" :key="jabatan.id" :value="jabatan.id">
                                                        {{ jabatan.nama }}
                                                    </option>
                                                </select>
                                            </div>
                                        </td>

                                        <!-- Status - Inline Editable -->
                                        <td>
                                            <div v-if="editingId !== pegawai.id">
                                                <span class="cursor-pointer hover:text-blue-600" @click="startEdit(pegawai.id, 'status_pegawai', pegawai.status_pegawai)">
                                                    <span class="badge" :class="getStatusBadgeClass(pegawai.status_pegawai)">
                                                        {{ getStatusLabel(pegawai.status_pegawai) }}
                                                    </span>
                                                </span>
                                            </div>
                                            <div v-else>
                                                <select class="select select-bordered select-sm w-full" v-model="editingData.status_pegawai">
                                                    <option v-for="option in config.statusPegawaiOptions" :key="option.value" :value="option.value">
                                                        {{ option.label }}
                                                    </option>
                                                </select>
                                            </div>
                                        </td>

                                        <!-- Unit Kerja - Inline Editable -->
                                        <td>
                                            <div v-if="editingId !== pegawai.id">
                                                <span class="cursor-pointer hover:text-blue-600" @click="startEdit(pegawai.id, 'unit_kerja', pegawai.unit_kerja)">
                                                    {{ pegawai.unit_kerja || '-' }}
                                                </span>
                                            </div>
                                            <div v-else>
                                                <input
                                                    type="text"
                                                    class="input input-bordered input-sm w-full"
                                                    v-model="editingData.unit_kerja"
                                                    @keyup.enter="saveEdit"
                                                    @keyup.esc="cancelEdit"
                                                />
                                            </div>
                                        </td>

                                        <!-- No HP - Inline Editable -->
                                        <td>
                                            <div v-if="editingId !== pegawai.id">
                                                <span class="cursor-pointer hover:text-blue-600" @click="startEdit(pegawai.id, 'no_hp', pegawai.no_hp)">
                                                    {{ pegawai.no_hp || '-' }}
                                                </span>
                                            </div>
                                            <div v-else>
                                                <input
                                                    type="text"
                                                    class="input input-bordered input-sm w-full"
                                                    v-model="editingData.no_hp"
                                                    @keyup.enter="saveEdit"
                                                    @keyup.esc="cancelEdit"
                                                />
                                            </div>
                                        </td>

                                        <!-- Email - Inline Editable -->
                                        <td>
                                            <div v-if="editingId !== pegawai.id">
                                                <span class="cursor-pointer hover:text-blue-600" @click="startEdit(pegawai.id, 'email', pegawai.email)">
                                                    {{ pegawai.email || '-' }}
                                                </span>
                                            </div>
                                            <div v-else>
                                                <input
                                                    type="email"
                                                    class="input input-bordered input-sm w-full"
                                                    v-model="editingData.email"
                                                    @keyup.enter="saveEdit"
                                                    @keyup.esc="cancelEdit"
                                                />
                                            </div>
                                        </td>

                                        <!-- Actions -->
                                        <td>
                                            <div v-if="editingId !== pegawai.id" class="flex gap-1">
                                                <div class="tooltip" data-tip="Lihat detail">
                                                    <Link :href="route('pegawai.show', pegawai.id)" class="btn btn-sm btn-info">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </Link>
                                                </div>
                                                <div class="tooltip" data-tip="Edit lengkap">
                                                    <Link :href="route('pegawai.edit', pegawai.id)" class="btn btn-sm btn-warning">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </Link>
                                                </div>
                                                <div class="tooltip" data-tip="Hapus">
                                                    <button @click="confirmDelete(pegawai)" class="btn btn-sm btn-error">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div v-else class="flex gap-1">
                                                <button @click="saveEdit" class="btn btn-sm btn-success">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>
                                                <button @click="cancelEdit" class="btn btn-sm btn-ghost">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            <Pagination :links="pegawais.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, onMounted, nextTick } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

const props = defineProps({
    pegawais: Object,
    jabatans: Array,
    agamas: Array,
    pendidikans: Array,
    filters: Object,
    config: Object,
})

// Reactive data
const editingId = ref(null)
const editingData = reactive({
    nama: '',
    nip: '',
    status_pegawai: '',
    unit_kerja: '',
    no_hp: '',
    email: '',
    jabatan_ids: [],
})

const filters = reactive({
    search: props.filters.search || '',
    jabatan_id: props.filters.jabatan_id || '',
    status_pegawai: props.filters.status_pegawai || '',
    sortBy: props.filters.sortBy || 'nama',
    sortDir: props.filters.sortDir || 'asc',
    perPage: props.filters.perPage || 10,
})

// Methods
const debounceSearch = () => {
    clearTimeout(window.searchTimeout)
    window.searchTimeout = setTimeout(() => {
        applyFilters()
    }, 500)
}

const applyFilters = () => {
    router.get(route('pegawai.index'), filters, {
        preserveState: true,
        preserveScroll: true,
    })
}

const clearFilters = () => {
    Object.assign(filters, {
        search: '',
        jabatan_id: '',
        status_pegawai: '',
        sortBy: 'nama',
        sortDir: 'asc',
        perPage: 10,
    })
    applyFilters()
}

const sort = (column) => {
    if (filters.sortBy === column) {
        filters.sortDir = filters.sortDir === 'asc' ? 'desc' : 'asc'
    } else {
        filters.sortBy = column
        filters.sortDir = 'asc'
    }
    applyFilters()
}

const startEdit = (id, field, value) => {
    editingId.value = id
    editingData.nama = ''
    editingData.nip = ''
    editingData.status_pegawai = ''
    editingData.unit_kerja = ''
    editingData.no_hp = ''
    editingData.email = ''
    editingData.jabatan_ids = []

    // Find the pegawai data
    const pegawai = props.pegawais.data.find(p => p.id === id)
    if (pegawai) {
        editingData.nama = pegawai.nama
        editingData.nip = pegawai.nip || ''
        editingData.status_pegawai = pegawai.status_pegawai
        editingData.unit_kerja = pegawai.unit_kerja || ''
        editingData.no_hp = pegawai.no_hp || ''
        editingData.email = pegawai.email || ''
        editingData.jabatan_ids = pegawai.jabatans.map(j => j.id)
    }

    nextTick(() => {
        const input = document.querySelector('input, select')
        if (input) input.focus()
    })
}

const saveEdit = async () => {
    try {
        const response = await fetch(route('pegawai.update-inline', editingId.value), {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify(editingData)
        })
        
        const result = await response.json()
        
        if (response.ok && result.success) {
            toast.success(result.message || 'Data pegawai berhasil diperbarui!')
            editingId.value = null
            // Refresh the page to get updated data
            router.reload()
        } else {
            toast.error(result.message || 'Gagal memperbarui data pegawai!')
        }
    } catch (error) {
        toast.error('Gagal memperbarui data pegawai!')
        console.error('Error updating pegawai:', error)
    }
}

const cancelEdit = () => {
    editingId.value = null
}

const confirmDelete = (pegawai) => {
    if (confirm(`Apakah Anda yakin ingin menghapus pegawai "${pegawai.nama}"?`)) {
        router.delete(route('pegawai.destroy', pegawai.id), {
            onSuccess: () => {
                toast.success('Data pegawai berhasil dihapus!')
            },
            onError: () => {
                toast.error('Gagal menghapus data pegawai!')
            }
        })
    }
}

const getStatusLabel = (status) => {
    const statusMap = {
        'pns': 'PNS',
        'cpns': 'CPNS',
        'non_pns': 'Non PNS',
        'pjlp': 'PJLP'
    }
    return statusMap[status] || 'Tidak Diketahui'
}

const getStatusBadgeClass = (status) => {
    const badgeMap = {
        'pns': 'badge-success',
        'cpns': 'badge-warning',
        'non_pns': 'badge-info',
        'pjlp': 'badge-secondary'
    }
    return badgeMap[status] || 'badge-ghost'
}

const getJabatanNames = (jabatans) => {
    if (!jabatans || jabatans.length === 0) return '-'
    return jabatans.map(j => j.nama).join(', ')
}
</script> 