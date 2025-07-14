<script setup lang="ts">
import LeaderboardSection from '@/Components/Game/LeaderboardSection.vue';
import TeamManagementModal from '@/Components/Game/TeamManagementModal.vue';
import DesktopLayout from '@/Components/Layout/DesktopLayout.vue';
import MobileLayout from '@/Components/Layout/MobileLayout.vue';
import PokedexModal from '@/Components/Pokedex/PokedexModal.vue';
import BadgesModal from '@/Components/Profile/BadgesModal.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Modal from '@/Components/UI/Modal.vue';
import type { PageProps } from '@/types';
import type { Inventory } from '@/types/inventory';
import type { Leaderboards } from '@/types/leaderboard';
import type { Pokedex } from '@/types/pokedex';
import type { Pokemon } from '@/types/pokemon';
import type { Success, UserSuccess } from '@/types/success';
import type { User } from '@/types/user';
import { Head, router } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

interface Props extends PageProps {
  auth: {
    user: User;
  };
  inventory?: Inventory[];
  pokedex?: Pokedex[];
  all_pokemons?: Pokemon[];
  leaderboards?: Leaderboards;
  successes?: Success[];
  unclaimed_successes?: UserSuccess[];
  claimed_successes?: UserSuccess[];
  progress?: {
    total: number;
    unlocked: number;
    claimed: number;
    unclaimed: number;
    percentage: number;
  };
}

const {
  auth,
  inventory = [],
  pokedex = [],
  all_pokemons = [],
  leaderboards,
  successes = [],
  unclaimed_successes = [],
  claimed_successes = [],
  progress = { total: 0, unlocked: 0, claimed: 0, unclaimed: 0, percentage: 0 },
} = defineProps<Props>();

const pokedexModalOpen = ref(false);
const leaderboardModalOpen = ref(false);
const teamManagementModalOpen = ref(false);
const badgesModalOpen = ref(false);
const showWelcomeAlert = ref(false);

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('verified') === '1') {
    showWelcomeAlert.value = true;
    const newUrl = window.location.pathname;
    window.history.replaceState({}, '', newUrl);

    setTimeout(() => {
      showWelcomeAlert.value = false;
    }, 8000);
  }
});

const userTeamPokemons = computed(() => {
  return pokedex
    .filter(p => p.is_in_team)
    .sort((a, b) => (a.team_position || 0) - (b.team_position || 0))
    .slice(0, 6);
});

const goToMarketplace = () => {
  router.visit('/marketplace');
};

const openLeaderboardModal = () => {
  leaderboardModalOpen.value = true;
};

const openTeamManagementModal = () => {
  teamManagementModalOpen.value = true;
}

const openBadgesModal = () => {
  badgesModalOpen.value = true;
}

const dismissWelcomeAlert = () => {
  showWelcomeAlert.value = false;
}
</script>

<template>

  <Head title="Mon Profil" />

  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">

    <BackgroundEffects />

    <div class="relative z-10 h-screen w-screen overflow-y-auto lg:overflow-hidden">
      <MobileLayout :user="auth.user" :inventory="inventory" :pokedex="pokedex" :team-pokemons="userTeamPokemons"
                    :onOpenPokedexModal="() => pokedexModalOpen = true" :onGoToMarketplace="goToMarketplace"
                    :onGoToLeaderboard="openLeaderboardModal" :onOpenTeamManagementModal="openTeamManagementModal"
                    :onOpenBadgesModal="openBadgesModal" :has-unclaimed-successes="unclaimed_successes.length > 0" />

      <DesktopLayout :user="auth.user" :inventory="inventory" :pokedex="pokedex" :team-pokemons="userTeamPokemons"
                     :onOpenPokedexModal="() => pokedexModalOpen = true" :onGoToMarketplace="goToMarketplace"
                     :onGoToLeaderboard="openLeaderboardModal" :onOpenTeamManagementModal="openTeamManagementModal"
                     :onOpenBadgesModal="openBadgesModal" :has-unclaimed-successes="unclaimed_successes.length > 0" />
    </div>

    <PokedexModal :show="pokedexModalOpen" :user-pokedex="pokedex" :all-pokemons="all_pokemons"
                  :on-close="() => pokedexModalOpen = false" />

    <TeamManagementModal :show="teamManagementModalOpen" :user-pokemons="pokedex"
                         :on-close="() => teamManagementModalOpen = false" />

    <Modal :show="leaderboardModalOpen" @close="leaderboardModalOpen = false" max-width="4xl">
      <template #header>
        <div class="flex items-center gap-3">
          <div
            class="w-8 h-8 bg-gradient-to-br from-warning/20 to-warning/40 rounded-lg flex items-center justify-center">
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

    <BadgesModal :show="badgesModalOpen" :on-close="() => badgesModalOpen = false" :successes="successes"
                 :unclaimed_successes="unclaimed_successes" :claimed_successes="claimed_successes" :progress="progress" />

    <div v-if="showWelcomeAlert" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-96">
      <Alert type="success"
             :message="`🎉 Bienvenue ${auth.user.username} ! Votre email a été vérifié avec succès. Votre aventure Pokémon peut commencer !`"
             dismissible @dismiss="dismissWelcomeAlert" />
    </div>
  </div>
</template>
