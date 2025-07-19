<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Button from '@/Components/UI/Button.vue';
import Badge from '@/Components/UI/Badge.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';

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
  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />
    <div class="relative z-10 h-screen w-screen overflow-hidden flex flex-col">
      <div class="shrink-0 p-4 bg-base-200/50 backdrop-blur-md border-b border-base-300/30">
        <div class="max-w-4xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
          <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent tracking-wider">
              🎟️ Détail du Code Promo
            </h1>
            <p class="text-xs text-base-content/70 uppercase tracking-wider">
              Informations et statistiques du code
            </p>
          </div>
          <div class="flex items-center gap-2">
            <Link href="/admin/promocodes">
              <Button variant="ghost" size="sm">Retour à la liste</Button>
            </Link>
            <Link :href="`/admin/promocodes/${props.promoCode.id}/edit`">
              <Button variant="secondary" size="sm">Modifier</Button>
            </Link>
          </div>
        </div>
      </div>
      <div class="flex-1 overflow-auto p-4">
        <div class="max-w-4xl mx-auto bg-base-200/50 backdrop-blur-sm rounded-2xl border border-base-300/30 p-8 space-y-8">
          <!-- Header Info -->
          <div class="bg-base-200/30 rounded-xl p-6 border border-base-300/20">
            <div class="flex flex-col md:flex-row gap-8">
              <div class="flex-1 space-y-4">
                <div class="flex items-center gap-3 flex-wrap">
                  <span class="font-mono font-bold text-primary text-2xl">{{ props.promoCode.code }}</span>
                  <Badge :variant="props.promoCode.is_global ? 'info' : 'secondary'" size="sm">
                    {{ props.promoCode.is_global ? 'Global' : 'Ciblé' }}
                  </Badge>
                  <Badge :variant="getStatusBadge(props.promoCode)" size="sm">
                    {{ getStatusLabel(props.promoCode) }}
                  </Badge>
                </div>
                <div class="grid grid-cols-2 gap-y-2 text-sm">
                  <div class="text-base-content/70">Créé le :</div>
                  <div class="font-medium">{{ formatDate(props.promoCode.created_at) }}</div>
                  
                  <div class="text-base-content/70">Expiration :</div>
                  <div class="font-medium">{{ formatDate(props.promoCode.expires_at) }}</div>
                  
                  <div class="text-base-content/70">Montant :</div>
                  <div class="font-bold text-primary">{{ props.promoCode.cash }} 💰</div>
                </div>
              </div>
              
              <div class="flex-1 bg-base-300/20 rounded-xl p-4 border border-base-300/30">
                <div class="font-semibold text-base-content/80 mb-3">Statistiques d'utilisation</div>
                <div class="grid grid-cols-2 gap-3">
                  <div class="bg-base-200/40 rounded-lg p-3 border border-base-300/30">
                    <div class="text-xs text-base-content/70 mb-1">Total utilisateurs</div>
                    <div class="text-xl font-bold">{{ props.usageStats.total_users }}</div>
                  </div>
                  <div class="bg-base-200/40 rounded-lg p-3 border border-base-300/30">
                    <div class="text-xs text-base-content/70 mb-1">Utilisations</div>
                    <div class="text-xl font-bold text-success">{{ props.usageStats.used_count }}</div>
                  </div>
                  <div class="bg-base-200/40 rounded-lg p-3 border border-base-300/30">
                    <div class="text-xs text-base-content/70 mb-1">Non utilisés</div>
                    <div class="text-xl font-bold text-warning">{{ props.usageStats.unused_count }}</div>
                  </div>
                  <div class="bg-base-200/40 rounded-lg p-3 border border-base-300/30">
                    <div class="text-xs text-base-content/70 mb-1">Dernière utilisation</div>
                    <div class="text-sm font-bold">{{ formatDate(props.usageStats.last_used) }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Items and Users Sections -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-base-200/30 rounded-xl p-6 border border-base-300/20">
              <h3 class="text-lg font-semibold mb-4 bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">Items attribués</h3>
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

            <div class="bg-base-200/30 rounded-xl p-6 border border-base-300/20">
              <h3 class="text-lg font-semibold mb-4 bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">Utilisateurs ciblés</h3>
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
</template>