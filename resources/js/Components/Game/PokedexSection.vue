<script setup lang="ts">
import { computed } from 'vue';
import StarsBadge from '@/Components/UI/StarsBadge.vue';
import RarityBadge from '@/Components/UI/RarityBadge.vue';
import type { Pokedex } from '@/types/pokedex';

interface Props {
    pokedex?: Pokedex[];
    onOpenModal: () => void;
}

const { pokedex = [], onOpenModal } = defineProps<Props>();

const favoritePokemon = computed(() => pokedex?.filter((p) => p.is_favorite) || []);
const teamPokemon = computed(() => pokedex?.filter((p) => p.is_in_team) || []);
const shinyPokemon = computed(() => pokedex?.filter((p) => p.pokemon?.is_shiny) || []);

const getStars = (pokemon: Pokedex) => {
  if (pokemon.star !== undefined) {
    return pokemon.star;
  }
  const rarity = pokemon.pokemon?.rarity;
  if (!rarity) return 1;
  
  switch(rarity) {
    case 'normal': return 1;
    case 'rare': return 2;
    case 'epic': return 3;
    case 'legendary': return 4;
    default: return 1;
  }
};
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
            <div v-if="favoritePokemon && favoritePokemon.length > 0" class="space-y-2">
                <div
                    v-for="pokemon in favoritePokemon"
                    :key="pokemon.id"
                    class="bg-base-200/30 backdrop-blur-sm rounded-lg p-3 border border-base-300/20 transition-all duration-200 group"
                >
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <img
                                :src="`/images/pokemon-gifs/${pokemon.pokemon.is_shiny ? (pokemon.pokemon.id - 1000) + '_S' : pokemon.pokemon.id}.gif`"
                                :alt="pokemon.pokemon.name"
                                class="w-12 h-12 object-contain group-hover:scale-110 transition-transform duration-200"
                                style="image-rendering: pixelated;"
                            />
                            <div v-if="pokemon.pokemon.is_shiny" class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
                                <span class="text-yellow-400 text-xs">✨</span>
                            </div>
                            <div class="absolute -top-1 -left-1">
                                <StarsBadge :stars="getStars(pokemon)" size="xs" />
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1">
                                <h4 class="font-bold text-sm text-base-content truncate">{{ pokemon.nickname || pokemon.pokemon.name }}</h4>
                            </div>
                            <p class="text-xs text-base-content/70 mb-1">Niv. {{ pokemon.level }}</p>
                            <div v-if="pokemon.pokemon.rarity" class="mb-1">
                                <RarityBadge :rarity="pokemon.pokemon.rarity" size="xs" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-8">
                <p class="text-2xl mb-2">❤️</p>
                <p class="text-sm mb-1">Aucun favori</p>
                <p class="opacity-70 text-xs">Cliquez sur un Pokémon pour l'ajouter !</p>
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