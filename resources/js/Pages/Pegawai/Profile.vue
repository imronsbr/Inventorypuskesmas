<template>
  <AuthenticatedLayout>
    <Head title="Profile Pegawai" />
    
    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
              <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Profile Pegawai</h2>
                <p class="text-sm text-gray-600 mt-1">Informasi lengkap data pegawai</p>
              </div>
              <div class="flex gap-2">
                <Link :href="route('pegawai.edit', pegawai.id)" class="btn btn-outline btn-sm">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                  Edit Profile
                </Link>
                <Link :href="route('pegawai.index')" class="btn btn-ghost btn-sm">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                  </svg>
                  Kembali
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Profile Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Profile Card -->
          <div class="lg:col-span-1">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <div class="text-center mb-6">
                  <div class="avatar placeholder mb-4">
                    <div class="bg-primary text-primary-content rounded-full w-24">
                      <span class="text-3xl">{{ getInitials(pegawai.nama) }}</span>
                    </div>
                  </div>
                  <h3 class="text-xl font-semibold text-gray-900">{{ pegawai.nama }}</h3>
                  <p class="text-sm text-gray-600">{{ pegawai.nip || 'NIP tidak tersedia' }}</p>
                  <div class="badge badge-primary mt-2">{{ getStatusLabel(pegawai.status_pegawai) }}</div>
                </div>

                <!-- Contact Info -->
                <div class="space-y-3">
                  <div v-if="pegawai.email" class="flex items-center text-sm">
                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>{{ pegawai.email }}</span>
                  </div>
                  <div v-if="pegawai.no_hp" class="flex items-center text-sm">
                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span>{{ pegawai.no_hp }}</span>
                  </div>
                  <div v-if="pegawai.unit_kerja" class="flex items-center text-sm">
                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>{{ pegawai.unit_kerja }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pribadi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                    <p class="text-sm text-gray-900">{{ getJenisKontrakLabel(pegawai.jenis_kontrak) }}</p>
                  </div>
                  <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-500">Alamat</label>
                    <p class="text-sm text-gray-900">{{ pegawai.alamat || '-' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Jabatan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Jabatan</h3>
                <div v-if="pegawai.jabatans && pegawai.jabatans.length > 0" class="space-y-2">
                  <div v-for="jabatan in pegawai.jabatans" :key="jabatan.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                      <p class="font-medium text-gray-900">{{ jabatan.nama }}</p>
                      <p class="text-sm text-gray-600">{{ jabatan.deskripsi || 'Tidak ada deskripsi' }}</p>
                    </div>
                    <div class="badge badge-primary">{{ jabatan.level || 'Level tidak tersedia' }}</div>
                  </div>
                </div>
                <div v-else class="text-center py-4 text-gray-500">
                  <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  <p>Belum ada jabatan yang ditugaskan</p>
                </div>
              </div>
            </div>

            <!-- STR & SIP -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- STR -->
              <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">STR (Surat Tanda Registrasi)</h3>
                  <div v-if="pegawai.str" class="space-y-3">
                    <div>
                      <label class="text-sm font-medium text-gray-500">Nomor STR</label>
                      <p class="text-sm text-gray-900 font-mono">{{ pegawai.str.nomor_str }}</p>
                    </div>
                    <div>
                      <label class="text-sm font-medium text-gray-500">Tanggal Terbit</label>
                      <p class="text-sm text-gray-900">{{ formatDate(pegawai.str.tanggal_terbit) }}</p>
                    </div>
                  </div>
                  <div v-else class="text-center py-4 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p>Data STR belum tersedia</p>
                  </div>
                </div>
              </div>

              <!-- SIP -->
              <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">SIP (Surat Izin Praktik)</h3>
                  <div v-if="pegawai.sip" class="space-y-3">
                    <div>
                      <label class="text-sm font-medium text-gray-500">Nomor SIP</label>
                      <p class="text-sm text-gray-900 font-mono">{{ pegawai.sip.nomor_sip }}</p>
                    </div>
                    <div>
                      <label class="text-sm font-medium text-gray-500">Tempat Praktik</label>
                      <p class="text-sm text-gray-900">{{ pegawai.sip.tempat_praktik }}</p>
                    </div>
                  </div>
                  <div v-else class="text-center py-4 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p>Data SIP belum tersedia</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Keluarga -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Keluarga</h3>
                <div v-if="pegawai.keluargas && pegawai.keluargas.length > 0" class="overflow-x-auto">
                  <table class="table table-zebra w-full">
                    <thead>
                      <tr>
                        <th class="text-sm">Nama</th>
                        <th class="text-sm">Hubungan</th>
                        <th class="text-sm">Jenis Kelamin</th>
                        <th class="text-sm">Tempat Lahir</th>
                        <th class="text-sm">Tanggal Lahir</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="keluarga in pegawai.keluargas" :key="keluarga.id">
                        <td class="text-sm">{{ keluarga.nama }}</td>
                        <td class="text-sm">{{ keluarga.hubungan }}</td>
                        <td class="text-sm">{{ getJenisKelaminLabel(keluarga.jenis_kelamin) }}</td>
                        <td class="text-sm">{{ keluarga.tempat_lahir || '-' }}</td>
                        <td class="text-sm">{{ formatDate(keluarga.tanggal_lahir) }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div v-else class="text-center py-4 text-gray-500">
                  <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  <p>Data keluarga belum tersedia</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  pegawai: Object
})

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

const getJenisKontrakLabel = (jenis) => {
  switch (jenis) {
    case 'tetap': return 'Tetap'
    case 'kontrak': return 'Kontrak'
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
</script> 