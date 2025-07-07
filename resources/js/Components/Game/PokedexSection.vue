<script setup lang="ts">
import { computed } from 'vue';
import PokedexCard from '@/Components/Cards/PokedexCard.vue';
import type { Pokedex } from '@/types/pokedex';

interface Props {
    pokedex?: Pokedex[];
    onOpenModal: () => void;
}

const { pokedex = [], onOpenModal } = defineProps<Props>();

const favoritePokemon = computed(() => pokedex?.filter((p) => p.is_favorite) || []);
const teamPokemon = computed(() => pokedex?.filter((p) => p.is_in_team) || []);
const shinyPokemon = computed(() => pokedex?.filter((p) => p.pokemon?.is_shiny) || []);
</script>

<template>
    <div class="h-full flex flex-col bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden max-h-[500px]">
        <div class="shrink-0 p-3 bg-gradient-to-r from-secondary/10 to-accent/5 border-b border-secondary/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">📚</span>
                MON POKÉDEX
            </h3>
        </div>

        <div class="flex-1 overflow-y-auto p-3" style="max-height: calc(100% - 11rem);">
            <div v-if="pokedex && pokedex.length > 0" class="space-y-2">
                <PokedexCard
                    v-for="pokemon in pokedex"
                    :key="pokemon.id"
                    :entry="pokemon"
                />
            </div>
            <div v-else class="text-center py-8">
                <p class="text-2xl mb-2">🎒</p>
                <p class="text-sm mb-1">Aucun Pokémon</p>
                <p class="opacity-70 text-xs">Partez à l'aventure !</p>
            </div>
        </div>

        <div v-if="pokedex && pokedex.length > 4" class="shrink-0 p-3 border-t border-secondary/20">
            <button
                @click="onOpenModal"
                class="w-full group relative overflow-hidden px-4 py-2 text-sm font-medium bg-gradient-to-br from-secondary to-accent border border-secondary/30 hover:border-secondary/50 rounded-lg shadow-lg shadow-secondary/15 hover:shadow-xl hover:shadow-secondary/20 hover:-translate-y-0.5 transition-all duration-300 cursor-pointer before:absolute before:inset-0 before:bg-gradient-to-r before:from-white/0 before:via-white/12 before:to-white/0 before:-translate-x-full hover:before:translate-x-full before:transition-transform before:duration-700 before:rounded-lg"
            >
                <span class="relative z-10 text-base-100 font-bold text-xs">
                    Voir tous ({{ pokedex.length }})
                </span>
            </button>
        </div>

        <div class="bg-gradient-to-r from-secondary/10 to-accent/10 px-3 py-2 border-t border-primary/20 flex-shrink-0">
            <div class="grid grid-cols-4 gap-2 text-center">
                <div class="group cursor-pointer">
                    <div class="text-xs text-base-content/70 group-hover:text-base-content transition-colors">Total</div>
                    <div class="text-sm font-bold group-hover:scale-110 transition-transform">{{ pokedex?.length || 0 }}</div>
                </div>
                <div class="group cursor-pointer">
                    <div class="text-xs text-base-content/70 group-hover:text-base-content transition-colors">Équipe</div>
                    <div class="text-sm font-bold text-blue-400 group-hover:scale-110 transition-transform">{{ teamPokemon.length }}</div>
                </div>
                <div class="group cursor-pointer">
                    <div class="text-xs text-base-content/70 group-hover:text-base-content transition-colors">Favoris</div>
                    <div class="text-sm font-bold text-pink-400 group-hover:scale-110 transition-transform">{{ favoritePokemon.length }}</div>
                </div>
                <div class="group cursor-pointer">
                    <div class="text-xs text-base-content/70 group-hover:text-base-content transition-colors">Shiny</div>
                    <div class="text-sm font-bold text-yellow-400 group-hover:scale-110 transition-transform">{{ shinyPokemon.length }}</div>
                </div>
            </div>
        </div>
    </div>
</template>