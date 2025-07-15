<script setup lang="ts">
import type { User } from '@/types/user';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
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

const props = defineProps<{
  users: PaginatedUsers;
  filters?: {
    role?: string;
    status?: string;
  };
}>();

const sortField = ref<string>('');
const sortDirection = ref<'asc' | 'desc'>('asc');
const roleFilter = ref<string>(props.filters?.role || '');
const statusFilter = ref<string>(props.filters?.status || '');

const sortedUsers = computed(() => {
  if (!sortField.value) return props.users.data;
  
  const sorted = [...props.users.data].sort((a, b) => {
    let aValue = a[sortField.value as keyof User];
    let bValue = b[sortField.value as keyof User];
    
    if (aValue === null || aValue === undefined) aValue = '';
    if (bValue === null || bValue === undefined) bValue = '';
    
    if (sortField.value === 'email_verified_at') {
      aValue = aValue ? 1 : 0;
      bValue = bValue ? 1 : 0;
    }
    
    if (typeof aValue === 'string' && typeof bValue === 'string') {
      aValue = aValue.toLowerCase();
      bValue = bValue.toLowerCase();
    }
    
    if (aValue < bValue) return sortDirection.value === 'asc' ? -1 : 1;
    if (aValue > bValue) return sortDirection.value === 'asc' ? 1 : -1;
    return 0;
  });
  
  return sorted;
});

const deleteUser = (userId: number) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
    router.delete(`/admin/users/${userId}`, {
      preserveScroll: true,
    });
  }
};

const sortBy = (field: string) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
};

const getSortIcon = (field: string) => {
  if (sortField.value !== field) return '↕️';
  return sortDirection.value === 'asc' ? '↑' : '↓';
};

