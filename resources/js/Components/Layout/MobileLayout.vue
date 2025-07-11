<script setup lang="ts">
import LevelDisplay from '@/Components/Profile/LevelDisplay.vue';
import GameInventory from '@/Components/Game/GameInventory.vue';
import UserMenu from '@/Components/Profile/UserMenu.vue';
import TrainerProfile from '@/Components/Profile/TrainerProfile.vue';
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
</script>

<template>
    <div class="lg:hidden flex flex-col p-4">
        <div class="shrink-0 mb-6">
            <LevelDisplay :user="user" :responsive="true" />
        </div>

        <div class="shrink-0 grid grid-cols-2 gap-4 mb-6">
            <GameInventory :inventory="inventory" :cash="user.cash" />
            <UserMenu :user="user" />
        </div>

        <div class="shrink-0 flex justify-center mb-6">
            <div class="scale-75">
                <TrainerProfile :user="user" :trainer-image-id="2" />
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 p-4 flex flex-col items-center justify-center text-center space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-accent/20 to-accent/40 rounded-lg flex items-center justify-center">
                        <span class="text-lg">⚡</span>
                    </div>
                    <h3 class="text-lg font-bold bg-gradient-to-r from-accent to-accent/80 bg-clip-text text-transparent">
                        Invocation
                    </h3>
                </div>

                <p class="text-sm text-base-content/70">
                    Invoquez de nouveaux Pokémon avec vos balls
                </p>

                <Button
                    @click="goToInvocation"
                    variant="secondary"
                    size="md"
                    class="w-full max-w-xs !bg-gradient-to-r !from-accent/10 !to-accent/20 !border-accent/30 !text-accent hover:!from-accent/20 hover:!to-accent/30"
                >
                    ⚡ Invoquer
                </Button>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 p-4 flex flex-col items-center justify-center text-center space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-warning/20 to-warning/40 rounded-lg flex items-center justify-center">
                        <span class="text-lg">🏪</span>
                    </div>
                    <h3 class="text-lg font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
                        Marketplace
                    </h3>
                </div>

                <p class="text-sm text-base-content/70">
                    Achetez et vendez vos Pokémon
                </p>

                <Button
                    v-if="onGoToMarketplace"
                    @click="onGoToMarketplace"
                    variant="secondary"
                    size="md"
                    class="w-full max-w-xs !bg-gradient-to-r !from-warning/10 !to-warning/20 !border-warning/30 !text-warning hover:!from-warning/20 hover:!to-warning/30"
                >
                    🏪 Marketplace
                </Button>
            </div>

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
                    size="md"
                    @click="onOpenPokedexModal"
                    class="w-full max-w-xs !bg-gradient-to-r !from-secondary/10 !to-accent/20 !border-secondary/30 !text-secondary hover:!from-secondary/20 hover:!to-accent/30"
                >
                    📚 Voir mes Pokémon
                </Button>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 p-4 flex flex-col items-center justify-center text-center space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary/20 to-primary/40 rounded-lg flex items-center justify-center">
                        <span class="text-lg">🏆</span>
                    </div>
                    <h3 class="text-lg font-bold bg-gradient-to-r from-primary to-primary/80 bg-clip-text text-transparent">
                        Classement
                    </h3>
                </div>

                <p class="text-sm text-base-content/70">
                    Top 100 des dresseurs
                </p>

                <Button
                    v-if="onGoToLeaderboard"
                    @click="onGoToLeaderboard"
                    variant="secondary"
                    size="md"
                    class="w-full max-w-xs !bg-gradient-to-r !from-primary/10 !to-primary/20 !border-primary/30 !text-primary hover:!from-primary/20 hover:!to-primary/30"
                >
                    🏆 Voir le classement
                </Button>
            </div>
        </div>
    </div>
</template>