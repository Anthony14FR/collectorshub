<script setup lang="ts">
import { getRequirementLabel, getRarityDotColor } from '@/utils/expedition';
import type { ExpeditionRequirement } from '@/constants/expedition';

interface Props {
  requirements: ExpeditionRequirement[];
  showTooltip?: boolean;
  maxVisible?: number;
}

const props = withDefaults(defineProps<Props>(), {
  showTooltip: true,
  maxVisible: 3
});

const getRequirementIcon = (requirement: ExpeditionRequirement) => {
  if (requirement.type === 'type') {
    return `/images/types/${requirement.value}.png`;
  }
  return null;
};

const visibleRequirements = props.requirements.slice(0, props.maxVisible);
const hasMore = props.requirements.length > props.maxVisible;
</script>

<template>
  <div class="flex items-center gap-2">
    <template v-if="requirements.length === 0">
      <span class="text-base-content/50 text-sm">Aucun prérequis</span>
    </template>
    
    <template v-else>
      <div 
        v-for="(requirement, index) in visibleRequirements" 
        :key="index"
        class="flex items-center gap-1"
      >
        <div class="relative group">
          <div class="flex items-center gap-1 text-sm">
            <img 
              v-if="requirement.type === 'type'"
              :src="getRequirementIcon(requirement)"
              :alt="requirement.value"
              class="w-4 h-4"
            />
            <span 
              v-else-if="requirement.type === 'rarity'"
              :class="[
                'w-2.5 h-2.5 rounded-full',
                getRarityDotColor(requirement.value)
              ]"
            ></span>
            <span class="font-medium">{{ requirement.quantity }}</span>
          </div>
          
          <div 
            v-if="showTooltip"
            class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none"
            style="z-index: 10000;"
          >
            {{ getRequirementLabel(requirement) }}
          </div>
        </div>
        
        <span v-if="index < visibleRequirements.length - 1" class="text-base-content/30">•</span>
      </div>
      
      <span v-if="hasMore" class="text-base-content/50 text-sm">
        +{{ requirements.length - maxVisible }} autre{{ requirements.length - maxVisible > 1 ? 's' : '' }}
      </span>
    </template>
  </div>
</template> 