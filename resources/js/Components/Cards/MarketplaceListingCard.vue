
<script setup lang="ts">
import type { MarketplaceListing, NormalizedPokemon } from '@/types/marketplace';
import { normalizePokemonData, formatPrice, parseTypes, getPokemonImageUrl } from '@/utils/marketplace';
import Button from '@/Components/UI/Button.vue';
import RarityBadge from '@/Components/UI/RarityBadge.vue';
import StarsBadge from '@/Components/UI/StarsBadge.vue';
import PokemonTypeBadge from '@/Components/UI/PokemonTypeBadge.vue';
import CPBadge from '@/Components/UI/CPBadge.vue';

interface Props {
  listing: MarketplaceListing;
  userCash: number;
  loading: boolean;
}

const props = defineProps<Props>();

defineEmits<{
  purchase: [listing: MarketplaceListing];
}>();

const pokemon: NormalizedPokemon = normalizePokemonData(props.listing);

const getStars = () => {
  if (props.listing.pokemon.star !== undefined) {
    return props.listing.pokemon.star;
  }
  switch(pokemon.rarity) {
  case 'normal': return 1;
  case 'rare': return 2;
  case 'epic': return 3;
  case 'legendary': return 4;
  default: return 1;
  }
};
</script> 

<template>
  <div class="flex flex-col h-full">
    <div class="flex flex-col items-center gap-4 mb-3">
      <div class="flex w-full justify-between items-center">
        <CPBadge :cp="listing.pokemon.cp" size="xs" :show-label="false" />
        <div class="flex items-center gap-2">
          <StarsBadge :stars="getStars()" size="sm" />
          <div v-if="pokemon.is_shiny" class="w-5 h-5 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
            <span class="text-yellow-400 text-xs">✨</span>
          </div>
        </div>
      </div>
      <div class="relative flex-shrink-0 flex items-center">
        <img
          :src="getPokemonImageUrl(pokemon)"
          :alt="pokemon.name"
          class="w-32 h-32 object-contain group-hover:scale-110 transition-transform duration-300"
          style="image-rendering: pixelated;"
        />
      </div>

      <div class="flex-1 w-full flex flex-col">
        <div class="flex justify-between">
          <div>
            <div class="flex items-start gap-1">
              <h4 class="font-bold text-base text-base-content flex items-center gap-1 mt-2">{{ pokemon.name }} <RarityBadge :rarity="pokemon.rarity" size="xs" /></h4>
            </div>
          </div>
        </div>

        <div class="mb-2">
          <div class="text-lg font-bold text-warning">{{ formatPrice(listing.price) }}</div>
        </div>

        <div class="flex flex-wrap gap-1">
          <PokemonTypeBadge
            v-for="(type, index) in parseTypes(pokemon.types)"
            :key="index"
            :type="type"
            size="xxs"
          />
        </div>
      </div>
    </div>

    <div class="flex items-center justify-between mt-auto pt-2 border-t border-base-300/20">
      <div class="text-xs text-base-content/70">
        <span>Vendeur : {{ listing.seller.username }}</span>
      </div>

      <div>
        <Button
          @click="$emit('purchase', listing)"
          variant="secondary"
          size="sm"
          :disabled="loading || userCash < listing.price"
          :class="userCash < listing.price ? 'opacity-50' : ''"
        >
          💰 Acheter
        </Button>
      </div>
    </div>
  </div>
</template>
