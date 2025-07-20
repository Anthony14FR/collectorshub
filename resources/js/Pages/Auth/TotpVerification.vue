<template>
  <div>
    <Head title="Vérification TOTP" />
    
    <BackgroundEffects />
    
    <div class="min-h-screen flex items-center justify-center p-4">
      <div class="max-w-lg w-full bg-base-100/90 backdrop-blur-sm rounded-2xl shadow-xl border border-base-300/30 p-4 sm:p-8">
        <div class="text-center mb-8">
          <div class="w-16 h-16 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <Lock :size="32" class="text-primary" />
          </div>
          <h1 class="text-3xl font-bold text-base-content mb-2">
            Vérification requise
          </h1>
          <p class="text-base-content/70">
            Entrez le code de votre authentificateur pour continuer
          </p>
        </div>

        <div class="space-y-6">
          <div class="bg-info/10 border border-info/30 rounded-lg p-4">
            <div class="flex gap-3">
              <Info :size="20" class="text-info" />
              <div>
                <h3 class="font-semibold text-info mb-1">Authentification à deux facteurs</h3>
                <p class="text-sm text-info/80">
                  Votre compte est protégé par un authentificateur. Entrez le code à 6 chiffres généré par votre application.
                </p>
              </div>
            </div>
          </div>

          <form @submit.prevent="verifyTotp" class="space-y-4">
            <Input
              v-model="verificationCode"
              label="Code de vérification"
              type="text"
              placeholder="123456"
              size="lg"
              variant="bordered"
              :state="errors.code ? 'error' : 'default'"
              :helper-text="errors.code"
              class="text-center tracking-widest"
              maxlength="6"
              @input="handleCodeInput"
              required
            />

            <div class="flex flex-col sm:flex-row gap-3">
              <Button 
                @click="goBack" 
                variant="outline" 
                class="flex-1"
                type="button"
              >
                Retour
              </Button>
              <Button 
                type="submit"
                :disabled="verificationCode.length !== 6 || processing"
                class="flex-1"
              >
                <span v-if="processing" class="loading loading-spinner loading-sm"></span>
                Vérifier
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue'
import Button from '@/Components/UI/Button.vue'
import Input from '@/Components/UI/Input.vue'
import { useMatomoTracking } from '@/composables/useMatomoTracking'
import { Head, router } from '@inertiajs/vue3'
import { Info, Lock } from 'lucide-vue-next'
import { onMounted, onUnmounted, ref } from 'vue'

const processing = ref(false)
const verificationCode = ref('')
const errors = ref({} as Record<string, string>)
const { trackAuthAction, startTimer, trackTimeSpent, trackFormError } = useMatomoTracking()
const pageStartTime = ref(0)

onMounted(() => {
  pageStartTime.value = startTimer()
  trackAuthAction('totp_verification_view')
})

onUnmounted(() => {
  if (pageStartTime.value) {
    trackTimeSpent(pageStartTime.value, 'Authentication', 'TOTP Verification Page')
  }
})

const handleCodeInput = (event: Event) => {
  const target = event.target as HTMLInputElement
  const value = target.value.replace(/\D/g, '')
  verificationCode.value = value
  errors.value = {}
}

const verifyTotp = async () => {
  processing.value = true
  errors.value = {}
  
  trackAuthAction('totp_verification', 'Verify attempt')
  
  router.post('/totp/verify', {
    code: verificationCode.value
  }, {
    onSuccess: () => {
      processing.value = false
      trackAuthAction('totp_verification', 'Verify success')
    },
    onError: (responseErrors: Record<string, string>) => {
      errors.value = responseErrors
      processing.value = false
      
      Object.keys(responseErrors).forEach(field => {
        trackFormError('TOTP Verification', field)
      })
      
      trackAuthAction('totp_verification', 'Verify error')
    }
  })
}

const goBack = () => {
  trackAuthAction('totp_verification', 'Navigate back to login')
  router.visit('/login')
}
</script> 