<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Users, Map, Store, BadgePercent, ShoppingBag, Trophy, Settings, BookUser, ListChecks } from 'lucide-vue-next';

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
    totalPromoCodes: number;
    totalSuccess: number;
  };
}

const props = defineProps<Props>();

const adminSections = ref([
  {
    title: 'Utilisateurs',
    description: 'Gérer les comptes, rôles et permissions',
    icon: Users,
    route: '/admin/users',
    color: 'info',
    stat: props.stats?.total_users || 0,
    statLabel: 'Total'
  },
  {
    title: 'Pokémon',
    description: 'Base de données et statistiques',
    icon: BookUser,
    route: '/admin/pokemons',
    color: 'error',
    stat: props.stats?.total_pokemons || 0,
    statLabel: 'En base'
  },
  {
    title: 'Expéditions',
    description: 'Gérer les expéditions et leurs récompenses',
    icon: Map,
    route: '/admin/expeditions',
    color: 'primary',
    stat: props.stats?.total_expeditions || 0,
    statLabel: 'Total'
  },
  {
    title: 'Codes promo',
    description: 'Gestion des codes promotionnels',
    icon: BadgePercent,
    route: '/admin/promocodes',
    color: 'secondary',
    stat: props.stats?.totalPromoCodes || 0,
    statLabel: 'Codes'
  },
  {
    title: 'Items',
    description: 'Items et configuration shop',
    icon: ShoppingBag,
    route: '/admin/items',
    color: 'success',
    stat: props.stats?.shop_items || 0,
    statLabel: 'Items'
  },
  {
    title: 'Succès',
    description: 'Gestion des succès et badges',
    icon: ListChecks,
    route: '/admin/success',
    color: 'accent',
    stat: props.stats?.totalSuccess || 0,
    statLabel: 'Succès'
  },
  {
    title: 'Clubs',
    description: 'Gestion des clubs et modération',
    icon: Trophy,
    route: '/admin/clubs',
    color: 'info',
    stat: props.stats?.clubsCount || 0,
    statLabel: 'Clubs'
  },
  {
    title: 'Configuration',
    description: 'Paramètres de récompenses et probabilités',
    icon: Settings,
    route: '/admin/game-configuration',
    color: 'success',
    stat: 3,
    statLabel: 'Catégories'
  }
]);

const quickTools = ref([
  { icon: Store, label: 'Cache', route: '/admin/cache/clear' },
  { icon: ListChecks, label: 'Logs', route: '/admin/logs' },
]);

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
  const notification = {
    id,
    type,
    message,
    show: true
  };

  notifications.value.push(notification);

  setTimeout(() => {
    const index = notifications.value.findIndex(n => n.id === id);
    if (index > -1) {
      notifications.value[index].show = false;
      setTimeout(() => {
        notifications.value.splice(index, 1);
      }, 300);
    }
  }, 4000);
};

const removeNotification = (id: string) => {
  const index = notifications.value.findIndex(n => n.id === id);
  if (index > -1) {
    notifications.value[index].show = false;
    setTimeout(() => {
      notifications.value.splice(index, 1);
    }, 300);
  }
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
      showNotification('success', data.message);
    } else {
      showNotification('error', data.message);
    }
  } catch (error) {
    showNotification('error', 'Erreur lors du vidage du cache');
  }
};
</script>

