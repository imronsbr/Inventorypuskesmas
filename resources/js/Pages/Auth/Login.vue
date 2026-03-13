<template>
  <div class="min-h-screen flex items-center justify-center" :style="{ background: gradient }">
    <div class="w-full max-w-md bg-white shadow-md rounded-lg p-6">
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Masuk ke Akun Anda</h2>
      <form @submit.prevent="submitLogin">
        <div class="mb-4">
          <label for="login" class="block text-sm font-medium text-gray-700">Email atau NRK</label>
          <div class="relative">
            <input
              v-model="form.login"
              type="text"
              id="login"
              placeholder="nama@domain.com atau NRK"
              class="input input-bordered w-full pl-10"
              required
            />
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2.94 6.94a1.5 1.5 0 012.12 0L10 11.88l4.94-4.94a1.5 1.5 0 112.12 2.12l-6 6a1.5 1.5 0 01-2.12 0l-6-6a1.5 1.5 0 010-2.12z" />
              </svg>
            </span>
          </div>
        </div>
        <div class="mb-4">
          <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
          <div class="relative">
            <input
              v-model="form.password"
              type="password"
              id="password"
              placeholder="********"
              class="input input-bordered w-full pl-10"
              required
            />
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 2a6 6 0 00-6 6v4.586l-.707.707A1 1 0 004 15h12a1 1 0 00.707-1.707L16 12.586V8a6 6 0 00-6-6zM8 8a2 2 0 114 0 2 2 0 01-4 0z" clip-rule="evenodd" />
              </svg>
            </span>
          </div>
        </div>
        <div v-if="errorMessage" class="mb-4 text-red-500 text-sm">{{ errorMessage }}</div>
        <div class="flex items-center justify-between">
          <button
            type="submit"
            class="w-full bg-gradient-to-r from-purple-500 via-pink-500 to-red-500 text-white py-2 px-4 rounded-md hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
          >
            Masuk
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const toast = useToast();
const form = ref({
  login: '',
  password: '',
});
const errorMessage = ref('');

const gradient = ref('linear-gradient(to right, #a1c4fd, #c2e9fb)');
const colors = [
  '#a1c4fd', '#c2e9fb', '#d4fc79', '#96e6a1', '#fbc2eb', '#a8edea', '#fed6e3', '#f5f7fa', '#cfd9df'
];



function submitLogin() {
  router.post('/login', form.value, {
    onSuccess: () => {
      toast.success('Login berhasil! Selamat datang.', 'Berhasil');
    },
    onError: (errors) => {
      errorMessage.value = 'Login gagal. Silakan periksa email/NRK dan kata sandi Anda.';
      toast.error('Login gagal. Silakan periksa email/NRK dan kata sandi Anda.', 'Gagal');
    },
  });
}

function updateGradientColors() {
  const randomColors = colors.sort(() => 0.5 - Math.random()).slice(0, 2);
  gradient.value = `linear-gradient(to right, ${randomColors[0]}, ${randomColors[1]})`;
}

onMounted(() => {
  setInterval(updateGradientColors, 5000);
});
</script>

<style>
body {
  font-family: 'Inter', sans-serif;
}
</style>
