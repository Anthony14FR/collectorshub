<script setup lang="ts">
import { getRarityColor, getTypeColor } from '@/utils/pokemon';
import type { Marketplace } from '@/types/marketplace';

interface Props {
    item: Marketplace;
}

const { item } = defineProps<Props>();
</script>

<template>
    <div class="bg-base-100/80 backdrop-blur-sm border-2 border-primary/20 rounded-xl p-4 hover:border-primary/40 hover:shadow-xl transition-all duration-300 group">
        <div class="flex items-center gap-3">
            <!-- Pokémon Sprite -->
            <div class="relative">
                <div class="w-16 h-16 bg-gradient-to-br from-base-200/50 to-base-300/30 rounded-lg flex items-center justify-center overflow-hidden">
                    <img 
                        :src="`/images/pokemon-gifs/${item.pokemon.id}.gif`"
                        :alt="item.pokemon.name"
                        class="w-14 h-14 group-hover:scale-110 transition-transform duration-300"
                        style="image-rendering: pixelated;"
                    />
                </div>
                <div v-if="item.pokemon.is_shiny" class="absolute -top-1 -right-1 text-yellow-400 text-sm">✨</div>
            </div>

            <!-- Infos Pokémon -->
            <div class="flex-1">
                <h4 class="font-bold text-sm mb-1">{{ item.pokemon.name }}</h4>
                <div class="flex gap-1">
                    <span 
                        v-for="type in item.pokemon.types" 
                        :key="type.name"
                        class="text-xs px-2 py-0.5 rounded-full bg-gradient-to-r" 
                        :class="getTypeColor(type.name)"
                    >
                        {{ type.name }}
                    </span>
                </div>
            </div>

            <!-- Prix et Rareté -->
            <div class="text-right">
                <div class="flex items-center gap-1 mb-1">
                    <span class="text-lg font-bold">{{ item.price.toLocaleString() }}</span>
                    <span class="text-warning">💰</span>
                </div>
                <div 
                    class="text-xs px-2 py-1 rounded-md bg-gradient-to-r text-center" 
                    :class="getRarityColor(item.pokemon.rarity)"
                >
                    {{ item.pokemon.rarity }}
                </div>
            </div>
        </div>
    </div>
</template> 