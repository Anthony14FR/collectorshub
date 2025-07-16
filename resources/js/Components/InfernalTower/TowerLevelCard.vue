<script setup lang="ts">
import CPBadge from '@/Components/UI/CPBadge.vue';
import Button from '@/Components/UI/Button.vue';
import Badge from '@/Components/UI/Badge.vue';
import ProgressBar from '@/Components/UI/ProgressBar.vue';

interface TowerPokemon {
  pokemon_id: number;
  name: string;
  star: number;
  is_shiny: boolean;
  cp: number;
}

interface Props {
  level: {
    level: number;
    team: TowerPokemon[];
    team_cp: number;
    rewards: {
      pokeballs?: number;
      masterballs?: number;
      money?: number;
      exp?: number;
    };
    success_rate: number;
    trainer_avatar: string;
  };
  isCurrentLevel: boolean;
  onAttempt: () => void;
  dailyDefeats: number;
  isAttempting?: boolean;
}

const props = defineProps<Props>();

const getSuccessRateColor = (rate: number) => {
  if (rate >= 80) return 'success';
  if (rate >= 60) return 'warning';
  if (rate >= 40) return 'error';
  return 'error';
};
</script>

<template>
  <div :class="[
    'flex flex-col lg:flex-row items-center gap-6 p-6 rounded-xl border transition-all duration-300',
    isCurrentLevel 
      ? 'bg-primary/10 border-primary/30 shadow-lg shadow-primary/20' 
      : 'bg-base-100/60 backdrop-blur-sm border-base-300/30 hover:bg-base-200/40'
  ]">
    <div class="flex items-center gap-4 w-full lg:w-auto">
      <Badge :variant="getSuccessRateColor(level.success_rate)" size="lg" class="text-lg font-bold">
        Niveau {{ level.level }}
      </Badge>
            
      <div class="relative overflow-hidden rounded-full w-20 h-20 flex-shrink-0 border-2 border-warning/50 shadow-lg">
        <img 
          :src="`/images/trainer/${level.trainer_avatar}`"
          :alt="`Dresseur niveau ${level.level}`"
          class="w-full h-full object-cover rounded-full"
          style="image-rendering: pixelated;"
        />
      </div>
            
      <div class="flex-1 min-w-0 text-center lg:text-left">
        <div class="font-bold text-base-content text-lg">
          Dresseur de la Tour
        </div>
        <div class="text-sm text-base-content/70">
          Niveau {{ level.level }}
        </div>
      </div>
    </div>
        
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 w-full">
      <div class="space-y-3 text-center">
        <CPBadge :cp="level.team_cp" size="md" variant="gradient" />
                
        <div class="flex flex-wrap gap-2 justify-center max-w-full">
          <div 
            v-for="(pokemon, index) in level.team" 
            :key="index"
            class="relative w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 bg-base-200/40 backdrop-blur-sm rounded-md flex items-center justify-center border border-base-300/50 overflow-hidden flex-shrink-0"
          >
            <img 
              :src="`/images/pokemon-gifs/${pokemon.pokemon_id}${pokemon.is_shiny ? '_S' : ''}.gif`"
              :alt="pokemon.name"
              class="w-full h-full object-contain"
            />
            <div class="absolute bottom-0 left-0 right-0 bg-black/70 text-white text-xs px-1 py-0.5 text-center">
              {{ pokemon.cp }}
            </div>
          </div>
        </div>
                
        <div v-if="level.rewards" class="mt-4">
          <div class="text-sm text-base-content/70 mb-2">Récompenses potentielles</div>
          <div class="flex flex-wrap gap-2 justify-center">
            <div 
              v-if="level.rewards.pokeballs && level.rewards.pokeballs > 0"
              class="flex items-center gap-1 bg-base-200/60 rounded-lg px-2 py-1 text-xs"
            >
              <img src="/images/items/pokeball.png" alt="Pokeball" class="w-4 h-4" />
              <span class="text-white">{{ level.rewards.pokeballs }}</span>
            </div>
            <div 
              v-if="level.rewards.masterballs && level.rewards.masterballs > 0"
              class="flex items-center gap-1 bg-base-200/60 rounded-lg px-2 py-1 text-xs"
            >
              <img src="/images/items/masterball.png" alt="Masterball" class="w-4 h-4" />
              <span class="text-white">{{ level.rewards.masterballs }}</span>
            </div>
            <div 
              v-if="level.rewards.money && level.rewards.money > 0"
              class="flex items-center gap-1 bg-base-200/60 rounded-lg px-2 py-1 text-xs"
            >
              <span class="text-yellow-400 font-bold">💰</span>
              <span class="text-white">{{ level.rewards.money }}</span>
            </div>
            <div 
              v-if="level.rewards.exp && level.rewards.exp > 0"
              class="flex items-center gap-1 bg-base-200/60 rounded-lg px-2 py-1 text-xs"
            >
              <span class="text-blue-400 font-bold">⭐</span>
              <span class="text-white">{{ level.rewards.exp }} EXP</span>
            </div>
          </div>
        </div>
      </div>
            
      <div class="flex flex-col items-end gap-4 flex-shrink-0">
        <div class="text-right">
          <div class="text-sm text-base-content/70 mb-2">Chance de réussite</div>
          <Badge :variant="getSuccessRateColor(level.success_rate)" size="lg" class="text-xl font-bold mb-3">
            {{ level.success_rate }}%
          </Badge>
          <div class="w-32 bg-base-300/30 rounded-full h-3 overflow-hidden">
            <div 
              :class="[
                'h-3 rounded-full transition-all duration-500',
                level.success_rate >= 80 ? 'bg-success' : 
                level.success_rate >= 60 ? 'bg-warning' : 
                level.success_rate >= 40 ? 'bg-error' : 'bg-error'
              ]"
              :style="{ width: level.success_rate + '%' }"
            ></div>
          </div>
        </div>
                
        <div v-if="isCurrentLevel" class="text-right">
          <Button 
            @click="onAttempt"
            :disabled="dailyDefeats <= 0 || isAttempting"
            :variant="dailyDefeats <= 0 ? 'outline' : 'primary'"
            size="lg"
            class="min-w-[200px]"
          >
            <span v-if="isAttempting">Tentative en cours...</span>
            <span v-else-if="dailyDefeats > 0">Tenter le niveau {{ level.level }}</span>
            <span v-else>Plus de tentatives</span>
          </Button>
          <div v-if="dailyDefeats <= 0" class="text-xs text-error mt-2 text-center max-w-[200px]">
            Vous avez épuisé toutes vos défaites quotidiennes. Revenez demain !
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 