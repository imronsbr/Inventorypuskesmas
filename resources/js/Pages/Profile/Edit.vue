<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    pegawai: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    name: '',
    email: '',
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const showPasswordForm = ref(false);

// Methods
const getInitials = (name) => {
    if (!name) return '?'
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

const getStatusLabel = (status) => {
    switch (status) {
        case 'pns': return 'PNS'
        case 'cpns': return 'CPNS'
        case 'non_pns': return 'Non PNS'
        case 'pjlp': return 'PJLP'
        default: return 'Tidak Diketahui'
    }
}

const getJenisKelaminLabel = (jenis) => {
    switch (jenis) {
        case 'l': return 'Laki-laki'
        case 'p': return 'Perempuan'
        default: return 'Tidak Diketahui'
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

const updatePassword = () => {
    passwordForm.patch(route('profile.password'), {
        onSuccess: () => {
            passwordForm.reset()
            showPasswordForm.value = false
        },
    })
}
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profile
            </h2>
        </template>

        <div class="py-6">
            <div class="w-full space-y-6 sm:px-6 lg:px-8">
                <!-- Profile Information -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                Profile Information
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                Update your account's profile information and email address.
                            </p>
                        </header>

                        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Name</span>
                                </label>
                                <input
                                    type="text"
                                    class="input input-bordered w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />
                                <label v-if="form.errors.name" class="label">
                                    <span class="label-text-alt text-error">{{ form.errors.name }}</span>
                                </label>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Email</span>
                                </label>
                                <input
                                    type="email"
                                    class="input input-bordered w-full"
                                    v-model="form.email"
                                    required
                                    autocomplete="username"
                                />
                                <label v-if="form.errors.email" class="label">
                                    <span class="label-text-alt text-error">{{ form.errors.email }}</span>
                                </label>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="tooltip" data-tip="Simpan perubahan profil">
                                    <button :disabled="form.processing" class="btn btn-primary">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>

                <!-- Password Update -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                Update Password
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                Ensure your account is using a long, random password to stay secure.
                            </p>
                        </header>

                        <div class="mt-6 space-y-6">
                            <div v-if="!showPasswordForm">
                                <button @click="showPasswordForm = true" class="btn btn-outline">
                                    Update Password
                                </button>
                            </div>

                            <form v-else @submit.prevent="updatePassword" class="space-y-6">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Current Password</span>
                                    </label>
                                    <input
                                        type="password"
                                        class="input input-bordered w-full"
                                        v-model="passwordForm.current_password"
                                        required
                                        autocomplete="current-password"
                                    />
                                    <label v-if="passwordForm.errors.current_password" class="label">
                                        <span class="label-text-alt text-error">{{ passwordForm.errors.current_password }}</span>
                                    </label>
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">New Password</span>
                                    </label>
                                    <input
                                        type="password"
                                        class="input input-bordered w-full"
                                        v-model="passwordForm.password"
                                        required
                                        autocomplete="new-password"
                                    />
                                    <label v-if="passwordForm.errors.password" class="label">
                                        <span class="label-text-alt text-error">{{ passwordForm.errors.password }}</span>
                                    </label>
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Confirm Password</span>
                                    </label>
                                    <input
                                        type="password"
                                        class="input input-bordered w-full"
                                        v-model="passwordForm.password_confirmation"
                                        required
                                        autocomplete="new-password"
                                    />
                                    <label v-if="passwordForm.errors.password_confirmation" class="label">
                                        <span class="label-text-alt text-error">{{ passwordForm.errors.password_confirmation }}</span>
                                    </label>
                                </div>

                                <div class="flex items-center gap-4">
                                    <button :disabled="passwordForm.processing" class="btn btn-primary">
                                        Update Password
                                    </button>
                                    <button type="button" @click="showPasswordForm = false" class="btn btn-ghost">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>

                <!-- Pegawai Information -->
                <div v-if="pegawai" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                Data Pegawai
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                Informasi lengkap data pegawai Anda.
                            </p>
                        </header>

                        <div class="mt-6">
                            <!-- Profile Card -->
                            <div class="flex items-center space-x-4 mb-6">
                                <div class="avatar placeholder">
                                    <div class="bg-primary text-primary-content rounded-full w-16">
                                        <span class="text-xl">{{ getInitials(pegawai.nama) }}</span>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900">{{ pegawai.nama }}</h3>
                                    <p class="text-sm text-gray-600">{{ pegawai.nip || 'NIP tidak tersedia' }}</p>
                                    <div class="badge badge-primary mt-1">{{ getStatusLabel(pegawai.status_pegawai) }}</div>
                                </div>
                            </div>

                            <!-- Personal Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <h4 class="font-medium text-gray-900">Informasi Pribadi</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Nama Lengkap</label>
                                            <p class="text-sm text-gray-900">{{ pegawai.nama }}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">NIP</label>
                                            <p class="text-sm text-gray-900">{{ pegawai.nip || '-' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">NIK</label>
                                            <p class="text-sm text-gray-900">{{ pegawai.nik || '-' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Tempat Lahir</label>
                                            <p class="text-sm text-gray-900">{{ pegawai.tempat_lahir || '-' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Tanggal Lahir</label>
                                            <p class="text-sm text-gray-900">{{ formatDate(pegawai.tanggal_lahir) }}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Jenis Kelamin</label>
                                            <p class="text-sm text-gray-900">{{ getJenisKelaminLabel(pegawai.jenis_kelamin) }}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Agama</label>
                                            <p class="text-sm text-gray-900">{{ pegawai.agama?.nama || '-' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Pendidikan</label>
                                            <p class="text-sm text-gray-900">{{ pegawai.pendidikan?.nama || '-' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Status Pegawai</label>
                                            <p class="text-sm text-gray-900">{{ getStatusLabel(pegawai.status_pegawai) }}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Jenis Kontrak</label>
                                            <p class="text-sm text-gray-900">{{ pegawai.jenis_kontrak ? (pegawai.jenis_kontrak === 'tetap' ? 'Tetap' : 'Kontrak') : '-' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Unit Kerja</label>
                                            <p class="text-sm text-gray-900">{{ pegawai.unit_kerja || '-' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Alamat</label>
                                            <p class="text-sm text-gray-900">{{ pegawai.alamat || '-' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">No. HP</label>
                                            <p class="text-sm text-gray-900">{{ pegawai.no_hp || '-' }}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Email</label>
                                            <p class="text-sm text-gray-900">{{ pegawai.email || '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <h4 class="font-medium text-gray-900">Jabatan</h4>
                                    <div v-if="pegawai.jabatans && pegawai.jabatans.length > 0" class="space-y-2">
                                        <div v-for="jabatan in pegawai.jabatans" :key="jabatan.id" class="p-3 bg-gray-50 rounded-lg">
                                            <p class="font-medium text-gray-900">{{ jabatan.nama }}</p>
                                            <p class="text-sm text-gray-600">{{ jabatan.deskripsi || 'Tidak ada deskripsi' }}</p>
                                        </div>
                                    </div>
                                    <div v-else class="text-center py-4 text-gray-500">
                                        <p>Belum ada jabatan yang ditugaskan</p>
                                    </div>

                                    <!-- STR & SIP -->
                                    <div class="space-y-4">
                                        <h4 class="font-medium text-gray-900">STR & SIP</h4>
                                        
                                        <div v-if="pegawai.str" class="p-3 bg-blue-50 rounded-lg">
                                            <h5 class="font-medium text-blue-900">STR (Surat Tanda Registrasi)</h5>
                                            <p class="text-sm text-blue-700">{{ pegawai.str.nomor_str }}</p>
                                            <p class="text-xs text-blue-600">{{ formatDate(pegawai.str.tanggal_terbit) }}</p>
                                        </div>
                                        
                                        <div v-if="pegawai.sip" class="p-3 bg-green-50 rounded-lg">
                                            <h5 class="font-medium text-green-900">SIP (Surat Izin Praktik)</h5>
                                            <p class="text-sm text-green-700">{{ pegawai.sip.nomor_sip }}</p>
                                            <p class="text-xs text-green-600">{{ pegawai.sip.tempat_praktik }}</p>
                                        </div>
                                        
                                        <div v-if="!pegawai.str && !pegawai.sip" class="text-center py-4 text-gray-500">
                                            <p>Data STR & SIP belum tersedia</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div v-else class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                Data Pegawai
                            </h2>
                        </header>
                        <div class="mt-6 text-center py-8 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <p>Data pegawai tidak ditemukan</p>
                            <p class="text-sm mt-2">Hubungi administrator untuk mengaitkan data pegawai dengan akun Anda.</p>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
