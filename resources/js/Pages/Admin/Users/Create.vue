<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Select from '@/Components/UI/Select.vue';

defineProps<{
  roles: string[];
  statuses: string[];
}>();

const form = reactive({
  username: '',
  email: '',
  password: '',
  level: 1,
  experience: 0,
  cash: 0,
  role: 'user',
  status: 'active',
  processing: false,
  errors: {} as Record<string, string>,
});

const submit = () => {
  form.processing = true;
  form.errors = {};

  router.post('/admin/users', {
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
  form.username = '';
  form.email = '';
  form.password = '';
  form.level = 1;
  form.experience = 0;
  form.cash = 0;
  form.role = 'user';
  form.status = 'active';
  form.errors = {};
};
</script>

<template>
  <Head title="Créer un utilisateur" />

  <div class="h-screen w-screen overflow-hidden bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 h-screen w-screen overflow-hidden">
      <div class="flex justify-center pt-4 mb-4">
        <div class="text-center">
          <h1 class="text-2xl font-bold bg-gradient-to-r from-success to-success/80 bg-clip-text text-transparent mb-1 tracking-wider">
            ➕ CRÉER UN UTILISATEUR
          </h1>
          <p class="text-xs text-base-content/70 uppercase tracking-wider">
            Nouvel utilisateur de la plateforme
          </p>
        </div>
      </div>

      <div class="absolute left-8 top-20 w-64">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden mb-4">
          <div class="p-3 bg-gradient-to-r from-info/10 to-info/5 border-b border-info/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">ℹ️</span>
              AIDE
            </h3>
          </div>
          <div class="p-3 text-xs text-base-content/70 space-y-2">
            <p>• Username : unique, 3-20 caractères</p>
            <p>• Email : format valide requis</p>
            <p>• Password : minimum 8 caractères</p>
            <p>• Level : défaut 1, max 100</p>
            <p>• Cash : montant initial en jeu</p>
          </div>
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">🔄</span>
              ACTIONS
            </h3>
          </div>
          <div class="p-3 space-y-2">
            <Button
              @click="resetForm"
              variant="outline"
              size="sm"
              class="w-full"
              :disabled="form.processing"
            >
              🗑️ Vider le formulaire
            </Button>
            <div class="border-t border-base-300/30 pt-2">
              <Button
                @click="router.visit('/admin/users')"
                variant="ghost"
                size="sm"
                class="w-full"
              >
                ← Liste utilisateurs
              </Button>
              <Button
                @click="router.visit('/admin')"
                variant="ghost"
                size="sm"
                class="w-full mt-1"
              >
                🏠 Dashboard
              </Button>
            </div>
          </div>
        </div>
      </div>

      <div class="absolute right-8 top-20 w-64">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">📋</span>
              APERÇU
            </h3>
          </div>
          <div class="p-3 space-y-3">
            <div class="text-center">
              <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-xl font-bold mb-2">
                {{ form.username ? form.username.charAt(0).toUpperCase() : '?' }}
              </div>
              <div class="text-sm font-semibold">{{ form.username || 'Nouveau utilisateur' }}</div>
              <div class="text-xs text-base-content/60">{{ form.email || 'email@example.com' }}</div>
            </div>
            <div class="space-y-2 text-xs">
              <div class="flex justify-between">
                <span class="text-base-content/70">Rôle:</span>
                <span class="font-semibold">{{ form.role }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-base-content/70">Statut:</span>
                <span class="font-semibold">{{ form.status }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-base-content/70">Niveau:</span>
                <span class="font-semibold">{{ form.level }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-base-content/70">Cash:</span>
                <span class="font-semibold text-success">{{ form.cash }} 💰</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="absolute top-20 left-1/2 -translate-x-1/2 w-[700px] h-[700px]">
        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden h-full flex flex-col">
          <div class="shrink-0 p-3 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">📝</span>
              FORMULAIRE DE CRÉATION
            </h3>
          </div>

          <form @submit.prevent="submit" class="flex-1 overflow-y-auto p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              
              <div>
                <label for="username" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                  Nom d'utilisateur <span class="text-error">*</span>
                </label>
                <Input
                  id="username"
                  v-model="form.username"
                  type="text"
                  required
                  placeholder="johndoe"
                  :class="{ 'border-error': form.errors.username }"
                />
                <p v-if="form.errors.username" class="mt-1 text-sm text-error">
                  {{ form.errors.username }}
                </p>
              </div>

              <div>
                <label for="email" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                  Email <span class="text-error">*</span>
                </label>
                <Input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  placeholder="john@example.com"
                  :class="{ 'border-error': form.errors.email }"
                />
                <p v-if="form.errors.email" class="mt-1 text-sm text-error">
                  {{ form.errors.email }}
                </p>
              </div>

              <div class="md:col-span-2">
                <label for="password" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                  Mot de passe <span class="text-error">*</span>
                </label>
                <Input
                  id="password"
                  v-model="form.password"
                  type="password"
                  required
                  placeholder="Mot de passe sécurisé (min. 8 caractères)"
                  :class="{ 'border-error': form.errors.password }"
                />
                <p v-if="form.errors.password" class="mt-1 text-sm text-error">
                  {{ form.errors.password }}
                </p>
              </div>

              <div>
                <label for="role" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                  Rôle <span class="text-error">*</span>
                </label>
                <Select
                  id="role"
                  v-model="form.role"
                  :options="roles.map(role => ({ value: role, label: role }))"
                  :class="{ 'border-error': form.errors.role }"
                />
                <p v-if="form.errors.role" class="mt-1 text-sm text-error">
                  {{ form.errors.role }}
                </p>
              </div>

              <div>
                <label for="status" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                  Statut <span class="text-error">*</span>
                </label>
                <Select
                  id="status"
                  v-model="form.status"
                  :options="statuses.map(status => ({ value: status, label: status }))"
                  :class="{ 'border-error': form.errors.status }"
                />
                <p v-if="form.errors.status" class="mt-1 text-sm text-error">
                  {{ form.errors.status }}
                </p>
              </div>

              <div>
                <label for="level" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                  Niveau initial
                </label>
                <Input
                  id="level"
                  v-model.number="form.level"
                  type="number"
                  min="1"
                  max="100"
                  placeholder="1"
                  :class="{ 'border-error': form.errors.level }"
                />
                <p v-if="form.errors.level" class="mt-1 text-sm text-error">
                  {{ form.errors.level }}
                </p>
              </div>

              <div>
                <label for="experience" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                  Expérience initiale
                </label>
                <Input
                  id="experience"
                  v-model.number="form.experience"
                  type="number"
                  min="0"
                  placeholder="0"
                  :class="{ 'border-error': form.errors.experience }"
                />
                <p v-if="form.errors.experience" class="mt-1 text-sm text-error">
                  {{ form.errors.experience }}
                </p>
              </div>

              <div>
                <label for="cash" class="block text-sm font-bold text-base-content/70 mb-2 uppercase tracking-wider">
                  Cash initial 💰
                </label>
                <Input
                  id="cash"
                  v-model.number="form.cash"
                  type="number"
                  min="0"
                  placeholder="1000"
                  :class="{ 'border-error': form.errors.cash }"
                />
                <p v-if="form.errors.cash" class="mt-1 text-sm text-error">
                  {{ form.errors.cash }}
                </p>
              </div>
            </div>
          </form>

          <div class="shrink-0 bg-gradient-to-r from-success/10 to-success/5 px-6 py-4 border-t border-success/20">
            <div class="flex items-center justify-between">
              <div class="text-xs text-base-content/70">
                Les champs marqués d'un <span class="text-error">*</span> sont obligatoires
              </div>
              <div class="flex items-center gap-3">
                <Button
                  @click="router.visit('/admin/users')"
                  variant="ghost"
                  size="md"
                  :disabled="form.processing"
                >
                  Annuler
                </Button>
                <Button
                  @click="submit"
                  variant="primary"
                  size="md"
                  :disabled="form.processing || !form.username || !form.email || !form.password"
                >
                  {{ form.processing ? '🔄 Création...' : '✅ Créer l\'utilisateur' }}
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>