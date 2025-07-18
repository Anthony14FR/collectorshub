<script setup lang="ts">
import DurationDisplay from '@/Components/Expeditions/DurationDisplay.vue';
import RarityBadge from '@/Components/Expeditions/RarityBadge.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import type { ExpeditionRarity } from '@/constants/expedition';
import {
  getRarityDotColor,
  getRarityLabel,
  getStatusColor,
  getStatusLabel
} from '@/utils/expedition';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Expedition {
  id: number;
  name: string;
  description: string;
  rarity: 'normal' | 'rare' | 'epic' | 'legendary';
  duration_minutes: number;
  is_active: boolean;
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
  user_expeditions_count?: number;
  created_at: string;
  updated_at: string;
}

interface PaginatedExpeditions {
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
}

const props = defineProps<{
  expeditions: PaginatedExpeditions;
  stats: {
    total: number;
    active: number;
    inactive: number;
    by_rarity: Record<string, number>;
  };
  filters?: {
    rarity?: string;
    is_active?: boolean;
    search?: string;
  };
  rarities: string[];
}>();

const sortField = ref<string>('');
const sortDirection = ref<'asc' | 'desc'>('asc');
const rarityFilter = ref<string>(props.filters?.rarity || '');
const statusFilter = ref<string>(props.filters?.is_active !== undefined ? props.filters.is_active.toString() : '');
const searchFilter = ref<string>(props.filters?.search || '');

const sortedExpeditions = computed(() => {
  if (!sortField.value) return props.expeditions.data;

  const sorted = [...props.expeditions.data].sort((a, b) => {
    let aValue = a[sortField.value as keyof Expedition];
    let bValue = b[sortField.value as keyof Expedition];

    if (aValue === null || aValue === undefined) aValue = '';
    if (bValue === null || bValue === undefined) bValue = '';

    if (sortField.value === 'is_active') {
      aValue = aValue ? 1 : 0;
      bValue = bValue ? 1 : 0;
    }

    if (typeof aValue === 'string' && typeof bValue === 'string') {
      aValue = aValue.toLowerCase();
      bValue = bValue.toLowerCase();
    }

    if (aValue < bValue) return sortDirection.value === 'asc' ? -1 : 1;
    if (aValue > bValue) return sortDirection.value === 'asc' ? 1 : -1;
    return 0;
  });

  return sorted;
});

const deleteExpedition = (expeditionId: number) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette expédition ?')) {
    router.delete(`/admin/expeditions/${expeditionId}`, {
      preserveScroll: true
    });
  }
};

const toggleExpedition = (expeditionId: number) => {
  router.post(`/admin/expeditions/${expeditionId}/toggle`, {}, {
    preserveScroll: true
  });
};

const duplicateExpedition = (expeditionId: number) => {
  router.post(`/admin/expeditions/${expeditionId}/duplicate`, {}, {
    preserveScroll: true
  });
};

const sortBy = (field: string) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
};

const getSortIcon = (field: string) => {
  if (sortField.value !== field) return '↕️';
  return sortDirection.value === 'asc' ? '↑' : '↓';
};

const applyFilters = () => {
  const params: Record<string, string> = {};

  if (rarityFilter.value) {
    params.rarity = rarityFilter.value;
  }

  if (statusFilter.value) {
    params.is_active = statusFilter.value;
  }

  if (searchFilter.value) {
    params.search = searchFilter.value;
  }

  router.get('/admin/expeditions', params, {
    preserveState: false,
    preserveScroll: true,
    replace: true
  });
};

const clearFilters = () => {
  sortField.value = '';
  sortDirection.value = 'asc';
  rarityFilter.value = '';
  statusFilter.value = '';
  searchFilter.value = '';
  router.get('/admin/expeditions', {}, {
    preserveState: false,
    preserveScroll: true,
    replace: true
  });
};
</script>

