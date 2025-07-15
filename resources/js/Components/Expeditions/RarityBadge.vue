<script setup lang="ts">
import { getRarityLabel, getRarityColor, getRarityDotColor } from '@/utils/expedition';
import type { ExpeditionRarity } from '@/constants/expedition';

interface Props {
  rarity: ExpeditionRarity;
  showDot?: boolean;
  size?: 'sm' | 'md' | 'lg';
}

const props = withDefaults(defineProps<Props>(), {
  showDot: true,
  size: 'md'
});

const sizeClasses = {
  sm: 'text-xs px-2 py-1',
  md: 'text-sm px-3 py-1',
  lg: 'text-base px-4 py-2'
};

const dotSizeClasses = {
  sm: 'w-2 h-2',
  md: 'w-2.5 h-2.5',
  lg: 'w-3 h-3'
};
</script>

<template>
  <span 
    :class="[
      'inline-flex items-center gap-2 rounded-full border font-medium',
      getRarityColor(rarity),
      sizeClasses[size]
    ]"
  >
    <span 
      v-if="showDot"
      :class="[
        'rounded-full',
        getRarityDotColor(rarity),
        dotSizeClasses[size]
      ]"
    ></span>
    {{ getRarityLabel(rarity) }}
  </span>
</template> 