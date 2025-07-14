<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { nextTick, reactive, ref } from 'vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Alert from '@/Components/UI/Alert.vue';

interface Props {
  canResetPassword?: boolean;
  status?: string;
}

defineProps<Props>();

const form = reactive({
  login: '',
  password: '',
  remember: false,
  processing: false,
  errors: {} as Record<string, string>,
});

const passwordInput = ref<HTMLInputElement>();

const submit = () => {
  form.processing = true;
  form.errors = {};

  router.post('/login', {
    login: form.login,
    password: form.password,
  }, {
    onFinish: () => {
      form.processing = false;
      form.password = '';
    },
    onError: (errors: Record<string, string>) => {
      form.processing = false;
      form.errors = errors;
      if (errors.password) {
        form.password = '';
        nextTick(() => passwordInput.value?.focus());
      }
    },
  });
};
</script>

<template>
  <Head title="Connexion" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen flex items-center justify-center py-8 px-4">
      <div class="w-full max-w-md">
        <div class="text-center mb-8">
          <Link href="/" class="text-3xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent hover:from-secondary hover:to-primary transition-all duration-300 tracking-wider">
            🎮 CollectorsHub
          </Link>
          <h2 class="mt-6 text-2xl font-bold text-base-content tracking-wider">
            CONNEXION
          </h2>
          <p class="mt-2 text-sm text-base-content/70">
            Ou
            <Link href="/register" class="font-medium text-primary hover:text-secondary transition-colors">
              créez un nouveau compte
            </Link>
          </p>
        </div>

        <div v-if="status" class="mb-6">
          <Alert type="success" :message="status" />
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-primary/10 to-primary/5 border-b border-primary/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <span class="text-lg">🔐</span>
              AUTHENTIFICATION
            </h3>
          </div>

          <form @submit.prevent="submit" class="p-6 space-y-6">
            <div class="space-y-4">
              <div>
                <Input
                  id="login"
                  v-model="form.login"
                  type="text"
                  required
                  label="Pseudo ou email"
                  placeholder="votre@email.com"
                  variant="default"
                  size="md"
                  :state="form.errors.login ? 'error' : 'default'"
                />
                <div v-if="form.errors.login" class="mt-2 text-sm text-error">
                  {{ form.errors.login }}
                </div>
              </div>

              <div>
                <Input
                  id="password"
                  ref="passwordInput"
                  v-model="form.password"
                  type="password"
                  required
                  label="Mot de passe"
                  placeholder="••••••••"
                  variant="default"
                  size="md"
                  :state="form.errors.password ? 'error' : 'default'"
                />
                <div v-if="form.errors.password" class="mt-2 text-sm text-error">
                  {{ form.errors.password }}
                </div>
              </div>

              <div v-if="canResetPassword" class="text-right">
                <Link href="/forgot-password" class="text-sm font-medium text-primary hover:text-secondary transition-colors">
                  Mot de passe oublié ?
                </Link>
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
                Connexion...
              </span>
              <span v-else>🚀 Se connecter</span>
            </Button>
          </form>
        </div>

        <div class="text-center mt-6">
          <Link href="/" class="text-sm text-base-content/70 hover:text-base-content transition-colors">
            ← Retour à l'accueil
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>