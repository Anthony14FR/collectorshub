<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import MobileLayout from '@/Components/Layout/MobileLayout.vue';
import DesktopLayout from '@/Components/Layout/DesktopLayout.vue';
import Modal from '@/Components/UI/Modal.vue';
import Button from '@/Components/UI/Button.vue';
import PokemonCard from '@/Components/Cards/PokemonCard.vue';
import type { PageProps } from '@/types';
import type { User } from '@/types/user';
import type { Inventory } from '@/types/inventory';
import type { Pokedex } from '@/types/pokedex';

interface Props extends PageProps {
    auth: {
        user: User;
    };
    inventory?: Inventory[];
    pokedex?: Pokedex[];
}

const { auth, inventory = [], pokedex = [] } = defineProps<Props>();
const pokedexModalOpen = ref(false);

const goToMarketplace = () => {
    router.visit('/marketplace');
};
</script>

<template>
    <Head title="Mon Profil" />

    <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">

        <BackgroundEffects />

        <div class="relative z-10 h-screen w-screen overflow-hidden">
            <MobileLayout
                :user="auth.user"
                :inventory="inventory"
                :pokedex="pokedex"
                :onOpenPokedexModal="() => pokedexModalOpen = true"
                :onGoToMarketplace="goToMarketplace"
            />

            <DesktopLayout
                :user="auth.user"
                :inventory="inventory"
                :pokedex="pokedex"
                :onOpenPokedexModal="() => pokedexModalOpen = true"
                :onGoToMarketplace="goToMarketplace"
            />
        </div>

        <Modal :show="pokedexModalOpen" @close="pokedexModalOpen = false" max-width="7xl">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-lg flex items-center justify-center">
                        <span class="text-lg">📚</span>
                    </div>
                    <div class="flex flex-col">
                        <h3 class="text-xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                            Pokédex Complet
                        </h3>
                        <div class="mt-1">
                            <span class="text-sm font-semibold text-primary">{{ pokedex.length }} Pokémon capturés</span>
                        </div>
                    </div>
                </div>
            </template>
            <template #default>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 max-h-[70vh] overflow-y-auto p-2">
                    <PokemonCard
                        v-for="pokemon in pokedex"
                        :key="pokemon.id"
                        :entry="pokemon"
                        size="large"
                        variant="modal"
                        :show-details="true"
                    />
                </div>
            </template>
        </Modal>
    </div>
</template>

<style>

#app {
    overflow: hidden !important;
    height: 100vh !important;
    width: 100vw !important;
}

::-webkit-scrollbar {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
}

* {
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
}
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin-slow {
    animation: spin-slow 10s linear infinite;
}

.bg-gradient-radial {
    background: radial-gradient(circle, var(--tw-gradient-stops));
}
</style>
