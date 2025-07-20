<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface PromoCode {
  id: number;
  code: string;
  is_global: boolean;
  is_active: boolean;
  cash: number;
  expires_at: string | null;
  created_at: string;
  updated_at: string;
  items: Array<{
    id: number;
    name: string;
    type: string;
    rarity: string;
    pivot: { quantity: number };
  }>;
  users: Array<{
    id: number;
    username: string;
    email: string;
    pivot: { is_used: boolean; used_at: string | null };
  }>;
}

interface UsageStats {
  total_users: number;
  used_count: number;
  unused_count: number;
  last_used: string | null;
}

interface Props {
  promoCode: PromoCode;
  usageStats: UsageStats;
}

const props = defineProps<Props>();

const showDeleteModal = ref(false);

const deletePromoCode = () => {
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  router.delete(`/admin/promocodes/${props.promoCode.id}`, {
    onSuccess: () => router.visit('/admin/promocodes'),
    onFinish: () => {
      showDeleteModal.value = false;
    }
  });
};

const cancelDelete = () => {
  showDeleteModal.value = false;
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

const getRarityColor = (rarity: string) => {
  switch (rarity) {
  case 'legendary': return 'text-error';
  case 'epic': return 'text-warning';
  case 'rare': return 'text-info';
  default: return 'text-base-content';
  }
};

const formatDate = (dateString: string | null) => {
  if (!dateString) return 'Jamais';
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
</script>

<template>
  <Head :title="`Code Promo: ${promoCode.code}`" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-2 tracking-wider">
            🎟️ CODE PROMOTIONNEL
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            Détails du code {{ props.promoCode.code }}
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-8 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-6 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <div class="flex items-center gap-4">
                  <div class="w-20 h-20 rounded-full bg-gradient-to-br from-warning/20 to-warning/10 flex items-center justify-center text-3xl">
                    🎟️
                  </div>
                  <div class="flex-1">
                    <h3 class="text-3xl font-bold text-warning mb-1">{{ props.promoCode.code }}</h3>
                    <p class="text-base-content/70 mb-2">ID: #{{ props.promoCode.id }}</p>
                    <div class="flex gap-3">
                      <span :class="[
                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border',
                        getTypeColor(props.promoCode.is_global)
                      ]">
                        {{ getTypeLabel(props.promoCode.is_global) }}
                      </span>
                      <span :class="[
                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border',
                        getStatusColor(props.promoCode)
                      ]">
                        {{ getStatusLabel(props.promoCode) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                  <div class="bg-gradient-to-br from-warning/10 to-warning/5 rounded-xl p-6 border border-warning/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-warning mb-2">{{ props.promoCode.cash.toLocaleString() }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">Cash 💰</div>
                    </div>
                  </div>

                  <div class="bg-gradient-to-br from-info/10 to-info/5 rounded-xl p-6 border border-info/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-info mb-2">{{ props.promoCode.items.length }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">Items</div>
                    </div>
                  </div>

                  <div class="bg-gradient-to-br from-secondary/10 to-secondary/5 rounded-xl p-6 border border-secondary/20">
                    <div class="text-center">
                      <div v-if="props.promoCode.is_global" class="text-3xl font-bold text-secondary mb-2">∞</div>
                      <div v-else class="text-3xl font-bold text-secondary mb-2">{{ props.usageStats.total_users }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">
                        {{ props.promoCode.is_global ? 'Tous les utilisateurs' : 'Utilisateurs ciblés' }}
                      </div>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                  <div class="space-y-6">
                    <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                      📅 Informations générales
                    </h4>

                    <div class="space-y-4">
                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Créé le</span>
                        <span class="font-medium">{{ formatSimpleDate(props.promoCode.created_at) }}</span>
                      </div>

                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Dernière modification</span>
                        <span class="font-medium">{{ formatSimpleDate(props.promoCode.updated_at) }}</span>
                      </div>

                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Expiration</span>
                        <span class="font-medium">{{ formatDate(props.promoCode.expires_at) }}</span>
                      </div>

                      <div v-if="!props.promoCode.is_global" class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Dernière utilisation</span>
                        <span class="font-medium">{{ formatDate(props.usageStats.last_used) }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="space-y-6">
                    <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                      📊 Utilisation
                    </h4>

                    <div v-if="!props.promoCode.is_global" class="space-y-4">
                      <div class="p-6 bg-gradient-to-br from-success/10 to-success/5 rounded-xl border border-success/20">
                        <div class="text-center">
                          <div class="text-2xl font-bold text-success mb-1">{{ props.usageStats.used_count }}</div>
                          <div class="text-sm text-base-content/70">Utilisations</div>
                        </div>
                      </div>

                      <div class="p-6 bg-gradient-to-br from-warning/10 to-warning/5 rounded-xl border border-warning/20">
                        <div class="text-center">
                          <div class="text-2xl font-bold text-warning mb-1">{{ props.usageStats.unused_count }}</div>
                          <div class="text-sm text-base-content/70">Non utilisés</div>
                        </div>
                      </div>
                    </div>

                    <div v-else class="p-6 bg-gradient-to-br from-info/10 to-info/5 rounded-xl border border-info/20">
                      <div class="text-center">
                        <div class="text-2xl font-bold text-info mb-2">🌍</div>
                        <div class="text-lg font-semibold text-base-content mb-1">Code Global</div>
                        <div class="text-sm text-base-content/70">
                          Ce code peut être utilisé par tous les utilisateurs de la plateforme.
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="props.promoCode.items.length > 0" class="mt-8">
                  <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2 mb-4">
                    🎁 Items inclus
                  </h4>

                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                      v-for="item in props.promoCode.items"
                      :key="item.id"
                      class="p-4 bg-base-200/30 rounded-xl border border-base-300/20 hover:border-primary/30 transition-colors"
                    >
                      <div class="flex items-center justify-between">
                        <div>
                          <div class="font-semibold text-base-content">{{ item.name }}</div>
                          <div class="text-sm text-base-content/70 capitalize">{{ item.type }}</div>
                          <div :class="['text-sm font-medium capitalize', getRarityColor(item.rarity)]">
                            {{ item.rarity }}
                          </div>
                        </div>
                        <div class="text-right">
                          <div class="text-xl font-bold text-primary">{{ item.pivot.quantity }}x</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="!props.promoCode.is_global && props.promoCode.users.length > 0" class="mt-8">
                  <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2 mb-4">
                    👥 Utilisateurs ciblés ({{ props.promoCode.users.length }})
                  </h4>

                  <div class="space-y-2 max-h-64 overflow-y-auto">
                    <div
                      v-for="user in props.promoCode.users"
                      :key="user.id"
                      class="flex items-center justify-between p-3 bg-base-200/30 rounded-lg"
                    >
                      <div>
                        <span class="font-medium">{{ user.username }}</span>
                        <span class="text-sm text-base-content/70 ml-2">({{ user.email }})</span>
                      </div>
                      <div class="flex items-center gap-2">
                        <span :class="[
                          'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                          user.pivot.is_used ? 'bg-success/20 text-success' : 'bg-warning/20 text-warning'
                        ]">
                          {{ user.pivot.is_used ? 'Utilisé' : 'En attente' }}
                        </span>
                        <span v-if="user.pivot.used_at" class="text-xs text-base-content/60">
                          {{ formatDate(user.pivot.used_at) }}
                        </span>
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
                  @click="router.visit(`/admin/promocodes/${props.promoCode.id}/edit`)"
                  variant="primary"
                  size="sm"
                  class="w-full justify-start"
                >
                  ✏️ Modifier le code promo
                </Button>
                <Button
                  @click="router.visit('/admin/promocodes')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  📋 Liste codes promo
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
              <div class="p-4 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">💰</span>
                  RÉCOMPENSES
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-center">
                  <div class="text-2xl font-bold text-warning">{{ props.promoCode.cash.toLocaleString() }}</div>
                  <div class="text-sm text-base-content/70">Cash par utilisation</div>
                </div>

                <div class="text-center">
                  <div class="text-2xl font-bold text-info">{{ props.promoCode.items.length }}</div>
                  <div class="text-sm text-base-content/70">Items différents</div>
                </div>

                <div class="pt-4 border-t border-base-300/30 text-center">
                  <div class="text-sm font-medium text-base-content mb-1">Total items</div>
                  <div class="text-xl font-bold text-success">
                    {{ props.promoCode.items.reduce((sum, item) => sum + item.pivot.quantity, 0) }}
                  </div>
                  <div class="text-xs text-base-content/60">
                    Quantité totale d'items
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
                  Actions irréversibles sur ce code promo
                </p>
                <Button
                  @click="deletePromoCode"
                  variant="outline"
                  size="sm"
                  class="w-full border-error text-error hover:bg-error hover:text-error-content"
                >
                  🗑️ Supprimer le code promo
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
          <h3 class="text-xl font-bold text-base-content">Supprimer le code promo</h3>
        </div>
      </template>

      <div class="space-y-4">
        <p class="text-base-content/80">
          Êtes-vous sûr de vouloir supprimer le code promo
          <span class="font-bold text-error">{{ props.promoCode.code }}</span> ?
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
