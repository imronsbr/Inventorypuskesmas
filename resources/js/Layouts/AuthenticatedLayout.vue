<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();
const isLoading = ref(false);

// Helper untuk cek route tersedia di Ziggy
function hasRoute(name) {
    return typeof route === 'function' && route().has(name);
}

// Example menu items with roles
const menuItems = [
    {
        label: 'Dashboard',
        routeName: 'dashboard',
        roles: ['admin', 'user', 'manager'],
    },
    {
        label: 'eKinerja',
        roles: ['admin', 'user', 'manager'],
        children: [
            { label: 'Laporan', routeName: 'ekinerja.laporan', roles: ['admin', 'user', 'manager'] },
            { label: 'Sasaran', routeName: 'ekinerja.sasaran', roles: ['admin', 'user', 'manager'] },
            { label: 'Aktivitas', routeName: 'ekinerja.aktivitas', roles: ['admin', 'user', 'manager'] },
        ],
    },
    {
        label: 'Master Data',
        roles: ['admin', 'user', 'manager'],
        children: [
            { label: 'Barang Habis Pakai', routeName: 'master-barang-habis-pakai.index', roles: ['admin', 'user', 'manager'] },
            { label: 'Barang Modal/Asset', routeName: 'master-barang-modal.index', roles: ['admin', 'user', 'manager'] },
            { label: 'Obat', routeName: 'master-obat.index', roles: ['admin', 'user', 'manager'] },
            { label: 'Monitoring Alkes', routeName: 'monitoring-alkes.index', roles: ['admin', 'user', 'manager'] },
        ],
    },
    {
        label: 'Stok & Inventaris',
        roles: ['admin', 'user', 'manager'],
        children: [
            { label: 'Stok Barang', routeName: 'stok-habis-pakai.index', roles: ['admin', 'user', 'manager'] },
            { label: 'Detail Asset', routeName: 'detail-barang-modal.index', roles: ['admin', 'user', 'manager'] },
        ],
    },
    {
        label: 'Permintaan',
        roles: ['admin', 'user', 'manager'],
        children: [
            { label: 'Buat Permintaan', routeName: 'permintaan-barang.create', roles: ['admin', 'user', 'manager'] },
            { label: 'Daftar Permintaan', routeName: 'permintaan-barang.index', roles: ['admin', 'user', 'manager'] },
            { label: 'Approval', routeName: 'permintaan-barang.approval', roles: ['admin', 'manager'] },
        ],
    },
    {
        label: 'Pengadaan',
        roles: ['admin', 'user', 'manager'],
        children: [
            { label: 'Daftar Pengadaan', routeName: 'pengadaan-barang.index', roles: ['admin', 'user', 'manager'] },
            { label: 'Buat Pengadaan', routeName: 'pengadaan-barang.create', roles: ['admin', 'user', 'manager'] },
        ],
    },
    {
        label: 'Laporan',
        roles: ['admin', 'user', 'manager'],
        children: [
            { label: 'Laporan Stok', routeName: 'laporan.stok', roles: ['admin', 'user', 'manager'] },
            { label: 'Laporan Permintaan', routeName: 'laporan.permintaan', roles: ['admin', 'user', 'manager'] },
            { label: 'Chart & Analytics', routeName: 'master-barang.chart', roles: ['admin', 'user', 'manager'] },
        ],
    },
    {
        label: 'Pegawai',
        roles: ['admin', 'user', 'manager'],
        children: [
            { label: 'Daftar Pegawai', routeName: 'pegawai.index', roles: ['admin', 'user', 'manager'] },
        ],
    },
    {
        label: 'Dokumen Saya',
        roles: ['admin', 'user', 'manager'],
        children: [
            { label: 'Daftar STR', routeName: 'str.index', roles: ['admin', 'user', 'manager'] },
            { label: 'Daftar SIP', routeName: 'sip.index', roles: ['admin', 'user', 'manager'] },
        ],
    },
];

// Cek user dari inertia page props
const user = page.props.auth && page.props.auth.user ? page.props.auth.user : null;
// Get user role, fallback ke 'user' jika tidak ada
const userRole = user && user.role ? user.role : 'user';
// Filter menu items based on user role dan route tersedia
const filteredMenu = computed(() => menuItems
    .filter(item => item.roles.includes(userRole))
    .map(item => {
        if (item.children) {
            // Filter and map children
            const children = item.children
                .filter(child => child.roles.includes(userRole))
                .filter(child => !child.routeName || hasRoute(child.routeName))
                .map(child => ({
                    ...child,
                    href: child.routeName ? route(child.routeName) : '#',
                }));
            if (children.length === 0) return null;
            return { ...item, children };
        }
        if (!item.routeName || !hasRoute(item.routeName)) return null;
        return {
            ...item,
            href: route(item.routeName),
        };
    })
    .filter(Boolean)
);

onMounted(() => {
    isLoading.value = false; // Reset loading saat mount (initial load)
    router.on('start', () => { isLoading.value = true; });
    router.on('finish', () => { isLoading.value = false; });
});
</script>

