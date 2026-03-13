<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import * as echarts from 'echarts';
import { onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

onMounted(() => {
    try {
        const { props } = usePage();
        const flash = props?.value?.flash || {}; // Safely access props.value and fallback to an empty object

        if (flash.loginSuccess) {
            const toastContainer = document.getElementById('toast-container') || createToastContainer();
            const toast = document.createElement('div');
            toast.className = 'alert shadow-lg text-lg font-semibold text-center';
            toast.style.background = 'linear-gradient(to right, #a1c4fd, #c2e9fb)'; // Gradient background
            toast.style.color = '#333333'; // Dark text for readability
            toast.style.padding = '15px';
            toast.style.borderRadius = '8px';
            toast.style.position = 'fixed';
            toast.style.top = '10%'; // Positioned at the top center
            toast.style.left = '50%';
            toast.style.transform = 'translateX(-50%)';
            toast.style.zIndex = '1000';
            toast.innerHTML = '<div>Login berhasil! Selamat datang di Dashboard.</div>';
            toastContainer.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 2000);
        }

        const chartDom = document.getElementById('dashboardChart');
        const myChart = echarts.init(chartDom);
        const option = {
            title: {
                text: 'Dashboard Chart Example',
                left: 'center',
                textStyle: {
                    fontSize: 18,
                    fontWeight: 'bold',
                },
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow',
                },
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true,
            },
            xAxis: {
                type: 'category',
                data: ['A', 'B', 'C', 'D', 'E'],
                axisLine: {
                    lineStyle: {
                        color: '#ccc',
                    },
                },
                axisLabel: {
                    fontSize: 12,
                },
            },
            yAxis: {
                type: 'value',
                axisLine: {
                    lineStyle: {
                        color: '#ccc',
                    },
                },
                axisLabel: {
                    fontSize: 12,
                },
            },
            series: [
                {
                    name: 'Example Data',
                    type: 'bar',
                    data: [5, 20, 36, 10, 10],
                    itemStyle: {
                        color: '#5470C6',
                    },
                },
            ],
        };
        myChart.setOption(option);
    } catch (error) {
        console.error('Error in mounted hook:', error);
    }
});

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
    return container;
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <!-- Welcome Card -->
                <div class="bg-gradient-to-r from-indigo-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Selamat Datang!</h3>
                            <p class="text-indigo-100">Sistem Informasi Manajemen Puskesmas</p>
                        </div>
                        <div class="text-3xl">🏥</div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik Cepat</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-indigo-600">150</div>
                            <div class="text-sm text-gray-600">Total Barang</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">25</div>
                            <div class="text-sm text-gray-600">Permintaan</div>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Sistem</h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Database</span>
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Online</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Server</span>
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Online</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Grafik Dashboard</h3>
                <div id="dashboardChart" style="width: 100%; height: 400px;"></div>
            </div>
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold">Permintaan Terbaru</h3>
              <div class="tooltip" data-tip="Lihat semua permintaan">
                <button @click="$inertia.visit('/barang/permintaan')" class="btn btn-sm btn-outline">
                  Lihat Semua
                </button>
              </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
