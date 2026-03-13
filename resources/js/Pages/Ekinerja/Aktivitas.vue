<template>
  <AuthenticatedLayout>
    <template #header>
      <h1 class="text-2xl font-bold mb-4">Aktivitas Kinerja</h1>
    </template>
    <div class="max-w-2xl mx-auto py-8">
      <div class="mb-6">
        <h2 class="font-semibold mb-2">Input Aktivitas</h2>
        <div class="mb-4">
          <label class="block mb-1 font-medium">Tanggal</label>
          <vue-cal
            style="height: 350px"
            hide-view-selector
            default-view="month"
            :disable-views="['years', 'year', 'week', 'day', 'agenda']"
            :selected-date="tanggal"
            @cell-click="onDateSelect"
            @cell-dblclick="onDateDblClick"
            :time="false"
            locale="id"
          >
            <template #cell-content="slotProps">
              <div v-if="slotProps.day && slotProps.day.date && slotProps.day.inMonth"
                :class="{
                  'bg-green-500 text-white rounded-full': aktivitasMap[slotProps.day.date]?.aktivitas,
                  'cursor-pointer': true
                }"
                @click.stop="onDateDblClick({ date: slotProps.day.dateObj })"
              >
                {{ slotProps.day.day }}
              </div>
            </template>
          </vue-cal>
        </div>
      </div>
      <div v-if="aktivitasList.length" class="mt-8">
        <h2 class="font-semibold mb-2">Riwayat Aktivitas</h2>
        <ul class="list-disc ml-6">
          <li v-for="(item, i) in aktivitasList" :key="i">
            {{ item.tanggal }} - {{ item.aktivitas }}
          </li>
        </ul>
      </div>
      <!-- Modal Input Aktivitas -->
      <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
          <h3 class="font-bold mb-2">Input Aktivitas Tanggal {{ tanggalEdit }}</h3>
          <input v-model="aktivitasInput" class="input input-bordered w-full mb-4" placeholder="Deskripsi aktivitas..." />
          <div class="flex justify-end gap-2">
            <div class="tooltip" data-tip="Batal dan tutup modal">
              <button class="btn" @click="showModal = false">Batal</button>
            </div>
            <div class="tooltip" data-tip="Simpan aktivitas">
              <button class="btn btn-primary" @click="simpanAktivitas">Simpan</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';
import VueCal from 'vue-cal';
import 'vue-cal/dist/vuecal.css';

const aktivitasList = ref([]); // [{tanggal, aktivitas}]
const aktivitasMap = computed(() => {
  const map = {};
  aktivitasList.value.forEach(item => { map[item.tanggal] = item; });
  return map;
});
const today = new Date();
const tanggal = ref(today.toISOString().slice(0, 10));

// Modal state
const showModal = ref(false);
const tanggalEdit = ref('');
const aktivitasInput = ref('');

function onDateSelect({ date }) {
  if (date instanceof Date && !isNaN(date)) {
    tanggal.value = date.toISOString().slice(0, 10);
  }
}
function onDateDblClick({ date }) {
  if (date instanceof Date && !isNaN(date)) {
    const dateStr = date.toISOString().slice(0, 10);
    tanggalEdit.value = dateStr;
    aktivitasInput.value = aktivitasMap.value[dateStr]?.aktivitas || '';
    showModal.value = true;
  }
}
function simpanAktivitas() {
  if (!tanggalEdit.value || !aktivitasInput.value) return;
  // Update jika sudah ada, tambah jika belum
  const idx = aktivitasList.value.findIndex(a => a.tanggal === tanggalEdit.value);
  if (idx !== -1) {
    aktivitasList.value[idx].aktivitas = aktivitasInput.value;
  } else {
    aktivitasList.value.push({ tanggal: tanggalEdit.value, aktivitas: aktivitasInput.value });
  }
  showModal.value = false;
}
</script>
