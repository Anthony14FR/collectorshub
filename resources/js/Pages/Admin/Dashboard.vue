<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import { Head, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

interface Props {
  auth: {
    user: {
      id: number;
      username: string;
      role: string;
      cash: number;
      level: number;
    };
  };
  stats?: {
    total_users: number;
    active_users: number;
    total_pokemons: number;
    total_expeditions: number;
    marketplace_listings: number;
    pending_tickets: number;
    shop_items: number;
    clubsCount: number;
  };
}

const props = defineProps<Props>();

const adminSections = ref([
  {
    title: 'Utilisateurs',
    description: 'Gérer les comptes, rôles et permissions',
    icon: '👥',
    route: '/admin/users',
    color: 'info',
    stat: props.stats?.total_users || 0,
    statLabel: 'Total'
  },
  {
    title: 'Expéditions',
    description: 'Gérer les expéditions et leurs récompenses',
    icon: '🗺️',
    route: '/admin/expeditions',
    color: 'primary',
    stat: props.stats?.total_expeditions || 0,
    statLabel: 'Total'
  },
  {
    title: 'Pokémon',
    description: 'Base de données et statistiques',
    icon: '⚡',
    route: '/admin/pokemons',
    color: 'error',
    stat: props.stats?.total_pokemons || 0,
    statLabel: 'En base'
  },
  {
    title: 'Boutique',
    description: 'Items et configuration shop',
    icon: '🛍️',
    route: '/admin/items',
    color: 'success',
    stat: props.stats?.shop_items || 42,
    statLabel: 'Items'
  },
  {
    title: 'Codes Promo',
    description: 'Créer et gérer les codes promotionnels',
    icon: '🎟️',
    route: '/admin/promocodes',
    color: 'warning',
    stat: props.stats?.total_promocodes || 0,
    statLabel: 'Codes'
  },
  {
    title: 'Système',
    description: 'Logs et maintenance serveur',
    icon: '⚙️',
    route: '/admin/system',
    color: 'accent',
    stat: props.stats?.active_users || 0,
    statLabel: 'Actifs'
  },
  {
    title: 'Clubs',
    description: 'Gestion des clubs et modération',
    icon: '🏆',
    route: '/admin/clubs',
    color: 'info',
    stat: props.stats?.clubsCount || 0,
    statLabel: 'Clubs'
  },
  {
    title: 'Configuration',
    description: 'Paramètres de récompenses et probabilités',
    icon: '🎮',
    route: '/admin/game-configuration',
    color: 'secondary',
    stat: 3,
    statLabel: 'Catégories'
  }
]);

const quickTools = ref([
  { icon: '🗑️', label: 'Cache', route: '/admin/cache/clear' },
  { icon: '💾', label: 'Backup', route: '/admin/backup' },
]);

const isMaintenanceMode = ref(false);
const isLoadingMaintenance = ref(false);
const showMaintenanceConfirm = ref(false);
const maintenanceAction = ref<'enable' | 'disable'>('enable');

const notifications = ref<Array<{
  id: string;
  type: 'success' | 'error' | 'warning' | 'info';
  message: string;
  show: boolean;
}>>([]);

const goToSection = (route: string) => {
  if (route === '/admin/cache/clear') {
    clearCache();
  } else {
    router.visit(route);
  }
};

const goBack = () => {
  router.visit('/me');
};

const getCsrfToken = () => {
  return (window as any).Laravel?.csrfToken ||
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
    '';
};

const showNotification = (type: 'success' | 'error' | 'warning' | 'info', message: string) => {
  const id = Date.now().toString();
  const notification = { id, type, message, show: true };
  notifications.value.push(notification);

  setTimeout(() => {
    notification.show = false;
    setTimeout(() => {
      const index = notifications.value.findIndex(n => n.id === id);
      if (index > -1) {
        notifications.value.splice(index, 1);
      }
    }, 300);
  }, 5000);
};

const clearCache = async () => {
  try {
    const response = await fetch('/admin/cache/clear', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
      },
    });

    const data = await response.json();

    if (data.success) {
      showNotification('success', 'Cache vidé avec succès !');
    } else {
      showNotification('error', 'Erreur lors du vidage du cache');
    }
  } catch (error) {
    showNotification('error', 'Erreur lors du vidage du cache');
  }
};

