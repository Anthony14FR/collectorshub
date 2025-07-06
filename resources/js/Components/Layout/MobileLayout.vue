<script setup lang="ts">
import LevelDisplay from '@/Components/Profile/LevelDisplay.vue';
import GameInventory from '@/Components/Game/GameInventory.vue';
import UserMenu from '@/Components/Profile/UserMenu.vue';
import TrainerProfile from '@/Components/Profile/TrainerProfile.vue';
import MarketplaceSection from '@/Components/Game/MarketplaceSection.vue';
import Button from '@/Components/UI/Button.vue';

import type { User } from '@/types/user';
import type { Inventory } from '@/types/inventory';
import type { Marketplace } from '@/types/marketplace';
import type { Pokedex } from '@/types/pokedex';

interface Props {
    user: User;
    inventory: Inventory[];
    marketplace: Marketplace[];
    pokedex: Pokedex[];
    onOpenPokedexModal: () => void;
}

const { user, inventory, marketplace, pokedex, onOpenPokedexModal } = defineProps<Props>();
</script>

<template>
    <div class="lg:hidden h-full flex flex-col p-4 overflow-hidden">
        <!-- Header avec niveau - VERSION RESPONSIVE -->
        <div class="shrink-0 mb-6">
            <LevelDisplay :user="user" :responsive="true" />
        </div>

        <!-- Boutons principaux -->
        <div class="shrink-0 grid grid-cols-2 gap-4 mb-6">
            <GameInventory :inventory="inventory" />
            <UserMenu :user="user" />
        </div>

        <!-- Avatar centré -->
        <div class="shrink-0 flex justify-center mb-6">
            <div class="scale-75">
                <TrainerProfile :user="user" :trainer-image-id="2" />
            </div>
        </div>

        <!-- Sections simplifiées -->
        <div class="flex-1 grid grid-cols-1 gap-4 min-h-0 overflow-hidden">
            <!-- Marketplace -->
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden max-h-60">
                <MarketplaceSection :marketplace="marketplace" />
            </div>
            
            <!-- Pokédex simplifié en mobile - JUSTE UN BOUTON -->
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 p-4 flex flex-col items-center justify-center text-center space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-secondary/20 to-accent/20 rounded-lg flex items-center justify-center">
                        <span class="text-lg">📚</span>
                    </div>
                    <h3 class="text-lg font-bold bg-gradient-to-r from-secondary to-accent bg-clip-text text-transparent">
                        Mon Pokédex
                    </h3>
                </div>
                
                <p class="text-sm text-base-content/70">
                    {{ pokedex.length }} Pokémon capturés
                </p>
                
                <Button 
                    variant="secondary" 
                    icon="collection" 
                    size="md"
                    @click="onOpenPokedexModal"
                    class="w-full max-w-xs"
                >
                    Voir tous mes Pokémon
                </Button>
            </div>
        </div>
    </div>
</template> 