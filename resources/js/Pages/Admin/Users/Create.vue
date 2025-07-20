<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Props {
  roles: string[];
  statuses: string[];
  errors?: Record<string, string>;
}

const props = defineProps<Props>();

const form = useForm({
  username: '',
  email: '',
  password: '',
  level: 1,
  experience: 0,
  cash: 0,
  role: 'user',
  status: 'active'
});

const isSubmitting = ref(false);

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
  form.post('/admin/users', {
    onSuccess: () => {
      router.visit('/admin/users');
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
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
</script>

<template>
  <Head title="Créer un utilisateur" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative overflow-x-hidden">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="container mx-auto px-4 py-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-success to-success/80 bg-clip-text text-transparent mb-2 tracking-wider">
            ➕ CRÉER UTILISATEUR
          </h1>
          <p class="text-sm text-base-content/70 uppercase tracking-wider">
            Ajouter un nouvel utilisateur à la plateforme
          </p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
          <div class="xl:col-span-8 xl:col-start-3">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-6 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
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
                    Mot de passe *
                  </label>
                  <Input
                    v-model="form.password"
                    type="password"
                    placeholder="••••••••"
                    class="w-full"
                    required
                  />
                  <p v-if="props.errors?.password" class="text-xs text-error mt-1">
                    {{ props.errors.password }}
                  </p>
                  <p class="text-xs text-base-content/60">
                    Minimum 8 caractères requis
                  </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Niveau
                    </label>
                    <Input
                      v-model="form.level"
                      type="number"
                      min="1"
                      class="w-full"
                    />
                    <p v-if="props.errors?.level" class="text-xs text-error mt-1">
                      {{ props.errors.level }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Expérience
                    </label>
                    <Input
                      v-model="form.experience"
                      type="number"
                      min="0"
                      class="w-full"
                    />
                    <p v-if="props.errors?.experience" class="text-xs text-error mt-1">
                      {{ props.errors.experience }}
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-bold text-base-content/80 mb-2">
                      Cash 💰
                    </label>
                    <Input
                      v-model="form.cash"
                      type="number"
                      min="0"
                      class="w-full"
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
                    {{ isSubmitting || form.processing ? 'Création...' : 'Créer l\'utilisateur' }}
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

          <div class="xl:col-span-3 xl:col-start-11">
            <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">💡</span>
                  AIDE
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="text-sm space-y-3">
                  <div>
                    <h4 class="font-semibold text-base-content mb-1">Rôles disponibles :</h4>
                    <ul class="text-base-content/70 space-y-1 text-xs">
                      <li>• <span class="text-error font-medium">Admin</span> : Accès complet</li>
                      <li>• <span class="text-info font-medium">Joueur</span> : Accès limité</li>
                    </ul>
                  </div>

                  <div>
                    <h4 class="font-semibold text-base-content mb-1">Statuts disponibles :</h4>
                    <ul class="text-base-content/70 space-y-1 text-xs">
                      <li>• <span class="text-success font-medium">Actif</span> : Peut se connecter</li>
                      <li>• <span class="text-error font-medium">Banni</span> : Accès refusé</li>
                    </ul>
                  </div>

                  <div class="pt-3 border-t border-base-300/30">
                    <p class="text-xs text-base-content/60">
                      ⚠️ Les champs marqués d'un * sont obligatoires
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-6 bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
                <h3 class="text-lg font-bold tracking-wider flex items-center gap-2">
                  <span class="text-xl">🔗</span>
                  NAVIGATION
                </h3>
              </div>
              <div class="p-6 space-y-3">
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
  </div>
</template>
