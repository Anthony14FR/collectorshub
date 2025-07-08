<script setup lang="ts">
import type { Pokedex } from '@/types/pokedex';
import PokemonTypeBadge from '@/Components/UI/PokemonTypeBadge.vue';
import RarityBadge from '@/Components/UI/RarityBadge.vue';

interface Props {
    entry: Pokedex;
    size?: 'small' | 'medium' | 'large' | 'xl';
    showDetails?: boolean;
    variant?: 'card' | 'modal';
}

const { entry, size = 'medium', showDetails = true, variant = 'card' } = defineProps<Props>();

const sizeClasses = {
    small: 'w-8 h-8',
    medium: 'w-12 h-12',
    large: 'w-20 h-20',
    xl: 'w-24 h-24'
};

const cardClasses = {
    card: 'bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-sm rounded-xl p-3 border border-base-300/30 hover:border-primary/50 shadow-lg',
    modal: 'bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-lg rounded-2xl p-4 border-2 border-primary/30 hover:border-primary/50 shadow-xl shadow-primary/10 hover:shadow-2xl hover:shadow-primary/20'
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
</script>

<template>
    <div
        :class="[
            cardClasses[variant],
            'text-center transition-all duration-300 group cursor-pointer hover:scale-105 relative'
        ]"
    >
        <!-- Top indicators simplified -->
        <div v-if="entry.pokemon.is_shiny" class="absolute top-2 right-2 w-6 h-6 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30">
            <span class="text-yellow-400 text-sm">✨</span>
        </div>

        <!-- Stars indicator -->
        <div v-if="entry.star || entry.pokemon.rarity" class="absolute top-2 left-2 w-auto h-5 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-yellow-500/30 px-2">
            <span class="text-yellow-400 text-xs font-bold">{{ entry.star || getRarityStars(entry.pokemon.rarity) }}</span>
            <span class="text-yellow-400 text-xs ml-1">⭐</span>
        </div>

        <div class="flex justify-center items-center mb-3" :class="variant === 'modal' ? 'h-24' : 'h-auto'">
            <div class="relative">
                <img
                    :src="`/images/pokemon-gifs/${entry.pokemon.is_shiny ? (entry.pokemon.id - 1000) + '_S' : entry.pokemon.id}.gif`"
                    :alt="entry.pokemon?.name"
                    :class="[sizeClasses[size], 'object-contain group-hover:scale-110 transition-transform duration-300']"
                    style="image-rendering: pixelated;"
                />
            </div>
        </div>

        <template v-if="showDetails">
            <h4 class="font-bold mb-1" :class="variant === 'modal' ? 'text-sm' : 'text-xs'">
                {{ entry.nickname || entry.pokemon?.name }}
            </h4>
            <p class="opacity-70 mb-2" :class="variant === 'modal' ? 'text-sm' : 'text-xs'">
                N°{{ entry.pokemon_id }} • Niv. {{ entry.level || entry.pokemon.level || 1 }}
            </p>

            <div v-if="entry.pokemon.types && entry.pokemon.types.length > 0" class="flex justify-center gap-1 mb-2">
                <PokemonTypeBadge
                    v-for="type in entry.pokemon.types"
                    :key="type.name"
                    :type="type.name"
                    :size="variant === 'modal' ? 'sm' : 'xs'"
                />
            </div>

            <!-- Status badges harmonisés -->
            <div v-if="entry.is_favorite || entry.is_in_team" class="flex justify-center gap-1 mb-2">
                <span v-if="entry.is_favorite" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-pink-100/80 text-pink-600 border border-pink-200">
                    ❤️ Favori
                </span>
                <span v-if="entry.is_in_team" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100/80 text-blue-600 border border-blue-200">
                    ⭐ Équipe
                </span>
            </div>

            <div v-if="variant === 'modal'" class="space-y-2">
                <div v-if="entry.level" class="flex justify-between items-center text-xs">
                    <span class="opacity-70">Niveau:</span>
                    <span class="font-semibold text-primary">{{ entry.level }}</span>
                </div>
            </div>
        </template>
    </div>
</template>
