<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue'
import Button from '@/Components/UI/Button.vue'
import { Head, router } from '@inertiajs/vue3'
import {
  ArrowLeft,
  Swords
} from 'lucide-vue-next'
import { computed, ref, watch } from 'vue'


import TeamManagementModal from '@/Components/Game/TeamManagementModal.vue'
import TowerLevelCard from '@/Components/InfernalTower/TowerLevelCard.vue'
import TrainerProfile from '@/Components/Profile/TrainerProfile.vue'
import CPBadge from '@/Components/UI/CPBadge.vue'

interface InfernalTowerLevel {
  level: number;
  team: {
    pokemon_id: number;
    name: string;
    star: number;
    is_shiny: boolean;
    cp: number;
  }[];
  team_cp: number;
  rewards: {
    pokeballs?: number;
    masterballs?: number;
    money?: number;
    exp?: number;
  };
  success_rate: number;
  trainer_avatar: string;
}

interface Pokedex {
  id: number;
  user_id: number;
  pokemon_id: number;
  level: number;
  star: number;
  is_in_team: boolean;
  is_favorite: boolean;
  obtained_at: string;
  cp: number;
  created_at: string;
  updated_at: string;
  pokemon: any;
  name?: string;
  is_shiny?: boolean;
  team_position?: number;
  hp_left?: number;
  [key: string]: any;
}

interface User {
  id: number;
  username: string;
  email: string;
  level: number;
  experience: number;
  cash: number;
  role: string;
  status: string;
  last_login: string;
  xp: number;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
  experience_for_current_level: number;
  experience_for_next_level: number;
  experience_percentage: number;
  avatar?: string;
  [key: string]: any;
}

interface Props {
  user: User;
  pokedex: Pokedex[];
  teamPokemons: Pokedex[];
  currentLevel: number;
  levels: InfernalTowerLevel[];
  dailyDefeats: number;
  userTeamCp: number;
}

const props = defineProps<Props>();

const userCurrentLevel = ref(props.currentLevel);
const towerLevels = ref(props.levels);
const userDailyDefeats = ref(props.dailyDefeats);
const userTeamCp = ref(props.userTeamCp);
const user = ref(props.user);
const pokedex = ref(props.pokedex);
const teamPokemons = ref(props.teamPokemons);
const showAlert = ref(false);
const alertMessage = ref('');
const alertType = ref<'success' | 'error' | 'info' | 'warning'>('success');
const teamManagementModalOpen = ref(false);
const isAttempting = ref(false);
const isBattling = ref(false);
const battleResult = ref<'victory' | 'defeat' | null>(null);

const currentLevelData = computed(() => {
  return towerLevels.value.find(level => level.level === userCurrentLevel.value);
});

const displayEnemyLeftTeam = computed(() => {
  const team = currentLevelData.value?.team ? [...currentLevelData.value.team] : [];
  return Array(3).fill(null).map((_, i) => team[i] || null);
});

const displayEnemyRightTeam = computed(() => {
  const team = currentLevelData.value?.team ? [...currentLevelData.value.team] : [];
  return Array(3).fill(null).map((_, i) => team[i + 3] || null);
});

const otherLevels = computed(() => {
  return towerLevels.value.filter(level => level.level !== userCurrentLevel.value);
});

watch(() => props.currentLevel, (newVal) => {
  userCurrentLevel.value = newVal;
});

watch(() => props.levels, (newVal) => {
  towerLevels.value = newVal;
});

watch(() => props.dailyDefeats, (newVal) => {
  userDailyDefeats.value = newVal;
});

watch(() => props.userTeamCp, (newVal) => {
  userTeamCp.value = newVal;
});

watch(() => props.user, (newVal) => {
  user.value = newVal;
});

watch(() => props.pokedex, (newVal) => {
  pokedex.value = newVal;
});

watch(() => props.teamPokemons, (newVal) => {
  teamPokemons.value = newVal;
});

const showMessage = (message: string, type: 'success' | 'error' | 'info' | 'warning' = 'success') => {
  alertMessage.value = message;
  alertType.value = type;
  showAlert.value = true;

  setTimeout(() => {
    showAlert.value = false;
  }, 5000);
};