<template>
  <div>
    <!-- Global Loading Overlay -->
    <transition name="fade">
      <div v-if="isLoading" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
        <div class="flex flex-col items-center">
          <span class="loading loading-infinity loading-lg text-primary"></span>
          <span class="mt-4 text-white font-semibold">Tunggu Sebentar</span>
        </div>
      </div>
    </transition>
    
    <div v-if="user" class="min-h-screen bg-gradient-to-br from-base-100 via-base-200 to-base-300">
      <!-- Top Navigation Bar -->
      <div class="navbar bg-base-100/90 shadow-lg border-b border-base-300 sticky top-0 z-40 backdrop-blur-md">
        <div class="navbar-start">
            <!-- Logo -->
          <Link :href="route('dashboard')" class="btn btn-ghost normal-case text-xl">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-secondary text-primary-content flex items-center justify-center font-bold">
              S
            </div>
            <span class="font-bold text-primary ml-2">SIMP</span>
            </Link>
        </div>
            
        <!-- Desktop Menu -->
        <div class="navbar-center hidden lg:flex">
          <ul class="menu menu-horizontal px-1">
              <template v-for="item in filteredMenu" :key="item.label">
              <li v-if="!item.children">
                <Link 
                  :href="item.href"
                  :class="route().current(item.routeName) ? 'active' : ''"
                  class="rounded-lg"
                >
                  {{ item.label }}
                </Link>
              </li>
              <li v-else>
                <details>
                  <summary :class="item.children.some(child => route().current(child.routeName)) ? 'active' : ''">
                      {{ item.label }}
                  </summary>
                  <ul class="p-2 bg-base-100 rounded-box shadow-lg">
                    <li v-for="child in item.children" :key="child.label">
                      <Link 
                        :href="child.href"
                        :class="route().current(child.routeName) ? 'active' : ''"
                      >
                        {{ child.label }}
                      </Link>
                    </li>
                  </ul>
                </details>
              </li>
            </template>
          </ul>
        </div>
        
        <!-- User Dropdown -->
        <div class="navbar-end">
          <div class="dropdown dropdown-end">
            <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
              <div class="w-10 rounded-full bg-gradient-to-br from-primary to-secondary text-primary-content flex items-center justify-center font-bold text-lg">
                {{ $page.props.auth.user.name[0] }}
                </div>
            </div>
            <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
              <li>
                <Link :href="route('profile.edit')" class="justify-between">
                  Profile
                  <span class="badge">New</span>
                </Link>
              </li>
              <li>
                <Link :href="route('logout')" method="post" as="button">
                  Logout
                </Link>
              </li>
            </ul>
          </div>
            
            <!-- Mobile Menu Button -->
            <div class="lg:hidden">
            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="btn btn-ghost btn-circle">
                <svg v-if="!showingNavigationDropdown" class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg v-else class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      
      <!-- Mobile Menu -->
      <transition name="slide-fade">
        <div v-show="showingNavigationDropdown" class="lg:hidden bg-base-100/95 border-b border-base-300 shadow-lg">
          <ul class="menu p-4 w-full">
            <template v-for="item in filteredMenu" :key="item.label">
              <li v-if="!item.children">
                <Link 
                  :href="item.href"
                  :class="route().current(item.routeName) ? 'active' : ''"
                  class="rounded-lg"
                >
                  {{ item.label }}
                </Link>
              </li>
              <li v-else>
                <details>
                  <summary :class="item.children.some(child => route().current(child.routeName)) ? 'active' : ''">
                    {{ item.label }}
                  </summary>
                  <ul>
                    <li v-for="child in item.children" :key="child.label">
                      <Link 
                        :href="child.href"
                        :class="route().current(child.routeName) ? 'active' : ''"
                      >
                        {{ child.label }}
                      </Link>
                    </li>
                  </ul>
                </details>
              </li>
            </template>
          </ul>
          <div class="border-t border-base-300 p-4">
            <div class="flex items-center gap-3 mb-2">
              <div class="avatar">
                <div class="w-10 rounded-full bg-gradient-to-br from-primary to-secondary text-primary-content flex items-center justify-center font-bold">
                {{ $page.props.auth.user.name[0] }}
                </div>
              </div>
              <div>
                <div class="font-semibold text-base-content">{{ $page.props.auth.user.name }}</div>
                <div class="text-sm text-base-content/70">{{ $page.props.auth.user.email }}</div>
              </div>
            </div>
            <div class="flex gap-2">
              <Link :href="route('profile.edit')" class="btn btn-sm btn-outline btn-primary">
                Profile
              </Link>
              <Link :href="route('logout')" method="post" as="button" class="btn btn-sm btn-outline btn-error">
                Logout
              </Link>
            </div>
          </div>
        </div>
      </transition>
      
      <!-- Page Heading -->
      <header class="bg-base-100/90 shadow-sm" v-if="$slots.header">
        <div class="px-4 py-6 sm:px-6 lg:px-8">
          <slot name="header" />
        </div>
      </header>
      
      <!-- Page Content -->
      <main>
        <div class="w-full min-h-screen px-4 sm:px-6 lg:px-8 py-6 bg-base-100/80 text-base-content transition-all duration-300">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-fade-enter-active, .slide-fade-leave-active { transition: all 0.3s; }
.slide-fade-enter-from, .slide-fade-leave-to { opacity: 0; transform: translateY(-10px); }
</style>
