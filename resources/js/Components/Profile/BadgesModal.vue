<script setup lang="ts">
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import Spinner from '@/Components/UI/Spinner.vue';
import { router } from '@inertiajs/vue3';
import { Sparkles, Target, Trophy } from 'lucide-vue-next';
import { ref } from 'vue';
import type { Success, UserSuccess } from '../../types/success';

defineProps<{
  show: boolean;
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
  onClose: () => void;
}>();

const activeTab = ref('unclaimed');
const loading = ref(false);

const claimSuccess = async (successId: number) => {
  loading.value = true;
  router.post(`/success/${successId}/claim`, {}, {
    preserveScroll: true,
    onFinish: () => { loading.value = false; }
  });
};

const claimAllSuccesses = async () => {
  loading.value = true;
  router.post('/success/claim-all', {}, {
    preserveScroll: true,
    onFinish: () => { loading.value = false; }
  });
};
</script>

<template>
  <Modal :show="show" @close="onClose" max-width="4xl" :fixed-height="true">
    <template #header>
      <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-xl flex items-center justify-center mb-2 sm:mb-0">
          <Trophy :size="24" class="text-primary" />
        </div>
        <div class="flex-1 text-center sm:text-left">
          <h3 class="text-xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
            Mes Succès
          </h3>
          <p class="text-sm text-base-content/70">Badges et récompenses</p>
        </div>
      </div>
    </template>
    
    <template #tabs>
      <div class="flex">
        <button 
          @click="activeTab = 'unclaimed'" 
          class="px-4 py-3 sm:text-sm text-xs font-medium transition-colors duration-200"
          :class="activeTab === 'unclaimed' ? 'text-info border-b-2 border-info' : 'text-base-content/70 hover:text-base-content'"
        >
          À réclamer ({{ unclaimed_successes.length }})
        </button>
        <button 
          @click="activeTab = 'claimed'" 
          class="px-4 py-3 sm:text-sm text-xs font-medium transition-colors duration-200"
          :class="activeTab === 'claimed' ? 'text-success border-b-2 border-success' : 'text-base-content/70 hover:text-base-content'"
        >
          Réclamés ({{ claimed_successes.length }})
        </button>
        <button 
          @click="activeTab = 'locked'" 
          class="px-4 py-3 sm:text-sm text-xs font-medium transition-colors duration-200"
          :class="activeTab === 'locked' ? 'text-base-content border-b-2 border-base-content' : 'text-base-content/70 hover:text-base-content'"
        >
          Verrouillés ({{ successes.length }})
        </button>
      </div>
    </template>

    <template #default>
      <div v-if="loading" class="flex justify-center items-center h-full">
        <Spinner size="lg" />
      </div>

      <div v-else class="min-h-[400px]">
        <div v-if="activeTab === 'unclaimed'">
          <div v-if="unclaimed_successes.length > 0" class="flex flex-col gap-4">
            <Button
              @click="claimAllSuccesses"
              variant="primary"
              :disabled="loading"
              class="w-full"
            >
              Tout réclamer ({{ unclaimed_successes.length }})
            </Button>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div 
                v-for="item in unclaimed_successes" 
                :key="item.id"
                class="bg-base-200/30 backdrop-blur-sm p-4 border border-base-300/20 hover:border-info/40 transition-all duration-200 group"
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
                    <div class="flex sm:flex-row flex-col justify-between sm:items-center">
                      <div class="flex gap-2">
                        <span v-if="item.success.cash_reward > 0" class="sm:text-xs text-[10px] px-2 py-0.5 bg-success/10 text-success rounded-full">
                          +{{ item.success.cash_reward }} Cash
                        </span>
                        <span v-if="item.success.xp_reward > 0" class="sm:text-xs text-[10px] px-2 py-0.5 bg-info/10 text-info rounded-full">
                          +{{ item.success.xp_reward }} XP
                        </span>
                      </div>
                      <Button
                        @click="claimSuccess(item.success_id)"
                        variant="primary"
                        size="sm"
                        :disabled="loading"
                        class="sm:mt-0 mt-2"
                      >
                        Réclamer
                      </Button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="flex flex-col justify-center items-center h-full min-h-[400px] text-center">
            <Trophy :size="48" class="mb-4 text-info/50" />
            <p class="text-lg font-semibold mb-1">Aucun succès à réclamer</p>
            <p class="opacity-70 text-sm">Continuez à jouer pour débloquer des succès !</p>
          </div>
        </div>

        <div v-else-if="activeTab === 'claimed'">
          <div v-if="claimed_successes.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
          </div>
          <div v-else class="flex flex-col justify-center items-center h-full min-h-[400px] text-center">
            <Target :size="48" class="mb-4 text-success/50" />
            <p class="text-lg font-semibold mb-1">Vous n'avez pas encore réclamé de succès</p>
            <p class="opacity-70 text-sm">Réclamez vos succès pour obtenir des récompenses !</p>
          </div>
        </div>

        <div v-else-if="activeTab === 'locked'">
          <div v-if="successes.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                    class="w-12 h-12 object-contain filter grayscale"
                  />
                </div>
                <div class="flex-1">
                  <h3 class="font-bold text-base-content transition-colors">
                    {{ success.title }}
                  </h3>
                  <p class="text-xs text-base-content/70 mb-2">
                    {{ success.description }}
                  </p>
                  <div class="flex gap-2">
                    <span v-if="success.cash_reward > 0" class="text-xs px-2 py-0.5 bg-base-content/10 text-base-content rounded-full">
                      +{{ success.cash_reward }} Cash
                    </span>
                    <span v-if="success.xp_reward > 0" class="text-xs px-2 py-0.5 bg-base-content/10 text-base-content rounded-full">
                      +{{ success.xp_reward }} XP
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="flex flex-col justify-center items-center h-full min-h-[400px] text-center">
            <Sparkles :size="48" class="mb-4 text-warning/50" />
            <p class="text-lg font-semibold mb-1">Tous les succès ont été débloqués !</p>
            <p class="opacity-70 text-sm">Félicitations, dresseur !</p>
          </div>
        </div>
      </div>
    </template>
  </Modal>
</template> 