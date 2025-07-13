<template>
    <div class="min-h-screen relative overflow-hidden">
      <BackgroundEffects />
      <Head title="Confirmer le mot de passe" />
  
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="w-full max-w-md">
          <!-- Logo / Title -->
          <div class="text-center mb-8">
            <div class="text-6xl mb-4">🛡️</div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
              Confirmation requise
            </h1>
            <p class="text-base-content/70 mt-2">
              Cette action nécessite votre mot de passe
            </p>
          </div>
  
          <!-- Confirmation Card -->
          <div class="bg-base-100/60 backdrop-blur-sm rounded-2xl border border-base-300/30 p-8 shadow-2xl">
            <!-- Info Message -->
            <div class="mb-6">
              <div class="bg-gradient-to-br from-warning/10 to-warning/5 rounded-xl p-4">
                <div class="flex items-start gap-3">
                  <span class="text-2xl">⚠️</span>
                  <div>
                    <h3 class="font-bold text-sm mb-1">Zone sécurisée</h3>
                    <p class="text-xs text-base-content/70">
                      Pour votre sécurité, nous devons vérifier votre identité avant de continuer.
                    </p>
                  </div>
                </div>
              </div>
            </div>
  
            <form @submit.prevent="submit" class="space-y-6">
              <div>
                <label class="text-sm font-medium text-base-content/90 mb-2 block">
                  <span class="inline-flex items-center gap-2">
                    <span>🔒</span>
                    Mot de passe actuel
                  </span>
                </label>
                <Input
                  v-model="form.password"
                  type="password"
                  placeholder="••••••••"
                  :error="form.errors.password"
                  required
                  autofocus
                  autocomplete="current-password"
                />
              </div>
  
              <Button
                type="submit"
                variant="primary"
                size="lg"
                class="w-full"
                :disabled="form.processing"
              >
                <span v-if="!form.processing" class="flex items-center justify-center gap-2">
                  <span>✅</span>
                  Confirmer
                </span>
                <span v-else class="flex items-center justify-center gap-2">
                  <Spinner size="sm" />
                  Vérification...
                </span>
              </Button>
            </form>
  
            <div class="mt-6 text-center">
              <Link
                :href="route('login')"
                class="inline-flex items-center gap-2 text-sm text-primary hover:text-primary-focus transition-colors"
              >
                <span>⬅️</span>
                Annuler
              </Link>
            </div>
          </div>
  
          <!-- Help Section -->
          <div class="mt-6 text-center">
            <div class="bg-info/10 border border-info/30 rounded-xl p-4">
              <h3 class="font-bold text-sm mb-2 text-info">
                💡 Pourquoi cette vérification ?
              </h3>
              <p class="text-xs text-base-content/60">
                Nous demandons votre mot de passe pour protéger votre compte contre les accès non autorisés, 
                notamment si vous avez laissé votre session ouverte.
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
  import Spinner from '@/Components/UI/Spinner.vue';
  
  const form = useForm({
    password: '',
  });
  
  const submit = () => {
    form.post(route('password.confirm'), {
      onFinish: () => {
        form.reset();
      },
    });
  };
  </script>