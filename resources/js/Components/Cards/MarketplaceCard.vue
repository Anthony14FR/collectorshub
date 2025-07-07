<script setup lang="ts">
import type { Marketplace } from '@/types/marketplace';
import Button from '@/Components/UI/Button.vue';
import Badge from '@/Components/UI/Badge.vue';
import PokemonTypeBadge from '@/Components/UI/PokemonTypeBadge.vue';
import RarityBadge from '@/Components/UI/RarityBadge.vue';

interface Props {
    item: Marketplace;
    variant?: 'buy' | 'sell';
}

const { item, variant = 'buy' } = defineProps<Props>();

const emit = defineEmits<{
    buy: [id: number];
    cancel: [id: number];
}>();

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('fr-FR').format(price);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const handleBuy = () => {
    emit('buy', item.id);
};

const handleCancel = () => {
    emit('cancel', item.id);
};

// Méthodes utilitaires simplifiées avec les nouveaux composants
</script>

<template>
    <div class="group bg-gradient-to-br from-base-100/90 to-base-200/70 backdrop-blur-sm rounded-2xl p-6 border-2 border-base-300/30 hover:border-primary/50 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
        <!-- Header avec rareté -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
                    <span class="text-sm">{{ variant === 'buy' ? '🛒' : '💼' }}</span>
                </div>
                <h3 class="font-bold text-base truncate">
                    {{ item.nickname || item.pokemon?.pokemon?.name }}
                </h3>
            </div>

            <!-- Rareté moderne -->
            <div class="flex items-center gap-2">
                <RarityBadge
                    :rarity="item.pokemon?.pokemon?.rarity || 'normal'"
                    size="sm"
                    variant="filled"
                    :show-text="false"
                />
                <Badge v-if="item.pokemon?.pokemon?.is_shiny" variant="warning" size="sm">
                    ✨ Shiny
                </Badge>
            </div>
        </div>

        <!-- Pokémon et infos -->
        <div class="flex items-center gap-4 mb-4">
            <!-- Image du Pokémon -->
            <div class="w-16 h-16 bg-gradient-to-br from-base-300/50 to-base-300/30 rounded-xl flex items-center justify-center border border-base-300/50">
                <img
                    :src="`/images/pokemon-gifs/${item.pokemon?.pokemon?.is_shiny ? (item.pokemon.pokemon.id - 1000) + '_S' : item.pokemon?.pokemon?.id}.gif`"
                    :alt="item.pokemon?.pokemon?.name"
                    class="w-12 h-12 group-hover:scale-110 transition-transform duration-300"
                    style="image-rendering: pixelated;"
                />
            </div>

            <!-- Infos de base -->
            <div class="flex-1">
                <div class="flex items-center gap-2 text-sm text-base-content/70 mb-2">
                    <span>N°{{ item.pokemon?.pokemon?.id }}</span>
                    <span>•</span>
                    <span>Niv. {{ item.pokemon?.level }}</span>
                </div>

                <!-- Types -->
                <div class="flex gap-1.5">
                    <PokemonTypeBadge
                        v-for="type in (item.pokemon?.pokemon?.types || []).slice(0, 2)"
                        :key="type.name"
                        :type="type.name"
                        size="xs"
                        variant="filled"
                        :show-text="false"
                    />
                </div>
            </div>
        </div>

        <!-- Stats compactes -->
        <div class="grid grid-cols-4 gap-2 mb-4">
            <div class="text-center">
                <div class="text-xs text-red-500">❤️</div>
                <div class="text-sm font-semibold">{{ item.pokemon?.pokemon?.hp || 0 }}</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-orange-500">⚔️</div>
                <div class="text-sm font-semibold">{{ item.pokemon?.pokemon?.attack || 0 }}</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-blue-500">🛡️</div>
                <div class="text-sm font-semibold">{{ item.pokemon?.pokemon?.defense || 0 }}</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-green-500">💨</div>
                <div class="text-sm font-semibold">{{ item.pokemon?.pokemon?.speed || 0 }}</div>
            </div>
        </div>

        <!-- Prix -->
        <div class="text-center mb-4">
            <div class="text-2xl font-black bg-gradient-to-r from-warning to-secondary bg-clip-text text-transparent">
                {{ formatPrice(item.price) }} 💰
            </div>
        </div>

        <!-- Action -->
        <Button
            v-if="variant === 'buy'"
            @click="handleBuy"
            variant="primary"
            size="md"
            class="w-full"
        >
            <span class="mr-2">🛒</span>
            Acheter
        </Button>

        <Button
            v-if="variant === 'sell'"
            @click="handleCancel"
            variant="outline"
            size="md"
            class="w-full"
        >
            <span class="mr-2">❌</span>
            Annuler
        </Button>

        <!-- Info vendeur/date -->
        <div class="text-center mt-3">
            <div class="text-xs text-base-content/50">
                <template v-if="variant === 'buy'">
                    Par {{ item.seller?.name || 'Dresseur' }}
                </template>
                <template v-else>
                    {{ formatDate(item.created_at) }}
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>
