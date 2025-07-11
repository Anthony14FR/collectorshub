<script setup lang="ts">
import { computed } from 'vue';
import Badge from '@/Components/UI/Badge.vue';
import type { User } from '@/types/user';

interface Props {
  user: User;
  responsive?: boolean;
}

const { user, responsive = false } = defineProps<Props>();

const experienceProgress = computed(() => {
  const currentLevelExp = (user.level - 1) * 1000;
  const nextLevelExp = user.level * 1000;
  const progressInLevel = user.experience - currentLevelExp;
  const expNeededForLevel = nextLevelExp - currentLevelExp;

  return Math.min((progressInLevel / expNeededForLevel) * 100, 100);
});

const experienceForNextLevel = computed(() => {
  const nextLevelExp = user.level * 1000;
  return nextLevelExp - user.experience;
});
</script>

<template>
  <div v-if="!responsive" class="absolute left-1/2 -translate-x-1/2 top-8 z-20">
    <div class="bg-gradient-to-br from-base-100/60 to-base-200/40 backdrop-blur-sm border-2 border-success/20 rounded-3xl px-16 py-6 relative overflow-hidden shadow-2xl shadow-primary/10 min-w-[500px]">
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
            <span class="text-lg font-bold text-base-content/80">{{ (user.level * 1000).toLocaleString() }}</span>
          </div>
          <div class="h-px bg-gradient-to-l from-transparent via-primary/40 to-primary/10 w-20"></div>
        </div>
      </div>

      <div class="mt-2 text-center">
        <p class="text-xs text-base-content/50">
          {{ experienceForNextLevel.toLocaleString() }} EXP pour le niveau {{ user.level + 1 }}
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

      <div class="text-center text-xs">
        <div class="mb-1">
          <span class="font-bold text-success">{{ user.experience.toLocaleString() }}</span>
          <span class="text-base-content/50 mx-1">/</span>
          <span class="font-bold text-base-content/80">{{ (user.level * 1000).toLocaleString() }}</span>
          <span class="text-base-content/50 ml-1">EXP</span>
        </div>
        <p class="text-base-content/50 text-xs">
          {{ experienceForNextLevel.toLocaleString() }} pour niveau {{ user.level + 1 }}
        </p>
      </div>
    </div>
  </div>
</template>
