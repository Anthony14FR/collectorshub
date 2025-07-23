<script setup lang="ts">
import DurationDisplay from '@/Components/Expeditions/DurationDisplay.vue';
import RarityBadge from '@/Components/Expeditions/RarityBadge.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import type { ExpeditionRarity } from '@/constants/expedition';
import {
  getRarityDotColor,
  getRarityLabel,
  getStatusColor,
  getStatusLabel
} from '@/utils/expedition';
import { Head, router } from '@inertiajs/vue3';
import { AlertTriangle, ArrowLeft, BarChart3, CheckCircle, Copy, Edit, Eye, Filter, Map, Plus, RotateCcw, Trash2, User, Users, XCircle, Zap } from 'lucide-vue-next';
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
const showDeleteModal = ref(false);
const expeditionToDelete = ref<Expedition | null>(null);

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

const deleteExpedition = (expedition: Expedition) => {
  expeditionToDelete.value = expedition;
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  if (expeditionToDelete.value) {
    router.delete(`/admin/expeditions/${expeditionToDelete.value.id}`, {
      preserveScroll: true
    });
    showDeleteModal.value = false;
    expeditionToDelete.value = null;
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

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider flex items-center gap-2">
            <component :is="Map" :size="28" class="inline align-middle mr-2" />
            GESTION EXPÉDITIONS
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            {{ expeditions.total }} expédition{{ expeditions.total > 1 ? 's' : '' }} configurée{{ expeditions.total > 1 ? 's' : '' }}
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
                  <Button @click="router.visit('/admin/expeditions/create')" variant="primary" size="sm" class="w-full justify-start">
                    <component :is="Plus" :size="16" class="mr-2" /> Nouvelle expédition
                  </Button>
                  <Button @click="router.visit('/admin/')" variant="outline" size="sm" class="w-full justify-start">
                    <component :is="ArrowLeft" :size="16" class="mr-2" /> Dashboard
                  </Button>
                  <Button @click="router.visit('/me')" variant="ghost" size="sm" class="w-full justify-start">
                    <component :is="User" :size="16" class="mr-2" /> Profil
                  </Button>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="BarChart3" :size="18" />
                    STATISTIQUES
                  </h3>
                </div>
                <div class="p-4 space-y-3">
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

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Filter" :size="18" />
                    FILTRES
                  </h3>
                </div>
                <div class="p-4 space-y-3">
                  <div>
                    <label class="block text-xs font-medium text-base-content/70 mb-1">Rareté</label>
                    <select v-model="rarityFilter" @change="applyFilters"
                            class="w-full px-3 py-2 text-sm bg-base-100/50 border border-base-300/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/50 transition-all duration-200">
                      <option value="">Toutes</option>
                      <option v-for="rarity in rarities" :key="rarity" :value="rarity">
                        {{ getRarityLabel(rarity) }}
                      </option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-xs font-medium text-base-content/70 mb-1">Statut</label>
                    <select v-model="statusFilter" @change="applyFilters"
                            class="w-full px-3 py-2 text-sm bg-base-100/50 border border-base-300/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/50 transition-all duration-200">
                      <option value="">Tous</option>
                      <option value="true">Active</option>
                      <option value="false">Inactive</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-xs font-medium text-base-content/70 mb-1">Recherche</label>
                    <input v-model="searchFilter" @keyup.enter="applyFilters" type="text"
                           placeholder="Nom ou description..."
                           class="w-full px-3 py-2 text-sm bg-base-100/50 border border-base-300/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/50 transition-all duration-200 placeholder:text-base-content/50" />
                  </div>

                  <div class="flex gap-2">
                    <Button @click="applyFilters" variant="primary" size="sm" class="flex-1">
                      <component :is="Filter" :size="14" class="mr-1" /> Filtrer
                    </Button>
                    <Button @click="clearFilters" variant="ghost" size="sm">
                      <component :is="RotateCcw" :size="14" />
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
                <div class="flex items-center justify-between">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Map" :size="18" />
                    LISTE DES EXPÉDITIONS
                  </h3>
                  <div class="text-xs text-base-content/70">
                    {{ expeditions.total }} expédition{{ expeditions.total > 1 ? 's' : '' }}
                  </div>
                </div>
              </div>

              <div class="p-6 max-h-[850px] overflow-y-scroll">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div v-for="expedition in sortedExpeditions" :key="expedition.id"
                       class="bg-base-200/30 backdrop-blur-sm rounded-xl border border-base-300/20 overflow-hidden transition-all duration-200 hover:shadow-lg hover:shadow-primary/10 hover:-translate-y-1">

                    <div class="p-4">
                      <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                          <h4 class="font-bold text-base-content text-sm mb-1">{{ expedition.name }}</h4>
                          <p class="text-xs text-base-content/70 line-clamp-2">{{ expedition.description }}</p>
                        </div>
                        <RarityBadge :rarity="expedition.rarity as ExpeditionRarity" size="sm" />
                      </div>

                      <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between text-xs">
                          <span class="text-base-content/70">Durée:</span>
                          <DurationDisplay :minutes="expedition.duration_minutes" size="sm" />
                        </div>

                        <div class="flex items-center justify-between text-xs">
                          <span class="text-base-content/70">Statut:</span>
                          <span :class="[
                            'inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-full border',
                            getStatusColor(expedition.is_active)
                          ]">
                            <component :is="expedition.is_active ? CheckCircle : XCircle" :size="12" />
                            {{ getStatusLabel(expedition.is_active) }}
                          </span>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                          <span class="text-base-content/70">Participants:</span>
                          <div class="flex items-center gap-1">
                            <component :is="Users" :size="12" />
                            {{ expedition.user_expeditions_count || 0 }}
                          </div>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                          <span class="text-base-content/70">Récompenses:</span>
                          <span class="font-medium">{{ expedition.rewards?.length || 0 }}</span>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                          <span class="text-base-content/70">Prérequis:</span>
                          <span class="font-medium">{{ expedition.requirements?.length || 0 }}</span>
                        </div>
                      </div>

                      <div class="flex gap-1 flex-wrap">
                        <Button @click="router.visit(`/admin/expeditions/${expedition.id}`)" variant="ghost" size="sm"
                                class="text-info hover:text-info hover:bg-info/10" title="Voir">
                          <component :is="Eye" :size="14" />
                        </Button>
                        <Button @click="router.visit(`/admin/expeditions/${expedition.id}/edit`)" variant="ghost"
                                size="sm" class="text-warning hover:text-warning hover:bg-warning/10" title="Modifier">
                          <component :is="Edit" :size="14" />
                        </Button>
                        <Button @click="toggleExpedition(expedition.id)"
                                :variant="expedition.is_active ? 'outline' : 'secondary'" size="sm"
                                :class="expedition.is_active ? 'text-error border-error hover:bg-error/10' : 'text-success border-success hover:bg-success/10'"
                                :title="expedition.is_active ? 'Désactiver' : 'Activer'">
                          {{ expedition.is_active ? 'Désactiver' : 'Activer' }}
                        </Button>
                        <Button @click="duplicateExpedition(expedition.id)" variant="outline" size="sm"
                                class="text-secondary border-secondary hover:bg-secondary/10" title="Dupliquer">
                          <component :is="Copy" :size="14" />
                        </Button>
                        <Button @click="deleteExpedition(expedition)" variant="outline" size="sm"
                                class="text-error border-error hover:bg-error/10" title="Supprimer">
                          <component :is="Trash2" :size="14" />
                        </Button>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="expeditions.links && expeditions.links.length > 3"
                     class="flex justify-center mt-6 pt-4 border-t border-base-300/30">
                  <div class="flex gap-1">
                    <template v-for="link in expeditions.links" :key="link.label">
                      <button v-if="link.url" @click="router.visit(link.url)" :class="[
                        'px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                        link.active
                          ? 'bg-primary text-primary-content shadow-lg shadow-primary/20'
                          : 'bg-base-200/50 text-base-content/70 hover:bg-base-200 hover:text-base-content border border-base-300/30'
                      ]" v-html="link.label" />
                      <span v-else :class="[
                        'px-3 py-2 text-sm font-medium rounded-lg bg-base-200/30 text-base-content/50 cursor-not-allowed',
                        link.active ? 'bg-primary/20 text-primary/50' : ''
                      ]" v-html="link.label" />
                    </template>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal :show="showDeleteModal" @close="showDeleteModal = false" max-width="md">
      <div class="p-6">
        <div class="flex items-center gap-3 mb-4">
          <div class="p-2 bg-error/10 rounded-lg">
            <component :is="AlertTriangle" :size="24" class="text-error" />
          </div>
          <div>
            <h3 class="text-lg font-bold text-base-content">Supprimer l'expédition</h3>
            <p class="text-sm text-base-content/70">Cette action est irréversible</p>
          </div>
        </div>

        <div v-if="expeditionToDelete" class="bg-base-200/30 rounded-lg p-4 mb-6">
          <h4 class="font-bold text-base-content mb-2">{{ expeditionToDelete.name }}</h4>
          <p class="text-sm text-base-content/70">{{ expeditionToDelete.description }}</p>
          <div class="flex items-center gap-2 mt-2">
            <RarityBadge :rarity="expeditionToDelete.rarity as ExpeditionRarity" size="sm" />
            <DurationDisplay :minutes="expeditionToDelete.duration_minutes" size="sm" />
          </div>
        </div>

        <div class="flex justify-end gap-3">
          <Button @click="showDeleteModal = false" variant="outline">
            Annuler
          </Button>
          <Button @click="confirmDelete" variant="secondary">
            <component :is="Trash2" :size="16" class="mr-2" />
            Supprimer
          </Button>
        </div>
      </div>
    </Modal>
  </div>
</template>
