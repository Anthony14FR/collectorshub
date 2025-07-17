<script setup lang="ts">
import LevelDisplay from '@/Components/Profile/LevelDisplay.vue';
import GameInventory from '@/Components/Game/GameInventory.vue';
import UserMenu from '@/Components/Profile/UserMenu.vue';
import TrainerProfile from '@/Components/Profile/TrainerProfile.vue';
import Button from '@/Components/UI/Button.vue';
import Badge from '@/Components/UI/Badge.vue';
import StarsBadge from '@/Components/UI/StarsBadge.vue';
import { router } from '@inertiajs/vue3';
import type { User } from '@/types/user';
import type { Inventory } from '@/types/inventory';
import type { Pokedex } from '@/types/pokedex';
import type { LevelReward, LevelRewardPreview } from "@/types/user";
import DailyQuestsModal from '@/Components/DailyQuests/DailyQuestsModal.vue';
import type { DailyQuest, DailyQuestStats } from '@/types/daily-quest';
import { ref, computed } from 'vue';

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
  onGoToTower?: () => void;
  onOpenFriendsModal?: () => void;
  daily_quests?: DailyQuest[];
  daily_quest_stats?: DailyQuestStats;
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
  onGoToTower,
  onOpenFriendsModal,
  daily_quests = [],
  daily_quest_stats = { total: 0, completed: 0, claimed: 0, can_claim_bonus: false, completion_percentage: 0 },
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

const dailyQuestsModalOpen = ref(false);

const unclaimedQuestsCount = computed(() => 
  daily_quests.filter(q => q.is_completed && !q.is_claimed).length
);

const handleQuestClaimed = (data: any) => {
  router.reload({ only: ['auth', 'daily_quests', 'daily_quest_stats'] });
};

const handleBonusClaimed = (data: any) => {
  router.reload({ only: ['auth', 'daily_quests', 'daily_quest_stats'] });
};
</script>

