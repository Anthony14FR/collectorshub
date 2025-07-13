<script setup lang="ts">
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import Badge from "@/Components/UI/Badge.vue";
import Button from "@/Components/UI/Button.vue";
import Modal from "@/Components/UI/Modal.vue";
import type { User, LevelReward } from "@/types/user";

interface Props {
  user: User;
  responsive?: boolean;
  level_rewards_to_claim?: LevelReward[];
}

const {
  user,
  responsive = false,
  level_rewards_to_claim = [],
} = defineProps<Props>();

const experienceProgress = computed(() => {
  return user.experience_percentage;
});

const experienceForNextLevel = computed(() => {
  return user.experience_for_next_level - user.experience;
});

const hasAvailableReward = computed(() => {
  return level_rewards_to_claim.length > 0;
});

const getMainRewardType = computed(() => {
  const level = user.level;
  if (level % 50 === 0) return "milestone_50";
  if (level % 25 === 0) return "milestone_25";
  if (level % 10 === 0) return "milestone_10";
  if (level % 5 === 0) return "milestone_5";
  return "regular_level";
});

const claimReward = (level, type) => {
  router.post(
    "/level-rewards/claim",
    {
      level,
      type
    },
    {
      onSuccess: () => {},
      onError: (errors) => {},
    },
  );
};

const claimAllRewards = () => {
  router.post(
    "/level-rewards/claim-all",
    {},
    {
      onSuccess: () => {
        modalOpen.value = false;
      },
      onError: (errors) => {},
    },
  );
};

const modalOpen = ref(false);

const openModal = () => {
  modalOpen.value = true;
};

const closeModal = () => {
  modalOpen.value = false;
};

const openTiers = ref(new Set());

const toggleTier = (tier: number) => {
  if (openTiers.value.has(tier)) {
    openTiers.value.delete(tier);
  } else {
    openTiers.value.add(tier);
  }
};

const isTierOpen = (tier: number) => {
  return openTiers.value.has(tier);
};

const rewardsByTier = computed(() => {
  const tiers = {};

  level_rewards_to_claim.forEach((reward) => {
    const tier = Math.floor((reward.level - 1) / 50) + 1;
    const tierStart = (tier - 1) * 50 + 1;
    const tierEnd = tier * 50;

    if (!tiers[tier]) {
      tiers[tier] = {
        range: `${tierStart}-${tierEnd}`,
        rewards: [],
      };
    }

    tiers[tier].rewards.push(reward);
  });

  return Object.entries(tiers)
    .map(([tier, data]) => ({
      tier: parseInt(tier),
      ...data,
    }))
    .sort((a, b) => a.tier - b.tier);
});

const getRewardIcon = (type: string) => {
  switch (type) {
  case "milestone_5":
    return "🎯";
  case "milestone_10":
    return "⭐";
  case "milestone_25":
    return "💎";
  case "milestone_50":
    return "👑";
  default:
    return "🎁";
  }
};

const getRewardColor = (type: string) => {
  switch (type) {
  case "milestone_5":
    return "from-blue-500 to-blue-600";
  case "milestone_10":
    return "from-purple-500 to-purple-600";
  case "milestone_25":
    return "from-pink-500 to-pink-600";
  case "milestone_50":
    return "from-yellow-500 to-yellow-600";
  default:
    return "from-gray-500 to-gray-600";
  }
};
</script>

