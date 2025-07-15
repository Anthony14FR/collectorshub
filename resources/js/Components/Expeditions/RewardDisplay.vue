<script setup lang="ts">
import { getRewardLabel } from '@/utils/expedition';
import type { ExpeditionReward } from '@/constants/expedition';

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

const props = withDefaults(defineProps<Props>(), {
  showTooltip: true,
  maxVisible: 3
});

const getRewardIcon = (reward: ExpeditionReward) => {
  switch (reward.type) {
  case 'cash': return '$';
  case 'xp': return 'XP';
  case 'pokeball': return '⚾';
  case 'masterball': return '🏀';
  case 'item': return '🎁';
  default: return '?';
  }
};

const getRewardImage = (reward: ExpeditionReward) => {
  if (reward.type === 'pokeball') return '/images/items/pokeball.png';
  if (reward.type === 'masterball') return '/images/items/masterball.png';
  if (reward.type === 'item' && reward.item_id) {
    const item = props.items?.find(i => i.id === reward.item_id);
    return item?.image || '/images/items/pokeball.png';
  }
  return null;
};

const visibleRewards = props.rewards.slice(0, props.maxVisible);
const hasMore = props.rewards.length > props.maxVisible;
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
            <span v-else class="text-xs">{{ getRewardIcon(reward) }}</span>
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