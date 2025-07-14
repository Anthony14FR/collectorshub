<script setup lang="ts">
import SocialLoginButtons from '@/Components/Auth/SocialLoginButtons.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import Input from '@/Components/UI/Input.vue';
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, nextTick, reactive, ref } from "vue";

const AVATAR_OPTIONS = [1, 2];
const AVATAR_PATH = (id: number) => `/images/trainer/${id}.png`;

const currentStep = ref(1);
const totalSteps = 4;

const form = reactive({
  username: "",
  email: "",
  password: "",
  password_confirmation: "",
  avatar: AVATAR_PATH(1),
  processing: false,
  errors: {} as Record<string, string>,
});

const nameInput = ref<HTMLInputElement>();

const progressPercentage = computed(() => {
  return (currentStep.value / totalSteps) * 100;
});

const canProceedStep1 = computed(() => {
  return form.avatar !== '';
});

const canProceedStep2 = computed(() => {
  return form.username.trim() !== '' && form.email.trim() !== '';
});

const canProceedStep3 = computed(() => {
  return form.password !== '' && form.password_confirmation !== '' && form.password === form.password_confirmation;
});

const canSubmit = computed(() => {
  return canProceedStep1.value && canProceedStep2.value && canProceedStep3.value;
});

const nextStep = () => {
  if (currentStep.value < totalSteps) {
    currentStep.value++;
  }
};

const prevStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--;
  }
};

const submit = () => {
  if (!canSubmit.value || currentStep.value !== 4) return;

  form.processing = true;
  form.errors = {};

  router.post(
    "/register",
    {
      avatar: form.avatar,
      username: form.username,
      email: form.email,
      password: form.password,
      password_confirmation: form.password_confirmation,
    },
    {
      onFinish: () => {
        form.processing = false;
        form.password = "";
        form.password_confirmation = "";
      },
      onError: (errors: Record<string, string>) => {
        form.processing = false;
        form.errors = errors;

        if (errors.avatar) {
          currentStep.value = 1;
        } else if (errors.username || errors.email) {
          currentStep.value = 2;
          nextTick(() => nameInput.value?.focus());
        } else if (errors.password || errors.password_confirmation) {
          currentStep.value = 3;
        }
      },
    }
  );
};
</script>

