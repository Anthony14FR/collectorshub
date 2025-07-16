<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import MobileLayout from '@/Components/Layout/MobileLayout.vue';
import DesktopLayout from '@/Components/Layout/DesktopLayout.vue';
import PokedexModal from '@/Components/Pokedex/PokedexModal.vue';
import TeamManagementModal from '@/Components/Game/TeamManagementModal.vue';
import BadgesModal from '@/Components/Profile/BadgesModal.vue';
import type { PageProps } from '@/types';
import type { User } from '@/types/user';
import type { Inventory } from '@/types/inventory';
import type { Pokedex } from '@/types/pokedex';
import type { Pokemon } from '@/types/pokemon';
import type { Success, UserSuccess } from '@/types/success';
import type { LevelReward, LevelRewardPreview } from '@/types/user';
import type { UserFriend, UserFriendGift, FriendRequest } from '@/types/friend';
import FriendsModal from '@/Components/Friends/FriendsModal.vue';

interface Props extends PageProps {
  auth: {
    user: User;
  };
  inventory?: Inventory[];
  pokedex?: Pokedex[];
  all_pokemons?: Pokemon[];
  successes?: Success[];
  unclaimed_successes?: UserSuccess[];
  claimed_successes?: UserSuccess[];
  level_rewards_to_claim?: LevelReward[];
  level_rewards_preview?: LevelRewardPreview;
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

const { 
  auth, 
  inventory = [], 
  pokedex = [], 
  all_pokemons = [],
  successes = [],
  unclaimed_successes = [],
  claimed_successes = [],
  level_rewards_to_claim = [],
  level_rewards_preview,
  progress = { total: 0, unlocked: 0, claimed: 0, unclaimed: 0, percentage: 0 },
  friend_gifts_to_claim = [],
  friend_requests = [],
  friends = [],
  suggestions = [],
} = defineProps<Props>();

const pokedexModalOpen = ref(false);
const teamManagementModalOpen = ref(false);
const badgesModalOpen = ref(false);
const friendsModalOpen = ref(false);
const friendsData = ref([...friends]);
const friendRequestsData = ref([...friend_requests]);
const suggestionsData = ref([...suggestions]);


const sendGift = (friendId) => {
  router.post(
    "/friend-gifts/send",
    { receiver_id: friendId },
    { preserveScroll: true }
  );
};

const claimGift = (giftId) => {
  router.post(
    "/friend-gifts/claim",
    { gift_id: giftId },
    { preserveScroll: true }
  );
};

const removeFriend = (friendId) => {
  router.post(
    "/friends/remove",
    { target_id: friendId },
    { preserveScroll: true }
  );
};

const userTeamPokemons = computed(() => {
  return pokedex
    .filter(p => p.is_in_team)
    .sort((a, b) => (a.team_position || 0) - (b.team_position || 0))
    .slice(0, 6);
});

const refreshInventory = async () => {
  const { data } = await axios.get('/me/inventory');
};

const handleDataRefreshed = (newData) => {
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

const goToExpeditions = () => {
  router.visit('/expeditions');
}
</script>

<template>
  <Head title="Mon Profil" />

  <div class="">

    <BackgroundEffects />

    <div class="relative z-50 h-screen w-screen overflow-x-hidden">
      <MobileLayout
        :user="auth.user"
        :inventory="inventory"
        :pokedex="pokedex"
        :level_rewards_to_claim="level_rewards_to_claim"
        :level_rewards_preview="level_rewards_preview"
        :team-pokemons="userTeamPokemons"
        :onOpenPokedexModal="() => pokedexModalOpen = true"
        :onGoToMarketplace="goToMarketplace"
        :onGoToLeaderboard="goToLeaderboard"
        :onOpenTeamManagementModal="openTeamManagementModal"
        :onOpenBadgesModal="openBadgesModal"
        :has-unclaimed-successes="unclaimed_successes.length > 0"
        :onGoToExpeditions="goToExpeditions"
        :onOpenFriendsModal="openFriendsModal"
        :has-unclaimed-gifts="friend_gifts_to_claim.length > 0"
      />

      <DesktopLayout
        :user="auth.user"
        :inventory="inventory"
        :pokedex="pokedex"
        :level_rewards_to_claim="level_rewards_to_claim"
        :level_rewards_preview="level_rewards_preview"
        :team-pokemons="userTeamPokemons"
        :onOpenPokedexModal="() => pokedexModalOpen = true"
        :onGoToMarketplace="goToMarketplace"
        :onGoToLeaderboard="goToLeaderboard"
        :onOpenTeamManagementModal="openTeamManagementModal"
        :onOpenBadgesModal="openBadgesModal"
        :has-unclaimed-successes="unclaimed_successes.length > 0"
        :onGoToExpeditions="goToExpeditions"
        :onOpenFriendsModal="openFriendsModal"
        :has-unclaimed-gifts="friend_gifts_to_claim.length > 0"
      />
    </div>

    <PokedexModal 
      :show="pokedexModalOpen" 
      :user-pokedex="pokedex" 
      :all-pokemons="all_pokemons" 
      :on-close="() => pokedexModalOpen = false" 
    />

    <TeamManagementModal 
      :show="teamManagementModalOpen"
      :user-pokemons="pokedex"
      :on-close="() => teamManagementModalOpen = false"
    />

    <BadgesModal
      :show="badgesModalOpen"
      :on-close="() => badgesModalOpen = false"
      :successes="successes"
      :unclaimed_successes="unclaimed_successes"
      :claimed_successes="claimed_successes"
      :progress="progress"
    />

    <FriendsModal
      :show="friendsModalOpen"
      :onClose="() => friendsModalOpen = false"
      :friends="friendsData"
      :friend_requests="friendRequestsData"
      :suggestions="suggestionsData"
      :currentUserId="auth.user.id"
      @send-gift="sendGift"
      @claim-gift="claimGift"
      @remove-friend="removeFriend"
      @dataRefreshed="handleDataRefreshed"
    />
  </div>
</template>
