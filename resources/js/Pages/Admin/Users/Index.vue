<script setup lang="ts">
import type { User } from '@/types/user';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Users, Settings, BarChart3, Trash2, Eye, Edit, ArrowLeft, Home, Plus, FilterX, ArrowUpDown, ArrowUp, ArrowDown, CheckCircle, XCircle, Coins } from 'lucide-vue-next';

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
const showDeleteModal = ref(false);
const userToDelete = ref<User | null>(null);

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

const confirmDeleteUser = (user: User) => {
  userToDelete.value = user;
  showDeleteModal.value = true;
};

const deleteUser = () => {
  if (userToDelete.value) {
    router.delete(`/admin/users/${userToDelete.value.id}`, {
      preserveScroll: true,
    });
    showDeleteModal.value = false;
    userToDelete.value = null;
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
  if (sortField.value !== field) return ArrowUpDown;
  return sortDirection.value === 'asc' ? ArrowUp : ArrowDown;
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

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-info to-info/80 bg-clip-text text-transparent mb-1 tracking-wider">
            <component :is="Users" :size="28" class="inline align-middle mr-2" />
            GESTION UTILISATEURS
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            {{ users.total }} utilisateur{{ users.total > 1 ? 's' : '' }} enregistré{{ users.total > 1 ? 's' : '' }}
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
                    <component :is="Settings" :size="18" />
                    ACTIONS
                  </h3>
                </div>
                <div class="p-3 space-y-2">
                  <Button @click="router.visit('/admin/users/create')" variant="secondary" size="sm" class="w-full justify-start">
                    <component :is="Plus" :size="16" class="mr-2" /> Nouvel utilisateur
                  </Button>
                  <Button @click="router.visit('/admin/')" variant="outline" size="sm" class="w-full justify-start">
                    <component :is="ArrowLeft" :size="16" class="mr-2" /> Dashboard
                  </Button>
                  <Button @click="router.visit('/me')" variant="ghost" size="sm" class="w-full justify-start">
                    <component :is="Home" :size="16" class="mr-2" /> Profil
                  </Button>
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

          <div class="xl:col-span-9 order-2 xl:order-2">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-[650px] sm:h-[700px] md:h-[750px] xl:h-[800px] flex flex-col">

              <div class="shrink-0 p-4 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                <h3 class="text-sm font-bold tracking-wider flex items-center gap-2 mb-3">
                  <component :is="Users" :size="18" />
                  LISTE DES UTILISATEURS
                </h3>

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

                  <Button @click="clearFilters" variant="ghost" size="sm" class="text-base-content/70 hover:text-base-content">
                    <component :is="FilterX" :size="16" class="mr-2" /> Réinitialiser
                  </Button>
                </div>
              </div>

              <div class="flex-1 overflow-auto">
                <table class="w-full min-w-[900px]">
                  <thead class="sticky top-0 bg-base-200/90 backdrop-blur-md border-b border-base-300/30 z-50">
                    <tr>
                      <th class="px-2 md:px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                        <button
                          @click="sortBy('username')"
                          class="flex items-center gap-1 hover:text-base-content transition-colors"
                        >
                          Utilisateur
                          <component :is="getSortIcon('username')" :size="14" class="text-base-content/50" />
                        </button>
                      </th>
                      <th class="px-2 md:px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                        <button
                          @click="sortBy('role')"
                          class="flex items-center gap-1 hover:text-base-content transition-colors"
                        >
                          Rôle
                          <component :is="getSortIcon('role')" :size="14" class="text-base-content/50" />
                        </button>
                      </th>
                      <th class="px-2 md:px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                        <button
                          @click="sortBy('status')"
                          class="flex items-center gap-1 hover:text-base-content transition-colors"
                        >
                          Statut
                          <component :is="getSortIcon('status')" :size="14" class="text-base-content/50" />
                        </button>
                      </th>
                      <th class="px-2 md:px-4 py-3 text-center text-xs font-bold text-base-content/70 uppercase tracking-wider">
                        <button
                          @click="sortBy('email_verified_at')"
                          class="flex items-center gap-1 hover:text-base-content transition-colors mx-auto"
                        >
                          Email vérifié
                          <component :is="getSortIcon('email_verified_at')" :size="14" class="text-base-content/50" />
                        </button>
                      </th>
                      <th class="px-2 md:px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                        <button
                          @click="sortBy('level')"
                          class="flex items-center gap-1 hover:text-base-content transition-colors"
                        >
                          Niveau / XP
                          <component :is="getSortIcon('level')" :size="14" class="text-base-content/50" />
                        </button>
                      </th>
                      <th class="px-2 md:px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                        <button
                          @click="sortBy('cash')"
                          class="flex items-center gap-1 hover:text-base-content transition-colors"
                        >
                          Cash
                          <component :is="getSortIcon('cash')" :size="14" class="text-base-content/50" />
                        </button>
                      </th>
                      <th class="px-2 md:px-4 py-3 text-left text-xs font-bold text-base-content/70 uppercase tracking-wider">
                        <button
                          @click="sortBy('last_login')"
                          class="flex items-center gap-1 hover:text-base-content transition-colors"
                        >
                          Dernière connexion
                          <component :is="getSortIcon('last_login')" :size="14" class="text-base-content/50" />
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
                          <component :is="CheckCircle" :size="20" class="text-success" title="Email vérifié" />
                        </div>
                        <div v-else class="flex items-center justify-center">
                          <component :is="XCircle" :size="20" class="text-error" title="Email non vérifié" />
                        </div>
                      </td>

                      <td class="px-2 md:px-4 py-4">
                        <div class="text-sm">
                          <div class="font-semibold">Niveau {{ user.level || 1 }}</div>
                          <div class="text-xs text-base-content/60">{{ (user.experience || 0).toLocaleString() }} XP</div>
                        </div>
                      </td>

                      <td class="px-2 md:px-4 py-4">
                        <div class="text-sm font-semibold text-success flex items-center gap-1">
                          {{ (user.cash || 0).toLocaleString() }}
                          <component :is="Coins" :size="14" />
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
                            <component :is="Eye" :size="16" />
                          </Button>
                          <Button
                            @click="router.visit(`/admin/users/${user.id}/edit`)"
                            variant="ghost"
                            size="sm"
                            class="text-warning hover:text-warning hover:bg-warning/10"
                            title="Modifier"
                          >
                            <component :is="Edit" :size="16" />
                          </Button>
                          <Button
                            @click="confirmDeleteUser(user)"
                            variant="ghost"
                            size="sm"
                            class="text-error hover:text-error hover:bg-error/10"
                            title="Supprimer"
                          >
                            <component :is="Trash2" :size="16" />
                          </Button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div v-if="users.last_page > 1" class="shrink-0 bg-gradient-to-r from-info/10 to-info/5 px-4 py-3 border-t border-info/20">
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
        </div>
      </div>
    </div>

    <Modal :show="showDeleteModal" @close="showDeleteModal = false">
      <div class="p-6">
        <h3 class="text-lg font-bold text-base-content mb-4 flex items-center gap-2">
          <component :is="Trash2" :size="20" class="text-error" />
          Confirmer la suppression
        </h3>
        <p class="text-base-content/70 mb-6">
          Êtes-vous sûr de vouloir supprimer l'utilisateur <strong>{{ userToDelete?.username }}</strong> ?
          Cette action est irréversible.
        </p>
        <div class="flex justify-end gap-3">
          <Button @click="showDeleteModal = false" variant="ghost" size="sm">
            Annuler
          </Button>
          <Button @click="deleteUser" variant="error" size="sm">
            <component :is="Trash2" :size="16" class="mr-2" />
            Supprimer
          </Button>
        </div>
      </div>
    </Modal>
  </div>
</template>
