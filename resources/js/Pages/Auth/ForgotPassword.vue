<template>
    <div class="min-h-screen relative overflow-hidden">
      <BackgroundEffects />
      <Head title="Mot de passe oublié" />
  
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="w-full max-w-md">
          <!-- Logo / Title -->
          <div class="text-center mb-8">
            <div class="text-6xl mb-4">🔐</div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
              Mot de passe oublié ?
            </h1>
            <p class="text-base-content/70 mt-2">
              Pas de panique, on va vous aider !
            </p>
          </div>
  
          <!-- Password Reset Card -->
          <div class="bg-base-100/60 backdrop-blur-sm rounded-2xl border border-base-300/30 p-8 shadow-2xl">
            <!-- Status Message -->
            <div v-if="status" class="mb-6">
              <Alert
                type="success"
                title="Email envoyé !"
                :message="status"
              />
            </div>
  
            <!-- Error Message -->
            <div v-if="form.errors.email" class="mb-6">
              <Alert
                type="error"
                :message="form.errors.email"
              />
            </div>
  
            <div class="mb-6 text-center">
              <div class="bg-gradient-to-br from-info/10 to-info/5 rounded-xl p-4">
                <p class="text-sm text-base-content/80">
                  Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
                </p>
              </div>
            </div>
  
            <form @submit.prevent="submit" class="space-y-6">
              <div>
                <label class="text-sm font-medium text-base-content/90 mb-2 block">
                  <span class="inline-flex items-center gap-2">
                    <span>📧</span>
                    Adresse email
                  </span>
                </label>
                <Input
                  v-model="form.email"
                  type="email"
                  placeholder="trainer@pokemon.com"
                  required
                  autofocus
                  autocomplete="username"
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
                  <span>📤</span>
                  Envoyer le lien de réinitialisation
                </span>
                <span v-else class="flex items-center justify-center gap-2">
                  <Spinner size="sm" />
                  Envoi en cours...
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
  
            <div class="text-center space-y-3">
              <Link
                :href="route('login')"
                class="inline-flex items-center gap-2 text-sm text-primary hover:text-primary-focus transition-colors"
              >
                <span>⬅️</span>
                Retour à la connexion
              </Link>
              
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
  
          <!-- Help Section -->
          <div class="mt-6 text-center">
            <div class="bg-warning/10 border border-warning/30 rounded-xl p-4">
              <h3 class="font-bold text-sm mb-2 text-warning">
                ⚡ Conseils de sécurité
              </h3>
              <ul class="text-xs text-base-content/60 space-y-1 text-left max-w-xs mx-auto">
                <li>• Utilisez un mot de passe unique pour chaque site</li>
                <li>• Choisissez un mot de passe d'au moins 8 caractères</li>
                <li>• Mélangez lettres, chiffres et symboles</li>
                <li>• Évitez les informations personnelles évidentes</li>
              </ul>
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
    status?: string;
  }>();
  
  const form = useForm({
    email: '',
  });
  
  const submit = () => {
    form.post(route('password.email'));
  };
  </script>