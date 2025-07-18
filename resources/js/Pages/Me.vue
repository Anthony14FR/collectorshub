<script setup lang="ts">
import FriendsModal from '@/Components/Friends/FriendsModal.vue';
import LeaderboardSection from '@/Components/Game/LeaderboardSection.vue';
import TeamManagementModal from '@/Components/Game/TeamManagementModal.vue';
import DesktopLayout from '@/Components/Layout/DesktopLayout.vue';
import MobileLayout from '@/Components/Layout/MobileLayout.vue';
import PokedexModal from '@/Components/Pokedex/PokedexModal.vue';
import BadgesModal from '@/Components/Profile/BadgesModal.vue';
import Alert from '@/Components/UI/Alert.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import HelpModal from '@/Components/UI/HelpModal.vue';
import Modal from '@/Components/UI/Modal.vue';
import type { PageProps } from '@/types';
import type { FriendRequest, UserFriend, UserFriendGift } from '@/types/friend';
import type { Inventory } from '@/types/inventory';
import type { Leaderboards } from '@/types/leaderboard';
import type { Pokedex } from '@/types/pokedex';
import type { Pokemon } from '@/types/pokemon';
import type { Success, UserSuccess } from '@/types/success';
import type { AvailableLevelReward, LevelRewardPreview, User } from '@/types/user';
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
  level_rewards_to_claim?: AvailableLevelReward[];
  level_rewards_preview?: LevelRewardPreview;
  announcements?: any[];
  marketplace_history?: any[];
  unread_notifications_count?: number;
  progress?: {
    total: number;
    unlocked: number;
    claimed: number;
    unclaimed: number;
    percentage: number;
  };
  friend_gifts_to_claim: UserFriendGift[];
  friend_requests: FriendRequest[];
  friends: UserFriend[];
  suggestions: User[];
}

interface RefreshedData {
  friends?: UserFriend[];
  friend_requests?: FriendRequest[];
  suggestions?: User[];
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
  level_rewards_to_claim = [],
  level_rewards_preview,
  announcements = [],
  marketplace_history = [],
  unread_notifications_count = 0,
  progress = { total: 0, unlocked: 0, claimed: 0, unclaimed: 0, percentage: 0 },
  friend_gifts_to_claim = [],
  friend_requests = [],
  friends = [],
  suggestions = [],
} = defineProps<Props>();

const pokedexModalOpen = ref(false);
const teamManagementModalOpen = ref(false);
const badgesModalOpen = ref(false);
const leaderboardModalOpen = ref(false);

const friendsModalOpen = ref(false);
const friendsData = ref([...friends]);
const friendRequestsData = ref([...friend_requests]);
const suggestionsData = ref([...suggestions]);
const showHelpModal = ref(false)


const sendGift = (friendId: number) => {
  router.post(
    "/friend-gifts/send",
    { receiver_id: friendId },
    { preserveScroll: true }
  );
};

const claimGift = (giftId: number) => {
  router.post(
    "/friend-gifts/claim",
    { gift_id: giftId },
    { preserveScroll: true }
  );
};

const removeFriend = (friendId: number) => {
  router.post(
    "/friends/remove",
    { target_id: friendId },
    { preserveScroll: true }
  );
};

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

const handleDataRefreshed = (newData: RefreshedData) => {
  if (newData.friends) {
    friendsData.value = newData.friends;
  }
  if (newData.friend_requests) {
    friendRequestsData.value = newData.friend_requests;
  }
  if (newData.suggestions) {
    suggestionsData.value = newData.suggestions;
  }

  friendsData.value = [...friendsData.value];
  friendRequestsData.value = [...friendRequestsData.value];
  suggestionsData.value = [...suggestionsData.value];
};

const goToMarketplace = () => {
  router.visit('/marketplace');
};

const goToLeaderboard = () => {
  router.visit('/leaderboard');
};

const openTeamManagementModal = () => {
  teamManagementModalOpen.value = true;
}

const openBadgesModal = () => {
  badgesModalOpen.value = true;
}

const openFriendsModal = () => {
  friendsModalOpen.value = true;
}

const dismissWelcomeAlert = () => {
  showWelcomeAlert.value = false;
}

const goToExpeditions = () => {
  router.visit('/expeditions');
}

const goToTower = () => {
  router.visit('/tower');
}
</script>

<template>

  <Head title="Mon Profil" />

  <div class="">

    <BackgroundEffects />

    <div class="relative z-50 h-screen w-screen overflow-x-hidden">

      <MobileLayout :user="auth.user" :inventory="inventory" :pokedex="pokedex"
                    :level_rewards_to_claim="level_rewards_to_claim" :level_rewards_preview="level_rewards_preview"
                    :team-pokemons="userTeamPokemons" :onOpenPokedexModal="() => pokedexModalOpen = true"
                    :onGoToMarketplace="goToMarketplace" :onGoToLeaderboard="goToLeaderboard"
                    :onOpenTeamManagementModal="openTeamManagementModal" :onOpenBadgesModal="openBadgesModal"
                    :has-unclaimed-successes="unclaimed_successes.length > 0" :onGoToExpeditions="goToExpeditions"
                    :onGoToTower="goToTower" :onOpenFriendsModal="openFriendsModal"
                    :has-unclaimed-gifts="friend_gifts_to_claim.length > 0" :announcements="announcements"
                    :marketplace-history="marketplace_history" :unread-notifications-count="unread_notifications_count" />

      <DesktopLayout :user="auth.user" :inventory="inventory" :pokedex="pokedex"
                     :level_rewards_to_claim="level_rewards_to_claim" :level_rewards_preview="level_rewards_preview"
                     :team-pokemons="userTeamPokemons" :onOpenPokedexModal="() => pokedexModalOpen = true"
                     :onGoToMarketplace="goToMarketplace" :onGoToLeaderboard="goToLeaderboard"
                     :onOpenTeamManagementModal="openTeamManagementModal" :onOpenBadgesModal="openBadgesModal"
                     :has-unclaimed-successes="unclaimed_successes.length > 0" :onGoToExpeditions="goToExpeditions"
                     :onGoToTower="goToTower" :onOpenFriendsModal="openFriendsModal"
                     :has-unclaimed-gifts="friend_gifts_to_claim.length > 0" :announcements="announcements"
                     :marketplace-history="marketplace_history" :unread-notifications-count="unread_notifications_count" />
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
    <button @click="showHelpModal = true"
            class="fixed bottom-4 left-4 w-12 h-12 bg-info hover:bg-info/80 cursor-pointer hover:bg-base-300 text-white rounded-full flex items-center justify-center shadow-lg transition-all z-50"
            title="Aide">
      <span class="text-xl font-bold">?</span>
    </button>
    <FriendsModal :show="friendsModalOpen" :onClose="() => friendsModalOpen = false" :friends="friendsData"
                  :friend_requests="friendRequestsData" :suggestions="suggestionsData" :currentUserId="auth.user.id"
                  @send-gift="sendGift" @claim-gift="claimGift" @remove-friend="removeFriend"
                  @dataRefreshed="handleDataRefreshed" />
  </div>
  <HelpModal :show="showHelpModal" :onClose="() => showHelpModal = false" />
</template>
