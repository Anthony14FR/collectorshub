<template>
  <div class="min-h-screen relative overflow-hidden">
    <BackgroundEffects />
    <Head title="Inscription" />

    <div class="flex items-center justify-center min-h-screen px-4 py-12">
      <div class="w-full max-w-md">
        <!-- Logo / Title -->
        <div class="text-center mb-8">
          <div class="text-6xl mb-4">🎮</div>
          <h1 class="text-4xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
            PokéArena
          </h1>
          <p class="text-base-content/70 mt-2">Créez votre compte dresseur</p>
        </div>

        <!-- Progress Steps -->
        <div class="flex items-center justify-center mb-8">
          <div class="flex items-center gap-2">
            <div
              v-for="i in 3"
              :key="i"
              class="relative"
            >
              <div
                :class="[
                  'w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300',
                  currentStep >= i
                    ? 'bg-gradient-to-br from-primary to-secondary text-base-100'
                    : 'bg-base-300/50 text-base-content/50'
                ]"
              >
                {{ i }}
              </div>
              <div
                v-if="i < 3"
                :class="[
                  'absolute top-5 left-10 w-12 h-0.5 transition-all duration-300',
                  currentStep > i ? 'bg-primary' : 'bg-base-300/50'
                ]"
              />
            </div>
          </div>
        </div>

        <!-- Registration Form -->
        <div class="bg-base-100/60 backdrop-blur-sm rounded-2xl border border-base-300/30 p-8 shadow-2xl">
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Step 1: Account Info -->
            <Transition
              enter-active-class="transition duration-300 ease-out"
              enter-from-class="transform translate-x-full opacity-0"
              enter-to-class="transform translate-x-0 opacity-100"
              leave-active-class="transition duration-200 ease-in"
              leave-from-class="transform translate-x-0 opacity-100"
              leave-to-class="transform -translate-x-full opacity-0"
              mode="out-in"
            >
              <div v-if="currentStep === 1" key="step1" class="space-y-6">
                <h2 class="text-xl font-bold text-center mb-6">
                  <span class="inline-flex items-center gap-2">
                    <span>📧</span>
                    Informations du compte
                  </span>
                </h2>

                <div>
                  <label class="text-sm font-medium text-base-content/90 mb-2 block">
                    Email
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
                    Nom d'utilisateur
                  </label>
                  <Input
                    v-model="form.username"
                    type="text"
                    placeholder="AshKetchum"
                    :error="form.errors.username"
                    required
                    autocomplete="username"
                  />
                  <p class="text-xs text-base-content/60 mt-1">
                    3-20 caractères, lettres et chiffres uniquement
                  </p>
                </div>
              </div>
            </Transition>

            <!-- Step 2: Password -->
            <Transition
              enter-active-class="transition duration-300 ease-out"
              enter-from-class="transform translate-x-full opacity-0"
              enter-to-class="transform translate-x-0 opacity-100"
              leave-active-class="transition duration-200 ease-in"
              leave-from-class="transform translate-x-0 opacity-100"
              leave-to-class="transform -translate-x-full opacity-0"
              mode="out-in"
            >
              <div v-if="currentStep === 2" key="step2" class="space-y-6">
                <h2 class="text-xl font-bold text-center mb-6">
                  <span class="inline-flex items-center gap-2">
                    <span>🔒</span>
                    Sécurité du compte
                  </span>
                </h2>

                <div>
                  <label class="text-sm font-medium text-base-content/90 mb-2 block">
                    Mot de passe
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
                      {{ passwordStrengthText }}
                    </p>
                  </div>
                </div>

                <div>
                  <label class="text-sm font-medium text-base-content/90 mb-2 block">
                    Confirmer le mot de passe
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
              </div>
            </Transition>

            <!-- Step 3: Terms & Starter -->
            <Transition
              enter-active-class="transition duration-300 ease-out"
              enter-from-class="transform translate-x-full opacity-0"
              enter-to-class="transform translate-x-0 opacity-100"
              leave-active-class="transition duration-200 ease-in"
              leave-from-class="transform translate-x-0 opacity-100"
              leave-to-class="transform -translate-x-full opacity-0"
              mode="out-in"
            >
              <div v-if="currentStep === 3" key="step3" class="space-y-6">
                <h2 class="text-xl font-bold text-center mb-6">
                  <span class="inline-flex items-center gap-2">
                    <span>🎯</span>
                    Choisissez votre starter
                  </span>
                </h2>

                <!-- Starter Pokemon Selection -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                  <button
                    v-for="starter in starters"
                    :key="starter.id"
                    type="button"
                    @click="form.starter_pokemon = starter.id"
                    :class="[
                      'relative p-4 rounded-xl border-2 transition-all duration-300',
                      form.starter_pokemon === starter.id
                        ? 'border-primary bg-primary/10 scale-105'
                        : 'border-base-300/50 hover:border-base-300 hover:bg-base-300/20'
                    ]"
                  >
                    <div class="text-4xl mb-2">{{ starter.emoji }}</div>
                    <p class="text-sm font-bold">{{ starter.name }}</p>
                    <p class="text-xs text-base-content/60">{{ starter.type }}</p>
                  </button>
                </div>

                <!-- Terms -->
                <div class="space-y-3">
                  <label class="flex items-start gap-3 cursor-pointer">
                    <input
                      v-model="form.terms"
                      type="checkbox"
                      class="checkbox checkbox-primary checkbox-sm mt-0.5"
                      required
                    />
                    <span class="text-sm text-base-content/70">
                      J'accepte les <Link href="/terms" class="text-primary hover:underline">conditions d'utilisation</Link>
                      et la <Link href="/privacy" class="text-primary hover:underline">politique de confidentialité</Link>
                    </span>
                  </label>
                  <p v-if="form.errors.terms" class="text-xs text-error">
                    {{ form.errors.terms }}
                  </p>
                </div>
              </div>
            </Transition>

            <!-- Navigation Buttons -->
            <div class="flex gap-3">
              <Button
                v-if="currentStep > 1"
                type="button"
                variant="ghost"
                size="lg"
                class="flex-1"
                @click="previousStep"
                :disabled="form.processing"
              >
                <span class="flex items-center justify-center gap-2">
                  <span>⬅️</span>
                  Retour
                </span>
              </Button>

              <Button
                v-if="currentStep < 3"
                type="button"
                variant="primary"
                size="lg"
                class="flex-1"
                @click="nextStep"
                :disabled="!canProceed"
              >
                <span class="flex items-center justify-center gap-2">
                  Suivant
                  <span>➡️</span>
                </span>
              </Button>

              <Button
                v-if="currentStep === 3"
                type="submit"
                variant="primary"
                size="lg"
                class="flex-1"
                :disabled="form.processing || !canSubmit"
              >
                <span v-if="!form.processing" class="flex items-center justify-center gap-2">
                  <span>🚀</span>
                  Créer mon compte
                </span>
                <span v-else class="flex items-center justify-center gap-2">
                  <Spinner size="sm" />
                  Création...
                </span>
              </Button>
            </div>
          </form>

          <div class="text-center mt-6">
            <p class="text-sm text-base-content/70">
              Déjà un compte ?
              <Link
                :href="route('login')"
                class="text-primary hover:text-primary-focus font-medium transition-colors"
              >
                Se connecter
              </Link>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import Spinner from '@/Components/UI/Spinner.vue';

