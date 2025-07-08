<script setup lang="ts">
import { computed } from 'vue';
import type { MarketplaceListing } from '@/types/marketplace';
import { formatPrice, parseTypes, getPokemonImageUrl, normalizePokemonData } from '@/utils/marketplace';
import Modal from '@/Components/UI/Modal.vue';
import Button from '@/Components/UI/Button.vue';
import PokemonTypeBadge from '@/Components/UI/PokemonTypeBadge.vue';
import RarityBadge from '@/Components/UI/RarityBadge.vue';
import StarsBadge from '@/Components/UI/StarsBadge.vue';

interface Props {
  show: boolean;
  listing?: MarketplaceListing | null;
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

const normalizedPokemon = computed(() => {
  return props.listing ? normalizePokemonData(props.listing) : null;
});

const canAfford = computed(() => {
  return props.listing ? props.userCash >= props.listing.price : false;
});

const remainingCash = computed(() => {
  return props.listing ? props.userCash - props.listing.price : props.userCash;
});

const getStars = () => {
  if (!props.listing) return 1;
  
  if (props.listing.pokemon.star !== undefined) {
    return props.listing.pokemon.star;
  }
  const pokemon = normalizedPokemon.value;
  if (!pokemon) return 1;
  
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
  <Modal :show="show" @close="emit('close')" max-width="md">
    <template #header>
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 bg-gradient-to-br from-warning/20 to-warning/40 rounded-lg flex items-center justify-center">
          <span class="text-lg">💰</span>
        </div>
        <div>
          <h3 class="text-xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
            Confirmer l'achat
          </h3>
          <p v-if="normalizedPokemon" class="text-sm text-base-content/70 mt-1">
            {{ normalizedPokemon.name }} - Niveau {{ normalizedPokemon.level }}
          </p>
        </div>
      </div>
    </template>

    <template #default>
      <div v-if="listing && normalizedPokemon" class="space-y-6">
        <div class="bg-base-200/30 rounded-xl p-6 border border-base-300/20">
          <div class="flex flex-col items-center text-center mb-4">
            <div class="relative mb-4">
              <img
                :src="getPokemonImageUrl(normalizedPokemon)"
                :alt="normalizedPokemon.name"
                class="w-32 h-32 object-contain"
                style="image-rendering: pixelated;"
              />
              <div v-if="normalizedPokemon.is_shiny" class="absolute -top-2 -right-2">
                <div class="w-8 h-8 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
                  <span class="text-yellow-400 text-lg">✨</span>
                </div>
              </div>
              <div class="absolute -top-2 -left-2">
                <StarsBadge :stars="getStars()" size="md" />
              </div>
            </div>

            <div class="space-y-3">
              <div class="flex items-center justify-center gap-2">
                <h4 class="text-2xl font-bold text-base-content">{{ normalizedPokemon.name }}</h4>
              </div>
              
              <p class="text-lg text-base-content/70">Niveau {{ normalizedPokemon.level }}</p>
              
              <div v-if="normalizedPokemon.rarity">
                <RarityBadge :rarity="normalizedPokemon.rarity" size="md" />
              </div>

              <div class="flex justify-center gap-2 flex-wrap">
                <PokemonTypeBadge
                  v-for="(type, index) in parseTypes(normalizedPokemon.types)"
                  :key="index"
                  :type="type"
                  size="sm"
                />
              </div>

              <div class="bg-warning/10 rounded-lg p-3 border border-warning/20">
                <div class="text-3xl font-bold text-warning">{{ formatPrice(listing.price) }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-base-100/40 rounded-xl p-4 space-y-3">
          <h5 class="font-semibold text-base-content/80 text-sm">📋 Détails de la transaction</h5>
          
          <div class="space-y-2 text-sm">
            <div class="flex justify-between items-center">
              <span class="text-base-content/70">Vendeur :</span>
              <span class="font-medium">{{ listing.seller.username }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-base-content/70">Prix :</span>
              <span class="font-bold text-warning">{{ formatPrice(listing.price) }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-base-content/70">Votre solde :</span>
              <span class="font-medium">{{ formatPrice(userCash) }}</span>
            </div>
            <div class="border-t border-base-300/30 pt-2">
              <div class="flex justify-between items-center">
                <span class="text-base-content/70 font-medium">Après achat :</span>
                <span class="font-bold text-lg" :class="canAfford ? 'text-success' : 'text-error'">
                  {{ formatPrice(remainingCash) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div v-if="!canAfford" class="bg-error/10 border border-error/20 rounded-xl p-3 text-center">
          <div class="text-error text-sm font-medium">
            ⚠️ Fonds insuffisants pour cet achat
          </div>
        </div>

        <div class="flex gap-3 pt-2">
          <Button
            @click="emit('close')"
            variant="outline"
            size="lg"
            class="flex-1"
            :disabled="loading"
          >
            ❌ Annuler
          </Button>

          <Button
            @click="emit('confirm')"
            variant="secondary"
            size="lg"
            class="flex-1"
            :disabled="!canAfford || loading"
          >
            {{ loading ? '🔄 En cours...' : '💰 Confirmer l\'achat' }}
          </Button>
        </div>
      </div>
    </template>
  </Modal>
</template>

