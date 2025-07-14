<template>
  <Head title="Expéditions" />

  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />

    <div class="relative z-10 min-h-screen">
      <div class="w-full max-w-7xl mx-auto px-2 sm:px-4 lg:px-6 pb-16">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 sm:pt-6 mb-4 sm:mb-6">
          <div class="flex items-center gap-2 sm:gap-4">
            <Button @click="goBack" variant="outline" size="sm" class="shrink-0">
              ← Retour
            </Button>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold bg-gradient-to-r from-warning to-warning/80 bg-clip-text text-transparent">
              🗺️ Expéditions du jour
            </h1>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
          <div v-for="expedition in expeditions" :key="expedition.id" 
               class="bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-sm rounded-xl border border-base-300/30 hover:border-warning/50 shadow-lg transition-all duration-300 group">
            
            <div class="p-4 sm:p-6">
              <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-2">
                    <h2 class="text-lg sm:text-xl font-bold text-base-content">
                      {{ expedition.expedition.name }}
                    </h2>
                    <span class="px-2 py-1 rounded-full text-xs font-medium"
                          :class="{
                            'bg-gray-100 text-gray-800': expedition.expedition.rarity === 'normal',
                            'bg-blue-100 text-blue-800': expedition.expedition.rarity === 'rare',
                            'bg-purple-100 text-purple-800': expedition.expedition.rarity === 'epic',
                            'bg-yellow-100 text-yellow-800': expedition.expedition.rarity === 'legendary'
                          }">
                      {{ expedition.expedition.rarity === 'normal' ? 'Normal' : 
                        expedition.expedition.rarity === 'rare' ? 'Rare' : 
                        expedition.expedition.rarity === 'epic' ? 'Épique' : 'Légendaire' }}
                    </span>
                  </div>
                  <p class="text-sm text-base-content/70 mb-3">
                    {{ expedition.expedition.description }}
                  </p>
                </div>
                <div class="flex-shrink-0 ml-4">
                  <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                       :class="{
                         'bg-gradient-to-br from-gray-200/50 to-gray-300/50': expedition.expedition.rarity === 'normal',
                         'bg-gradient-to-br from-blue-200/50 to-blue-300/50': expedition.expedition.rarity === 'rare',
                         'bg-gradient-to-br from-purple-200/50 to-purple-300/50': expedition.expedition.rarity === 'epic',
                         'bg-gradient-to-br from-yellow-200/50 to-yellow-300/50': expedition.expedition.rarity === 'legendary'
                       }">
                    <svg class="w-6 h-6"
                         :class="{
                           'text-gray-600': expedition.expedition.rarity === 'normal',
                           'text-blue-600': expedition.expedition.rarity === 'rare',
                           'text-purple-600': expedition.expedition.rarity === 'epic',
                           'text-yellow-600': expedition.expedition.rarity === 'legendary'
                         }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                  </div>
                </div>
              </div>

              <div class="space-y-4">
                <div class="bg-base-200/50 rounded-lg p-3">
                  <h3 class="font-semibold text-sm mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-info rounded-full"></span>
                    Exigences
                  </h3>
                  <ul class="space-y-1">
                    <li v-for="req in expedition.expedition.requirements" :key="req.id" 
                        class="text-xs text-base-content/70 flex items-center gap-2">
                      <span class="w-1 h-1 bg-base-content/50 rounded-full"></span>
                      {{ req.quantity }} {{ req.type === 'rarity' ? 'Pokémon' : req.type === 'type' ? 'type' : 'niveau' }} {{ req.value }}{{ req.type === 'level' ? '+' : '' }}
                    </li>
                  </ul>
                </div>

                <div class="bg-base-200/50 rounded-lg p-3">
                  <h3 class="font-semibold text-sm mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-success rounded-full"></span>
                    Récompenses
                  </h3>
                  <ul class="space-y-1">
                    <li v-for="reward in expedition.expedition.rewards" :key="reward.type" 
                        class="text-xs text-base-content/70 flex items-center gap-2">
                      <span class="w-1 h-1 bg-base-content/50 rounded-full"></span>
                      {{ reward.amount }} 
                      {{ reward.type === 'cash' ? '💰' : 
                        reward.type === 'xp' ? '⭐' : 
                        reward.type === 'pokeball' ? '⚪' : 
                        reward.type === 'masterball' ? '🟣' : '🎁' }} 
                      {{ reward.type }}
                    </li>
                  </ul>
                </div>

                <div class="flex items-center justify-between pt-2">
                  <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full" 
                         :class="{
                           'bg-success': expedition.status === 'available',
                           'bg-warning': expedition.status === 'in_progress',
                           'bg-base-content/50': expedition.status === 'completed'
                         }"></div>
                    <span class="text-xs font-medium"
                          :class="{
                            'text-success': expedition.status === 'available',
                            'text-warning': expedition.status === 'in_progress',
                            'text-base-content/50': expedition.status === 'completed'
                          }">
                      {{ expedition.status === 'available' ? 'Disponible' : 
                        expedition.status === 'in_progress' ? 'En cours' : 'Terminée' }}
                    </span>
                  </div>
                  <div class="text-xs text-base-content/60">
                    {{ formatDuration(expedition.expedition.duration_minutes) }}
                  </div>
                </div>

                <div class="pt-2">
                  <Button v-if="expedition.status === 'available'" 
                          @click="openSelectionModal(expedition.expedition)"
                          variant="primary" 
                          size="sm" 
                          class="w-full">
                    Démarrer l'expédition
                  </Button>
                  
                  <Button v-else-if="expedition.can_be_claimed" 
                          @click="claim(expedition.id)"
                          variant="secondary" 
                          size="sm" 
                          class="w-full">
                    🎁 Réclamer les récompenses
                  </Button>
                  
                  <div v-else-if="expedition.status === 'in_progress'" 
                       class="text-center py-2 px-4 bg-warning/10 rounded-lg">
                    <p class="text-xs text-warning font-medium mb-2">En cours...</p>
                    <ExpeditionCountdown 
                      :end-time="expedition.ends_at" 
                      @expired="onExpeditionExpired(expedition.id)" 
                    />
                  </div>
                  
                  <div v-else class="text-center py-2 px-4 bg-base-content/10 rounded-lg">
                    <p class="text-xs text-base-content/50 font-medium">Terminée</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <Toast 
          v-if="showToast"
          :type="toastType"
          :title="toastMessage"
          @close="showToast = false"
        />
      </div>
    </div>

    <ExpeditionPokemonSelectionModal 
      :show="showSelectionModal"
      :expedition="selectedExpedition"
      :eligible-pokemons="eligiblePokemons"
      @close="closeSelectionModal"
      @success="onExpeditionSuccess"
      @error="onExpeditionError"
    />

    <RewardsModal 
      :show="showRewardsModal"
      :rewards="expeditionRewards"
      @close="showRewardsModal = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue'
