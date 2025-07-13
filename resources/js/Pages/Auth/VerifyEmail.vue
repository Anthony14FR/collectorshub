<template>
    <div class="min-h-screen relative overflow-hidden">
      <BackgroundEffects />
      <Head title="Vérification Email" />
  
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="w-full max-w-md">
          <!-- Logo / Title -->
          <div class="text-center mb-8">
            <div class="text-6xl mb-4 animate-pulse">📧</div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
              Vérifiez votre email
            </h1>
            <p class="text-base-content/70 mt-2">
              Un dernier pas avant de commencer l'aventure !
            </p>
          </div>
  
          <!-- Verification Card -->
          <div class="bg-base-100/60 backdrop-blur-sm rounded-2xl border border-base-300/30 p-8 shadow-2xl">
            <!-- Success Message -->
            <div v-if="verificationLinkSent" class="mb-6">
              <Alert
                type="success"
                title="Email envoyé !"
                message="Un nouveau lien de vérification a été envoyé à votre adresse email."
                :show="verificationLinkSent"
                dismissible
                @dismiss="verificationLinkSent = false"
              />
            </div>
  
            <div class="text-center space-y-6">
              <div class="bg-gradient-to-br from-primary/10 to-secondary/10 rounded-xl p-6">
                <h2 class="text-lg font-bold mb-3">
                  Merci pour votre inscription ! 🎉
                </h2>
                <p class="text-sm text-base-content/80 mb-4">
                  Nous avons envoyé un email de confirmation à votre adresse. 
                  Cliquez sur le lien dans l'email pour activer votre compte.
                </p>
                <div class="flex items-center justify-center gap-2 text-xs text-base-content/60">
                  <span>📨</span>
                  <span>Vérifiez aussi vos spams</span>
                </div>
              </div>
  
              <!-- Timer Display -->
              <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
                mode="out-in"
              >
                <div v-if="remainingTime > 0" key="timer" class="space-y-2">
                  <p class="text-sm text-base-content/70">
                    Renvoyer l'email dans :
                  </p>
                  <div class="text-3xl font-bold text-primary">
                    {{ formatTime(remainingTime) }}
                  </div>
                  <div class="w-full bg-base-300/50 rounded-full h-2 overflow-hidden">
                    <div
                      class="h-full bg-gradient-to-r from-primary to-secondary transition-all duration-1000"
                      :style="{ width: `${(remainingTime / 60) * 100}%` }"
                    />
                  </div>
                </div>
                
                <form v-else key="resend" @submit.prevent="submit">
                  <Button
                    type="submit"
                    variant="primary"
                    size="lg"
                    class="w-full"
                    :disabled="form.processing"
                  >
                    <span v-if="!form.processing" class="flex items-center justify-center gap-2">
                      <span>📤</span>
                      Renvoyer l'email
                    </span>
                    <span v-else class="flex items-center justify-center gap-2">
                      <Spinner size="sm" />
                      Envoi...
                    </span>
                  </Button>
                </form>
              </Transition>
  
              <div class="divider text-xs text-base-content/50">OU</div>
  
              <!-- Logout Option -->
              <div class="space-y-3">
                <p class="text-sm text-base-content/70">
                  Mauvaise adresse email ?
                </p>
                <Link
                  :href="route('logout')"
                  method="post"
                  as="button"
                  class="w-full"
                >
                  <Button
                    variant="ghost"
                    size="md"
                    class="w-full"
                  >
                    <span class="flex items-center justify-center gap-2">
                      <span>🚪</span>
                      Se déconnecter et réessayer
                    </span>
                  </Button>
                </Link>
              </div>
            </div>
          </div>
  
          <!-- Help Section -->
          <div class="mt-6 text-center">
            <div class="bg-info/10 border border-info/30 rounded-xl p-4">
              <h3 class="font-bold text-sm mb-2 text-info">
                💡 Besoin d'aide ?
              </h3>
              <p class="text-xs text-base-content/70 mb-2">
                Si vous ne trouvez pas l'email :
              </p>
              <ul class="text-xs text-base-content/60 space-y-1 text-left max-w-xs mx-auto">
                <li>• Vérifiez votre dossier spam/courrier indésirable</li>
                <li>• Assurez-vous que l'adresse email est correcte</li>
                <li>• Attendez quelques minutes (parfois ça prend du temps)</li>
                <li>• Contactez le support si le problème persiste</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, computed, onMounted, onUnmounted } from 'vue';
  import { Head, Link, useForm } from '@inertiajs/vue3';
  import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue';
  import Button from '@/Components/UI/Button.vue';
  import Alert from '@/Components/UI/Alert.vue';
  import Spinner from '@/Components/UI/Spinner.vue';
  
  const props = defineProps<{
    status?: string;
  }>();
  
  const THROTTLE_DURATION = 60; // 60 seconds
  const STORAGE_KEY = 'email_verification_throttle';
  
  const form = useForm({});
  const verificationLinkSent = ref(false);
  const remainingTime = ref(0);
  let interval: number | null = null;
  
  // Get throttle end time from localStorage
  const getThrottleEndTime = () => {
    const stored = localStorage.getItem(STORAGE_KEY);
    return stored ? parseInt(stored, 10) : 0;
  };
  
  // Set throttle end time in localStorage
  const setThrottleEndTime = () => {
    const endTime = Date.now() + (THROTTLE_DURATION * 1000);
    localStorage.setItem(STORAGE_KEY, endTime.toString());
    return endTime;
  };
  
  // Format time as MM:SS
  const formatTime = (seconds: number) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  };
  
  // Update remaining time
  const updateRemainingTime = () => {
    const endTime = getThrottleEndTime();
    const now = Date.now();
    
    if (endTime > now) {
      remainingTime.value = Math.ceil((endTime - now) / 1000);
    } else {
      remainingTime.value = 0;
      if (interval) {
        clearInterval(interval);
        interval = null;
      }
    }
  };
  
  // Submit form
  const submit = () => {
    form.post(route('verification.send'), {
      onSuccess: () => {
        verificationLinkSent.value = true;
        setThrottleEndTime();
        updateRemainingTime();
        
        // Start countdown
        interval = setInterval(updateRemainingTime, 1000);
        
        // Hide success message after 5 seconds
        setTimeout(() => {
          verificationLinkSent.value = false;
        }, 5000);
      },
    });
  };
  
  // Check for existing throttle on mount
  onMounted(() => {
    updateRemainingTime();
    
    if (remainingTime.value > 0) {
      interval = setInterval(updateRemainingTime, 1000);
    }
    
    // Check if verification link was sent (from props)
    if (props.status === 'verification-link-sent') {
      verificationLinkSent.value = true;
      setTimeout(() => {
        verificationLinkSent.value = false;
      }, 5000);
    }
  });
  
  // Cleanup interval on unmount
  onUnmounted(() => {
    if (interval) {
      clearInterval(interval);
    }
  });
  </script>