<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface ExpeditionReward {
  type: string;
  amount?: number;
  item_id?: number;
  quantity?: number;
  item?: {
    id: number;
    name: string;
    type: string;
    rarity: string;
  };
}

interface ExpeditionRequirement {
  id: number;
  type: string;
  value: string;
  quantity: number;
}

interface Expedition {
  id: number;
  name: string;
  description: string;
  rarity: string;
  duration_minutes: number;
  rewards: ExpeditionReward[];
  is_active: boolean;
  requirements: ExpeditionRequirement[];
  created_at: string;
  updated_at: string;
  total_completions?: number;
  average_completion_time?: number;
}

interface Props {
  expedition: Expedition;
  items: Array<{ id: number; name: string; type: string; rarity: string }>;
}

const props = defineProps<Props>();

const showDeleteModal = ref(false);

const deleteExpedition = () => {
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  router.delete(`/admin/expeditions/${props.expedition.id}`, {
    onSuccess: () => router.visit('/admin/expeditions'),
    onFinish: () => {
      showDeleteModal.value = false;
    }
  });
};

const cancelDelete = () => {
  showDeleteModal.value = false;
};

const getRarityLabel = (rarity: string) => {
  switch (rarity) {
  case 'normal': return 'Normal';
  case 'rare': return 'Rare';
  case 'epic': return 'Épique';
  case 'legendary': return 'Légendaire';
  default: return rarity;
  }
};

const getRarityColor = (rarity: string) => {
  switch (rarity) {
  case 'normal': return 'text-base-content bg-base-200/50 border-base-300/30';
  case 'rare': return 'text-info bg-info/10 border-info/30';
  case 'epic': return 'text-warning bg-warning/10 border-warning/30';
  case 'legendary': return 'text-error bg-error/10 border-error/30';
  default: return 'text-base-content bg-base-200/50 border-base-300/30';
  }
};

const getStatusColor = (isActive: boolean) => {
  return isActive ? 'text-success bg-success/10 border-success/30' : 'text-error bg-error/10 border-error/30';
};

const getStatusLabel = (isActive: boolean) => {
  return isActive ? 'Active' : 'Inactive';
};

const getRewardIcon = (type: string) => {
  switch (type) {
  case 'cash': return '💰';
  case 'xp': return '⭐';
  case 'pokeball': return '⚪';
  case 'masterball': return '🟣';
  case 'item': return '🎁';
  default: return '🎁';
  }
};

const getRewardLabel = (type: string) => {
  switch (type) {
  case 'cash': return 'Cash';
  case 'xp': return 'Expérience';
  case 'pokeball': return 'Pokéballs';
  case 'masterball': return 'Masterballs';
  case 'item': return 'Item';
  default: return type;
  }
};

const getRewardColor = (type: string) => {
  switch (type) {
  case 'cash': return 'from-warning/10 to-warning/5 border-warning/20';
  case 'xp': return 'from-info/10 to-info/5 border-info/20';
  case 'pokeball': return 'from-primary/10 to-primary/5 border-primary/20';
  case 'masterball': return 'from-error/10 to-error/5 border-error/20';
  case 'item': return 'from-success/10 to-success/5 border-success/20';
  default: return 'from-secondary/10 to-secondary/5 border-secondary/20';
  }
};

const getRequirementIcon = (type: string) => {
  return type === 'rarity' ? '🏆' : '🎯';
};