<template>
  <div class="lg:hidden min-h-screen pb-safe">
    <div class="z-50 px-3 py-2">
      <LevelDisplay :user="user" :responsive="true" :level_rewards_to_claim="level_rewards_to_claim" :level_rewards_preview="level_rewards_preview" />
    </div>

    <div class="mb-3 mx-5">
      <Button 
        @click="dailyQuestsModalOpen = true"
        variant="secondary" 
        size="sm"
        class="w-full relative"
      >
        <div class="flex items-center justify-center gap-2">
          <span>Quêtes</span>
          <Badge 
            v-if="unclaimedQuestsCount > 0" 
            variant="error" 
            size="sm"
            class="absolute -top-2 -right-2"
          >
            {{ unclaimedQuestsCount }}
          </Badge>
        </div>
      </Button>
    </div>

    <div class="space-y-3">
      <div class="relative mx-5">
        <GameInventory :inventory="inventory" :cash="user.cash" />
      </div>

      <div class="relative mx-5">
        <UserMenu :user="user" />
      </div>
    </div>

    <div class="px-3 py-3 space-y-3 relative">

      <div class="flex justify-center bg-base-100/60 backdrop-blur-sm rounded-2xl border border-base-300/20 p-4">
        <div class="scale-90 -my-2">
          <TrainerProfile :user="user" :trainer-image-id="2" :on-open-pokedex-modal="onOpenTeamManagementModal" :team-pokemons="teamPokemons" />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-2">
        <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-emerald-500/20 to-green-600/30 border border-green-400/30">
          <div class="absolute inset-0 bg-[url('/images/background/invocation.gif')] bg-cover bg-center opacity-30"></div>
          <div class="relative p-4 flex flex-col h-32">
            <div class="flex items-center mb-2">
              <img src="/images/items/pokeball.png" alt="pokeball" class="w-5 h-5 mr-2">
              <h3 class="text-sm font-bold text-white">Invocation</h3>
            </div>
            <p class="text-xs text-white/80 mb-auto">Nouveaux Pokémon</p>
            <Button @click="goToInvocation" variant="invocation" size="sm" class="w-full text-xs py-1.5">
              Invoquer
            </Button>
          </div>
        </div>

        <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-warning/20 to-orange-500/30 border border-warning/30">
          <div class="absolute inset-0 bg-[url('/images/marketplace.jpg')] bg-cover bg-center opacity-40"></div>
          <div class="absolute inset-0 bg-black/40"></div>
          <div class="relative p-4 flex flex-col h-32">
            <div class="flex items-center mb-2">
              <img src="/images/icons/marketplace.png" alt="Marketplace" class="w-5 h-5 mr-2">
              <h3 class="text-sm font-bold text-warning">Market</h3>
            </div>
            <p class="text-xs text-white/80 mb-auto">Acheter & vendre</p>
            <Button v-if="onGoToMarketplace" @click="onGoToMarketplace" variant="invocation" size="sm" class="w-full text-xs py-1.5">
              Explorer
            </Button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-2">
        <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-info/20 to-blue-500/30 border border-info/30">
          <div class="absolute inset-0 bg-[url('/images/badge.jpg')] bg-cover bg-center opacity-30"></div>
          <div v-if="hasUnclaimedSuccesses" class="absolute top-2 right-2 z-20">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-error opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-error"></span>
            </span>
          </div>
          <div class="relative p-4 flex flex-col h-32">
            <div class="flex items-center mb-2">
              <img src="/images/icons/success.webp" alt="Badges" class="w-5 h-5 mr-2">
              <h3 class="text-sm font-bold text-info">Badges</h3>
            </div>
            <p class="text-xs text-white/80 mb-auto">Vos succès</p>
            <Button v-if="onOpenBadgesModal" @click="onOpenBadgesModal" variant="secondary" size="sm" class="w-full text-xs py-1.5">
              Voir
            </Button>
          </div>
        </div>

        <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-secondary/20 to-secondary/40 border border-secondary/30">
          <div class="absolute inset-0 bg-[url('/images/friends.jpg')] bg-cover bg-center opacity-30"></div>
          <div class="relative p-4 flex flex-col h-32">
            <div class="flex items-center mb-2">
              <img src="/images/icons/friends.png" alt="Amis" class="w-5 h-5 mr-2">
              <h3 class="text-sm font-bold text-secondary">Amis</h3>
            </div>
            <p class="text-xs text-white/80 mb-auto">Gérer mes amis</p>
            <Button v-if="onOpenFriendsModal" @click="onOpenFriendsModal" variant="secondary" size="sm" class="w-full text-xs py-1.5">
              Voir
            </Button>
          </div>
        </div>

        <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-warning/30 to-amber-500/40 border border-warning/40">
          <div class="absolute inset-0 bg-[url('/images/background/upgrade.gif')] bg-cover bg-center opacity-40"></div>
          <div class="relative p-4 flex flex-col h-32">
            <div class="flex items-center mb-2">
              <StarsBadge :stars="6" class="scale-75" />
              <h3 class="text-sm font-bold text-white ml-1">Upgrade</h3>
            </div>
            <p class="text-xs text-white/90 mb-auto">Améliorer Pokémon</p>
            <Button @click="goToPokemonUpgrade" variant="secondary" size="sm" class="w-full text-xs py-1.5">
              Améliorer
            </Button>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-r from-secondary/10 to-accent/20 rounded-xl border border-secondary/20 p-4">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 bg-gradient-to-br from-secondary/30 to-accent/30 rounded-lg flex items-center justify-center text-sm">📚</div>
            <h3 class="font-bold bg-gradient-to-r from-secondary to-accent bg-clip-text text-transparent">Mon Pokédex</h3>
          </div>
          <span class="text-sm font-medium text-secondary">{{ pokedex.length }}</span>
        </div>
        <Button variant="secondary" size="sm" @click="onOpenPokedexModal" class="w-full bg-gradient-to-r from-secondary/10 to-accent/20 border-secondary/30 text-secondary hover:from-secondary/20 hover:to-accent/30">
          Voir mes Pokémon
        </Button>
      </div>

      <div class="grid grid-cols-2 gap-2">
        <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-primary/20 to-blue-600/30 border border-primary/30">
          <div class="absolute inset-0 bg-[url('/images/background/leaderboard.jpg')] bg-cover bg-center opacity-30"></div>
          <div class="relative p-4 flex flex-col h-32">
            <div class="flex items-center mb-2">
              <img src="/images/icons/classement.png" alt="Classement" class="w-5 h-5 mr-2">
              <h3 class="text-sm font-bold text-primary">Ranking</h3>
            </div>
            <p class="text-xs text-white/80 mb-auto">Top 100</p>
            <Button v-if="onGoToLeaderboard" @click="onGoToLeaderboard" variant="leaderboard" size="sm" class="w-full text-xs py-1.5">
              Voir
            </Button>
          </div>
        </div>

        <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-success/20 to-emerald-600/30 border border-success/30">
          <div class="absolute inset-0 bg-[url('/images/background/expeditions.gif')] bg-cover bg-center opacity-30"></div>
          <div class="relative p-4 flex flex-col h-32">
            <div class="flex items-center mb-2">
              <img src="/images/icons/treasure-map.webp" alt="Expedition" class="w-5 h-5 mr-2">
              <h3 class="text-sm font-bold text-success">Expéditions</h3>
            </div>
            <p class="text-xs text-white/80 mb-auto">Aventures</p>
            <Button v-if="onGoToExpeditions" @click="onGoToExpeditions" variant="secondary" size="sm" class="w-full text-xs py-1.5">
              Partir
            </Button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-2">
        <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-error/20 to-red-600/30 border border-error/30">
          <div class="absolute inset-0 bg-[url('/images/background/tower.jpg')] bg-cover bg-center opacity-40"></div>
          <div class="absolute inset-0 bg-black/40"></div>
          <div class="relative p-4 flex flex-col h-32">
            <div class="flex items-center mb-2">
              <span class="text-lg mr-2">🔥</span>
              <h3 class="text-sm font-bold text-error">Tour Infernale</h3>
            </div>
            <p class="text-xs text-white/80 mb-auto">Défis épiques</p>
            <Button v-if="onGoToTower" @click="onGoToTower" variant="secondary" size="sm" class="w-full text-xs py-1.5">
              Défier
            </Button>
          </div>
        </div>
      </div>

      <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/20 p-4">
        <Button variant="outline" size="md" class="w-full" @click="goToShop">
          <img src="/images/icons/shop.webp" alt="Boutique" class="w-5 h-5 mr-2" /> 
          Boutique
        </Button>
      </div>
    </div>
  </div>
  <DailyQuestsModal
    :show="dailyQuestsModalOpen"
    :onClose="() => dailyQuestsModalOpen = false"
    :initial-quests="daily_quests"
    :initial-stats="daily_quest_stats"
    @questClaimed="handleQuestClaimed"
    @bonusClaimed="handleBonusClaimed"
  />
</template>