<template>
  <div>
    <div
      v-if="!responsive"
      class="absolute left-1/2 -translate-x-1/2 top-8 z-20"
    >
      <div class="bg-gradient-to-br from-base-100/60 to-base-200/40 backdrop-blur-sm border-2 border-success/20 rounded-3xl px-16 py-6 relative overflow-hidden shadow-2xl shadow-primary/10 min-w-[500px]">
        <div class="absolute top-3 right-3 z-20">
          <Button
            @click="openModal"
            :disabled="!hasAvailableReward"
            variant="primary"
            size="sm"
            class="relative group"
            :class="{
              'opacity-50 cursor-not-allowed':
                !hasAvailableReward,
            }"
          >
            <span class="text-xl">🎁</span>
          </Button>
          <div
            v-if="hasAvailableReward"
            class="absolute -top-1 -right-1"
          >
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-error opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-error"></span>
            </span>
          </div>
        </div>

        <div class="absolute inset-0 overflow-hidden pointer-events-none">
          <div class="absolute top-3 left-6 w-1 h-1 bg-success rounded-full animate-pulse opacity-60"></div>
          <div class="absolute top-6 right-8 w-1.5 h-1.5 bg-primary rounded-full animate-pulse delay-300 opacity-40"></div>
          <div class="absolute bottom-4 left-10 w-1 h-1 bg-accent rounded-full animate-pulse delay-700 opacity-50"></div>
          <div class="absolute top-8 right-12 w-2 h-2 bg-secondary rounded-full animate-pulse delay-500 opacity-30"></div>
        </div>

        <div class="relative z-10 text-center">
          <Badge
            variant="success"
            size="lg"
            pill
            class="shadow-2xl shadow-primary/20"
          >
            <div class="w-2 h-2 bg-success rounded-full animate-pulse"></div>
            <span class="font-bold tracking-wider text-sm">NIVEAU {{ user.level }}</span>
            <div class="w-2 h-2 bg-success rounded-full animate-pulse"></div>
          </Badge>
        </div>

        <div class="mt-4 mb-3">
          <div class="h-2 bg-base-200/50 rounded-full overflow-hidden relative">
            <div
              class="h-full bg-gradient-to-r from-success via-primary to-secondary transition-all duration-1000 ease-out relative group"
              :style="{ width: experienceProgress + '%' }"
            >
              <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-center text-xs gap-4">
          <div class="flex items-center gap-2">
            <div class="h-px bg-gradient-to-r from-transparent via-primary/40 to-primary/10 w-20"></div>
            <div class="text-center">
              <span class="text-lg font-bold bg-gradient-to-r from-success to-primary bg-clip-text text-transparent">
                {{ user.experience.toLocaleString() }}
              </span>
            </div>
          </div>

          <div class="text-2xl font-bold text-base-content/30">/</div>

          <div class="flex items-center gap-2">
            <div class="text-center">
              <span class="text-lg font-bold text-base-content/80">{{user.experience_for_next_level.toLocaleString()}}</span>
            </div>
            <div class="h-px bg-gradient-to-l from-transparent via-primary/40 to-primary/10 w-20"></div>
          </div>
        </div>

        <div class="mt-2 text-center">
          <p
            class="text-xs text-base-content/50"
            v-if="user.level < 100"
          >
            Encore
            {{
              (user.experience_for_next_level - user.experience).toLocaleString()
            }}
            EXP pour le niveau {{ user.level + 1 }}
          </p>
          <p class="text-xs text-base-content/50" v-else>
            Niveau maximum atteint
          </p>
        </div>
      </div>
    </div>

    <div v-else class="w-full max-w-sm mx-auto">
      <div class="bg-gradient-to-br from-base-100/60 to-base-200/40 backdrop-blur-sm border-2 border-success/20 rounded-2xl px-6 py-4 relative overflow-hidden shadow-xl shadow-primary/10">
        <div class="text-center mb-3">
          <Badge
            variant="success"
            size="md"
            pill
            class="shadow-lg shadow-primary/20"
          >
            <div class="w-1.5 h-1.5 bg-success rounded-full animate-pulse"></div>
            <span class="font-bold tracking-wider text-xs">NIVEAU {{ user.level }}</span>
            <div class="w-1.5 h-1.5 bg-success rounded-full animate-pulse"></div>
          </Badge>
        </div>

        <div class="mb-3">
          <div class="h-1.5 bg-base-200/50 rounded-full overflow-hidden relative">
            <div
              class="h-full bg-gradient-to-r from-success via-primary to-secondary transition-all duration-1000 ease-out"
              :style="{ width: experienceProgress + '%' }"
            ></div>
          </div>
        </div>

        <div class="mb-3 flex justify-center">
          <Button
            @click="openModal"
            :disabled="!hasAvailableReward"
            variant="warning"
            size="xs"
            class="relative"
            :class="{
              'opacity-50 cursor-not-allowed':
                !hasAvailableReward,
            }"
          >
            <div class="flex items-center gap-1">
              <span class="text-sm">🎁</span>
              <span class="text-xs font-bold">RÉCOMPENSE</span>
              <div
                v-if="hasAvailableReward"
                class="w-1.5 h-1.5 bg-warning rounded-full animate-pulse"
              ></div>
            </div>
            <div
              v-if="hasAvailableReward"
              class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-warning rounded-full animate-ping"
            ></div>
          </Button>
        </div>

        <div class="text-center text-xs">
          <div class="mb-1">
            <span class="font-bold text-success">{{user.experience.toLocaleString()}}</span>
            <span class="text-base-content/50 mx-1">/</span>
            <span class="font-bold text-base-content/80">{{user.experience_for_next_level.toLocaleString()}}</span>
            <span class="text-base-content/50 ml-1">EXP</span>
          </div>
          <p
            class="text-base-content/50 text-xs"
            v-if="user.level < 100"
          >
            {{
              (user.experience_for_next_level - user.experience).toLocaleString()
            }}
            pour niveau {{ user.level + 1 }}
          </p>
          <p class="text-base-content/50 text-xs" v-else>
            Niveau maximum atteint
          </p>
        </div>
      </div>
    </div>

    <Modal :show="modalOpen" @close="closeModal" max-width="2xl">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-gradient-to-br from-warning/20 to-warning/40 rounded-lg flex items-center justify-center">
            <span class="text-lg">🎁</span>
          </div>
          <div class="flex flex-col">
            <h3 class="text-xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
              Récompenses de Niveau
            </h3>
            <div class="mt-1">
              <span class="text-sm font-semibold text-warning">{{ level_rewards_to_claim.length }} récompenses disponibles</span>
            </div>
          </div>
        </div>
      </template>

      <template #default>
        <div class="space-y-6">
          <div class="flex justify-center">
            <Button
              @click="claimAllRewards"
              variant="success"
              size="lg"
              class="shadow-lg"
            >
              <span class="text-lg mr-2">🎯</span>
              <span class="font-bold">Tout Récupérer</span>
            </Button>
          </div>

          <div class="space-y-4 max-h-96 overflow-y-auto">
            <div
              v-for="tier in rewardsByTier"
              :key="tier.tier"
              class="bg-gradient-to-br from-base-200/50 to-base-300/30 rounded-xl border border-base-300"
            >
              <div
                class="flex items-center justify-between p-4 cursor-pointer hover:bg-base-200/30 transition-colors rounded-t-xl"
                @click="toggleTier(tier.tier)"
              >
                <div class="flex items-center gap-3">
                  <h4 class="text-lg font-bold text-base-content">
                    Niveaux {{ tier.range }}
                  </h4>
                  <Badge variant="primary" size="sm">
                    {{ tier.rewards.length }} récompenses
                  </Badge>
                </div>
                <div class="flex items-center gap-2">
                  <span class="text-sm text-base-content/60">
                    {{
                      isTierOpen(tier.tier)
                        ? "Masquer"
                        : "Afficher"
                    }}
                  </span>
                  <div
                    class="w-5 h-5 flex items-center justify-center transition-transform duration-200"
                    :class="{
                      'rotate-180': isTierOpen(tier.tier),
                    }"
                  >
                    <span class="text-lg">▼</span>
                  </div>
                </div>
              </div>

              <div
                v-show="isTierOpen(tier.tier)"
                class="p-4 pt-0 space-y-2 border-t border-base-300/50"
              >
                <div
                  v-for="reward in tier.rewards"
                  :key="`${reward.type}_${reward.level}`"
                  class="flex items-center justify-between p-3 rounded-lg bg-gradient-to-r"
                  :class="getRewardColor(reward.type)"
                >
                  <div class="flex items-center justify-between p-3 rounded-lg bg-gradient-to-r" :class="getRewardColor(reward.type)">
                    <div class="flex items-center gap-2 w-48 min-w-[12rem]">
                      <span class="text-2xl">{{ getRewardIcon(reward.type) }}</span>
                      <div>
                        <div class="font-bold text-white">Niveau {{ reward.level }}</div>
                        <div class="text-sm text-white/80">
                          {{ reward.type.replace('_', ' ').replace('milestone', 'Palier').replace('regular level', 'Niveau standard') }}
                        </div>
                      </div>
                    </div>
                    <div class="text-center w-20">
                      <div class="font-bold text-white">{{ reward.cash ? reward.cash.toLocaleString() + '$' : '0' }}</div>
                      <div class="text-xs opacity-80">Cash</div>
                    </div>
                    <div class="text-center w-20">
                  <div class="font-bold text-white">{{ reward.pokeballs ? reward.pokeballs : '0' }}</div>
    <div class="text-xs opacity-80">Pokéballs</div>
  </div>
  <div class="text-center w-20">
    <div class="font-bold text-white">{{ reward.masterballs ? reward.masterballs : '0' }}</div>
                      <div class="text-xs opacity-80">Masterballs</div>
                    </div>
                    <div class="flex justify-end w-28">
    <Button
      @click="() => claimReward(reward.level, reward.type)"
      variant="primary"
      size="sm"
      class="bg-white/20 hover:bg-white/30 text-white border-white/30"
    >
      Récupérer
    </Button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </Modal>
  </div>
</template>

<style></style>