const applyFilters = () => {
  const params = new URLSearchParams();
  
  if (roleFilter.value) {
    params.append('role', roleFilter.value);
  }
  
  if (statusFilter.value) {
    params.append('status', statusFilter.value);
  }
  
  router.get(`/admin/users?${params.toString()}`, {}, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  sortField.value = '';
  sortDirection.value = 'asc';
  roleFilter.value = '';
  statusFilter.value = '';
  router.get('/admin/users', {}, {
    preserveState: true,
    preserveScroll: true,
  });
};

const getRoleColor = (role: string) => {
  switch (role) {
  case 'admin': return 'text-error bg-error/10 border-error/30';
  default: return 'text-info bg-info/10 border-info/30';
  }
};

const getStatusColor = (status: string) => {
  switch (status) {
  case 'active': return 'text-success bg-success/10 border-success/30';
  case 'banned': return 'text-error bg-error/10 border-error/30';
  default: return 'text-base-content/70 bg-base-200/50 border-base-300/30';
  }
};

const capitalizeFirst = (str: string) => {
  return str.charAt(0).toUpperCase() + str.slice(1);
};

const getAvatarUrl = (user: User): string | undefined => {
  return user.avatar || undefined;
};

const getRoleLabel = (role: string) => {
  switch (role) {
  case 'admin': return 'Administrateur';
  case 'user': return 'Joueur';
  default: return capitalizeFirst(role);
  }
};

const getStatusLabel = (status: string) => {
  switch (status) {
  case 'active': return 'Actif';
  case 'banned': return 'Banni';
  default: return capitalizeFirst(status);
  }
};
</script>

<template>
  <Head title="Gestion des utilisateurs" />
  
  <div class="h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative overflow-hidden">
    <BackgroundEffects />

    <div class="relative z-10 h-full w-full flex flex-col">
      <div class="flex justify-center pt-4 mb-4 flex-shrink-0">
        <div class="text-center">
          <h1 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-info to-info/80 bg-clip-text text-transparent mb-1 tracking-wider">
            👥 GESTION UTILISATEURS
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            {{ users.total }} utilisateur{{ users.total > 1 ? 's' : '' }} enregistré{{ users.total > 1 ? 's' : '' }}
          </p>
        </div>
      </div>

      <div class="flex-1 flex flex-col lg:flex-row gap-4 px-2 md:px-4 lg:px-8 min-h-0 pb-4">
        <!-- Main Table Container -->
        <div class="flex-1 lg:mr-4 flex flex-col min-h-0" style="max-height: 85vh;">
          <div class="bg-base-100/60 backdrop-blur-sm rounded-t-xl border border-b-0 border-base-300/30 p-4 flex-shrink-0">
            <div class="flex flex-wrap gap-4 items-center">
              <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-base-content/70">Rôle:</label>
                <select 
                  v-model="roleFilter"
                  @change="applyFilters"
                  class="select select-sm select-bordered bg-base-100/80 border-base-300/50 text-sm min-w-[120px]"
                >
                  <option value="">Tous</option>
                  <option value="user">Joueur</option>
                  <option value="admin">Administrateur</option>
                </select>
              </div>
              
              <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-base-content/70">Statut:</label>
                <select 
                  v-model="statusFilter"
                  @change="applyFilters"
                  class="select select-sm select-bordered bg-base-100/80 border-base-300/50 text-sm min-w-[100px]"
                >
                  <option value="">Tous</option>
                  <option value="active">Actif</option>
                  <option value="banned">Banni</option>
                </select>
              </div>
              
              <Button
                @click="clearFilters"
                variant="ghost"
                size="sm"
                class="text-base-content/70 hover:text-base-content"
              >
                🗑️ Réinitialiser
              </Button>
            </div>
          </div>

          <div class="bg-base-100/60 backdrop-blur-sm rounded-b-xl border border-base-300/30 overflow-hidden flex flex-col flex-1">
            <div class="flex-shrink-0 p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">📋</span>
                LISTE DES UTILISATEURS
              </h3>
            </div>

            <div class="flex-1 overflow-auto min-h-0">
              <table class="w-full min-w-[900px]">
                <thead class="sticky top-0 bg-base-200/90 backdrop-blur-md border-b border-base-300/30 z-50">
                  <tr>
                    <th class="px-2 md:px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                      <button 
                        @click="sortBy('username')"
                        class="flex items-center gap-1 hover:text-base-content transition-colors"
                      >
                        Utilisateur
                        <span class="text-base-content/50">{{ getSortIcon('username') }}</span>
                      </button>
                    </th>
                    <th class="px-2 md:px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                      <button 
                        @click="sortBy('role')"
                        class="flex items-center gap-1 hover:text-base-content transition-colors"
                      >
                        Rôle
                        <span class="text-base-content/50">{{ getSortIcon('role') }}</span>
                      </button>
                    </th>
                    <th class="px-2 md:px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                      <button 
                        @click="sortBy('status')"
                        class="flex items-center gap-1 hover:text-base-content transition-colors"
                      >
                        Statut
                        <span class="text-base-content/50">{{ getSortIcon('status') }}</span>
                      </button>
                    </th>
                    <th class="px-2 md:px-4 py-3 text-center text-xs font-bold text-base-content/70 uppercase tracking-wider">
                      <button 
                        @click="sortBy('email_verified_at')"
                        class="flex items-center gap-1 hover:text-base-content transition-colors mx-auto"
                      >
                        Email vérifié
                        <span class="text-base-content/50">{{ getSortIcon('email_verified_at') }}</span>
                      </button>
                    </th>
                    <th class="px-2 md:px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                      <button 
                        @click="sortBy('level')"
                        class="flex items-center gap-1 hover:text-base-content transition-colors"
                      >
                        Niveau / XP
                        <span class="text-base-content/50">{{ getSortIcon('level') }}</span>
                      </button>
                    </th>
                    <th class="px-2 md:px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                      <button 
                        @click="sortBy('cash')"
                        class="flex items-center gap-1 hover:text-base-content transition-colors"
                      >
                        Cash
                        <span class="text-base-content/50">{{ getSortIcon('cash') }}</span>
                      </button>
                    </th>
                    <th class="px-2 md:px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                      <button 
                        @click="sortBy('last_login')"
                        class="flex items-center gap-1 hover:text-base-content transition-colors"
                      >
                        Dernière connexion
                        <span class="text-base-content/50">{{ getSortIcon('last_login') }}</span>
                      </button>
                    </th>
                    <th class="px-2 md:px-4 py-3 text-center text-xs font-bold text-base-content/70 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-base-300/20">
                  <tr v-for="user in sortedUsers" :key="user.id" 
                      class="hover:bg-base-200/30 transition-colors">
                    <td class="px-2 md:px-4 py-4">
                      <div class="flex items-center gap-2 md:gap-3">
                        <div class="relative">
                          <img 
                            v-if="getAvatarUrl(user)"
                            :src="getAvatarUrl(user)"
                            :alt="user.username"
                            class="w-8 h-8 md:w-10 md:h-10 rounded-full object-cover border-2 border-primary/20"
                          />
                          <div 
                            v-else
                            class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-xs md:text-sm font-bold border-2 border-primary/20"
                          >
                            {{ user.username.charAt(0).toUpperCase() }}
                          </div>
                        </div>
                        <div class="min-w-0 flex-1">
                          <div class="font-semibold text-base-content truncate">{{ user.username }}</div>
                          <div class="text-xs text-base-content/60 truncate">{{ user.email }}</div>
                        </div>
                      </div>
                    </td>

                    <td class="px-2 md:px-4 py-4">
                      <span :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full border',
                        getRoleColor(user.role)
                      ]">
                        {{ getRoleLabel(user.role) }}
                      </span>
                    </td>

                    <td class="px-2 md:px-4 py-4">
                      <span :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full border',
                        getStatusColor(user.status)
                      ]">
                        {{ getStatusLabel(user.status) }}
                      </span>
                    </td>

                    <td class="px-2 md:px-4 py-4 text-center">
                      <div v-if="user.email_verified_at" class="flex items-center justify-center">
                        <div class="w-6 h-6 bg-success rounded-full flex items-center justify-center" title="Email vérifié">
                          <span class="text-white text-xs font-bold">✓</span>
                        </div>
                      </div>
                      <div v-else class="flex items-center justify-center">
                        <div class="w-6 h-6 bg-error rounded-full flex items-center justify-center" title="Email non vérifié">
                          <span class="text-white text-xs font-bold">✗</span>
                        </div>
                      </div>
                    </td>

                    <td class="px-2 md:px-4 py-4">
                      <div class="text-sm">
                        <div class="font-semibold">Niveau {{ user.level || 1 }}</div>
                        <div class="text-xs text-base-content/60">{{ (user.experience || 0).toLocaleString() }} XP</div>
                      </div>
                    </td>

                    <td class="px-2 md:px-4 py-4">
                      <div class="text-sm font-semibold text-success">
                        {{ (user.cash || 0).toLocaleString() }} <span class="text-xs">💰</span>
                      </div>
                    </td>

                    <td class="px-2 md:px-4 py-4">
                      <div class="text-xs text-base-content/60">
                        {{ user.last_login ? 
                          new Date(user.last_login).toLocaleDateString('fr-FR') :
                          'Jamais' }}
                      </div>
                    </td>

                    <td class="px-2 md:px-4 py-4">
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

            <div v-if="users.last_page > 1" class="flex-shrink-0 bg-gradient-to-r from-info/10 to-info/5 px-4 py-3 border-t border-info/20">
              <div class="flex justify-center items-center gap-2 flex-wrap">
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

        <!-- Actions Panel - Sidebar -->
        <div class="lg:w-64 lg:flex-shrink-0">
          <div class="space-y-4">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
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
        </div>
      </div>
    </div>
  </div>
</template>