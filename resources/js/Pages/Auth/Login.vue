<template>
  <div class="min-h-screen relative overflow-hidden">
    <BackgroundEffects />
    <Head title="Connexion" />

    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="w-full max-w-md">
        <!-- Logo / Title -->
        <div class="text-center mb-8">
          <div class="text-6xl mb-4">🎮</div>
          <h1 class="text-4xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
            PokéArena
          </h1>
          <p class="text-base-content/70 mt-2">Connectez-vous à votre compte</p>
        </div>

        <!-- Status Message -->
        <div v-if="status" class="mb-4">
          <Alert type="success" :message="status" />
        </div>

        <!-- Login Form -->
        <div class="bg-base-100/60 backdrop-blur-sm rounded-2xl border border-base-300/30 p-8 shadow-2xl">
          <form @submit.prevent="submit" class="space-y-6">
            <div>
              <label class="text-sm font-medium text-base-content/90 mb-2 block">
                <span class="inline-flex items-center gap-2">
                  <span>📧</span>
                  Email
                </span>
              </label>
              <Input
                v-model="form.email"
                type="email"
                placeholder="trainer@pokemon.com"
                :error="form.errors.email"
                required
                autofocus
                autocomplete="username"
              />
            </div>

            <div>
              <label class="text-sm font-medium text-base-content/90 mb-2 block">
                <span class="inline-flex items-center gap-2">
                  <span>🔒</span>
                  Mot de passe
                </span>
              </label>
              <Input
                v-model="form.password"
                type="password"
                placeholder="••••••••"
                :error="form.errors.password"
                required
                autocomplete="current-password"
              />
            </div>

            <div class="flex items-center justify-between">
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="form.remember"
                  type="checkbox"
                  class="checkbox checkbox-primary checkbox-sm"
                />
                <span class="text-sm text-base-content/70">Se souvenir de moi</span>
              </label>

              <Link
                :href="route('password.request')"
                class="text-sm text-primary hover:text-primary-focus transition-colors"
              >
                Mot de passe oublié ?
              </Link>
            </div>

            <Button
              type="submit"
              variant="primary"
              size="lg"
              class="w-full"
              :disabled="form.processing"
            >
              <span v-if="!form.processing" class="flex items-center justify-center gap-2">
                <span>🚀</span>
                Se connecter
              </span>
              <span v-else class="flex items-center justify-center gap-2">
                <Spinner size="sm" />
                Connexion...
              </span>
            </Button>
          </form>

          <div class="relative my-8">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-base-300/50"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-4 bg-base-100/60 text-base-content/70">Ou</span>
            </div>
          </div>

          <div class="text-center">
            <p class="text-sm text-base-content/70">
              Pas encore de compte ?
              <Link
                :href="route('register')"
                class="text-primary hover:text-primary-focus font-medium transition-colors"
              >
                Créer un compte
              </Link>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Alert from '@/Components/UI/Alert.vue';
import Spinner from '@/Components/UI/Spinner.vue';

defineProps<{
  canResetPassword?: boolean;
  status?: string;
}>();

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => {
      form.reset('password');
    },
  });
};
</script>