const checkMaintenanceStatus = async () => {
  try {
    const response = await fetch('/admin/maintenance/status');
    const data = await response.json();
    isMaintenanceMode.value = data.maintenance;
  } catch (error) {
    console.error('Erreur lors de la vérification du mode maintenance');
  }
};

const toggleMaintenance = () => {
  maintenanceAction.value = isMaintenanceMode.value ? 'disable' : 'enable';
  showMaintenanceConfirm.value = true;
};

const confirmMaintenanceAction = async () => {
  isLoadingMaintenance.value = true;
  showMaintenanceConfirm.value = false;

  try {
    const endpoint = maintenanceAction.value === 'enable' ? '/admin/maintenance/enable' : '/admin/maintenance/disable';
    const response = await fetch(endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
      },
    });

    const data = await response.json();

    if (data.success) {
      isMaintenanceMode.value = maintenanceAction.value === 'enable';
      showNotification('success', data.message);
    } else {
      showNotification('error', data.message);
    }
  } catch (error) {
    showNotification('error', 'Erreur lors de la modification du mode maintenance');
  } finally {
    isLoadingMaintenance.value = false;
  }
};

const cancelMaintenanceAction = () => {
  showMaintenanceConfirm.value = false;
};

onMounted(() => {
  checkMaintenanceStatus();
});
</script>

