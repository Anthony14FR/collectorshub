<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    promoCodes: any[];
}>();

const getStatusColor = (status: string) => {
    switch (status) {
        case 'active': return 'bg-green-100 text-green-800';
        case 'inactive': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

</script>

<template>

    <Head title="Codes promo" />

    <div class="min-h-screen bg-gray-100 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Codes promo</h1>
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-gray-900">Codes promo</h1>
                        <Link href="/promocodes/create"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Nouveau code promo
                        </Link>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <div v-for="promoCode in promoCodes" :key="promoCode.id" class="bg-white shadow rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ promoCode.code }}</h3>
                                    <p class="text-sm text-gray-500">{{ promoCode.description }}</p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span :class="getStatusColor(promoCode.status)">
                                        {{ promoCode.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>