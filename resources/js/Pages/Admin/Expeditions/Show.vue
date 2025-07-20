<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import RarityBadge from '@/Components/Expeditions/RarityBadge.vue';
import DurationDisplay from '@/Components/Expeditions/DurationDisplay.vue';
import { formatDate, getRewardLabel, getRequirementLabel, getRarityDotColor } from '@/utils/expedition';
import type { ExpeditionRarity } from '@/constants/expedition';
import { Zap, ArrowLeft, Edit, Eye, AlertTriangle, BarChart3, Map, Hash, Calendar, Clock, DollarSign, Star, Circle, Sparkles, Gift, Target, Shield, CheckCircle, XCircle, Award, Info } from 'lucide-vue-next';

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

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider flex items-center gap-2">
            <component :is="Eye" :size="28" class="inline align-middle mr-2" />
            DÉTAILS EXPÉDITION
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            {{ expedition.name }}
          </p>
        </div>
      </div>

      <div class="container mx-auto px-4 max-w-7xl">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 xl:gap-6">

          <div class="xl:col-span-3 order-1 xl:order-1">
            <div class="space-y-4">

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Zap" :size="18" />
                    ACTIONS
                  </h3>
                </div>
                <div class="p-3 space-y-2">
                  <Button @click="editExpedition" variant="primary" size="sm" class="w-full justify-start">
                    <component :is="Edit" :size="16" class="mr-2" /> Modifier
                  </Button>
                  <Button @click="goBack" variant="outline" size="sm" class="w-full justify-start">
                    <component :is="ArrowLeft" :size="16" class="mr-2" /> Retour
                  </Button>
                  <Button @click="router.visit('/admin/')" variant="ghost" size="sm" class="w-full justify-start">
                    <component :is="Map" :size="16" class="mr-2" /> Dashboard
                  </Button>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="BarChart3" :size="18" />
                    MÉTADONNÉES
                  </h3>
                </div>
                <div class="p-4 space-y-3">
                  <div class="flex justify-between items-center text-xs">
                    <span class="text-base-content/70 flex items-center gap-1">
                      <component :is="Hash" :size="12" />
                      ID:
                    </span>
                    <span class="font-mono font-bold">{{ expedition.id }}</span>
                  </div>
                  <div class="flex justify-between items-center text-xs">
                    <span class="text-base-content/70 flex items-center gap-1">
                      <component :is="Calendar" :size="12" />
                      Créé le:
                    </span>
                    <span>{{ formatDate(expedition.created_at) }}</span>
                  </div>
                  <div class="flex justify-between items-center text-xs">
                    <span class="text-base-content/70 flex items-center gap-1">
                      <component :is="Clock" :size="12" />
                      Modifié le:
                    </span>
                    <span>{{ formatDate(expedition.updated_at) }}</span>
                  </div>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Info" :size="18" />
                    INFORMATIONS
                  </h3>
                </div>
                <div class="p-4 space-y-3">
                  <div class="text-xs text-base-content/70 leading-relaxed">
                    Consultez les détails de cette expédition et ses paramètres.
                  </div>
                  <div class="text-xs text-base-content/70 leading-relaxed">
                    Utilisez le bouton "Modifier" pour ajuster les paramètres.
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="space-y-4">

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
                  <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                      <component :is="Map" :size="18" />
                      INFORMATIONS GÉNÉRALES
                    </h3>
                    <RarityBadge :rarity="expedition.rarity as ExpeditionRarity" />
                  </div>
                </div>
                <div class="p-6 space-y-4">
                  <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-base-content">{{ expedition.name }}</h2>
                  </div>

                  <p class="text-base-content/80 leading-relaxed">{{ expedition.description }}</p>

                  <div class="flex items-center gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                      <component :is="Clock" :size="16" class="text-primary" />
                      <DurationDisplay :minutes="expedition.duration_minutes" />
                    </div>
                    <div class="flex items-center gap-2">
                      <span class="text-sm font-medium">Statut:</span>
                      <span :class="[
                        'inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-full border',
                        expedition.is_active
                          ? 'bg-success/10 text-success border-success/30'
                          : 'bg-error/10 text-error border-error/30'
                      ]">
                        <component :is="expedition.is_active ? CheckCircle : XCircle" :size="12" />
                        {{ expedition.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-4 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Award" :size="18" />
                    RÉCOMPENSES
                  </h3>
                </div>
                <div class="p-6">
                  <div v-if="expedition.rewards.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div v-for="(reward, index) in expedition.rewards" :key="index"
                         class="flex items-center gap-3 p-3 bg-base-200/30 backdrop-blur-sm rounded-lg border border-base-300/20">
                      <div class="w-8 h-8 flex items-center justify-center">
                        <component v-if="reward.type === 'cash'" :is="DollarSign" :size="20" class="text-success" />
                        <component v-else-if="reward.type === 'xp'" :is="Star" :size="20" class="text-warning" />
                        <img v-else-if="reward.type === 'pokeball'" src="/images/items/pokeball.png" alt="Pokéball" class="w-6 h-6">
                        <img v-else-if="reward.type === 'masterball'" src="/images/items/masterball.png" alt="Masterball" class="w-6 h-6">
                        <component v-else :is="Gift" :size="20" class="text-secondary" />
                      </div>
                      <span class="font-medium text-sm">{{ getRewardLabel(reward, items) }}</span>
                    </div>
                  </div>

                  <div v-else class="text-center py-8 text-base-content/60">
                    <component :is="Award" :size="32" class="mx-auto mb-2 opacity-50" />
                    <p class="text-sm">Aucune récompense configurée</p>
                  </div>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-4 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Shield" :size="18" />
                    PRÉREQUIS
                  </h3>
                </div>
                <div class="p-6">
                  <div v-if="expedition.requirements.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div v-for="(req, index) in expedition.requirements" :key="index"
                         class="flex items-center gap-3 p-3 bg-base-200/30 backdrop-blur-sm rounded-lg border border-base-300/20">
                      <div class="w-8 h-8 flex items-center justify-center">
                        <div v-if="req.type === 'rarity'" class="w-3 h-3 rounded-full" :class="getRarityDotColor(req.value)"></div>
                        <img v-else-if="req.type === 'type'" :src="`/images/types/${req.value}.png`" :alt="req.value" class="w-6 h-6 object-contain">
                        <component v-else :is="Target" :size="16" class="text-warning" />
                      </div>
                      <span class="font-medium text-sm">{{ getRequirementLabel(req) }}</span>
                    </div>
                  </div>

                  <div v-else class="text-center py-8 text-base-content/60">
                    <component :is="Shield" :size="32" class="mx-auto mb-2 opacity-50" />
                    <p class="text-sm">Aucun prérequis configuré</p>
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
