<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

interface Props {
  token: string;
  email: string;
}

const { token, email } = defineProps<Props>();

const form = reactive({
  token: token,
  email: email,
  password: '',
  password_confirmation: '',
  processing: false,
  errors: {} as Record<string, string>,
});

const passwordInput = ref<HTMLInputElement>();

const submit = () => {
  form.processing = true;
  form.errors = {};

  router.post('/reset-password', {
    token: form.token,
    email: form.email,
    password: form.password,
    password_confirmation: form.password_confirmation,
  }, {
    onFinish: () => {
      form.processing = false;
      form.password = '';
      form.password_confirmation = '';
    },
    onError: (errors: Record<string, string>) => {
      form.processing = false;
      form.errors = errors;
      if (errors.password) {
        passwordInput.value?.focus();
      }
    },
  });
};
</script>

<template>

  <Head title="Nouveau mot de passe" />

  <div
    class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div class="text-center">
        <Link href="/" class="text-3xl font-bold text-white hover:text-blue-300 transition-colors">
          🎮 CollectorsHub
        </Link>
        <div class="mt-6">
          <div
            class="bg-green-500/20 border border-green-500/50 rounded-full p-4 w-20 h-20 mx-auto flex items-center justify-center mb-4">
            <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 7a2 2 0 012 2m0 0a2 2 0 01-2 2m2-2h.01M9 9h.01M9 15h.01m4.5 0a2.5 2.5 0 01-2.5 2.5m0 0a2.5 2.5 0 01-2.5-2.5m2.5 0V9m0 0a2.5 2.5 0 012.5-2.5m0 0V5.5a1.5 1.5 0 011.5-1.5m0 0h3m-3 0h-.01M9 5.5v3m0 0h.01M9 8.5H6.5A1.5 1.5 0 015 7V5.5m0 0v3m0-3h3m3 0h.01">
              </path>
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-white">
            Nouveau mot de passe
          </h2>
          <p class="mt-2 text-sm text-gray-400">
            Choisissez un mot de passe sécurisé ! 🔒
          </p>
        </div>
      </div>

      <form @submit.prevent="submit"
            class="mt-8 space-y-6 bg-white/5 backdrop-blur-sm rounded-xl p-8 border border-white/10">
        <div class="space-y-4">
          <div class="text-center space-y-4">
            <div class="flex justify-center">
              <div class="relative">
                <div
                  class="w-12 h-12 bg-gradient-to-b from-green-500 to-green-600 rounded-full border-4 border-gray-800 relative">
                  <div
                    class="absolute top-1/2 left-0 right-0 h-1 bg-gray-800 transform -translate-y-1/2">
                  </div>
                  <div
                    class="absolute top-1/2 left-1/2 w-3 h-3 bg-white rounded-full border-2 border-gray-800 transform -translate-x-1/2 -translate-y-1/2">
                  </div>
                  <div class="absolute bottom-0 left-0 right-0 h-6 bg-gray-200 rounded-b-full"></div>
                </div>
                <div
                  class="absolute -top-1 -right-1 w-3 h-3 bg-yellow-400 rounded-full border-2 border-gray-800 animate-pulse">
                </div>
              </div>
            </div>

            <div>
              <h3 class="text-lg font-semibold text-white mb-2">
                Réinitialisation réussie !
              </h3>
              <p class="text-gray-300 text-sm leading-relaxed">
                Votre lien de récupération est valide. Entrez votre nouveau mot de passe ci-dessous.
              </p>
              <div class="mt-3 text-xs text-gray-400">
                <span class="font-medium">Compte :</span> {{ email }}
              </div>
            </div>
          </div>

          <input type="hidden" v-model="form.email" />
          <input type="hidden" v-model="form.token" />

          <div>
            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
              Nouveau mot de passe
            </label>
            <input id="password" ref="passwordInput" v-model="form.password" type="password" required
                   autocomplete="new-password"
                   class="w-full px-3 py-3 bg-gray-800/50 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                   placeholder="••••••••" />
            <div v-if="form.errors.password" class="mt-2 text-sm text-red-400">
              {{ form.errors.password }}
            </div>
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
              Confirmer le mot de passe
            </label>
            <input id="password_confirmation" v-model="form.password_confirmation" type="password" required
                   autocomplete="new-password"
                   class="w-full px-3 py-3 bg-gray-800/50 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                   placeholder="••••••••" />
            <div v-if="form.errors.password_confirmation" class="mt-2 text-sm text-red-400">
              {{ form.errors.password_confirmation }}
            </div>
          </div>

          <div class="bg-blue-900/30 border border-blue-500/50 rounded-lg p-4">
            <p class="text-blue-300 text-sm">
              💡 <strong>Exigences :</strong> Au moins 8 caractères avec lettres et chiffres recommandés.
            </p>
          </div>
        </div>

        <div>
          <button type="submit" :disabled="form.processing"
                  class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 hover:shadow-lg hover:scale-105">
            <span v-if="form.processing" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                   fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
              </svg>
              Sauvegarde...
            </span>
            <span v-else class="flex items-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 13l4 4L19 7"></path>
              </svg>
              Sauvegarder le nouveau mot de passe
            </span>
          </button>
        </div>

        <div class="bg-yellow-900/30 border border-yellow-500/50 rounded-lg p-4">
          <div class="flex items-start">
            <svg class="w-5 h-5 text-yellow-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z">
              </path>
            </svg>
            <div>
              <p class="text-yellow-300 text-sm font-medium">Sécurité</p>
              <p class="text-yellow-200 text-xs mt-1">
                Après la sauvegarde, vous serez redirigé vers la page de connexion. Ce lien ne sera plus
                valide.
              </p>
            </div>
          </div>
        </div>
      </form>

      <div class="text-center">
        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
          <h4 class="text-white font-medium mb-2">Problème avec le lien ?</h4>
          <p class="text-sm text-gray-400 mb-3">
            Si ce lien a expiré, vous pouvez en demander un nouveau.
          </p>
          <Link href="/forgot-password"
                class="inline-flex items-center text-sm text-blue-400 hover:text-blue-300 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
              </path>
            </svg>
            Demander un nouveau lien
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>