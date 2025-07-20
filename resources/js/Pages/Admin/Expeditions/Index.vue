<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface ExpeditionReward {
  type: string;
  amount?: number;
  item_id?: number;
  quantity?: number;
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
}

interface Props {
  expeditions: {
    data: Expedition[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{
      url: string | null;
      label: string;
      active: boolean;
    }>;
  };
  filters?: {
    rarity?: string;
    is_active?: string;
    search?: string;
  };
  rarities: string[];
}

const props = defineProps<Props>();

const rarityFilter = ref(props.filters?.rarity || '');
const statusFilter = ref(props.filters?.is_active || '');
const searchTerm = ref(props.filters?.search || '');
const showDeleteModal = ref(false);
const expeditionToDelete = ref<Expedition | null>(null);

const filteredExpeditions = computed(() => {
  return props.expeditions.data.filter(expedition => {
    const matchesSearch = expedition.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      expedition.description.toLowerCase().includes(searchTerm.value.toLowerCase());
    return matchesSearch;
  });
});

const deleteExpedition = (expedition: Expedition) => {
  expeditionToDelete.value = expedition;
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  if (expeditionToDelete.value) {
    router.delete(`/admin/expeditions/${expeditionToDelete.value.id}`, {
      preserveScroll: true,
      onFinish: () => {
        showDeleteModal.value = false;
        expeditionToDelete.value = null;
      }
    });
  }
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  expeditionToDelete.value = null;
};

const applyFilters = () => {
  const params = new URLSearchParams();

  if (rarityFilter.value) {
    params.append('rarity', rarityFilter.value);
  }

  if (statusFilter.value) {
    params.append('is_active', statusFilter.value);
  }

  if (searchTerm.value) {
    params.append('search', searchTerm.value);
  }

  router.get(`/admin/expeditions?${params.toString()}`, {}, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  rarityFilter.value = '';
  statusFilter.value = '';
  searchTerm.value = '';
  router.get('/admin/expeditions', {}, {
    preserveState: true,
    preserveScroll: true,
  });
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

const formatDuration = (minutes: number) => {
  if (minutes < 60) {
    return `${minutes}min`;
  }
  const hours = Math.floor(minutes / 60);
  const remainingMinutes = minutes % 60;
  return remainingMinutes > 0 ? `${hours}h ${remainingMinutes}min` : `${hours}h`;
};

const getRewardSummary = (rewards: ExpeditionReward[]) => {
  const summary = rewards.slice(0, 2).map(reward => {
    switch (reward.type) {
    case 'cash': return `💰 ${reward.amount?.toLocaleString()}`;
    case 'xp': return `⭐ ${reward.amount?.toLocaleString()} XP`;
    case 'pokeball': return `⚪ ${reward.amount} Pokéballs`;
    case 'masterball': return `🟣 ${reward.amount} Masterballs`;
    case 'item': return `🎁 ${reward.quantity} Item(s)`;
    default: return `${reward.type}`;
    }
  }).join(', ');

  return rewards.length > 2 ? `${summary}...` : summary;
};

const getRequirementSummary = (requirements: ExpeditionRequirement[]) => {
  if (requirements.length === 0) return 'Aucune';
  const first = requirements[0];
  const text = `${first.quantity} ${first.type === 'rarity' ? getRarityLabel(first.value) : first.value}`;
  return requirements.length > 1 ? `${text}...` : text;
};
</script>

<template>
  <Head title="Gestion des expéditions" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-primary to-primary/80 bg-clip-text text-transparent mb-2 tracking-wider">
            🗺️ GESTION EXPÉDITIONS
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            {{ props.expeditions.total }} expédition{{ props.expeditions.total > 1 ? 's' : '' }} configurée{{ props.expeditions.total > 1 ? 's' : '' }}
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-9 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-primary/20">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                  <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                    <span class="text-2xl">📋</span>
                    LISTE DES EXPÉDITIONS
                  </h3>

                  <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <Input
                      v-model="searchTerm"
                      placeholder="🔍 Rechercher une expédition..."
                      class="w-full sm:w-64"
                      size="sm"
                    />

                    <div class="flex gap-2">
                      <Select
                        v-model="rarityFilter"
                        @change="applyFilters"
                        :options="[
                          { value: '', label: 'Toutes raretés' },
                          ...props.rarities.map(rarity => ({ value: rarity, label: getRarityLabel(rarity) }))
                        ]"
                        class="w-32"
                      />

                      <Select
                        v-model="statusFilter"
                        @change="applyFilters"
                        :options="[
                          { value: '', label: 'Tous statuts' },
                          { value: 'true', label: 'Actives' },
                          { value: 'false', label: 'Inactives' }
                        ]"
                        class="w-32"
                      />

