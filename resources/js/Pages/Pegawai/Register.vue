<template>
  <div class="max-w-4xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Tabel Register Pegawai</h1>
    <div v-if="userRole === 'admin'">
      <div class="tooltip" data-tip="Tambah data pegawai baru">
        <button class="btn btn-primary mb-4" @click="openTambahRegister">Tambah Register</button>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="table table-zebra w-full">
        <thead>
          <tr>
            <th>Nama Pegawai</th>
            <th>NIP</th>
            <th>Jabatan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="pegawai in pegawais" :key="pegawai.id">
            <td>{{ pegawai.nama }}</td>
            <td>{{ pegawai.nip }}</td>
            <td>{{ pegawai.jabatan }}</td>
            <td>
              <div class="flex gap-2">
                <div class="tooltip" data-tip="Lihat detail pegawai">
                  <button class="btn btn-sm btn-info">Detail</button>
                </div>
                <div v-if="userRole === 'admin'" class="tooltip" data-tip="Edit data pegawai">
                  <button class="btn btn-sm btn-warning">Edit</button>
                </div>
                <div v-if="userRole === 'admin'" class="tooltip" data-tip="Hapus data pegawai">
                  <button class="btn btn-sm btn-error">Hapus</button>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
const toast = useToast();
const page = usePage();
const userRole = page.props.auth && page.props.auth.user && page.props.auth.user.role ? page.props.auth.user.role : 'user';
// Dummy data pegawai
const pegawais = ref([
  { id: 1, nama: 'Budi', nip: '123456', jabatan: 'Staff' },
  { id: 2, nama: 'Siti', nip: '654321', jabatan: 'Kasubag' },
]);
function openTambahRegister() {
  toast.info('Fitur tambah register hanya untuk admin.', 'Informasi');
}
</script>
