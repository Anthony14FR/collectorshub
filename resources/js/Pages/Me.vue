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
  daily_quests?: any[];
  daily_quest_stats?: any;
  expeditions?: any[];
  has_completed_expeditions?: boolean;
  has_infernal_tower_attempts?: boolean;
  should_show_welcome_modal?: boolean;
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
  daily_quests = [],
  daily_quest_stats = { total: 0, completed: 0, claimed: 0, can_claim_bonus: false, completion_percentage: 0 },
  has_completed_expeditions = false,
  has_infernal_tower_attempts = false,
  should_show_welcome_modal = false,
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
const showWelcomeModal = ref(should_show_welcome_modal);


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

const dismissWelcomeModal = async () => {
  try {
    await fetch('/me/dismiss-welcome-modal', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
    });
    showWelcomeModal.value = false;
  } catch (error) {
    console.error('Erreur lors de la fermeture de la modal:', error);
    showWelcomeModal.value = false;
  }
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
                    :has-unclaimed-gifts="friend_gifts_to_claim.length > 0" :daily_quests="daily_quests"
                    :daily_quest_stats="daily_quest_stats" :announcements="announcements" :marketplace-history="marketplace_history"
                    :unreadNotificationsCount="unread_notifications_count" :hasFriendRequests="friend_requests.length > 0"
                    :hasUnclaimedFriendGifts="friend_gifts_to_claim.length > 0" :hasCompletedExpeditions="has_completed_expeditions"
                    :hasInfernalTowerAttempts="has_infernal_tower_attempts" />

      <DesktopLayout :user="auth.user" :inventory="inventory" :pokedex="pokedex"
                     :level_rewards_to_claim="level_rewards_to_claim" :level_rewards_preview="level_rewards_preview"
                     :team-pokemons="userTeamPokemons" :onOpenPokedexModal="() => pokedexModalOpen = true"
                     :onGoToMarketplace="goToMarketplace" :onGoToLeaderboard="goToLeaderboard"
                     :onOpenTeamManagementModal="openTeamManagementModal" :onOpenBadgesModal="openBadgesModal"
                     :has-unclaimed-successes="unclaimed_successes.length > 0" :onGoToExpeditions="goToExpeditions"
                     :onGoToTower="goToTower" :onOpenFriendsModal="openFriendsModal"
                     :has-unclaimed-gifts="friend_gifts_to_claim.length > 0" :daily_quests="daily_quests"
                     :daily_quest_stats="daily_quest_stats" :announcements="announcements" :marketplace-history="marketplace_history"
                     :unreadNotificationsCount="unread_notifications_count" :hasFriendRequests="friend_requests.length > 0"
                     :hasUnclaimedFriendGifts="friend_gifts_to_claim.length > 0" :hasCompletedExpeditions="has_completed_expeditions"
                     :hasInfernalTowerAttempts="has_infernal_tower_attempts" />
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

  <Modal :show="showWelcomeModal" @close="dismissWelcomeModal" max-width="2xl">
    <template #header>
      <div class="text-center">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
          Bienvenue sur CollectorsHub !
        </h2>
        <p class="text-lg text-base-content/70 mt-2">
          Bonjour <span class="font-bold text-primary">{{ auth.user.username }}</span> !
        </p>
      </div>
    </template>
    <template #default>
      <div class="space-y-6">
        <div class="text-center">
          <p class="text-base-content/80 leading-relaxed mb-2">
            Félicitations ! Votre compte a été créé avec succès et vous êtes maintenant prêt à commencer votre
            aventure Pokémon !
          </p>
          <p class="text-sm text-base-content/60">
            En tant que nouveau dresseur, vous recevez automatiquement des cadeaux de bienvenue.
          </p>
        </div>

        <div class="bg-gradient-to-r from-success/10 to-success/5 rounded-xl p-6 border border-success/20">

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-base-100/60 rounded-xl p-4 text-center border border-info/20">
              <div class="w-16 h-16 mx-auto mb-3 bg-info/10 rounded-full flex items-center justify-center">
                <img src="/images/items/pokeball.png" alt="Pokeball" class="w-10 h-10 object-contain" />
              </div>
              <h4 class="font-bold text-info">+10 Pokeball</h4>
            </div>

            <div class="bg-base-100/60 rounded-xl p-4 text-center border border-warning/20">
              <div class="w-16 h-16 mx-auto mb-3 bg-warning/10 rounded-full flex items-center justify-center">
                <img src="/images/items/masterball.png" alt="Masterball" class="w-10 h-10 object-contain" />
              </div>
              <h4 class="font-bold text-warning">+10 Masterball</h4>
            </div>

            <div class="bg-base-100/60 rounded-xl p-4 text-center border border-accent/20">
              <div class="w-16 h-16 mx-auto mb-3 bg-accent/10 rounded-full flex items-center justify-center">
                <span class="text-2xl">💰</span>
              </div>
              <h4 class="font-bold text-accent">+50 000 coins</h4>
            </div>
          </div>
        </div>

        <div class="text-center pt-4">
          <button @click="dismissWelcomeModal" class="btn btn-primary btn-lg min-w-48">
            🚀 Commencer l'aventure !
          </button>
        </div>
      </div>
    </template>
  </Modal>
</template>