<template>

  <Head title="Inscription" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen flex items-center justify-center py-8 px-4">
      <div class="w-full max-w-md">
        <div class="text-center mb-8">
          <Link href="/"
            class="text-3xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent hover:from-secondary hover:to-primary transition-all duration-300 tracking-wider">
          🎮 CollectorsHub
          </Link>
          <h2 class="mt-6 text-2xl font-bold text-base-content tracking-wider">
            INSCRIPTION
          </h2>
          <p class="mt-2 text-sm text-base-content/70">
            Ou
            <Link href="/login" class="font-medium text-primary hover:text-secondary transition-colors">
            connectez-vous à votre compte existant
            </Link>
          </p>
        </div>

        <!-- Social Login Buttons -->
        <div class="mb-6">
          <SocialLoginButtons text="S'inscrire avec" />
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-accent/10 to-accent/5 border-b border-accent/20">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
                <span class="text-lg">👤</span>
                CRÉER VOTRE COMPTE
              </h3>
              <div class="text-xs text-base-content/70">
                {{ currentStep }}/{{ totalSteps }}
              </div>
            </div>
            <div class="mt-2 w-full bg-base-300/30 rounded-full h-1.5">
              <div class="bg-gradient-to-r from-accent to-accent/80 h-1.5 rounded-full transition-all duration-500"
                :style="{ width: progressPercentage + '%' }"></div>
            </div>
          </div>

          <div class="p-6">
            <div v-if="currentStep === 1" class="space-y-6">
              <div class="text-center">
                <h4 class="text-lg font-bold text-base-content mb-2">Choisissez votre avatar</h4>
                <p class="text-sm text-base-content/70 mb-6">Sélectionnez l'avatar qui vous représentera</p>
              </div>

              <div class="flex justify-center space-x-6">
                <label v-for="id in AVATAR_OPTIONS" :key="id" class="cursor-pointer flex flex-col items-center group">
                  <input type="radio" v-model="form.avatar" :value="AVATAR_PATH(id)" class="hidden" />
                  <div class="relative transition-all duration-300"
                    :class="form.avatar === AVATAR_PATH(id) ? 'scale-110' : 'scale-100 group-hover:scale-105'">
                    <img :src="AVATAR_PATH(id)" :alt="`Avatar ${id}`"
                      class="w-20 h-20 rounded-full border-4 transition-all duration-300"
                      :class="form.avatar === AVATAR_PATH(id) ? 'border-accent shadow-lg shadow-accent/30' : 'border-base-300 opacity-70 group-hover:opacity-100 group-hover:border-accent/50'" />
                    <div v-if="form.avatar === AVATAR_PATH(id)"
                      class="absolute -top-2 -right-2 w-6 h-6 bg-accent rounded-full flex items-center justify-center shadow-lg">
                      <span class="text-xs">✓</span>
                    </div>
                  </div>
                  <span v-if="form.avatar === AVATAR_PATH(id)"
                    class="text-xs text-accent mt-2 font-bold tracking-wider">CHOISI</span>
                </label>
              </div>

              <div v-if="form.errors.avatar" class="text-sm text-error text-center">
                {{ form.errors.avatar }}
              </div>

              <div class="flex justify-end">
                <Button @click="nextStep" :disabled="!canProceedStep1" variant="primary" size="md">
                  Suivant →
                </Button>
              </div>
            </div>

            <div v-if="currentStep === 2" class="space-y-6">
              <div class="text-center">
                <h4 class="text-lg font-bold text-base-content mb-2">Informations du compte</h4>
                <p class="text-sm text-base-content/70 mb-6">Choisissez votre nom d'utilisateur et email</p>
              </div>

              <div class="space-y-4">
                <div>
                  <Input id="username" ref="nameInput" v-model="form.username" type="text" required
                    label="Nom d'utilisateur" placeholder="Votre nom d'utilisateur" variant="default" size="md"
                    :state="form.errors.username ? 'error' : 'default'" />
                  <div v-if="form.errors.username" class="mt-2 text-sm text-error">
                    {{ form.errors.username }}
                  </div>
                </div>

                <div>
                  <Input id="email" v-model="form.email" type="email" required label="Adresse email"
                    placeholder="votre@email.com" variant="default" size="md"
                    :state="form.errors.email ? 'error' : 'default'" />
                  <div v-if="form.errors.email" class="mt-2 text-sm text-error">
                    {{ form.errors.email }}
                  </div>
                </div>
              </div>

              <div class="flex justify-between">
                <Button @click="prevStep" variant="secondary" size="md">
                  ← Précédent
                </Button>
                <Button @click="nextStep" :disabled="!canProceedStep2" variant="primary" size="md">
                  Suivant →
                </Button>
              </div>
            </div>

            <div v-if="currentStep === 3" class="space-y-6">
              <div class="text-center">
                <h4 class="text-lg font-bold text-base-content mb-2">Sécurisation du compte</h4>
                <p class="text-sm text-base-content/70 mb-6">Créez un mot de passe sécurisé</p>
              </div>

              <div class="space-y-4">
                <div>
                  <Input id="password" v-model="form.password" type="password" required label="Mot de passe"
                    placeholder="••••••••" variant="default" size="md"
                    :state="form.errors.password ? 'error' : 'default'" />
                  <div v-if="form.errors.password" class="mt-2 text-sm text-error">
                    {{ form.errors.password }}
                  </div>
                </div>

                <div>
                  <Input id="password_confirmation" v-model="form.password_confirmation" type="password" required
                    label="Confirmer le mot de passe" placeholder="••••••••" variant="default" size="md"
                    :state="form.errors.password_confirmation ? 'error' : 'default'" />
                  <div v-if="form.errors.password_confirmation" class="mt-2 text-sm text-error">
                    {{ form.errors.password_confirmation }}
                  </div>
                </div>
              </div>

              <div class="flex justify-between">
                <Button @click="prevStep" variant="secondary" size="md">
                  ← Précédent
                </Button>
                <Button @click="nextStep" :disabled="!canProceedStep3" variant="primary" size="md">
                  Suivant →
                </Button>
              </div>
            </div>

            <div v-if="currentStep === 4" class="space-y-6">
              <div class="text-center">
                <h4 class="text-lg font-bold text-base-content mb-2">Récapitulatif</h4>
                <p class="text-sm text-base-content/70 mb-6">Vérifiez vos informations avant de confirmer</p>
              </div>

              <div class="space-y-4">
                <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/30">
                  <h5 class="font-medium text-base-content mb-3">Avatar sélectionné</h5>
                  <div class="flex items-center gap-3">
                    <img :src="form.avatar" alt="Avatar choisi" class="w-12 h-12 rounded-full border-2 border-accent" />
                    <span class="text-sm text-base-content/70">Votre avatar de dresseur</span>
                  </div>
                </div>

                <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/30">
                  <h5 class="font-medium text-base-content mb-3">Informations du compte</h5>
                  <div class="space-y-2">
                    <div class="flex justify-between">
                      <span class="text-sm text-base-content/70">Nom d'utilisateur :</span>
                      <span class="text-sm font-medium">{{ form.username }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-sm text-base-content/70">Email :</span>
                      <span class="text-sm font-medium">{{ form.email }}</span>
                    </div>
                  </div>
                </div>

                <div class="bg-base-200/30 rounded-lg p-4 border border-base-300/30">
                  <h5 class="font-medium text-base-content mb-3">Sécurité</h5>
                  <div class="flex justify-between">
                    <span class="text-sm text-base-content/70">Mot de passe :</span>
                    <span class="text-sm font-medium">••••••••</span>
                  </div>
                </div>
              </div>

              <div class="flex justify-between">
                <Button @click="prevStep" variant="secondary" size="md">
                  ← Précédent
                </Button>
                <Button @click="submit" :disabled="form.processing || !canSubmit" variant="primary" size="md">
                  <span v-if="form.processing" class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                      viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                      </path>
                    </svg>
                    Inscription...
                  </span>
                  <span v-else>🎯 Confirmer l'inscription</span>
                </Button>
              </div>
            </div>
          </div>
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