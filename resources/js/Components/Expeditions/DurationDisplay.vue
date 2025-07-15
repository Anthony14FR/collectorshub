<script setup lang="ts">
import { getFormattedDuration } from '@/utils/expedition';

interface Props {
  minutes: number;
  showIcon?: boolean;
  size?: 'sm' | 'md' | 'lg';
}

const props = withDefaults(defineProps<Props>(), {
  showIcon: true,
  size: 'md'
});

const sizeClasses = {
  sm: 'text-xs',
  md: 'text-sm',
  lg: 'text-base'
};

const iconSizeClasses = {
  sm: 'w-3 h-3',
  md: 'w-4 h-4',
  lg: 'w-5 h-5'
};
</script>

<template>
  <div class="flex items-center gap-1">
    <svg 
      v-if="showIcon"
      :class="[
        'text-base-content/60',
        iconSizeClasses[size]
      ]"
      fill="none" 
      stroke="currentColor" 
      viewBox="0 0 24 24"
    >
      <path 
        stroke-linecap="round" 
        stroke-linejoin="round" 
        stroke-width="2" 
        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
      />
    </svg>
    <span :class="[sizeClasses[size], 'font-medium text-base-content/80']">
      {{ getFormattedDuration(minutes) }}
    </span>
  </div>
</template> 