<template>
  <div v-if="show" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-gradient-to-br from-base-100 to-base-200 rounded-xl border border-base-300/30 shadow-2xl max-w-md w-full mx-4 animate-in zoom-in-95 duration-200">
      <div class="p-6 text-center">
        <div class="w-16 h-16 bg-gradient-to-br from-success/20 to-success/30 rounded-full flex items-center justify-center mx-auto mb-4">
          <CheckCircle :size="32" class="text-success" />
        </div>
        
        <h2 class="text-xl font-bold text-base-content mb-2 flex items-center justify-center gap-2">
          <PartyPopper :size="24" class="text-warning" />
          Expédition terminée !
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
                <Coins :size="20" class="text-warning" />
              </div>
              <div v-else-if="reward.type === 'xp'" 
                   class="w-8 h-8 bg-success/20 rounded-full flex items-center justify-center">
                <Zap :size="20" class="text-success" />
              </div>
              <div v-else-if="reward.type === 'pokeball'" 
                   class="w-8 h-8 bg-primary/20 rounded-full flex items-center justify-center">
                <Circle :size="20" class="text-primary" />
              </div>
              <div v-else-if="reward.type === 'masterball'" 
                   class="w-8 h-8 bg-secondary/20 rounded-full flex items-center justify-center">
                <CircleDot :size="20" class="text-secondary" />
              </div>
              <div v-else 
                   class="w-8 h-8 bg-primary/20 rounded-full flex items-center justify-center">
                <Gift :size="20" class="text-primary" />
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
import { CheckCircle, Circle, CircleDot, Coins, Gift, PartyPopper, Zap } from 'lucide-vue-next'

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