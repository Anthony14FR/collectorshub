<script setup lang="ts">
import PokemonTypeBadge from '@/Components/UI/PokemonTypeBadge.vue';
import RarityBadge from '@/Components/UI/RarityBadge.vue';
import StarsBadge from '@/Components/UI/StarsBadge.vue';
import { Sparkles, X } from 'lucide-vue-next';

interface Pokemon {
  id: number;
  user_id: number;
  pokemon_id: number;
  level: number;
  nickname?: string;
  star?: number;
  is_favorite: boolean;
  is_in_team: boolean;
  obtained_at: string;
  pokemon: {
    id: number;
    name: string;
    is_shiny: boolean;
    rarity: string;
    types: any[];
  };
}

interface Props {
  pokemon: Pokemon;
}

const props = defineProps<Props>();
defineEmits<{
  close: [];
}>();

const getTypes = (types: any[]) => {
  if (!types) return [];
  if (typeof types === 'string') {
    try {
      return JSON.parse(types);
    } catch (e) {
      return [];
    }
  }
  if (Array.isArray(types)) {
    return types.map(type => typeof type === 'object' && type.name ? type.name : type);
  }
  return [];
};

const getStars = () => {
  if (props.pokemon.star !== undefined) {
    return props.pokemon.star;
  }
  const rarity = props.pokemon.pokemon?.rarity;
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
  <div class="bg-gradient-to-br from-success/10 to-success/5 backdrop-blur-lg rounded-2xl border-2 border-success/20 p-4 relative overflow-hidden shadow-xl">
    <button
      @click="$emit('close')"
      class="absolute top-3 right-3 w-8 h-8 bg-error/20 hover:bg-error/30 rounded-full flex items-center justify-center border border-error/30 hover:border-error/50 transition-all duration-200 z-10"
    >
      <X :size="20" class="text-error" />
    </button>

    <div class="text-center">
      <div class="relative inline-block mb-4">
        <div class="w-24 h-24 bg-gradient-to-br from-success/20 to-success/40 rounded-2xl flex items-center justify-center border-2 border-success/30 relative mx-auto">
          <img
            :src="`/images/pokemon-gifs/${pokemon.pokemon?.is_shiny ? (pokemon.pokemon.id - 1000) + '_S' : pokemon.pokemon.id}.gif`"
            :alt="pokemon.pokemon?.name"
            class="w-20 h-20 object-contain"
            style="image-rendering: pixelated;"
          />
          <div v-if="pokemon.pokemon?.is_shiny" class="absolute -top-2 -right-2 w-6 h-6 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/40">
            <Sparkles :size="12" class="text-yellow-400" />
          </div>
          <div class="absolute -top-2 -left-2">
            <StarsBadge :stars="getStars()" size="sm" />
          </div>
        </div>
      </div>

      <div class="mb-4">
        <div class="flex items-center justify-center gap-2 mb-2">
          <h4 class="text-xl font-bold bg-gradient-to-r from-success to-success/80 bg-clip-text text-transparent">
            {{ pokemon.pokemon?.name }}
          </h4>
        </div>
        <p class="text-sm text-success/70 font-medium mb-2">Niveau {{ pokemon.level }}</p>
        
        <div v-if="pokemon.pokemon?.rarity" class="mb-3 flex justify-center">
          <RarityBadge :rarity="pokemon.pokemon.rarity" size="sm" />
        </div>
        
        <div class="flex flex-wrap gap-1 justify-center">
          <PokemonTypeBadge
            v-for="type in getTypes(pokemon.pokemon?.types)"
            :key="type"
            :type="type"
            size="xs"
          />
        </div>
      </div>
    </div>

    <div class="absolute top-2 left-2 w-3 h-3 bg-success/20 rounded-full animate-pulse"></div>
    <div class="absolute bottom-2 left-3 w-2 h-2 bg-success/30 rounded-full animate-pulse delay-500"></div>
    <div class="absolute top-1/2 right-2 w-1 h-1 bg-success/40 rounded-full animate-pulse delay-1000"></div>
  </div>
</template>

