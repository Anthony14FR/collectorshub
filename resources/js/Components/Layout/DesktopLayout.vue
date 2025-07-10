<script setup lang="ts">
import LevelDisplay from '@/Components/Profile/LevelDisplay.vue';
import TrainerProfile from '@/Components/Profile/TrainerProfile.vue';
import SideSection from '@/Components/Layout/SideSection.vue';
import GameInventory from '@/Components/Game/GameInventory.vue';
import UserMenu from '@/Components/Profile/UserMenu.vue';
import PokedexSection from '@/Components/Game/PokedexSection.vue';
import Button from '@/Components/UI/Button.vue';

import type { User } from '@/types/user';
import type { Inventory } from '@/types/inventory';
import type { Marketplace } from '@/types/marketplace';
import type { Pokedex } from '@/types/pokedex';

interface Props {
    user: User;
    inventory: Inventory[];
    pokedex: Pokedex[];
    onOpenPokedexModal: () => void;
    onGoToMarketplace?: () => void;
    onGoToLeaderboard?: () => void;
}

const { user, inventory, pokedex, onOpenPokedexModal, onGoToMarketplace, onGoToLeaderboard } = defineProps<Props>();
</script>

<template>
    <div class="hidden lg:block h-screen w-screen overflow-hidden relative">
        <div class="flex justify-center pt-8 mb-8">
            <LevelDisplay :user="user" />
        </div>

        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-20">
            <TrainerProfile :user="user" :trainer-image-id="2" />
        </div>

        <SideSection position="left" :top="true">
            <GameInventory :inventory="inventory" :cash="user.cash" />
        </SideSection>

        <SideSection position="left" :top="false">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-full flex flex-col justify-center items-center p-6">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-warning/20 to-warning/40 rounded-full flex items-center justify-center mb-3 mx-auto">
                        <span class="text-3xl">🏪</span>
                    </div>
                    <h3 class="text-lg font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-1">
                        Marketplace
                    </h3>
                    <p class="text-xs text-base-content/70">Achetez et vendez vos Pokémon</p>
                </div>

                <Button
                    v-if="onGoToMarketplace"
                    @click="onGoToMarketplace"
                    variant="secondary"
                    size="md"
                    class="w-full mb-3"
                >
                    🏪 Accéder au marketplace
                </Button>
                
                <Button
                    v-if="onGoToLeaderboard"
                    @click="onGoToLeaderboard"
                    variant="secondary"
                    size="md"
                    class="w-full"
                >
                    🏆 Classement
                </Button>
            </div>
        </SideSection>

        <SideSection position="right" :top="true">
            <UserMenu :user="user" />
        </SideSection>

        <SideSection position="right" :top="false">
            <PokedexSection :pokedex="pokedex" :onOpenModal="onOpenPokedexModal" />
        </SideSection>
    </div>
</template>
