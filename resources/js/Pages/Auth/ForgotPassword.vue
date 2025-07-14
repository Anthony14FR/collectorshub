<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Alert from '@/Components/UI/Alert.vue';

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

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen flex items-center justify-center py-8 px-4">
      <div class="w-full max-w-md">
        <div class="text-center mb-8">
          <Link href="/" class="text-3xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent hover:from-secondary hover:to-primary transition-all duration-300 tracking-wider">
            🎮 CollectorsHub
          </Link>
          <div class="mt-6">
            <div class="bg-secondary/20 border border-secondary/50 rounded-full p-4 w-20 h-20 mx-auto flex items-center justify-center mb-4">
              <span class="text-4xl">🔑</span>
            </div>
            <h2 class="text-2xl font-bold text-base-content tracking-wider">
              MOT DE PASSE OUBLIÉ
            </h2>
            <p class="mt-2 text-sm text-base-content/70">
              Pas de souci, nous allons vous aider ! 🔐
            </p>
          </div>
        </div>

        <div v-if="status" class="mb-6">
          <Alert 
            type="success" 
            title="Email envoyé !"
            message="Nous avons envoyé un lien de réinitialisation à votre adresse email."
          />
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-secondary/10 to-secondary/5 border-b border-secondary/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">🔓</span>
              RÉCUPÉRATION DE COMPTE
            </h3>
          </div>

          <form @submit.prevent="submit" class="p-6 space-y-6">
            <div class="text-center space-y-4">
              <div>
                <h4 class="text-lg font-semibold text-base-content mb-2">
                  Récupération de compte
                </h4>
                <p class="text-base-content/80 text-sm leading-relaxed">
                  Entrez votre nom d'utilisateur ou votre email et nous vous enverrons un lien pour
                  réinitialiser votre mot de passe.
                </p>
              </div>
            </div>

            <div>
              <Input
                id="login"
                ref="loginInput"
                v-model="form.login"
                type="text"
                required
                label="Nom d'utilisateur ou email"
                placeholder="votre@email.com ou votre_pseudo"
                variant="default"
                size="md"
                :state="form.errors.login ? 'error' : 'default'"
              />
              <div v-if="form.errors.login" class="mt-2 text-sm text-error">
                {{ form.errors.login }}
              </div>
            </div>

            <Button
              type="submit"
              :disabled="form.processing"
              variant="primary"
              size="md"
              class="w-full"
            >
              <span v-if="form.processing" class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Envoi en cours...
              </span>
              <span v-else class="flex items-center justify-center">
                <span class="text-lg mr-2">📧</span>
                Envoyer le lien de récupération
              </span>
            </Button>

            <div class="text-center pt-4 border-t border-base-300/50">
              <Link href="/login" class="text-sm text-base-content/70 hover:text-base-content transition-colors inline-flex items-center">
                <span class="text-lg mr-2">←</span>
                Retour à la connexion
              </Link>
            </div>
          </form>
        </div>

        <div class="mt-6">
          <div class="bg-base-100/40 backdrop-blur-sm rounded-lg p-4 border border-base-300/30">
            <h4 class="text-base-content font-medium mb-2 tracking-wider">Besoin d'aide ?</h4>
            <p class="text-sm text-base-content/70 mb-3">
              Si vous rencontrez des problèmes, contactez notre équipe support.
            </p>
            <a
              href="mailto:contact@collectorshub.fr"
              class="inline-flex items-center text-sm text-primary hover:text-secondary transition-colors"
            >
              <span class="text-lg mr-2">📧</span>
              contact@collectorshub.fr
            </a>
          </div>
        </div>

        <div class="text-center mt-4">
          <Link href="/" class="text-sm text-base-content/70 hover:text-base-content transition-colors">
            ← Retour à l'accueil
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>