<template>

  <Head title="Gestion des expéditions" />

  <div class="h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative overflow-hidden">
    <BackgroundEffects />

    <div class="relative z-10 h-full w-full flex flex-col">
      <div class="flex justify-center pt-4 mb-4 flex-shrink-0">
        <div class="text-center">
          <h1
            class="text-xl md:text-2xl font-bold bg-gradient-to-r from-primary to-primary/80 bg-clip-text text-transparent mb-1 tracking-wider flex items-center gap-2">
            <svg class="w-6 h-6 md:w-8 md:h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 20l-5.447-2.724A1 1 0 113 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
              </path>
            </svg>
            GESTION EXPÉDITIONS
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            {{ expeditions.total }} expédition{{ expeditions.total > 1 ? 's' : '' }} configurée{{ expeditions.total > 1
              ? 's' : '' }}
          </p>
        </div>
      </div>

      <div class="flex-1 flex flex-col lg:flex-row gap-4 px-2 md:px-4 lg:px-8 min-h-0 pb-4">
        <div class="flex-1 lg:mr-4 flex flex-col min-h-0" style="max-height: 85vh;">
          <div
            class="bg-base-100/60 backdrop-blur-sm rounded-t-xl border border-b-0 border-base-300/30 p-4 flex-shrink-0">
            <div class="flex flex-wrap gap-4 items-center">
              <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-base-content/70">Rareté:</label>
                <select v-model="rarityFilter" @change="applyFilters"
                        class="select select-sm select-bordered bg-base-100/80 border-base-300/50 text-sm min-w-[120px]">
                  <option value="">Toutes</option>
                  <option v-for="rarity in rarities" :key="rarity" :value="rarity">
                    {{ getRarityLabel(rarity) }}
                  </option>
                </select>
              </div>

              <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-base-content/70">Statut:</label>
                <select v-model="statusFilter" @change="applyFilters"
                        class="select select-sm select-bordered bg-base-100/80 border-base-300/50 text-sm min-w-[100px]">
                  <option value="">Tous</option>
                  <option value="true">Active</option>
                  <option value="false">Inactive</option>
                </select>
              </div>

              <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-base-content/70">Recherche:</label>
                <input v-model="searchFilter" @keyup.enter="applyFilters" type="text"
                       placeholder="Nom ou description..."
                       class="input input-sm input-bordered bg-base-100/80 border-base-300/50 text-sm min-w-[200px]" />
                <Button @click="applyFilters" variant="ghost" size="sm" class="text-primary hover:text-primary">
                  🔍
                </Button>
              </div>

              <Button @click="clearFilters" variant="ghost" size="sm"
                      class="text-base-content/70 hover:text-base-content">
                🗑️ Réinitialiser
              </Button>
            </div>
          </div>

          <div
            class="bg-base-100/60 backdrop-blur-sm rounded-b-xl border border-base-300/30 overflow-hidden flex flex-col flex-1">
            <div class="flex-shrink-0 p-3 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-primary/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">📋</span>
                LISTE DES EXPÉDITIONS
              </h3>
            </div>

            <div class="flex-1 overflow-auto min-h-0">
              <table class="w-full min-w-[1200px]">
                <thead class="sticky top-0 bg-base-200/90 backdrop-blur-md border-b border-base-300/30 z-[60]">
                  <tr>
                    <th
                      class="px-2 md:px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                      <button @click="sortBy('name')"
                              class="flex items-center gap-1 hover:text-base-content transition-colors">
                        Expédition
                        <span class="text-base-content/50">{{ getSortIcon('name') }}</span>
                      </button>
                    </th>
                    <th
                      class="px-2 md:px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                      <button @click="sortBy('rarity')"
                              class="flex items-center gap-1 hover:text-base-content transition-colors">
                        Rareté
                        <span class="text-base-content/50">{{ getSortIcon('rarity') }}</span>
                      </button>
                    </th>
                    <th
                      class="px-2 md:px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                      <button @click="sortBy('duration_minutes')"
                              class="flex items-center gap-1 hover:text-base-content transition-colors">
                        Durée
                        <span class="text-base-content/50">{{ getSortIcon('duration_minutes') }}</span>
                      </button>
                    </th>
                    <th
                      class="px-2 md:px-4 py-3 text-center text-xs font-bold text-base-content/70 uppercase tracking-wider">
                      <button @click="sortBy('is_active')"
                              class="flex items-center gap-1 hover:text-base-content transition-colors mx-auto">
                        Statut
                        <span class="text-base-content/50">{{ getSortIcon('is_active') }}</span>
                      </button>
                    </th>
                    <th
                      class="px-2 md:px-4 py-3 text-center text-xs font-bold text-base-content/70 uppercase tracking-wider">
                      Récompenses
                    </th>
                    <th
                      class="px-2 md:px-4 py-3 text-center text-xs font-bold text-base-content/70 uppercase tracking-wider">
                      Prérequis
                    </th>
                    <th
                      class="px-2 md:px-4 py-3 text-center text-xs font-bold text-base-content/70 uppercase tracking-wider">
                      Participants
                    </th>
                    <th
                      class="px-2 md:px-4 py-3 text-center text-xs font-bold text-base-content/70 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-base-300/20">
                  <tr v-for="expedition in sortedExpeditions" :key="expedition.id"
                      class="hover:bg-base-200/30 transition-colors">
                    <td class="px-2 md:px-4 py-4">
                      <div class="min-w-0 flex-1">
                        <div class="font-semibold text-base-content truncate">{{ expedition.name }}</div>
                        <div class="text-xs text-base-content/60 truncate max-w-[200px]">{{ expedition.description }}
                        </div>
                      </div>
                    </td>

                    <td class="px-2 md:px-4 py-4">
                      <RarityBadge :rarity="expedition.rarity as ExpeditionRarity" size="sm" />
                    </td>

                    <td class="px-2 md:px-4 py-4">
                      <DurationDisplay :minutes="expedition.duration_minutes" size="sm" />
                    </td>

                    <td class="px-2 md:px-4 py-4 text-center">
                      <span :class="[
                        'inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-full border',
                        getStatusColor(expedition.is_active)
                      ]">
                        <div class="w-1.5 h-1.5 rounded-full" :class="expedition.is_active ? 'bg-success' : 'bg-error'">
                        </div>
                        {{ getStatusLabel(expedition.is_active) }}
                      </span>
                    </td>

                    <td class="px-2 md:px-4 py-4 text-center">
                      <div class="relative group">
                        <div class="flex items-center justify-center gap-1 text-sm font-medium cursor-pointer">
                          <svg class="w-3 h-3 text-base-content/60" fill="none" stroke="currentColor"
                               viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                          </svg>
                          {{ expedition.rewards?.length || 0 }}
                        </div>
                        <div
                          class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 w-80 bg-gray-800 text-white rounded-lg shadow-lg p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-[10000]">
                          <div v-if="expedition.rewards?.length" class="space-y-2">
                            <div v-for="reward in expedition.rewards" :key="reward.type"
                                 class="flex items-center justify-between gap-2 text-sm">
                              <div class="flex items-center gap-2">
                                <div class="w-6 h-6 flex items-center justify-center">
                                  <span v-if="reward.type === 'cash'" class="text-green-400">💰</span>
                                  <span v-else-if="reward.type === 'xp'" class="text-blue-400">⭐</span>
                                  <img v-else-if="reward.type === 'pokeball'" src="/images/items/pokeball.png"
                                       alt="Pokéball" class="w-5 h-5">
                                  <img v-else-if="reward.type === 'masterball'" src="/images/items/masterball.png"
                                       alt="Masterball" class="w-5 h-5">
                                  <span v-else class="text-purple-400">🎁</span>
                                </div>
                                <span class="text-gray-200">{{ reward.type === 'cash' ? 'Cash' : reward.type === 'xp' ?
                                  'XP' : reward.type === 'pokeball' ? 'Pokéball' : reward.type === 'masterball' ?
                                    'Masterball' : 'Item' }}</span>
                              </div>
                              <span class="font-bold text-blue-300">{{ reward.amount || reward.quantity }}</span>
                            </div>
                          </div>
                          <div v-else class="text-center text-sm text-gray-400">Aucune récompense</div>
                        </div>
                      </div>
                    </td>

                    <td class="px-2 md:px-4 py-4 text-center">
                      <div class="relative group">
                        <div class="flex items-center justify-center gap-1 text-sm font-medium cursor-pointer">
                          <svg class="w-3 h-3 text-base-content/60" fill="none" stroke="currentColor"
                               viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                          </svg>
                          {{ expedition.requirements?.length || 0 }}
                        </div>
                        <div
                          class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 w-80 bg-gray-800 text-white rounded-lg shadow-lg p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-[10000]">
                          <div v-if="expedition.requirements?.length" class="space-y-1.5">
                            <div v-for="req in expedition.requirements" :key="req.type + req.value"
                                 class="flex items-center justify-between gap-2 text-xs">
                              <div class="flex items-center gap-2">
                                <div class="w-6 h-6 flex items-center justify-center">
                                  <div v-if="req.type === 'rarity'" class="w-2 h-2 rounded-full"
                                       :class="getRarityDotColor(req.value)"></div>
                                  <img v-else-if="req.type === 'type'" :src="`/images/types/${req.value}.png`"
                                       :alt="req.value" class="w-5 h-5 object-contain">
                                  <span v-else class="text-blue-400">📝</span>
                                </div>
                                <span class="text-gray-200">{{ req.type === 'rarity' ? 'Rareté' : 'Type' }} {{ req.value
                                }}</span>
                              </div>
                              <span class="font-bold text-blue-300">{{ req.quantity }}</span>
                            </div>
                          </div>
                          <div v-else class="text-center text-xs text-gray-400">Aucun prérequis</div>
                        </div>
                      </div>
                    </td>

                    <td class="px-2 md:px-4 py-4 text-center">
                      <div class="flex items-center justify-center gap-1 text-sm font-medium">
                        <svg class="w-3 h-3 text-base-content/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                          </path>
                        </svg>
                        {{ expedition.user_expeditions_count || 0 }}
                      </div>
                    </td>

                    <td class="px-2 md:px-4 py-4">
                      <div class="flex justify-center gap-1 flex-wrap">
                        <Button @click="router.visit(`/admin/expeditions/${expedition.id}`)" variant="ghost" size="sm"
                                class="text-info hover:text-info hover:bg-info/10" title="Voir">
                          👁️
                        </Button>
                        <Button @click="router.visit(`/admin/expeditions/${expedition.id}/edit`)" variant="ghost"
                                size="sm" class="text-warning hover:text-warning hover:bg-warning/10" title="Modifier">
                          ✏️
                        </Button>
                        <Button @click="toggleExpedition(expedition.id)"
                                :variant="expedition.is_active ? 'outline' : 'secondary'" size="sm"
                                :class="expedition.is_active ? 'text-error border-error hover:bg-error/10' : 'text-success border-success hover:bg-success/10'"
                                :title="expedition.is_active ? 'Désactiver' : 'Activer'">
                          {{ expedition.is_active ? 'Désactiver' : 'Activer' }}
                        </Button>
                        <Button @click="duplicateExpedition(expedition.id)" variant="outline" size="sm"
                                class="text-secondary border-secondary hover:bg-secondary/10" title="Dupliquer">
                          Dupliquer
                        </Button>
                        <Button @click="deleteExpedition(expedition.id)" variant="outline" size="sm"
                                class="text-error border-error hover:bg-error/10" title="Supprimer">
                          🗑️
                        </Button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-if="expeditions.links && expeditions.links.length > 3"
                 class="flex justify-center p-4 border-t border-base-300/30">
              <div class="join">
                <template v-for="link in expeditions.links" :key="link.label">
                  <button v-if="link.url" @click="router.visit(link.url)" :class="[
                    'join-item btn btn-sm',
                    link.active ? 'btn-primary' : 'btn-outline'
                  ]" v-html="link.label" />
                  <span v-else :class="[
                    'join-item btn btn-sm btn-disabled',
                    link.active ? 'btn-primary' : 'btn-outline'
                  ]" v-html="link.label" />
                </template>
              </div>
            </div>
          </div>
        </div>

        <div class="lg:w-64 lg:flex-shrink-0">
          <div class="space-y-4">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-3 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                  <span class="text-lg">⚙️</span>
                  ACTIONS
                </h3>
              </div>
              <div class="p-3 space-y-2">
                <Button @click="router.visit('/admin/expeditions/create')" variant="secondary" size="sm" class="w-full">
                  ➕ Nouvelle expédition
                </Button>
                <Button @click="router.visit('/admin/')" variant="outline" size="sm" class="w-full">
                  ← Dashboard
                </Button>
                <Button @click="router.visit('/me')" variant="ghost" size="sm" class="w-full">
                  🏠 Profil
                </Button>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                  <span class="text-lg">📊</span>
                  STATISTIQUES
                </h3>
              </div>
              <div class="p-3 space-y-3">
                <div class="grid grid-cols-2 gap-2 text-xs">
                  <div class="bg-base-200/40 rounded-lg p-2">
                    <div class="text-base-content/70">Total</div>
                    <div class="font-bold text-lg">{{ stats.total }}</div>
                  </div>
                  <div class="bg-success/10 rounded-lg p-2">
                    <div class="text-success/70">Actives</div>
                    <div class="font-bold text-lg text-success">{{ stats.active }}</div>
                  </div>
                  <div class="bg-error/10 rounded-lg p-2">
                    <div class="text-error/70">Inactives</div>
                    <div class="font-bold text-lg text-error">{{ stats.inactive }}</div>
                  </div>
                </div>

                <div class="border-t border-base-300/30 pt-3">
                  <div class="text-xs text-base-content/70 mb-2 font-medium">Par rareté</div>
                  <div class="space-y-1">
                    <div v-for="(count, rarity) in stats.by_rarity" :key="rarity"
                         class="flex justify-between items-center text-xs">
                      <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full" :class="getRarityDotColor(rarity)"></div>
                        <span>{{ getRarityLabel(rarity) }}</span>
                      </div>
                      <span class="font-bold">{{ count }}</span>
                    </div>
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