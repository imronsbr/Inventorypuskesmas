<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import * as echarts from 'echarts';
import { onMounted, ref } from 'vue';

const chartData = ref({
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    values: [120, 200, 150, 80, 70, 110]
});

onMounted(() => {
    // Initialize charts
    initBarChart();
    initPieChart();
    initLineChart();
});

function initBarChart() {
    const chartDom = document.getElementById('barChart');
    const myChart = echarts.init(chartDom);
    
    const option = {
        title: {
            text: 'Statistik Barang per Bulan',
            left: 'center',
            textStyle: {
                color: 'var(--fallback-bc,oklch(var(--bc)/0.6))'
            }
        },
        tooltip: {
            trigger: 'axis'
        },
        xAxis: {
            type: 'category',
            data: chartData.value.categories,
            axisLabel: {
                color: 'var(--fallback-bc,oklch(var(--bc)/0.6))'
            }
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                color: 'var(--fallback-bc,oklch(var(--bc)/0.6))'
            }
        },
        series: [{
            data: chartData.value.values,
            type: 'bar',
            itemStyle: {
                color: 'hsl(var(--p))'
            }
        }]
    };
    
    myChart.setOption(option);
}

function initPieChart() {
    const chartDom = document.getElementById('pieChart');
    const myChart = echarts.init(chartDom);
    
    const option = {
        title: {
            text: 'Distribusi Kategori Barang',
            left: 'center',
            textStyle: {
                color: 'var(--fallback-bc,oklch(var(--bc)/0.6))'
            }
        },
        tooltip: {
            trigger: 'item'
        },
        series: [{
            name: 'Kategori',
            type: 'pie',
            radius: '50%',
            data: [
                { value: 335, name: 'ATK' },
                { value: 310, name: 'ART' },
                { value: 234, name: 'Alat Makan' },
                { value: 135, name: 'Alat Laboratorium' },
                { value: 1548, name: 'Lainnya' }
            ],
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }]
    };
    
    myChart.setOption(option);
}

function initLineChart() {
    const chartDom = document.getElementById('lineChart');
    const myChart = echarts.init(chartDom);
    
    const option = {
        title: {
            text: 'Trend Permintaan Barang',
            left: 'center',
            textStyle: {
                color: 'var(--fallback-bc,oklch(var(--bc)/0.6))'
            }
        },
        tooltip: {
            trigger: 'axis'
        },
        xAxis: {
            type: 'category',
            data: chartData.value.categories,
            axisLabel: {
                color: 'var(--fallback-bc,oklch(var(--bc)/0.6))'
            }
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                color: 'var(--fallback-bc,oklch(var(--bc)/0.6))'
            }
        },
        series: [{
            data: [820, 932, 901, 934, 1290, 1330],
            type: 'line',
            smooth: true,
            itemStyle: {
                color: 'hsl(var(--s))'
            }
        }]
    };
    
    myChart.setOption(option);
}
</script>

<template>
    <Head title="Chart & Analytics" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-base-content">
                Chart & Analytics
            </h2>
        </template>

        <div class="py-6">
            <div class="w-full">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="card bg-base-100 shadow-xl border border-base-300">
                        <div class="card-body">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-primary/10">
                                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-base-content/70">Total Barang</p>
                                    <p class="text-2xl font-bold text-base-content">1,234</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-base-100 shadow-xl border border-base-300">
                        <div class="card-body">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-success/10">
                                    <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-base-content/70">Permintaan Disetujui</p>
                                    <p class="text-2xl font-bold text-base-content">89</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-base-100 shadow-xl border border-base-300">
                        <div class="card-body">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-warning/10">
                                    <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-base-content/70">Menunggu Approval</p>
                                    <p class="text-2xl font-bold text-base-content">12</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-base-100 shadow-xl border border-base-300">
                        <div class="card-body">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-error/10">
                                    <svg class="w-6 h-6 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-base-content/70">Stok Menipis</p>
                                    <p class="text-2xl font-bold text-base-content">5</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Bar Chart -->
                    <div class="card bg-base-100 shadow-xl border border-base-300">
                        <div class="card-body">
                            <div id="barChart" style="width: 100%; height: 400px;"></div>
                        </div>
                    </div>

                    <!-- Pie Chart -->
                    <div class="card bg-base-100 shadow-xl border border-base-300">
                        <div class="card-body">
                            <div id="pieChart" style="width: 100%; height: 400px;"></div>
                        </div>
                    </div>

                    <!-- Line Chart -->
                    <div class="card bg-base-100 shadow-xl border border-base-300 lg:col-span-2">
                        <div class="card-body">
                            <div id="lineChart" style="width: 100%; height: 400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 