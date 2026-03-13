<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah SIP Baru
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900">Form Tambah SIP</h3>
                            <p class="text-sm text-gray-600 mt-1">Lengkapi data SIP Anda</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Nomor SIP -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Nomor SIP *</span>
                                </label>
                                <input
                                    type="text"
                                    class="input input-bordered w-full"
                                    v-model="form.nomor_sip"
                                    required
                                    placeholder="Contoh: SIP-123456789"
                                />
                                <label v-if="form.errors.nomor_sip" class="label">
                                    <span class="label-text-alt text-error">{{ form.errors.nomor_sip }}</span>
                                </label>
                            </div>

                            <!-- Tempat Praktik -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Tempat Praktik *</span>
                                </label>
                                <input
                                    type="text"
                                    class="input input-bordered w-full"
                                    v-model="form.tempat_praktik"
                                    required
                                    placeholder="Contoh: Puskesmas ABC"
                                />
                                <label v-if="form.errors.tempat_praktik" class="label">
                                    <span class="label-text-alt text-error">{{ form.errors.tempat_praktik }}</span>
                                </label>
                            </div>

                            <!-- Tanggal Terbit -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Tanggal Terbit *</span>
                                </label>
                                <input
                                    type="date"
                                    class="input input-bordered w-full"
                                    v-model="form.tanggal_terbit"
                                    required
                                />
                                <label v-if="form.errors.tanggal_terbit" class="label">
                                    <span class="label-text-alt text-error">{{ form.errors.tanggal_terbit }}</span>
                                </label>
                            </div>

                            <!-- Tanggal Berakhir -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Tanggal Berakhir *</span>
                                </label>
                                <input
                                    type="date"
                                    class="input input-bordered w-full"
                                    v-model="form.tanggal_berakhir"
                                    required
                                />
                                <label v-if="form.errors.tanggal_berakhir" class="label">
                                    <span class="label-text-alt text-error">{{ form.errors.tanggal_berakhir }}</span>
                                </label>
                            </div>

                            <!-- File Upload -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">File SIP (Opsional)</span>
                                </label>
                                <input
                                    type="file"
                                    class="file-input file-input-bordered w-full"
                                    @change="handleFileChange"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                />
                                <label class="label">
                                    <span class="label-text-alt">Format: PDF, JPG, JPEG, PNG (Max: 2MB)</span>
                                </label>
                                <label v-if="form.errors.file_sip" class="label">
                                    <span class="label-text-alt text-error">{{ form.errors.file_sip }}</span>
                                </label>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-4">
                                <button :disabled="form.processing" class="btn btn-primary">
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ form.processing ? 'Menyimpan...' : 'Simpan SIP' }}
                                </button>
                                <Link :href="route('sip.index')" class="btn btn-ghost">
                                    Batal
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    pegawai: Object,
})

const form = useForm({
    nomor_sip: '',
    tempat_praktik: '',
    tanggal_terbit: '',
    tanggal_berakhir: '',
    file_sip: null,
})

const handleFileChange = (event) => {
    form.file_sip = event.target.files[0]
}

const submit = () => {
    form.post(route('sip.store'), {
        onSuccess: () => {
            form.reset()
        },
    })
}
</script> 