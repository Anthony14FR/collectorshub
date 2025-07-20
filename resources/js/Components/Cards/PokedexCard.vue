<script setup lang="ts">
import type { Pokedex } from '@/types/pokedex';
import { getRarityColor } from '@/utils/pokemon';
import { Heart, Sparkles, Star } from 'lucide-vue-next';

interface Props {
  entry: Pokedex;
}

const { entry } = defineProps<Props>();
</script>

<template>
  <div class="bg-base-100/80 backdrop-blur-sm border-2 border-primary/20 rounded-xl p-4 hover:border-primary/40 hover:shadow-xl transition-all duration-300 group">
    <div class="flex items-center gap-3">
      <div class="relative">
        <div class="w-16 h-16 bg-gradient-to-br from-base-200/50 to-base-300/30 rounded-lg flex items-center justify-center overflow-hidden">
          <img
            :src="`/images/pokemon-gifs/${entry.pokemon?.is_shiny ? (entry.pokemon.id - 1000) + '_S' : entry.pokemon?.id}.gif`"
            :alt="entry.nickname || entry.pokemon?.name"
            class="w-14 h-14 group-hover:scale-110 transition-transform duration-300"
            style="image-rendering: pixelated;"
          />
        </div>
        <div class="absolute -top-1 -right-1 flex gap-0.5">
          <Sparkles v-if="entry.pokemon?.is_shiny" :size="12" class="text-yellow-400" />
          <Heart v-if="entry.is_favorite" :size="12" class="text-pink-400" />
          <Star v-if="entry.is_in_team" :size="12" class="text-blue-400" />
        </div>
      </div>

      <div class="flex-1">
        <h4 class="font-bold text-sm mb-1">
          {{ entry.nickname || entry.pokemon?.name }}
        </h4>
        <div class="flex items-center gap-2 text-xs opacity-70">
          <span>Niv. {{ entry.level }}</span>
          <span>•</span>
          <span>{{ entry.star }}<Star :size="10" class="inline" /></span>
        </div>
      </div>

      <div>
        <div
          class="text-xs px-3 py-1 rounded-md bg-gradient-to-r"
          :class="getRarityColor(entry.pokemon?.rarity || 'normal')"
        >
          {{ entry.pokemon?.rarity || 'normal' }}
        </div>
      </div>
    </div>
  </div>
</template>