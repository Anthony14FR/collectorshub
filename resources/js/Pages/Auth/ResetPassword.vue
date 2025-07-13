<template>
    <div class="min-h-screen relative overflow-hidden">
      <BackgroundEffects />
      <Head title="Réinitialiser le mot de passe" />
  
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="w-full max-w-md">
          <!-- Logo / Title -->
          <div class="text-center mb-8">
            <div class="text-6xl mb-4">🔑</div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
              Nouveau mot de passe
            </h1>
            <p class="text-base-content/70 mt-2">
              Choisissez un nouveau mot de passe sécurisé
            </p>
          </div>
  
          <!-- Reset Password Card -->
          <div class="bg-base-100/60 backdrop-blur-sm rounded-2xl border border-base-300/30 p-8 shadow-2xl">
            <form @submit.prevent="submit" class="space-y-6">
              <!-- Email Field -->
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
  
              <!-- New Password -->
              <div>
                <label class="text-sm font-medium text-base-content/90 mb-2 block">
                  <span class="inline-flex items-center gap-2">
                    <span>🔒</span>
                    Nouveau mot de passe
                  </span>
                </label>
                <Input
                  v-model="form.password"
                  type="password"
                  placeholder="••••••••"
                  :error="form.errors.password"
                  required
                  autocomplete="new-password"
                  @input="checkPasswordStrength"
                />
                
                <!-- Password Strength Indicator -->
                <div class="mt-2 space-y-2">
                  <div class="flex gap-1">
                    <div
                      v-for="i in 4"
                      :key="i"
                      :class="[
                        'h-1 flex-1 rounded-full transition-all duration-300',
                        passwordStrength >= i ? passwordStrengthColor : 'bg-base-300/50'
                      ]"
                    />
                  </div>
                  <p v-if="passwordStrength > 0" :class="['text-xs', passwordStrengthTextColor]">
                    Force : {{ passwordStrengthText }}
                  </p>
                </div>
              </div>
  
              <!-- Confirm Password -->
              <div>
                <label class="text-sm font-medium text-base-content/90 mb-2 block">
                  <span class="inline-flex items-center gap-2">
                    <span>🔒</span>
                    Confirmer le mot de passe
                  </span>
                </label>
                <Input
                  v-model="form.password_confirmation"
                  type="password"
                  placeholder="••••••••"
                  :error="form.errors.password_confirmation"
                  required
                  autocomplete="new-password"
                />
              </div>
  
              <!-- Security Tips -->
              <div class="bg-info/10 border border-info/30 rounded-xl p-4">
                <h4 class="text-xs font-bold text-info mb-2">
                  💡 Conseils pour un mot de passe sécurisé :
                </h4>
                <ul class="text-xs text-base-content/70 space-y-1">
                  <li class="flex items-start gap-2">
                    <span :class="form.password.length >= 8 ? 'text-success' : 'text-base-content/40'">
                      {{ form.password.length >= 8 ? '✅' : '⭕' }}
                    </span>
                    <span>Au moins 8 caractères</span>
                  </li>
                  <li class="flex items-start gap-2">
                    <span :class="hasUpperAndLower ? 'text-success' : 'text-base-content/40'">
                      {{ hasUpperAndLower ? '✅' : '⭕' }}
                    </span>
                    <span>Majuscules et minuscules</span>
                  </li>
                  <li class="flex items-start gap-2">
                    <span :class="hasNumbers ? 'text-success' : 'text-base-content/40'">
                      {{ hasNumbers ? '✅' : '⭕' }}
                    </span>
                    <span>Au moins un chiffre</span>
                  </li>
                  <li class="flex items-start gap-2">
                    <span :class="hasSpecialChars ? 'text-success' : 'text-base-content/40'">
                      {{ hasSpecialChars ? '✅' : '⭕' }}
                    </span>
                    <span>Un caractère spécial (!@#$%^&*)</span>
                  </li>
                </ul>
              </div>
  
              <Button
                type="submit"
                variant="primary"
                size="lg"
                class="w-full"
                :disabled="form.processing || passwordStrength < 2"
              >
                <span v-if="!form.processing" class="flex items-center justify-center gap-2">
                  <span>🔄</span>
                  Réinitialiser le mot de passe
                </span>
                <span v-else class="flex items-center justify-center gap-2">
                  <Spinner size="sm" />
                  Réinitialisation...
                </span>
              </Button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, computed } from 'vue';
  import { Head, useForm } from '@inertiajs/vue3';
  import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
  import Button from '@/Components/UI/Button.vue';
  import Input from '@/Components/UI/Input.vue';
  import Spinner from '@/Components/UI/Spinner.vue';
  
  const props = defineProps<{
    email: string;
    token: string;
  }>();
  
  const passwordStrength = ref(0);
  
  const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
  });
  
  const hasUpperAndLower = computed(() => {
    return /[a-z]/.test(form.password) && /[A-Z]/.test(form.password);
  });
  
  const hasNumbers = computed(() => {
    return /[0-9]/.test(form.password);
  });
  
  const hasSpecialChars = computed(() => {
    return /[^a-zA-Z0-9]/.test(form.password);
  });
  
  const checkPasswordStrength = () => {
    const password = form.password;
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (hasUpperAndLower.value) strength++;
    if (hasNumbers.value) strength++;
    if (hasSpecialChars.value) strength++;
    
    passwordStrength.value = strength;
  };
  
  const passwordStrengthColor = computed(() => {
    const colors = ['', 'bg-error', 'bg-warning', 'bg-info', 'bg-success'];
    return colors[passwordStrength.value];
  });
  
  const passwordStrengthTextColor = computed(() => {
    const colors = ['', 'text-error', 'text-warning', 'text-info', 'text-success'];
    return colors[passwordStrength.value];
  });
  
  const passwordStrengthText = computed(() => {
    const texts = ['', 'Faible', 'Moyen', 'Bon', 'Excellent'];
    return texts[passwordStrength.value];
  });
  
  const submit = () => {
    form.post(route('password.store'), {
      onFinish: () => {
        form.reset('password', 'password_confirmation');
      },
    });
  };
  </script>