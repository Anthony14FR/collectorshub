<script setup lang="ts">
import { computed } from 'vue';
import { getRarityColor, getTypeColor } from '@/utils/pokemon';
import type { Marketplace } from '@/types/marketplace';

interface Props {
    item: Marketplace;
}

const { item } = defineProps<Props>();

const getRarityStars = (rarity: string) => {
  switch(rarity) {
    case 'normal': return 1;
    case 'rare': return 2;
    case 'epic': return 3;
    case 'legendary': return 4;
    default: return 1;
  }
};

const getCardBackground = computed(() => {
  const baseClass = 'bg-gradient-to-br backdrop-blur-sm border-2 rounded-xl p-3 hover:shadow-xl transition-all duration-300 group';

  // Background selon rareté
  const rarityBg = {
    normal: 'from-gray-100/80 to-gray-200/60 border-gray-300/30 hover:border-gray-400/50',
    rare: 'from-blue-100/80 to-blue-200/60 border-blue-300/30 hover:border-blue-400/50',
    epic: 'from-purple-100/80 to-purple-200/60 border-purple-300/30 hover:border-purple-400/50',
    legendary: 'from-yellow-100/80 to-amber-200/60 border-yellow-300/30 hover:border-yellow-400/50'
  };

  // Si shiny, ajouter un effet doré
  const shinyEffect = item.pokemon.is_shiny ? 'shadow-lg shadow-yellow-400/20' : '';

  const rarity = item.pokemon.rarity || 'normal';
  return `${baseClass} ${rarityBg[rarity as keyof typeof rarityBg]} ${shinyEffect}`;
});

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('fr-FR').format(price) + ' ₽';
};
</script>

<template>
    <div :class="getCardBackground">
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="w-16 h-16 bg-gradient-to-br from-base-200/50 to-base-300/30 rounded-lg flex items-center justify-center overflow-hidden">
                    <img
                        :src="`/images/pokemon-gifs/${item.pokemon.is_shiny ? (item.pokemon.id - 1000) + '_S' : item.pokemon.id}.gif`"
                        :alt="item.pokemon.name"
                        class="w-14 h-14 group-hover:scale-110 transition-transform duration-300"
                        style="image-rendering: pixelated;"
                    />
                </div>
                <!-- Shiny indicator -->
                <div v-if="item.pokemon.is_shiny" class="absolute -top-1 -right-1 w-5 h-5 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
                    <span class="text-yellow-400 text-xs">✨</span>
                </div>
                <!-- Stars in top left -->
                <div class="absolute -top-1 -left-1 w-auto h-4 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30 px-1">
                    <span class="text-yellow-400 text-xs font-bold">{{ getRarityStars(item.pokemon.rarity) }}</span>
                    <span class="text-yellow-400 text-xs">⭐</span>
                </div>
            </div>

            <div class="flex-1">
                <h4 class="font-bold text-sm mb-1">{{ item.pokemon.name }}</h4>
                <p class="text-xs text-base-content/70 mb-1">Niveau {{ item.pokemon.level || 1 }}</p>
                <div class="flex gap-1 mb-1">
                    <span
                        v-for="type in item.pokemon.types"
                        :key="type.name || type"
                        class="text-xs px-2 py-0.5 rounded-full bg-gradient-to-r"
                        :class="getTypeColor(type.name || type)"
                    >
                        {{ type.name || type }}
                    </span>
                </div>
                <!-- Vendeur en bas -->
                <div class="text-xs text-base-content/60">
                    Vendeur : {{ item.seller?.username || 'Inconnu' }}
                </div>
            </div>

            <div class="text-right">
                <div class="flex items-center gap-1 mb-1">
                    <span class="text-base font-bold text-warning">{{ formatPrice(item.price) }}</span>
                </div>
                <div v-if="item.pokemon.is_shiny" class="text-xs text-yellow-400 font-bold">
                    ✨ Shiny
                </div>
            </div>
        </div>
    </div>
</template>
