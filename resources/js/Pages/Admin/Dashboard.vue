<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';

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
    marketplace_listings: number;
    pending_tickets: number;
    shop_items: number;
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
    title: 'Marketplace',
    description: 'Modérer les annonces et transactions',
    icon: '🏪',
    route: '/admin/marketplace',
    color: 'warning',
    stat: props.stats?.marketplace_listings || 0,
    statLabel: 'Annonces'
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
    title: 'Support',
    description: 'Tickets et demandes utilisateurs',
    icon: '🎫',
    route: '/admin/support',
    color: 'secondary',
    stat: props.stats?.pending_tickets || 7,
    statLabel: 'En attente'
  },
  {
    title: 'Boutique',
    description: 'Items et configuration shop',
    icon: '🛍️',
    route: '/admin/shop',
    color: 'success',
    stat: props.stats?.shop_items || 42,
    statLabel: 'Items'
  },
  {
    title: 'Système',
    description: 'Logs et maintenance serveur',
    icon: '⚙️',
    route: '/admin/system',
    color: 'accent',
    stat: props.stats?.active_users || 0,
    statLabel: 'Actifs'
  }
]);

const quickTools = ref([
  { icon: '🗑️', label: 'Cache', route: '/admin/cache/clear' },
  { icon: '🔧', label: 'Maintenance', route: '/admin/maintenance' },
  { icon: '💾', label: 'Backup', route: '/admin/backup' },
  { icon: '📊', label: 'Reports', route: '/admin/reports' }
]);

const goToSection = (route: string) => {
  router.visit(route);
};

const goBack = () => {
  router.visit('/me');
};
</script>

<template>
  <Head title="Dashboard Admin" />

  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 h-screen w-screen overflow-hidden">
      <div class="flex justify-center pt-4 mb-4">
        <div class="text-center">
          <h1 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-1 tracking-wider">
            🔧 DASHBOARD ADMIN
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
              <span class="text-lg">📊</span>
              STATISTIQUES
            </h3>
          </div>
          <div class="p-3 space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-xs text-base-content/70">Utilisateurs</span>
              <span class="text-sm font-bold text-info">{{ props.stats?.total_users || 0 }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-xs text-base-content/70">Actifs</span>
              <span class="text-sm font-bold text-success">{{ props.stats?.active_users || 0 }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-xs text-base-content/70">Marketplace</span>
              <span class="text-sm font-bold text-warning">{{ props.stats?.marketplace_listings || 0 }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-xs text-base-content/70">Pokémon</span>
              <span class="text-sm font-bold text-error">{{ props.stats?.total_pokemons || 0 }}</span>
            </div>
          </div>
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-accent/10 to-accent/5 border-b border-accent/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">🛠️</span>
              OUTILS RAPIDES
            </h3>
          </div>
          <div class="p-3 space-y-2">
            <Button
              v-for="tool in quickTools"
              :key="tool.route"
              @click="goToSection(tool.route)"
              variant="outline"
              size="sm"
              class="w-full justify-start"
            >
              {{ tool.icon }} {{ tool.label }}
            </Button>
          </div>
        </div>
      </div>

      <div class="absolute right-8 top-20 w-64">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
          <div class="p-3 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">👤</span>
              ADMIN INFO
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
              <span class="text-lg">🚨</span>
              ACTIONS
            </h3>
          </div>
          <div class="p-3 space-y-2">
            <Button @click="goBack" variant="secondary" size="sm" class="w-full">
              ← Retour au profil
            </Button>
            <Button @click="goToSection('/admin/logs')" variant="outline" size="sm" class="w-full">
              📝 Voir les logs
            </Button>
          </div>
        </div>
      </div>

      <div class="absolute top-20 left-1/2 -translate-x-1/2 w-[600px] h-[700px]">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-full flex flex-col">
          <div class="shrink-0 p-3 bg-gradient-to-r from-primary/10 to-secondary/5 border-b border-primary/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">🎛️</span>
              MODULES ADMINISTRATION
            </h3>
          </div>

          <div class="flex-1 overflow-y-auto p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div
                v-for="section in adminSections"
                :key="section.route"
                @click="goToSection(section.route)"
                class="group bg-base-200/30 backdrop-blur-sm rounded-xl p-4 border border-base-300/20 hover:border-primary/40 transition-all duration-200 cursor-pointer hover:scale-105"
              >
                <div class="flex items-start justify-between mb-3">
                  <div class="text-2xl">{{ section.icon }}</div>
                  <div class="text-right">
                    <div :class="[
                      'text-lg font-bold',
                      section.color === 'info' ? 'text-info' :
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
  </div>
</template>