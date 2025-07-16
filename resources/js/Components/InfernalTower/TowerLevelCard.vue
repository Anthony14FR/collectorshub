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
    'p-6 rounded-xl border transition-all duration-300',
    isCurrentLevel 
      ? 'bg-primary/10 border-primary/30 shadow-lg shadow-primary/20' 
      : 'bg-base-100/60 backdrop-blur-sm border-base-300/30 hover:bg-base-200/40'
  ]">
    <div class="space-y-5">
      
      <div class="space-y-4 sm:space-y-0">
        <div class="flex flex-col sm:hidden space-y-3">
          <div class="flex items-center justify-between">
            <Badge :variant="getSuccessRateColor(level.success_rate)" size="lg" class="text-lg font-bold">
              Niveau {{ level.level }}
            </Badge>
            <div class="text-right">
              <div class="text-xl font-bold" :class="{
                'text-success': level.success_rate >= 80,
                'text-warning': level.success_rate >= 60 && level.success_rate < 80,
                'text-error': level.success_rate < 60
              }">
                {{ level.success_rate }}%
              </div>
              <div class="text-xs text-base-content/70 uppercase tracking-wider">Réussite</div>
            </div>
          </div>
          
          <div class="flex items-center gap-3">
            <div class="relative overflow-hidden rounded-full w-12 h-12 flex-shrink-0 border-2 border-warning/50 shadow-lg">
              <img 
                :src="`/images/trainer/${level.trainer_avatar}`"
                :alt="`Dresseur niveau ${level.level}`"
                class="w-full h-full object-cover rounded-full"
                style="image-rendering: pixelated;"
              />
            </div>
            <div class="flex-1">
              <div class="font-bold text-base-content text-sm">
                Dresseur de la Tour
              </div>
              <div class="text-xs text-base-content/70">
                CP: {{ level.team_cp.toLocaleString() }}
              </div>
            </div>
          </div>
        </div>

        <div class="hidden sm:flex items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <Badge :variant="getSuccessRateColor(level.success_rate)" size="lg" class="text-lg font-bold flex-shrink-0">
              Niveau {{ level.level }}
            </Badge>
            
            <div class="flex items-center gap-3">
              <div class="relative overflow-hidden rounded-full w-14 h-14 flex-shrink-0 border-2 border-warning/50 shadow-lg">
                <img 
                  :src="`/images/trainer/${level.trainer_avatar}`"
                  :alt="`Dresseur niveau ${level.level}`"
                  class="w-full h-full object-cover rounded-full"
                  style="image-rendering: pixelated;"
                />
              </div>
              
              <div class="min-w-0 flex-1">
                <div class="font-bold text-base-content text-base truncate">
                  Dresseur de la Tour
                </div>
                <div class="text-sm text-base-content/70">
                  CP Total: {{ level.team_cp.toLocaleString() }}
                </div>
              </div>
            </div>
          </div>
          
          <div class="flex-shrink-0 text-right">
            <div class="text-2xl font-bold" :class="{
              'text-success': level.success_rate >= 80,
              'text-warning': level.success_rate >= 60 && level.success_rate < 80,
              'text-error': level.success_rate < 60
            }">
              {{ level.success_rate }}%
            </div>
            <div class="text-xs text-base-content/70 uppercase tracking-wider">Réussite</div>
          </div>
        </div>
      </div>

      <div class="space-y-4 md:space-y-0 md:grid md:grid-cols-3 md:gap-6 md:items-start">
        
        <div class="md:col-span-2">
          <div class="text-sm text-base-content/70 mb-3 font-medium uppercase tracking-wider">Équipe adverse</div>
          <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
            <div 
              v-for="(pokemon, index) in level.team" 
              :key="index"
              class="relative aspect-square bg-base-200/40 backdrop-blur-sm rounded-lg flex items-center justify-center border border-base-300/50 overflow-hidden group hover:scale-105 transition-transform"
            >
              <img 
                :src="`/images/pokemon-gifs/${pokemon.pokemon_id}${pokemon.is_shiny ? '_S' : ''}.gif`"
                :alt="pokemon.name"
                class="w-full h-full object-contain p-1"
              />
              <div class="absolute bottom-0 left-0 right-0 bg-black/70 text-white text-xs px-1 py-0.5 text-center font-medium">
                {{ pokemon.cp }}
              </div>
            </div>
          </div>
        </div>
          
        <div>
          <div class="text-sm text-base-content/70 mb-3 font-medium uppercase tracking-wider">Récompenses</div>
          <div v-if="level.rewards" class="grid grid-cols-2 sm:grid-cols-1 md:grid-cols-2 gap-2">
            <div 
              v-if="level.rewards.pokeballs && level.rewards.pokeballs > 0"
              class="flex items-center gap-2 bg-base-200/60 rounded-lg px-2 py-2 text-xs sm:text-sm border border-base-300/30"
            >
              <img src="/images/items/pokeball.png" alt="Pokeball" class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" />
              <span class="text-base-content font-medium">{{ level.rewards.pokeballs }}</span>
            </div>
            <div 
              v-if="level.rewards.masterballs && level.rewards.masterballs > 0"
              class="flex items-center gap-2 bg-base-200/60 rounded-lg px-2 py-2 text-xs sm:text-sm border border-base-300/30"
            >
              <img src="/images/items/masterball.png" alt="Masterball" class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" />
              <span class="text-base-content font-medium">{{ level.rewards.masterballs }}</span>
            </div>
            <div 
              v-if="level.rewards.money && level.rewards.money > 0"
              class="flex items-center gap-2 bg-base-200/60 rounded-lg px-2 py-2 text-xs sm:text-sm border border-base-300/30"
            >
              <span class="text-yellow-500 font-bold text-base sm:text-lg flex-shrink-0">💰</span>
              <span class="text-base-content font-medium">{{ level.rewards.money.toLocaleString() }}</span>
            </div>
            <div 
              v-if="level.rewards.exp && level.rewards.exp > 0"
              class="flex items-center gap-2 bg-base-200/60 rounded-lg px-2 py-2 text-xs sm:text-sm border border-base-300/30"
            >
              <span class="text-blue-500 font-bold text-base sm:text-lg flex-shrink-0">⭐</span>
              <span class="text-base-content font-medium">{{ level.rewards.exp }} EXP</span>
            </div>
          </div>
          <div v-else class="text-sm text-base-content/50 italic">
            Aucune récompense
          </div>
        </div>
      </div>

      <div v-if="isCurrentLevel" class="pt-4 border-t border-base-300/30">
        <div class="sm:hidden space-y-3">
          <div class="w-full bg-base-300/30 rounded-full h-2 overflow-hidden">
            <div 
              :class="[
                'h-2 rounded-full transition-all duration-500',
                level.success_rate >= 80 ? 'bg-success' : 
                level.success_rate >= 60 ? 'bg-warning' : 
                level.success_rate >= 40 ? 'bg-error' : 'bg-error'
              ]"
              :style="{ width: level.success_rate + '%' }"
            ></div>
          </div>
          
          <Button 
            @click="onAttempt"
            :disabled="dailyDefeats <= 0 || isAttempting"
            :variant="dailyDefeats <= 0 ? 'outline' : 'primary'"
            size="lg"
            class="w-full"
          >
            <span v-if="isAttempting">⚔️ Combat...</span>
            <span v-else-if="dailyDefeats > 0">⚔️ Combattre</span>
            <span v-else>🚫 Épuisé</span>
          </Button>
        </div>

        <div class="hidden sm:flex items-center justify-between gap-4">
          <div class="flex-1">
            <div class="w-full bg-base-300/30 rounded-full h-2 overflow-hidden">
              <div 
                :class="[
                  'h-2 rounded-full transition-all duration-500',
                  level.success_rate >= 80 ? 'bg-success' : 
                  level.success_rate >= 60 ? 'bg-warning' : 
                  level.success_rate >= 40 ? 'bg-error' : 'bg-error'
                ]"
                :style="{ width: level.success_rate + '%' }"
              ></div>
            </div>
          </div>
          
          <Button 
            @click="onAttempt"
            :disabled="dailyDefeats <= 0 || isAttempting"
            :variant="dailyDefeats <= 0 ? 'outline' : 'primary'"
            size="lg"
            class="flex-shrink-0 min-w-[180px]"
          >
            <span v-if="isAttempting">⚔️ Combat...</span>
            <span v-else-if="dailyDefeats > 0">⚔️ Combattre</span>
            <span v-else">🚫 Épuisé</span>
          </Button>
        </div>
        
        <div v-if="dailyDefeats <= 0" class="text-xs text-error mt-2 text-center">
          Vous avez épuisé toutes vos défaites quotidiennes. Revenez demain !
        </div>
      </div>
    </div>
  </div>
</template> 