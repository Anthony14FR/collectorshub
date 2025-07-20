<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Modal from '@/Components/UI/Modal.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
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
  created_at: string;
}

interface Props {
  user: User;
  roles: string[];
  statuses: string[];
  errors?: Record<string, string>;
}

const props = defineProps<Props>();

const form = useForm({
  username: props.user.username,
  email: props.user.email,
  level: props.user.level,
  experience: props.user.experience,
  cash: props.user.cash,
  role: props.user.role,
  status: props.user.status,
  password: ''
});

const isSubmitting = ref(false);
const showDeleteModal = ref(false);

const roleOptions = computed(() => {
  return props.roles.map(role => ({
    value: role,
    label: getRoleLabel(role)
  }));
});

const statusOptions = computed(() => {
  return props.statuses.map(status => ({
    value: status,
    label: getStatusLabel(status)
  }));
});

const submit = () => {
  isSubmitting.value = true;
  form.put(`/admin/users/${props.user.id}`, {
    onSuccess: () => {
      router.visit('/admin/users');
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

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

const goBack = () => {
  router.visit('/admin/users');
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

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};
</script>

<template>
  <Head title="Modifier l'utilisateur" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-2 tracking-wider">
            ✏️ MODIFIER UTILISATEUR
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            Édition de {{ props.user.username }}
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-8">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-6 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <h3 class="text-xl font-bold tracking-wider flex items-center gap-2">
                  <span class="text-2xl">👤</span>
                  INFORMATIONS UTILISATEUR
                </h3>
              </div>

              <form @submit.prevent="submit" class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Nom d'utilisateur *
                    </label>
                    <Input
                      v-model="form.username"
                      placeholder="Entrer le nom d'utilisateur"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.username" class="text-xs text-error mt-1">
                      {{ props.errors.username }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Adresse email *
                    </label>
                    <Input
                      v-model="form.email"
                      type="email"
                      placeholder="exemple@email.com"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.email" class="text-xs text-error mt-1">
                      {{ props.errors.email }}
                    </p>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-bold text-base-content/80 mb-2">
                    Nouveau mot de passe
                  </label>
                  <Input
                    v-model="form.password"
                    type="password"
                    placeholder="••••••••"
                    class="w-full"
                  />
                  <p v-if="props.errors?.password" class="text-xs text-error mt-1">
                    {{ props.errors.password }}
                  </p>
                  <p class="text-xs text-base-content/60">
                    Laisser vide pour conserver le mot de passe actuel
                  </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Niveau *
                    </label>
                    <Input
                      v-model="form.level"
                      type="number"
                      min="1"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.level" class="text-xs text-error mt-1">
                      {{ props.errors.level }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Expérience *
                    </label>
                    <Input
                      v-model="form.experience"
                      type="number"
                      min="0"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.experience" class="text-xs text-error mt-1">
                      {{ props.errors.experience }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Cash 💰 *
                    </label>
                    <Input
                      v-model="form.cash"
                      type="number"
                      min="0"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.cash" class="text-xs text-error mt-1">
                      {{ props.errors.cash }}
                    </p>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Rôle *
                    </label>
                    <Select
                      v-model="form.role"
                      :options="roleOptions"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.role" class="text-xs text-error mt-1">
                      {{ props.errors.role }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Statut *
                    </label>
                    <Select
                      v-model="form.status"
                      :options="statusOptions"
                      class="w-full"
                      required
                    />
                    <p v-if="props.errors?.status" class="text-xs text-error mt-1">
                      {{ props.errors.status }}
                    </p>
                  </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-base-300/30">
                  <Button
                    type="submit"
                    variant="primary"
                    size="lg"
                    :disabled="isSubmitting || form.processing"
                    class="flex-1 sm:flex-none sm:px-8"
                  >
                    <span v-if="isSubmitting || form.processing">⏳</span>
                    <span v-else>💾</span>
                    {{ isSubmitting || form.processing ? 'Mise à jour...' : 'Mettre à jour' }}
                  </Button>

                  <Button
                    @click="goBack"
                    variant="secondary"
                    size="lg"
                    :disabled="isSubmitting || form.processing"
                    class="flex-1 sm:flex-none sm:px-8"
                  >
                    ← Retour à la liste
                  </Button>
                </div>
              </form>
            </div>
          </div>

          <div class="xl:col-span-4 space-y-6">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">👤</span>
                  UTILISATEUR ACTUEL
                </h3>
              </div>
              <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                  <div class="w-16 h-16 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-xl font-bold">
                    {{ props.user.username.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <div class="font-semibold text-lg">{{ props.user.username }}</div>
                    <div class="text-sm text-base-content/70">{{ props.user.email }}</div>
                  </div>
                </div>

                <div class="space-y-3 text-sm">
                  <div class="flex justify-between">
                    <span class="text-base-content/70">ID:</span>
                    <span class="font-medium">#{{ props.user.id }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Inscription:</span>
                    <span class="font-medium">{{ formatDate(props.user.created_at) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Niveau:</span>
                    <span class="font-medium text-primary">{{ props.user.level }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-base-content/70">Cash:</span>
                    <span class="font-medium text-warning">{{ props.user.cash.toLocaleString() }} 💰</span>
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
                  La suppression d'un utilisateur est définitive et supprimera toutes ses données.
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

            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">🔗</span>
                  NAVIGATION
                </h3>
              </div>
              <div class="p-6 space-y-3">
                <Button
                  @click="router.visit(`/admin/users/${props.user.id}`)"
                  variant="outline"
                  size="sm"
                  class="w-full justify-start"
                >
                  👁️ Voir l'utilisateur
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
