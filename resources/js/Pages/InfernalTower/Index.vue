<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue'
import Button from '@/Components/UI/Button.vue'
import Alert from '@/Components/UI/Alert.vue'
import TrainerProfile from '@/Components/Profile/TrainerProfile.vue'
import TeamManagementModal from '@/Components/Game/TeamManagementModal.vue'
import TowerLevelCard from '@/Components/InfernalTower/TowerLevelCard.vue'

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

    if (data.success) {
      const rewardsText = [
        data.rewards.pokeballs ? `🎾 ${data.rewards.pokeballs} Pokeballs` : '',
        data.rewards.masterballs ? `🔮 ${data.rewards.masterballs} Masterballs` : '',
        data.rewards.money ? `💰 ${data.rewards.money} Argent` : '',
        data.rewards.exp ? `⭐ ${data.rewards.exp} EXP` : ''
      ].filter(Boolean).join(', ');
            
      showMessage(`🎉 Victoire ! Vous avez vaincu le niveau ${userCurrentLevel.value}. Récompenses: ${rewardsText}`);
            
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
      showMessage(`${data.message} Défaites restantes: ${data.daily_defeats}`, 'error');
    }
  } catch (error) {
    console.error('Erreur lors du combat:', error);
    showMessage('Erreur lors du combat', 'error');
  } finally {
    isAttempting.value = false;
  }
};

const goBack = () => {
  router.visit('/me');
};
</script>

<template>
  <Head title="Tour Infernale" />

  <div class="">
    <BackgroundEffects />

    <div class="relative z-50 min-h-screen">
      <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
          <h1 class="text-3xl font-bold text-white">Tour Infernale</h1>
          <Button @click="goBack" variant="outline" size="sm">
            Retour
          </Button>
        </div>

        <TrainerProfile
          :user="user as User"
          :teamPokemons="teamPokemons"
          :onOpenPokedexModal="() => teamManagementModalOpen = true"
        />

        <TeamManagementModal
          :show="teamManagementModalOpen"
          :onClose="() => teamManagementModalOpen = false"
          :userPokemons="pokedex"
        />

        <div class="bg-base-100/80 backdrop-blur-sm rounded-lg p-6 mb-6 mt-8">
          <h2 class="text-xl font-semibold text-white mb-4">Votre progression</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-base-200/50 rounded-lg p-4">
              <p class="text-white">Niveau actuel : <span class="font-bold text-warning">{{ userCurrentLevel }}</span></p>
            </div>
            <div class="bg-base-200/50 rounded-lg p-4">
              <p class="text-white">
                Défaites restantes : 
                <span :class="[
                  'font-bold',
                  userDailyDefeats > 0 ? 'text-error' : 'text-error/50'
                ]">
                  {{ userDailyDefeats }}/10
                </span>
              </p>
              <p v-if="userDailyDefeats <= 0" class="text-xs text-error mt-1">
                Revenez demain pour de nouvelles tentatives !
              </p>
            </div>
            <div class="bg-base-200/50 rounded-lg p-4">
              <p class="text-white">CP de votre équipe : <span class="font-bold text-primary">{{ userTeamCp }}</span></p>
            </div>
          </div>
        </div>

        <div class="space-y-6">
          <TowerLevelCard
            v-for="level in towerLevels"
            :key="level.level"
            :level="level"
            :isCurrentLevel="level.level === userCurrentLevel"
            :onAttempt="attemptLevel"
            :dailyDefeats="userDailyDefeats"
            :isAttempting="isAttempting"
          />
        </div>
      </div>
    </div>

    <div v-if="showAlert" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-96">
      <Alert :type="alertType" :message="alertMessage" dismissible @dismiss="showAlert = false" />
    </div>
  </div>
</template> 