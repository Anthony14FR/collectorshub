<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface User {
  id: number;
  username: string;
  email: string;
  level: number;
  experience: number;
  cash: number;
  role: string;
  status: string;
  last_login: string | null;
  created_at: string;
}

interface Props {
  users: {
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
  };
  filters: {
    role?: string;
    status?: string;
  };
}

const props = defineProps<Props>();

const roleFilter = ref(props.filters.role || '');
const statusFilter = ref(props.filters.status || '');
const searchTerm = ref('');
const showDeleteModal = ref(false);
const userToDelete = ref<User | null>(null);

const roleOptions = [
  { value: '', label: 'Tous rôles' },
  { value: 'admin', label: 'Admin' },
  { value: 'user', label: 'Joueur' }
];

const statusOptions = [
  { value: '', label: 'Tous statuts' },
  { value: 'active', label: 'Actif' },
  { value: 'banned', label: 'Banni' }
];

const filteredUsers = computed(() => {
  return props.users.data.filter(user => {
    const matchesSearch = user.username.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      user.email.toLowerCase().includes(searchTerm.value.toLowerCase());
    return matchesSearch;
  });
});

const deleteUser = (user: User) => {
  userToDelete.value = user;
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  if (userToDelete.value) {
    router.delete(`/admin/users/${userToDelete.value.id}`, {
      preserveScroll: true,
      onFinish: () => {
        showDeleteModal.value = false;
        userToDelete.value = null;
      }
    });
  }
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  userToDelete.value = null;
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
  roleFilter.value = '';
  statusFilter.value = '';
  searchTerm.value = '';
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

const getRoleLabel = (role: string) => {
  switch (role) {
  case 'admin': return 'Administrateur';
  case 'user': return 'Joueur';
  default: return role;
  }
};

const getStatusLabel = (status: string) => {
  switch (status) {
  case 'active': return 'Actif';
  case 'banned': return 'Banni';
  default: return status;
  }
};

const formatDate = (dateString: string | null) => {
  if (!dateString) return 'Jamais';
  return new Date(dateString).toLocaleDateString('fr-FR');
};
</script>

<template>
  <Head title="Gestion des utilisateurs" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-info to-info/80 bg-clip-text text-transparent mb-2 tracking-wider">
            👥 GESTION UTILISATEURS
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            {{ props.users.total }} utilisateur{{ props.users.total > 1 ? 's' : '' }} enregistré{{ props.users.total > 1 ? 's' : '' }}
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-9 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                  <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                    <span class="text-2xl">📋</span>
                    LISTE DES UTILISATEURS
                  </h3>

                  <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <Input
                      v-model="searchTerm"
                      placeholder="🔍 Rechercher..."
                      class="w-full sm:w-64"
                      size="sm"
                    />

                    <div class="flex gap-2">
                      <Select
                        v-model="roleFilter"
                        @change="applyFilters"
                        :options="roleOptions"
                        class="w-32"
                      />

                      <Select
                        v-model="statusFilter"
                        @change="applyFilters"
                        :options="statusOptions"
                        class="w-32"
                      />

                      <Button @click="clearFilters" variant="outline" size="sm">
                        ✨ Reset
                      </Button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead class="bg-gradient-to-r from-base-200/50 to-base-300/30 border-b border-base-300/30">
                    <tr>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Utilisateur
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Niveau & XP
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Cash
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Rôle
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Statut
                      </th>
                      <th class="px-6 py-4 text-left text-sm font-bold text-base-content uppercase tracking-wider">
                        Dernière connexion
                      </th>
                      <th class="px-6 py-4 text-center text-sm font-bold text-base-content uppercase tracking-wider">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-base-300/30">
                    <tr
                      v-for="user in filteredUsers"
                      :key="user.id"
                      class="hover:bg-base-200/30 transition-colors duration-200"
                    >
                      <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-sm font-bold">
                            {{ user.username.charAt(0).toUpperCase() }}
                          </div>
                          <div>
                            <div class="font-semibold text-base-content">{{ user.username }}</div>
                            <div class="text-sm text-base-content/70">{{ user.email }}</div>
                          </div>
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <div class="text-sm">
                          <div class="font-semibold text-primary">Niveau {{ user.level }}</div>
                          <div class="text-base-content/70">{{ user.experience.toLocaleString() }} XP</div>
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <div class="font-semibold text-warning">
                          {{ user.cash.toLocaleString() }} 💰
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <span :class="[
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border',
                          getRoleColor(user.role)
                        ]">
                          {{ getRoleLabel(user.role) }}
                        </span>
                      </td>

                      <td class="px-6 py-4">
                        <span :class="[
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border',
                          getStatusColor(user.status)
                        ]">
                          {{ getStatusLabel(user.status) }}
                        </span>
                      </td>

                      <td class="px-6 py-4">
                        <div class="text-sm text-base-content/70">
                          {{ formatDate(user.last_login) }}
                        </div>
                      </td>

                      <td class="px-6 py-4">
                        <div class="flex justify-center gap-1">
                          <Button
                            @click="router.visit(`/admin/users/${user.id}`)"
                            variant="ghost"
                            size="sm"
                            class="text-info hover:text-info hover:bg-info/20 transition-colors"
                            title="Voir"
                          >
                            👁️
                          </Button>
                          <Button
                            @click="router.visit(`/admin/users/${user.id}/edit`)"
                            variant="ghost"
                            size="sm"
                            class="text-warning hover:text-warning hover:bg-warning/20 transition-colors"
                            title="Modifier"
                          >
                            ✏️
                          </Button>
                          <Button
                            @click="deleteUser(user)"
                            variant="ghost"
                            size="sm"
                            class="text-error hover:text-error hover:bg-error/20 transition-colors"
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

              <div v-if="props.users.last_page > 1" class="p-4 bg-gradient-to-r from-info/5 to-info/10 border-t border-info/20">
                <div class="flex justify-center items-center gap-2 flex-wrap">
                  <template v-for="link in props.users.links" :key="link.label">
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
                  Affichage de {{ (props.users.current_page - 1) * props.users.per_page + 1 }}
                  à {{ Math.min(props.users.current_page * props.users.per_page, props.users.total) }}
                  sur {{ props.users.total }} résultats
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-3 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">⚙️</span>
                  ACTIONS
                </h3>
              </div>
              <div class="p-6 space-y-3">
                <Button
                  @click="router.visit('/admin/users/create')"
                  variant="secondary"
                  size="sm"
                  class="w-full justify-start"
                >
                  ➕ Nouvel utilisateur
                </Button>
                <Button
                  @click="router.visit('/admin/')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  ← Dashboard
                </Button>
                <Button
                  @click="router.visit('/me')"
                  variant="ghost"
                  size="sm"
                  class="w-full justify-start"
                >
                  🏠 Profil
                </Button>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">📊</span>
                  STATISTIQUES
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-center">
                  <div class="text-2xl font-bold text-info">{{ props.users.total }}</div>
                  <div class="text-sm text-base-content/70">Total utilisateurs</div>
                </div>
                <div class="grid grid-cols-2 gap-3 text-center">
                  <div>
                    <div class="text-lg font-bold text-success">{{ props.users.data.filter(u => u.status === 'active').length }}</div>
                    <div class="text-xs text-base-content/70">Actifs</div>
                  </div>
                  <div>
                    <div class="text-lg font-bold text-error">{{ props.users.data.filter(u => u.role === 'admin').length }}</div>
                    <div class="text-xs text-base-content/70">Admins</div>
                  </div>
                </div>
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
          <h3 class="text-xl font-bold text-base-content">Supprimer l'utilisateur</h3>
        </div>
      </template>

      <div class="space-y-4">
        <p class="text-base-content/80">
          Êtes-vous sûr de vouloir supprimer l'utilisateur
          <span class="font-bold text-error">{{ userToDelete?.username }}</span> ?
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
