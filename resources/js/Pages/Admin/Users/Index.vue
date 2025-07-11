<script setup lang="ts">
import type { User } from '@/types/user';
import { Head, Link, router } from '@inertiajs/vue3';

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
  case 'admin': return 'bg-red-100 text-red-800';
  case 'premium': return 'bg-yellow-100 text-yellow-800';
  default: return 'bg-gray-100 text-gray-800';
  }
};

const getStatusColor = (status: string) => {
  switch (status) {
  case 'active': return 'bg-green-100 text-green-800';
  case 'suspended': return 'bg-orange-100 text-orange-800';
  case 'banned': return 'bg-red-100 text-red-800';
  default: return 'bg-gray-100 text-gray-800';
  }
};
</script>

<template>

  <Head title="Gestion des utilisateurs" />

  <div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Gestion des utilisateurs</h1>
            <div class="flex items-center space-x-3">
              <Link href="/me"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                ← Retour
              </Link>
              <Link href="/admin/users/create"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                Nouvel utilisateur
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Users Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Utilisateur
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Rôle / Statut
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Niveau / XP
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Cash
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Dernière connexion
                </th>
                <th
                  class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ user.username }}</div>
                    <div class="text-sm text-gray-500">{{ user.email }}</div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="space-y-1">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                          :class="getRoleColor(user.role)">
                      {{ user.role }}
                    </span>
                    <br>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                          :class="getStatusColor(user.status)">
                      {{ user.status }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div>Niveau {{ user.level }}</div>
                  <div class="text-xs text-gray-500">{{ user.experience }} XP</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ user.cash.toLocaleString() }} ₽
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ user.last_login ? new Date(user.last_login).toLocaleDateString('fr-FR') :
                    'Jamais' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                  <Link :href="`/admin/users/${user.id}`"
                        class="text-blue-600 hover:text-blue-900 transition-colors">
                    Voir
                  </Link>
                  <Link :href="`/admin/users/${user.id}/edit`"
                        class="text-indigo-600 hover:text-indigo-900 transition-colors">
                    Modifier
                  </Link>
                  <button @click="deleteUser(user.id)"
                          class="text-red-600 hover:text-red-900 transition-colors">
                    Supprimer
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6" v-if="users.last_page > 1">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <Link v-if="users.current_page > 1"
                    :href="users.links.find(link => link.label === '&laquo; Previous')?.url || ''"
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Précédent
              </Link>
              <Link v-if="users.current_page < users.last_page"
                    :href="users.links.find(link => link.label === 'Next &raquo;')?.url || ''"
                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Suivant
              </Link>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Affichage de <span class="font-medium">{{ (users.current_page - 1) * users.per_page
                    + 1 }}</span>
                  à <span class="font-medium">{{ Math.min(users.current_page * users.per_page,
                                                          users.total) }}</span>
                  sur <span class="font-medium">{{ users.total }}</span> résultats
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <template v-for="link in users.links" :key="link.label">
                    <Link v-if="link.url" :href="link.url" :class="[
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors',
                      link.active
                        ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                    ]" v-html="link.label" />
                    <span v-else :class="[
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                      'bg-white border-gray-300 text-gray-300 cursor-default'
                    ]" v-html="link.label" />
                  </template>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>