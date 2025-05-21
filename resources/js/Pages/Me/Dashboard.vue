<script setup lang="ts">
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type PageProps } from '@/types/page';

defineProps<{
    auth: PageProps['auth']
}>();
</script>

<template>
    <Head title="Mon Espace" />

    <MainLayout :auth="auth">
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Carte des dernières activités -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Dernières Activités</h3>
                        <p class="text-gray-600">Aucune activité récente</p>
                    </div>

                    <!-- Carte des statistiques -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Mes Statistiques</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Pokémon capturés</span>
                                <span class="font-semibold">0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Combats gagnés</span>
                                <span class="font-semibold">0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Badges obtenus</span>
                                <span class="font-semibold">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