const attemptLevel = async () => {
  if (isAttempting.value) {
    showMessage('Une tentative est déjà en cours...', 'warning');
    return;
  }

  if (userDailyDefeats.value <= 0) {
    showMessage('Vous avez épuisé toutes vos défaites quotidiennes. Revenez demain !', 'error');
    return;
  }

  isAttempting.value = true;
  isBattling.value = true;
  battleResult.value = null;

  try {
    const response = await fetch('/api/tower/attempt', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement).content,
      },
      body: JSON.stringify({ level: userCurrentLevel.value }),
    });

    const data = await response.json();

    battleResult.value = data.success ? 'victory' : 'defeat';

    setTimeout(() => {
      if (data.success) {
        const rewardsText = [
          data.rewards.pokeballs ? `${data.rewards.pokeballs} Pokeballs` : '',
          data.rewards.masterballs ? `${data.rewards.masterballs} Masterballs` : '',
          data.rewards.money ? `${data.rewards.money} Cash` : '',
          data.rewards.exp ? `${data.rewards.exp} EXP` : ''
        ].filter(Boolean).join(' • ');

        showMessage(`VICTOIRE ! Niveau ${userCurrentLevel.value} vaincu !\nRécompenses: ${rewardsText}`, 'success');

        router.reload({
          only: ['user', 'pokedex', 'teamPokemons', 'currentLevel', 'levels', 'dailyDefeats', 'userTeamCp'],
          onSuccess: (page: any) => {
            user.value = page.props.user as User;
            pokedex.value = page.props.pokedex as Pokedex[];
            teamPokemons.value = page.props.teamPokemons as Pokedex[];
            userCurrentLevel.value = page.props.currentLevel as number;
            towerLevels.value = page.props.levels as InfernalTowerLevel[];
            userDailyDefeats.value = page.props.dailyDefeats as number;
            userTeamCp.value = page.props.userTeamCp as number;
          }
        });
      } else {
        userDailyDefeats.value = data.daily_defeats;
        showMessage(`DÉFAITE ! ${data.message}\nDéfaites restantes: ${data.daily_defeats}/10`, 'error');
      }

      setTimeout(() => {
        isAttempting.value = false;
        isBattling.value = false;
        battleResult.value = null;
      }, 1000);
    }, 800);

  } catch (error) {
    console.error('Erreur lors du combat:', error);
    showMessage('Erreur lors du combat', 'error');
    battleResult.value = 'defeat';
    setTimeout(() => {
      isAttempting.value = false;
      isBattling.value = false;
      battleResult.value = null;
    }, 1000);
  }
};

const goBack = () => {
  router.visit('/me');
};
</script>

