<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

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
</script>

<template>

  <Head title="Créer un utilisateur" />

  <div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Créer un utilisateur</h1>
            <Link href="/admin/users" class="text-gray-600 hover:text-gray-900 transition-colors">
              ← Retour à la liste
            </Link>
          </div>
        </div>
      </div>

      <!-- Form -->
      <div class="bg-white shadow rounded-lg">
        <form @submit.prevent="submit" class="p-6 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Username -->
            <div>
              <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                Nom d'utilisateur *
              </label>
              <input id="username" v-model="form.username" type="text" required
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                     :class="{ 'border-red-500': form.errors.username }" />
              <p v-if="form.errors.username" class="mt-1 text-sm text-red-600">
                {{ form.errors.username }}
              </p>
            </div>

            <!-- Email -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Email *
              </label>
              <input id="email" v-model="form.email" type="email" required
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                     :class="{ 'border-red-500': form.errors.email }" />
              <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                {{ form.errors.email }}
              </p>
            </div>

            <!-- Password -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Mot de passe *
              </label>
              <input id="password" v-model="form.password" type="password" required
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                     :class="{ 'border-red-500': form.errors.password }" />
              <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                {{ form.errors.password }}
              </p>
            </div>

            <!-- Role -->
            <div>
              <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                Rôle *
              </label>
              <select id="role" v-model="form.role" required
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      :class="{ 'border-red-500': form.errors.role }">
                <option v-for="role in roles" :key="role" :value="role">
                  {{ role }}
                </option>
              </select>
              <p v-if="form.errors.role" class="mt-1 text-sm text-red-600">
                {{ form.errors.role }}
              </p>
            </div>

            <!-- Status -->
            <div>
              <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                Statut *
              </label>
              <select id="status" v-model="form.status" required
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      :class="{ 'border-red-500': form.errors.status }">
                <option v-for="status in statuses" :key="status" :value="status">
                  {{ status }}
                </option>
              </select>
              <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                {{ form.errors.status }}
              </p>
            </div>

            <!-- Level -->
            <div>
              <label for="level" class="block text-sm font-medium text-gray-700 mb-2">
                Niveau
              </label>
              <input id="level" v-model.number="form.level" type="number" min="1"
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                     :class="{ 'border-red-500': form.errors.level }" />
              <p v-if="form.errors.level" class="mt-1 text-sm text-red-600">
                {{ form.errors.level }}
              </p>
            </div>

            <!-- Experience -->
            <div>
              <label for="experience" class="block text-sm font-medium text-gray-700 mb-2">
                Expérience
              </label>
              <input id="experience" v-model.number="form.experience" type="number" min="0"
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                     :class="{ 'border-red-500': form.errors.experience }" />
              <p v-if="form.errors.experience" class="mt-1 text-sm text-red-600">
                {{ form.errors.experience }}
              </p>
            </div>

            <!-- Cash -->
            <div>
              <label for="cash" class="block text-sm font-medium text-gray-700 mb-2">
                Cash
              </label>
              <input id="cash" v-model.number="form.cash" type="number" min="0"
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                     :class="{ 'border-red-500': form.errors.cash }" />
              <p v-if="form.errors.cash" class="mt-1 text-sm text-red-600">
                {{ form.errors.cash }}
              </p>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
            <Link href="/admin/users"
                  class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition-colors">
              Annuler
            </Link>
            <button type="submit" :disabled="form.processing"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50">
              {{ form.processing ? 'Création...' : 'Créer l\'utilisateur' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped></style>