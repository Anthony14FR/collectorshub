<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

interface Props {
  status?: string;
}

defineProps<Props>();

const form = reactive({
  login: '',
  processing: false,
  errors: {} as Record<string, string>,
});

const loginInput = ref<HTMLInputElement>();

const submit = () => {
  form.processing = true;
  form.errors = {};

  router.post('/forgot-password', {
    login: form.login,
  }, {
    onFinish: () => {
      form.processing = false;
    },
    onError: (errors: Record<string, string>) => {
      form.processing = false;
      form.errors = errors;
      if (errors.login) {
        loginInput.value?.focus();
      }
    },
  });
};
</script>

<template>

  <Head title="Mot de passe oublié" />

  <div
    class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div class="text-center">
        <Link href="/" class="text-3xl font-bold text-white hover:text-blue-300 transition-colors">
          🎮 CollectorsHub
        </Link>
        <div class="mt-6">
          <div
            class="bg-blue-500/20 border border-blue-500/50 rounded-full p-4 w-20 h-20 mx-auto flex items-center justify-center mb-4">
            <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 7a2 2 0 012 2m0 0a2 2 0 01-2 2m2-2h.01M9 9h.01M9 15h.01m4.5 0a2.5 2.5 0 01-2.5 2.5m0 0a2.5 2.5 0 01-2.5-2.5m2.5 0V9m0 0a2.5 2.5 0 012.5-2.5m0 0V5.5a1.5 1.5 0 011.5-1.5m0 0h3m-3 0h-.01M9 5.5v3m0 0h.01M9 8.5H6.5A1.5 1.5 0 015 7V5.5m0 0v3m0-3h3m3 0h.01">
              </path>
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-white">
            Mot de passe oublié ?
          </h2>
          <p class="mt-2 text-sm text-gray-400">
            Pas de souci, nous allons vous aider ! 🔑
          </p>
        </div>
      </div>

      <div v-if="status"
           class="bg-green-900/50 border border-green-500 text-green-300 px-4 py-3 rounded-md text-sm text-center">
        <div class="flex items-center justify-center mb-2">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
          Email envoyé !
        </div>
        Nous avons envoyé un lien de réinitialisation à votre adresse email.
      </div>

      <form @submit.prevent="submit"
            class="mt-8 space-y-6 bg-white/5 backdrop-blur-sm rounded-xl p-8 border border-white/10">
        <div class="space-y-4">
          <div class="text-center space-y-4">
            <div class="flex justify-center">
              <div class="relative">
                <div
                  class="w-12 h-12 bg-gradient-to-b from-yellow-500 to-yellow-600 rounded-full border-4 border-gray-800 relative">
                  <div
                    class="absolute top-1/2 left-0 right-0 h-1 bg-gray-800 transform -translate-y-1/2">
                  </div>
                  <div
                    class="absolute top-1/2 left-1/2 w-3 h-3 bg-white rounded-full border-2 border-gray-800 transform -translate-x-1/2 -translate-y-1/2">
                  </div>
                  <div class="absolute bottom-0 left-0 right-0 h-6 bg-gray-200 rounded-b-full"></div>
                </div>
                <div
                  class="absolute -top-1 -right-1 w-3 h-3 bg-red-400 rounded-full border-2 border-gray-800 animate-pulse">
                </div>
              </div>
            </div>

            <div>
              <h3 class="text-lg font-semibold text-white mb-2">
                Récupération de compte
              </h3>
              <p class="text-gray-300 text-sm leading-relaxed">
                Entrez votre nom d'utilisateur ou votre email et nous vous enverrons un lien pour
                réinitialiser votre mot de passe.
              </p>
            </div>
          </div>

          <div>
            <label for="login" class="block text-sm font-medium text-gray-300 mb-2">
              Nom d'utilisateur ou email
            </label>
            <input id="login" ref="loginInput" v-model="form.login" type="text" required
                   autocomplete="username"
                   class="w-full px-3 py-3 bg-gray-800/50 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                   placeholder="votre@email.com ou votre_pseudo" />
            <div v-if="form.errors.login" class="mt-2 text-sm text-red-400">
              {{ form.errors.login }}
            </div>
          </div>
        </div>

        <div>
          <button type="submit" :disabled="form.processing"
                  class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 hover:shadow-lg hover:scale-105">
            <span v-if="form.processing" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                   fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
              </svg>
              Envoi en cours...
            </span>
            <span v-else class="flex items-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                </path>
              </svg>
              Envoyer le lien de récupération
            </span>
          </button>
        </div>

        <div class="text-center pt-4 border-t border-gray-700">
          <Link href="/login"
                class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour à la connexion
          </Link>
        </div>
      </form>

      <div class="text-center">
        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
          <h4 class="text-white font-medium mb-2">Besoin d'aide ?</h4>
          <p class="text-sm text-gray-400 mb-3">
            Si vous rencontrez des problèmes, contactez notre équipe support.
          </p>
          <a href="mailto:contact@collectorshub.fr"
             class="inline-flex items-center text-sm text-blue-400 hover:text-blue-300 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
              </path>
            </svg>
            contact@collectorshub.fr
          </a>
        </div>
      </div>
    </div>
  </div>
</template>