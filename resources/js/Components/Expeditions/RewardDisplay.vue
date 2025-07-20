<script setup lang="ts">
import type { ExpeditionReward } from '@/constants/expedition';
import { getRewardLabel } from '@/utils/expedition';
import { Circle, CircleDot, Coins, Gift, HelpCircle, Zap } from 'lucide-vue-next';

interface Props {
  rewards: ExpeditionReward[];
  items?: Array<{
    id: number;
    name: string;
    image?: string;
  }>;
  showTooltip?: boolean;
  maxVisible?: number;
}

const { rewards, items, showTooltip, maxVisible } = withDefaults(defineProps<Props>(), {
  showTooltip: true,
  maxVisible: 3
});

const getRewardIcon = (reward: ExpeditionReward) => {
  switch (reward.type) {
  case 'cash': return Coins;
  case 'xp': return Zap;
  case 'pokeball': return Circle;
  case 'masterball': return CircleDot;
  case 'item': return Gift;
  default: return HelpCircle;
  }
};

const getRewardImage = (reward: ExpeditionReward) => {
  if (reward.type === 'pokeball') return '/images/items/pokeball.png';
  if (reward.type === 'masterball') return '/images/items/masterball.png';
  if (reward.type === 'item' && reward.item_id) {
    const item = items?.find(i => i.id === reward.item_id);
    return item?.image || '/images/items/pokeball.png';
  }
  return undefined;
};

const visibleRewards = rewards.slice(0, maxVisible);
const hasMore = rewards.length > maxVisible;
</script>

<template>
  <div class="flex items-center gap-2">
    <template v-if="rewards.length === 0">
      <span class="text-base-content/50 text-sm">Aucune récompense</span>
    </template>
    
    <template v-else>
      <div 
        v-for="(reward, index) in visibleRewards" 
        :key="index"
        class="flex items-center gap-1"
      >
        <div class="relative group">
          <div class="flex items-center gap-1 text-sm">
            <img 
              v-if="getRewardImage(reward)"
              :src="getRewardImage(reward)"
              :alt="reward.type"
              class="w-4 h-4"
            />
            <component 
              v-else 
              :is="getRewardIcon(reward)"
              :size="16"
              class="text-base-content/70"
            />
            <span class="font-medium">
              {{ reward.type === 'item' ? reward.quantity : reward.amount }}
            </span>
          </div>
          
          <div 
            v-if="showTooltip"
            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none"
            style="z-index: 10000;"
          >
            {{ getRewardLabel(reward, items) }}
          </div>
        </div>
        
        <span v-if="index < visibleRewards.length - 1" class="text-base-content/30">•</span>
      </div>
      
      <span v-if="hasMore" class="text-base-content/50 text-sm">
        +{{ rewards.length - maxVisible }} autre{{ rewards.length - maxVisible > 1 ? 's' : '' }}
      </span>
    </template>
  </div>
</template> 