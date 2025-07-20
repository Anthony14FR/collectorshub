<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

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
  updated_at: string;
}

interface Props {
  user: User;
}

const props = defineProps<Props>();

const showDeleteModal = ref(false);

const deleteUser = () => {
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  router.delete(`/admin/users/${props.user.id}`, {
    onSuccess: () => router.visit('/admin/users'),
    onFinish: () => {
      showDeleteModal.value = false;
    }
  });
};

const cancelDelete = () => {
  showDeleteModal.value = false;
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
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatSimpleDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const getAccountAge = () => {
  const createdDate = new Date(props.user.created_at);
  const now = new Date();
  const diffTime = Math.abs(now.getTime() - createdDate.getTime());
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays < 30) {
    return `${diffDays} jour${diffDays > 1 ? 's' : ''}`;
  } else if (diffDays < 365) {
    const months = Math.floor(diffDays / 30);
    return `${months} mois`;
  } else {
    const years = Math.floor(diffDays / 365);
    return `${years} an${years > 1 ? 's' : ''}`;
  }
};
</script>

<template>
  <Head :title="`Utilisateur: ${user.username}`" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-info to-info/80 bg-clip-text text-transparent mb-2 tracking-wider">
            👁️ PROFIL UTILISATEUR
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            Détails de {{ props.user.username }}
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-8 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-6 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                <div class="flex items-center gap-4">
                  <div class="w-20 h-20 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-2xl font-bold">
                    {{ props.user.username.charAt(0).toUpperCase() }}
                  </div>
                  <div class="flex-1">
                    <h3 class="text-2xl font-bold text-base-content mb-1">{{ props.user.username }}</h3>
                    <p class="text-base-content/70 mb-2">{{ props.user.email }}</p>
                    <div class="flex gap-3">
                      <span :class="[
                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border',
                        getRoleColor(props.user.role)
                      ]">
                        {{ getRoleLabel(props.user.role) }}
                      </span>
                      <span :class="[
                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border',
                        getStatusColor(props.user.status)
                      ]">
                        {{ getStatusLabel(props.user.status) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                  <div class="bg-gradient-to-br from-primary/10 to-primary/5 rounded-xl p-6 border border-primary/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-primary mb-2">{{ props.user.level }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">Niveau</div>
                    </div>
                  </div>

                  <div class="bg-gradient-to-br from-secondary/10 to-secondary/5 rounded-xl p-6 border border-secondary/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-secondary mb-2">{{ props.user.experience.toLocaleString() }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">Expérience</div>
                    </div>
                  </div>

                  <div class="bg-gradient-to-br from-warning/10 to-warning/5 rounded-xl p-6 border border-warning/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-warning mb-2">{{ props.user.cash.toLocaleString() }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">Cash 💰</div>
                    </div>
                  </div>

                  <div class="bg-gradient-to-br from-info/10 to-info/5 rounded-xl p-6 border border-info/20">
                    <div class="text-center">
                      <div class="text-3xl font-bold text-info mb-2">#{{ props.user.id }}</div>
                      <div class="text-sm text-base-content/70 uppercase tracking-wide">ID Utilisateur</div>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                  <div class="space-y-6">
                    <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                      📅 Informations temporelles
                    </h4>

                    <div class="space-y-4">
                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Inscription</span>
                        <span class="font-medium">{{ formatSimpleDate(props.user.created_at) }}</span>
                      </div>

                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Dernière modification</span>
                        <span class="font-medium">{{ formatSimpleDate(props.user.updated_at) }}</span>
                      </div>

                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Dernière connexion</span>
                        <span class="font-medium">{{ formatDate(props.user.last_login) }}</span>
                      </div>

                      <div class="flex justify-between items-center p-4 bg-base-200/30 rounded-lg">
                        <span class="text-base-content/70">Ancienneté</span>
                        <span class="font-medium text-success">{{ getAccountAge() }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="space-y-6">
                    <h4 class="text-lg font-bold text-base-content border-b border-base-300/30 pb-2">
                      📊 Informations compte
                    </h4>

                    <div class="space-y-4">
                      <div class="p-6 bg-gradient-to-br from-success/10 to-success/5 rounded-xl border border-success/20">
                        <div class="text-center">
                          <div class="text-2xl font-bold text-success mb-1">{{ props.user.role === 'admin' ? 'Admin' : 'Joueur' }}</div>
                          <div class="text-sm text-base-content/70">Type de compte</div>
                        </div>
                      </div>

                      <div class="p-6 bg-gradient-to-br from-accent/10 to-accent/5 rounded-xl border border-accent/20">
                        <div class="text-center">
                          <div class="text-2xl font-bold text-accent mb-1">{{ props.user.status === 'active' ? 'Actif' : 'Inactif' }}</div>
                          <div class="text-sm text-base-content/70">Statut du compte</div>
                        </div>
                      </div>

                      <div class="p-4 bg-base-200/30 rounded-lg">
                        <div class="text-center">
                          <div class="text-sm text-base-content/70 mb-2">Progression de niveau</div>
                          <div class="w-full bg-base-300/50 rounded-full h-3">
                            <div
                              class="bg-gradient-to-r from-primary to-secondary h-3 rounded-full transition-all duration-500"
                              :style="{ width: Math.min((props.user.level / 100) * 100, 100) + '%' }"
                            ></div>
                          </div>
                          <div class="text-xs text-base-content/60 mt-1">
                            Niveau {{ props.user.level }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-4 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">⚙️</span>
                  ACTIONS
                </h3>
              </div>
              <div class="p-6 space-y-3">
                <Button
                  @click="router.visit(`/admin/users/${props.user.id}/edit`)"
                  variant="primary"
                  size="sm"
                  class="w-full justify-start"
                >
                  ✏️ Modifier l'utilisateur
                </Button>
                <Button
                  @click="router.visit('/admin/users')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  📋 Liste utilisateurs
                </Button>
                <Button
                  @click="router.visit('/admin')"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  🏠 Dashboard
                </Button>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">📊</span>
                  RÉSUMÉ
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4 text-center">
                  <div>
                    <div class="text-xl font-bold text-info">{{ props.user.level }}</div>
                    <div class="text-xs text-base-content/70">Niveau</div>
                  </div>
                  <div>
                    <div class="text-xl font-bold text-success">{{ Math.floor(props.user.cash / 1000) }}k</div>
                    <div class="text-xs text-base-content/70">Cash</div>
                  </div>
                </div>

                <div class="text-center pt-4 border-t border-base-300/30">
                  <div class="text-sm font-medium text-base-content mb-1">Statut de connexion</div>
                  <div class="text-2xl font-bold text-primary">
                    {{ props.user.last_login ? '🟢' : '🔴' }}
                  </div>
                  <div class="text-xs text-base-content/60">
                    {{ props.user.last_login ? 'Connecté récemment' : 'Jamais connecté' }}
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-error/10 to-error/5 border-b border-error/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">⚠️</span>
                  ZONE DANGER
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <p class="text-sm text-base-content/70">
                  Actions irréversibles sur cet utilisateur
                </p>
                <Button
                  @click="deleteUser"
                  variant="outline"
                  size="sm"
                  class="w-full border-error text-error hover:bg-error hover:text-error-content"
                >
                  🗑️ Supprimer l'utilisateur
                </Button>
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
          <span class="font-bold text-error">{{ props.user.username }}</span> ?
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
