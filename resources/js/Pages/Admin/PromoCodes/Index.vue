<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface PromoCode {
  id: number;
  code: string;
  is_global: boolean;
  is_active: boolean;
  cash: number;
  expires_at: string | null;
  created_at: string;
  items: Array<{
    id: number;
    name: string;
    quantity: number;
  }>;
  users: Array<{
    id: number;
    username: string;
  }>;
}

interface Props {
  promoCodes: {
    data: PromoCode[];
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
    is_active?: string;
    is_global?: string;
  };
}

const props = defineProps<Props>();

const isActiveFilter = ref(props.filters?.is_active || '');
const isGlobalFilter = ref(props.filters?.is_global || '');
const searchTerm = ref('');
const showDeleteModal = ref(false);
const promoCodeToDelete = ref<PromoCode | null>(null);

const statusOptions = [
  { value: '', label: 'Tous statuts' },
  { value: '1', label: 'Actifs' },
  { value: '0', label: 'Inactifs' }
];

const typeOptions = [
  { value: '', label: 'Tous types' },
  { value: '1', label: 'Globaux' },
  { value: '0', label: 'Ciblés' }
];

const filteredPromoCodes = computed(() => {
  return props.promoCodes.data.filter(promoCode => {
    const matchesSearch = promoCode.code.toLowerCase().includes(searchTerm.value.toLowerCase());
    return matchesSearch;
  });
});

const deletePromoCode = (promoCode: PromoCode) => {
  promoCodeToDelete.value = promoCode;
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  if (promoCodeToDelete.value) {
    router.delete(`/admin/promocodes/${promoCodeToDelete.value.id}`, {
      preserveScroll: true,
      onFinish: () => {
        showDeleteModal.value = false;
        promoCodeToDelete.value = null;
      }
    });
  }
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  promoCodeToDelete.value = null;
};

