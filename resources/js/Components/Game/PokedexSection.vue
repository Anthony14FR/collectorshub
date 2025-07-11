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
    <div class="absolute top-2 right-2 bg-black/50 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-full border border-white/20">
      {{ pokedex.length }} Capturés
    </div>
    <!-- Pokedex Header -->
    <div class="flex items-center p-3 bg-black/20 border-b-2 border-black/20">
      <div class="w-12 h-12 bg-white rounded-full border-4 border-black/30 flex items-center justify-center">
        <div class="w-8 h-8 bg-cyan-300 rounded-full border-2 border-cyan-500 shadow-inner" />
      </div>
      <div class="ml-4">
        <div class="w-8 h-2 bg-red-500 rounded-full mb-1" />
        <div class="w-5 h-2 bg-yellow-400 rounded-full" />
      </div>
    </div>

    <!-- Pokedex Body -->
    <div class="flex-1 flex flex-col justify-between p-4 bg-black/30 ">
      <div>
        <h3 class="text-white font-bold text-xl tracking-wider [text-shadow:0_2px_4px_rgba(0,0,0,0.5)]">
          Mon Pokédex
        </h3>
      </div>

      <Button
        @click="onOpenModal"
        variant="outline"
        class="w-full !border-white/50 !text-white hover:!bg-white/10 backdrop-blur-md"
      >
        Voir tous
      </Button>
    </div>

    <div class="h-4 bg-black/20 border-t-2 border-black/20" />
  </div>
</template>