import Button from '@/Components/UI/Button.vue'
import Toast from '@/Components/UI/Toast.vue'
import ExpeditionPokemonSelectionModal from '@/Components/Expeditions/ExpeditionPokemonSelectionModal.vue'
import ExpeditionCountdown from '@/Components/Expeditions/ExpeditionCountdown.vue'
import RewardsModal from '@/Components/Expeditions/RewardsModal.vue'

defineProps({
  expeditions: Array
})

const page = usePage()
const showSelectionModal = ref(false)
const selectedExpedition = ref(null)
const eligiblePokemons = ref([])
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')
const showRewardsModal = ref(false)
const expeditionRewards = ref([])

const goBack = () => {
  router.visit('/me')
}

const showToastMessage = (message, type = 'success') => {
  toastMessage.value = message
  toastType.value = type
  showToast.value = true
  
  setTimeout(() => {
    showToast.value = false
  }, 5000)
}

const claim = async (userExpeditionId) => {
  try {
    const response = await fetch('/api/expeditions/claim', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': page.props.csrf_token,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        user_expedition_id: userExpeditionId
      })
    })

    const data = await response.json()

    if (response.ok && data.success) {
      expeditionRewards.value = data.rewards
      showRewardsModal.value = true
      router.reload({ only: ['expeditions'] })
    } else {
      const errorMessage = data.message || `Erreur ${response.status}: ${response.statusText}`
      showToastMessage(errorMessage, 'error')
      console.error('Erreur serveur:', {
        status: response.status,
        statusText: response.statusText,
        data: data
      })
    }
  } catch (error) {
    console.error('Erreur:', error)
    showToastMessage('Erreur de connexion lors de la réclamation', 'error')
  }
}

const openSelectionModal = async (expedition) => {
  selectedExpedition.value = expedition
  
  try {
    const response = await fetch(`/expeditions/${expedition.id}`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    
    if (!response.ok) {
      throw new Error('Erreur réseau')
    }
    
    const data = await response.json()
    eligiblePokemons.value = data.eligiblePokemons || []
    showSelectionModal.value = true
  } catch (error) {
    console.error('Erreur lors du chargement des Pokémons éligibles:', error)
    showToastMessage('Erreur lors du chargement des Pokémons éligibles', 'error')
  }
}

const closeSelectionModal = () => {
  showSelectionModal.value = false
  selectedExpedition.value = null
  eligiblePokemons.value = []
}

const onExpeditionExpired = (userExpeditionId) => {
  router.reload({ only: ['expeditions'] })
}

const onExpeditionSuccess = (message) => {
  showToastMessage(message, 'success')
  router.reload({ only: ['expeditions'] })
}

const onExpeditionError = (message) => {
  showToastMessage(message, 'error')
}

const formatDuration = (minutes) => {
  const hours = Math.floor(minutes / 60)
  const remainingMinutes = minutes % 60
  
  if (hours > 0) {
    if (remainingMinutes > 0) {
      const wholeMinutes = Math.floor(remainingMinutes)
      const seconds = Math.round((remainingMinutes - wholeMinutes) * 60)
      
      if (wholeMinutes > 0 && seconds > 0) {
        return `${hours}h ${wholeMinutes}min ${seconds}s`
      } else if (wholeMinutes > 0) {
        return `${hours}h ${wholeMinutes}min`
      } else if (seconds > 0) {
        return `${hours}h ${seconds}s`
      }
    }
    return `${hours}h`
  }
  
  if (minutes >= 1) {
    const wholeMinutes = Math.floor(minutes)
    const seconds = Math.round((minutes - wholeMinutes) * 60)
    
    if (wholeMinutes > 0 && seconds > 0) {
      return `${wholeMinutes}min ${seconds}s`
    } else if (wholeMinutes > 0) {
      return `${wholeMinutes}min`
    } else if (seconds > 0) {
      return `${seconds}s`
    }
  }
  
  return `${Math.round(minutes * 60)}s`
}
</script> 