<template>

  <Head title="Tour Infernale" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="w-full max-w-7xl mx-auto px-4 lg:px-6 pb-16">
        <div class="flex items-center gap-4 pt-6 mb-6">
          <Button @click="goBack" variant="outline" size="sm" class="shrink-0">
            <ArrowLeft :size="16" class="inline" /> Retour
          </Button>
          <div>
            <h1 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-warning via-warning/90 to-accent bg-clip-text text-transparent">
              Tour Infernale
            </h1>
            <p class="text-base-content/60 text-sm mt-1">Défiez les dresseurs les plus puissants</p>
          </div>
        </div>

        <div v-if="showAlert" class="fixed top-4 right-4 z-50 max-w-sm animate-in slide-in-from-right-2 duration-300">
          <div class="bg-base-100/90 backdrop-blur-sm border border-base-300/50 rounded-xl p-4 shadow-lg">
            <div class="flex items-start gap-3">
              <div class="flex-shrink-0">
                <div v-if="alertType === 'success'"
                     class="w-6 h-6 rounded-full bg-success/20 flex items-center justify-center">
                  <span class="text-success text-sm">✓</span>
                </div>
                <div v-else class="w-6 h-6 rounded-full bg-error/20 flex items-center justify-center">
                  <span class="text-error text-sm">✕</span>
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-base-content/90">{{ alertMessage }}</p>
              </div>
              <button @click="showAlert = false"
                      class="flex-shrink-0 w-5 h-5 rounded-full hover:bg-base-300/50 flex items-center justify-center transition-colors">
                <span class="text-base-content/60 text-xs">✕</span>
              </button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
          <div :class="[
            battleResult === 'defeat' ? 'animate-shake' : ''
          ]">
            <TrainerProfile :user="user as User" :teamPokemons="teamPokemons"
                            :onOpenPokedexModal="() => teamManagementModalOpen = true" />
          </div>

          <!-- Profil de l'adversaire -->
          <div v-if="currentLevelData" :class="[
            battleResult === 'victory' ? 'animate-shake' : ''
          ]">
            <div class="flex flex-col items-center space-y-4 mt-14">
              <div class="mb-2">
                <CPBadge :cp="currentLevelData.team_cp" size="lg" variant="gradient" :show-label="true" />
              </div>

              <div class="relative flex items-center justify-center">
                <div class="absolute sm:-left-28 -left-16 flex flex-col space-y-2">
                  <template v-for="(pokemon, index) in displayEnemyLeftTeam" :key="index">
                    <div v-if="pokemon"
                         class="relative w-20 h-20 bg-base-200/40 backdrop-blur-sm rounded-md flex items-center justify-center border border-base-300/50 overflow-hidden flex-shrink-0">
                      <img :src="`/images/pokemon-gifs/${pokemon.pokemon_id}${pokemon.is_shiny ? '_S' : ''}.gif`"
                           :alt="pokemon.name" class="w-full h-full object-contain" />
                      <div
                        class="absolute bottom-0 left-0 right-0 bg-black/70 text-white text-xs px-1 py-0.5 text-center">
                        {{ pokemon.cp }}
                      </div>
                    </div>
                    <div v-else
                         class="relative w-20 h-20 bg-base-200/40 backdrop-blur-sm rounded-md flex items-center justify-center border border-base-300/50 opacity-50">
                      <img src="/images/items/pokeball.png" alt="placeholder"
                           class="w-8 h-8 object-contain opacity-50" />
                    </div>
                  </template>
                </div>

                <div class="w-48 h-48">
                  <img :src="`/images/trainer/${currentLevelData.trainer_avatar}`"
                       :alt="`Dresseur niveau ${currentLevelData.level}`" class="w-full h-full object-cover"
                       style="image-rendering: pixelated" />
                </div>

                <div class="absolute sm:-right-28 -right-16 flex flex-col space-y-2">
                  <template v-for="(pokemon, index) in displayEnemyRightTeam" :key="index + 3">
                    <div v-if="pokemon"
                         class="relative w-20 h-20 bg-base-200/40 backdrop-blur-sm rounded-md flex items-center justify-center border border-base-300/50 overflow-hidden flex-shrink-0">
                      <img :src="`/images/pokemon-gifs/${pokemon.pokemon_id}${pokemon.is_shiny ? '_S' : ''}.gif`"
                           :alt="pokemon.name" class="w-full h-full object-contain" />
                      <div
                        class="absolute bottom-0 left-0 right-0 bg-black/70 text-white text-xs px-1 py-0.5 text-center">
                        {{ pokemon.cp }}
                      </div>
                    </div>
                    <div v-else
                         class="relative w-20 h-20 bg-base-200/40 backdrop-blur-sm rounded-md flex items-center justify-center border border-base-300/50 opacity-50">
                      <img src="/images/items/pokeball.png" alt="placeholder"
                           class="w-8 h-8 object-contain opacity-50" />
                    </div>
                  </template>
                </div>
              </div>

              <div class="text-center">
                <h2 class="text-xl font-bold text-base-content mb-2">
                  Dresseur de la Tour
                </h2>
                <div class="text-lg text-base-content/70 mb-4">Niveau {{ currentLevelData.level }}</div>

                <Button @click="attemptLevel" :disabled="userDailyDefeats <= 0 || isAttempting"
                        :variant="userDailyDefeats <= 0 ? 'outline' : 'primary'" size="lg" class="min-w-[200px]">
                  <span v-if="isAttempting"><Swords :size="16" class="inline" /> Combat en cours...</span>
                  <span v-else-if="userDailyDefeats > 0"><Swords :size="16" class="inline" /> Combattre</span>
                  <span v-else>Plus de tentatives</span>
                </Button>
                <div v-if="userDailyDefeats <= 0" class="text-xs text-error mt-2">
                  Vous avez épuisé toutes vos défaites quotidiennes
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 mt-8">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl p-4 border border-base-300/30 text-center">
            <div class="text-3xl font-bold text-base-content mb-1">{{ userCurrentLevel }}</div>
            <div class="text-xs text-base-content/70 font-medium uppercase tracking-wider">Niveau actuel</div>
          </div>
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl p-4 border border-base-300/30 text-center">
            <div
              :class="['text-3xl font-bold mb-1', userDailyDefeats > 0 ? 'text-base-content' : 'text-base-content/50']">
              {{ userDailyDefeats }}/10
            </div>
            <div class="text-xs text-base-content/70 font-medium uppercase tracking-wider">Défaites restantes</div>
            <div v-if="userDailyDefeats <= 0" class="text-xs text-error mt-1">
              Revenez demain !
            </div>
          </div>
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl p-4 border border-base-300/30 text-center">
            <div class="text-3xl font-bold text-base-content mb-1">{{ userTeamCp.toLocaleString() }}</div>
            <div class="text-xs text-base-content/70 font-medium uppercase tracking-wider">CP de votre équipe</div>
          </div>
          <div v-if="currentLevelData"
               class="bg-base-100/60 backdrop-blur-sm rounded-xl p-4 border border-base-300/30 text-center">
            <div class="text-3xl font-bold text-base-content mb-1">{{ currentLevelData.success_rate }}%</div>
            <div class="text-xs text-base-content/70 font-medium uppercase tracking-wider">Chance de réussite</div>
          </div>
        </div>

        <div v-if="otherLevels.length > 0" class="mt-12">
          <div class="flex items-center gap-4 mb-8">
            <div class="flex items-center gap-2">
              <div
                class="w-8 h-8 rounded-lg bg-gradient-to-br from-error/20 to-accent/20 flex items-center justify-center border border-error/20">
                <span class="text-lg"><Swords :size="20" /></span>
              </div>
              <h3 class="text-2xl font-bold">
                Prochains défis
              </h3>
            </div>
            <div class="flex-1 h-px bg-gradient-to-r from-error/30 via-transparent to-transparent"></div>
            <div class="text-sm text-base-content/60 bg-base-100/40 px-3 py-1 rounded-full border border-base-300/30">
              {{ otherLevels.length }} niveau{{ otherLevels.length > 1 ? 's' : '' }} disponible{{ otherLevels.length > 1
                ? 's' :
                  '' }}
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div v-for="(level, index) in otherLevels" :key="level.level"
                 :style="{ 'animation-delay': `${index * 100}ms` }"
                 class="animate-in slide-in-from-bottom-4 duration-500 fill-mode-both">
              <TowerLevelCard :level="level" :isCurrentLevel="false" :onAttempt="attemptLevel"
                              :dailyDefeats="userDailyDefeats" :isAttempting="isAttempting" />
            </div>
          </div>

          <div class="mt-12 flex items-center justify-center">
            <div class="flex items-center gap-3 text-base-content/40">
              <div class="w-12 h-px bg-gradient-to-r from-transparent to-base-content/20"></div>
              <div class="w-12 h-px bg-gradient-to-l from-transparent to-base-content/20"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <TeamManagementModal :show="teamManagementModalOpen" :onClose="() => teamManagementModalOpen = false"
                         :userPokemons="pokedex" />
  </div>
</template>

<style scoped>
@keyframes shake {

  0%,
  100% {
    transform: translateX(0);
  }

  25% {
    transform: translateX(-5px);
  }

  75% {
    transform: translateX(5px);
  }
}

.animate-shake {
  animation: shake 0.15s ease-in-out 4;
}
</style>