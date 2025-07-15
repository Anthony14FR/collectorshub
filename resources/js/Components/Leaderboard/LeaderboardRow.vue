<script setup lang="ts">
import { computed } from 'vue';
import CPBadge from '@/Components/UI/CPBadge.vue';
import TeamPokemonCard from '@/Components/Profile/TeamPokemonCard.vue';
import type { LeaderboardUser } from '@/types/leaderboard';

interface Props {
  user: LeaderboardUser;
  type: 'level' | 'cp' | 'pokemon';
  isCurrentUser?: boolean;
}

const { user, type, isCurrentUser = false } = defineProps<Props>();

const getValue = computed(() => {
  switch(type) {
  case 'level': return user.level;
  case 'cp': return user.team_cp || 0;
  case 'pokemon': return user.value || 0;
  }
});

const getValueLabel = computed(() => {
  switch(type) {
  case 'level': return 'Niveau';
  case 'cp': return 'CP Total';
  case 'pokemon': return 'Pokémon';
  }
});

const getRankColor = computed(() => {
  if (user.rank <= 3) {
    switch(user.rank) {
    case 1: return 'text-warning';
    case 2: return 'text-base-content/70';
    case 3: return 'text-accent';
    }
  }
  return 'text-base-content/70';
});
</script>

<template>
  <div :class="[
    'flex flex-col sm:flex-row items-start justify-between sm:items-center gap-4 p-4 rounded-xl border transition-colors',
    isCurrentUser 
      ? 'bg-primary/10 border-primary/30' 
      : 'bg-base-100/60 backdrop-blur-sm border-base-300/30 hover:bg-base-200/40'
  ]">
    <div class="flex items-center gap-4 w-full sm:w-auto">
      <div :class="['text-xl font-bold w-8 text-center flex-shrink-0', getRankColor]">
        {{ user.rank }}
      </div>
      
      <div class="relative overflow-hidden rounded-full w-12 h-12 sm:w-16 sm:h-16 flex-shrink-0 border-2 border-base-300/50 shadow-lg">
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
      
      <div class="flex-1 min-w-0">
        <div class="font-bold text-base-content truncate">
          {{ user.username }}
          <span v-if="isCurrentUser" class="text-xs text-primary ml-2">(Vous)</span>
        </div>
        <div class="text-sm text-base-content/70">
          Niveau {{ user.level }}
        </div>
      </div>
    </div>
    
    <div class="flex justify-end">
      <div v-if="type === 'cp'" class="space-y-3 text-right">
        <CPBadge :cp="getValue" size="sm" variant="gradient" />
        <div v-if="user.team_pokemons">
          <div class="grid grid-cols-6 gap-2 w-fit ml-auto">
            <TeamPokemonCard 
              v-for="(pokemon, index) in user.team_pokemons.slice(0, 6)" 
              :key="index"
              :pokemon="pokemon"
              class="!w-10 !h-10 sm:!w-12 sm:!h-12 md:!w-16 md:!h-16"
            />
            <div 
              v-for="index in Math.max(0, 6 - user.team_pokemons.length)"
              :key="`placeholder-${index}`"
              class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 bg-base-200/40 backdrop-blur-sm rounded-md flex items-center justify-center border border-base-300/50 opacity-50"
            >
              <img src="/images/items/pokeball.png" alt="placeholder" class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 object-contain opacity-50" />
            </div>
          </div>
        </div>
      </div>
      <div v-else class="text-right">
        <div class="text-xl font-bold text-base-content">
          {{ getValue.toLocaleString() }}
        </div>
        <div class="text-xs text-base-content/70">
          {{ getValueLabel }}
        </div>
      </div>
    </div>
  </div>
</template>