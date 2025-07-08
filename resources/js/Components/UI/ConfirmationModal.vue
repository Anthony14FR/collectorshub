<script setup lang="ts">
import { computed } from 'vue';
import Modal from '@/Components/UI/Modal.vue';
import Button from '@/Components/UI/Button.vue';
import PokemonTypeBadge from '@/Components/UI/PokemonTypeBadge.vue';
import RarityBadge from '@/Components/UI/RarityBadge.vue';

interface Pokemon {
  id: number;
  name: string;
  level: number;
  hp: number;
  attack: number;
  defense: number;
  speed: number;
  is_shiny: boolean;
  rarity: string;
  types: string | any[];
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
  show: boolean;
  listing?: Listing | null;
  userCash: number;
  loading?: boolean;
}

interface Emits {
  (e: 'close'): void;
  (e: 'confirm'): void;
}

const props = withDefaults(defineProps<Props>(), {
  loading: false
});

const emit = defineEmits<Emits>();

const getTypes = (types: string | any[]) => {
  if (!types) return [];
  if (typeof types === 'string') {
    try {
      return JSON.parse(types);
    } catch (e) {
      return [];
    }
  }
  if (Array.isArray(types)) {
    return types.map(type => typeof type === 'object' && type.name ? type.name : type);
  }
  return [];
};

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('fr-FR').format(price) + ' ₽';
};

const canAfford = computed(() => {
  return props.listing ? props.userCash >= props.listing.price : false;
});

const remainingCash = computed(() => {
  return props.listing ? props.userCash - props.listing.price : props.userCash;
});

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

<template>
  <Modal :show="show" @close="emit('close')" max-width="sm">
    <template #header>
      <div class="flex items-center gap-2">
        <div class="w-6 h-6 bg-gradient-to-br from-warning/20 to-warning/40 rounded-lg flex items-center justify-center">
          <span class="text-sm">💰</span>
        </div>
        <div>
          <h3 class="text-lg font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
            Confirmer l'achat
          </h3>
        </div>
      </div>
    </template>

    <template #default>
      <div v-if="listing" class="space-y-3">
        <!-- Pokemon Info -->
        <div class="flex items-center gap-3 p-3 bg-base-200/30 rounded-lg border border-base-300/20">
          <div class="relative">
            <img
              :src="`/images/pokemon-gifs/${listing.pokemon.is_shiny ? (listing.pokemon.id - 1000) + '_S' : listing.pokemon.id}.gif`"
              :alt="listing.pokemon.name"
              class="w-14 h-14 object-contain"
              style="image-rendering: pixelated;"
            />
            <!-- Shiny indicator only -->
            <div v-if="listing.pokemon.is_shiny" class="absolute -top-1 -left-1">
              <div class="w-5 h-5 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
                <span class="text-yellow-400 text-sm">✨</span>
              </div>
            </div>
          </div>
          <div class="flex-1 min-w-0 relative">
            <!-- Rarity badge in top right of pokemon card -->
            <div v-if="listing.pokemon.rarity" class="absolute -top-2 -right-2">
              <div class="bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30 px-2 py-1">
                <span class="text-yellow-400 text-xs font-bold">{{ getRarityStars(listing.pokemon.rarity) }}⭐</span>
              </div>
            </div>
            <h4 class="font-bold text-base text-base-content">{{ listing.pokemon.name }}</h4>
            <p class="text-sm text-base-content/70">Niveau {{ listing.pokemon.level }}</p>
            <p v-if="listing.pokemon.rarity" class="text-xs font-semibold uppercase" :class="getRarityColor(listing.pokemon.rarity)">
              {{ listing.pokemon.rarity }}
            </p>
            <div class="text-sm font-bold text-warning">{{ formatPrice(listing.price) }}</div>
          </div>
        </div>

        <!-- Transaction details -->
        <div class="space-y-2 text-sm">
          <div class="flex justify-between">
            <span class="text-base-content/70">Vendeur :</span>
            <span class="font-medium">{{ listing.seller.username }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-base-content/70">Votre solde :</span>
            <span class="font-medium">{{ formatPrice(userCash) }}</span>
          </div>
          <div class="flex justify-between border-t border-base-300/30 pt-2">
            <span class="text-base-content/70">Après achat :</span>
            <span class="font-medium" :class="canAfford ? 'text-success' : 'text-error'">
              {{ formatPrice(remainingCash) }}
            </span>
          </div>
        </div>

        <!-- Warning if can't afford -->
        <div v-if="!canAfford" class="text-center text-error text-sm bg-error/10 rounded-lg p-2">
          ⚠️ Fonds insuffisants pour cet achat
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 pt-2">
          <Button
            @click="emit('close')"
            variant="outline"
            size="sm"
            class="flex-1"
            :disabled="loading"
          >
            Annuler
          </Button>

          <Button
            @click="emit('confirm')"
            variant="secondary"
            size="sm"
            class="flex-1"
            :disabled="!canAfford || loading"
          >
            {{ loading ? '🔄 En cours...' : 'Oui, acheter' }}
          </Button>
        </div>
      </div>
    </template>
  </Modal>
</template>
