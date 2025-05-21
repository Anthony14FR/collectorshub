<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';

interface Pokemon {
    id: number;
    pokedexId: number;
    name: string;
    description: string;
    sprite_url: string;
    image_url: string;
    types: string[];
    typeImages: Record<string, string>;
    generation: number;
    rarity: string;
    stats: {
        HP: number;
        attack: number;
        defense: number;
        special_attack: number;
        special_defense: number;
        speed: number;
    };
    evolutions: Array<{
        name: string;
        pokedexId: number;
    }>;
}

interface Props {
    pokemons: Pokemon[];
    error?: string;
    auth?: any;
}

const props = defineProps<Props>();
const searchQuery = ref('');

// Filtered pokemons based on search
const filteredPokemons = computed(() => {
    if (!searchQuery.value) return props.pokemons;
    
    const query = searchQuery.value.toLowerCase();
    return props.pokemons.filter(pokemon => 
        pokemon.name.toLowerCase().includes(query) || 
        pokemon.pokedexId.toString().includes(query) ||
        pokemon.types.some(type => type.toLowerCase().includes(query))
    );
});

// Map of type to tailwind background color
const typeColors: Record<string, string> = {
    'normal': 'bg-gray-400 text-gray-800',
    'combat': 'bg-red-700 text-white',
    'vol': 'bg-indigo-300 text-indigo-800',
    'poison': 'bg-purple-500 text-white',
    'sol': 'bg-yellow-600 text-white',
    'roche': 'bg-yellow-700 text-white',
    'insecte': 'bg-lime-500 text-white',
    'spectre': 'bg-purple-800 text-white',
    'acier': 'bg-gray-500 text-white',
    'feu': 'bg-red-500 text-white',
    'eau': 'bg-blue-500 text-white',
    'plante': 'bg-green-500 text-white',
    'électrik': 'bg-yellow-400 text-yellow-900',
    'psy': 'bg-pink-500 text-white',
    'glace': 'bg-blue-200 text-blue-800',
    'dragon': 'bg-indigo-600 text-white',
    'ténèbres': 'bg-gray-800 text-white',
    'fée': 'bg-pink-300 text-pink-800',
    
    // French alternative spellings and English equivalents
    'electrik': 'bg-yellow-400 text-yellow-900',
    'fee': 'bg-pink-300 text-pink-800',
    'tenebres': 'bg-gray-800 text-white',
    'fighting': 'bg-red-700 text-white',
    'flying': 'bg-indigo-300 text-indigo-800',
    'ground': 'bg-yellow-600 text-white',
    'rock': 'bg-yellow-700 text-white',
    'bug': 'bg-lime-500 text-white',
    'ghost': 'bg-purple-800 text-white',
    'steel': 'bg-gray-500 text-white',
    'fire': 'bg-red-500 text-white',
    'water': 'bg-blue-500 text-white',
    'grass': 'bg-green-500 text-white',
    'electric': 'bg-yellow-400 text-yellow-900',
    'psychic': 'bg-pink-500 text-white',
    'ice': 'bg-blue-200 text-blue-800',
    'dark': 'bg-gray-800 text-white',
    'fairy': 'bg-pink-300 text-pink-800',
    
    // Variants with accents
    'Électrik': 'bg-yellow-400 text-yellow-900',
    'Fée': 'bg-pink-300 text-pink-800',
    'Ténèbres': 'bg-gray-800 text-white',
    'Poison': 'bg-purple-500 text-white',
};

function getTypeColor(type: string): string {
    const lowercaseType = type.toLowerCase();
    return typeColors[lowercaseType] || typeColors[type] || 'bg-gray-300 text-gray-700';
}

// Calculate stat total
function calculateStatTotal(stats: Pokemon['stats']): number {
    return stats.HP + 
           stats.attack + 
           stats.defense + 
           stats.special_attack + 
           stats.special_defense + 
           stats.speed;
}

