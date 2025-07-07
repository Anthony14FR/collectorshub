<script setup lang="ts">
import type { Marketplace } from '@/types/marketplace';
import Button from '@/Components/UI/Button.vue';

interface Props {
    item: Marketplace;
}

const { item } = defineProps<Props>();

const emit = defineEmits<{
    cancel: [id: number];
}>();

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('fr-FR').format(price);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const handleCancel = () => {
    emit('cancel', item.id);
};

const getRarityIcon = (rarity: string) => {
    const icons: Record<string, string> = {
        normal: '⚪',
        rare: '🔵',
        epic: '🟣',
        legendary: '🟡'
    };
    return icons[rarity] || '⚪';
};

const getRarityStars = (rarity: string) => {
    const stars: Record<string, number> = {
        normal: 1,
        rare: 2,
        epic: 3,
        legendary: 4
    };
    return stars[rarity] || 1;
};
</script>

<template>
    <div class="bg-base-100/80 backdrop-blur-sm border border-base-300/50 rounded-xl p-4 hover:border-secondary/50 hover:bg-base-100/90 transition-all duration-200 group">
        <div class="flex items-center gap-4">
            <!-- Image Pokémon -->
            <div class="w-12 h-12 bg-gradient-to-br from-base-300/30 to-base-300/20 rounded-lg flex items-center justify-center border border-base-300/40 overflow-hidden">
                <img
                    :src="`/images/pokemon-gifs/${item.pokemon?.pokemon?.is_shiny ? (item.pokemon.pokemon.id - 1000) + '_S' : item.pokemon?.pokemon?.id}.gif`"
                    :alt="item.pokemon?.pokemon?.name"
                    class="w-10 h-10 group-hover:scale-110 transition-transform duration-200"
                    style="image-rendering: pixelated;"
                />
            </div>

            <!-- Informations Pokémon -->
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="font-bold text-base truncate">
                        {{ item.nickname || item.pokemon?.pokemon?.name }}
                    </h3>

                    <!-- Rareté compacte -->
                    <div class="flex items-center gap-1">
                        <span class="text-sm">{{ getRarityIcon(item.pokemon?.pokemon?.rarity || 'normal') }}</span>
                        <span class="text-xs text-base-content/60">
                            {{ '⭐'.repeat(getRarityStars(item.pokemon?.pokemon?.rarity || 'normal')) }}
                        </span>
                    </div>

                    <!-- Badge Shiny -->
                    <span v-if="item.pokemon?.pokemon?.is_shiny" class="text-sm">✨</span>
                </div>

                <div class="flex items-center gap-4 text-xs text-base-content/60">
                    <span>N°{{ item.pokemon?.pokemon?.id }}</span>
                    <span>Niv. {{ item.pokemon?.level }}</span>
                    <span>{{ formatDate(item.created_at) }}</span>
                </div>
            </div>

            <!-- Prix -->
            <div class="text-right">
                <div class="text-lg font-bold bg-gradient-to-r from-warning to-secondary bg-clip-text text-transparent">
                    {{ formatPrice(item.price) }}
                </div>
                <div class="text-xs text-base-content/60">pièces</div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
                <Button
                    @click="handleCancel"
                    variant="ghost"
                    size="sm"
                    class="text-error hover:text-error hover:bg-error/10 border-error/20"
                >
                    <span class="text-sm">🗑️</span>
                    <span class="hidden sm:inline ml-1">Retirer</span>
                </Button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}
</style>
