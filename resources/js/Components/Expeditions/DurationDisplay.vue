<script setup lang="ts">
import { getFormattedDuration } from '@/utils/expedition';
import { Clock } from 'lucide-vue-next';

interface Props {
  minutes: number;
  showIcon?: boolean;
  size?: 'sm' | 'md' | 'lg';
}

const { showIcon, size } = withDefaults(defineProps<Props>(), {
  showIcon: true,
  size: 'md'
});

const sizeClasses = {
  sm: 'text-xs',
  md: 'text-sm',
  lg: 'text-base'
};

const iconSizes = {
  sm: 12,
  md: 16,
  lg: 20
};
</script>

<template>
  <div class="flex items-center gap-1">
    <Clock 
      v-if="showIcon"
      :size="iconSizes[size]"
      class="text-base-content/60"
    />
    <span :class="[sizeClasses[size], 'font-medium text-base-content/80']">
      {{ getFormattedDuration(minutes) }}
    </span>
  </div>
</template> 