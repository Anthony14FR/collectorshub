<template>
  <div v-if="show" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-gradient-to-br from-base-100 to-base-200 rounded-xl border border-base-300/30 shadow-2xl max-w-md w-full mx-4 animate-in zoom-in-95 duration-200">
      <div class="p-6 text-center">
        <div class="w-16 h-16 bg-gradient-to-br from-success/20 to-success/30 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        
        <h2 class="text-xl font-bold text-base-content mb-2">
          🎉 Expédition terminée !
        </h2>
        
        <p class="text-base-content/70 mb-6">
          Vous avez obtenu les récompenses suivantes :
        </p>
        
        <div class="space-y-3 mb-6">
          <div v-for="reward in rewards" :key="reward.type" 
               class="flex items-center justify-between bg-base-200/50 rounded-lg p-3">
            <div class="flex items-center gap-3">
              <div v-if="reward.type === 'cash'" 
                   class="w-8 h-8 bg-warning/20 rounded-full flex items-center justify-center">
                <span class="text-warning text-sm font-bold">💰</span>
              </div>
              <div v-else-if="reward.type === 'xp'" 
                   class="w-8 h-8 bg-info/20 rounded-full flex items-center justify-center">
                <span class="text-info text-sm font-bold">⭐</span>
              </div>
              <div v-else 
                   class="w-8 h-8 bg-primary/20 rounded-full flex items-center justify-center">
                <span class="text-primary text-sm font-bold">🎁</span>
              </div>
              
              <span class="text-base-content font-medium">
                {{ getRewardLabel(reward.type) }}
              </span>
            </div>
            
            <span class="text-base-content font-bold">
              +{{ reward.amount || reward.quantity }}
            </span>
          </div>
        </div>
        
        <Button 
          @click="$emit('close')"
          variant="primary"
          class="w-full"
        >
          Parfait !
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup>
import Button from '@/Components/UI/Button.vue'

defineProps({
  show: {
    type: Boolean,
    default: false
  },
  rewards: {
    type: Array,
    default: () => []
  }
})

defineEmits(['close'])

const getRewardLabel = (type) => {
  switch (type) {
  case 'cash':
    return 'Cash'
  case 'xp':
    return 'Expérience'
  case 'pokeball':
    return 'Pokeball'
  case 'masterball':
    return 'Masterball'
  case 'item':
    return 'Objet'
  default:
    return type
  }
}
</script> 