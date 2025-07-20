<script setup lang="ts">
import SimplePokemonCard from '@/Components/PokemonUpgrade/SimplePokemonCard.vue';
import Button from '@/Components/UI/Button.vue';
import type { PokedexEntry } from '@/types/user';
import { Frown, X } from 'lucide-vue-next';

interface UpgradeRequirement {
  star: number;
  pokemon_id: number | null;
  quantity: number;
  description: string;
}

interface Props {
  show: boolean;
  availablePokemons: PokedexEntry[];
  requirement: UpgradeRequirement | null;
  pokemonToUpgrade: PokedexEntry;
}

const { show, availablePokemons, requirement, pokemonToUpgrade } = defineProps<Props>();

const emit = defineEmits<{
  close: [];
  select: [pokemon: PokedexEntry];
}>();

const getModalTitle = () => {
  if (!requirement) return 'Sélectionner un Pokémon';
  
  if (requirement.pokemon_id) {
    return `Sélectionner un ${pokemonToUpgrade.pokemon?.name} ${requirement.star}★`;
  }
  
  return `Sélectionner un Pokémon ${requirement.star}★`;
};

const selectPokemon = (pokemon: PokedexEntry) => {
  emit('select', pokemon);
};
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-[70] flex items-center justify-center p-2 sm:p-4" @click.self="emit('close')">
    <div class="absolute inset-0 bg-base-100/80 backdrop-blur-md"></div>
    
    <div class="relative w-full max-w-sm sm:max-w-2xl md:max-w-4xl max-h-[75vh] sm:max-h-[80vh] md:max-h-[70vh] bg-gradient-to-br from-base-100/95 to-base-200/90 backdrop-blur-lg border-2 border-primary/20 rounded-2xl sm:rounded-3xl shadow-2xl shadow-primary/20 overflow-hidden flex flex-col">
      <div class="bg-gradient-to-r from-primary/20 to-secondary/20 border-b border-primary/20 px-4 sm:px-6 md:px-8 py-4 sm:py-6 flex-shrink-0">
        <h3 class="text-base sm:text-lg font-bold truncate pr-8">{{ getModalTitle() }}</h3>
      </div>

      <button
        @click="emit('close')"
        class="absolute top-2 sm:top-4 right-2 sm:right-4 z-20 rounded-xl bg-base-200/50 hover:bg-base-200 transition-colors duration-200 flex items-center justify-center font-bold hover:text-error w-8 h-8 sm:w-10 sm:h-10 text-xl sm:text-2xl"
      >
        <X :size="20" />
      </button>

      <div class="flex-1 overflow-y-auto p-2 sm:p-4 md:p-6">
        <div v-if="availablePokemons.length > 0" class="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-2 sm:gap-3 md:gap-4">
          <div
            v-for="pokemon in availablePokemons"
            :key="pokemon.id"
            @click="selectPokemon(pokemon)"
            class="cursor-pointer transform transition-transform hover:scale-105"
          >
            <SimplePokemonCard :entry="pokemon" size="small" />
          </div>
        </div>

        <div v-else class="text-center py-6 sm:py-8">
          <Frown :size="48" class="mb-2 text-base-content/50 mx-auto" />
          <h4 class="font-bold text-base sm:text-lg mb-2">Aucun Pokémon disponible</h4>
          <p class="text-xs sm:text-sm text-base-content/70 px-4">
            Vous n'avez pas de Pokémon correspondant aux critères requis.
          </p>
        </div>
      </div>

      <div class="border-t border-primary/20 p-3 sm:p-4 md:p-6 flex justify-end flex-shrink-0">
        <Button @click="emit('close')" variant="outline" size="sm" class="w-full sm:w-auto">
          Annuler
        </Button>
      </div>

      <div class="absolute top-4 sm:top-6 right-12 sm:right-16 w-4 sm:w-6 h-4 sm:h-6 border-2 border-success/20 rounded-full animate-pulse pointer-events-none"></div>
      <div class="absolute bottom-4 sm:bottom-6 left-4 sm:left-6 w-3 sm:w-4 h-3 sm:h-4 border-2 border-primary/30 rounded-full animate-pulse delay-500 pointer-events-none"></div>
      <div class="absolute top-8 sm:top-12 left-8 sm:left-12 w-1.5 sm:w-2 h-1.5 sm:h-2 bg-accent/40 rounded-full blur-sm animate-pulse opacity-60 delay-1000 pointer-events-none"></div>
    </div>
  </div>
</template>

<style scoped>
@media (max-width: 475px) {
  .xs\:grid-cols-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}
</style>