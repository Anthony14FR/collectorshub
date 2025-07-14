<script setup lang="ts">
import StarsBadge from '@/Components/UI/StarsBadge.vue';
import type { PokedexEntry } from '@/types/user';

interface Props {
  entry: PokedexEntry;
  size?: 'small' | 'medium';
  selected?: boolean;
}

const { entry, size = 'medium', selected = false } = defineProps<Props>();

const sizeClasses = {
  small: 'w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12',
  medium: 'w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16'
};

const cardSizeClasses = {
  small: 'p-1.5 sm:p-2 min-h-[75px] sm:min-h-[85px] md:min-h-[100px]',
  medium: 'p-2 sm:p-2.5 md:p-3 min-h-[90px] sm:min-h-[105px] md:min-h-[120px]'
};
</script>

<template>
  <div
    :class="[
      'bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-sm rounded-lg sm:rounded-xl border border-base-300/30 hover:border-primary/50 shadow-lg text-center transition-all duration-300 group cursor-pointer hover:scale-105 relative',
      cardSizeClasses[size],
      selected ? 'ring-2 ring-primary border-primary/70' : ''
    ]"
  >
    <div class="absolute top-0.5 sm:top-1 right-0.5 sm:right-1 flex flex-col gap-0.5 sm:gap-1">
      <div v-if="entry.pokemon?.is_shiny" class="w-3 h-3 sm:w-4 sm:h-4 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
        <span class="text-yellow-400 text-[8px] sm:text-xs">✨</span>
      </div>
      <div v-if="entry.is_favorite" class="w-3 h-3 sm:w-4 sm:h-4 bg-red-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-red-500/30">
        <span class="text-red-400 text-[8px] sm:text-xs">❤️</span>
      </div>
    </div>

    <div class="absolute top-0.5 sm:top-1 left-0.5 sm:left-1">
      <StarsBadge :stars="entry.star" size="xs" />
    </div>

    <div class="flex justify-center items-center mb-1 sm:mb-2 mt-4 sm:mt-5 md:mt-6">
      <img
        :src="`/images/pokemon-gifs/${entry.pokemon?.is_shiny ? (entry.pokemon.id - 1000) + '_S' : entry.pokemon?.id}.gif`"
        :alt="entry.pokemon?.name"
        :class="[sizeClasses[size], 'object-contain group-hover:scale-110 transition-transform duration-300']"
        style="image-rendering: pixelated;"
      />
    </div>

    <div class="space-y-0.5 sm:space-y-1">
      <h4 class="font-bold text-[10px] sm:text-xs truncate px-1">
        {{ entry.nickname || entry.pokemon?.name }}
      </h4>
      <p class="opacity-70 text-[9px] sm:text-xs">
        Niv. {{ entry.level }}
      </p>
    </div>
  </div>
</template>