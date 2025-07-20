<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import Modal from '@/Components/UI/Modal.vue';
import type { User } from '@/types/user';
import { Head, router } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
import { UserCog, User as UserIcon, Settings, Eye, Home, Users, FileText, RotateCcw, Trash2, AlertTriangle, Save, Loader2, Coins } from 'lucide-vue-next';

const props = defineProps<{
  user: User;
  roles: string[];
  statuses: string[];
}>();

const form = reactive({
  username: props.user.username,
  email: props.user.email,
  password: '',
  level: props.user.level,
  experience: props.user.experience,
  cash: props.user.cash,
  role: props.user.role,
  status: props.user.status,
  processing: false,
  errors: {} as Record<string, string>,
});

const showDeleteModal = ref(false);

const hasChanges = computed(() => {
  return form.username !== props.user.username ||
    form.email !== props.user.email ||
    form.password !== '' ||
    form.level !== props.user.level ||
    form.experience !== props.user.experience ||
    form.cash !== props.user.cash ||
    form.role !== props.user.role ||
    form.status !== props.user.status;
});

const submit = () => {
  form.processing = true;
  form.errors = {};

  router.put(`/admin/users/${props.user.id}`, {
    username: form.username,
    email: form.email,
    password: form.password,
    level: form.level,
    experience: form.experience,
    cash: form.cash,
    role: form.role,
    status: form.status,
  }, {
    onSuccess: () => {
      form.processing = false;
    },
    onError: (errors: Record<string, string>) => {
      form.processing = false;
      form.errors = errors;
    },
  });
};

const resetForm = () => {
  form.username = props.user.username;
  form.email = props.user.email;
  form.password = '';
  form.level = props.user.level;
  form.experience = props.user.experience;
  form.cash = props.user.cash;
  form.role = props.user.role;
  form.status = props.user.status;
  form.errors = {};
};

const getRoleColor = (role: string) => {
  switch (role) {
  case 'admin': return 'text-error bg-error/10 border-error/20';
  default: return 'text-info bg-info/10 border-info/20';
  }
};

const getStatusColor = (status: string) => {
  switch (status) {
  case 'active': return 'text-success bg-success/10 border-success/20';
  case 'suspended': return 'text-warning bg-warning/10 border-warning/20';
  case 'banned': return 'text-error bg-error/10 border-error/20';
  default: return 'text-base-content/70 bg-base-200/50 border-base-300/20';
  }
};

const confirmDeleteUser = () => {
  showDeleteModal.value = true;
};

const deleteUser = () => {
  router.delete(`/admin/users/${props.user.id}`, {
    onSuccess: () => router.visit('/admin/users')
  });
  showDeleteModal.value = false;
};
</script>

