<script setup lang="ts">
import { computed } from 'vue';
import CPBadge from '@/Components/UI/CPBadge.vue';
import TeamPokemonCard from '@/Components/Profile/TeamPokemonCard.vue';
import type { LeaderboardUser } from '@/types/leaderboard';
import { Link } from '@inertiajs/vue3';

interface Props {
  user: LeaderboardUser;
  position: 1 | 2 | 3;
  type: 'level' | 'cp' | 'pokemon' | 'club';
}

const { user, position, type } = defineProps<Props>();

const podiumHeight = computed(() => {
  switch(position) {
  case 1: return 'h-32';
  case 2: return 'h-24';
  case 3: return 'h-20';
  }
});

const podiumColor = computed(() => {
  switch(position) {
  case 1: return 'bg-gradient-to-t from-warning/30 to-warning/20 border-warning/50';
  case 2: return 'bg-gradient-to-t from-base-300/40 to-base-300/20 border-base-300/50';
  case 3: return 'bg-gradient-to-t from-accent/30 to-accent/20 border-accent/50';
  }
});

const medalImage = computed(() => {
  switch(position) {
  case 1: return '/images/icons/gold-medal.png';
  case 2: return '/images/icons/silver-medal.png';
  case 3: return '/images/icons/bronze-medal.png';
  }
});

const getValue = computed(() => {
  switch(type) {
  case 'level': return user.level;
  case 'cp': return user.team_cp || 0;
  case 'pokemon': return user.value || 0;
  case 'club': return user.total_cp || 0;
  }
});

const getValueLabel = computed(() => {
  switch(type) {
  case 'level': return 'Niv.';
  case 'cp': return 'CP';
  case 'pokemon': return 'Pokémon';
  case 'club': return 'CP';
  }
});

const avatarSize = computed(() => {
  switch(position) {
  case 1: return 'w-20 h-20 sm:w-24 sm:h-24';
  case 2: return 'w-16 h-16 sm:w-20 sm:h-20';
  case 3: return 'w-14 h-14 sm:w-18 sm:h-18';
  }
});
</script>

<template>
  <div class="flex flex-col items-center">
    <div class="mb-4 flex flex-col items-center">
      <div class="relative mb-3">
        <div :class="['relative overflow-hidden rounded-full border-2 border-base-300/50 shadow-lg', avatarSize]">
          <div
            v-if="user.background"
            class="absolute inset-0 w-full h-full rounded-full overflow-hidden"
            :style="{
              backgroundImage: `url(${user.background})`,
              backgroundSize: 'cover',
              backgroundPosition: 'center',
              backgroundRepeat: 'no-repeat'
            }"
          >
            <div class="absolute inset-0 bg-black/40 rounded-full"></div>
          </div>

          <div
            v-else
            class="absolute inset-0 w-full h-full rounded-full bg-gradient-to-br from-primary/20 to-secondary/20"
          ></div>

          <img
            :src="user.avatar || `/images/trainer/${(user.id % 10) + 1}.png`"
            :alt="user.username"
            class="relative z-10 w-full h-full object-cover rounded-full"
            style="image-rendering: pixelated;"
          />
        </div>
        <div class="absolute -top-2 -right-4">
          <img
            :src="medalImage"
            :alt="`Médaille position ${position}`"
            class="w-8 h-8 sm:w-10 sm:h-10 object-contain"
          />
        </div>
      </div>

      <div :class="['text-center', position === 1 ? 'sm:mb-8 mb-0' : '']">
        <div :class="['font-bold text-base-content mb-1', position === 1 ? 'text-base sm:text-lg' : 'text-sm sm:text-base']">
          <template v-if="type === 'club'">
            <Link :href="`/club/${user.id}`" class="text-primary hover:underline">
              {{ user.name || user.username }}
            </Link>
          </template>
          <template v-else>
            <Link :href="`/profile/${user.username}`" class="text-primary hover:underline">
              {{ user.username }}
            </Link>
          </template>
        </div>
        <div class="text-xs text-base-content/70 mb-2">
          {{ getValueLabel }} {{ getValue.toLocaleString() }}
        </div>

        <div v-if="type === 'cp' && user.team_pokemons" class="space-y-2">
          <CPBadge :cp="user.team_cp || 0" size="sm" variant="gradient" />
          <div class="grid grid-cols-3 gap-2 max-w-[180px] sm:max-w-[240px] mx-auto">
            <TeamPokemonCard
              v-for="(pokemon, index) in user.team_pokemons.slice(0, 6)"
              :key="index"
              :pokemon="pokemon"
              class="!w-12 !h-12 sm:!w-16 sm:!h-16 md:!w-20 md:!h-20"
            />
            <div
              v-for="index in Math.max(0, 6 - user.team_pokemons.length)"
              :key="`placeholder-${index}`"
              class="w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-base-200/40 backdrop-blur-sm rounded-md flex items-center justify-center border border-base-300/50 opacity-50"
            >
              <img src="/images/items/pokeball.png" alt="placeholder" class="w-4 h-4 sm:w-6 sm:h-6 md:w-8 md:h-8 object-contain opacity-50" />
            </div>
          </div>
        </div>

        <div v-if="type === 'club'" class="space-y-2">
          <CPBadge :cp="user.total_cp || 0" size="sm" variant="gradient" />
        </div>
      </div>
    </div>
  </div>
</template>