<template>

  <Head title="Dashboard Admin" />

  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 h-screen w-screen overflow-hidden">
      <div class="flex justify-center pt-4 mb-4">
        <div class="text-center">
          <h1
            class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider">
            <component :is="Settings" :size="28" class="inline align-middle mr-2" /> Dashboard Admin
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Gestion de la plateforme
          </p>
        </div>
      </div>

      <div class="absolute left-8 top-20 w-64">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
          <div class="p-3 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-primary/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <component :is="ListChecks" :size="18" class="inline align-middle mr-2" /> Statistiques
            </h3>
          </div>
          <div class="p-3 space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-xs text-base-content/70">Utilisateurs</span>
              <span class="text-sm font-bold text-info">{{ props.stats?.total_users || 0 }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-xs text-base-content/70">Pokémon</span>
              <span class="text-sm font-bold text-error">{{ props.stats?.total_pokemons || 0 }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-xs text-base-content/70">Expéditions</span>
              <span class="text-sm font-bold text-primary">{{ props.stats?.total_expeditions || 0 }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-xs text-base-content/70">Items</span>
              <span class="text-sm font-bold text-success">{{ props.stats?.shop_items || 0 }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-xs text-base-content/70">Clubs</span>
              <span class="text-sm font-bold text-info">{{ props.stats?.clubsCount || 0 }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-xs text-base-content/70">Codes promo</span>
              <span class="text-sm font-bold text-secondary">{{ props.stats?.totalPromoCodes || 0 }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-xs text-base-content/70">Succès</span>
              <span class="text-sm font-bold text-accent">{{ props.stats?.totalSuccess || 0 }}</span>
            </div>
          </div>
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-accent/10 to-accent/5 border-b border-accent/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <component :is="Store" :size="18" class="inline align-middle mr-2" /> Outils rapides
            </h3>
          </div>
          <div class="p-3 space-y-2">
            <Button v-for="tool in quickTools" :key="tool.route" @click="goToSection(tool.route)" variant="outline"
                    size="sm" class="w-full justify-start">
              <component :is="tool.icon" :size="16" class="mr-2" /> {{ tool.label }}
            </Button>
          </div>
        </div>
      </div>

      <div class="absolute right-8 top-20 w-64">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
          <div class="p-3 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <component :is="Users" :size="18" class="inline align-middle mr-2" /> Admin info
            </h3>
          </div>
          <div class="p-3">
            <div class="text-center">
              <div class="text-sm font-bold">{{ props.auth.user.username }}</div>
              <div class="text-xs text-base-content/70 mb-2">Niveau {{ props.auth.user.level }}</div>
              <div class="text-xs bg-gradient-to-r from-primary/20 to-secondary/20 px-2 py-1 rounded-full">
                {{ props.auth.user.role }}
              </div>
            </div>
          </div>
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-error/10 to-error/5 border-b border-error/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <component :is="BadgePercent" :size="18" class="inline align-middle mr-2" /> Actions
            </h3>
          </div>
          <div class="p-3 space-y-2">
            <Button @click="goBack" variant="secondary" size="sm" class="w-full">
              <component :is="Map" :size="16" class="inline align-middle mr-2" /> Retour au profil
            </Button>
            <Button @click="router.visit('/admin/notifications/create')" variant="outline" size="sm" class="w-full">
              <component :is="BadgePercent" :size="16" class="inline align-middle mr-2" /> Créer une annonce
            </Button>
          </div>
        </div>
      </div>

      <div class="absolute top-20 left-1/2 -translate-x-1/2 w-[600px] h-[700px]">
        <div
          class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-full flex flex-col">
          <div class="shrink-0 p-3 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <component :is="Settings" :size="18" class="inline align-middle mr-2" /> Modules administration
            </h3>
          </div>

          <div class="flex-1 overflow-y-auto p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-for="section in adminSections" :key="section.route" @click="goToSection(section.route)"
                   class="group bg-base-200/30 backdrop-blur-sm rounded-xl p-4 border border-base-300/20 hover:border-primary/40 transition-all duration-200 cursor-pointer hover:scale-105">
                <div class="flex items-start justify-between mb-3">
                  <div class="text-2xl">
                    <component :is="section.icon" :size="24" />
                  </div>
                  <div class="text-right">
                    <div :class="[
                      'text-lg font-bold',
                      section.color === 'info' ? 'text-info' :
                      section.color === 'primary' ? 'text-primary' :
                      section.color === 'warning' ? 'text-warning' :
                      section.color === 'error' ? 'text-error' :
                      section.color === 'success' ? 'text-success' :
                      section.color === 'secondary' ? 'text-secondary' :
                      'text-accent'
                    ]">
                      {{ section.stat.toLocaleString() }}
                    </div>
                    <div class="text-xs text-base-content/60">{{ section.statLabel }}</div>
                  </div>
                </div>

                <h4 class="font-bold text-base-content mb-1 group-hover:text-primary transition-colors">
                  {{ section.title }}
                </h4>
                <p class="text-xs text-base-content/70 leading-relaxed">
                  {{ section.description }}
                </p>

                <div class="flex items-center justify-end mt-3 opacity-70 group-hover:opacity-100 transition-opacity">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <div class="shrink-0 bg-gradient-to-r from-primary/10 to-secondary/5 px-3 py-2 border-t border-primary/20">
            <div class="text-xs text-center text-base-content/70">
              {{ adminSections.length }} modules d'administration disponibles
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Système de notifications -->
    <div class="fixed top-4 right-4 z-50 space-y-2">
      <div v-for="notification in notifications" :key="notification.id" :class="[
        'alert transition-all duration-300 transform max-w-md',
        notification.show ? 'translate-x-0 opacity-100' : 'translate-x-full opacity-0',
        notification.type === 'success' ? 'bg-green-100 border-green-500 text-green-800' :
        notification.type === 'error' ? 'bg-red-100 border-red-500 text-red-800' :
        notification.type === 'warning' ? 'alert-warning' :
        'alert-info'
      ]">
        <div class="flex items-center justify-between w-full">
          <div class="flex items-center gap-2">
            <svg v-if="notification.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <svg v-else-if="notification.type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <svg v-else-if="notification.type === 'warning'" class="w-5 h-5" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z">
              </path>
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-medium">{{ notification.message }}</span>
          </div>
          <button @click="removeNotification(notification.id)" class="btn btn-ghost btn-xs ml-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
