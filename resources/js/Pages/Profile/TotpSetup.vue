<template>
  <div>

    <Head title="Configuration TOTP" />

    <BackgroundEffects />

    <div class="min-h-screen flex items-center justify-center p-4">
      <div
        class="max-w-lg w-full bg-base-100/90 backdrop-blur-sm rounded-2xl shadow-xl border border-base-300/30 p-4 sm:p-8">
        <div class="text-center mb-8">
          <div
            class="w-16 h-16 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <span class="text-2xl">🔐</span>
          </div>
          <h1 class="text-3xl font-bold text-base-content mb-2">
            Authentification à deux facteurs
          </h1>
          <p class="text-base-content/70">
            Configurez un authentificateur pour sécuriser votre compte
          </p>
        </div>

        <div v-if="!enabled" class="space-y-6">
          <div class="bg-info/10 border border-info/30 rounded-lg p-4">
            <div class="flex gap-3">
              <span class="text-info text-xl">ℹ️</span>
              <div>
                <h3 class="font-semibold text-info mb-1">Instructions</h3>
                <ol class="text-sm text-info/80 space-y-1 list-decimal list-inside">
                  <li>Téléchargez un authentificateur sur votre téléphone (Google Authenticator, Authy, etc.)</li>
                  <li>Scannez le QR code ci-dessous avec l'application</li>
                  <li>Entrez le code à 6 chiffres généré par l'application</li>
                </ol>
              </div>
            </div>
          </div>

          <div class="text-center space-y-4">
            <div class="inline-block bg-white p-4 sm:p-6 rounded-lg shadow-sm">
              <canvas ref="qrCanvas" class="w-40 h-40 sm:w-48 sm:h-48"></canvas>
              <div v-if="!qrGenerated" class="text-sm text-gray-500 mt-2">
                Chargement du QR code...
              </div>
            </div>

            <div class="text-center">
              <p class="text-sm text-base-content/70 mb-2">
                Ou entrez manuellement ce code secret :
              </p>
              <div class="bg-base-200 px-4 py-2 rounded-lg inline-block">
                <code class="font-mono text-sm break-all">{{ secret }}</code>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <Input v-model="verificationCode" label="Code de vérification" type="text" placeholder="123456" size="lg"
                   variant="bordered" :state="errors.code ? 'error' : 'default'" :helper-text="errors.code"
                   class="text-center tracking-widest" maxlength="6" @input="handleCodeInput" />

            <div class="flex flex-col sm:flex-row gap-3">
              <Button @click="goBack" variant="outline" class="flex-1">
                Annuler
              </Button>
              <Button @click="enableTotp" :disabled="verificationCode.length !== 6 || processing" class="flex-1">
                <span v-if="processing" class="loading loading-spinner loading-sm"></span>
                Activer
              </Button>
            </div>
          </div>
        </div>

        <div v-else class="text-center space-y-6">
          <div class="alert alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                 viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>TOTP configuré avec succès !</span>
          </div>

          <div class="space-y-4">
            <p class="text-base-content/70">
              Votre authentification à deux facteurs est maintenant activée.
              Un code sera demandé à chaque connexion.
            </p>

            <Button @click="goBack" class="w-full">
              Retour au profil
            </Button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue'
import Button from '@/Components/UI/Button.vue'
import Input from '@/Components/UI/Input.vue'
import { Head, router } from '@inertiajs/vue3'
import * as QRCode from 'qrcode'
import { nextTick, onMounted, ref } from 'vue'

interface Props {
  qrUrl?: string;
  secret?: string;
  enabled?: boolean;
}

const props = defineProps<Props>()

const processing = ref(false)
const verificationCode = ref('')
const errors = ref({} as Record<string, string>)
const qrCanvas = ref<HTMLCanvasElement | null>(null)
const qrGenerated = ref(false)

const handleCodeInput = (event: Event) => {
  const target = event.target as HTMLInputElement
  const value = target.value.replace(/\D/g, '')
  verificationCode.value = value
  errors.value = {}
}

const enableTotp = async () => {
  processing.value = true
  errors.value = {}

  router.post('/profile/totp/enable', {
    code: verificationCode.value
  }, {
    onSuccess: () => {
      processing.value = false
    },
    onError: (responseErrors: Record<string, string>) => {
      errors.value = responseErrors
      processing.value = false
    }
  })
}

const goBack = () => {
  router.visit('/profile')
}

const generateQRCode = async () => {
  if (!props.qrUrl || !qrCanvas.value) return

  try {
    const canvas = qrCanvas.value
    const isMobile = window.innerWidth < 640
    const size = isMobile ? 160 : 192

    await QRCode.toCanvas(canvas, props.qrUrl, {
      width: size,
      margin: 2,
      color: {
        dark: '#000000',
        light: '#FFFFFF'
      }
    })
    qrGenerated.value = true
  } catch (error) {
    console.error('Erreur lors de la génération du QR code:', error)
  }
}

onMounted(async () => {
  await nextTick()
  await generateQRCode()
})
</script>