<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import RarityBadge from '@/Components/Expeditions/RarityBadge.vue';
import DurationDisplay from '@/Components/Expeditions/DurationDisplay.vue';
import { formatDate, getRewardLabel, getRequirementLabel, getRarityDotColor } from '@/utils/expedition';
import type { ExpeditionRarity } from '@/constants/expedition';

interface Expedition {
  id: number;
  name: string;
  description: string;
  rarity: string;
  duration_minutes: number;
  rewards: Array<{
    type: string;
    amount?: number;
    item_id?: number;
    quantity?: number;
  }>;
  requirements: Array<{
    type: string;
    value: string;
    quantity: number;
  }>;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

interface Props {
  expedition: Expedition;
  items: Array<{
    id: number;
    name: string;
    image?: string;
  }>;
}

const props = defineProps<Props>();

const goBack = () => {
  router.visit('/admin/expeditions');
};

const editExpedition = () => {
  router.visit(`/admin/expeditions/${props.expedition.id}/edit`);
};
</script>

<template>
  <Head :title="`Expédition: ${expedition.name}`" />
  
  <div class="h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative overflow-hidden">
    <BackgroundEffects />

    <div class="relative z-10 h-full w-full flex flex-col">
      <div class="flex justify-center pt-4 mb-4 flex-shrink-0">
        <div class="text-center">
          <h1 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-primary to-primary/80 bg-clip-text text-transparent mb-1 tracking-wider flex items-center gap-2">
            <svg class="w-6 h-6 md:w-8 md:h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            DÉTAILS EXPÉDITION
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            {{ expedition.name }}
          </p>
        </div>
      </div>

      <div class="flex-1 px-2 md:px-4 lg:px-8 pb-4 overflow-hidden">
        <div class="h-full max-w-5xl mx-auto w-full">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 p-6 h-full overflow-y-auto">
            <div class="flex flex-col lg:flex-row gap-6 h-full">
              <div class="flex-1 space-y-6">
                <div class="bg-base-200/50 rounded-lg p-4 space-y-4">
                  <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold">{{ expedition.name }}</h2>
                    <RarityBadge :rarity="expedition.rarity as ExpeditionRarity" />
                  </div>
                  
                  <p class="text-base-content/80">{{ expedition.description }}</p>
                  
                  <div class="flex items-center gap-4">
                    <DurationDisplay :minutes="expedition.duration_minutes" />
                    <div class="flex items-center gap-2">
                      <span class="text-sm font-medium">Statut:</span>
                      <span :class="[
                        'px-2 py-1 text-xs font-semibold rounded-full',
                        expedition.is_active 
                          ? 'bg-success/20 text-success' 
                          : 'bg-error/20 text-error'
                      ]">
                        {{ expedition.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </div>
                  </div>
                </div>

                <div class="bg-base-200/50 rounded-lg p-4 space-y-4">
                  <h3 class="text-lg font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Récompenses
                  </h3>
                  
                  <div v-if="expedition.rewards.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div v-for="(reward, index) in expedition.rewards" :key="index" 
                         class="flex items-center gap-3 p-3 bg-base-100/50 rounded-lg">
                      <div class="w-8 h-8 flex items-center justify-center">
                        <span v-if="reward.type === 'cash'" class="text-2xl">💰</span>
                        <span v-else-if="reward.type === 'xp'" class="text-2xl">⭐</span>
                        <img v-else-if="reward.type === 'pokeball'" src="/images/items/pokeball.png" alt="Pokéball" class="w-6 h-6">
                        <img v-else-if="reward.type === 'masterball'" src="/images/items/masterball.png" alt="Masterball" class="w-6 h-6">
                        <span v-else class="text-2xl">🎁</span>
                      </div>
                      <span class="font-medium">{{ getRewardLabel(reward, items) }}</span>
                    </div>
                  </div>
                  
                  <div v-else class="text-center py-4 text-base-content/60">
                    Aucune récompense configurée
                  </div>
                </div>

                <div class="bg-base-200/50 rounded-lg p-4 space-y-4">
                  <h3 class="text-lg font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Prérequis
                  </h3>
                  
                  <div v-if="expedition.requirements.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div v-for="(req, index) in expedition.requirements" :key="index" 
                         class="flex items-center gap-3 p-3 bg-base-100/50 rounded-lg">
                      <div class="w-8 h-8 flex items-center justify-center">
                        <div v-if="req.type === 'rarity'" class="w-3 h-3 rounded-full" :class="getRarityDotColor(req.value)"></div>
                        <img v-else-if="req.type === 'type'" :src="`/images/types/${req.value}.png`" :alt="req.value" class="w-6 h-6 object-contain">
                        <span v-else class="text-warning">📝</span>
                      </div>
                      <span class="font-medium">{{ getRequirementLabel(req) }}</span>
                    </div>
                  </div>
                  
                  <div v-else class="text-center py-4 text-base-content/60">
                    Aucun prérequis configuré
                  </div>
                </div>
              </div>

              <div class="lg:w-64 space-y-4">
                <div class="bg-base-200/50 rounded-lg p-4 space-y-4">
                  <h3 class="text-lg font-semibold">Métadonnées</h3>
                  
                  <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                      <span class="text-base-content/70">ID:</span>
                      <span class="font-mono">{{ expedition.id }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-base-content/70">Créé le:</span>
                      <span>{{ formatDate(expedition.created_at) }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-base-content/70">Modifié le:</span>
                      <span>{{ formatDate(expedition.updated_at) }}</span>
                    </div>
                  </div>
                </div>

                <div class="bg-base-200/50 rounded-lg p-4 space-y-3">
                  <h3 class="text-lg font-semibold">Actions</h3>
                  
                  <div class="space-y-2">
                    <Button
                      @click="editExpedition"
                      variant="primary"
                      size="sm"
                      class="w-full"
                    >
                      ✏️ Modifier
                    </Button>
                    
                    <Button
                      @click="goBack"
                      variant="outline"
                      size="sm"
                      class="w-full"
                    >
                      ← Retour
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 