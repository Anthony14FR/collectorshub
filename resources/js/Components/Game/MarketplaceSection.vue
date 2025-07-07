<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import MarketplaceCard from '@/Components/Cards/MarketplaceCard.vue';
import type { Marketplace } from '@/types/marketplace';

interface Props {
    marketplace?: Marketplace[];
}

const { marketplace = [] } = defineProps<Props>();

const activeSales = computed(() => marketplace?.filter((m) => m.status === 'active') || []);
</script>

<template>
    <div class="h-full flex flex-col bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden max-h-[500px]">
        <div class="shrink-0 p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">🏪</span>
                MARKETPLACE
            </h3>
        </div>

        <div class="flex-1 overflow-y-auto p-3">
            <div v-if="marketplace && marketplace.length > 0" class="space-y-2">
                <MarketplaceCard
                    v-for="item in marketplace"
                    :key="item.id"
                    :item="item"
                />
            </div>
            <div v-else class="text-center py-8">
                <p class="text-2xl mb-2">🛒</p>
                <p class="text-sm mb-1">Aucun objet en vente</p>
                <p class="opacity-70 text-xs">Revenez plus tard !</p>
            </div>
        </div>
    </div>
</template>