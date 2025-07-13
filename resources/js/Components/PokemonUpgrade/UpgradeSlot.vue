<script setup lang="ts">
import StarsBadge from '@/Components/UI/StarsBadge.vue';
import type { PokedexEntry } from '@/types/user';

interface UpgradeRequirement {
  star: number;
  pokemon_id: number | null;
  quantity: number;
  description: string;
}

interface Props {
  requirement: UpgradeRequirement;
  selectedPokemon?: PokedexEntry;
  pokemonToUpgrade: PokedexEntry;
}

const { requirement, selectedPokemon, pokemonToUpgrade } = defineProps<Props>();

const emit = defineEmits<{
  click: [];
  remove: [pokemon: PokedexEntry];
}>();

const getPlaceholderImage = () => {
  if (requirement.pokemon_id) {
    const isShiny = pokemonToUpgrade.pokemon?.is_shiny;
    return `/images/pokemon-gifs/${isShiny ? (requirement.pokemon_id - 1000) + '_S' : requirement.pokemon_id}.gif`;
  }
  return '/images/pokemon-gifs/unknown.gif';
};

const getPlaceholderName = () => {
  if (requirement.pokemon_id) {
    return pokemonToUpgrade.pokemon?.name || 'Pokémon';
  }
  return 'N\'importe lequel';
};
</script>

<template>
  <div class="w-16 sm:w-18 md:w-20 h-20 sm:h-24 md:h-28 flex flex-col">
    <div class="flex justify-center mb-0.5 sm:mb-1">
      <StarsBadge :stars="requirement.star" size="xs" />
    </div>
    
    <div
      class="relative w-16 sm:w-18 md:w-20 h-14 sm:h-16 md:h-20 rounded-lg sm:rounded-xl border-2 border-dashed border-base-300 hover:border-primary/50 cursor-pointer transition-all duration-300 flex items-center justify-center group"
      :class="{
        'bg-base-100/60 backdrop-blur-sm': !selectedPokemon,
        'bg-gradient-to-br from-primary/10 to-secondary/10 border-primary/50': selectedPokemon
      }"
      @click="emit('click')"
    >
      <div v-if="selectedPokemon" class="relative">
        <img
          :src="`/images/pokemon-gifs/${selectedPokemon.pokemon?.is_shiny ? (selectedPokemon.pokemon.id - 1000) + '_S' : selectedPokemon.pokemon?.id}.gif`"
          :alt="selectedPokemon.pokemon?.name"
          class="w-10 sm:w-12 md:w-14 h-10 sm:h-12 md:h-14 object-contain"
          style="image-rendering: pixelated;"
        />

        <div v-if="selectedPokemon.pokemon?.is_shiny" class="absolute -top-0.5 sm:-top-1 -left-0.5 sm:-left-1">
          <span class="text-yellow-400 text-xs">✨</span>
        </div>
      </div>

      <div v-else class="text-center">
        <div class="relative mb-0.5 sm:mb-1">
          <img
            v-if="requirement.pokemon_id"
            :src="getPlaceholderImage()"
            :alt="getPlaceholderName()"
            class="w-8 sm:w-10 md:w-12 h-8 sm:h-10 md:h-12 object-contain opacity-30 group-hover:opacity-50 transition-opacity"
            style="image-rendering: pixelated;"
          />
          <div v-else class="w-8 sm:w-10 md:w-12 h-8 sm:h-10 md:h-12 flex items-center justify-center">
            <span class="text-2xl sm:text-3xl opacity-30 group-hover:opacity-50 transition-opacity">?</span>
          </div>
          
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-4 sm:w-5 md:w-6 h-4 sm:h-5 md:h-6 bg-primary/20 rounded-full flex items-center justify-center group-hover:bg-primary/30 transition-colors">
              <span class="text-primary text-sm sm:text-base md:text-lg font-bold">+</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="text-xs text-center mt-0.5 sm:mt-1 text-base-content/70">
      <div v-if="selectedPokemon" class="font-medium truncate px-0.5">
        {{ selectedPokemon.nickname || selectedPokemon.pokemon?.name }}
      </div>
      <div v-else class="font-medium truncate px-0.5">
        {{ requirement.pokemon_id ? getPlaceholderName() : 'Tout Pokémon' }}
      </div>
      <div class="text-[10px] sm:text-xs opacity-70">{{ requirement.star }}★</div>
    </div>
  </div>
</template>