const currentStep = ref(1);
const passwordStrength = ref(0);

const starters = [
  { id: 1, name: 'Bulbizarre', emoji: '🌱', type: 'Plante/Poison' },
  { id: 4, name: 'Salamèche', emoji: '🔥', type: 'Feu' },
  { id: 7, name: 'Carapuce', emoji: '💧', type: 'Eau' },
];

const form = useForm({
  username: '',
  email: '',
  password: '',
  password_confirmation: '',
  starter_pokemon: 1,
  terms: false,
});

const checkPasswordStrength = () => {
  const password = form.password;
  let strength = 0;
  
  if (password.length >= 8) strength++;
  if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
  if (password.match(/[0-9]/)) strength++;
  if (password.match(/[^a-zA-Z0-9]/)) strength++;
  
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

const canProceedStep1 = computed(() => {
  return form.email.length > 0 && 
         form.username.length >= 3 && 
         form.username.length <= 20 &&
         /^[a-zA-Z0-9]+$/.test(form.username);
});

const canProceedStep2 = computed(() => {
  return form.password.length >= 8 && 
         form.password === form.password_confirmation &&
         passwordStrength.value >= 2;
});

const canProceed = computed(() => {
  if (currentStep.value === 1) return canProceedStep1.value;
  if (currentStep.value === 2) return canProceedStep2.value;
  return true;
});

const canSubmit = computed(() => {
  return canProceedStep1.value && 
         canProceedStep2.value && 
         form.starter_pokemon !== null &&
         form.terms;
});

const nextStep = () => {
  if (canProceed.value) {
    currentStep.value++;
  }
};

const previousStep = () => {
  currentStep.value--;
};

const handleSubmit = () => {
  if (!canSubmit.value) return;
  
  form.post(route('register'), {
    onFinish: () => {
      form.reset('password', 'password_confirmation');
    },
  });
};
</script>