<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Button from '@/Components/UI/Button.vue';
import Alert from '@/Components/UI/Alert.vue';
import Toast from '@/Components/UI/Toast.vue';
import Badge from '@/Components/UI/Badge.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Modal from '@/Components/UI/Modal.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { Ticket, Home, Plus, Eye, Edit, Trash2, AlertTriangle, Search, Filter, RotateCcw, ChevronUp, ChevronDown, Users, Gift } from 'lucide-vue-next';

const props = defineProps({
  auth: Object,
  promoCodes: Object,
  stats: Object,
  filters: Object,
  sort: Object,
  flash: Object
});

const showDeleteModal = ref(false);
const promocodeToDelete = ref(null);
const notification = ref(null);
const showToast = ref(false);
const toastMessage = ref('');

if (props.flash?.success) {
  toastMessage.value = props.flash.success;
  showToast.value = true;
}

if (props.flash?.error) {
  notification.value = {
    type: 'error',
    message: props.flash.error
  };
  setTimeout(() => {
    notification.value = null;
  }, 5000);
}
const isLoading = ref(false);

const search = ref(props.filters?.search || '');
const isActiveFilter = ref(props.filters?.is_active || '');
const isGlobalFilter = ref(props.filters?.is_global || '');
const sortBy = ref(props.sort?.sort_by || 'created_at');
const sortDirection = ref(props.sort?.sort_direction || 'desc');

const promocodesList = computed(() => {
  return props.promoCodes?.data || [];
});

const hasNoPromocodes = computed(() => {
  return !props.promoCodes?.data || props.promoCodes.data.length === 0;
});

const statusBadgeClass = (promoCode) => {
  if (!promoCode.is_active) return 'error';
  if (promoCode.expires_at && new Date(promoCode.expires_at) < new Date()) return 'warning';
  return 'success';
};

const statusLabel = (promoCode) => {
  if (!promoCode.is_active) return 'Inactif';
  if (promoCode.expires_at && new Date(promoCode.expires_at) < new Date()) return 'Expiré';
  return 'Actif';
};

const confirmDelete = (promoCode) => {
  promocodeToDelete.value = promoCode;
  showDeleteModal.value = true;
};

const deletePromoCode = () => {
  if (!promocodeToDelete.value) return;

  isLoading.value = true;
  router.delete(`/admin/promocodes/${promocodeToDelete.value.id}`, {
    onSuccess: () => {
      showDeleteModal.value = false;
      toastMessage.value = 'Le code promo a été supprimé avec succès';
      showToast.value = true;
    },
    onError: () => {
      notification.value = {
        type: 'error',
        message: 'Une erreur est survenue lors de la suppression'
      };
    },
    onFinish: () => {
      isLoading.value = false;
    }
  });
};

const toggleStatus = (promoCode) => {
  isLoading.value = true;
  router.post(`/admin/promocodes/${promoCode.id}/toggle`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      toastMessage.value = `Le code promo a été ${promoCode.is_active ? 'désactivé' : 'activé'} avec succès`;
      showToast.value = true;
    },
    onError: () => {
      notification.value = {
        type: 'error',
        message: 'Une erreur est survenue lors du changement de statut'
      };
    },
    onFinish: () => {
      isLoading.value = false;
    }
  });
};

const applyFilters = () => {
  const params = {};

  if (search.value) params.search = search.value;
  if (isActiveFilter.value) params.is_active = isActiveFilter.value;
  if (isGlobalFilter.value) params.is_global = isGlobalFilter.value;
  if (sortBy.value !== 'created_at') params.sort_by = sortBy.value;
  if (sortDirection.value !== 'desc') params.sort_direction = sortDirection.value;

  router.get('/admin/promocodes', params, {
    preserveState: true,
    replace: true
  });
};

const resetFilters = () => {
  search.value = '';
  isActiveFilter.value = '';
  isGlobalFilter.value = '';
  sortBy.value = 'created_at';
  sortDirection.value = 'desc';
  router.get('/admin/promocodes', {}, {
    preserveState: true,
    replace: true
  });
};

const toggleSort = (field) => {
  if (sortBy.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortBy.value = field;
    sortDirection.value = 'asc';
  }
  applyFilters();
};

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
};