// Format rarity with first letter capitalized
function formatRarity(rarity: string): string {
    return rarity.charAt(0).toUpperCase() + rarity.slice(1);
}
</script>

<template>
    <Head title="Pokédex" />
    
    <MainLayout :auth="auth">
        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8 text-center">
                    <h1 class="text-3xl font-bold text-gray-900">Pokédex National</h1>
                    <p class="mt-2 text-lg text-gray-600">Découvrez tous les Pokémon et leurs informations détaillées</p>
                </div>

                <!-- Message d'erreur si nécessaire -->
                <div v-if="props.error" class="mb-8 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                {{ props.error }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Filtre et recherche -->
                <div v-if="props.pokemons.length > 0" class="mb-8 flex justify-center">
                    <div class="relative w-full max-w-md">
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            placeholder="Rechercher par nom, numéro ou type..." 
                            class="w-full rounded-full border-gray-300 py-2 pl-10 pr-4 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" 
                        />
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Indicateur de résultat de recherche -->
                <div v-if="searchQuery && props.pokemons.length > 0" class="mb-4 text-center">
                    <p class="text-gray-600">
                        {{ filteredPokemons.length }} résultat(s) pour "{{ searchQuery }}"
                    </p>
                </div>

                <!-- Grid des Pokémon -->
                <div v-if="filteredPokemons.length > 0" 
                     class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    <div v-for="pokemon in filteredPokemons" :key="pokemon.id"
                         class="bg-white rounded-lg shadow-md overflow-hidden transform transition hover:scale-105 hover:shadow-xl">
                        <Link :href="`/pokemon/${pokemon.id}`">
                            <div class="p-4">
                                <!-- Numéro et rareté -->
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium bg-red-100 text-red-800 px-2 py-0.5 rounded-full">
                                        #{{ pokemon.pokedexId.toString().padStart(3, '0') }}
                                    </span>
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">
                                        {{ formatRarity(pokemon.rarity) }}
                                    </span>
                                </div>
                                
                                <!-- Image -->
                                <div class="flex justify-center bg-gray-50 rounded-lg p-2">
                                    <img :src="pokemon.sprite_url" :alt="pokemon.name" 
                                         class="h-24 w-24 object-contain" />
                                </div>
                                
                                <!-- Nom et génération -->
                                <div class="mt-3 text-center">
                                    <h3 class="text-lg font-bold text-gray-800">{{ pokemon.name }}</h3>
                                    <p class="text-xs text-gray-500">Génération {{ pokemon.generation }}</p>
                                </div>
                                
                                <!-- Types -->
                                <div class="mt-2 flex justify-center flex-wrap gap-2">
                                    <span v-for="type in pokemon.types" :key="type"
                                          :class="[getTypeColor(type), 'px-2 py-1 rounded-full text-xs font-medium']">
                                        {{ type }}
                                    </span>
                                </div>
                                
                                <!-- Statistiques de base -->
                                <div class="mt-3 border-t border-gray-100 pt-3">
                                    <div class="grid grid-cols-3 gap-1 text-center text-xs">
                                        <div>
                                            <p class="text-gray-500">PV</p>
                                            <p class="font-semibold">{{ pokemon.stats.HP }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Attaque</p>
                                            <p class="font-semibold">{{ pokemon.stats.attack }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Défense</p>
                                            <p class="font-semibold">{{ pokemon.stats.defense }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <p class="text-xs text-center text-gray-500">Total: <span class="font-semibold">{{ calculateStatTotal(pokemon.stats) }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- État vide -->
                <div v-else class="text-center py-12">
                    <div class="mx-auto w-24 opacity-50">
                        <img src="/images/empty-pokeball.png" alt="Empty Pokeball" onerror="this.src='/images/pokeball.png'" />
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun Pokémon</h3>
                    <p class="mt-1 text-gray-500">{{ props.error ? props.error : 'Nous travaillons pour remplir le Pokédex !' }}</p>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped>
.pokedex-entry {
    transition: all 0.3s ease;
}
</style> 