const applyFilters = () => {
  const params = new URLSearchParams();

  if (isActiveFilter.value) {
    params.append('is_active', isActiveFilter.value);
  }

  if (isGlobalFilter.value) {
    params.append('is_global', isGlobalFilter.value);
  }

  router.get(`/admin/promocodes?${params.toString()}`, {}, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  isActiveFilter.value = '';
  isGlobalFilter.value = '';
  searchTerm.value = '';
  router.get('/admin/promocodes', {}, {
    preserveState: true,
    preserveScroll: true,
  });
};

const getStatusColor = (promoCode: PromoCode) => {
  if (!promoCode.is_active) return 'text-error bg-error/10 border-error/30';
  if (promoCode.expires_at && new Date(promoCode.expires_at) < new Date()) return 'text-warning bg-warning/10 border-warning/30';
  return 'text-success bg-success/10 border-success/30';
};

const getStatusLabel = (promoCode: PromoCode) => {
  if (!promoCode.is_active) return 'Inactif';
  if (promoCode.expires_at && new Date(promoCode.expires_at) < new Date()) return 'Expiré';
  return 'Actif';
};

const getTypeColor = (isGlobal: boolean) => {
  return isGlobal ? 'text-info bg-info/10 border-info/30' : 'text-secondary bg-secondary/10 border-secondary/30';
};

const getTypeLabel = (isGlobal: boolean) => {
  return isGlobal ? 'Global' : 'Ciblé';
};

const formatDate = (dateString: string | null) => {
  if (!dateString) return 'Jamais';
  return new Date(dateString).toLocaleDateString('fr-FR');
};

const getUsersLabel = (promoCode: PromoCode) => {
  if (promoCode.is_global) return 'Tous';
  return promoCode.users?.length || 0;
};

const getItemsLabel = (promoCode: PromoCode) => {
  return promoCode.items?.length || 0;
};
</script>

<template>
  <Head title="Gestion des codes promo" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-2 tracking-wider">
            🎟️ GESTION CODES PROMO
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            {{ props.promoCodes.total }} code{{ props.promoCodes.total > 1 ? 's' : '' }} promotionnel{{ props.promoCodes.total > 1 ? 's' : '' }}
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-9 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                  <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                    <span class="text-2xl">📋</span>
                    LISTE DES CODES PROMO
                  </h3>

                  <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <Input
                      v-model="searchTerm"
                      placeholder="🔍 Rechercher un code..."
                      class="w-full sm:w-64"
                      size="sm"
                    />

                    <div class="flex gap-2">
                      <Select
                        v-model="isActiveFilter"
                        @change="applyFilters"
                        :options="statusOptions"
                        class="w-32"
                      />

                      <Select
                        v-model="isGlobalFilter"
                        @change="applyFilters"
                        :options="typeOptions"
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
                        Code
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Type
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Cash
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Items
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Utilisateurs
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Statut
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Expiration
                      </th>
                      <th class="px-6 py-4 text-center text-sm font-bold text-base-content uppercase tracking-wider">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-base-300/30">
                    <tr
                      v-for="promoCode in filteredPromoCodes"
                      :key="promoCode.id"
                      class="hover:bg-base-200/30 transition-colors duration-200"
                    >
                      <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 rounded-full bg-gradient-to-br from-warning/20 to-warning/10 flex items-center justify-center text-sm font-bold">
                            🎟️
                          </div>
                          <div>
                            <div class="font-bold text-base-content text-lg">{{ promoCode.code }}</div>
                            <div class="text-sm text-base-content/70">ID: #{{ promoCode.id }}</div>
                          </div>
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <span :class="[
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border',
                          getTypeColor(promoCode.is_global)
                        ]">
                          {{ getTypeLabel(promoCode.is_global) }}
                        </span>
                      </td>

                      <td class="px-6 py-4">
                        <div class="font-semibold text-warning">
                          {{ promoCode.cash.toLocaleString() }} 💰
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <div class="text-sm text-center">
                          <div class="font-semibold text-info">{{ getItemsLabel(promoCode) }}</div>
                          <div class="text-base-content/70">item{{ getItemsLabel(promoCode) > 1 ? 's' : '' }}</div>
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <div class="text-sm text-center">
                          <div class="font-semibold text-secondary">{{ getUsersLabel(promoCode) }}</div>
                          <div class="text-base-content/70">{{ promoCode.is_global ? 'tous' : 'ciblés' }}</div>
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <span :class="[
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border',
                          getStatusColor(promoCode)
                        ]">
                          {{ getStatusLabel(promoCode) }}
                        </span>
                      </td>

                      <td class="px-6 py-4">
                        <div class="text-sm text-base-content/70">
                          {{ formatDate(promoCode.expires_at) }}
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <div class="flex justify-center gap-1">
                          <Button
                            @click="router.visit(`/admin/promocodes/${promoCode.id}`)"
                            variant="ghost"
                            size="sm"
                            class="text-info hover:text-info hover:bg-info/20 transition-colors"
                            title="Voir"
                          >
                            👁️
                          </Button>
                          <Button
                            @click="router.visit(`/admin/promocodes/${promoCode.id}/edit`)"
                            variant="ghost"
                            size="sm"
                            class="text-warning hover:text-warning hover:bg-warning/20 transition-colors"
                            title="Modifier"
                          >
                            ✏️
                          </Button>
                          <Button
                            @click="deletePromoCode(promoCode)"
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

              <div v-if="props.promoCodes.last_page > 1" class="p-4 bg-gradient-to-r from-warning/5 to-warning/10 border-t border-warning/20">
                <div class="flex justify-center items-center gap-2 flex-wrap">
                  <template v-for="link in props.promoCodes.links" :key="link.label">
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
                  Affichage de {{ (props.promoCodes.current_page - 1) * props.promoCodes.per_page + 1 }}
                  à {{ Math.min(props.promoCodes.current_page * props.promoCodes.per_page, props.promoCodes.total) }}
                  sur {{ props.promoCodes.total }} résultats
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
                  @click="router.visit('/admin/promocodes/create')"
                  variant="secondary"
                  size="sm"
                  class="w-full justify-start"
                >
                  ➕ Nouveau code promo
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
              <div class="p-4 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">📊</span>
                  STATISTIQUES
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-center">
                  <div class="text-2xl font-bold text-warning">{{ props.promoCodes.total }}</div>
                  <div class="text-sm text-base-content/70">Total codes</div>
                </div>
                <div class="grid grid-cols-2 gap-3 text-center">
                  <div>
                    <div class="text-lg font-bold text-success">{{ props.promoCodes.data.filter(p => p.is_active).length }}</div>
                    <div class="text-xs text-base-content/70">Actifs</div>
                  </div>
                  <div>
                    <div class="text-lg font-bold text-info">{{ props.promoCodes.data.filter(p => p.is_global).length }}</div>
                    <div class="text-xs text-base-content/70">Globaux</div>
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
          <h3 class="text-xl font-bold text-base-content">Supprimer le code promo</h3>
        </div>
      </template>

      <div class="space-y-4">
        <p class="text-base-content/80">
          Êtes-vous sûr de vouloir supprimer le code promo
          <span class="font-bold text-error">{{ promoCodeToDelete?.code }}</span> ?
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
