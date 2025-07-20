<script setup lang="ts">
import Avatar from '@/Components/UI/Avatar.vue';
import Button from '@/Components/UI/Button.vue';
import { Coins, Gift, PartyPopper, Sparkles } from 'lucide-vue-next';
import { computed } from 'vue';

interface GiftReward {
  amount: number;
  senderName: string;
  senderAvatar?: string;
  senderId: number;
}

interface Props {
  show: boolean;
  reward: GiftReward | null;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  close: [];
}>();

const getSenderAvatarSrc = computed(() => {
  if (!props.reward) return '';
  return props.reward.senderAvatar || `/images/trainer/${props.reward.senderId % 10 + 1}.png`;
});

const handleClose = () => {
  emit('close');
};
</script>

<template>
  <div v-if="show && reward" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-gradient-to-br from-base-100/95 to-base-200/90 backdrop-blur-lg rounded-2xl border-2 border-success/30 shadow-2xl shadow-success/20 max-w-md w-full mx-4 overflow-hidden">
      
      <div class="bg-gradient-to-r from-success/20 to-success/30 p-6 text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -skew-x-12 animate-pulse"></div>
        
        <div class="relative z-10">
          <div class="w-20 h-20 bg-gradient-to-br from-success/30 to-success/50 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
            <Gift :size="40" class="text-success" />
          </div>
          
          <h2 class="text-2xl font-bold text-success mb-2">
            Cadeau reçu !
          </h2>
          
          <p class="text-success/80 font-medium">
            Vous avez reçu un cadeau de votre ami
          </p>
        </div>
      </div>
      
      <div class="p-6 space-y-6">
        <div class="bg-gradient-to-br from-base-100/80 to-base-200/60 backdrop-blur-sm rounded-xl p-4 border border-base-300/30">
          <div class="flex items-center gap-3">
            <Avatar 
              :src="getSenderAvatarSrc"
              :alt="reward.senderName"
              size="md"
              gradient
              ring
              ring-color="success"
            />
            
            <div>
              <p class="text-sm text-base-content/70 mb-1">Cadeau envoyé par</p>
              <h3 class="font-bold text-base-content">{{ reward.senderName }}</h3>
            </div>
          </div>
        </div>
        
        <div class="bg-gradient-to-br from-success/10 to-success/20 rounded-xl p-4 border border-success/30">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-gradient-to-br from-warning/30 to-warning/50 rounded-full flex items-center justify-center">
                <Coins :size="24" class="text-warning" />
              </div>
              
              <div>
                <p class="text-sm text-base-content/70">Vous avez reçu</p>
                <h3 class="font-bold text-base-content">Cash</h3>
              </div>
            </div>
            
            <div class="text-right">
              <div class="text-2xl font-bold text-success">
                +{{ reward.amount.toLocaleString() }}
              </div>
              <p class="text-xs text-base-content/70">Cash</p>
            </div>
          </div>
        </div>
        
        <div class="text-center">
          <p class="text-base-content/80 text-sm flex items-center justify-center gap-2">
            <Sparkles :size="16" class="text-warning" />
            N'oubliez pas de rendre la pareille à vos amis !
            <Sparkles :size="16" class="text-warning" />
          </p>
        </div>
        
        <Button 
          @click="handleClose"
          variant="success"
          class="w-full"
          size="lg"
        >
          <span class="flex items-center justify-center gap-2">
            <PartyPopper :size="20" />
            Merci !
          </span>
        </Button>
      </div>
      
      <div class="absolute top-4 right-4 w-2 h-2 bg-success/40 rounded-full animate-pulse"></div>
      <div class="absolute bottom-4 left-4 w-3 h-3 bg-warning/30 rounded-full animate-pulse delay-300"></div>
      <div class="absolute top-1/3 right-8 w-1 h-1 bg-info/50 rounded-full animate-ping delay-700"></div>
    </div>
  </div>
</template>