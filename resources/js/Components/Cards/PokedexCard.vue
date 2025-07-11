<script setup lang="ts">
import { getRarityColor } from '@/utils/pokemon';
import type { Pokedex } from '@/types/pokedex';

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
            :src="`/images/pokemon-gifs/${entry.pokemon.is_shiny ? (entry.pokemon.id - 1000) + '_S' : entry.pokemon.id}.gif`"
            :alt="entry.nickname || entry.pokemon.name"
            class="w-14 h-14 group-hover:scale-110 transition-transform duration-300"
            style="image-rendering: pixelated;"
          />
        </div>
        <div class="absolute -top-1 -right-1 flex gap-0.5">
          <span v-if="entry.pokemon.is_shiny" class="text-yellow-400 text-sm">✨</span>
          <span v-if="entry.is_favorite" class="text-pink-400 text-sm">❤️</span>
          <span v-if="entry.is_in_team" class="text-blue-400 text-sm">⭐</span>
        </div>
      </div>

      <div class="flex-1">
        <h4 class="font-bold text-sm mb-1">
          {{ entry.nickname || entry.pokemon.name }}
        </h4>
        <div class="flex items-center gap-2 text-xs opacity-70">
          <span>Niv. {{ entry.level }}</span>
          <span>•</span>
          <span>{{ entry.star }}⭐</span>
        </div>
      </div>

      <div>
        <div
          class="text-xs px-3 py-1 rounded-md bg-gradient-to-r"
          :class="getRarityColor(entry.pokemon.rarity)"
        >
          {{ entry.pokemon.rarity }}
        </div>
      </div>
    </div>
  </div>
</template>