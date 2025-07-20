<script setup lang="ts">
import Button from '@/Components/UI/Button.vue';
import type { Pokedex } from '@/types/pokedex';

interface Props {
  pokedex?: Pokedex[];
  onOpenModal: () => void;
}

const { pokedex = [], onOpenModal } = defineProps<Props>();
</script>

<template>
  <div
    class="h-full flex flex-col rounded-xl overflow-hidden shadow-lg bg-cover bg-center relative"
    style="background-image: url('/images/background/pokedex.jpg');"
  >
    <div class="absolute top-2 right-2 bg-black/50 backdrop-blur-sm text-white text-sm font-semibold px-2.5 py-1 rounded-full border border-white/20">
      {{ pokedex.length }} Capturés
    </div>
    <div class="flex items-center p-3 bg-black/30 border-b-2 border-black/20">
      <div class="w-12 h-12 bg-white rounded-full border-4 border-black/30 flex items-center justify-center">
        <div class="w-8 h-8 bg-cyan-300 rounded-full border-2 border-cyan-500 shadow-inner" />
      </div>
    </div>
    <div class="flex-1 flex flex-col justify-between p-4 bg-black/40 ">

      <Button
        @click="onOpenModal"
        variant="secondary"
        class="mt-3"
      >
        Voir mon Pokédex
      </Button>
    </div>

    <div class="h-4 bg-black/20 border-t-2 border-black/40" />
  </div>
</template>