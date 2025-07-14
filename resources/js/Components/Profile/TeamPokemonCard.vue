<script setup lang="ts">
import StarsBadge from '@/Components/UI/StarsBadge.vue';
import type { Pokedex } from '@/types/pokedex';
import { computed } from 'vue';
import { getRarityStars } from '@/utils/marketplace';

interface Props {
  pokemon: Pokedex;
}

const { pokemon } = defineProps<Props>();

const imageUrl = computed(() => {
  const pokemonId = pokemon.pokemon.is_shiny
    ? (pokemon.pokemon.id - 1000) + '_S'
    : pokemon.pokemon.id;
  return `/images/pokemon-gifs/${pokemonId}.gif`;
});

const getStars = computed(() => {
  if (pokemon.star !== undefined) {
    return pokemon.star;
  }
  return getRarityStars(pokemon.pokemon.rarity);
});

const rarityBorderClass = computed(() => {
  switch (pokemon.pokemon.rarity) {
    case 'normal': return 'border-base-300';
    case 'rare': return 'border-blue-400';
    case 'epic': return 'border-purple-500';
    case 'legendary': return 'border-yellow-500';
    default: return 'border-base-300';
  }
});
</script>

<template>
  <div
    class="relative w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 lg:w-14 lg:h-14 xl:w-16 xl:h-16 2xl:w-20 2xl:h-20
           bg-base-200/60 backdrop-blur-sm rounded-md flex items-center justify-center p-0.5
           transition-all duration-300 group overflow-hidden"
    :class="rarityBorderClass"
    style="border-width: 1px;"
  >
    <img
      :src="imageUrl"
      :alt="pokemon.pokemon.name"
      class="w-full h-full object-contain"
      style="image-rendering: pixelated;"
    />

    <div class="absolute top-1 left-1">
      <StarsBadge :stars="getStars" size="xxs" />
    </div>

    <div v-if="pokemon.pokemon.is_shiny" class="absolute top-1 right-1 w-4 h-4 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
      <span class="text-yellow-400 text-[10px]">✨</span>
    </div>

    <div class="absolute bottom-0 left-0 right-0 text-center px-0.5">
      <p class="text-white text-[6px] sm:text-[7px] md:text-[8px] font-semibold truncate bg-black/30 rounded-sm px-0.5 py-0">
        {{ pokemon.pokemon.name }}
      </p>
    </div>
  </div>
</template>

<style scoped>
</style>
