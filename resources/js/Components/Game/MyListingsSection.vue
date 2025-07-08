<template>
  <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden max-h-[500px]">
    <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
      <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
        <span class="text-lg">🏪</span>
        MES ANNONCES
      </h3>
    </div>

    <div class="flex-1 overflow-y-auto p-3">
      <div v-if="listings.length === 0" class="text-center py-8">
        <p class="text-2xl mb-2">📭</p>
        <p class="text-sm mb-1">Aucune annonce</p>
        <p class="opacity-70 text-xs">Créez votre première annonce !</p>
      </div>

      <div v-else class="space-y-2">
        <div
          v-for="listing in listings"
          :key="listing.id"
          class="bg-base-200/30 backdrop-blur-sm rounded-lg p-3 border border-base-300/20 hover:border-primary/40 transition-all duration-200 cursor-pointer group"
          @click="$emit('showDetails', listing.pokemon)"
        >
          <div class="flex items-center gap-3">
            <div class="relative">
              <img
                :src="`/images/pokemon-gifs/${listing.pokemon.is_shiny ? (listing.pokemon.id - 1000) + '_S' : listing.pokemon.id}.gif`"
                :alt="listing.pokemon.name"
                class="w-12 h-12 object-contain group-hover:scale-110 transition-transform duration-200"
                style="image-rendering: pixelated;"
              />
              <!-- Shiny indicator -->
              <div v-if="listing.pokemon.is_shiny" class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
                <span class="text-yellow-400 text-xs">✨</span>
              </div>
              <!-- Rarity stars -->
              <div v-if="listing.pokemon.rarity" class="absolute -top-1 -left-1 w-auto h-4 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30 px-1">
                <span class="text-yellow-400 text-xs font-bold">{{ getRarityStars(listing.pokemon.rarity) }}⭐</span>
              </div>
            </div>

            <div class="flex-1 min-w-0">
              <h4 class="font-bold text-sm text-base-content truncate">{{ listing.pokemon.name }}</h4>
              <p class="text-xs text-base-content/70">Niv. {{ listing.pokemon.level }}</p>
              <!-- Rarity text with color -->
              <p v-if="listing.pokemon.rarity" class="text-xs font-medium uppercase" :class="getRarityColor(listing.pokemon.rarity)">
                {{ listing.pokemon.rarity }}
              </p>
              <div class="text-xs font-bold text-warning">{{ formatPrice(listing.price) }}</div>
            </div>

            <Button
              v-if="showCancelButton"
              @click.stop="$emit('cancelListing', listing)"
              variant="ghost"
              size="sm"
              class="!text-error hover:!bg-error/5"
            >
              🗑️
            </Button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="listings.length > 0" class="bg-gradient-to-r from-warning/10 to-warning/5 px-3 py-2 border-t border-warning/20">
      <div class="text-xs text-center text-base-content/70">
        {{ listings.length }} annonce{{ listings.length > 1 ? 's' : '' }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Button from '@/Components/UI/Button.vue';

interface Pokemon {
  id: number;
  name: string;
  level: number;
  is_shiny: boolean;
  rarity: string;
}

interface Listing {
  id: number;
  price: number;
  pokemon: Pokemon;
  seller: {
    id: number;
    username: string;
  };
}

interface Props {
  listings: Listing[];
  showCancelButton?: boolean;
}

withDefaults(defineProps<Props>(), {
  showCancelButton: true
});

defineEmits<{
  showDetails: [pokemon: Pokemon];
  cancelListing: [listing: Listing];
}>();

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('fr-FR').format(price) + ' ₽';
};

const getRarityStars = (rarity: string) => {
  switch(rarity) {
    case 'normal': return 1;
    case 'rare': return 2;
    case 'epic': return 3;
    case 'legendary': return 4;
    default: return 1;
  }
};

const getRarityColor = (rarity: string) => {
  switch(rarity) {
    case 'normal': return 'text-base-content/70';
    case 'rare': return 'text-blue-400';
    case 'epic': return 'text-purple-400';
    case 'legendary': return 'text-orange-400';
    default: return 'text-base-content/70';
  }
};
</script>
