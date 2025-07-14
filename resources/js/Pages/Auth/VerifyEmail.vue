<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, onUnmounted, reactive, ref } from 'vue';

interface Props {
  status?: string;
}

defineProps<Props>();

const form = reactive({
  processing: false,
  recentlySent: false,
});

const lastSentTime = ref<number>(0);
const remainingTime = ref<number>(0);
const intervalId = ref<number | null>(null);

const canResend = computed(() => remainingTime.value <= 0 && !form.processing);

const submit = () => {
  if (!canResend.value) return;

  form.processing = true;

  router.post('/email/verification-notification', {}, {
    onSuccess: () => {
      form.processing = false;
      form.recentlySent = true;
      startCountdown();
    },
    onError: () => {
      form.processing = false;
    },
  });
};

const startCountdown = () => {
  lastSentTime.value = Date.now();
  remainingTime.value = 30;

  if (intervalId.value) {
    clearInterval(intervalId.value);
  }

  intervalId.value = window.setInterval(() => {
    const elapsed = Math.floor((Date.now() - lastSentTime.value) / 1000);
    remainingTime.value = Math.max(30 - elapsed, 0);

    if (remainingTime.value <= 0 && intervalId.value) {
      clearInterval(intervalId.value);
      intervalId.value = null;
    }
  }, 1000);
};

const logout = () => {
  router.post('/logout');
};

onUnmounted(() => {
  if (intervalId.value) {
    clearInterval(intervalId.value);
  }
});
</script>

<template>

  <Head title="Vérification de l'email" />

  <div
    class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-lg w-full space-y-8">
      <div class="text-center">
        <Link href="/" class="text-3xl font-bold text-white hover:text-blue-300 transition-colors">
          🎮 CollectorsHub
        </Link>
        <div class="mt-6">
          <div
            class="bg-yellow-500/20 border border-yellow-500/50 rounded-full p-4 w-20 h-20 mx-auto flex items-center justify-center mb-4">
            <svg class="w-10 h-10 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
              </path>
            </svg>
          </div>
          <h2 class="text-2xl font-bold text-white">
            Vérifiez votre adresse email
          </h2>
          <p class="mt-2 text-sm text-gray-400">
            Votre aventure Pokémon vous attend ! ✨
          </p>
        </div>
      </div>

      <div v-if="status === 'verification-link-sent' || form.recentlySent"
           class="bg-green-900/50 border border-green-500 text-green-300 px-4 py-3 rounded-md text-sm text-center">
        <div class="flex items-center justify-center mb-2">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
          Email de vérification envoyé !
        </div>
        Un nouveau lien de vérification a été envoyé à votre adresse email.
      </div>

      <div class="bg-white/5 backdrop-blur-sm rounded-xl p-8 border border-white/10">
        <div class="text-center space-y-6">
          <div class="flex justify-center">
            <div class="relative">
              <div
                class="w-16 h-16 bg-gradient-to-b from-red-500 to-red-600 rounded-full border-4 border-gray-800 relative">
                <div class="absolute top-1/2 left-0 right-0 h-1 bg-gray-800 transform -translate-y-1/2">
                </div>
                <div
                  class="absolute top-1/2 left-1/2 w-4 h-4 bg-white rounded-full border-2 border-gray-800 transform -translate-x-1/2 -translate-y-1/2">
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-8 bg-gray-200 rounded-b-full"></div>
              </div>
              <div
                class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 rounded-full border-2 border-gray-800 animate-pulse">
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <h3 class="text-xl font-semibold text-white">
              Presque prêt à capturer !
            </h3>
            <p class="text-gray-300 leading-relaxed">
              Nous avons envoyé un email de vérification à votre adresse.
              Cliquez sur le lien dans l'email pour activer votre compte et commencer votre collection
              Pokémon.
            </p>
            <div class="bg-blue-900/30 border border-blue-500/50 rounded-lg p-4">
              <p class="text-blue-300 text-sm">
                💡 <strong>Astuce :</strong> Vérifiez vos spams si vous ne trouvez pas l'email !
              </p>
            </div>
          </div>

          <div class="space-y-4">
            <button @click="submit" :disabled="!canResend" :class="[
              'w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md transition-all duration-200',
              canResend
                ? 'text-white bg-green-600 hover:bg-green-700 focus:ring-2 focus:ring-green-500 hover:shadow-lg hover:scale-105'
                : 'text-gray-400 bg-gray-600 cursor-not-allowed'
            ]">
              <span v-if="form.processing" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                          stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                  </path>
                </svg>
                Envoi en cours...
              </span>
              <span v-else-if="remainingTime > 0" class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Renvoyer dans {{ remainingTime }}s
              </span>
              <span v-else class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                  </path>
                </svg>
                Renvoyer l'email de vérification
              </span>
            </button>

            <div class="text-xs text-gray-500 text-center">
              Vous pouvez renvoyer un email toutes les 30 secondes
            </div>
          </div>

          <div class="pt-4 border-t border-gray-700">
            <button @click="logout"
                    class="text-sm text-gray-400 hover:text-white transition-colors flex items-center justify-center mx-auto">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                </path>
              </svg>
              Se déconnecter
            </button>
          </div>
        </div>
      </div>

      <div class="text-center space-y-4">
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

        <Link href="/"
              class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Retour à l'accueil
        </Link>
      </div>
    </div>
  </div>
</template>