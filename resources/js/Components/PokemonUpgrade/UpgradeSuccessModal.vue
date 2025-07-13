<script setup lang="ts">
import { ref, watch } from 'vue';
import Modal from '@/Components/UI/Modal.vue';
import Button from '@/Components/UI/Button.vue';
import StarsBadge from '@/Components/UI/StarsBadge.vue';
import type { PokedexEntry } from '@/types/user';

interface Props {
  pokemon: PokedexEntry;
  show: boolean;
}

const { pokemon, show } = defineProps<Props>();

const emit = defineEmits<{
  close: [];
}>();

const showAnimation = ref(false);

watch(() => show, (newShow) => {
  if (newShow) {
    showAnimation.value = false;
    
    setTimeout(() => {
      showAnimation.value = true;
    }, 100);
  } else {
    showAnimation.value = false;
  }
});

const closeModal = () => {
  emit('close');
};
</script>

<template>
  <Modal :show="show" @close="closeModal" max-width="3xl">
    <div class="relative overflow-hidden">
      <div class="text-center py-4 sm:py-6 md:py-8 space-y-4 sm:space-y-6">
        <div class="space-y-1 sm:space-y-2">
          <h1 :class="[
            'text-xl sm:text-2xl md:text-3xl font-bold text-primary transition-all duration-500',
            showAnimation ? 'scale-100 opacity-100' : 'scale-90 opacity-0'
          ]">
            Amélioration réussie !
          </h1>
          <p :class="[
            'text-sm sm:text-base text-base-content/70 transition-all duration-500 delay-200 px-4',
            showAnimation ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'
          ]">
            Votre Pokémon a été amélioré avec succès
          </p>
        </div>

        <div :class="[
          'flex justify-center transition-all duration-500 delay-300',
          showAnimation ? 'scale-100 opacity-100' : 'scale-90 opacity-0'
        ]">
          <div class="relative">
            <div class="relative bg-base-200/50 rounded-xl p-4 sm:p-6">
              <img
                :src="`/images/pokemon-gifs/${pokemon.pokemon?.is_shiny ? (pokemon.pokemon.id - 1000) + '_S' : pokemon.pokemon?.id}.gif`"
                :alt="pokemon.pokemon?.name"
                class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 object-contain mx-auto"
                style="image-rendering: pixelated;"
              />
              
              <div v-if="pokemon.pokemon?.is_shiny" class="absolute top-1 sm:top-2 right-1 sm:right-2">
                <span class="text-yellow-400 text-base sm:text-lg">✨</span>
              </div>
            </div>

            <div class="mt-3 sm:mt-4 flex items-center justify-center gap-2 sm:gap-3">
              <div class="text-center">
                <div class="text-[10px] sm:text-xs text-base-content/70 mb-1">Avant</div>
                <StarsBadge :stars="Math.max(0, pokemon.star - 1)" />
              </div>
              <span class="text-primary font-bold text-lg sm:text-xl">→</span>
              <div class="text-center">
                <div class="text-[10px] sm:text-xs text-base-content/70 mb-1">Après</div>
                <StarsBadge :stars="pokemon.star" />
              </div>
            </div>
          </div>
        </div>

        <div :class="[
          'space-y-3 sm:space-y-4 transition-all duration-500 delay-500 px-2 sm:px-4',
          showAnimation ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'
        ]">
          <div class="bg-base-200/50 rounded-xl p-4 sm:p-6">
            <h3 class="text-lg sm:text-xl font-bold mb-3 sm:mb-4 text-primary">
              {{ pokemon.nickname || pokemon.pokemon?.name }}
            </h3>
            <div class="grid grid-cols-2 gap-3 sm:gap-4 text-xs sm:text-sm">
              <div class="text-center">
                <div class="text-base-content/70">Niveau</div>
                <div class="font-bold text-base sm:text-lg">{{ pokemon.level }}</div>
              </div>
              <div class="text-center">
                <div class="text-base-content/70">Étoiles</div>
                <div class="font-bold text-base sm:text-lg text-warning">{{ pokemon.star }}★</div>
              </div>
              <div class="text-center">
                <div class="text-base-content/70">Rareté</div>
                <div class="font-bold capitalize text-sm sm:text-base">{{ pokemon.pokemon?.rarity }}</div>
              </div>
              <div class="text-center">
                <div class="text-base-content/70">Type</div>
                <div class="font-bold text-sm sm:text-base">{{ pokemon.pokemon?.is_shiny ? 'Shiny' : 'Normal' }}</div>
              </div>
            </div>
          </div>

          <div class="bg-success/10 border border-success/20 rounded-xl p-3 sm:p-4">
            <p class="text-success font-medium text-sm sm:text-base">
              Félicitations ! Votre Pokémon est maintenant plus puissant !
            </p>
          </div>
        </div>

        <div :class="[
          'pt-3 sm:pt-4 transition-all duration-500 delay-700',
          showAnimation ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'
        ]">
          <Button @click="closeModal" variant="primary" size="lg" class="px-6 sm:px-8 w-full sm:w-auto">
            Continuer
          </Button>
        </div>
      </div>
    </div>
  </Modal>
</template>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.8s ease-out;
}
</style>