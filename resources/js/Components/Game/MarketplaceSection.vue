<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import MarketplaceCard from '@/Components/Cards/MarketplaceCard.vue';
import MyListingsSection from '@/Components/Game/MyListingsSection.vue';
import Button from '@/Components/UI/Button.vue';
import type { Marketplace } from '@/types/marketplace';

interface Props {
    marketplace?: Marketplace[];
    myListings?: any[];
}

const { marketplace = [], myListings = [] } = defineProps<Props>();

const activeSales = computed(() => marketplace?.filter((m) => m.status === 'active') || []);
const showMyListings = ref(false);
</script>

<template>
    <div class="h-full flex flex-col">
        <div class="shrink-0 p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <span class="text-lg">🏪</span>
                    MARKETPLACE
                </h3>
                <!-- Toggle button -->
                <Button
                    @click="showMyListings = !showMyListings"
                    :variant="showMyListings ? 'primary' : 'secondary'"
                    size="xs"
                    class="text-xs"
                >
                    {{ showMyListings ? '🛒 Tous' : '👤 Mes' }}
                </Button>
            </div>
        </div>

        <div class="flex-1 overflow-hidden">
            <!-- All marketplace items -->
            <div v-if="!showMyListings" class="h-full overflow-y-auto p-3">
                <div v-if="activeSales && activeSales.length > 0" class="space-y-2">
                    <MarketplaceCard
                        v-for="item in activeSales"
                        :key="item.id"
                        :item="item"
                    />
                </div>
                <div v-else class="text-center py-8">
                    <p class="text-2xl mb-2">🛒</p>
                    <p class="text-sm mb-1">Aucun Pokémon en vente</p>
                    <p class="opacity-70 text-xs">Revenez plus tard !</p>
                </div>
            </div>

            <!-- My listings -->
            <div v-else class="h-full p-3">
                <MyListingsSection
                    :listings="myListings"
                    :show-cancel-button="false"
                    class="h-full"
                />
            </div>
        </div>
    </div>
</template>
