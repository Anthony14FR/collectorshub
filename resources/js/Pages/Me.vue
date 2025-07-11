<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import MobileLayout from '@/Components/Layout/MobileLayout.vue';
import DesktopLayout from '@/Components/Layout/DesktopLayout.vue';
import Modal from '@/Components/UI/Modal.vue';
import LeaderboardSection from '@/Components/Game/LeaderboardSection.vue';
import PokedexModal from '@/Components/Pokedex/PokedexModal.vue';
import type { PageProps } from '@/types';
import type { User } from '@/types/user';
import type { Inventory } from '@/types/inventory';
import type { Pokedex } from '@/types/pokedex';
import type { Pokemon } from '@/types/pokemon';
import type { Leaderboards } from '@/types/leaderboard';

interface Props extends PageProps {
    auth: {
        user: User;
    };
    inventory?: Inventory[];
    pokedex?: Pokedex[];
    all_pokemons?: Pokemon[];
    leaderboards?: Leaderboards;
}

const { auth, inventory = [], pokedex = [], all_pokemons = [], leaderboards } = defineProps<Props>();
const pokedexModalOpen = ref(false);
const leaderboardModalOpen = ref(false);

const goToMarketplace = () => {
    router.visit('/marketplace');
};

const openLeaderboardModal = () => {
    leaderboardModalOpen.value = true;
};
</script>

<template>
    <Head title="Mon Profil" />

    <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">

        <BackgroundEffects />

        <div class="relative z-10 h-screen w-screen overflow-y-auto lg:overflow-hidden">
            <MobileLayout
                :user="auth.user"
                :inventory="inventory"
                :pokedex="pokedex"
                :onOpenPokedexModal="() => pokedexModalOpen = true"
                :onGoToMarketplace="goToMarketplace"
                :onGoToLeaderboard="openLeaderboardModal"
            />

            <DesktopLayout
                :user="auth.user"
                :inventory="inventory"
                :pokedex="pokedex"
                :onOpenPokedexModal="() => pokedexModalOpen = true"
                :onGoToMarketplace="goToMarketplace"
                :onGoToLeaderboard="openLeaderboardModal"
            />
        </div>

        <PokedexModal 
            :show="pokedexModalOpen" 
            :user-pokedex="pokedex" 
            :all-pokemons="all_pokemons" 
            :on-close="() => pokedexModalOpen = false" 
        />

        <Modal :show="leaderboardModalOpen" @close="leaderboardModalOpen = false" max-width="4xl">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-warning/20 to-warning/40 rounded-lg flex items-center justify-center">
                        <span class="text-lg">🏆</span>
                    </div>
                    <div class="flex flex-col">
                        <h3 class="text-xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
                            Classements
                        </h3>
                        <div class="mt-1">
                            <span class="text-sm font-semibold text-warning">Top 100 des dresseurs</span>
                        </div>
                    </div>
                </div>
            </template>
            <template #default>
                <LeaderboardSection v-if="leaderboards" :leaderboards="leaderboards" />
            </template>
        </Modal>
    </div>
</template>

<style>
</style>