const getItemsLabel = (promoCode) => {
  if (!promoCode.items || promoCode.items.length === 0) return 'Aucun';
  return `${promoCode.items.length} item${promoCode.items.length > 1 ? 's' : ''}`;
};

const getUsersLabel = (promoCode) => {
  if (promoCode.is_global) return 'Global';
  if (!promoCode.users || promoCode.users.length === 0) return 'Aucun';
  return `${promoCode.users.length} utilisateur${promoCode.users.length > 1 ? 's' : ''}`;
};
</script>

<template>
  <Head title="Gestion des Codes Promo" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider">
            <component :is="Ticket" :size="28" class="inline align-middle mr-2" />
            GESTION DES CODES PROMO
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Création et gestion des codes promotionnels
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
                    <component :is="Ticket" :size="18" />
                    ACTIONS
                  </h3>
                </div>
                <div class="p-3 space-y-2">
                  <Link href="/admin/promocodes/create">
                    <Button variant="secondary" size="sm" class="w-full justify-start mb-2">
                      <component :is="Plus" :size="16" class="mr-2" /> Créer un code
                    </Button>
                  </Link>
                  <Link href="/admin">
                    <Button variant="outline" size="sm" class="w-full justify-start">
                      <component :is="Home" :size="16" class="mr-2" /> Dashboard
                    </Button>
                  </Link>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Ticket" :size="18" />
                    STATISTIQUES
                  </h3>
                </div>
                <div class="p-4 space-y-3">
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Total</span>
                    <span class="text-sm font-bold text-info">{{ props.stats?.total || 0 }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Actifs</span>
                    <span class="text-sm font-bold text-success">{{ props.stats?.active || 0 }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Globaux</span>
                    <span class="text-sm font-bold text-warning">{{ props.stats?.global || 0 }}</span>
                  </div>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-primary/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Filter" :size="18" />
                    FILTRES
                  </h3>
                </div>
                <div class="p-3 space-y-3">
                  <div>
                    <input
                      type="text"
                      placeholder="Rechercher..."
                      class="w-full px-3 py-2 bg-base-200/50 border border-base-300/30 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                      v-model="search"
                      @keyup.enter="applyFilters"
                    />
                  </div>
                  <div>
                    <select
                      v-model="isActiveFilter"
                      class="w-full px-3 py-2 bg-base-200/50 border border-base-300/30 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                    >
                      <option value="">Tous les statuts</option>
                      <option value="true">Actifs</option>
                      <option value="false">Inactifs</option>
                    </select>
                  </div>
                  <div>
                    <select
                      v-model="isGlobalFilter"
                      class="w-full px-3 py-2 bg-base-200/50 border border-base-300/30 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                    >
                      <option value="">Tous les types</option>
                      <option value="true">Globaux</option>
                      <option value="false">Ciblés</option>
                    </select>
                  </div>
                  <div class="flex gap-2">
                    <Button @click="applyFilters" variant="primary" size="sm" class="flex-1">
                      <component :is="Search" :size="14" class="mr-1" />
                      Filtrer
                    </Button>
                    <Button @click="resetFilters" variant="ghost" size="sm">
                      <component :is="RotateCcw" :size="14" />
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-[650px] sm:h-[700px] md:h-[750px] xl:h-[800px] flex flex-col">

              <div class="shrink-0 p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                  <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-base-content/70">Codes promo:</span>
                    <span class="text-sm font-bold">{{ promocodesList.length }}</span>
                    <span class="text-xs text-base-content/50">sur {{ props.promoCodes?.total || 0 }}</span>
                  </div>
                  <Link href="/admin/promocodes/create">
                    <Button variant="primary" size="sm">
                      <component :is="Plus" :size="16" class="mr-2" />
                      Créer
                    </Button>
                  </Link>
                </div>
              </div>

              <div class="flex-1 overflow-y-auto p-6">
                <Alert v-if="notification" :type="notification.type" :message="notification.message" />

                <div v-if="hasNoPromocodes" class="text-center py-12">
                  <h3 class="text-xl font-semibold mb-2">Aucun code promo trouvé</h3>
                  <p class="text-base-content/70 mb-6">Commencez par créer votre premier code promo</p>
                  <Link href="/admin/promocodes/create">
                    <Button variant="primary">
                      <component :is="Plus" :size="20" class="mr-2" />
                      Créer un code promo
                    </Button>
                  </Link>
                </div>

                <div v-else class="space-y-4">
                  <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <div
                      v-for="promocode in promocodesList"
                      :key="promocode.id"
                      class="bg-base-200/30 backdrop-blur-sm rounded-xl border border-base-300/20 p-4 hover:border-primary/40 transition-all duration-200 flex flex-col justify-between min-h-[280px] group hover:scale-105"
                    >
                      <div>
                        <div class="flex items-start justify-between mb-3">
                          <div class="flex-1">
                            <h3 class="font-mono font-bold text-lg text-primary group-hover:text-primary-focus transition-colors">
                              {{ promocode.code }}
                            </h3>
                            <div class="flex items-center gap-2 mt-1">
                              <Badge :variant="promocode.is_global ? 'info' : 'secondary'" size="sm">
                                {{ promocode.is_global ? 'Global' : 'Ciblé' }}
                              </Badge>
                              <Badge :variant="statusBadgeClass(promocode)" size="sm">
                                {{ statusLabel(promocode) }}
                              </Badge>
                            </div>
                          </div>
                        </div>
                        <div class="space-y-2 mb-4">
                          <div class="flex justify-between items-center">
                            <span class="text-sm font-medium">Montant:</span>
                            <span class="text-sm font-bold text-success">{{ promocode.cash }}</span>
                          </div>
                          <div class="flex justify-between items-center">
                            <span class="text-sm font-medium">Récompenses:</span>
                            <span class="text-xs text-base-content/70">{{ getItemsLabel(promocode) }}</span>
                          </div>
                          <div class="flex justify-between items-center">
                            <span class="text-sm font-medium">Cible:</span>
                            <span class="text-xs text-base-content/70">{{ getUsersLabel(promocode) }}</span>
                          </div>
                          <div class="flex justify-between items-center">
                            <span class="text-sm font-medium">Expiration:</span>
                            <span class="text-xs text-base-content/70">{{ formatDate(promocode.expires_at) }}</span>
                          </div>
                        </div>
                      </div>
                      <div class="flex gap-2 pt-2">
                        <Link :href="`/admin/promocodes/${promocode.id}`" class="flex-1">
                          <Button variant="ghost" size="sm" class="w-full" title="Voir le code">
                            <component :is="Eye" :size="16" />
                          </Button>
                        </Link>
                        <Link :href="`/admin/promocodes/${promocode.id}/edit`" class="flex-1">
                          <Button variant="secondary" size="sm" class="w-full" title="Modifier le code">
                            <component :is="Edit" :size="16" />
                          </Button>
                        </Link>
                        <Button
                          variant="error"
                          size="sm"
                          @click="confirmDelete(promocode)"
                          class="flex-1"
                          title="Supprimer le code"
                        >
                          <component :is="Trash2" :size="16" />
                        </Button>
                      </div>
                    </div>
                  </div>

                  <div v-if="props.promoCodes?.links" class="mt-6 flex justify-center">
                    <Pagination :links="props.promoCodes.links" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal :show="showDeleteModal" @close="showDeleteModal = false">
      <div class="p-6">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0">
            <component :is="AlertTriangle" :size="24" class="text-error" />
          </div>
          <div class="ml-3">
            <h3 class="text-lg font-medium text-base-content">Confirmer la suppression</h3>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-sm text-base-content/70">
            Êtes-vous sûr de vouloir supprimer le code promo
            <strong class="font-mono text-primary">{{ promocodeToDelete?.code }}</strong>
            ? Cette action est irréversible.
          </p>
        </div>
        <div class="mt-6 flex justify-end gap-3">
          <Button variant="ghost" @click="showDeleteModal = false">Annuler</Button>
          <Button variant="error" @click="deletePromoCode" :loading="isLoading">
            <component :is="Trash2" :size="16" class="mr-2" />
            Supprimer
          </Button>
        </div>
      </div>
    </Modal>

    <Toast v-if="showToast" :message="toastMessage" @close="showToast = false" />
  </div>
</template>
