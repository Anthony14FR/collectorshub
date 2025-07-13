<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import Badge from "@/Components/UI/Badge.vue";
import Button from "@/Components/UI/Button.vue";
import type { User, LevelReward } from "@/types/user";

interface Props {
  user: User;
  responsive?: boolean;
}

const { user, responsive = false } = defineProps<Props>();
const page = usePage();

const flashSuccess = ref(page.props.flash?.success || null);

watch(
  () => page.props.flash?.success,
  (val) => {
    flashSuccess.value = val;
    if (val) {
      setTimeout(() => {
        flashSuccess.value = null;
      }, 3000);
    }
  }
);

const experienceProgress = computed(() => {
  return user.experience_percentage;
});

const experienceForNextLevel = computed(() => {
  return user.experience_for_next_level - user.experience;
});

const hasAvailableReward = computed(() => {
  const level = user.level;
  const claimedRewards = user.claimed_level_rewards || [];

  const milestones = [];

  if (level % 5 === 0) {
    milestones.push(`milestone_5_${level}`);
  }
  if (level % 10 === 0) {
    milestones.push(`milestone_10_${level}`);
  }
  if (level % 25 === 0) {
    milestones.push(`milestone_25_${level}`);
  }
  if (level % 50 === 0) {
    milestones.push(`milestone_50_${level}`);
  }
  if (
    level % 5 !== 0 &&
    level % 10 !== 0 &&
    level % 25 !== 0 &&
    level % 50 !== 0
  ) {
    milestones.push(`regular_level_${level}`);
  }

  return milestones.some((milestone) => !claimedRewards.includes(milestone));
});

const getMainRewardType = computed(() => {
  const level = user.level;
  if (level % 50 === 0) return "milestone_50";
  if (level % 25 === 0) return "milestone_25";
  if (level % 10 === 0) return "milestone_10";
  if (level % 5 === 0) return "milestone_5";
  return "regular_level";
});

const claimReward = () => {
  router.post(
    "/level-rewards/claim",
    {
      level: user.level,
      type: getMainRewardType.value,
    },
    {
      onSuccess: () => {
      },
      onError: (errors) => {
      },
    }
  );
};
</script>

<template>
  <div>
    <div
      v-if="flashSuccess"
      class="fixed top-4 left-1/2 -translate-x-1/2 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50"
    >
      {{ flashSuccess }}
    </div>

    <div
      v-if="!responsive"
      class="absolute left-1/2 -translate-x-1/2 top-8 z-20"
    >
      <div
        class="bg-gradient-to-br from-base-100/60 to-base-200/40 backdrop-blur-sm border-2 border-success/20 rounded-3xl px-16 py-6 relative overflow-hidden shadow-2xl shadow-primary/10 min-w-[500px]"
      >
        <div class="absolute top-3 right-3 z-20">
          <Button
            @click="claimReward"
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
              <span
                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-error opacity-75"
              ></span>
              <span
                class="relative inline-flex rounded-full h-3 w-3 bg-error"
              ></span>
            </span>
          </div>
        </div>

        <div
          class="absolute inset-0 overflow-hidden pointer-events-none"
        >
          <div
            class="absolute top-3 left-6 w-1 h-1 bg-success rounded-full animate-pulse opacity-60"
          ></div>
          <div
            class="absolute top-6 right-8 w-1.5 h-1.5 bg-primary rounded-full animate-pulse delay-300 opacity-40"
          ></div>
          <div
            class="absolute bottom-4 left-10 w-1 h-1 bg-accent rounded-full animate-pulse delay-700 opacity-50"
          ></div>
          <div
            class="absolute top-8 right-12 w-2 h-2 bg-secondary rounded-full animate-pulse delay-500 opacity-30"
          ></div>
        </div>

        <div class="relative z-10 text-center">
          <Badge
            variant="success"
            size="lg"
            pill
            class="shadow-2xl shadow-primary/20"
          >
            <div
              class="w-2 h-2 bg-success rounded-full animate-pulse"
            ></div>
            <span class="font-bold tracking-wider text-sm"
            >NIVEAU {{ user.level }}</span
            >
            <div
              class="w-2 h-2 bg-success rounded-full animate-pulse"
            ></div>
          </Badge>
        </div>

        <div class="mt-4 mb-3">
          <div
            class="h-2 bg-base-200/50 rounded-full overflow-hidden relative"
          >
            <div
              class="h-full bg-gradient-to-r from-success via-primary to-secondary transition-all duration-1000 ease-out relative group"
              :style="{ width: experienceProgress + '%' }"
            >
              <div
                class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"
              ></div>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-center text-xs gap-4">
          <div class="flex items-center gap-2">
            <div
              class="h-px bg-gradient-to-r from-transparent via-primary/40 to-primary/10 w-20"
            ></div>
            <div class="text-center">
              <span
                class="text-lg font-bold bg-gradient-to-r from-success to-primary bg-clip-text text-transparent"
              >
                {{ user.experience.toLocaleString() }}
              </span>
            </div>
          </div>

          <div class="text-2xl font-bold text-base-content/30">/</div>

          <div class="flex items-center gap-2">
            <div class="text-center">
              <span
                class="text-lg font-bold text-base-content/80"
              >{{
                user.experience_for_next_level.toLocaleString()
              }}</span
              >
            </div>
            <div
              class="h-px bg-gradient-to-l from-transparent via-primary/40 to-primary/10 w-20"
            ></div>
          </div>
        </div>

        <div class="mt-2 text-center">
          <p
            class="text-xs text-base-content/50"
            v-if="user.level < 100"
          >
            Encore
            {{
              (
                user.experience_for_next_level - user.experience
              ).toLocaleString()
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
      <div
        class="bg-gradient-to-br from-base-100/60 to-base-200/40 backdrop-blur-sm border-2 border-success/20 rounded-2xl px-6 py-4 relative overflow-hidden shadow-xl shadow-primary/10"
      >
        <div class="text-center mb-3">
          <Badge
            variant="success"
            size="md"
            pill
            class="shadow-lg shadow-primary/20"
          >
            <div
              class="w-1.5 h-1.5 bg-success rounded-full animate-pulse"
            ></div>
            <span class="font-bold tracking-wider text-xs"
            >NIVEAU {{ user.level }}</span
            >
            <div
              class="w-1.5 h-1.5 bg-success rounded-full animate-pulse"
            ></div>
          </Badge>
        </div>

        <div class="mb-3">
          <div
            class="h-1.5 bg-base-200/50 rounded-full overflow-hidden relative"
          >
            <div
              class="h-full bg-gradient-to-r from-success via-primary to-secondary transition-all duration-1000 ease-out"
              :style="{ width: experienceProgress + '%' }"
            ></div>
          </div>
        </div>

        <!-- Bouton de récompense (responsive) -->
        <div class="mb-3 flex justify-center">
          <Button
            @click="claimReward"
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
            <span class="font-bold text-success">{{
              user.experience.toLocaleString()
            }}</span>
            <span class="text-base-content/50 mx-1">/</span>
            <span class="font-bold text-base-content/80">{{
              user.experience_for_next_level.toLocaleString()
            }}</span>
            <span class="text-base-content/50 ml-1">EXP</span>
          </div>
          <p
            class="text-base-content/50 text-xs"
            v-if="user.level < 100"
          >
            {{
              (
                user.experience_for_next_level - user.experience
              ).toLocaleString()
            }}
            pour niveau {{ user.level + 1 }}
          </p>
          <p class="text-base-content/50 text-xs" v-else>
            Niveau maximum atteint
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
