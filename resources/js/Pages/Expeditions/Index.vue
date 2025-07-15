<script setup>
import { ref, computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import BackgroundEffects from '@/Components/UI/BackgroundEffects.vue'
import Button from '@/Components/UI/Button.vue'
import ExpeditionPokemonSelectionModal from '@/Components/Expeditions/ExpeditionPokemonSelectionModal.vue'
import ExpeditionCountdown from '@/Components/Expeditions/ExpeditionCountdown.vue'
import RewardsModal from '@/Components/Expeditions/RewardsModal.vue'

const page = usePage()
console.log(page.props)
const activeTab = ref('disponibles')
const showSelectionModal = ref(false)
const selectedExpedition = ref(null)
const eligiblePokemons = ref([])
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')
const showRewardsModal = ref(false)
const expeditionRewards = ref([])

const expeditions = computed(() => page.props.expeditions || [])

const tabs = computed(() => [
  { id: 'disponibles', label: 'Disponibles', count: stats.value.available },
  { id: 'en-cours', label: 'En cours', count: stats.value.inProgress }
])

const stats = computed(() => {
  const available = expeditions.value.filter(e => e.status === 'available').length || 0
  const inProgress = expeditions.value.filter(e => e.status === 'in_progress').length || 0
  return { available, inProgress }
})

const filteredExpeditions = computed(() => {
  return expeditions.value.filter(expedition => {
    switch (activeTab.value) {
    case 'disponibles':
      return expedition.status === 'available'
    case 'en-cours':
      return expedition.status === 'in_progress'
    default:
      return true
    }
  })
})

const isExpeditionClaimable = (expedition) => {
  return expedition.status === 'in_progress' &&
    expedition.ends_at &&
    new Date() >= new Date(expedition.ends_at)
}

const refreshExpeditions = () => {
  router.reload()
}

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
      refreshExpeditions()
    } else {
      const errorMessage = data.message || `Erreur ${response.status}: ${response.statusText}`
      showToastMessage(errorMessage, 'error')
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

const onExpeditionSuccess = (message) => {
  closeSelectionModal()
  showToastMessage(message, 'success')
  refreshExpeditions()
}

const onExpeditionError = (message) => {
  showToastMessage(message, 'error')
}

const formatDuration = (minutes) => {
  const hours = Math.floor(minutes / 60)
  const remainingMinutes = minutes % 60
  
  if (hours > 0) {
    return remainingMinutes > 0 ? `${hours}h ${remainingMinutes}m` : `${hours}h`
  }
  return `${minutes}m`
}

const getRarityBorderClass = (rarity) => {
  switch (rarity) {
  case 'normal': return 'border-base-300/40'
  case 'rare': return 'border-info/40'
  case 'epic': return 'border-purple-500/40'
  case 'legendary': return 'border-warning/40'
  default: return 'border-base-300/40'
  }
}

const getRarityBackgroundClass = (rarity) => {
  switch (rarity) {
  case 'normal': return 'bg-gradient-to-br from-base-100/80 to-base-200/60'
  case 'rare': return 'bg-gradient-to-br from-info/5 via-base-100/80 to-info/10'
  case 'epic': return 'bg-gradient-to-br from-purple-500/5 via-base-100/80 to-purple-500/10'
  case 'legendary': return 'bg-gradient-to-br from-warning/5 via-base-100/80 to-warning/10'
  default: return 'bg-gradient-to-br from-base-100/80 to-base-200/60'
  }
}

const getRarityGlowClass = (rarity) => {
  switch (rarity) {
  case 'normal': return 'bg-gradient-to-br from-base-300/10 to-base-300/5'
  case 'rare': return 'bg-gradient-to-br from-info/15 to-info/5'
  case 'epic': return 'bg-gradient-to-br from-purple-500/15 to-purple-500/5'
  case 'legendary': return 'bg-gradient-to-br from-warning/15 to-warning/5'
  default: return 'bg-gradient-to-br from-base-300/10 to-base-300/5'
  }
}

const getRarityColorClass = (rarity) => {
  switch (rarity) {
  case 'normal': return 'bg-base-content/50'
  case 'rare': return 'bg-info'
  case 'epic': return 'bg-purple-500'
  case 'legendary': return 'bg-warning'
  default: return 'bg-base-content/50'
  }
}

const getRarityBadgeClass = (rarity) => {
  switch (rarity) {
  case 'normal': return 'bg-base-300/40 text-base-content border border-base-300/60'
  case 'rare': return 'bg-info/20 text-info border border-info/40'
  case 'epic': return 'bg-purple-500/20 text-purple-500 border border-purple-500/40'
  case 'legendary': return 'bg-warning/20 text-warning border border-warning/40'
  default: return 'bg-base-300/40 text-base-content border border-base-300/60'
  }
}

const getRarityIconBgClass = (rarity) => {
  switch (rarity) {
  case 'normal': return 'bg-gradient-to-br from-base-300/20 to-base-300/10'
  case 'rare': return 'bg-gradient-to-br from-info/20 to-info/10'
  case 'epic': return 'bg-gradient-to-br from-purple-500/20 to-purple-500/10'
  case 'legendary': return 'bg-gradient-to-br from-warning/20 to-warning/10'
  default: return 'bg-gradient-to-br from-base-300/20 to-base-300/10'
  }
}

const getRarityIconClass = (rarity) => {
  switch (rarity) {
  case 'normal': return 'text-base-content/70'
  case 'rare': return 'text-info'
  case 'epic': return 'text-purple-500'
  case 'legendary': return 'text-warning'
  default: return 'text-base-content/70'
  }
}

const getRarityLabel = (rarity) => {
  switch (rarity) {
  case 'normal': return 'Normal'
  case 'rare': return 'Rare'
  case 'epic': return 'Épique'
  case 'legendary': return 'Légendaire'
  default: return 'Normal'
  }
}

const getStatusColorClass = (status) => {
  switch (status) {
  case 'available': return 'bg-success'
  case 'in_progress': return 'bg-warning'
  default: return 'bg-base-content/50'
  }
}

const getStatusTextClass = (status) => {
  switch (status) {
  case 'available': return 'text-success'
  case 'in_progress': return 'text-warning'
  default: return 'text-base-content/50'
  }
}

const getStatusLabel = (status) => {
  switch (status) {
  case 'available': return 'Disponible'
  case 'in_progress': return 'En cours'
  default: return 'Inconnue'
  }
}

const getRequirementText = (req) => {
  switch (req.type) {
  case 'rarity':
    return `Pokémon ${req.value}`
  case 'type':
    return `de type ${req.value}`
  case 'level':
    return `niveau ${req.value}+`
  default:
    return `${req.type} ${req.value}`
  }
}

const getRewardLabel = (type) => {
  switch (type) {
  case 'cash': return 'Cash'
  case 'pokeball': return 'Pokeball'
  case 'masterball': return 'Masterball'
  default: return type
  }
}
</script>

<template>
  <Head title="Expéditions" />
  <div class="min-h-screen w-full bg-gradient-to-br from-base-200 to-base-300 relative">
    <BackgroundEffects />
    <div class="relative z-10 min-h-screen">
      <div class="w-full max-w-7xl mx-auto px-4 lg:px-6 pb-16">
        <div class="flex items-center gap-4 pt-6 mb-6">
          <Button @click="goBack" variant="outline" size="sm" class="shrink-0">
            ← Retour
          </Button>
          <div>
            <h1 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-warning via-warning/90 to-accent bg-clip-text text-transparent">
              🗺️ Expéditions
            </h1>
            <p class="text-base-content/60 text-sm mt-1">Explorez les terres lointaines avec vos Pokémons</p>
          </div>
        </div>
        <div class="mb-6">
          <div class="flex flex-wrap gap-2 bg-base-100/50 backdrop-blur-sm rounded-xl p-2 border border-base-300/30">
            <Button 
              v-for="tab in tabs" 
              :key="tab.id"
              @click="activeTab = tab.id"
              :variant="activeTab === tab.id ? 'primary' : 'ghost'"
              size="sm"
              class="flex-1 min-w-0"
            >
              {{ tab.label }} ({{ tab.count }})
            </Button>
          </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
          <div 
            v-for="expedition in filteredExpeditions" 
            :key="expedition.id" 
            class="group relative overflow-hidden backdrop-blur-sm rounded-xl border transition-all duration-300 hover:border-primary/50 shadow-lg hover:shadow-xl flex flex-col"
            :class="[getRarityBorderClass(expedition.expedition.rarity), getRarityBackgroundClass(expedition.expedition.rarity)]"
          >
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                 :class="getRarityGlowClass(expedition.expedition.rarity)"></div>
            
            <div class="relative p-4 flex flex-col flex-1">
              <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-2">
                    <div class="w-2 h-2 rounded-full" :class="getRarityColorClass(expedition.expedition.rarity)"></div>
                    <h2 class="text-base font-bold text-base-content">
                      {{ expedition.expedition.name }}
                    </h2>
                  </div>
                  <div class="mb-2">
                    <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getRarityBadgeClass(expedition.expedition.rarity)">
                      {{ getRarityLabel(expedition.expedition.rarity) }}
                    </div>
                  </div>
                  <p class="text-xs text-base-content/70 mb-3">
                    {{ expedition.expedition.description }}
                  </p>
                </div>
                <div class="flex-shrink-0 ml-3">
                  <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="getRarityIconBgClass(expedition.expedition.rarity)">
                    <svg class="w-5 h-5" :class="getRarityIconClass(expedition.expedition.rarity)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 113 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="space-y-3 mb-4 flex-1">
                <div class="bg-gradient-to-r from-info/10 to-info/5 rounded-lg p-3 border border-info/20">
                  <div class="flex items-center gap-2 mb-2">
                    <div class="w-1.5 h-1.5 bg-info rounded-full"></div>
                    <h3 class="font-semibold text-xs text-info">Exigences</h3>
                  </div>
                  <div class="flex flex-wrap gap-1">
                    <div v-for="req in expedition.expedition.requirements" :key="req.id" 
                         class="inline-flex items-center gap-1.5 bg-info/10 border border-info/20 rounded-md px-2 py-1">
                      <span class="font-bold text-info text-xs">{{ req.quantity }}</span>
                      <span class="text-xs text-base-content/80">{{ getRequirementText(req) }}</span>
                    </div>
                  </div>
                </div>
                <div class="bg-gradient-to-r from-success/10 to-success/5 rounded-lg p-3 border border-success/20">
                  <div class="flex items-center gap-2 mb-2">
                    <div class="w-1.5 h-1.5 bg-success rounded-full"></div>
                    <h3 class="font-semibold text-xs text-success">Récompenses</h3>
                  </div>
                  <div class="flex flex-wrap gap-1">
                    <div v-for="reward in expedition.expedition.rewards" :key="reward.type" 
                         class="inline-flex items-center gap-1.5 bg-success/10 border border-success/20 rounded-md px-2 py-1">
                      <div class="w-4 h-4 flex items-center justify-center">
                        <span v-if="reward.type === 'cash'" class="text-warning font-bold text-xs">$</span>
                        <span v-else-if="reward.type === 'xp'" class="text-success font-bold text-xs">XP</span>
                        <img v-else-if="reward.type === 'pokeball'" src="/images/items/pokeball.png" alt="Pokeball" class="w-3 h-3 object-contain">
                        <img v-else-if="reward.type === 'masterball'" src="/images/items/masterball.png" alt="Masterball" class="w-3 h-3 object-contain">
                        <span v-else class="text-primary text-xs">🎁</span>
                      </div>
                      <span class="font-bold text-success text-xs">{{ reward.amount }}</span>
                      <span v-if="reward.type !== 'xp'" class="text-xs text-base-content/70">{{ getRewardLabel(reward.type) }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-1.5">
                  <div class="w-1.5 h-1.5 rounded-full" :class="getStatusColorClass(expedition.status)"></div>
                  <span class="text-xs font-medium" :class="getStatusTextClass(expedition.status)">
                    {{ getStatusLabel(expedition.status) }}
                  </span>
                </div>
                <div class="text-xs text-base-content/60 flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  {{ formatDuration(expedition.expedition.duration_minutes) }}
                </div>
              </div>
              <div class="mt-auto">
                <Button v-if="expedition.status === 'available'" 
                        @click="openSelectionModal(expedition.expedition)"
                        variant="primary" 
                        size="sm" 
                        class="w-full">
                  🚀 Démarrer l'expédition
                </Button>
                
                <Button v-else-if="isExpeditionClaimable(expedition)" 
                        @click="claim(expedition.id)"
                        variant="secondary" 
                        size="sm" 
                        class="w-full bg-gradient-to-r from-success/20 to-success/10 hover:from-success/30 hover:to-success/20 text-success border-success/30">
                  🎁 Réclamer les récompenses
                </Button>
                
                <div v-else-if="expedition.status === 'in_progress'" 
                     class="bg-gradient-to-r from-warning/10 to-warning/5 rounded-lg p-3 border border-warning/20">
                  <div class="text-center">
                    <p class="text-xs font-medium text-warning mb-2">⏳ Expédition en cours...</p>
                    <ExpeditionCountdown 
                      :end-time="expedition.ends_at" 
                      @expired="refreshExpeditions" 
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-if="filteredExpeditions.length === 0" class="text-center py-16">
          <div class="w-16 h-16 bg-base-200/50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-base-content/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 113 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
            </svg>
          </div>
          <h3 class="text-base font-semibold text-base-content/60 mb-2">Aucune expédition {{ activeTab }}</h3>
          <p class="text-sm text-base-content/40">Revenez plus tard pour de nouvelles aventures !</p>
        </div>
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
    <div v-if="showToast" class="fixed bottom-6 right-6 z-50">
      <div :class="[
        'bg-gradient-to-r backdrop-blur-lg rounded-xl p-4 border-2 shadow-xl transform transition-all duration-300',
        toastType === 'success' 
          ? 'from-success/20 to-success/10 border-success/30 text-success' 
          : 'from-error/20 to-error/10 border-error/30 text-error'
      ]">
        <div class="flex items-center gap-3">
          <div class="w-6 h-6 rounded-full flex items-center justify-center" :class="toastType === 'success' ? 'bg-success/20' : 'bg-error/20'">
            <svg v-if="toastType === 'success'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </div>
          <span class="font-medium">{{ toastMessage }}</span>
          <button @click="showToast = false" class="ml-2 opacity-70 hover:opacity-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>