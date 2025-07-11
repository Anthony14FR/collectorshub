<script setup lang="ts">
import LevelDisplay from '@/Components/Profile/LevelDisplay.vue';
import TrainerProfile from '@/Components/Profile/TrainerProfile.vue';
import SideSection from '@/Components/Layout/SideSection.vue';
import GameInventory from '@/Components/Game/GameInventory.vue';
import UserMenu from '@/Components/Profile/UserMenu.vue';
import PokedexSection from '@/Components/Game/PokedexSection.vue';
import Button from '@/Components/UI/Button.vue';
import { router } from '@inertiajs/vue3';

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

const { user, inventory, pokedex, onOpenPokedexModal, onGoToMarketplace } = defineProps<Props>();

const goToInvocation = () => {
    router.visit('/opening');
};
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
            <div class="grid grid-cols-1 gap-3 h-full">
                <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-full flex flex-col justify-center items-center p-4">
                    <div class="text-center mb-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-accent/20 to-accent/40 rounded-full flex items-center justify-center mb-2 mx-auto">
                            <span class="text-xl">⚡</span>
                        </div>
                        <h3 class="text-base font-bold bg-gradient-to-r from-accent to-accent/80 bg-clip-text text-transparent mb-1">
                            Invocation
                        </h3>
                        <p class="text-xs text-base-content/70">Invoquez de nouveaux Pokémon</p>
                    </div>

                    <Button
                        @click="goToInvocation"
                        variant="secondary"
                        size="sm"
                        class="w-full !bg-gradient-to-r !from-accent/10 !to-accent/20 !border-accent/30 !text-accent hover:!from-accent/20 hover:!to-accent/30"
                    >
                        ⚡ Invoquer
                    </Button>
                </div>

                <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-full flex flex-col justify-center items-center p-4">
                    <div class="text-center mb-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-warning/20 to-warning/40 rounded-full flex items-center justify-center mb-2 mx-auto">
                            <span class="text-xl">🏪</span>
                        </div>
                        <h3 class="text-base font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-1">
                            Marketplace
                        </h3>
                        <p class="text-xs text-base-content/70">Achetez et vendez</p>
                    </div>

                    <Button
                        v-if="onGoToMarketplace"
                        @click="onGoToMarketplace"
                        variant="secondary"
                        size="sm"
                        class="w-full !bg-gradient-to-r !from-warning/10 !to-warning/20 !border-warning/30 !text-warning hover:!from-warning/20 hover:!to-warning/30"
                    >
                        🏪 Marketplace
                    </Button>
                </div>
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