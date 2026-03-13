<template>
  <AuthenticatedLayout>
    <Head title="Master Barang" />
    
    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="text-center">
              <h2 class="text-2xl font-bold text-gray-900 mb-4">Master Barang</h2>
              <p class="text-gray-600 mb-6">Halaman ini telah dipindahkan ke struktur menu yang baru.</p>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="card bg-base-100 shadow-xl">
                  <div class="card-body">
                    <h3 class="card-title">Master Data</h3>
                    <p>Kelola data master barang, obat, dan alat kesehatan</p>
                    <div class="card-actions justify-end">
                      <div class="tooltip" data-tip="Lihat data master barang">
                        <a href="/master-barang-habis-pakai" class="btn btn-primary">Lihat Master Data</a>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="card bg-base-100 shadow-xl">
                  <div class="card-body">
                    <h3 class="card-title">Stok & Inventaris</h3>
                    <p>Kelola stok barang dan inventaris asset</p>
                    <div class="card-actions justify-end">
                      <div class="tooltip" data-tip="Lihat stok barang">
                        <a href="/stok-habis-pakai" class="btn btn-primary">Lihat Stok</a>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="card bg-base-100 shadow-xl">
                  <div class="card-body">
                    <h3 class="card-title">Laporan</h3>
                    <p>Lihat laporan stok dan permintaan barang</p>
                    <div class="card-actions justify-end">
                      <div class="tooltip" data-tip="Lihat laporan stok">
                        <a href="/laporan/stok" class="btn btn-primary">Lihat Laporan</a>
                      </div>
                    </div>
                  </div>
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
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
</script> 