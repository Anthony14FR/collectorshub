<script setup lang="ts">
import type { Pokemon } from '@/types/pokemon';
import type { Pokedex } from '@/types/pokedex';
import CountBadge from '@/Components/UI/CountBadge.vue';
import StarsBadge from '@/Components/UI/StarsBadge.vue';
import RarityBadge from '@/Components/UI/RarityBadge.vue';

interface DisplayPokemon {
  pokedexInfo: Pokedex | null;
  pokemon: Pokemon;
  owned: boolean;
  count: number;
}

interface Props {
  displayPokemon: DisplayPokemon;
}

const { displayPokemon } = defineProps<Props>();

const getStars = (pokedex: Pokedex | null) => {
  if (pokedex?.star !== undefined) {
    return pokedex.star;
  }
  const rarity = displayPokemon.pokemon.rarity;
  if (!rarity) return 1;

  switch (rarity) {
  case 'normal': return 1;
  case 'rare': return 2;
  case 'epic': return 3;
  case 'legendary': return 4;
  default: return 1;
  }
};

const getImagePath = () => {
  const pokemonId = displayPokemon.pokemon.is_shiny 
    ? (displayPokemon.pokemon.id - 1000) + '_S' 
    : displayPokemon.pokemon.id;
  return `/images/pokemon-gifs/${pokemonId}.gif`;
}
</script>

<template>
  <div
    class="relative bg-base-200/60 backdrop-blur-sm rounded-xl p-3 border-2 transition-all duration-300 cursor-pointer group"
    :class="{
      'border-base-300/30 hover:border-primary/50 hover:bg-base-200': displayPokemon.owned,
      'border-transparent': !displayPokemon.owned,
    }"
  >
    <div class="flex flex-col items-center text-center">
      <div class="relative w-24 h-24 mb-2">
        <img
          :src="getImagePath()"
          :alt="displayPokemon.pokemon.name"
          class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-200"
          :class="{ 'brightness-0 opacity-60': !displayPokemon.owned }"
          style="image-rendering: pixelated;"
        />
        <CountBadge v-if="displayPokemon.owned" :count="displayPokemon.count" />
      </div>

      <div v-if="displayPokemon.owned && displayPokemon.pokedexInfo" class="w-full">
        <div class="absolute -top-1 -left-1">
          <StarsBadge :stars="getStars(displayPokemon.pokedexInfo)" size="sm" />
        </div>
        <div v-if="displayPokemon.pokemon.is_shiny" class="absolute -top-1 -right-1 w-5 h-5 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
          <span class="text-yellow-400 text-sm">✨</span>
        </div>
        <h4 class="font-bold text-base-content truncate">{{ displayPokemon.pokedexInfo.nickname || displayPokemon.pokemon.name }}</h4>
        <p class="text-xs text-base-content/70 mb-1">Niv. {{ displayPokemon.pokedexInfo.level }}</p>
        <RarityBadge :rarity="displayPokemon.pokemon.rarity" size="xs" />
      </div>
            
      <div v-else class="w-full">
        <h4 class="font-bold text-base-content/70">#{{ displayPokemon.pokemon.id < 1000 ? displayPokemon.pokemon.id : displayPokemon.pokemon.id - 1000 }} ???</h4>
        <p class="text-xs text-base-content/50">Non capturé</p>
      </div>
    </div>
  </div>
</template> 