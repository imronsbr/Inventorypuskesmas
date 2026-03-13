<template>
  <AuthenticatedLayout>
    <template #header>
      <h1 class="text-2xl font-bold mb-4">eKinerja</h1>
    </template>
    <div class="max-w-2xl mx-auto py-8">
      <div class="mb-6">
        <h2 class="font-semibold mb-2">Menu eKinerja</h2>
        <ul class="menu bg-base-100 rounded-box">
          <li><a :href="route('ekinerja.sasaran')">Sasaran Kinerja</a></li>
          <li><a :href="route('ekinerja.aktivitas')">Aktivitas Kinerja</a></li>
        </ul>
      </div>
      <p>Silakan pilih submenu eKinerja di atas.</p>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
</script>
