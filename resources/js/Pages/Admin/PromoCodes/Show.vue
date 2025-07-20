<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Button from '@/Components/UI/Button.vue';
import Badge from '@/Components/UI/Badge.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import { Ticket, ArrowLeft, Edit, Users, Gift, Coins, Calendar, BarChart3, Eye, CheckCircle, XCircle } from 'lucide-vue-next';

const props = defineProps({
  promoCode: Object,
  usageStats: Object
});

const formatDate = (dateString) => {
  if (!dateString) return 'Jamais';
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
};

const getStatusBadge = (promoCode) => {
  if (!promoCode.is_active) return 'error';
  if (promoCode.expires_at && new Date(promoCode.expires_at) < new Date()) return 'warning';
  return 'success';
};

const getStatusLabel = (promoCode) => {
  if (!promoCode.is_active) return 'Inactif';
  if (promoCode.expires_at && new Date(promoCode.expires_at) < new Date()) return 'Expiré';
  return 'Actif';
};
</script>

<template>
  <Head title="Détail du Code Promo" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider">
            <component :is="Ticket" :size="28" class="inline align-middle mr-2" />
            DÉTAIL DU CODE PROMO
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Informations et statistiques du code
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
                  <Link href="/admin/promocodes">
                    <Button variant="outline" size="sm" class="w-full justify-start mb-2">
                      <component :is="ArrowLeft" :size="16" class="mr-2" /> Retour
                    </Button>
                  </Link>
                  <Link :href="`/admin/promocodes/${props.promoCode.id}/edit`">
                    <Button variant="secondary" size="sm" class="w-full justify-start">
                      <component :is="Edit" :size="16" class="mr-2" /> Modifier
                    </Button>
                  </Link>
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
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Total utilisateurs</span>
                    <span class="text-sm font-bold text-info">{{ props.usageStats.total_users }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Utilisations</span>
                    <span class="text-sm font-bold text-success">{{ props.usageStats.used_count }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Non utilisés</span>
                    <span class="text-sm font-bold text-warning">{{ props.usageStats.unused_count }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-base-content/70">Dernière utilisation</span>
                    <span class="text-xs text-base-content/70">{{ formatDate(props.usageStats.last_used) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-[650px] sm:h-[700px] md:h-[750px] xl:h-[800px] flex flex-col">

              <div class="shrink-0 p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
                <div class="flex items-center gap-3">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Ticket" :size="18" />
                    INFORMATIONS DU CODE
                  </h3>
                  <div class="flex items-center gap-2 ml-auto">
                    <Badge :variant="props.promoCode.is_global ? 'info' : 'secondary'" size="sm">
                      {{ props.promoCode.is_global ? 'Global' : 'Ciblé' }}
                    </Badge>
                    <Badge :variant="getStatusBadge(props.promoCode)" size="sm">
                      {{ getStatusLabel(props.promoCode) }}
                    </Badge>
                  </div>
                </div>
              </div>

              <div class="flex-1 overflow-y-auto p-6">
                <div class="space-y-6">
                  <div class="bg-base-200/30 backdrop-blur-sm rounded-xl border border-base-300/20 p-6">
                    <div class="flex items-center gap-3 mb-4">
                      <h3 class="text-lg font-semibold text-base-content">Code promotionnel</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div class="space-y-3">
                        <div class="flex justify-between items-center">
                          <span class="text-sm font-medium text-base-content/70">Code:</span>
                          <span class="font-mono font-bold text-lg text-primary">{{ props.promoCode.code }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                          <span class="text-sm font-medium text-base-content/70">Montant:</span>
                          <span class="text-sm font-bold text-success">{{ props.promoCode.cash }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                          <span class="text-sm font-medium text-base-content/70">Créé le:</span>
                          <span class="text-sm">{{ formatDate(props.promoCode.created_at) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                          <span class="text-sm font-medium text-base-content/70">Expiration:</span>
                          <span class="text-sm">{{ formatDate(props.promoCode.expires_at) }}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-base-200/30 backdrop-blur-sm rounded-xl border border-base-300/20 p-6">
                      <div class="flex items-center gap-2 mb-4">
                        <component :is="Gift" :size="20" class="text-primary" />
                        <h3 class="text-lg font-semibold text-base-content">Items attribués</h3>
                      </div>
                      <div v-if="!props.promoCode.items || props.promoCode.items.length === 0" class="text-base-content/50 text-center py-4">
                        Aucun item attribué avec ce code
                      </div>
                      <ul v-else class="space-y-3">
                        <li v-for="item in props.promoCode.items" :key="item.id" class="bg-base-200/40 rounded-lg p-3 border border-base-300/30 flex justify-between items-center">
                          <div class="flex items-center gap-2">
                            <Badge variant="primary" size="sm">{{ item.type }}</Badge>
                            <span class="font-medium">{{ item.name }}</span>
                          </div>
                          <div class="flex items-center gap-2">
                            <Badge :variant="item.rarity === 'legendary' ? 'error' : item.rarity === 'epic' ? 'warning' : item.rarity === 'rare' ? 'info' : 'neutral'" size="xs">
                              {{ item.rarity }}
                            </Badge>
                            <span class="text-xs bg-primary/10 text-primary font-medium rounded-full px-2 py-0.5">
                              × {{ item.pivot?.quantity || 1 }}
                            </span>
                          </div>
                        </li>
                      </ul>
                    </div>

                    <div class="bg-base-200/30 backdrop-blur-sm rounded-xl border border-base-300/20 p-6">
                      <div class="flex items-center gap-2 mb-4">
                        <component :is="Users" :size="20" class="text-primary" />
                        <h3 class="text-lg font-semibold text-base-content">Utilisateurs ciblés</h3>
                      </div>
                      <div v-if="props.promoCode.is_global" class="flex justify-center items-center py-8">
                        <Badge variant="info" size="lg">Global - Tous les utilisateurs</Badge>
                      </div>
                      <div v-else>
                        <div v-if="!props.promoCode.users || props.promoCode.users.length === 0" class="text-base-content/50 text-center py-4">
                          Aucun utilisateur ciblé par ce code
                        </div>
                        <ul v-else class="space-y-2">
                          <li v-for="user in props.promoCode.users" :key="user.id" class="bg-base-200/40 rounded-lg p-3 border border-base-300/30 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                              <span class="font-medium">{{ user.username }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                              <Badge :variant="user.pivot?.is_used ? 'success' : 'warning'" size="xs">
                                <component :is="user.pivot?.is_used ? CheckCircle : XCircle" :size="12" class="mr-1" />
                                {{ user.pivot?.is_used ? 'Utilisé' : 'Non utilisé' }}
                              </Badge>
                              <span v-if="user.pivot?.used_at" class="text-xs text-base-content/70">
                                {{ formatDate(user.pivot.used_at) }}
                              </span>
                            </div>
                          </li>
                        </ul>
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
  </div>
</template>
