<script setup lang="ts">
import PokemonTypeBadge from '@/Components/UI/PokemonTypeBadge.vue';
import RarityBadge from '@/Components/UI/RarityBadge.vue';
import StarsBadge from '@/Components/UI/StarsBadge.vue';
import type { PokedexEntry } from '@/types/marketplace';
import type { Pokedex } from '@/types/pokedex';
import { parseTypes } from '@/utils/marketplace';
import { Heart, Sparkles, Star } from 'lucide-vue-next';

interface Props {
  entry: PokedexEntry | Pokedex;
  size?: 'small' | 'medium' | 'large' | 'xl';
  showDetails?: boolean;
  variant?: 'card' | 'modal';
}

const { entry, size = 'medium', showDetails = true, variant = 'card' } = defineProps<Props>();

const sizeClasses = {
  small: 'w-8 h-8',
  medium: 'w-12 h-12',
  large: 'w-20 h-20',
  xl: 'w-24 h-24'
};

const cardClasses = {
  card: 'bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-sm rounded-xl p-3 border border-base-300/30 hover:border-primary/50 shadow-lg',
  modal: 'bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-lg rounded-2xl p-4 border-2 border-primary/30 hover:border-primary/50 shadow-xl shadow-primary/10 hover:shadow-2xl hover:shadow-primary/20'
};

const getStars = () => {
  if (entry.star !== undefined) {
    return entry.star;
  }
  const rarity = entry.pokemon?.rarity;
  if (!rarity) return 1;

  switch (rarity) {
  case 'normal': return 1;
  case 'rare': return 2;
  case 'epic': return 3;
  case 'legendary': return 4;
  default: return 1;
  }
};

const getRarityForBadge = () => {
  const rarity = entry.pokemon?.rarity;
  if (!rarity) return undefined;

  const validRarities = ['normal', 'rare', 'epic', 'legendary'];
  return validRarities.includes(rarity) ? rarity : 'normal';
};
</script>

<template>
  <div :class="[
    cardClasses[variant],
    'text-center transition-all duration-300 group cursor-pointer hover:scale-105 relative'
  ]">
    <div v-if="entry.pokemon?.is_shiny"
         class="absolute top-2 right-2 w-6 h-6 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
      <Sparkles :size="14" class="text-yellow-400" />
    </div>

    <div v-if="entry.is_favorite" :class="[
      'absolute w-6 h-6 bg-red-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-red-500/30',
      entry.pokemon?.is_shiny ? 'top-10 right-2' : 'top-2 right-2'
    ]">
      <Heart :size="14" class="text-red-400" />
    </div>

    <div
      class="absolute top-2 left-2 w-auto h-5 bg-base-100/80 backdrop-blur-sm rounded-full flex items-center justify-center border border-base-300/30 px-2">
      <span class="text-base-content text-xs font-bold">N°{{ entry.pokemon_id }}</span>
    </div>

    <div class="absolute top-8 left-2">
      <StarsBadge :stars="getStars()" size="xs" />
    </div>

    <div v-if="getRarityForBadge()" class="absolute top-14 left-2">
      <RarityBadge :rarity="getRarityForBadge()!" size="xs" />
    </div>

    <div class="flex justify-center items-center mb-3" :class="variant === 'modal' ? 'h-24' : 'h-auto'">
      <div class="relative">
        <img
          :src="`/images/pokemon-gifs/${entry.pokemon?.is_shiny ? (entry.pokemon?.id ? entry.pokemon.id - 1000 : 0) + '_S' : entry.pokemon?.id || 0}.gif`"
          :alt="entry.pokemon?.name"
          :class="[sizeClasses[size], 'object-contain group-hover:scale-110 transition-transform duration-300']"
          style="image-rendering: pixelated;" />
      </div>
    </div>

    <template v-if="showDetails">
      <h4 class="font-bold mb-1" :class="variant === 'modal' ? 'text-sm' : 'text-xs'">
        {{ entry.nickname || entry.pokemon?.name }}
      </h4>
      <p class="opacity-70 mb-2" :class="variant === 'modal' ? 'text-sm' : 'text-xs'">
        Niv. {{ entry.level }}
      </p>

      <div v-if="entry.pokemon?.types" class="flex justify-center gap-1 mb-2">
        <PokemonTypeBadge v-for="type in parseTypes(entry.pokemon?.types || [])" :key="type" :type="type" size="xs" />
      </div>

      <div v-if="entry.is_in_team" class="flex justify-center gap-1 mb-2">
        <span v-if="entry.is_in_team"
              class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100/80 text-blue-600 border border-blue-200">
          <Star :size="12" class="mr-1" />
          Équipe
        </span>
      </div>

    </template>
  </div>
</template>
