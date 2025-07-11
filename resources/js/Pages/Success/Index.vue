<script setup lang="ts">
import { ref } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Alert from '@/Components/UI/Alert.vue';
import Spinner from '@/Components/UI/Spinner.vue';

interface Success {
  id: number;
  title: string;
  description: string;
  image: string;
  cash_reward: number;
  xp_reward: number;
}

interface UserSuccess {
  id: number;
  success_id: number;
  user_id: number;
  unlocked_at: string;
  claimed_at: string | null;
  is_claimed: boolean;
  success: Success;
}

defineProps<{
  successes: Success[];
  unclaimed_successes: UserSuccess[];
  claimed_successes: UserSuccess[];
  progress: {
    total: number;
    unlocked: number;
    claimed: number;
    unclaimed: number;
    percentage: number;
  };
  flash?: {
    success?: string;
    error?: string;
  };
}>();

const activeTab = ref('unclaimed');
const loading = ref(false);

const claimSuccess = async (successId: number) => {
  loading.value = true;
  try {
    await router.post(`/success/${successId}/claim`);
  } catch (error) {
    console.error('Erreur lors de la réclamation du badge:', error);
  } finally {
    loading.value = false;
  }
};

const claimAllSuccesses = async () => {
  loading.value = true;
  try {
    await router.post('/success/claim-all');
  } catch (error) {
    console.error('Erreur lors de la réclamation de tous les badges:', error);
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <Head title="Succès" />

  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 h-screen w-screen overflow-hidden">
      <div class="flex justify-center pt-4 mb-4">
        <div class="text-center">
          <h1 class="text-2xl font-bold bg-gradient-to-r from-info to-info/80 bg-clip-text text-transparent mb-1 tracking-wider">
            🏆 SUCCÈS
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            BADGES ET RÉCOMPENSES
          </p>
        </div>
      </div>

      <div class="absolute top-24 left-1/2 -translate-x-1/2 w-[800px] max-h-[600px]">
        <div v-if="flash?.success" class="mb-4">
          <Alert type="success" :message="flash.success" />
        </div>
        <div v-if="flash?.error" class="mb-4">
          <Alert type="error" :message="flash.error" />
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-full flex flex-col">
          <div class="shrink-0 p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
            <div class="flex justify-between items-center">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">🏆</span>
                MES SUCCÈS
              </h3>
              <div class="flex items-center gap-2">
                <div class="text-xs text-base-content/70">Progression:</div>
                <div class="w-32 h-2 bg-base-300 rounded-full overflow-hidden">
                  <div 
                    class="h-full bg-info" 
                    :style="{ width: `${progress.percentage}%` }"
                  ></div>
                </div>
                <div class="text-xs font-bold">{{ progress.unlocked }}/{{ progress.total }}</div>
              </div>
            </div>
          </div>

          <div class="shrink-0 border-b border-base-300/20">
            <div class="flex px-2">
              <button 
                @click="activeTab = 'unclaimed'" 
                class="px-4 py-2 text-sm font-medium transition-colors duration-200"
                :class="activeTab === 'unclaimed' ? 'text-info border-b-2 border-info' : 'text-base-content/70 hover:text-base-content'"
              >
                À réclamer ({{ unclaimed_successes.length }})
              </button>
              <button 
                @click="activeTab = 'claimed'" 
                class="px-4 py-2 text-sm font-medium transition-colors duration-200"
                :class="activeTab === 'claimed' ? 'text-success border-b-2 border-success' : 'text-base-content/70 hover:text-base-content'"
              >
                Réclamés ({{ claimed_successes.length }})
              </button>
              <button 
                @click="activeTab = 'locked'" 
                class="px-4 py-2 text-sm font-medium transition-colors duration-200"
                :class="activeTab === 'locked' ? 'text-base-content border-b-2 border-base-content' : 'text-base-content/70 hover:text-base-content'"
              >
                Verrouillés ({{ successes.length }})
              </button>
            </div>
          </div>

          <div class="flex-1 overflow-y-auto p-4">
            <div v-if="loading" class="flex justify-center items-center py-12">
              <Spinner size="md" />
            </div>

            <div v-else-if="activeTab === 'unclaimed'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div 
                v-for="item in unclaimed_successes" 
                :key="item.id"
                class="bg-base-200/30 backdrop-blur-sm rounded-xl p-4 border border-base-300/20 hover:border-info/40 transition-all duration-200 group"
              >
                <div class="flex items-start gap-3">
                  <div class="w-14 h-14 shrink-0 bg-base-300/50 rounded-lg overflow-hidden flex items-center justify-center">
                    <img 
                      :src="`/images/badges/${item.success.image}`" 
                      :alt="item.success.title"
                      class="w-12 h-12 object-contain"
                    />
                  </div>
                  <div class="flex-1">
                    <h3 class="font-bold text-base-content group-hover:text-info transition-colors">
                      {{ item.success.title }}
                    </h3>
                    <p class="text-xs text-base-content/70 mb-2">
                      {{ item.success.description }}
                    </p>
                    <div class="flex justify-between items-center">
                      <div class="flex gap-2">
                        <span v-if="item.success.cash_reward > 0" class="text-xs px-2 py-0.5 bg-success/10 text-success rounded-full">
                          +{{ item.success.cash_reward }} Cash
                        </span>
                        <span v-if="item.success.xp_reward > 0" class="text-xs px-2 py-0.5 bg-info/10 text-info rounded-full">
                          +{{ item.success.xp_reward }} XP
                        </span>
                      </div>
                      <Button
                        @click="claimSuccess(item.success_id)"
                        variant="primary"
                        size="sm"
                        :disabled="loading"
                      >
                        Réclamer
                      </Button>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="unclaimed_successes.length === 0" class="col-span-full text-center py-8">
                <p class="text-2xl mb-2">🏆</p>
                <p class="text-sm mb-1">Aucun succès à réclamer</p>
                <p class="opacity-70 text-xs">Continuez à jouer pour débloquer des succès !</p>
              </div>
            </div>

            <div v-else-if="activeTab === 'claimed'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div 
                v-for="item in claimed_successes" 
                :key="item.id"
                class="bg-base-200/30 backdrop-blur-sm rounded-xl p-4 border border-base-300/20 hover:border-success/40 transition-all duration-200 group"
              >
                <div class="flex items-start gap-3">
                  <div class="w-14 h-14 shrink-0 bg-base-300/50 rounded-lg overflow-hidden flex items-center justify-center">
                    <img 
                      :src="`/images/badges/${item.success.image}`" 
                      :alt="item.success.title"
                      class="w-12 h-12 object-contain"
                    />
                  </div>
                  <div class="flex-1">
                    <h3 class="font-bold text-base-content group-hover:text-success transition-colors">
                      {{ item.success.title }}
                    </h3>
                    <p class="text-xs text-base-content/70 mb-2">
                      {{ item.success.description }}
                    </p>
                    <div class="flex justify-between items-center">
                      <div class="flex gap-2">
                        <span v-if="item.success.cash_reward > 0" class="text-xs px-2 py-0.5 bg-success/10 text-success rounded-full">
                          +{{ item.success.cash_reward }} Cash
                        </span>
                        <span v-if="item.success.xp_reward > 0" class="text-xs px-2 py-0.5 bg-info/10 text-info rounded-full">
                          +{{ item.success.xp_reward }} XP
                        </span>
                      </div>
                      <span class="text-xs px-2 py-0.5 bg-success/10 text-success rounded-full">
                        Réclamé
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="claimed_successes.length === 0" class="col-span-full text-center py-8">
                <p class="text-2xl mb-2">🎯</p>
                <p class="text-sm mb-1">Vous n'avez pas encore réclamé de succès</p>
                <p class="opacity-70 text-xs">Réclamez vos succès pour obtenir des récompenses !</p>
              </div>
            </div>

            <div v-else-if="activeTab === 'locked'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div 
                v-for="success in successes" 
                :key="success.id"
                class="bg-base-200/30 backdrop-blur-sm rounded-xl p-4 border border-base-300/20 hover:border-base-content/20 transition-all duration-200 group opacity-80"
              >
                <div class="flex items-start gap-3">
                  <div class="w-14 h-14 shrink-0 bg-base-300/50 rounded-lg overflow-hidden flex items-center justify-center">
                    <img 
                      :src="`/images/badges/${success.image}`" 
                      :alt="success.title"
                      class="w-12 h-12 object-contain grayscale"
                    />
                  </div>
                  <div class="flex-1">
                    <h3 class="font-bold text-base-content/70 group-hover:text-base-content/90 transition-colors">
                      {{ success.title }}
                    </h3>
                    <p class="text-xs text-base-content/60 mb-2">
                      {{ success.description }}
                    </p>
                    <div class="flex justify-between items-center">
                      <div class="flex gap-2">
                        <span v-if="success.cash_reward > 0" class="text-xs px-2 py-0.5 bg-base-300/30 text-base-content/60 rounded-full">
                          +{{ success.cash_reward }} Cash
                        </span>
                        <span v-if="success.xp_reward > 0" class="text-xs px-2 py-0.5 bg-base-300/30 text-base-content/60 rounded-full">
                          +{{ success.xp_reward }} XP
                        </span>
                      </div>
                      <span class="text-xs px-2 py-0.5 bg-base-300/30 text-base-content/60 rounded-full">
                        Verrouillé
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="successes.length === 0" class="col-span-full text-center py-8">
                <p class="text-2xl mb-2">🎉</p>
                <p class="text-sm mb-1">Vous avez débloqué tous les succès disponibles !</p>
                <p class="opacity-70 text-xs">Félicitations pour votre progression !</p>
              </div>
            </div>
          </div>

          <div v-if="activeTab === 'unclaimed' && unclaimed_successes.length > 1" class="shrink-0 bg-gradient-to-r from-info/10 to-info/5 px-4 py-3 border-t border-info/20">
            <div class="flex justify-center">
              <Button
                @click="claimAllSuccesses"
                variant="primary"
                size="sm"
                :disabled="loading"
              >
                {{ loading ? '🔄 Chargement...' : '🏆 Réclamer tous les badges' }}
              </Button>
            </div>
          </div>

          <div class="shrink-0 bg-gradient-to-r from-info/10 to-info/5 px-3 py-2 border-t border-info/20">
            <div class="text-xs text-center text-base-content/70">
              {{ progress.unlocked }} succès débloqué{{ progress.unlocked > 1 ? 's' : '' }} sur {{ progress.total }} ({{ progress.percentage }}%)
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 