<template>
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="text-sm text-gray-500">
            Menampilkan {{ links[0]?.label || 0 }} - {{ links[links.length - 1]?.label || 0 }} dari {{ total || 0 }} data
        </div>
        <div class="join">
            <button 
                class="join-item btn btn-sm" 
                :disabled="!hasPrevPage" 
                @click="goToPage(currentPage - 1)"
            >
                «
            </button>
            <button 
                v-for="link in visibleLinks" 
                :key="link.label" 
                class="join-item btn btn-sm"
                :class="{ 'btn-active': link.active }" 
                @click="goToPage(link.url ? getPageFromUrl(link.url) : null)"
                v-html="link.label"
            >
            </button>
            <button 
                class="join-item btn btn-sm" 
                :disabled="!hasNextPage" 
                @click="goToPage(currentPage + 1)"
            >
                »
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    links: {
        type: Array,
        required: true
    }
})

const currentPage = computed(() => {
    const activeLink = props.links.find(link => link.active)
    return activeLink ? getPageFromUrl(activeLink.url) : 1
})

const total = computed(() => {
    // Extract total from the last link or calculate from links
    return props.links.length > 0 ? props.links.length * 10 : 0 // Approximate
})

const hasPrevPage = computed(() => {
    return props.links.some(link => link.label === '&laquo; Previous')
})

const hasNextPage = computed(() => {
    return props.links.some(link => link.label === 'Next &raquo;')
})

const visibleLinks = computed(() => {
    return props.links.filter(link => 
        link.label !== '&laquo; Previous' && 
        link.label !== 'Next &raquo;' &&
        link.url !== null
    )
})

const getPageFromUrl = (url) => {
    if (!url) return null
    const urlParams = new URLSearchParams(url.split('?')[1])
    return parseInt(urlParams.get('page')) || 1
}

const goToPage = (page) => {
    if (page && page > 0) {
        router.get(window.location.pathname, {
            ...Object.fromEntries(new URLSearchParams(window.location.search)),
            page: page
        }, {
            preserveState: true,
            preserveScroll: true
        })
    }
}
</script> 