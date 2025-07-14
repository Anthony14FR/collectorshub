<script setup lang="ts">
import LevelDisplay from '@/Components/Profile/LevelDisplay.vue';
import GameInventory from '@/Components/Game/GameInventory.vue';
import UserMenu from '@/Components/Profile/UserMenu.vue';
import TrainerProfile from '@/Components/Profile/TrainerProfile.vue';
import Button from '@/Components/UI/Button.vue';
import StarsBadge from '@/Components/UI/StarsBadge.vue';
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
  onOpenTeamManagementModal?: () => void;
  onOpenBadgesModal?: () => void;
  hasUnclaimedSuccesses?: boolean;
  teamPokemons?: Pokedex[];
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
  <div class="lg:hidden flex flex-col p-4">
    <div class="shrink-0 mb-6">
      <LevelDisplay :user="user" :responsive="true" />
    </div>

    <div class="shrink-0 grid grid-cols-2 gap-4 mb-6">
      <GameInventory :inventory="inventory" :cash="user.cash" />
      <UserMenu :user="user" />
    </div>

    <div class="shrink-0 mb-6">
      <Button
        variant="outline"
        size="md"
        class="w-full"
        @click="goToShop"
      >
        🛍️ Boutique
      </Button>
    </div>

    <div class="shrink-0 flex justify-center mb-6">
      <div class="scale-75">
        <TrainerProfile :user="user" :trainer-image-id="2" :on-open-pokedex-modal="onOpenTeamManagementModal" :team-pokemons="teamPokemons" />
      </div>
    </div>

    <div class="grid grid-cols-1 gap-4">
      <div
        class="relative h-40 overflow-hidden rounded-xl border border-base-300/30 bg-base-100/60 p-4 flex flex-col justify-end"
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

      <div class="flex flex-row items-center justify-between overflow-hidden rounded-xl border border-base-300/30 bg-base-100/60 p-4 backdrop-blur-sm relative">
        <div v-if="hasUnclaimedSuccesses" class="absolute top-3 right-3 z-10">
          <span class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-error opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-error"></span>
          </span>
        </div>
        <div>
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
          variant="primary"
          size="sm"
        >
          <span class="text-xl">🏆</span>
        </Button>
      </div>

      <div
        class="relative h-40 overflow-hidden rounded-xl bg-base-100/60 backdrop-blur-sm p-4"
        style="background-image: url('/images/background/upgrade.gif'); background-size: cover; background-position: center;"
      >
        <div class="absolute inset-0 bg-gradient-to-br from-warning/80 via-warning/40 to-transparent" />
        <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-bl from-white/30 to-transparent rounded-bl-full" />
        
        <div class="relative z-10 h-full flex flex-col justify-between">
          <div class="flex items-start justify-between">
            <div class="flex items-center space-x-2">
              <div>
                <StarsBadge :stars="6" />
              </div>
              <div>
                <h3 class="text-base font-bold text-white drop-shadow-sm">
                  Amélioration
                </h3>
              </div>
            </div>
          </div>
          <p class="text-sm text-white/90 drop-shadow-sm">Augmentez le niveau d'étoiles de vos Pokémon</p>
          
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

      <div class="rounded-xl bg-gradient-to-r from-primary/20 to-primary/40 p-0.5" style="background-image: url('/images/background/leaderboard.jpg'); background-size: cover; background-position: center;">
        <div class="flex h-full flex-col rounded-[11px] bg-base-100/80 p-4 backdrop-blur-sm">
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
            class="mt-3 w-full"
          >
            Voir le classement
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>
