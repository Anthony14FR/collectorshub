<script setup lang="ts">
import LevelDisplay from '@/Components/Profile/LevelDisplay.vue';
import TrainerProfile from '@/Components/Profile/TrainerProfile.vue';
import SideSection from '@/Components/Layout/SideSection.vue';
import GameInventory from '@/Components/Game/GameInventory.vue';
import UserMenu from '@/Components/Profile/UserMenu.vue';
import PokedexSection from '@/Components/Game/PokedexSection.vue';
import Button from '@/Components/UI/Button.vue';
import { router } from '@inertiajs/vue3';
import StarsBadge from '@/Components/UI/StarsBadge.vue';

import type { User } from '@/types/user';
import type { Inventory } from '@/types/inventory';
import type { Pokedex } from '@/types/pokedex';
import type { LevelReward, LevelRewardPreview } from "@/types/user";

interface Props {
  user: User;
  inventory: Inventory[];
  pokedex: Pokedex[];
  onOpenPokedexModal: () => void;
  onGoToMarketplace?: () => void;
  onGoToLeaderboard?: () => void;
  onOpenTeamManagementModal?: () => void;
  onOpenBadgesModal?: () => void;
  hasUnclaimedSuccesses?: boolean;
  teamPokemons?: Pokedex[];
  level_rewards_to_claim?: LevelReward[];
  level_rewards_preview?: LevelRewardPreview;
  onGoToExpeditions?: () => void;
}

const { 
  user, 
  inventory, 
  pokedex, 
  onOpenPokedexModal, 
  onGoToMarketplace, 
  onGoToLeaderboard, 
  onOpenTeamManagementModal,
  onOpenBadgesModal,
  hasUnclaimedSuccesses,
  teamPokemons,
  level_rewards_to_claim = [],
  level_rewards_preview,
  onGoToExpeditions,
} = defineProps<Props>();

const goToInvocation = () => {
  router.visit('/opening');
};

const goToShop = () => {
  router.visit('/shop');
};

const goToPokemonUpgrade = () => {
  router.visit('/pokemon-upgrade');
};
</script>

<template>
  <div class="hidden lg:block h-screen w-screen relative">
    <div class="flex flex-col space-y-1 w-72 mx-auto">
      <div class="flex justify-center mt-4">
        <LevelDisplay :user="user" :level_rewards_to_claim="level_rewards_to_claim" :level_rewards_preview="level_rewards_preview" />
      </div>

      <div class="z-20 mb-14">
        <TrainerProfile :user="user" :trainer-image-id="2" :on-open-pokedex-modal="onOpenTeamManagementModal" :team-pokemons="teamPokemons" />
      </div>
    </div>

    <SideSection position="left" :top="true" customClasses="flex gap-2">
      <GameInventory :inventory="inventory" :cash="user.cash" />
      <Button
        variant="secondary"
        size="sm"
        class="w-full"
        @click="goToShop"
      >
        <img src="/images/icons/shop.webp" alt="Boutique" class="w-6 h-6 mr-2" /> Boutique
      </Button>
    </SideSection>

    <SideSection position="left" :top="false" class="top-24">
      <div class="grid grid-cols-1 gap-3 ">
        <div
          class="relative h-40 overflow-hidden border-2 border-green-800/70 bg-base-100/60 p-4 flex flex-col justify-end"
          style="background-image: url('/images/background/invocation.gif'); background-size: cover; background-position: center;"
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

        <div
          class="flex flex-row items-center justify-between overflow-hidden p-4 backdrop-blur-sm relative"
          style="background-image: url('/images/marketplace.jpg'); background-size: cover; background-position: center;"
        >
          <div class="absolute inset-0 bg-black/70" />
          <div class="relative z-10">
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
            variant="secondary"
            size="sm"
            class="relative z-10"
          >
            <img src="/images/icons/marketplace.png" alt="Marketplace" class="w-10 h-10" />
          </Button>
        </div>

        <div
          class="flex flex-row items-center justify-between overflow-hidden p-4 backdrop-blur-sm relative"
          style="background-image: url('/images/badge.jpg'); background-size: cover; background-position: center;"
        >
          <div class="absolute inset-0 bg-black/70" />
          <div v-if="hasUnclaimedSuccesses" class="absolute top-3 right-3 z-20">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-error opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-error"></span>
            </span>
          </div>
          <div class="relative z-10">
            <h3 class="mb-1 bg-gradient-to-r from-info to-info/80 bg-clip-text text-base font-bold text-transparent">
              Badges
            </h3>
            <p class="text-xs text-base-content/70">
              Vos succès et récompenses
            </p>
          </div>
          <Button
            v-if="onOpenBadgesModal"
            @click="onOpenBadgesModal"
            variant="secondary"
            size="sm"
            class="relative z-10"
          >
            <img src="/images/icons/success.webp" alt="Badges" class="w-10 h-10" />
          </Button>
        </div>

        <div class="bg-gradient-to-r from-primary/20 to-primary/40" style="background-image: url('/images/background/leaderboard.jpg'); background-size: cover; background-position: center;">
          <div class="flex h-full flex-col bg-base-100/70 p-4">
            <div class="flex-grow">
              <div class="flex items-center">
                <img src="/images/icons/classement.png" alt="Classement" class="w-10 h-10" />
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
    <SideSection position="right" :top="false" class="top-74">
      <div 
        class="relative h-40 overflow-hidden border-2 border-warning bg-base-100/60 backdrop-blur-sm"
        style="background-image: url('/images/background/upgrade.gif'); background-size: cover; background-position: center;"
      >
        <div class="absolute inset-0 bg-gradient-to-br from-warning/80 via-warning/40 to-transparent" />
        
        <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-bl from-white/30 to-transparent rounded-bl-full" />
        
        <div class="relative z-10 h-full flex flex-col justify-between p-4">
          <div class="flex items-start justify-between">
            <div class="flex items-center space-x-2">
              <div class="">
                <StarsBadge :stars="6" />
              </div>
              <div>
                <h3 class="text-base font-bold text-white drop-shadow-sm">
                  Amélioration
                </h3>
              </div>
            </div>
          </div>
          <p>Augmentez le niveau d'étoiles de vos Pokémon</p>
          
          <div class="flex">
            <Button
              @click="goToPokemonUpgrade"
              variant="secondary"
              size="sm"
              class="shadow-lg w-full"
            >
              Améliorer
            </Button>
          </div>
        </div>
      </div>
    </SideSection>
    <SideSection position="right" :top="false" class="top-[475px]">
      <div
        class="relative h-40 overflow-hidden  border-2 border-info bg-base-100/60 p-4 flex flex-col justify-end"
        style="background-image: url('/images/background/expeditions.gif'); background-size: cover; background-position: center;"
      >
        <div>
          <img src="/images/icons/treasure-map.webp" alt="Expedition" class="w-10 h-10 absolute top-2 right-4" />
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-success/70 to-transparent" />
        <div class="relative z-10">
          <h3 class="mb-1 text-lg font-bold text-white flex items-center">
            Expéditions
          </h3>
          <p class="mb-3 text-xs text-white/80">
            Partez à l'aventure et gagnez des récompenses
          </p>
          <Button
            v-if="onGoToExpeditions"
            @click="onGoToExpeditions"
            variant="secondary"
            size="sm"
            class="w-full"
          >
            Commencer une expédition
          </Button>
        </div>
      </div>
    </SideSection>
  </div>
</template>
