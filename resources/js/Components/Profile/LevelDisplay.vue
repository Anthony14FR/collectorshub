<script setup lang="ts">
import Badge from '@/Components/UI/Badge.vue';
import Button from "@/Components/UI/Button.vue";
import Modal from "@/Components/UI/Modal.vue";
import type { AvailableLevelReward, GroupedReward, LevelRewardPreview, PreviewLevelReward, User } from '@/types/user';
import { router } from "@inertiajs/vue3";
import { computed, ref } from 'vue';

interface Props {
  user: User;
  responsive?: boolean;
  level_rewards_to_claim?: AvailableLevelReward[];
  level_rewards_preview?: LevelRewardPreview;
  unreadNotificationsCount?: number;
}

const { user, level_rewards_to_claim = [], level_rewards_preview, unreadNotificationsCount = 0 } = defineProps<Props>();

const experienceProgress = computed(() => {
  return user.experience_percentage;
});

const hasAvailableReward = computed(() => {
  return level_rewards_to_claim.length > 0;
});

const claimReward = (level: number, type: string) => {
  router.post(
    "/level-rewards/claim",
    {
      level,
      type
    },
    {
      onSuccess: () => { },
      onError: () => { },
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
      onError: () => { },
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

const formatRewardType = (type: string): string => {
  return type.replace('_', ' ').replace('milestone', 'Palier').replace('regular level', 'Niveau standard');
};

function groupRewardsByLevel(rewards: PreviewLevelReward[]): GroupedReward[] {
  const grouped: Record<number, GroupedReward> = {};

  rewards.forEach(reward => {
    if (!grouped[reward.level]) {
      grouped[reward.level] = {
        level: reward.level,
        types: [],
        cash: 0,
        pokeballs: 0,
        masterballs: 0,
        is_claimed: reward.is_claimed ?? false,
      };
    }
    grouped[reward.level].types.push(reward.type);
    grouped[reward.level].cash += reward.cash || 0;
    grouped[reward.level].pokeballs += reward.pokeballs || 0;
    grouped[reward.level].masterballs += reward.masterballs || 0;
  });

  return Object.values(grouped).sort((a, b) => a.level - b.level);
}

const groupedPrevious = computed(() =>
  level_rewards_preview?.previous ? groupRewardsByLevel(level_rewards_preview.previous) : []
);

const groupedNext = computed(() =>
  level_rewards_preview?.next ? groupRewardsByLevel(level_rewards_preview.next) : []
);
</script>

<template>
  <div>
    <div class="z-20">
      <div
        class="bg-gradient-to-br from-base-100/60 to-base-200/40 backdrop-blur-sm border-2 border-success/20 px-12 py-4 relative overflow-hidden shadow-2xl shadow-primary/10 min-w-[300px] xl:min-w-[500px] sm:min-w-[350px]">
        <div class="absolute top-3 left-3 z-20">
          <div class="relative">
            <Button @click="router.visit('/notifications')" variant="secondary" size="sm"
                    class="relative group rounded-lg shadow-lg shadow-secondary/10">
              <span class="text-sm">📬</span>
            </Button>
            <div v-if="unreadNotificationsCount > 0" class="absolute -top-1 -right-1">
              <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-error opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-error"></span>
              </span>
            </div>
          </div>
        </div>

        <div class="absolute top-3 right-3 z-20">
          <div class="relative">
            <Button @click="openModal" variant="secondary" size="sm"
                    class="relative group rounded-lg shadow-lg shadow-primary/10">
              <span class="text-sm">🎁</span>
            </Button>
            <div v-if="hasAvailableReward" class="absolute -top-1 -right-1">
              <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-error opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-error"></span>
              </span>
            </div>
          </div>
        </div>
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
          <div class="absolute top-3 left-6 w-1 h-1 bg-success rounded-full animate-pulse opacity-60"></div>
          <div class="absolute top-6 right-8 w-1.5 h-1.5 bg-primary rounded-full animate-pulse delay-300 opacity-40">
          </div>
          <div class="absolute bottom-4 left-10 w-1 h-1 bg-accent rounded-full animate-pulse delay-700 opacity-50">
          </div>
          <div class="absolute top-8 right-12 w-2 h-2 bg-secondary rounded-full animate-pulse delay-500 opacity-30">
          </div>
        </div>
        <div class="relative z-10 text-center">
          <Badge variant="success" size="sm" pill class="shadow-2xl shadow-primary/20">
            <div class="w-2 h-2 bg-success rounded-full animate-pulse"></div>
            <span class="font-bold tracking-wider text-sm">NIVEAU {{ user.level }}</span>
            <div class="w-2 h-2 bg-success rounded-full animate-pulse"></div>
          </Badge>
        </div>
        <div class="mt-4 mb-3">
          <div class="h-2 bg-base-200/50 rounded-full overflow-hidden relative">
            <div
              class="h-full bg-gradient-to-r from-success via-primary to-secondary transition-all duration-1000 ease-out relative group"
              :style="{ width: experienceProgress + '%' }">
              <div
                class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
              </div>
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
              <span class="text-lg font-bold text-base-content/80">{{ user.experience_for_next_level.toLocaleString()
              }}</span>
            </div>
            <div class="h-px bg-gradient-to-l from-transparent via-primary/40 to-primary/10 w-20"></div>
          </div>
        </div>
        <div class="mt-2 text-center">
          <p class="text-xs text-base-content/50" v-if="user.level < 100">
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
    <Modal :show="modalOpen" @close="closeModal" max-width="4xl">
      <template #header>
        <div class="flex items-center gap-3">
          <div
            class="w-8 h-8 bg-gradient-to-br from-warning/20 to-warning/40 rounded-lg flex items-center justify-center">
            <span class="text-lg">🏆</span>
          </div>
          <div class="flex flex-col">
            <h3
              class="sm:text-xl text-sm font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
              Récompenses de Niveau
            </h3>
            <div class="mt-1">
              <span class="sm:text-sm text-xs font-semibold text-warning">{{ level_rewards_to_claim.length }}
                récompenses disponibles</span>
            </div>
          </div>
        </div>
      </template>
      <template #default>
        <div class="space-y-6">
          <div v-if="level_rewards_to_claim.length > 0" class="flex justify-center">
            <Button @click="claimAllRewards" variant="primary" class="w-full max-w-xs">
              <span class="text-sm sm:text-xl mr-2">⚡</span>
              Tout réclamer ({{ level_rewards_to_claim.length }})
            </Button>
          </div>
          <div v-if="level_rewards_to_claim.length > 0">
            <h4 class="text-lg font-semibold mb-4 text-warning">Récompenses Disponibles</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-for="reward in level_rewards_to_claim" :key="`available-${reward.types.join('-')}-${reward.level}`"
                   class="bg-base-200/30 backdrop-blur-sm rounded-xl p-4 border border-base-300/20 hover:border-warning/40 transition-all duration-200 group">
                <div class="flex items-start gap-3">
                  <div
                    class="w-14 h-14 shrink-0 bg-warning/20 rounded-lg overflow-hidden flex items-center justify-center animate-pulse">
                    <span class="text-2xl">🎁</span>
                  </div>
                  <div class="flex-1">
                    <h3 class="font-bold text-base-content group-hover:text-warning transition-colors">
                      Niveau {{ reward.level }}
                    </h3>
                    <p class="text-xs text-base-content/70 mb-3 capitalize">
                      <span v-for="(type, idx) in reward.types" :key="type">
                        {{ formatRewardType(type) }}
                        <span v-if="idx < reward.types.length - 1"> </span>
                      </span>
                    </p>
                    <div class="flex justify-between items-center mb-3">
                      <div class="flex gap-2 flex-wrap">
                        <span class="text-xs px-2 py-0.5 bg-success/10 text-success rounded-full">
                          +{{ reward.cash.toLocaleString() }} Cash
                        </span>
                        <span v-if="reward.pokeballs > 0"
                              class="text-xs px-2 py-0.5 bg-primary/10 text-primary rounded-full">
                          +{{ reward.pokeballs }} Pokéballs
                        </span>
                        <span v-if="reward.masterballs > 0"
                              class="text-xs px-2 py-0.5 bg-accent/10 text-accent rounded-full">
                          +{{ reward.masterballs }} Masterballs
                        </span>
                      </div>
                    </div>
                    <Button @click="() => claimReward(reward.level, reward.types[0])" variant="outline" size="sm"
                            class="w-full">
                      Récupérer
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-if="level_rewards_preview?.next && level_rewards_preview.next.length > 0">
            <h4 class="sm:text-lg text-sm font-semibold mb-4 text-primary">Prochaines Récompenses</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-for="reward in groupedNext.slice(0, 4)" :key="`next-${reward.types[0]}-${reward.level}`"
                   class="bg-base-200/30 backdrop-blur-sm rounded-xl p-4 border border-base-300/20 hover:border-primary/40 transition-all duration-200 group opacity-60">
                <div class="flex items-start gap-3">
                  <div
                    class="w-14 h-14 shrink-0 bg-primary/20 rounded-lg overflow-hidden flex items-center justify-center">
                    <span class="text-2xl opacity-60">🎁</span>
                  </div>
                  <div class="flex-1">
                    <h3 class="font-bold text-base-content group-hover:text-primary transition-colors">
                      Niveau {{ reward.level }}
                    </h3>
                    <p class="text-xs text-base-content/70 mb-2 capitalize">
                      <span v-for="(type, idx) in reward.types" :key="type">
                        {{ formatRewardType(type) }}
                        <span v-if="idx < reward.types.length - 1"> </span>
                      </span>
                    </p>
                    <div class="flex justify-between items-center">
                      <div class="flex gap-2 flex-wrap">
                        <span
                          class="sm:text-xs text-[10px] px-2 py-0.5 bg-base-content/10 text-base-content/60 rounded-full">
                          {{ reward.cash.toLocaleString() }} Cash
                        </span>
                        <span v-if="reward.pokeballs > 0"
                              class="sm:text-xs text-[10px] px-2 py-0.5 bg-base-content/10 text-base-content/60 rounded-full">
                          {{ reward.pokeballs }} PB
                        </span>
                        <span v-if="reward.masterballs > 0"
                              class="sm:text-xs text-[10px] px-2 py-0.5 bg-base-content/10 text-base-content/60 rounded-full">
                          {{ reward.masterballs }} MB
                        </span>
                      </div>
                      <span class="sm:text-xs text-[10px] px-2 py-0.5 bg-primary/10 text-primary rounded-full">
                        Niveau {{ reward.level }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-base-100/50 rounded-xl p-4"
               v-if="level_rewards_preview?.previous && level_rewards_preview.previous.length > 0">
            <h4 class="sm:text-lg text-sm font-semibold mb-4 text-success">Récemment Réclamées</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-for="reward in groupedPrevious.slice(-4)" :key="`claimed-${reward.types[0]}-${reward.level}`"
                   class="bg-base-300/50 backdrop-blur-sm rounded-xl p-4 border border-base-300/20 hover:border-success/40 transition-all duration-200 group opacity-80">
                <div class="flex items-start gap-3">
                  <div
                    class="w-14 h-14 shrink-0 bg-success/20 rounded-lg overflow-hidden flex items-center justify-center">
                    <span class="text-2xl opacity-70">🎁</span>
                  </div>
                  <div class="flex-1">
                    <h3 class="font-bold text-base-content group-hover:text-success transition-colors">
                      Niveau {{ reward.level }}
                    </h3>
                    <p class="text-xs text-base-content/70 mb-2 capitalize">
                      <span v-for="(type, idx) in reward.types" :key="type">
                        {{ formatRewardType(type) }}
                        <span v-if="idx < reward.types.length - 1"> </span>
                      </span>
                    </p>
                    <div class="flex justify-between items-center">
                      <div class="flex gap-2 flex-wrap">
                        <span
                          class="sm:text-xs text-[10px] px-2 py-0.5 bg-base-content/10 text-base-content rounded-full">
                          {{ reward.cash.toLocaleString() }} Cash
                        </span>
                        <span v-if="reward.pokeballs > 0"
                              class="sm:text-xs text-[10px] px-2 py-0.5 bg-base-content/10 text-base-content rounded-full">
                          {{ reward.pokeballs }} PB
                        </span>
                        <span v-if="reward.masterballs > 0"
                              class="sm:text-xs text-[10px] px-2 py-0.5 bg-base-content/10 text-base-content rounded-full">
                          {{ reward.masterballs }} MB
                        </span>
                      </div>
                      <span class="sm:text-xs text-[10px] px-2 py-0.5 bg-success/10 text-success rounded-full">
                        Réclamé
                      </span>
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
