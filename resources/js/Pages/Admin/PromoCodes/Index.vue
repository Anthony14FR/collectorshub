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

  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 h-screen w-screen overflow-hidden flex flex-col">
      <div class="shrink-0 p-4 bg-base-200/50 backdrop-blur-md border-b border-base-300/30">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
          <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent tracking-wider">
              🎟️ GESTION DES CODES PROMO
            </h1>
            <p class="text-xs text-base-content/70 uppercase tracking-wider">
              Création et gestion des codes promotionnels
            </p>
          </div>
          <div class="flex items-center gap-2">
            <Link href="/admin">
              <Button variant="ghost" size="sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
              </Button>
            </Link>
            <Link href="/admin/promocodes/create">
              <Button variant="primary" size="sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nouveau Code
              </Button>
            </Link>
          </div>
        </div>
      </div>

      <Toast 
        v-if="showToast"
        :show="showToast"
        type="success"
        title="Succès"
        :message="toastMessage"
        @close="showToast = false"
      />
      
      <Alert v-if="notification" :variant="notification.type" class="fixed top-4 right-4 z-50">
        {{ notification.message }}
      </Alert>

      <div class="p-4 border-b border-base-300/30 bg-base-200/30">
        <div class="max-w-7xl mx-auto flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <input 
              type="text" 
              placeholder="Rechercher un code..."
              class="w-full px-4 py-2 bg-base-200/50 border border-base-300 rounded-lg focus:outline-none focus:border-primary transition-colors"
              v-model="search"
              @keyup.enter="applyFilters"
            />
          </div>
          <div>
            <select 
              v-model="isActiveFilter"
              class="px-4 py-2 bg-base-200/50 border border-base-300 rounded-lg focus:outline-none focus:border-primary transition-colors"
            >
              <option value="">Tous les statuts</option>
              <option value="true">Actifs</option>
              <option value="false">Inactifs</option>
            </select>
          </div>
          <div>
            <select 
              v-model="isGlobalFilter"
              class="px-4 py-2 bg-base-200/50 border border-base-300 rounded-lg focus:outline-none focus:border-primary transition-colors"
            >
              <option value="">Tous les types</option>
              <option value="true">Globaux</option>
              <option value="false">Ciblés</option>
            </select>
          </div>
          <div class="flex items-center gap-2">
            <Button @click="applyFilters" variant="secondary" size="sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
              Filtrer
            </Button>
            <Button @click="resetFilters" variant="ghost" size="sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Réinitialiser
            </Button>
          </div>
        </div>
      </div>

      <div class="flex-1 overflow-auto p-4">
        <div class="max-w-7xl mx-auto bg-base-200/50 backdrop-blur-sm rounded-2xl border border-base-300/30 overflow-hidden h-full">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-base-300/30">
                  <th class="text-left py-3 px-4 text-xs font-semibold text-base-content/70 uppercase cursor-pointer" @click="toggleSort('code')">
                    <div class="flex items-center">
                      Code
                      <svg v-if="sortBy === 'code'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'" />
                      </svg>
                    </div>
                  </th>
                  <th class="text-left py-3 px-4 text-xs font-semibold text-base-content/70 uppercase">Type</th>
                  <th class="text-left py-3 px-4 text-xs font-semibold text-base-content/70 uppercase cursor-pointer" @click="toggleSort('cash')">
                    <div class="flex items-center">
                      Montant
                      <svg v-if="sortBy === 'cash'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'" />
                      </svg>
                    </div>
                  </th>
                  <th class="text-left py-3 px-4 text-xs font-semibold text-base-content/70 uppercase">Récompenses</th>
                  <th class="text-left py-3 px-4 text-xs font-semibold text-base-content/70 uppercase">Cible</th>
                  <th class="text-left py-3 px-4 text-xs font-semibold text-base-content/70 uppercase cursor-pointer" @click="toggleSort('expires_at')">
                    <div class="flex items-center">
                      Expiration
                      <svg v-if="sortBy === 'expires_at'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'" />
                      </svg>
                    </div>
                  </th>
                  <th class="text-left py-3 px-4 text-xs font-semibold text-base-content/70 uppercase">Statut</th>
                  <th class="text-right py-3 px-4 text-xs font-semibold text-base-content/70 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr 
                  v-for="promocode in promocodesList" 
                  :key="promocode.id"
                  class="border-b border-base-300/20 hover:bg-base-300/10 transition-colors"
                >
                  <td class="py-3 px-4">
                    <div class="font-mono font-bold text-primary">{{ promocode.code }}</div>
                  </td>
                  <td class="py-3 px-4">
                    <Badge :variant="promocode.is_global ? 'info' : 'secondary'" size="sm">
                      {{ promocode.is_global ? 'Global' : 'Ciblé' }}
                    </Badge>
                  </td>
                  <td class="py-3 px-4 font-medium">
                    {{ promocode.cash }} 💰
                  </td>
                  <td class="py-3 px-4">
                    {{ getItemsLabel(promocode) }}
                  </td>
                  <td class="py-3 px-4">
                    {{ getUsersLabel(promocode) }}
                  </td>
                  <td class="py-3 px-4">
                    {{ formatDate(promocode.expires_at) }}
                  </td>
                  <td class="py-3 px-4">
                    <Badge :variant="statusBadgeClass(promocode)" size="sm">
                      {{ statusLabel(promocode) }}
                    </Badge>
                  </td>
                  <td class="py-3 px-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                      <Link :href="`/admin/promocodes/${promocode.id}`">
                        <Button variant="ghost" size="xs">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                          </svg>
                        </Button>
                      </Link>
                      <Link :href="`/admin/promocodes/${promocode.id}/edit`">
                        <Button variant="secondary" size="xs">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                          </svg>
                        </Button>
                      </Link>
                      <Button 
                        @click="confirmDelete(promocode)" 
                        variant="ghost" 
                        size="xs"
                        :disabled="isLoading"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </Button>
                    </div>
                  </td>
                </tr>
                <tr v-if="hasNoPromocodes">
                  <td colspan="8" class="py-8 text-center text-base-content/70">
                    <div class="flex flex-col items-center justify-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 text-base-content/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                      <p>Aucun code promo trouvé</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div v-if="props.promoCodes?.links" class="shrink-0 p-4 border-t border-base-300/30 flex justify-center">
        <Pagination :links="props.promoCodes.links" />
      </div>
    </div>
  </div>

  <Modal :show="showDeleteModal" @close="showDeleteModal = false" max-width="md">
    <template #header>
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 bg-gradient-to-br from-error/20 to-error/40 rounded-lg flex items-center justify-center">
          <span class="text-lg">🗑️</span>
        </div>
        <div>
          <h3 class="text-xl font-bold bg-gradient-to-r from-error to-error/80 bg-clip-text text-transparent">
            Confirmer la suppression
          </h3>
        </div>
      </div>
    </template>
    
    <template #default>
      <div class="space-y-4">
        <p>
          Êtes-vous sûr de vouloir supprimer le code promo <span class="font-mono font-bold text-primary">{{ promocodeToDelete?.code }}</span> ?
        </p>
        <p class="text-sm text-base-content/70">
          Cette action est irréversible.
        </p>
        
        <div class="flex gap-3 pt-2">
          <Button
            @click="showDeleteModal = false"
            variant="outline"
            size="lg"
            class="flex-1"
            :disabled="isLoading"
          >
            Annuler
          </Button>

          <Button
            @click="deletePromoCode"
            variant="error"
            size="lg"
            class="flex-1"
            :disabled="isLoading"
          >
            {{ isLoading ? '🔄 En cours...' : 'Supprimer' }}
          </Button>
        </div>
      </div>
    </template>
  </Modal>
</template>