                      <Button @click="clearFilters" variant="outline" size="sm">
                        ✨ Reset
                      </Button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead class="bg-gradient-to-r from-base-200/50 to-base-300/30 border-b border-base-300/30">
                    <tr>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Expédition
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Rareté
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Durée
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Récompenses
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Prérequis
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Statut
                      </th>
                      <th class="px-6 py-4 text-center text-sm font-bold text-base-content uppercase tracking-wider">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-base-300/30">
                    <tr
                      v-for="expedition in filteredExpeditions"
                      :key="expedition.id"
                      class="hover:bg-base-200/30 transition-colors duration-200"
                    >
                      <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-sm font-bold">
                            🗺️
                          </div>
                          <div>
                            <div class="font-semibold text-base-content">{{ expedition.name }}</div>
                            <div class="text-sm text-base-content/70 truncate max-w-xs">{{ expedition.description }}</div>
                          </div>
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <span :class="[
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border',
                          getRarityColor(expedition.rarity)
                        ]">
                          {{ getRarityLabel(expedition.rarity) }}
                        </span>
                      </td>

                      <td class="px-6 py-4">
                        <div class="text-sm">
                          <div class="font-semibold text-info">{{ formatDuration(expedition.duration_minutes) }}</div>
                          <div class="text-base-content/70">Durée</div>
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <div class="text-sm text-base-content/70 max-w-xs">
                          {{ getRewardSummary(expedition.rewards) }}
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <div class="text-sm text-base-content/70">
                          {{ getRequirementSummary(expedition.requirements) }}
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <span :class="[
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border',
                          getStatusColor(expedition.is_active)
                        ]">
                          {{ getStatusLabel(expedition.is_active) }}
                        </span>
                      </td>

                      <td class="px-6 py-4">
                        <div class="flex justify-center gap-1">
                          <Button
                            @click="router.visit(`/admin/expeditions/${expedition.id}`)"
                            variant="ghost"
                            size="sm"
                            class="text-info hover:text-info hover:bg-info/20 transition-colors"
                            title="Voir"
                          >
                            👁️
                          </Button>
                          <Button
                            @click="router.visit(`/admin/expeditions/${expedition.id}/edit`)"
                            variant="ghost"
                            size="sm"
                            class="text-warning hover:text-warning hover:bg-warning/20 transition-colors"
                            title="Modifier"
                          >
                            ✏️
                          </Button>
                          <Button
                            @click="deleteExpedition(expedition)"
                            variant="ghost"
                            size="sm"
                            class="text-error hover:text-error hover:bg-error/20 transition-colors"
                            title="Supprimer"
                          >
                            🗑️
                          </Button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div v-if="props.expeditions.last_page > 1" class="p-4 bg-gradient-to-r from-primary/5 to-primary/10 border-t border-primary/20">
                <div class="flex justify-center items-center gap-2 flex-wrap">
                  <template v-for="link in props.expeditions.links" :key="link.label">
                    <Button
                      v-if="link.url"
                      @click="router.visit(link.url)"
                      :variant="link.active ? 'primary' : 'ghost'"
                      size="sm"
                      class="min-w-[2.5rem]"
                      v-html="link.label"
                    />
                    <span v-else class="px-3 py-2 text-base-content/50 text-sm" v-html="link.label" />
                  </template>
                </div>
                <div class="text-xs text-center text-base-content/70 mt-2">
                  Affichage de {{ (props.expeditions.current_page - 1) * props.expeditions.per_page + 1 }}
                  à {{ Math.min(props.expeditions.current_page * props.expeditions.per_page, props.expeditions.total) }}
                  sur {{ props.expeditions.total }} résultats
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-3 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">⚙️</span>
                  ACTIONS
                </h3>
              </div>
              <div class="p-6 space-y-3">
                <Button
                  @click="router.visit('/admin/expeditions/create')"
                  variant="secondary"
                  size="sm"
                  class="w-full justify-start"
                >
                  ➕ Nouvelle expédition
                </Button>
                <Button
                  @click="router.visit('/admin/')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  ← Dashboard
                </Button>
                <Button
                  @click="router.visit('/me')"
                  variant="ghost"
                  size="sm"
                  class="w-full justify-start"
                >
                  🏠 Profil
                </Button>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-primary/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">📊</span>
                  STATISTIQUES
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-center">
                  <div class="text-2xl font-bold text-primary">{{ props.expeditions.total }}</div>
                  <div class="text-sm text-base-content/70">Total expéditions</div>
                </div>
                <div class="grid grid-cols-2 gap-3 text-center">
                  <div>
                    <div class="text-lg font-bold text-success">{{ props.expeditions.data.filter(e => e.is_active).length }}</div>
                    <div class="text-xs text-base-content/70">Actives</div>
                  </div>
                  <div>
                    <div class="text-lg font-bold text-warning">{{ props.expeditions.data.filter(e => e.rarity === 'legendary').length }}</div>
                    <div class="text-xs text-base-content/70">Légendaires</div>
                  </div>
                </div>
                <div class="space-y-2 pt-2 border-t border-base-300/30">
                  <div class="text-xs text-base-content/70 mb-2">Par rareté:</div>
                  <div v-for="rarity in props.rarities" :key="rarity" class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">{{ getRarityLabel(rarity) }}</span>
                    <span class="text-sm font-bold">{{ props.expeditions.data.filter(e => e.rarity === rarity).length }}</span>
                  </div>
                </div>
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
          <span class="font-bold text-error">{{ expeditionToDelete?.name }}</span> ?
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
