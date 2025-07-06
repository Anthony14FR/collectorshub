<script setup lang="ts">
import LevelDisplay from '@/Components/Profile/LevelDisplay.vue';
import TrainerProfile from '@/Components/Profile/TrainerProfile.vue';
import SideSection from '@/Components/Layout/SideSection.vue';
import GameInventory from '@/Components/Game/GameInventory.vue';
import MarketplaceSection from '@/Components/Game/MarketplaceSection.vue';
import UserMenu from '@/Components/Profile/UserMenu.vue';
import PokedexSection from '@/Components/Game/PokedexSection.vue';

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
    <div class="hidden lg:block h-screen w-screen overflow-hidden relative">
        <!-- Zone centrale top : Level centré -->
        <div class="flex justify-center pt-8 mb-8">
            <LevelDisplay :user="user" />
        </div>

        <!-- DRESSEUR AU CENTRE -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-20">
            <TrainerProfile :user="user" :trainer-image-id="2" />
        </div>

        <!-- Zone gauche : SAC -->
        <SideSection position="left" :top="true">
            <GameInventory :inventory="inventory" :cash="user.cash" />
        </SideSection>

        <!-- Zone gauche : MARKETPLACE -->
        <SideSection position="left" :top="false">
            <MarketplaceSection :marketplace="marketplace" />
        </SideSection>

        <!-- Zone droite : MENU -->
        <SideSection position="right" :top="true">
            <UserMenu :user="user" />
        </SideSection>

        <!-- Zone droite : POKÉDEX -->
        <SideSection position="right" :top="false">
            <PokedexSection :pokedex="pokedex" :onOpenModal="onOpenPokedexModal" />
        </SideSection>
    </div>
</template> 