<template>
  <Head title="Dashboard Admin" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-2 tracking-wider">
            🔧 DASHBOARD ADMIN
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            Gestion de la plateforme
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-8 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-2xl">🎛️</span>
                  MODULES ADMINISTRATION
                </h3>
              </div>

              <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div
                    v-for="section in adminSections"
                    :key="section.route"
                    @click="goToSection(section.route)"
                    class="group bg-gradient-to-br from-base-200/50 to-base-300/30 backdrop-blur-sm rounded-xl p-5 border border-base-300/30 hover:border-primary/50 transition-all duration-300 cursor-pointer hover:scale-105 hover:shadow-lg"
                  >
                    <div class="flex items-start justify-between mb-4">
                      <div class="text-3xl transform group-hover:scale-110 transition-transform duration-200">
                        {{ section.icon }}
                      </div>
                      <div class="text-right">
                        <div :class="[
                          'text-xl font-bold transition-colors duration-200',
                          section.color === 'info' ? 'text-info group-hover:text-info/80' :
                          section.color === 'primary' ? 'text-primary group-hover:text-primary/80' :
                          section.color === 'warning' ? 'text-warning group-hover:text-warning/80' :
                          section.color === 'error' ? 'text-error group-hover:text-error/80' :
                          section.color === 'success' ? 'text-success group-hover:text-success/80' :
                          section.color === 'secondary' ? 'text-secondary group-hover:text-secondary/80' :
                          'text-accent group-hover:text-accent/80'
                        ]">
                          {{ section.stat.toLocaleString() }}
                        </div>
                        <div class="text-xs text-base-content/60 uppercase tracking-wide">
                          {{ section.statLabel }}
                        </div>
                      </div>
                    </div>

                    <div class="space-y-2">
                      <h4 class="font-bold text-base-content group-hover:text-base-content/80 transition-colors">
                        {{ section.title }}
                      </h4>
                      <p class="text-sm text-base-content/70 leading-relaxed">
                        {{ section.description }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-4 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                  <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                    <span class="text-xl">🚨</span>
                    MODE MAINTENANCE
                  </h3>
                </div>

                <div class="p-6">
                  <div class="flex items-center justify-between mb-4">
                    <div>
                      <div class="text-sm font-medium text-base-content/80 mb-1">
                        État actuel
                      </div>
                      <span :class="[
                        'inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide',
                        isMaintenanceMode ? 'bg-error/20 text-error' : 'bg-success/20 text-success'
                      ]">
                        {{ isMaintenanceMode ? 'ACTIF' : 'INACTIF' }}
                      </span>
                    </div>
                  </div>

                  <Button
                    @click="toggleMaintenance"
                    :disabled="isLoadingMaintenance"
                    :variant="isMaintenanceMode ? 'secondary' : 'outline'"
                    size="sm"
                    class="w-full"
                  >
                    <span v-if="isLoadingMaintenance">⏳</span>
                    <span v-else>🔧</span>
                    {{ isLoadingMaintenance ? 'Chargement...' : (isMaintenanceMode ? 'Désactiver' : 'Activer') }}
                  </Button>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-4 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                  <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                    <span class="text-xl">⚡</span>
                    ACTIONS RAPIDES
                  </h3>
                </div>

                <div class="p-6 space-y-3">
                  <Button
                    v-for="tool in quickTools"
                    :key="tool.route"
                    @click="goToSection(tool.route)"
                    variant="outline"
                    size="sm"
                    class="w-full justify-start"
                  >
                    <span>{{ tool.icon }}</span>
                    {{ tool.label }}
                  </Button>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-4 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">👤</span>
                  ADMIN INFO
                </h3>
              </div>

              <div class="p-6">
                <div class="text-center space-y-4">
                  <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-3xl">
                    🛡️
                  </div>

                  <div>
                    <div class="text-lg font-bold text-base-content">
                      {{ props.auth.user.username }}
                    </div>
                    <div class="text-sm text-base-content/70 mb-2">
                      Niveau {{ props.auth.user.level }}
                    </div>
                    <div class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-primary/20 to-secondary/20 rounded-full">
                      <span class="text-sm font-medium">{{ props.auth.user.role }}</span>
                    </div>
                  </div>

                  <div class="pt-4 border-t border-base-300/30">
                    <div class="text-sm text-base-content/70 mb-1">Solde</div>
                    <div class="text-xl font-bold text-warning">
                      {{ props.auth.user.cash.toLocaleString() }} 💰
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-error/10 to-error/5 border-b border-error/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">🔗</span>
                  NAVIGATION
                </h3>
              </div>

              <div class="p-6 space-y-3">
                <Button @click="goBack" variant="secondary" size="sm" class="w-full justify-start">
                  ← Retour au profil
                </Button>
                <Button @click="goToSection('/admin/logs')" variant="outline" size="sm" class="w-full justify-start">
                  📝 Voir les logs
                </Button>
                <Button @click="router.visit('/admin/notifications/create')" variant="outline" size="sm" class="w-full justify-start">
                  📢 Créer une annonce
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div
      v-for="notification in notifications.filter(n => n.show)"
      :key="notification.id"
      :class="[
        'fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 max-w-sm',
        notification.type === 'success' ? 'bg-success text-success-content' :
        notification.type === 'error' ? 'bg-error text-error-content' :
        notification.type === 'warning' ? 'bg-warning text-warning-content' :
        'bg-info text-info-content'
      ]"
    >
      {{ notification.message }}
    </div>

    <div v-if="showMaintenanceConfirm" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-base-100 rounded-xl p-6 max-w-md w-full border border-base-300">
        <h3 class="text-lg font-bold mb-4">
          {{ maintenanceAction === 'enable' ? 'Activer' : 'Désactiver' }} le mode maintenance
        </h3>
        <p class="text-base-content/70 mb-6">
          {{ maintenanceAction === 'enable'
            ? 'Le site sera inaccessible aux utilisateurs pendant la maintenance.'
            : 'Le site redeviendra accessible aux utilisateurs.'
          }}
        </p>
        <div class="flex gap-3">
          <Button @click="confirmMaintenanceAction" variant="primary" class="flex-1">
            Confirmer
          </Button>
          <Button @click="cancelMaintenanceAction" variant="secondary" class="flex-1">
            Annuler
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>
