<script setup lang="ts">
import type { User } from '@/types/user';
import { Head, Link, router } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';

interface PaginatedUsers {
  data: User[];
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

defineProps<{
  users: PaginatedUsers;
}>();

const deleteUser = (userId: number) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
    router.delete(`/admin/users/${userId}`, {
      preserveScroll: true,
    });
  }
};

const getRoleColor = (role: string) => {
  switch (role) {
  case 'admin': return 'text-error bg-error/10 border-error/30';
  case 'premium': return 'text-warning bg-warning/10 border-warning/30';
  default: return 'text-info bg-info/10 border-info/30';
  }
};

const getStatusColor = (status: string) => {
  switch (status) {
  case 'active': return 'text-success bg-success/10 border-success/30';
  case 'suspended': return 'text-warning bg-warning/10 border-warning/30';
  case 'banned': return 'text-error bg-error/10 border-error/30';
  default: return 'text-base-content/70 bg-base-200/50 border-base-300/30';
  }
};
</script>

<template>
  <Head title="Gestion des utilisateurs" />

  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 h-screen w-screen overflow-hidden">
      <div class="flex justify-center pt-4 mb-4">
        <div class="text-center">
          <h1 class="text-2xl font-bold bg-gradient-to-r from-info to-info/80 bg-clip-text text-transparent mb-1 tracking-wider">
            👥 GESTION UTILISATEURS
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            {{ users.total }} utilisateur{{ users.total > 1 ? 's' : '' }} enregistré{{ users.total > 1 ? 's' : '' }}
          </p>
        </div>
      </div>

      <div class="absolute right-8 top-20 w-64">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
          <div class="p-3 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">⚙️</span>
              ACTIONS
            </h3>
          </div>
          <div class="p-3 space-y-2">
            <Button
              @click="router.visit('/admin/users/create')"
              variant="secondary"
              size="sm"
              class="w-full"
            >
              ➕ Nouvel utilisateur
            </Button>
            <Button
              @click="router.visit('/admin/')"
              variant="outline"
              size="sm"
              class="w-full"
            >
              ← Dashboard
            </Button>
            <Button
              @click="router.visit('/me')"
              variant="ghost"
              size="sm"
              class="w-full"
            >
              🏠 Profil
            </Button>
          </div>
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">📊</span>
              STATISTIQUES
            </h3>
          </div>
          <div class="p-3 space-y-2">
            <div class="flex justify-between items-center">
              <span class="text-xs text-base-content/70">Total</span>
              <span class="text-sm font-bold text-info">{{ users.total }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-xs text-base-content/70">Page</span>
              <span class="text-sm font-bold">{{ users.current_page }}/{{ users.last_page }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-xs text-base-content/70">Par page</span>
              <span class="text-sm font-bold">{{ users.per_page }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="absolute top-20 left-1/2 -translate-x-1/2 w-[900px] h-[700px]">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-full flex flex-col">
          <div class="shrink-0 p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">📋</span>
              LISTE DES UTILISATEURS
            </h3>
          </div>

          <div class="flex-1 overflow-y-auto">
            <table class="w-full">
              <thead class="sticky z-50 top-0 bg-base-200/80 backdrop-blur-sm border-b border-base-300/30">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                    Utilisateur
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                    Rôle / Statut
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                    Niveau / XP
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                    Cash
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                    Dernière connexion
                  </th>
                  <th class="px-4 py-3 text-center text-xs font-bold text-base-content/70 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-base-300/20">
                <tr v-for="user in users.data" :key="user.id" 
                    class="hover:bg-base-200/30 transition-colors">
                  <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-sm font-bold">
                        {{ user.username.charAt(0).toUpperCase() }}
                      </div>
                      <div>
                        <div class="font-semibold text-base-content">{{ user.username }}</div>
                        <div class="text-xs text-base-content/60">{{ user.email }}</div>
                      </div>
                    </div>
                  </td>

                  <td class="px-4 py-4">
                    <div class="space-y-1">
                      <span :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full border',
                        getRoleColor(user.role)
                      ]">
                        {{ user.role }}
                      </span>
                      <br>
                      <span :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full border',
                        getStatusColor(user.status)
                      ]">
                        {{ user.status }}
                      </span>
                    </div>
                  </td>

                  <td class="px-4 py-4">
                    <div class="text-sm">
                      <div class="font-semibold">Niveau {{ user.level || 1 }}</div>
                      <div class="text-xs text-base-content/60">{{ (user.experience || 0).toLocaleString() }} XP</div>
                    </div>
                  </td>

                  <td class="px-4 py-4">
                    <div class="text-sm font-semibold text-success">
                      {{ (user.cash || 0).toLocaleString() }} 💰
                    </div>
                  </td>

                  <td class="px-4 py-4">
                    <div class="text-xs text-base-content/60">
                      {{ user.last_login ? 
                        new Date(user.last_login).toLocaleDateString('fr-FR') :
                        'Jamais' }}
                    </div>
                  </td>

                  <td class="px-4 py-4">
                    <div class="flex justify-center gap-1">
                      <Button
                        @click="router.visit(`/admin/users/${user.id}`)"
                        variant="ghost"
                        size="sm"
                        class="text-info hover:text-info hover:bg-info/10"
                        title="Voir"
                      >
                        👁️
                      </Button>
                      <Button
                        @click="router.visit(`/admin/users/${user.id}/edit`)"
                        variant="ghost"
                        size="sm"
                        class="text-warning hover:text-warning hover:bg-warning/10"
                        title="Modifier"
                      >
                        ✏️
                      </Button>
                      <Button
                        @click="deleteUser(user.id)"
                        variant="ghost"
                        size="sm"
                        class="text-error hover:text-error hover:bg-error/10"
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

          <div v-if="users.last_page > 1" class="shrink-0 bg-gradient-to-r from-info/10 to-info/5 px-4 py-3 border-t border-info/20">
            <div class="flex justify-center items-center gap-2">
              <template v-for="link in users.links" :key="link.label">
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
              Affichage de {{ (users.current_page - 1) * users.per_page + 1 }}
              à {{ Math.min(users.current_page * users.per_page, users.total) }}
              sur {{ users.total }} résultats
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>