const getRequirementLabel = (type: string) => {
  return type === 'rarity' ? 'Rareté de Pokémon' : 'Type de Pokémon';
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatSimpleDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const formatDuration = (minutes: number) => {
  if (minutes < 60) {
    return `${minutes} minute${minutes > 1 ? 's' : ''}`;
  }
  const hours = Math.floor(minutes / 60);
  const remainingMinutes = minutes % 60;
  return remainingMinutes > 0 ? `${hours}h ${remainingMinutes}min` : `${hours} heure${hours > 1 ? 's' : ''}`;
};

const getItemById = (id: number) => {
  return props.items.find(item => item.id === id);
};

const getTotalRewardValue = () => {
  return props.expedition.rewards.reduce((total, reward) => {
    if (reward.type === 'cash' && reward.amount) {
      return total + reward.amount;
    }
    return total;
  }, 0);
};

const getExpectedDifficulty = () => {
  const baseScore = {
    'normal': 1,
    'rare': 2,
    'epic': 3,
    'legendary': 4
  }[props.expedition.rarity] || 1;

  const durationScore = Math.floor(props.expedition.duration_minutes / 30);
  const requirementScore = props.expedition.requirements.length;

  return baseScore + durationScore + requirementScore;
};
</script>

<template>
  <Head :title="`Expédition: ${expedition.name}`" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-info to-info/80 bg-clip-text text-transparent mb-2 tracking-wider">
            👁️ DÉTAILS EXPÉDITION
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            Informations complètes de {{ props.expedition.name }}
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-8 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-6 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                <div class="flex items-center gap-4">
                  <div class="w-20 h-20 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-3xl">
                    🗺️
                  </div>
                  <div class="flex-1">
                    <h3 class="text-2xl font-bold text-base-content mb-1">{{ props.expedition.name }}</h3>
                    <p class="text-base-content/70 mb-2">ID: #{{ props.expedition.id }}</p>
                    <div class="flex gap-3">
                      <span :class="[
                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border',
                        getRarityColor(props.expedition.rarity)
                      ]">
                        {{ getRarityLabel(props.expedition.rarity) }}
                      </span>
                      <span :class="[
                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border',
                        getStatusColor(props.expedition.is_active)
                      ]">
                        {{ getStatusLabel(props.expedition.is_active) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                  <div class="bg-gradient-to-br from-info/10 to-info/5 rounded-xl p-6 border border-info/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-info mb-2">{{ formatDuration(props.expedition.duration_minutes) }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">Durée estimée</div>
                    </div>
                  </div>

                  <div class="bg-gradient-to-br from-warning/10 to-warning/5 rounded-xl p-6 border border-warning/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-warning mb-2">{{ props.expedition.rewards.length }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">Récompenses</div>
                    </div>
                  </div>

                  <div class="bg-gradient-to-br from-secondary/10 to-secondary/5 rounded-xl p-6 border border-secondary/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-secondary mb-2">{{ props.expedition.requirements.length }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">Prérequis</div>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                  <div class="space-y-6">
                    <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                      📝 Description
                    </h4>

                    <div class="p-4 bg-base-200/30 rounded-lg">
                      <p class="text-base-content leading-relaxed">{{ props.expedition.description }}</p>
                    </div>

                    <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                      📅 Informations temporelles
                    </h4>

                    <div class="space-y-4">
                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Créé le</span>
                        <span class="font-medium">{{ formatSimpleDate(props.expedition.created_at) }}</span>
                      </div>

                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Dernière modification</span>
                        <span class="font-medium">{{ formatSimpleDate(props.expedition.updated_at) }}</span>
                      </div>

                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Durée d'expédition</span>
                        <span class="font-medium text-info">{{ formatDuration(props.expedition.duration_minutes) }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="space-y-6">
                    <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                      📊 Statistiques
                    </h4>

                    <div class="space-y-4">
                      <div class="p-6 bg-gradient-to-br from-success/10 to-success/5 rounded-xl border border-success/20">
                        <div class="text-center">
                          <div class="text-2xl font-bold text-success mb-1">{{ props.expedition.total_completions || 0 }}</div>
                          <div class="text-sm text-base-content/70">Complétions totales</div>
                        </div>
                      </div>

                      <div class="p-6 bg-gradient-to-br from-primary/10 to-primary/5 rounded-xl border border-primary/20">
                        <div class="text-center">
                          <div class="text-2xl font-bold text-primary mb-1">{{ getExpectedDifficulty() }}/10</div>
                          <div class="text-sm text-base-content/70">Difficulté estimée</div>
                        </div>
                      </div>

                      <div class="p-6 bg-gradient-to-br from-accent/10 to-accent/5 rounded-xl border border-accent/20">
                        <div class="text-center">
                          <div class="text-2xl font-bold text-accent mb-1">{{ formatDuration(props.expedition.average_completion_time || props.expedition.duration_minutes) }}</div>
                          <div class="text-sm text-base-content/70">Temps moyen</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="props.expedition.rewards.length > 0" class="mt-8">
                  <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2 mb-4">
                    🎁 Récompenses détaillées
                  </h4>

                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                      v-for="(reward, index) in props.expedition.rewards"
                      :key="index"
                      :class="['p-4 rounded-xl border hover:border-primary/30 transition-colors', 'bg-gradient-to-br', getRewardColor(reward.type)]"
                    >
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                          <span class="text-2xl">{{ getRewardIcon(reward.type) }}</span>
                          <div>
                            <div class="font-semibold text-base-content">{{ getRewardLabel(reward.type) }}</div>
                            <div class="text-sm text-base-content/70">
                              <span v-if="reward.type === 'item' && reward.item_id">
                                {{ getItemById(reward.item_id)?.name || 'Item inconnu' }}
                              </span>
                              <span v-else>Récompense standard</span>
                            </div>
                          </div>
                        </div>
                        <div class="text-right">
                          <div class="text-xl font-bold text-primary">
                            {{ reward.type === 'item' ? (reward.quantity || 1) : (reward.amount || 0) }}
                          </div>
                          <div class="text-xs text-base-content/60">
                            {{ reward.type === 'item' ? 'quantité' : 'points' }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="props.expedition.requirements.length > 0" class="mt-8">
                  <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2 mb-4">
                    📋 Prérequis détaillés
                  </h4>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                      v-for="requirement in props.expedition.requirements"
                      :key="requirement.id"
                      class="p-4 bg-base-200/30 rounded-xl border border-base-300/20 hover:border-secondary/30 transition-colors"
                    >
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                          <span class="text-xl">{{ getRequirementIcon(requirement.type) }}</span>
                          <div>
                            <div class="font-semibold text-base-content">{{ getRequirementLabel(requirement.type) }}</div>
                            <div class="text-sm text-base-content/70">
                              {{ requirement.type === 'rarity' ? getRarityLabel(requirement.value) : requirement.value }}
                            </div>
                          </div>
                        </div>
                        <div class="text-right">
                          <div class="text-xl font-bold text-secondary">{{ requirement.quantity }}</div>
                          <div class="text-xs text-base-content/60">requis</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-4 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">⚙️</span>
                  ACTIONS
                </h3>
              </div>
              <div class="p-6 space-y-3">
                <Button
                  @click="router.visit(`/admin/expeditions/${props.expedition.id}/edit`)"
                  variant="primary"
                  size="sm"
                  class="w-full justify-start"
                >
                  ✏️ Modifier l'expédition
                </Button>
                <Button
                  @click="router.visit('/admin/expeditions')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  📋 Liste expéditions
                </Button>
                <Button
                  @click="router.visit('/admin')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  🏠 Dashboard
                </Button>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">💰</span>
                  VALEUR TOTALE
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-center">
                  <div class="text-2xl font-bold text-warning">{{ getTotalRewardValue().toLocaleString() }}</div>
                  <div class="text-sm text-base-content/70">Cash total distribué</div>
                </div>

                <div class="grid grid-cols-2 gap-3 text-center">
                  <div>
                    <div class="text-lg font-bold text-info">{{ props.expedition.rewards.filter(r => r.type === 'xp').reduce((sum, r) => sum + (r.amount || 0), 0).toLocaleString() }}</div>
                    <div class="text-xs text-base-content/70">XP total</div>
                  </div>
                  <div>
                    <div class="text-lg font-bold text-success">{{ props.expedition.rewards.filter(r => r.type === 'item').length }}</div>
                    <div class="text-xs text-base-content/70">Items uniques</div>
                  </div>
                </div>

                <div class="pt-4 border-t border-base-300/30 text-center">
                  <div class="text-sm font-medium text-base-content mb-1">Ratio effort/récompense</div>
                  <div class="text-xl font-bold text-primary">
                    {{ Math.round(getTotalRewardValue() / props.expedition.duration_minutes) }}
                  </div>
                  <div class="text-xs text-base-content/60">
                    Cash par minute
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">📈</span>
                  RÉSUMÉ
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4 text-center">
                  <div>
                    <div class="text-xl font-bold text-info">{{ formatDuration(props.expedition.duration_minutes) }}</div>
                    <div class="text-xs text-base-content/70">Durée</div>
                  </div>
                  <div>
                    <div class="text-xl font-bold text-success">{{ getRarityLabel(props.expedition.rarity) }}</div>
                    <div class="text-xs text-base-content/70">Rareté</div>
                  </div>
                </div>

                <div class="text-center pt-4 border-t border-base-300/30">
                  <div class="text-sm font-medium text-base-content mb-1">Statut d'activation</div>
                  <div class="text-2xl font-bold text-primary">
                    {{ props.expedition.is_active ? '🟢' : '🔴' }}
                  </div>
                  <div class="text-xs text-base-content/60">
                    {{ props.expedition.is_active ? 'Expédition active' : 'Expédition inactive' }}
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-error/10 to-error/5 border-b border-error/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">⚠️</span>
                  ZONE DANGER
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <p class="text-sm text-base-content/70">
                  Actions irréversibles sur cette expédition
                </p>
                <Button
                  @click="deleteExpedition"
                  variant="outline"
                  size="sm"
                  class="w-full border-error text-error hover:bg-error hover:text-error-content"
                >
                  🗑️ Supprimer l'expédition
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal :show="showDeleteModal" @close="cancelDelete" max-width="md">
      <template #header>
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-error/20 rounded-lg flex items-center justify-center">
            <span class="text-xl">⚠️</span>
          </div>
          <h3 class="text-xl font-bold text-base-content">Supprimer l'expédition</h3>
        </div>
      </template>

      <div class="space-y-4">
        <p class="text-base-content/80">
          Êtes-vous sûr de vouloir supprimer l'expédition
          <span class="font-bold text-error">{{ props.expedition.name }}</span> ?
        </p>
        <p class="text-sm text-base-content/60">
          Cette action est irréversible et supprimera toutes les données associées.
        </p>

        <div class="flex gap-3 pt-4">
          <Button @click="confirmDelete" variant="outline" class="flex-1 border-error text-error hover:bg-error hover:text-error-content">
            🗑️ Supprimer
          </Button>
          <Button @click="cancelDelete" variant="secondary" class="flex-1">
            Annuler
          </Button>
        </div>
      </div>
    </Modal>
  </div>
</template>
