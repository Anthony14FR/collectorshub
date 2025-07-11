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

const goToInvocation = () => {
    router.visit('/opening');
};

const goToShop = () => {
    router.visit('/shop');
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
            <Button
                variant="outline"
                size="sm"
                class="w-full mt-2"
                @click="goToShop"
            >
                🛍️ Boutique
            </Button>
        </SideSection>

        <SideSection position="left" :top="false" class="top-34">
            <div class="grid grid-cols-1 gap-3 ">
                <div
                    class="relative h-40 overflow-hidden rounded-xl bg-base-100/60 p-4 flex flex-col justify-end"
                    style="background-image: url('/images/background/invocation.png'); background-size: cover; background-position: center;"
                >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent" />
                    <div class="relative z-10">
                        <h3 class="mb-1 text-lg font-bold text-white flex items-center">
                            <img src="/images/items/pokeball.png" alt="pokeball" class="w-6 h-6 mr-2">
                            Invocation
                        </h3>
                        <p class="mb-3 text-xs text-white/80">
                            Invoquez de nouveaux Pokémon
                        </p>
                        <Button
                            @click="goToInvocation"
                            variant="invocation"
                            size="sm"
                            class="w-full"
                        >
                            Invoquer maintenant
                        </Button>
                    </div>
                </div>

                <div class="flex flex-row items-center justify-between overflow-hidden rounded-xl border border-base-300/30 bg-base-100/60 p-4 backdrop-blur-sm">
                    <div>
                        <h3 class="mb-1 bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-base font-bold text-transparent">
                            Marketplace
                        </h3>
                        <p class="text-xs text-base-content/70">
                            Achetez et vendez
                        </p>
                    </div>
                    <Button
                        v-if="onGoToMarketplace"
                        @click="onGoToMarketplace"
                        variant="marketplace"
                        size="sm"
                    >
                        <span class="text-xl">🏪</span>
                    </Button>
                </div>

                <div class="rounded-xl bg-gradient-to-r from-primary/20 to-primary/40" style="background-image: url('/images/background/leaderboard.jpg'); background-size: cover; background-position: center;">
                    <div class="flex h-full flex-col rounded-[11px] bg-base-100/80 p-4">
                        <div class="flex-grow">
                            <div class="flex items-center">
                                <span class="text-4xl">🏆</span>
                                <div class="ml-3">
                                    <h3 class="mb-1 bg-gradient-to-r from-primary to-primary/80 bg-clip-text text-base font-bold text-transparent">
                                        Classement
                                    </h3>
                                    <p class="text-xs text-base-content/70">
                                        Top 100 dresseurs
                                    </p>
                                </div>
                            </div>
                        </div>
                        <Button
                            v-if="onGoToLeaderboard"
                            @click="onGoToLeaderboard"
                            variant="leaderboard"
                            size="sm"
                            class="mt-3"
                        >
                            Voir le classement
                        </Button>
                    </div>
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