<template>
  <Head title="Créer un Club" />
  
  <div class="min-h-screen bg-gradient-to-br from-base-100 via-base-200 to-base-100">
    <div class="container mx-auto px-4 py-8 max-w-2xl">
      <div class="bg-gradient-to-br from-base-100/95 to-base-200/90 backdrop-blur-lg border-2 border-primary/20 rounded-3xl shadow-2xl shadow-primary/20 overflow-hidden">
        <div class="bg-gradient-to-r from-primary/20 to-secondary/20 border-b border-primary/20 px-8 py-6">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-warning/20 to-warning/40 rounded-xl flex items-center justify-center">
              <span class="text-2xl">🏗️</span>
            </div>
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
                Créer un Club
              </h1>
              <p class="text-base-content/60 text-sm">Fondez votre propre communauté</p>
            </div>
          </div>
        </div>
        
        <div class="p-8">
          <Alert 
            :type="canCreateClub ? 'info' : 'warning'" 
            variant="ghost" 
            class="mb-6"
          >
            <template #icon>
              <span class="text-lg">💰</span>
            </template>
            <div>
              <strong>Coût de création : 100,000 Cash</strong><br>
              <span v-if="canCreateClub">
                Vous avez {{ userCash.toLocaleString() }} Cash. Vous pouvez créer un club.
              </span>
              <span v-else>
                Vous avez {{ userCash.toLocaleString() }} Cash. Vous devez avoir au moins 100,000 Cash pour créer un club.
              </span>
            </div>
          </Alert>

          <form @submit.prevent="submit" class="space-y-6">
            <div>
              <label for="name" class="block text-base-content font-semibold mb-2">Nom du Club</label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                class="w-full bg-base-200/50 border-2 border-primary/30 rounded-xl px-4 py-3 text-base-content focus:border-primary/50 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all duration-300"
                placeholder="Nom de votre club"
                maxlength="50"
                required
              >
              <p class="text-base-content/60 text-sm mt-1">Maximum 50 caractères</p>
            </div>

            <div>
              <label for="description" class="block text-base-content font-semibold mb-2">Description</label>
              <textarea
                id="description"
                v-model="form.description"
                rows="4"
                class="w-full bg-base-200/50 border-2 border-primary/30 rounded-xl px-4 py-3 text-base-content focus:border-primary/50 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all duration-300 resize-none"
                placeholder="Description de votre club"
                maxlength="500"
                required
              ></textarea>
              <p class="text-base-content/60 text-sm mt-1">Maximum 500 caractères</p>
            </div>

            <div>
              <label class="block text-base-content font-semibold mb-2">Icône du Club</label>
              <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 max-h-64 overflow-y-auto bg-base-200/30 rounded-xl p-4">
                <div
                  v-for="(imagePath, typeName) in typeImages"
                  :key="typeName"
                  @click="selectIcon(imagePath)"
                  class="relative cursor-pointer group"
                >
                  <div 
                    class="w-full aspect-square bg-gradient-to-br from-base-200/50 to-base-300/30 rounded-xl border-2 transition-all duration-300 hover:scale-105"
                    :class="form.icon === imagePath ? 'border-primary/60 bg-primary/10' : 'border-primary/20 hover:border-primary/40'"
                  >
                    <img 
                      :src="imagePath" 
                      :alt="typeName"
                      class="w-full h-full object-contain p-2 rounded-lg"
                    >
                  </div>
                  <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div class="absolute bottom-1 left-1 right-1 text-center">
                      <span class="text-xs text-white font-medium">{{ typeName }}</span>
                    </div>
                  </div>
                  <div 
                    v-if="form.icon === imagePath"
                    class="absolute -top-1 -right-1 w-6 h-6 bg-primary rounded-full flex items-center justify-center"
                  >
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                </div>
              </div>
              <p class="text-base-content/60 text-sm mt-2">Cliquez sur une icône pour la sélectionner</p>
            </div>

            <div class="flex gap-4 pt-4">
              <Button
                variant="secondary"
                size="md"
                class="flex-1"
                @click="router.visit('/clubs')"
              >
                Annuler
              </Button>
              <Button
                type="submit"
                variant="primary"
                size="md"
                class="flex-1"
                :disabled="processing || !canCreateClub"
              >
                <span v-if="processing" class="flex items-center gap-2">
                  <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                  Création...
                </span>
                <span v-else class="flex items-center gap-2">
                  <span class="text-lg">🏗️</span>
                  Créer le Club
                </span>
              </Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Button from '@/Components/UI/Button.vue'
import Alert from '@/Components/UI/Alert.vue'

interface Props {
  typeImages: Record<string, string>
  userCash: number
  canCreateClub: boolean
}

const props = defineProps<Props>()

const form = ref({
  name: '',
  description: '',
  icon: ''
})

const processing = ref(false)

const selectIcon = (imagePath: string) => {
  form.value.icon = imagePath
}

const submit = () => {
  if (!props.canCreateClub) {
    return
  }
  
  processing.value = true
  
  router.post('/clubs', form.value, {
    onFinish: () => {
      processing.value = false
    }
  })
}
</script> 