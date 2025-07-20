<template>
  <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30">
    <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
      <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
        <Store :size="16" class="inline" />
        MES ANNONCES
      </h3>
    </div>

    <div class="flex-1 overflow-y-auto p-3 overflow-y-auto max-h-[500px]">
      <div v-if="listings.length === 0" class="text-center py-8">
        <Inbox :size="32" class="mx-auto mb-2 text-base-content/30" />
        <p class="text-sm mb-1">Aucune annonce</p>
        <p class="opacity-70 text-xs">Créez votre première annonce !</p>
      </div>

      <div v-else class="space-y-2">
        <div
          v-for="listing in listings"
          :key="listing.id"
          class="bg-base-200/30 backdrop-blur-sm rounded-lg p-3 border border-base-300/20 transition-all duration-200 group"
        >
          <div class="flex items-center gap-3">
            <div class="relative">
              <img
                :src="getPokemonImageUrl(normalizePokemonData(listing))"
                :alt="normalizePokemonData(listing).name"
                class="w-12 h-12 object-contain group-hover:scale-110 transition-transform duration-200"
                style="image-rendering: pixelated;"
              />
              <div v-if="normalizePokemonData(listing).is_shiny" class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
                <Sparkles :size="12" class="text-yellow-400" />
              </div>
              <div class="absolute -top-1 -left-1">
                <StarsBadge :stars="getStars(listing)" size="xs" />
              </div>
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-1">
                <h4 class="font-bold text-sm text-base-content truncate">{{ normalizePokemonData(listing).name }}</h4>
              </div>
              <p class="text-xs text-base-content/70 mb-1">Niv. {{ normalizePokemonData(listing).level }}</p>
              <div v-if="normalizePokemonData(listing).rarity" class="mb-1">
                <RarityBadge :rarity="normalizePokemonData(listing).rarity" size="xs" />
              </div>
              <div class="text-xs font-bold text-warning">{{ formatPrice(listing.price) }}</div>
            </div>

            <Button
              v-if="showCancelButton"
              @click.stop="$emit('cancelListing', listing)"
              variant="ghost"
              size="sm"
              class="!text-error hover:!bg-error/5"
            >
              <Trash2 :size="16" />
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
import RarityBadge from '@/Components/UI/RarityBadge.vue';
import StarsBadge from '@/Components/UI/StarsBadge.vue';
import type { MarketplaceListing } from '@/types/marketplace';
import { formatPrice, getPokemonImageUrl, normalizePokemonData } from '@/utils/marketplace';
import {
  Inbox,
  Sparkles,
  Store,
  Trash2
} from 'lucide-vue-next';

interface Props {
  listings: MarketplaceListing[];
  showCancelButton?: boolean;
}

withDefaults(defineProps<Props>(), {
  showCancelButton: true
});

defineEmits<{
  cancelListing: [listing: MarketplaceListing];
}>();

const getStars = (listing: MarketplaceListing) => {
  if (listing.pokemon.star !== undefined) {
    return listing.pokemon.star;
  }
  const pokemon = normalizePokemonData(listing);
  switch(pokemon.rarity) {
  case 'normal': return 1;
  case 'rare': return 2;
  case 'epic': return 3;
  case 'legendary': return 4;
  default: return 1;
  }
};
</script>
