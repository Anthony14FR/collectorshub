<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';

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

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen flex items-center justify-center py-8 px-4">
      <div class="w-full max-w-md">
        <div class="text-center mb-8">
          <Link href="/" class="text-3xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent hover:from-secondary hover:to-primary transition-all duration-300 tracking-wider">
            🎮 CollectorsHub
          </Link>
          <div class="mt-6">
            <div class="bg-success/20 border border-success/50 rounded-full p-4 w-20 h-20 mx-auto flex items-center justify-center mb-4">
              <span class="text-4xl">🔐</span>
            </div>
            <h2 class="text-2xl font-bold text-base-content tracking-wider">
              NOUVEAU MOT DE PASSE
            </h2>
            <p class="mt-2 text-sm text-base-content/70">
              Choisissez un mot de passe sécurisé ! 🔒
            </p>
          </div>
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-success/10 to-success/5 border-b border-success/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">✅</span>
              RÉINITIALISATION RÉUSSIE
            </h3>
          </div>

          <form @submit.prevent="submit" class="p-6 space-y-6">
            <div class="text-center space-y-4">
              <div>
                <h4 class="text-lg font-semibold text-base-content mb-2">
                  Réinitialisation réussie !
                </h4>
                <p class="text-base-content/80 text-sm leading-relaxed">
                  Votre lien de récupération est valide. Entrez votre nouveau mot de passe ci-dessous.
                </p>
                <div class="mt-3 text-xs text-base-content/60">
                  <span class="font-medium">Compte :</span> {{ email }}
                </div>
              </div>
            </div>

            <input type="hidden" v-model="form.email" />
            <input type="hidden" v-model="form.token" />

            <div class="space-y-4">
              <div>
                <Input
                  id="password"
                  ref="passwordInput"
                  v-model="form.password"
                  type="password"
                  required
                  label="Nouveau mot de passe"
                  placeholder="••••••••"
                  variant="default"
                  size="md"
                  :state="form.errors.password ? 'error' : 'default'"
                />
                <div v-if="form.errors.password" class="mt-2 text-sm text-error">
                  {{ form.errors.password }}
                </div>
              </div>

              <div>
                <Input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  type="password"
                  required
                  label="Confirmer le mot de passe"
                  placeholder="••••••••"
                  variant="default"
                  size="md"
                  :state="form.errors.password_confirmation ? 'error' : 'default'"
                />
                <div v-if="form.errors.password_confirmation" class="mt-2 text-sm text-error">
                  {{ form.errors.password_confirmation }}
                </div>
              </div>

              <div class="bg-info/10 border border-info/30 rounded-lg p-4">
                <p class="text-info text-sm">
                  💡 <strong>Exigences :</strong> Au moins 8 caractères avec lettres et chiffres recommandés.
                </p>
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
                Sauvegarde...
              </span>
              <span v-else class="flex items-center justify-center">
                <span class="text-lg mr-2">💾</span>
                Sauvegarder le nouveau mot de passe
              </span>
            </Button>

            <div class="bg-warning/10 border border-warning/30 rounded-lg p-4">
              <div class="flex items-start">
                <span class="text-warning mr-2 mt-0.5 text-lg">⚠️</span>
                <div>
                  <p class="text-warning text-sm font-medium">Sécurité</p>
                  <p class="text-warning/80 text-xs mt-1">
                    Après la sauvegarde, vous serez redirigé vers la page de connexion. Ce lien ne sera plus valide.
                  </p>
                </div>
              </div>
            </div>
          </form>
        </div>

        <div class="mt-6">
          <div class="bg-base-100/40 backdrop-blur-sm rounded-lg p-4 border border-base-300/30">
            <h4 class="text-base-content font-medium mb-2 tracking-wider">Problème avec le lien ?</h4>
            <p class="text-sm text-base-content/70 mb-3">
              Si ce lien a expiré, vous pouvez en demander un nouveau.
            </p>
            <Link href="/forgot-password" class="inline-flex items-center text-sm text-primary hover:text-secondary transition-colors">
              <span class="text-lg mr-2">📧</span>
              Demander un nouveau lien
            </Link>
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