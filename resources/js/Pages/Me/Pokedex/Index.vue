<script setup lang="ts">
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type PageProps } from '@/types/page';

interface Pokemon {
    id: number;
    name: string;
    level: number;
    experience: number;
    sprite_url: string;
    types: string[];
    is_in_team: boolean;
}

interface Props {
    auth: PageProps['auth'];
    pokemons: Pokemon[];
}

const props = defineProps<Props>();
</script>

<template>
    <Head title="Mon Pokédex" />

    <MainLayout :auth="props.auth">
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-4 text-center">
                    <h1 class="text-3xl font-bold text-gray-900">Mon Pokédex</h1>
                    <p class="mt-2 text-lg text-gray-600">Gérez votre collection de Pokémon</p>
                </div>
                
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Stats Summary -->
                        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div class="rounded-lg bg-blue-100 p-4 text-center">
                                <h3 class="text-lg font-semibold text-blue-800">Total Pokémon</h3>
                                <p class="text-2xl font-bold text-blue-600">{{ props.pokemons.length }}</p>
                            </div>
                            <div class="rounded-lg bg-green-100 p-4 text-center">
                                <h3 class="text-lg font-semibold text-green-800">Dans l'équipe</h3>
                                <p class="text-2xl font-bold text-green-600">
                                    {{ props.pokemons.filter(p => p.is_in_team).length }}/6
                                </p>
                            </div>
                            <div class="rounded-lg bg-purple-100 p-4 text-center">
                                <h3 class="text-lg font-semibold text-purple-800">Niveau moyen</h3>
                                <p class="text-2xl font-bold text-purple-600">
                                    {{ Math.round(props.pokemons.reduce((acc, p) => acc + p.level, 0) / props.pokemons.length || 0) }}
                                </p>
                            </div>
                        </div>

                        <!-- Pokemon Grid -->
                        <div v-if="props.pokemons.length > 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            <div v-for="pokemon in props.pokemons" :key="pokemon.id"
                                class="relative overflow-hidden rounded-lg border-4 border-gray-200 bg-white p-4 shadow-lg transition-transform hover:scale-105">
                                <div class="absolute top-0 right-0 p-2">
                                    <span v-if="pokemon.is_in_team"
                                        class="rounded-full bg-green-500 px-2 py-1 text-xs font-semibold text-white">
                                        Équipe
                                    </span>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <img :src="pokemon.sprite_url" :alt="pokemon.name"
                                        class="h-24 w-24 object-contain" />
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-800">{{ pokemon.name }}</h3>
                                        <div class="mt-1 flex flex-wrap gap-2">
                                            <span v-for="type in pokemon.types" :key="type"
                                                class="rounded-full bg-gray-200 px-2 py-1 text-xs font-semibold text-gray-700">
                                                {{ type }}
                                            </span>
                                        </div>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-600">Niveau {{ pokemon.level }}</p>
                                            <div class="mt-1 h-2 w-full rounded-full bg-gray-200">
                                                <div class="h-full rounded-full bg-blue-500"
                                                    :style="{ width: `${(pokemon.experience % 100)}%` }"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center">
                            <div class="mx-auto w-24 opacity-50">
                                <img src="/images/empty-pokeball.png" alt="Empty Pokeball" />
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun Pokémon</h3>
                            <p class="mt-1 text-gray-500">Commencez votre aventure en attrapant des Pokémon !</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped>
.pokemon-card {
    transition: all 0.3s ease;
}

.pokemon-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style> 