<template>
  <Head title="Modifier l'utilisateur" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen w-full">
      <div class="flex justify-center pt-6 mb-6">
        <div class="text-center">
          <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent mb-1 tracking-wider">
            <component :is="UserCog" :size="28" class="inline align-middle mr-2" />
            MODIFIER UTILISATEUR
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Édition de {{ user.username }}
          </p>
        </div>
      </div>

      <div class="container mx-auto px-4 max-w-7xl">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 xl:gap-6">

          <div class="xl:col-span-3 order-1 xl:order-1">
            <div class="space-y-4">

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="UserIcon" :size="18" />
                    UTILISATEUR ACTUEL
                  </h3>
                </div>
                <div class="p-4">
                  <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-lg font-bold border-2 border-primary/20">
                      {{ user.username.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                      <div class="font-semibold text-sm">{{ user.username }}</div>
                      <div class="text-xs text-base-content/60">ID: #{{ user.id }}</div>
                    </div>
                  </div>
                  <div class="space-y-2">
                    <span :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full border w-full justify-center',
                      getRoleColor(user.role)
                    ]">
                      {{ user.role }}
                    </span>
                    <span :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full border w-full justify-center',
                      getStatusColor(user.status)
                    ]">
                      {{ user.status }}
                    </span>
                  </div>
                  <div class="mt-3 pt-3 border-t border-base-300/30 text-xs space-y-2">
                    <div class="flex justify-between">
                      <span class="text-base-content/70">Niveau:</span>
                      <span class="font-semibold">{{ user.level }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-base-content/70">Cash:</span>
                      <span class="font-semibold text-success flex items-center gap-1">
                        {{ (user.cash || 0).toLocaleString() }} <component :is="Coins" :size="12" />
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
                <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                  <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                    <component :is="Settings" :size="18" />
                    ACTIONS
                  </h3>
                </div>
                <div class="p-3 space-y-2">
                  <Button @click="resetForm" variant="outline" size="sm" class="w-full justify-start" :disabled="form.processing || !hasChanges">
                    <component :is="RotateCcw" :size="16" class="mr-2" /> Annuler les modifications
                  </Button>
                  <Button @click="router.visit(`/admin/users/${user.id}`)" variant="outline" size="sm" class="w-full justify-start">
                    <component :is="Eye" :size="16" class="mr-2" /> Voir le profil
                  </Button>
                  <Button @click="confirmDeleteUser" variant="outline" size="sm" class="w-full justify-start text-error hover:text-error hover:bg-error/10">
                    <component :is="Trash2" :size="16" class="mr-2" /> Supprimer
                  </Button>
                  <div class="border-t border-base-300/30 pt-2 space-y-1">
                    <Button @click="router.visit('/admin/users')" variant="ghost" size="sm" class="w-full justify-start">
                      <component :is="Users" :size="16" class="mr-2" /> Liste utilisateurs
                    </Button>
                    <Button @click="router.visit('/admin')" variant="ghost" size="sm" class="w-full justify-start">
                      <component :is="Home" :size="16" class="mr-2" /> Dashboard
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-6 order-2 xl:order-2">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-[650px] sm:h-[700px] md:h-[750px] xl:h-[800px] flex flex-col">
              <div class="shrink-0 p-4 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                  <component :is="FileText" :size="18" />
                  FORMULAIRE DE MODIFICATION
                </h3>
              </div>

              <form @submit.prevent="submit" class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                  <div>
                    <label for="username" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                      Nom d'utilisateur <span class="text-error">*</span>
                    </label>
                    <Input id="username" v-model="form.username" type="text" required :class="{
                      'border-error': form.errors.username,
                      'border-warning': form.username !== user.username
                    }" />
                    <p v-if="form.errors.username" class="mt-1 text-sm text-error">
                      {{ form.errors.username }}
                    </p>
                  </div>

                  <div>
                    <label for="email" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                      Email <span class="text-error">*</span>
                    </label>
                    <Input id="email" v-model="form.email" type="email" required :class="{
                      'border-error': form.errors.email,
                      'border-warning': form.email !== user.email
                    }" />
                    <p v-if="form.errors.email" class="mt-1 text-sm text-error">
                      {{ form.errors.email }}
                    </p>
                  </div>

                  <div class="md:col-span-2">
                    <label for="password" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                      Nouveau mot de passe
                      <span class="text-xs text-base-content/50 normal-case">(laisser vide pour ne pas changer)</span>
                    </label>
                    <Input id="password" v-model="form.password" type="password" placeholder="Nouveau mot de passe (optionnel)" :class="{
                      'border-error': form.errors.password,
                      'border-warning': form.password !== ''
                    }" />
                    <p v-if="form.errors.password" class="mt-1 text-sm text-error">
                      {{ form.errors.password }}
                    </p>
                  </div>

                  <div>
                    <label for="role" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                      Rôle <span class="text-error">*</span>
                    </label>
                    <Select id="role" v-model="form.role" :options="roles.map(role => ({ value: role, label: role }))" :class="{
                      'border-error': form.errors.role,
                      'border-warning': form.role !== user.role
                    }" />
                    <p v-if="form.errors.role" class="mt-1 text-sm text-error">
                      {{ form.errors.role }}
                    </p>
                  </div>

                  <div>
                    <label for="status" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                      Statut <span class="text-error">*</span>
                    </label>
                    <Select id="status" v-model="form.status" :options="statuses.map(status => ({ value: status, label: status }))" :class="{
                      'border-error': form.errors.status,
                      'border-warning': form.status !== user.status
                    }" />
                    <p v-if="form.errors.status" class="mt-1 text-sm text-error">
                      {{ form.errors.status }}
                    </p>
                  </div>

                  <div>
                    <label for="level" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                      Niveau
                    </label>
                    <Input id="level" v-model.number="form.level" type="number" min="1" max="100" :class="{
                      'border-error': form.errors.level,
                      'border-warning': form.level !== user.level
                    }" />
                    <p v-if="form.errors.level" class="mt-1 text-sm text-error">
                      {{ form.errors.level }}
                    </p>
                  </div>

                  <div>
                    <label for="experience" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                      Expérience
                    </label>
                    <Input id="experience" v-model.number="form.experience" type="number" min="0" :class="{
                      'border-error': form.errors.experience,
                      'border-warning': form.experience !== user.experience
                    }" />
                    <p v-if="form.errors.experience" class="mt-1 text-sm text-error">
                      {{ form.errors.experience }}
                    </p>
                  </div>

                  <div>
                    <label for="cash" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider flex items-center gap-1">
                      Cash <component :is="Coins" :size="14" />
                    </label>
                    <Input id="cash" v-model.number="form.cash" type="number" min="0" :class="{
                      'border-error': form.errors.cash,
                      'border-warning': form.cash !== user.cash
                    }" />
                    <p v-if="form.errors.cash" class="mt-1 text-sm text-error">
                      {{ form.errors.cash }}
                    </p>
                  </div>
                </div>
              </form>

              <div class="shrink-0 bg-gradient-to-r from-warning/10 to-warning/5 px-6 py-4 border-t border-warning/20">
                <div class="flex items-center justify-between">
                  <div class="text-xs text-base-content/70">
                    <span v-if="hasChanges" class="text-warning flex items-center gap-1">
                      <component :is="AlertTriangle" :size="14" /> Modifications non sauvegardées
                    </span>
                    <span v-else>Aucune modification</span>
                  </div>
                  <div class="flex items-center gap-3">
                    <Button @click="router.visit('/admin/users')" variant="ghost" size="md" :disabled="form.processing">
                      Annuler
                    </Button>
                    <Button @click="submit" variant="primary" size="md" :disabled="form.processing || !hasChanges">
                      <component :is="form.processing ? Loader2 : Save" :size="16" class="mr-2" :class="{ 'animate-spin': form.processing }" />
                      {{ form.processing ? 'Mise à jour...' : 'Sauvegarder les modifications' }}
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="xl:col-span-3 order-3 xl:order-3">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-3 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
                <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                  <component :is="Eye" :size="18" />
                  APERÇU DES MODIFICATIONS
                </h3>
              </div>
              <div class="p-4 space-y-4">
                <div class="text-center">
                  <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-xl font-bold mb-2 border-2 border-primary/20">
                    {{ form.username ? form.username.charAt(0).toUpperCase() : '?' }}
                  </div>
                  <div class="text-sm font-semibold">{{ form.username }}</div>
                  <div class="text-xs text-base-content/60">{{ form.email }}</div>
                </div>
                <div class="space-y-3 text-xs">
                  <div class="flex justify-between items-center">
                    <span class="text-base-content/70">Rôle:</span>
                    <span :class="[
                      'px-2 py-1 rounded text-xs font-semibold',
                      form.role !== user.role ? 'bg-warning/20 text-warning' : ''
                    ]">
                      {{ form.role }}
                    </span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-base-content/70">Statut:</span>
                    <span :class="[
                      'px-2 py-1 rounded text-xs font-semibold',
                      form.status !== user.status ? 'bg-warning/20 text-warning' : ''
                    ]">
                      {{ form.status }}
                    </span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-base-content/70">Niveau:</span>
                    <span :class="[
                      'font-semibold',
                      form.level !== user.level ? 'text-warning' : ''
                    ]">
                      {{ form.level }}
                    </span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-base-content/70">Cash:</span>
                    <span :class="[
                      'font-semibold flex items-center gap-1',
                      form.cash !== user.cash ? 'text-warning' : 'text-success'
                    ]">
                      {{ form.cash }} <component :is="Coins" :size="12" />
                    </span>
                  </div>
                  <div v-if="form.password" class="flex justify-between items-center">
                    <span class="text-base-content/70">Mot de passe:</span>
                    <span class="text-warning font-semibold">Sera modifié</span>
                  </div>
                </div>
                <div v-if="hasChanges" class="text-xs text-warning bg-warning/10 p-2 rounded border border-warning/20 flex items-center gap-2">
                  <component :is="AlertTriangle" :size="14" />
                  Modifications en attente
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
          Êtes-vous sûr de vouloir supprimer l'utilisateur <strong>{{ user.username }}</strong> ?
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
