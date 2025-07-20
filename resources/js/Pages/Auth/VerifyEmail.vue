<script setup lang="ts">
import Alert from '@/Components/UI/Alert.vue';
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
import Button from '@/Components/UI/Button.vue';
import { useMatomoTracking } from '@/composables/useMatomoTracking';
import { Head, Link, router } from '@inertiajs/vue3';
import {
  ArrowLeft,
  Clock,
  Gamepad2,
  Lightbulb,
  LogOut,
  Mail,
  RotateCcw,
  Sparkles, Zap
} from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, reactive, ref } from 'vue';

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
const { trackAuthAction, startTimer, trackTimeSpent } = useMatomoTracking();
const pageStartTime = ref<number>(0);

onMounted(() => {
  pageStartTime.value = startTimer();
  trackAuthAction('verify_email_view');
});

const canResend = computed(() => remainingTime.value <= 0 && !form.processing);

const submit = () => {
  if (!canResend.value) return;

  form.processing = true;
  trackAuthAction('email_verification', 'Resend request');

  router.post('/email/verification-notification', {}, {
    onSuccess: () => {
      form.processing = false;
      form.recentlySent = true;
      trackAuthAction('email_verification', 'Resend success');
      startCountdown();
    },
    onError: () => {
      form.processing = false;
      trackAuthAction('email_verification', 'Resend error');
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
  trackAuthAction('email_verification', 'Logout from verify page');
  router.post('/logout');
};

onUnmounted(() => {
  if (pageStartTime.value) {
    trackTimeSpent(pageStartTime.value, 'Authentication', 'Verify Email Page');
  }
  
  if (intervalId.value) {
    clearInterval(intervalId.value);
  }
});
</script>

<template>
  <Head title="Vérification de l'email" />

  <div class="min-h-screen bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen flex items-center justify-center py-8 px-4">
      <div class="w-full max-w-lg">
        <div class="text-center mb-8">
          <Link href="/" class="text-3xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent hover:from-secondary hover:to-primary transition-all duration-300 tracking-wider">
            <Gamepad2 :size="32" class="inline" /> CollectorsHub
          </Link>
          <div class="mt-6">
            <h2 class="text-2xl font-bold text-base-content tracking-wider">
              VÉRIFICATION EMAIL
            </h2>
            <p class="mt-2 text-sm text-base-content/70">
              Votre aventure vous attend ! <Sparkles :size="16" class="inline" />
            </p>
          </div>
        </div>

        <div v-if="status === 'verification-link-sent' || form.recentlySent" class="mb-6">
          <Alert type="success" message="Email de vérification envoyé ! Un nouveau lien de vérification a été envoyé à votre adresse email." />
        </div>

        <div class="bg-base-100/60 backdrop-blur-sm rounded-xl border border-base-300/30 overflow-hidden">
          <div class="p-3 bg-gradient-to-r from-warning/10 to-warning/5 border-b border-warning/20">
            <h3 class="text-sm font-bold tracking-wider flex items-center gap-2">
              <Zap :size="18" />
              ACTIVATION REQUISE
            </h3>
          </div>

          <div class="p-6 space-y-6">

            <div class="text-center space-y-4">
              <h3 class="text-xl font-bold text-base-content">
                Presque prêt à capturer !
              </h3>
              <p class="text-base-content/80 leading-relaxed">
                Nous avons envoyé un email de vérification à votre adresse.
                Cliquez sur le lien dans l'email pour activer votre compte et commencer votre collection.
              </p>
              
              <div class="bg-info/10 border border-info/30 rounded-lg p-4">
                <p class="text-info text-sm flex items-center gap-2">
                  <Lightbulb :size="16" />
                  <strong>Astuce :</strong> Vérifiez vos spams si vous ne trouvez pas l'email !
                </p>
              </div>
            </div>

            <div class="space-y-4">
              <Button
                @click="submit"
                :disabled="!canResend"
                :variant="canResend ? 'secondary' : 'ghost'"
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
                <span v-else-if="remainingTime > 0" class="flex items-center justify-center">
                  <Clock :size="18" class="mr-2" />
                  Renvoyer dans {{ remainingTime }}s
                </span>
                <span v-else class="flex items-center justify-center">
                  <RotateCcw :size="18" class="mr-2" />
                  Renvoyer l'email de vérification
                </span>
              </Button>

              <div class="text-xs text-base-content/50 text-center">
                Vous pouvez renvoyer un email toutes les 30 secondes
              </div>
            </div>

            <div class="pt-4 border-t border-base-300/50">
              <Button
                @click="logout"
                variant="ghost"
                size="sm"
                class="w-full"
              >
                <LogOut :size="18" class="mr-2" />
                Se déconnecter
              </Button>
            </div>
          </div>
        </div>

        <div class="mt-6 space-y-4">
          <div class="bg-base-100/40 backdrop-blur-sm rounded-lg p-4 border border-base-300/30">
            <h4 class="text-base-content font-medium mb-2 tracking-wider">Besoin d'aide ?</h4>
            <p class="text-sm text-base-content/70 mb-3">
              Si vous rencontrez des problèmes, contactez notre équipe support.
            </p>
            <a
              href="mailto:contact@collectorshub.fr"
              class="inline-flex items-center text-sm text-primary hover:text-secondary transition-colors"
            >
              <Mail :size="16" class="mr-2" />
              contact@collectorshub.fr
            </a>
          </div>

          <div class="text-center">
            <Link href="/" class="text-sm text-base-content/70 hover:text-base-content transition-colors inline-flex items-center">
              <ArrowLeft :size="16" class="mr-1" />
              Retour à l'accueil
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>